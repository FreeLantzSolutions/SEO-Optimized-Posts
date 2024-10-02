<div class="container" style="min-width:100%;">
     <?php
        if (isset($_GET['success'])) {
            echo '<div class="notice notice-success"><p>' . esc_html($_GET['success']) . '</p></div>';
        }
        ?>
    <div class="row">
        <div class="col-md-6">
        <h3>SEO Optimized Content</h3>
        <div class="centered-div">
            <form id="gpt-chat-form" method="post" style="margin-top:5%;">

                
                <div class="form-group" style="margin-bottom: 4px;">

                    <textarea type="text" class="form-control" id="user-input" name="user_input"
                        placeholder="Type text here..." rows='10' cols="90" require> </textarea>

                </div>

                <div class="form-group" style="margin-top: 20px;margin-bottom:8px;">

                    <input type="number" class="form-control" id="w_count" name="w_count"
                        placeholder="Enter number of word" style="padding: 6px;width:100%;">

                </div>

                <button class="btn btn-primary op_submit_btn" type="submit">Update Content</button>

            </form>

            <div class="lds-roller" style="display:none;">
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
            </div>

            </div>

            <div id="gpt-chat-response">
            </div>
            <?php include_once(plugin_dir_path(__FILE__) . 'generate_ai_image.php'); ?>

        </div>
        <div class="col-md-6">
            <?php


            $post_id = isset($_GET['ai_post_id']) ? intval($_GET['ai_post_id']) : 0;
            $post = get_post($post_id);
            $post_status = get_post_status($post_id);
            
            
            // Get the scheduled date if post is scheduled
            $schedule_date = '';
            if ($post_status === 'future') {
                $schedule_date_timestamp = strtotime(get_post_time('Y-m-d H:i:s', true, $post_id));
                $schedule_date = date('Y-m-d', $schedule_date_timestamp);
            }

            if ($post) :
                $post_title = esc_html($post->post_title);
                $content = $post->post_content;
                $editor_id = 'post_contents';
                $settings = array(
                    'textarea_name' => 'post_content',
                    'media_buttons' => true,
                    'textarea_rows' => 30,
                    'teeny' => true,
                    'required' => true
                );

                $args = array(
                    'hide_empty'      => false,
                );
                $categories = get_categories($args);
                $post_categories = get_the_category($post_id);
                $post_category_id = !empty($post_categories) ? $post_categories[0]->term_id : '';
                $post_category_ids = array();
                    foreach ($post_categories as $category) {
                        $post_category_ids[] = $category->term_id;
                    }
                $image_tag = 'thumbnail'; // Replace with the image tag (e.g., 'thumbnail', 'medium', 'full')

                $image_attributes = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), $image_tag);
                $alt_text = get_post_meta(get_post_thumbnail_id($post_id), '_wp_attachment_image_alt', true);
                $focus_keywords_json = get_post_meta($post_id, '_yoast_wpseo_focuskeywords', true);

                // Convert JSON string back to PHP array
                $focus_keywords = json_decode($focus_keywords_json, true);

                // Initialize an array to store keywords
                $user_keywords = [];

                // Extract keywords from the array
                foreach ($focus_keywords as $keyword) {
                    $user_keywords[] = $keyword['keyword'];
                }

                // Convert array to a comma-separated string
                $comma_separated_keywords = implode(', ', $user_keywords);

                ?>

                <div class="wrap">
                    <h1>Edit Post:</h1>
                    <form method="post" id="edit_ai_optimize_post">
                        <input type="hidden" id="post_id" name="post_id" value="<?php echo $post_id; ?>">
                        <input type="hidden" id="featured_image" name="featured_image" value="">
                        <div class="form-group" style="margin-top: 20px;margin-bottom:8px;">
                            <input type="text" class="form-control" id="p_title" name="post_title"
                                value="<?php echo $post_title; ?>" aria-describedby="emailHelp" placeholder="Enter Post Title"
                                style="padding: 6px;width:100%;">
                        </div>
                        <div class="form-group" style="margin-top: 20px;margin-bottom:8px;">
                         <div id="keyword_sec">
                            <label>Keywords: </label>
                            <!-- <input type="text" class="form-control" value="" > -->
                            <textarea rows='4' cols="90" class="form-control" name="user_keywords" id="user_keywords"><?php echo $comma_separated_keywords; ?></textarea>
                            <?php //include_once(plugin_dir_path(__FILE__) . 'keyword_generate.php'); ?>
                        </div>
                    </div>

                        <div id="editor_container">
                            <?php wp_editor($content, $editor_id, $settings); ?>
                        </div>
                        <div class="form-group">
                        <label for="datetime">Featured Image:</label><br>
                            <?php
                                if ($image_attributes) {
                                    echo '<img src="' . $image_attributes[0] . '" alt="'.$alt_text.'" width="200">';
                                }
                            ?>
                        </div>
                        <div class="form-group">
                            <label for="datetime">Schedule:</label><br>
                            <input type="date" class="form-control" id="datetime" name="datetime"
                                value="<?php echo esc_attr($schedule_date); ?>">
                        </div>

                        <div class="form-group">
                            <label for="category">Select Category:</label><br>
                            <select class="form-control" size="4" name="p_category[]" id="p_category" multiple="multiple">
                                <option value=""></option>
                                <?php foreach ($categories as $category) : ?>
                                <option value="<?php echo $category->term_id; ?>"
                                <?php echo in_array($category->term_id, $post_category_ids) ? 'selected' : ''; ?>><?php echo $category->name; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <?php wp_nonce_field('save_post_as_draft_action', 'save_post_as_draft_nonce'); ?>
                        <button class="btn btn-primary" type="submit" style="margin-top:4px;">Update Post</button>
                    </form>
                    <div id="yoast-seo-container">
                        <?php
                        //if (defined('WPSEO_VERSION')) {
                            do_action('wpseo_post_content_fields', $post);
                        //}
                        ?>
                    </div>
                 
                   
                </div>


                <?php else :
                echo '<p>No post found.</p>';
            endif;
            ?>
            </div>
        </div>
        
    </div>
