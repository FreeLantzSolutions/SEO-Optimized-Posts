<?php





// function gpt_chat_form() {

    $args = array(
        'hide_empty'      => false,
    );
    $categories = get_categories($args);
   if(isset($_GET['ai_post_id']) && !empty($_GET['ai_post_id'])){
    include_once(plugin_dir_path(__FILE__) . 'templates/ai_edit_post-page.php');
   }else{
    ?>

<div class="container mt-3">
    <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#tabs-1" role="tab">All Posts</a>
        </li>
        <li class="nav-item">
            <a class="nav-link"  href="/wp-admin/admin.php?page=ai-create-post">Create a Post</a>
        </li>
        
        <li class="nav-item">
            <a class="nav-link"  href="/wp-admin/admin.php?page=ai-manage-categories" >Categories</a>
        </li>
    </ul><!-- Tab panes -->
    <div class="tab-content">
        <div class="tab-pane active" id="tabs-1" role="tabpanel">
            <?php include_once(plugin_dir_path(__FILE__) . 'templates/ai_post_listing-page.php'); ?>
        </div>
        <div class="tab-pane" id="tabs-2" role="tabpanel">
        <?php include_once(plugin_dir_path(__FILE__) . 'templates/ai_create_post-page.php'); ?>
        </div>
        <div class="tab-pane" id="tabs-3" role="tabpanel">
        <?php include_once(plugin_dir_path(__FILE__) . 'templates/generate_ai_image.php'); ?>
        </div>
        <div class="tab-pane" id="tabs-4" role="tabpanel">
        <?php include_once(plugin_dir_path(__FILE__) . 'templates/ai_posts_category-page.php'); ?>
        </div>
    </div>
</div>

<?php
   }


