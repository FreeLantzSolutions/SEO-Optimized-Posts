<h3>Generate Image</h3>
    <div class="centered-div">

        <form id="gpt-image-form" method="post" style="margin-top:5%;">
            <div class="form-group">
                <h3>How you want to set your image?</h3>
                <select class="choose-style" name="set_image" id="set_image">
                    <option value="generate_by_prompt">Generate by Prompt</option>
                    <option value="device_upload">Upload Image From Device</option>
                </select>
            </div>
            <div class="form-group by_prompt">
                <h3>Style of Image</h3>
                <select class="choose-style" name="img_type_gen" id="img_type_gen">
                    <option value="realistic">Realistic</option>
                    <option value="non-realistic">Non-Realistic</option>
                </select>
            </div>
            <div class="form-group by_prompt regenerate-paerent" style="margin-bottom: 4px;">

                <textarea type="text" class="form-control" id="img-input" name="img_input"
                    placeholder="Type text here..." rows='10' cols="90" required> </textarea>
                    <button class="btn btn-primary text-generate-btn" type="submit">Regenerate </button>
    

            </div>
            <!-- <div class="form-group by_prompt">
            <
            ?php 
             if(isset($_GET['ai_post_id'])){
                 ?>
                 <button class="btn btn-primary img_submit_btn" type="submit">Regenerate the Image</button>
                 <
                 ?php
             }else{
                 ?>
                    <button class="btn btn-primary img_submit_btn" type="submit">Submit</button>
                 <
                 ?php
             }
            
            ?>
            </div> -->

        </form>

        <div class="lds-roller_img" style="display:none;">
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
        </div>
         <div class="form-group" id="device_upload">
                <label>Upload Image from Device:</label>
                <input class="form-control" type="file" id="ai_image_file" name="image_file">
            </div>
        <div class="img_div">
                <?php 
                 
                 //if(isset($_SESSION["ai_image"])){
                  ?>
                  
                  <?php
                 //}
             ?>
        </div>
        

    </div>
    <script>
        jQuery(document).ready(function(){
            jQuery('#device_upload').hide();
            jQuery('#set_image').change(function() {
                // Hide all divs
                

                // Get the selected value
                var selectedValue = jQuery(this).val();

               if(selectedValue == 'generate_by_prompt'){
                jQuery('#device_upload').hide();
                jQuery('.by_prompt').show();
               }else if(selectedValue == 'device_upload'){
                jQuery('#device_upload').show();
                jQuery('.by_prompt').hide();
               }
            });
        })
    </script>