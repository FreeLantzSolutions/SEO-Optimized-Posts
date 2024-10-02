<?php
if (isset($_POST['action']) && $_POST['action'] === 'delete_post' && isset($_POST['post_id'])) {
    $post_id = intval($_POST['post_id']);
    if (wp_verify_nonce($_POST['_wpnonce'], 'delete_post_' . $post_id)) {
        if (wp_delete_post($post_id, true)) {
             $redirect_url = add_query_arg('message', 'Post deleted successfully');
            wp_safe_redirect($redirect_url);
            exit;
        } else {
            echo '<div class="notice notice-error"><p>Error deleting post. Please try again.</p></div>';
        }
    } else {
        echo '<div class="notice notice-error"><p>Security check failed. Please try again.</p></div>';
    }
}


$category_filter = isset($_GET['category']) ? $_GET['category'] : '';

$args = array(
    'post_type'      => 'post',
    'posts_per_page' => -1,
    'meta_query'     => array(
        array(
            'key'   => 'ai_generated',
            'value' => 'true',
        ),
    ),
    'orderby'        => 'ID',
    'order'          => 'DESC',
);

// Add category filter if selected
if (!empty($category_filter)) {
    $args['category_name'] = $category_filter;
}

// Get posts for the current page
$posts_query = new WP_Query($args);


?>
    <div class="wrap">
        <h1 class="mb-3">List of Posts</h1>
        <div class="cat_wrap">
        <form id="category-filter-form" style="margin-bottom: 20px;">
            <?php
             $args = array(
                    'hide_empty'      => false,
                );
                $categories = get_categories($args);
            ?>
            <label for="category">Filter by Category:</label>
            <select name="category" id="category">
                <option value="">All Categories</option>
                <?php foreach ($categories as $cat) : ?>
                    <option value="<?php echo esc_attr($cat->slug); ?>" <?php selected($cat->slug, $category_filter); ?>><?php echo esc_html($cat->name); ?></option>
                <?php endforeach; ?>
            </select>
        </form>
        </div>
        <?php 
            if ($posts_query->have_posts()) :
            $post_status = get_post_status(get_the_ID());
        ?>
        <table id="ai_posts_tbl" class="wp-list-table widefat striped">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Date</th>
                    <th>Category</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($posts_query->have_posts()) : $posts_query->the_post(); ?>
                    <tr>
                        <td><?php the_ID(); ?></td>
                        <td><?php the_title(); ?></td>
                        <td><?php echo esc_html(get_the_date('', get_the_ID())); ?></td>
                        <td><?php echo get_the_category_list(', '); ?></td>
                        <td>
                            <a href="<?php echo esc_url(add_query_arg('ai_post_id', get_the_ID(), admin_url('admin.php?page=ai-post'))); ?>" class="btn btn-primary button"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="post_id" value="<?php echo esc_attr(get_the_ID()); ?>">
                                <input type="hidden" name="action" value="delete_post">
                                <?php wp_nonce_field('delete_post_' . get_the_ID()); ?>
                                <button type="submit" class="btn btn-danger button" onclick="return confirm('Are you sure you want to delete this post?');"><i class="fa fa-trash-o" aria-hidden="true"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php 
        else :
            echo '<p>No posts found.</p>';
        endif;
        
        ?>
    </div>
    <script>
     document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('category').addEventListener('change', function() {
            var selectedCategory = this.value;
            var currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('category', selectedCategory);
            window.location.href = currentUrl.href;
        });
    });
        jQuery(document).ready(function($) {
            $('#ai_posts_tbl').DataTable({
                "order": [
                    [0, "desc"]
                ] // 0 refers to the first column (index starts from 0)
            });
        });
    </script>
<?php


wp_reset_postdata();
?>
