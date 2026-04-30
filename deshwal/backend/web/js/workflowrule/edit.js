$(document).ready(function () {
  let module = $("#workflowrule-module").val();
  $(document).on("click", "#refresh-icon", function (e) {
    location.reload(); // Reload the current page
  });
  $(".section-workflowrule-stage_id").hide();
  if (module != '') {
    getRuleFields(module);
  }
  $(document).on("change", "#workflowrule-module", function (e) {
    getRuleFields($(this).val());
  });
  function getRuleFields(tabname) {
    startLoading();
    $.ajax({
      url: "getfields",
      type: "GET",
      data: { tabname: tabname, _csrf: $("#csrfToken").val(), },
      success: function (response) {

        let dropdown = $("#workflowrule-trigger_fields");
        let availableList = $("#availableFields");
        availableList.empty();
        dropdown.addClass("form-control productinput singleselect DD~M")
        dropdown.empty();
        dropdown.append('<option value="">Select Field</option>');

        let form = $("#pristine-valid-example").data("yiiActiveForm");
        if (form) {
          form.settings.validateOnChange = false;
          form.settings.validateOnBlur = false;
          form.settings.validateOnType = false;
        }

        $.each(response, function (i, item) {
          dropdown.append(
            `<option value="${item.fieldname}">
                            ${item.fieldlabel}
                        </option>`
          );
          let oldValue = $("#workflowrule-trigger_fields").data("value");
          if (oldValue) {
            $("#workflowrule-trigger_fields").val(oldValue);
          }
          // availableList.append(
          //   `<option value="${item.fieldname}">
          //       ${item.fieldlabel}
          //   </option>`
          // );
          availableList.append(`
            <div class="field-item"
              draggable="true"
              data-label="${item.fieldlabel}">
              ${item.fieldlabel}
          </div>
        `);
        });


        // if (module != '') {
          $("#workflowrule-trigger_fields").val(modulefields).trigger('change');
        // }
        // $("#relatedcolumnModal").modal("show");
      }
    });
    stopLoading();
  }
  /* $(document).on("click", ".savebutton", function (e) {
     e.preventDefault();
     const modeInput = document.getElementById("mode");
     let module = $("#workflowrule-module").val().trim();
     let rulename = $("#workflowrule-name").val().trim();
     let event = $("#workflowrule-trigger_event").val().trim();
     let fields = $("#workflowrule-trigger_fields").val().trim();
     // let template = $("#workflowrule-template_id").val().trim();
     let ttype = $("#workflowrule-trigger_type").val().trim();
     let active = $("#workflowrule-active").val().trim();
 
     let temp_name = $("#workflowtemplate-name").val().trim();
     let temp_subject = $("#workflowtemplate-subject").val().trim();
     //this is required becuse 
     if (window.workflowEditor) {
         // 2) Sync CKEditor data into textarea
         $("#workflowtemplate-body").val(window.workflowEditor.getData());
     }
 
     let temp_body = $("#workflowtemplate-body").val().trim();
     let error = false;
 
     let required = "This field is mandatory.";
     // Reset errors
     $(".field-workflowtemplate-name, .field-workflowtemplate-subject, .field-workflowtemplate-body, .field-workflowrule-module , .field-workflowrule-name, .field-workflowrule-trigger_event, .field-workflowrule-trigger_fields, .field-workflowrule-trigger_type, .field-workflowrule-active")
       .removeClass("has-error")
       .find(".help-block")
       .text("");
 
     // Validate Name
     if (temp_name === "") {
       $("#workflowtemplate-name").closest(".field-workflowtemplate-name")
         .addClass("has-error")
         .find(".help-block").text(required);
       error = true;
     }
 
     // Validate Subject
     if (temp_subject === "") {
       $("#workflowtemplate-subject").closest(".field-workflowtemplate-subject")
         .addClass("has-error")
         .find(".help-block").text(required);
       error = true;
     }
 
     // Validate Body
     if (temp_body === "") {
       $("#workflowtemplate-body").closest(".field-workflowtemplate-body")
         .addClass("has-error")
         .find(".help-block").text(required);
       error = true;
     }
     // rule fields start
     if (module === "") {
       $("#workflowrule-module").closest(".field-workflowrule-module")
         .addClass("has-error")
         .find(".help-block").text(required);
       error = true;
     }
 
     if (rulename === "") {
       $("#workflowrule-name").closest(".field-workflowrule-name")
         .addClass("has-error")
         .find(".help-block").text(required);
       error = true;
     }
 
     if (event === "") {
       $("#workflowrule-trigger_event").closest(".field-workflowrule-trigger_event")
         .addClass("has-error")
         .find(".help-block").text(required);
       error = true;
     }
 
     if (fields === "") {
       // $("#workflowrule-trigger_fields").closest(".field-workflowrule-trigger_fields").hasClass("")
       $("#workflowrule-trigger_fields").closest(".field-workflowrule-trigger_fields")
         .addClass("has-error")
         .find(".help-block").text(required);
       error = true;
     }
 
     if (ttype === "") {
       $("#workflowrule-trigger_type").closest(".field-workflowrule-trigger_type")
         .addClass("has-error")
         .find(".help-block").text(required);
       error = true;
     }
 
     if (active === "") {
       $("#workflowrule-active").closest(".field-workflowrule-active")
         .addClass("has-error")
         .find(".help-block").text(required);
       error = true;
     }
     // rule fields end
     if (!error) {
       const form = document.getElementById("pristine-valid-example");
       form.submit();
     }
   });*/

  $(document).on("click", ".savebutton", function (e) {

    e.preventDefault();

    const modeInput = document.getElementById("mode");

    let module = $("#workflowrule-module").val().trim();
    let rulename = $("#workflowrule-name").val().trim();
    let event = $("#workflowrule-trigger_event").val().trim();
    let fields = $("#workflowrule-trigger_fields").val().trim();
    let ttype = $("#workflowrule-trigger_type").val().trim();
    let active = $("#workflowrule-active").val().trim();

    let copyTemplate = $("#workflowrule-copy_template_id").val();   // <--- IMPORTANT

    // Sync CKEditor
    if (window.workflowEditor) {
      $("#workflowtemplate-body").val(window.workflowEditor.getData());
    }

    let temp_name = $("#workflowtemplate-name").val().trim();
    let temp_subject = $("#workflowtemplate-subject").val().trim();
    let temp_body = $("#workflowtemplate-body").val().trim();

    let error = false;
    let required = "This field is mandatory.";

    // Reset old errors
    $(".has-error .help-block").text("");
    $(".has-error").removeClass("has-error");

    // --------------------------
    // RULE VALIDATION (always)
    // --------------------------

    if (module === "") {
      $("#workflowrule-module").closest(".field-workflowrule-module")
        .addClass("has-error")
        .find(".help-block").text(required);
      error = true;
    }

    if (rulename === "") {
      $("#workflowrule-name").closest(".field-workflowrule-name")
        .addClass("has-error")
        .find(".help-block").text(required);
      error = true;
    }

    if (event === "") {
      $("#workflowrule-trigger_event").closest(".field-workflowrule-trigger_event")
        .addClass("has-error")
        .find(".help-block").text(required);
      error = true;
    }

    if (fields === "") {
      $("#workflowrule-trigger_fields").closest(".field-workflowrule-trigger_fields")
        .addClass("has-error")
        .find(".help-block").text(required);
      error = true;
    }

    if (ttype === "") {
      $("#workflowrule-trigger_type").closest(".field-workflowrule-trigger_type")
        .addClass("has-error")
        .find(".help-block").text(required);
      error = true;
    }

    if (active === "") {
      $("#workflowrule-active").closest(".field-workflowrule-active")
        .addClass("has-error")
        .find(".help-block").text(required);
      error = true;
    }

    // --------------------------------------------------------
    // TEMPLATE VALIDATION
    // If COPY TEMPLATE selected → skip name/subject/body check
    // --------------------------------------------------------

    if (!copyTemplate || copyTemplate === "") {

      // Validate Name
      if (temp_name === "") {
        $("#workflowtemplate-name").closest(".field-workflowtemplate-name")
          .addClass("has-error")
          .find(".help-block").text(required);
        error = true;
      }

      // Validate Subject
      if (temp_subject === "") {
        $("#workflowtemplate-subject").closest(".field-workflowtemplate-subject")
          .addClass("has-error")
          .find(".help-block").text(required);
        error = true;
      }

      // Validate Body
      if (temp_body === "") {
        $("#workflowtemplate-body").closest(".field-workflowtemplate-body")
          .addClass("has-error")
          .find(".help-block").text(required);
        error = true;
      }
    }

    // --------------------------
    // Submit if no error
    // --------------------------
    if (!error) {
      document.getElementById("pristine-valid-example").submit();
    }

  });

  //added code for approve triger
  $(document).on(
  "change",
  "#workflowrule-trigger_event, #workflowrule-trigger_fields",
  function () {

    let triggerEvent = $("#workflowrule-trigger_event").val();

    if (triggerEvent === "approve") {
      $(".section-workflowrule-stage_id").show();
      let modulename = $("#workflowrule-module").val();
      let field = $("#workflowrule-trigger_fields").val();

      if (field) {
        getstagedata(modulename, field);
      }
    }
  }
);

   function getstagedata(modulename, field) {
    startLoading();

    $.ajax({
      url: "getstagedata",
      type: "GET",
      data: {
        modulename: modulename,
        field: field,
        _csrf: $("#csrfToken").val(),
      },
      success: function (response) {

        let dropdown = $("#workflowrule-stage_id");

        dropdown.empty(); 
        dropdown.append('<option value="">Select Stage</option>');

        if (response.success && response.items.length > 0) {
          $.each(response.items, function (i, item) {
            dropdown.append(
              `<option value="${item.id}">${item.value}</option>`
            );
          });
        } else {
          console.warn(response.message);
        }
      },
      complete: function () {
        stopLoading(); // 
      }
    });
  }
  //end code added for approve trigger
  $("#dtrecord").DataTable({
    processing: true,
    serverSide: false,
    ajax: "getallworkflowrules",
    columns: [
      // { data: "id" },
      { data: "name" },
      { data: "module_name" },
      { data: "template" },
      { data: "active" },
      { data: "action" },
    ],
  });

  //to drag options
  // ------------------------------------------
  // DRAG START
  // ------------------------------------------
  $(document).on("dragstart", ".field-item", function (e) {

    let rawLabel = $(this).data("label");
    let cleanLabel = rawLabel.replace(/\s+/g, "");

    let text = "{" + cleanLabel + "}";

    e.originalEvent.dataTransfer.setData("text/plain", text);
  });


  // ------------------------------------------
  // SUBJECT
  // ------------------------------------------
  $("#workflowtemplate-subject").on("dragover", function (e) {
    e.preventDefault();
  });

  $("#workflowtemplate-subject").on("drop", function (e) {
    e.preventDefault();

    let text = e.originalEvent.dataTransfer.getData("text/plain");
    insertAtCaret(this, text);
  });


  // ------------------------------------------
  // BODY (textarea OR CKEditor)
  // ------------------------------------------
  $("#workflowtemplate-body").on("dragover", function (e) {
    e.preventDefault();
  });

  $("#workflowtemplate-body").on("drop", function (e) {
    // e.preventDefault();

    let text = e.originalEvent.dataTransfer.getData("text/plain");

    if (typeof CKEDITOR !== "undefined" && CKEDITOR.instances['workflowtemplate-body']) {
      CKEDITOR.instances['workflowtemplate-body'].insertText(text);
    } else {
      insertAtCaret(this, text);
    }
  });


  // ------------------------------------------
  // Insert at caret
  // ------------------------------------------
  function insertAtCaret(el, text) {
    let start = el.selectionStart;
    let end = el.selectionEnd;
    let val = el.value;

    el.value = val.slice(0, start) + text + val.slice(end);
    el.selectionStart = el.selectionEnd = start + text.length;
  }

  //end to drag options

  $('#copy_template_checkbox').on('change', function () {
    if ($(this).is(':checked')) {
      // hide new template section
      $('.workflow-template-title').closest('.accordion-item').hide();

      // show dropdown
      $('#copy_template_section').show();
    } else {
      // show new template form
      $('.workflow-template-title').closest('.accordion-item').show();

      // hide dropdown
      $('#copy_template_section').hide();
    }
  });
});