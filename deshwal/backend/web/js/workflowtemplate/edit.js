$(document).ready(function () {
    $.getScript(getAbsoluteUrl()+"../js/ckeditor/ckeditor.js", function () {
        if (!CKEDITOR.instances['workflowtemplate-body']) {
            CKEDITOR.replace('workflowtemplate-body');
        }
    });
  getTemplatedata();
  //save data
  $(document).on("click", ".savetemplate", function (e) {
    
    e.preventDefault();
    const modeInput = document.getElementById("mode");
    let name = $("#workflowtemplate-name").val().trim();
    let subject = $("#workflowtemplate-subject").val().trim();
    let body = $("#workflowtemplate-body").val().trim();

    let error = false;

    // Reset errors
    $(".field-workflowtemplate-name, .field-workflowtemplate-subject, .field-workflowtemplate-body")
      .removeClass("has-error")
      .find(".help-block")
      .text("");

    // Validate Name
    if (name === "") {
      $("#workflowtemplate-name").closest(".field-workflowtemplate-name")
        .addClass("has-error")
        .find(".help-block").text("Name is required.");
      error = true;
    }

    // Validate Subject
    if (subject === "") {
      $("#workflowtemplate-subject").closest(".field-workflowtemplate-subject")
        .addClass("has-error")
        .find(".help-block").text("Subject is required.");
      error = true;
    }

    // Validate Body
    if (body === "") {
      $("#workflowtemplate-body").closest(".field-workflowtemplate-body")
        .addClass("has-error")
        .find(".help-block").text("Body is required.");
      error = true;
    }

    if (!error) {
      // Submit form if no error
      // $("form").submit();

     
      if (modeInput && modeInput.value === "Create") {
       const form = document.getElementById("pristine-valid-example");
       form.submit();
      }
      else
      {
        let data = {
          _csrf: $("#csrfToken").val(),
          id: $("#editRecordId").val(),
          name: $("#workflowtemplate-name").val(),
          subject: $("#workflowtemplate-subject").val(),
          body: $("#workflowtemplate-body").val(),
        };

        $.ajax({
          type: "POST",
          url: "savetemplate",
          data: data,
          success: function (data) {
            if (data.status === "success") {
              $("#workflowtemplateModal").modal("hide");
              alert(data.message);              
              getTemplatedata();
            }
            else alert(data.message);
          },
          error: function (data) {
            alert("Error occured.please try again");
          },
          dataType: "json",
        });
    }
    }

  });

  function getTemplatedata() {
    startLoading();
    $.ajax({
      url: "getdata",
      type: "GET",
      success: function (response) {
        let tableBody = $("#relatedtable tbody");

        // Create <tbody> if missing
        if (tableBody.length === 0) {
          $("#relatedtable").append("<tbody></tbody>");
          tableBody = $("#relatedtable tbody");
        }

        tableBody.empty(); // clear previous rows

        if (response.status === 'error') {
          // No data found
          tableBody.append(`
                  <tr>
                      <td colspan="2" class="text-center">${response.message}</td>
                  </tr>
              `);
        } else if (response.status === 'success' && response.data.length > 0) {
          // Populate table with data
          $.each(response.data, function (i, item) {
            tableBody.append(`
                      <tr 
                          data-id="${item.id}"
                          data-name="${item.name}"
                      >
                          <td>${item.name}</td>
                          <td>
                              <button class="btn btn-primary btn-sm edittemplate" id="edittemplate">Edit</button>
                          </td>
                      </tr>
                  `);
          });
        } else {
          // Fallback if data array is empty
          tableBody.append(`
                  <tr>
                      <td colspan="2" class="text-center">No data available</td>
                  </tr>
              `);
        }

        // pagination start
        // pagination end
         stopLoading();
      }

    });
  }

  $(document).on("click", ".edittemplate", function (e) {


    let row = $(this).closest("tr");
    let template_id = row.data("id");
    getTemplatedatabyid(template_id);
  });

  function getTemplatedatabyid(template_id) {
    $("#editRecordId").val(template_id);
    $.ajax({
      url: "gettemplatedatabyid",
      type: "POST",
      data: { template_id: template_id, _csrf: $("#csrfToken").val(), },
      success: function (response) {
        if (response.status == "success") {
          $("#workflowtemplate-name").val(response.data.name);
          $("#workflowtemplate-subject").val(response.data.subject);
          $("#workflowtemplate-body").val(response.data.body);
          $("#workflowtemplateModal").modal("show");
        }
        else
          alert(response.message);
      }
    });
  }
});
function getAbsoluteUrl() {
        var newURL = window.location.href;
        var module = jQuery("#module").val();
        var str = newURL.indexOf(module);

        var slicestr = newURL.substring(0, str);
        return slicestr;
    }