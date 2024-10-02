    <?php
    session_start();
    $args = array(
        'hide_empty'      => false,
    );
    $categories = get_categories($args);

    
    ?>
    
    <div class="container pt-4" style="min-width:100%;">
        <div class="alert alert-danger" id="ai_error" role="alert" style="display:none;">
          Our tool is taking a rest. We apologize for the inconvenience and it will be live again in 1 hour
        </div>
        <?php
        if (isset($_GET['success'])) {
            echo '<div class="notice notice-success"><p>' . esc_html($_GET['success']) . '</p></div>';
        }
        ?>
        <div class="ai-body">
            <div class="header-plugin">
               <img src="<?php echo plugin_dir_url( dirname( __FILE__ ) ) . 'assets/images/ai-logo.png'; ?>" alt="logo"/>


                <p>SEO Optimized Content</p>
            </div>
            <?php
            
            ?>
            <div class="row">
                <div class="col-md-12 p-0">
                    <div class="centered-div">
                        <div class="create-post px-3">
                           <img src="<?php echo plugin_dir_url( dirname( __FILE__ ) ) . 'assets/images/star-icon.svg'; ?>" alt="star-icon"/>

                            <h2>create your post</h2>
                        </div>
                        <div class="form-counting-steps">
                            <div class="choose-step" id="choose-step-1">
                                <p>01</p>
                                <p class="step-title">Choose Your Blog Post Topic</p>
                            </div>
                            <img src="<?php echo plugin_dir_url( dirname( __FILE__ ) ) . 'assets/images/right-chevron.svg'; ?>" alt="right-chevron"/>

                            <div class="choose-step" id="choose-step-2">
                                <p>02</p>
                                <p class="step-title">Word Count</p>
                            </div>
                            <img src="<?php echo plugin_dir_url( dirname( __FILE__ ) ) . 'assets/images/right-chevron.svg'; ?>" alt="right-chevron"/>
                            <div class="choose-step" id="choose-step-3">
                                <p>03</p>
                                <p class="step-title">Final Result</p>
                            </div>
                        </div>

                        <form id="gpt-chat-form" method="post" style="margin-top:25px;" novalidate="novalidate">
                            <div id="step-1" class="form-step">
                                <div class="form-group" style="margin-bottom: 4px;">
                                    <h2>Choose Your Blog Post Topic</h2>
                                    <p>
                                        Begin by providing a topic for your new blog post. This will be main subject you’ll explore in your writing.
                                    </p>
                                    <textarea class="form-control mt-2" id="user-input" name="user_input" placeholder="Write here please..." rows="10" cols="90" required></textarea>
                                    <div class="image-field">
                                    <h3>Style of Image</h3>
                                    <select class="choose-style" name="img_type" id="img_type">
                                        <option value="realistic">Realistic</option>
                                        <option value="non-realistic">Non-Realistic</option>
                                    </select>
                                    </div>
                                </div>
                                <button type="button" id="next-step" class="plugin-btn mt-4">Next</button>
                            </div>

                            <div id="step-2" class="form-step" style="display: none;">
                                <div class="form-group" style="margin-top: 20px;margin-bottom:8px;">
                                    <h2> Specify the Word Count</h2>
                                    <p>Decide how long you want your blog post to be.<br>
                                        Provide the number of words to tailor the length according to your needs.
                                    </p>
                                    <input type="number" class="form-control mt-2" id="w_count" name="w_count" placeholder="Enter number of words" style="padding: 6px;width:100%;" required>
                                </div>
                                <div class="form-group" style="margin-top: 20px;margin-bottom:8px;">
                                    <label>Keywords:</label>
                                    <input type="text" class="form-control mt-2" id="keywords" name="keywords" placeholder="Enter keywords" style="padding: 6px;width:100%;" required>
                                </div>
                               <div class="btn_style d-flex justify-content-between mt-3">
                                <button id="chat_sub_btn" type="submit" style="display:none;">submit</button>
                               <button type="button" id="previous-step" class="plugin-btn  plugin-grey">Back</button>
                               <button type="button" id="next-step" class="plugin-btn">Next</button>
                               </div>
                            </div>
                        </form>
                            <div id="step-3" class="form-step" style="display: none;">
                                <h2 style="color:green;">Success!</h2>
                                <p>Your WordPress post has been successfully generated by AI. Review and publish it now!</p>
                                <div id="ai_gen_image">
                                        <?php include_once(plugin_dir_path(__FILE__) . 'generate_ai_image.php'); ?>
                                    </div>
                                <div class="col-md-12 pl-0">
                                    <form method="post" id="add_ai_optimize_post">
                                        <input type="hidden" value="<?php echo $_SESSION["ai_keywords"]; ?>" name="user_keywords" id="user_keywords">
                                        <div class="form-group" style="margin-top: 20px;margin-bottom:8px;">
                                        <input type="hidden" id="featured_image" name="featured_image" value="<?php echo $_SESSION["ai_image"]; ?>">
                                            <label for="p_title">Post Title</label>
                                            <input type="text" class="form-control mb-2" id="p_title" name="post_title"
                                                aria-describedby="emailHelp" placeholder="Enter Post Title" value='<?php echo str_replace('"', '', $_SESSION["ai_title"]); ?>' style="padding: 6px;width:100%;"
                                                require>
                                                <span>You can always Add or Edit the title given!</span>

                                        </div>
                                        <div class="form-group" style="margin-top: 20px;margin-bottom:8px;">
                                            <div id="keyword_sec">
                                                <label>Keywords: </label>
                                                <textarea rows='4' cols="90" class="form-control mb-4" id="ai_keywords"><?php echo $_SESSION["ai_keywords"]; ?></textarea>
                                                
                                                <?php //include_once(plugin_dir_path(__FILE__) . 'keyword_generate.php'); ?>
                                            </div>
                                        </div>

                                        <div id="editor_container">
                                            
                                            <?php
                                            
                                                $ai_content = isset($_SESSION["ai_content"]) ? $_SESSION["ai_content"] : '';
                                                $content = $ai_content; // Default content for the editor
                                                $editor_id = 'post_contents'; // Unique ID for the editor
                                                $settings = array(
                                                    'textarea_name' => 'post_content', // Name of the textarea for form submission
                                                    'media_buttons' => true, // Disable media buttons
                                                    'textarea_rows' => 30, // Number of rows for the editor
                                                    'teeny' => true, // Use the minimal editor toolbar
                                                    'required' => true
                                                );
                                                wp_editor($content, $editor_id, $settings);
                                            ?>
                                            <span id="post_contents_err"></span>
                                            <button class="btn btn-primary" id="regenerate_text" type="submit">Regenerate </button>

                                        </div>
                                        <div class="lds-roller regen_load" style="display:none;">
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                        </div>
                                        <input id="post_tags" type="hidden" name="post_tags" value="">

                                        <div class="form-group" style="margin-top: 20px;margin-bottom:20px;">
                                        <label for="p_title">Post Tags</label>
                                            
                                        <input type="text" class="form-control mb-2" id="p_tags" name="post_tags"
                                                aria-describedby="emailHelp" placeholder="Enter Your Tags" value='' style="padding: 6px;width:100%;"
                                                require>
                                            <div id="tag-container" class="tag-container">
                                                
                                            </div>
                                        </div>
                                        <div class="flex-row">
                                            <div class="form-group">
                                                <label for="datetime">Schedule:</label>
                                                <br>
                                                <input type="date" class="form-control" id="datetime" name="datetime">
                                                <span id="datetime-error"></span>

                                            </div>
                                            <div class="form-group">
                                                <label for="category">Select Category:</label><br>
                                                <select class="form-control select" name="p_category[]" id="p_category">
                                                    <option value="">Select a Category</option>
                                                    <?php foreach ($categories as $category) { ?>
                                                        <option value="<?php echo $category->term_id; ?>"><?php echo $category->name; ?></option>
                                                    <?php } ?>
                                                </select>



                                            </div>
                                        </div>
                                        <?php wp_nonce_field('save_post_as_draft_action', 'save_post_as_draft_nonce'); ?>
                                        <button class="btn btn-primary ai_post_save button-custom" type="submit" style="margin-top:4px; display:none;">Save Post</button>
                                    </form>

                                    <br><br>

                                   
                                </div>
                                <div class="btn_style d-flex justify-content-between mt-3">
                                <button type="button" id="previous-step" class="plugin-btn  plugin-grey">Back</button>
                                <button type="submit" class="plugin-btn save_ai_post">Save Post</button>
                                </div>
                            </div>
                      




                    

                    </div>

                    <div id="gpt-chat-response">
                    </div>
                    <?php 
                    $display_img;
                    if(isset($_SESSION["ai_image"])){
                        $display_img="display:block;";
                    }else{
                        $display_img="display:none;";
                    }
                ?>
                
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
                <!-- <div class="col-md-6" id="final_rasult" style="display:none;">
                    <form method="post" id="add_ai_optimize_post" >
                        <input type="hidden" value="<?php echo $_SESSION["ai_keywords"]; ?>" name="user_keywords" id="user_keywords">
                        <div class="form-group" style="margin-top: 20px;margin-bottom:8px;">
                        <input type="hidden" id="featured_image" name="featured_image" value="<?php echo $_SESSION["ai_image"]; ?>">
                            <input type="text" class="form-control mb-2" id="p_title" name="post_title"
                                aria-describedby="emailHelp" placeholder="Enter Post Title" value='<?php echo str_replace('"', '', $_SESSION["ai_title"]); ?>' style="padding: 6px;width:100%;"
                                require>
                                <span>You can always Add or Edit the title given!</span>

                        </div>
                        <div class="form-group" style="margin-top: 20px;margin-bottom:8px;">
                            <div id="keyword_sec">
                                <label>Keywords: </label>
                                <textarea rows='4' cols="90" class="form-control mb-4" id="ai_keywords"><?php echo $_SESSION["ai_keywords"]; ?></textarea>
                                
                                <?php //include_once(plugin_dir_path(__FILE__) . 'keyword_generate.php'); ?>
                            </div>
                        </div>

                        <div id="editor_container">
                            
                            <?php
                            
                                $ai_content = isset($_SESSION["ai_content"]) ? $_SESSION["ai_content"] : '';
                                $content = $ai_content; // Default content for the editor
                                $editor_id = 'post_contents'; // Unique ID for the editor
                                $settings = array(
                                    'textarea_name' => 'post_content', // Name of the textarea for form submission
                                    'media_buttons' => true, // Disable media buttons
                                    'textarea_rows' => 30, // Number of rows for the editor
                                    'teeny' => true, // Use the minimal editor toolbar
                                    'required' => true
                                );
                                wp_editor($content, $editor_id, $settings);
                            ?>
                            <span id="post_contents_err"></span>
                        </div>
                        <div class="form-group">
                            <label for="datetime">Schedule:</label>
                            <br>
                            <input type="date" class="form-control" id="datetime" name="datetime">
                            <span id="datetime-error"></span>

                        </div>
                        <div>
                            <div class="form-group">
                                <legend></legend>
                                <label for="category">Select Category:</label><br>
                                <select class="form-control" size="4" name="p_category[]" id="p_category" multiple="multiple">
                                    <option value=""></option>
                                    <?php foreach ($categories as $category) { ?>
                                                <option value="<?php echo $category->term_id; ?>"><?php echo $category->name; ?>
                                                </option>
                                                <?php
                                    }
                                    ?>

                                </select>


                            </div>
                        </div>

                        <?php wp_nonce_field('save_post_as_draft_action', 'save_post_as_draft_nonce'); ?>
                        <button class="btn btn-primary ai_post_save button-custom" type="submit" style="margin-top:4px;">Save Post</button>
                    </form>

                    <br><br>
                </div> -->
            </div>
        </div>
    </div>
    <script>
        jQuery(document).ready(function(){
            //jQuery('#ai_gen_image').hide();
        })
    </script>