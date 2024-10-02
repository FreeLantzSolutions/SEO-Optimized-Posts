<?php

// Define the class
class AI_Post_Handler {

    public function __construct() {
        // Register AJAX actions
        add_action('wp_ajax_ai_post_save_as_draft', array($this, 'save_as_draft'));
        add_action('wp_ajax_ai_post_update', array($this, 'update_post'));
        add_action('wp_ajax_upload_ai_image', array($this, 'upload_image'));
        add_action('wp_ajax_nopriv_upload_ai_image', array($this, 'upload_image'));
        add_action('wp_ajax_get_tag_suggestions', array($this, 'get_tag_suggestions'));
        add_action('wp_ajax_nopriv_get_tag_suggestions', array($this, 'get_tag_suggestions'));

        // Start session
        session_start();
    }

    // Save post as draft
    public function save_as_draft() {
        if (!empty($_POST['content'])) {
            $post_content = $_POST['content'] ?? '';
            $post_title = sanitize_text_field($_POST['post_title'] ?? '');
            $datetime = sanitize_text_field($_POST['datetime'] ?? '');
            $p_categories = $_POST['p_category'] ?? array();
            $featured_image_url = $_POST['featured_image'] ?? '';
            $keywords_input = $_POST['user_keywords'] ?? '';
            $tags_input = sanitize_text_field($_POST['post_tags'] ?? '');

            $p_status = !empty($datetime) ? 'future' : 'draft';
            $iso_date_time = !empty($datetime) ? date('Y-m-d H:i:s', strtotime($datetime)) : '';

            if (!empty($datetime) && !strtotime($iso_date_time)) {
                wp_send_json_error('Error: Invalid datetime format');
                return;
            }

            // Prepare post data
            $post_data = array(
                'post_title'   => $post_title,
                'post_content' => $post_content,
                'post_status'  => $p_status,
                'post_author'  => get_current_user_id(),
                'post_type'    => 'post'
            );

            // Insert the post
            $post_id = wp_insert_post($post_data);

            if (is_wp_error($post_id)) {
                wp_send_json_error('Error: Failed to save the post as draft.');
                return;
            }

            // Add post meta and categories
            add_post_meta($post_id, 'ai_generated', 'true');
            wp_set_post_categories($post_id, $p_categories);

            // Handle tags
            if (!empty($tags_input)) {
                $tags = array_map('trim', explode(',', $tags_input));
                wp_set_post_tags($post_id, $tags);
            }

            // Handle keywords
            $keywords_array = array_map('trim', explode(',', $keywords_input));
            $focus_keywords = array();
            foreach ($keywords_array as $keyword) {
                if (!empty($keyword)) {
                    $focus_keywords[] = array('keyword' => $keyword, 'score' => 0);
                }
            }
            update_post_meta($post_id, '_yoast_wpseo_focuskeywords', json_encode($focus_keywords));

            // Clear session data
            unset($_SESSION["ai_title"], $_SESSION["ai_content"], $_SESSION["ai_image"], $_SESSION["ai_keywords"], $_SESSION["user_keywords"]);

            // Handle featured image
            if (!empty($featured_image_url)) {
                $this->handle_featured_image($featured_image_url, $post_title, $post_id);
            }

            // Schedule post if datetime provided
            if (!empty($iso_date_time)) {
                wp_update_post(array(
                    'ID'            => $post_id,
                    'post_date'     => $iso_date_time,
                    'post_date_gmt' => get_gmt_from_date($iso_date_time)
                ));
            }

            wp_send_json_success('Post saved!');
        } else {
            wp_send_json_error('Error: Post content is required.');
        }

        wp_die();
    }

    // Update post
    public function update_post() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized access');
        }

        $post_id = intval($_POST['post_id'] ?? 0);
        $post_content = $_POST['content'] ?? '';
        $post_title = sanitize_text_field($_POST['post_title'] ?? '');
        $datetime = sanitize_text_field($_POST['datetime'] ?? '');
        $p_categories = $_POST['p_category'] ?? array();
        $featured_image_url = $_POST['featured_image'] ?? '';
        $keywords_input = $_POST['user_keywords'] ?? '';

        $iso_date_time = !empty($datetime) ? date('Y-m-d H:i:s', strtotime($datetime)) : '';

        // Validate datetime format
        if (!empty($datetime) && !strtotime($iso_date_time)) {
            wp_send_json_error('Error: Invalid datetime format');
            return;
        }

        // Update post data
        $post_data = array(
            'ID'           => $post_id,
            'post_title'   => $post_title,
            'post_content' => $post_content
        );

        if (!empty($iso_date_time)) {
            $post_data['post_date'] = $iso_date_time;
            $post_data['post_date_gmt'] = get_gmt_from_date($iso_date_time);
            $post_data['post_status'] = 'future';
        }

        $update_result = wp_update_post($post_data);

        if (is_wp_error($update_result)) {
            wp_send_json_error('Error: Failed to update the post.');
        } else {
            wp_set_post_categories($post_id, $p_categories);
            if (!empty($featured_image_url)) {
                $this->handle_featured_image($featured_image_url, $post_title, $post_id);
            }
            wp_send_json_success('Post updated successfully!');
        }
    }

    // Handle image upload
    public function upload_image() {
        $upload_dir = wp_upload_dir();
        $file = $_FILES['ai_image_file'] ?? null;

        if ($file) {
            $upload_overrides = array('test_form' => false);
            $uploaded_file = wp_handle_upload($file, $upload_overrides);

            if ($uploaded_file && !isset($uploaded_file['error'])) {
                $image_url = $upload_dir['url'] . '/' . basename($uploaded_file['file']);
                echo $image_url;
            } else {
                echo 'error';
            }
        }

        wp_die();
    }

    // Get tag suggestions
    public function get_tag_suggestions() {
        $term = sanitize_text_field($_GET['term'] ?? '');
        $tags = get_tags(array('name__like' => $term, 'hide_empty' => false));

        $suggestions = array();
        foreach ($tags as $tag) {
            $suggestions[] = $tag->name;
        }

        wp_send_json($suggestions);
    }

    // Helper function to handle featured image
    private function handle_featured_image($featured_image_url, $post_title, $post_id) {
        $upload_dir = wp_upload_dir();
        $filename = 'blog_' . (new DateTime())->format('Ymd_His') . '.jpg';
        $file_path = $upload_dir['path'] . '/' . $filename;

        if (file_put_contents($file_path, file_get_contents($featured_image_url)) !== false) {
            $attachment = array(
                'guid'           => $upload_dir['url'] . '/' . $filename,
                'post_mime_type' => 'image/jpeg',
                'post_title'     => $filename,
                'post_content'   => '',
                'post_status'    => 'inherit'
            );

            $attach_id = wp_insert_attachment($attachment, $file_path, $post_id);
            update_post_meta($attach_id, '_wp_attachment_image_alt', $post_title);
            set_post_thumbnail($post_id, $attach_id);
        } else {
            wp_send_json_error('Failed to save image data to the file.');
        }
    }
}

// Initialize the class
new AI_Post_Handler();
