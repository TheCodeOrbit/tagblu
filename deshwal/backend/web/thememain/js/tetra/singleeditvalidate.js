  $(document).on("click", ".singleeditsavebtn", function() {
    // alert("inside modal event");
    saveModalData();
  });

function saveModalData() {
    var validator = new Validator();
  
      var form = document.getElementById("pristine-valid-example");
      // Validate on change for all inputs and select2 dropdowns
      $(".form-control, input[type='radio'], input[type='file'], input[type='checkbox'], .leave, .singleselect .multySelect").on("change", function () {
        // For regular inputs and file types, validate directly
        if ($(this).is(":visible") || $(this).hasClass("leave")) {
            validator.validateField($(this));
        }
  
        // For Select2, use the 'select2:select' event instead
        if ($(this).hasClass("singleselect")) {
            $(this).on("select2:select", function () {
                validator.validateField($(this));
            });
        }
        // For Select2, use the 'select2:select' event instead
        if ($(this).hasClass("multySelect")) {
            $(this).on("select2:select", function () {
                validator.validateField($(this));
            });
        }
    });
  
      let isValid = true;  // Start with assuming all fields are valid
      // //   alert("Sdf");
    // Validate all visible fields
      $(".form-control, input[type='radio'], input[type='file'], input[type='checkbox'], .leave, .singleselect .multySelect").each(function () {
          // For regular inputs and file types, validate directly
          if ($(this).is(":visible") || $(this).hasClass("leave")) {
              // Validate field and set isValid to false if any field is invalid
              if (!validator.validateField($(this))) {
                  isValid = false;
                  // alert("Field validation failed: " + $(this).attr('name'));
              }
          }
  
          // For Select2, use the 'select2:select' event instead
          if ($(this).hasClass("singleselect")) {
              $(this).on("select2:select", function () {
                  if (!validator.validateField($(this))) {
                      isValid = false;
                      // alert("Select2 validation failed: " + $(this).attr('name'));
                  }
              });
          }
  
          // For multi-select, use the 'select2:select' event instead
          if ($(this).hasClass("multySelect")) {
              $(this).on("select2:select", function () {
                  if (!validator.validateField($(this))) {
                      isValid = false;
                      // alert("Multi-Select validation failed: " + $(this).attr('name'));
                  }
              });
          }
      });
  
      // If all fields are valid, submit the form
      if (isValid) {
      // alert("form submit");
      //   form.submit();
        let form = $('#editModal .modal-body').find('form'); // Get the form inside modal
        
        let tablename = $("#tablename").val();
        let formData = new FormData(form[0]); // Create FormData object
        formData.append('_csrf', yii.getCsrfToken());
        let columnname = formData.get("columnname");
        let recordid = formData.get("recordid");
        let from_page = formData.get("from_page");
        startLoading();
        $.ajax({
          url: 'edit?Record='+recordid, // Update with your actual controller URL
          type: 'POST',
          data: formData,
          processData: false,  // Don't convert data to query string
          contentType: false,  // Let the browser set content type (needed for file uploads)
          success: function(response) {    
            console.log("in response"+response.success +"---"+response.from_page);  
            if(response.success && response.from_page === "multiple")//
                    { 
                        console.log("first"+columnname);
                        //   $("#summary").html('');
                       if(response.html){
                            // create a temporary container
                            var $temp = $('<div>').html(response.html);

                            // extract only inner content (remove outer #summary div)
                            var innerHtml = $temp.find('#summary').html();

                            $("#summary").html(innerHtml);
                        }

                        if(response.historyHtml){
                            var $tempHist = $('<div>').html(response.historyHtml);
                            var innerHist = $tempHist.find('#history').html();
                            $("#history").html(innerHist);
                        }
                          $('#editModal').modal('hide'); // Close the modal on success
                          let selectedColumns = document.getElementsByClassName('detail-' + columnname);
                          if (selectedColumns.length > 0) {  // Check if any element exists
                          let firstElement = selectedColumns[0];  // Select the first matching element
                          const parentDetails = firstElement.closest("details").id; // Find nearest <details> parent
                          const detailsElement = document.getElementById(parentDetails);
                              // Restore open state from localStorage
                              if (localStorage.getItem("leadDetailsOpen_"+parentDetails) === "true") {
                                  detailsElement.setAttribute("open", "");
                              }
                              restoreDetailsState(parentDetails); 
                          }  
                      
                          // Hide modal properly
                    const modalEl = document.getElementById('editModal');

                    // Try to get Bootstrap modal instance
                    let modalInstance = bootstrap.Modal.getInstance(modalEl);

                    //  If not found, reinitialize it
                    if (!modalInstance) {
                        modalInstance = new bootstrap.Modal(modalEl);
                    }

                    //  Use a small delay to ensure AJAX-rendered content doesn't block hide
                    setTimeout(() => {
                      modalEl.classList.remove('show');
                      modalEl.setAttribute('aria-hidden', 'true');
                      modalEl.style.display = 'none';

                      // Remove any leftover backdrop
                      document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());

                      document.body.classList.remove('modal-open');
                      document.body.style.removeProperty('padding-right');

                      console.log(" Modal forcibly closed");
                  }, 400);

                    //   if(from_page == "list")
                    //   {
                            stopLoading(); 
                    //   }
                }
                if (response.success && response.from_page === "summary") { 
                    if(response.html){
                        // create a temporary container
                        var $temp = $('<div>').html(response.html);

                        // extract only inner content (remove outer #summary div)
                        var innerHtml = $temp.find('#summary').html();

                        $("#summary").html(innerHtml);
                    }

                    if(response.historyHtml){
                        var $tempHist = $('<div>').html(response.historyHtml);
                        var innerHist = $tempHist.find('#history').html();
                        $("#history").html(innerHist);
                    }
                    // Hide modal properly
                    const modalEl = document.getElementById('editModal');

                    // Try to get Bootstrap modal instance
                    let modalInstance = bootstrap.Modal.getInstance(modalEl);

                    //  If not found, reinitialize it
                    if (!modalInstance) {
                        modalInstance = new bootstrap.Modal(modalEl);
                    }

                    //  Use a small delay to ensure AJAX-rendered content doesn't block hide
                    setTimeout(() => {
                      modalEl.classList.remove('show');
                      modalEl.setAttribute('aria-hidden', 'true');
                      modalEl.style.display = 'none';

                      // Remove any leftover backdrop
                      document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());

                      document.body.classList.remove('modal-open');
                      document.body.style.removeProperty('padding-right');

                      console.log(" Modal forcibly closed");
                  }, 400);


                    let selectedColumns = document.getElementsByClassName('detail-' + columnname);
                      if (selectedColumns.length > 0) {  
                          console.log(selectedColumns);
                          let firstElement = selectedColumns[0];
                          const detailsEl = firstElement.closest("details");

                          if (detailsEl) {
                              // give temporary ID if not exists
                              if (!detailsEl.id) {
                                  detailsEl.id = "details_temp_" + columnname;
                              }

                              const parentDetails = detailsEl.id;

                              if (localStorage.getItem("leadDetailsOpen_" + parentDetails) === "true") {
                                  detailsEl.setAttribute("open", "");
                              }
                              restoreDetailsState(parentDetails);
                          } else {
                              console.warn(" No <details> parent found for:", columnname);
                          }
                      }

                      stopLoading();

                }


               if (response.success && response.from_page === "list") {

                    // Close modal
                    $('#editModal').removeClass('show').hide();
                    $('body').removeClass('modal-open');
                    $('.modal-backdrop').remove();

                    // Update grid data
                    fetchAndSetColumnDefinitions();
                    fetchRowData();
                    if (typeof filterGridByTagNumber === "function") {
                         $("#searchTagInput").val("");
                        filterGridByTagNumber('');
                    }

                    // Stop loading spinner
                    if (typeof stopLoading === "function") {
                        stopLoading();
                    }
                }

                
          },
          error: function(xhr, status, error) {
              console.error('Error:', error + status);
              stopLoading();
          }
        });
      // Close the modal
      // var myModal = bootstrap.Modal.getInstance(document.getElementById('editModal'));
      // myModal.hide();

      // const modalEl = document.getElementById('editModal');
      // let myModal = bootstrap.Modal.getInstance(modalEl);

      // if (!myModal) {
      //   myModal = new bootstrap.Modal(modalEl);
      // }

      // myModal.hide();
      // singleeditformsubmit();
  
      } else {
          // Prevent form submission if any field is invalid
        //   e.preventDefault();
          $('html, body').animate({
              scrollTop: $(".help-block:visible:first").offset().top
          }, 500);
      } 
  }
  
  
    // Function to restore the open state after an AJAX refresh
    function restoreDetailsState(id) {
    const detailsElement = document.getElementById(id);
    if (detailsElement) {
        detailsElement.setAttribute("open", "");
    }
    stopLoading();
}