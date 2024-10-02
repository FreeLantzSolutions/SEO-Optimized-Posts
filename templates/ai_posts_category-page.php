<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Include WordPress administration header.
require_once ABSPATH . 'wp-admin/admin-header.php';
$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action === 'delete' && isset($_GET['category_id'])) {
    $category_id = intval($_GET['category_id']);
    if (wp_delete_term($category_id, 'category')) {
        $redirect_url = add_query_arg('success', 'Category deleted successfully');
        wp_safe_redirect($redirect_url);
        exit;
    }
}

if (isset($_POST['submit']) && isset($_POST['tag-name'])) {
    $category_name = sanitize_text_field($_POST['tag-name']);
    if(empty($category_name)){
        $redirect_url = add_query_arg('error', 'Category Name required');
        wp_safe_redirect($redirect_url);
        exit;
    }
    if (wp_insert_term($category_name, 'category')) {
        $redirect_url = add_query_arg('success', 'Category added successfully');
        wp_safe_redirect($redirect_url);
        exit;
    }
}

// Display the category management page header.
?>
<div class="wrap">
<?php
    if (isset($_GET['success'])) {
        echo '<div class="notice notice-success"><p>' . esc_html($_GET['success']) . '</p></div>';
    }
    ?>
    <h1 class="wp-heading-inline"><?php echo esc_html( __( 'Categories', 'text-domain' ) ); ?></h1>

    <!-- Add new category form -->
    <div id="col-container">
        <div id="col-left">
            <div class="col-wrap">
                <div class="form-wrap">
                    <h2><?php echo esc_html( __( 'Add New Category', 'text-domain' ) ); ?></h2>
                    <form id="addtag" method="post" action="" class="validate">
                        <input type="hidden" name="action" value="add-tag">
                        <?php wp_nonce_field('add-category', '_wpnonce_add-category'); ?>
                        <div class="form-field">
                            <label for="tag-name"><?php echo esc_html(__('Name', 'text-domain')); ?></label>
                            <input name="tag-name" id="tag-name" type="text" size="40" aria-required="true" required>
                        </div>
                        <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo esc_attr(__('Add New Category', 'text-domain')); ?>"></p>
                    </form>
                </div>
            </div>
        </div>
        <div id="col-right">
            <!-- Display existing categories -->
            <div class="col-wrap">
                <h2><?php echo esc_html( __( 'Existing Categories', 'text-domain' ) ); ?></h2>
                <table class="wp-list-table widefat fixed striped table-view-list tags">
                    <thead>
                        <tr>
                            <th><?php echo esc_html( __( 'Category Name', 'text-domain' ) ); ?></th>
                            <th><?php echo esc_html( __( 'Actions', 'text-domain' ) ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $args = array(
                            'hide_empty' => false,
                        );
                        $categories = get_categories( $args );
                        if ( $categories ) {
                            foreach ( $categories as $category ) {
                                ?>
                                <tr>
                                    <td><?php echo esc_html( $category->name ); ?></td>
                                    <td>
                                        <a href="<?php echo esc_url( get_edit_tag_link( $category->term_id, 'category' ) ); ?>" class="btn btn-primary"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                                        <form method="post" style="display:inline;">
                                            <input type="hidden" name="category_id" value="<?php echo esc_attr( $category->term_id ); ?>">
                                            <a href="<?php echo esc_url(add_query_arg(array('action' => 'delete', 'category_id' => $category->term_id))); ?>" class="btn btn-danger delete"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                                        </form>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            ?>
                            <tr>
                                <td colspan="2"><?php echo esc_html( __( 'No categories found', 'text-domain' ) ); ?></td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>
<script>
      jQuery(document).ready(function($) {
        jQuery('#addtag').validate({
            rules: {
                'tag-name': {
                    required: true
                }
            },
            messages: {
                'tag-name': {
                    required: "Please enter a name for the category"
                }
            },
            submitHandler: function(form) {
                // Handle form submission (e.g., AJAX submission)
                form.submit();
            }
        });
    });
</script>
<?php
// Include WordPress administration footer.
require_once ABSPATH . 'wp-admin/admin-footer.php';
