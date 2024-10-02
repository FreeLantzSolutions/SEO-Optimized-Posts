jQuery(document).ready(function () {
  var currentStep = 1;
  var totalSteps = 3;
  // Function to control step visibility and highlight active step
  function showSteps(step) {
    for (var i = 1; i <= totalSteps; i++) {
      document.getElementById("step-" + i).style.display = "none";
      if (i <= step) {
        document.getElementById("choose-step-" + i).classList.add("active");
      } else {
        document.getElementById("choose-step-" + i).classList.remove("active");
      }
    }
    document.getElementById("step-" + step).style.display = "block";
  }

  // Handle title and image visibility based on initial value
  let title = jQuery("#p_title").val();
  let img = jQuery(".img_div img").attr("src");

  if (title !== "") {
    jQuery(".op_submit_btn").css("display", "block");
    jQuery("#ai_gen_image").css("display", "block");

    jQuery("#img-input").text(title);
  }

  // Custom validation method to check for non-empty values
  jQuery.validator.addMethod(
    "noSpace",
    function (value, element) {
      // Trim the input and check if it's empty
      return jQuery.trim(value) !== "";
    },
    "Please enter text."
  );

  // Button click handler to regenerate content
  jQuery("#regenerate_text").click(function (e) {
    e.preventDefault();
    jQuery(".regen_load").css("display", "block");
    const wCount = jQuery("#w_count").val();
    const content = jQuery("#p_title").val();
    var formData = new FormData();
    formData.append("user_input", content);
    formData.append("w_count", wCount);
    formData.append("keywords", "");
    formData.append("img_type", "");

    fetch("https://coastalwebsolutions.agency/api", {
      method: "POST",
      body: formData,
    })
      .then((response) => {
        if (!response.ok) {
          throw new Error("Network response was not ok.");
        }
        return response.json(); // Attempt to parse JSON
      })
      .then((data) => {
        jQuery(".lds-roller").css("display", "none");
        jQuery(".regen_load").css("display", "none");
        //jQuery('#ai_gen_image').css('display', 'none');
        if (data.error) {
          console.error("Server error:", data.error);
          // Display error message to user
        } else {
          jQuery("#p_title").val(data.ai_title.replace(/"/g, ""));

          tinymce.get("post_contents").setContent(data.content);
        }
      })
      .catch((error) => {
        console.error("Fetch error:", error);
        jQuery(".lds-roller").css("display", "none");
        // Display fetch error message to user
      });
  });

  // Form validation 
  jQuery("#gpt-chat-form").validate({
    rules: {
      user_input: {
        required: true,
        noSpace: true, // Use the custom validation method
      },
    },
    messages: {
      user_input: {
        required: "Please enter text",
      },
    },
    submitHandler: function (form) {
      // Prevent default form submission
      event.preventDefault();

      // Display loader
      jQuery(".lds-roller").css("display", "block");
      jQuery("#gpt-chat-response").html("");

      // Gather user input
      const userInput = jQuery("#user-input").val();
      const wCount = jQuery("#w_count").val();
      const keywords = jQuery("#keywords").val();
      const imgType = jQuery("#img_type").val();

      var formData = new FormData();
      formData.append("user_input", userInput);
      formData.append("w_count", wCount);
      formData.append("keywords", keywords);
      formData.append("img_type", imgType);

      fetch("https://coastalwebsolutions.agency/api", {
        method: "POST",
        body: formData,
      })
        .then((response) => {
          if (!response.ok) {
            throw new Error("Network response was not ok.");
          }
          return response.json(); // Attempt to parse JSON
        })
        .then((data) => {
          jQuery(".lds-roller").css("display", "none");
          //jQuery('#ai_gen_image').css('display', 'none');
          if (data.error) {
            console.error("Server error:", data.error);
            // Display error message to user
          } else {
            jQuery("#p_title").val(data.ai_title.replace(/"/g, ""));
            jQuery("#img-input").val(data.ai_title.replace(/"/g, ""));
            jQuery("#ai_keywords").val(data.ai_keywords.replace(/"/g, ""));

            //jQuery('#ai_gen_image_img').attr('href', decodeURIComponent(response.img_url));
            jQuery(".img_div").html(
              '<img id="ai_gen_image_img" src="' +
                data.img_url +
                '" width="300" height="300" alt="">'
            );

            tinymce.get("post_contents").setContent(data.content);

            //jQuery('#final_rasult').css('display', 'block');
            showSteps(3);
            //console.log(data.content);
            // Process and display data
            jQuery("#op_post_title").val(data.ai_title);
            // Handle other response data
          }
        })
        .catch((error) => {
          console.error("Fetch error:", error);
          jQuery(".lds-roller").css("display", "none");
          // Display fetch error message to user
        });
    },
  });

  // Format plain text input as HTML
  function formatPlainTextAsHTML(text) {
    // Split the text into paragraphs
    var paragraphs = text.split("\n\n");

    // Format paragraphs with <p> tags
    var formattedParagraphs = paragraphs
      .map((paragraph) => `<p>${paragraph}</p>`)
      .join("");

    // Format ordered list items
    formattedParagraphs = formattedParagraphs.replace(
      /^(\d+): (.*)$/gm,
      "<ol><li>$2</li></ol>"
    );

    // Format unordered list items
    formattedParagraphs = formattedParagraphs.replace(
      /^\. (.*)$/gm,
      "<ul><li>$1</li></ul>"
    );

    return formattedParagraphs;
  }
  
  // Form submission handler for saving optimized post
  jQuery("#add_ai_optimize_post").submit(function (event) {
    event.preventDefault();
    jQuery(".lds-roller").css("display", "block");
    jQuery(".ai_post_save").prop("disabled", true);

    var formData = {
      action: "ai_post_save_as_draft",
      content: jQuery("#post_contents").val(),
      post_title: jQuery("#p_title").val(),
      p_category: jQuery("#p_category").val(),
      featured_image: jQuery("#featured_image").val(),
      datetime: jQuery("#datetime").val(),
      user_keywords: jQuery("#user_keywords").val(),
      save_post_as_draft_nonce: jQuery("#save_post_as_draft_nonce").val(),
      post_tags: jQuery("#post_tags").val(),
    };

    if (!validateForm(formData)) {
      jQuery(".ai_post_save").prop("disabled", false);
      return false;
    }

    jQuery.ajax({
      url: ajaxurl,
      type: "POST",
      data: formData,
      success: function (response) {
        jQuery(".lds-roller").css("display", "none");
        var url = window.location.href;
        window.location.href = url + "&success=Post+created";
      },
      error: function (xhr, status, error) {
        console.error(xhr.responseText);
        showAlert("danger", "Error: Unable to save post.");
      },
    });
  });

  jQuery("#gpt-image-form").validate({
    rules: {
      img_input: {
        required: true,
        noSpace: true,
      },
    },
    messages: {
      img_input: {
        required: "Please enter text",
      },
    },
    submitHandler: function (form) {
      // Prevent default form submission
      event.preventDefault();

      // Display loader
      jQuery(".lds-roller_img").css("display", "block");
      jQuery("#gpt-chat-response").html("");

      // Get user input
      var img_text = jQuery("#img-input").val();
      var imgtype = jQuery("#img_type_gen").val();

      // Update post title input
      jQuery("#op_post_title").val(img_text);
      jQuery(".img_submit_btn").css("display", "none");

      var formData = new FormData();
      formData.append("img_text", img_text);
      formData.append("imgtype", imgtype);

      fetch("https://coastalwebsolutions.agency/api", {
        method: "POST",
        body: formData,
      })
        .then((response) => {
          if (!response.ok) {
            throw new Error("Network response was not ok.");
          }
          return response.json(); // Attempt to parse JSON
        })
        .then((data) => {
          if (data.error) {
            console.error("Server error:", data.error);
            // Display error message to user
          } else {
            jQuery(".lds-roller_img").css("display", "none");
            jQuery(".img_submit_btn").css("display", "block");
            var img_url = data.img_url;
            jQuery("#featured_image").val(img_url);
            var title = jQuery("#p_title").val();
            // Display image
            jQuery(".img_div").html(
              '<img id="ai_gen_image" src="' +
                img_url +
                '" width="300" height="300" alt="' +
                title +
                '">'
            );
            console.log(data);
          }
        })
        .catch((error) => {
          console.error("Fetch error:", error);
          jQuery(".lds-roller_img").css("display", "none");
          // Display fetch error message to user
        });
    },
  });

  // Validation function
  function validateForm(formData) {
    var errors = [];

    if (formData.content === "") {
      errors.push("Content is required.");
    }

    // Add more validation rules as needed

    if (errors.length > 0) {
      jQuery.each(errors, function (index, error) {
        var errorMessage = jQuery("<span>").text(error).css("color", "red");
        switch (index) {
          case 0:
            jQuery("#post_contents_err").after(errorMessage);
            break;
          case 1:
            jQuery("#datetime").after(errorMessage);
            break;
          // Add more cases for each input field
        }
      });

      return false;
    }

    return true;
  }

  var category = jQuery("#p_category");
  var other = jQuery("#other_category");

  category.change(function () {
    console.log(category);
    if (category.val() === "other" && other.hasClass("hidden")) {
      other.removeClass("hidden");
    } else if (!other.hasClass("hidden")) {
      other.addClass("hidden");
      other.val("");
    }
  });

  jQuery("#edit_ai_optimize_post").on("submit", function (e) {
    e.preventDefault(); // Prevent form submission
    var formData = {
      action: "ai_post_update",
      content: jQuery("#post_contents").val(),
      post_id: jQuery("#post_id").val(),
      post_title: jQuery("#p_title").val(),
      p_category: jQuery("#p_category").val(),
      datetime: jQuery("#datetime").val(),
      featured_image: jQuery("#featured_image").val(),
      user_keywords: jQuery("#user_keywords").val(),
      save_post_as_draft_nonce: jQuery("#save_post_as_draft_nonce").val(),
    };

    jQuery.ajax({
      url: ajaxurl, // WordPress AJAX URL
      type: "post",
      data: formData,
      success: function (response) {
        var url = window.location.href;
        window.location.href = url + "&success=Post+Updated!";
      },
      error: function (xhr, status, error) {
        // Handle errors here (e.g., display error message)
        console.error(error);
      },
    });
  });

  function copyImage(imageUrl) {
    const img = document.createElement("img");
    img.crossOrigin = "anonymous";
    img.src = imageUrl;
    img.onload = () => {
      const canvas = document.createElement("canvas");
      const context = canvas.getContext("2d");
      canvas.width = img.width;
      canvas.height = img.height;
      context.drawImage(img, 0, 0);
      const dataURL = canvas.toDataURL("image/png");
      const blob = dataURItoBlob(dataURL);
      navigator.clipboard.write([new ClipboardItem({ "image/png": blob })]);
    };
  }

  // Helper function to convert dataURI to blob
  function dataURItoBlob(dataURI) {
    const binary = atob(dataURI.split(",")[1]);
    const array = [];
    for (let i = 0; i < binary.length; i++) {
      array.push(binary.charCodeAt(i));
    }
    return new Blob([new Uint8Array(array)], { type: "image/png" });
  }

  jQuery("#ai_image_file").change(function () {
    var formData = new FormData();
    formData.append("action", "upload_ai_image"); // Add the action parameter for WordPress AJAX
    formData.append("ai_image_file", jQuery(this).prop("files")[0]); // Append the selected image file to FormData
    console.log(formData);
    jQuery.ajax({
      url: ajaxurl,
      type: "POST",
      data: formData, // Pass the FormData directly as the data
      processData: false,
      contentType: false,
      success: function (response) {
        if (response !== "error") {
          // Set the image URL in the hidden field of the target form
          jQuery("#featured_image").val(response);
          var title = jQuery("#p_title").val();
          jQuery(".img_div").html(
            '<img id="ai_gen_image" src="' +
              response +
              '" width="300" height="300" alt="' +
              title +
              '">'
          );
          console.log(response);
        } else {
          console.error("Error uploading image.");
        }
      },
      error: function (xhr, textStatus, errorThrown) {
        console.error("Error uploading image.");
      },
    });
  });

  jQuery("#keyowrd_form").validate({
    rules: {
      keywords: {
        required: true,
        noSpace: true, // Use the custom validation method
      },
    },
    messages: {
      keywords: {
        required: "Please enter keyword",
      },
    },
    submitHandler: function (form) {
      // Prevent default form submission
      event.preventDefault();

      // Display loader
      jQuery(".lds-roller").css("display", "block");
      jQuery("#gpt-chat-response").html("");

      // Get user input
      var keywords = jQuery("#keywords").val();
      //var wCount = jQuery('#w_count').val();

      // AJAX request
      jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        data: {
          action: "gpt_keyword_ajax",
          keywords: keywords,
        },
        success: function (response) {
          jQuery(".lds-roller").hide();
          if (response.success) {
            var chatResponse = response.data.chat_response;
            var lines = chatResponse
              .split("\n")
              .filter((line) => line.trim() !== ""); // Remove empty lines

            var titleIndex = lines.findIndex((line) =>
              line.startsWith("Title: ")
            );
            var title = "";
            var description = "";

            if (titleIndex !== -1) {
              title = lines[titleIndex].replace("Title: ", "").trim();
              // Exclude the keywords and title lines when forming the description
              description = lines
                .slice(titleIndex + 1)
                .join("\n")
                .trim();
            }

            // Convert newlines in the description to HTML paragraphs
            var formattedDescription = description
              .split("\n")
              .map(function (paragraph) {
                return "<p>" + paragraph.trim() + "</p>";
              })
              .join("");

            jQuery("#p_title").val(title);
            tinyMCE.activeEditor.setContent(`${formattedDescription}`);
            jQuery("button.ai_keyword_gen").text("Update Content");
            jQuery("#img-input").text(title);
            jQuery(".img_submit_btn").trigger("click");
            jQuery(".op_submit_btn").css("display", "block");
            jQuery("#ai_gen_image").css("display", "block");
            // console.log(response.data.chat_response);
            // Display the title and description
          } else {
            console.log("error");
          }
        },
        error: function (xhr, status, error) {
          console.error(xhr.responseText);
          jQuery("#gpt-chat-response").html(
            "Error: Unable to fetch response from the server."
          );
        },
      });
    },
  });

  jQuery(".save_ai_post").click(function () {
    jQuery(".ai_post_save").click();
  });

  const input = jQuery("#p_tags");
  const tagContainer = document.querySelector("#tag-container");
  let tags = [];
  jQuery("#tag-container").hide();
  // Function to create a tag element
  function createTagElement(tagText) {
    const tag = document.createElement("span");
    tag.classList.add("tag");
    tag.innerHTML = `${tagText} <button type="button" aria-label="Remove Tag">&times;</button>`;
    tagContainer.appendChild(tag);
    jQuery("#tag-container").show();
    // Remove tag on button click
    tag.querySelector("button").addEventListener("click", () => {
      removeTag(tagText);
    });
  }

  // Function to update the hidden input with tags
  function updateHiddenInput() {
    const hiddenInput = document.querySelector('input[name="post_tags"]');
    hiddenInput.value = tags.join(",");
  }

  // Function to add a tag
  function addTag(tagText) {
    if (tags.includes(tagText)) {
      return; // Prevent duplicate tags
    }
    tags.push(tagText);
    createTagElement(tagText);
    updateHiddenInput();
  }

  // Function to remove a tag
  function removeTag(tagText) {
    tags = tags.filter((tag) => tag !== tagText);
    const tagElements = Array.from(tagContainer.querySelectorAll(".tag"));
    const tagToRemove = tagElements.find((tagElement) =>
      tagElement.textContent.includes(tagText)
    );
    tagContainer.removeChild(tagToRemove);
    updateHiddenInput();
  }

  // jQuery UI autocomplete for tag suggestions
  input.autocomplete({
    source: function (request, response) {
      jQuery.ajax({
        url: ajaxurl,
        dataType: "json",
        data: {
          action: "get_tag_suggestions",
          term: request.term,
        },
        success: function (data) {
          response(data);
        },
      });
    },
    select: function (event, ui) {
      event.preventDefault();
      const selectedTag = ui.item.value;
      addTag(selectedTag); // Add the selected tag to the tag container
      input.val(""); // Clear input field after selecting a tag
    },
    focus: function (event, ui) {
      event.preventDefault();
      input.val(ui.item.value); // Display the focused suggestion in the input field
    },
  });

  // Event listener for manual input (comma or Enter key)
  input.on("keydown", function (event) {
    const inputValue = input.val().trim();

    // Add tag on Enter key or comma
    if ((event.key === "Enter" || event.key === ",") && inputValue.length > 0) {
      event.preventDefault();
      addTag(inputValue);
      input.val(""); // Clear input field after adding a tag
    }
  });

  // Event listener for comma key (without submitting form)
  input.on("input", function () {
    const inputValue = input.val().trim();
    if (inputValue.includes(",")) {
      const tagText = inputValue.split(",")[0].trim();
      if (tagText.length > 0) {
        addTag(tagText);
        input.val(""); // Clear input after comma
      }
    }
  });
});

//form-steps//
document.addEventListener("DOMContentLoaded", function () {
  var currentStep = 1;
  var totalSteps = 3;

  function showStep(step) {
    for (var i = 1; i <= totalSteps; i++) {
      document.getElementById("step-" + i).style.display = "none";
      if (i <= step) {
        document.getElementById("choose-step-" + i).classList.add("active");
      } else {
        document.getElementById("choose-step-" + i).classList.remove("active");
      }
    }
    document.getElementById("step-" + step).style.display = "block";
  }

  function validateStep(step) {
    var isValid = true;

    if (step === 1) {
      var userInput = document.getElementById("user-input").value.trim();
      if (userInput === "") {
        isValid = false;
        alert("Please fill in the blog post topic.");
      }
    } else if (step === 2) {
      var wordCount = document.getElementById("w_count").value.trim();
      var keywords = document.getElementById("keywords").value.trim();
      if (wordCount === "" || keywords === "") {
        isValid = false;
        alert("Please fill in the word count and keywords.");
      }
    }

    return isValid;
  }

  // Event listener for the first "Next" button
  document
    .querySelector("#step-1 #next-step")
    .addEventListener("click", function () {
      if (validateStep(currentStep)) {
        if (currentStep < totalSteps) {
          console.log(currentStep);
          currentStep++;
          showStep(currentStep);
        }
      }
    });

  // Event listener for the second "Next" button
  document
    .querySelector("#step-2 #next-step")
    .addEventListener("click", function () {
      if (validateStep(currentStep)) {
        if (currentStep < totalSteps) {
          currentStep++;
          console.log(currentStep);
          jQuery("#chat_sub_btn").click();

          //showStep(currentStep);
        }
      }
    });

  // Event listener for the "Previous" button in step 2
  document
    .querySelector("#step-2 #previous-step")
    .addEventListener("click", function () {
      if (currentStep > 1) {
        currentStep--;
        showStep(currentStep);
      }
    });

  // Event listener for the "Previous" button in step 3
  document
    .querySelector("#step-3 #previous-step")
    .addEventListener("click", function () {
      if (currentStep > 1) {
        currentStep--;
        showStep(currentStep);
      }
    });

  // Initialize the form to show the first step
  showStep(currentStep);
});
