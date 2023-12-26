jQuery(document).ready(function ($) {
  $("#custom-contact-form").submit(function (event) {
    event.preventDefault();
    var formData = $(this).serialize();
    // console.log(formData);
    var submitButton = $("#custom-contact-form button");
    var formResponseDiv = $("#form-response");

    $.ajax({
      url: ajax_object.ajax_url,
      method: "POST",
      data: formData,
      beforeSend: function () {
        submitButton.prop("disabled", true);
        message = '<div class="info">Working...</div>';
        formResponseDiv.html(message);
      },
      success: function (response) {
        // console.log(response);
        handleFormResponse(response);
      },
      error: function (jqXHR, textStatus, errorThrown) {
        console.error("AJAX error:", textStatus, errorThrown);
      },
      complete: function () {
        submitButton.prop("disabled", false);
        setTimeout(function () {
          formResponseDiv.html("");
        }, 10000);
      },
    });
  });

  function handleFormResponse(response) {
    var formResponseDiv = $("#form-response");
    var message = "";

    if (response.data) {
      if (response.data.status === true) {
        message =
          "<div class=" +
          response.data.class +
          ">" +
          response.data.message +
          "</div>";
      } else {
        message =
          "<div class=" +
          response.data.class +
          ">" +
          response.data.message +
          "</div>";
      }
    } else {
      message = '<div class="danger">An unexpected error occurred.</div>';
    }

    formResponseDiv.html(message);

    setTimeout(function () {
      formResponseDiv.html("");
    }, 10000);
  }
});
