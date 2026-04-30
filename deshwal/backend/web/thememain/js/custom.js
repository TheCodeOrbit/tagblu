let currentPage = 1; // Current page number
//change as per ERP Point 477 on date 07-10-2025 by ptpatel 
let pageSize = parseInt(document.getElementById("page-size").value, 10); // Default page size
let totalPages = 1; // Total pages (calculated dynamically)
let sortby = "";
let sortorder = "";

  // code for filter widget data in aggrid on date 26-05-25
//   $(document).ready(function() {
//     if (window.location.search) {
//       var widgetId = $("#widget_filter_id").val();
//       if (widgetId) {
//         //filter widget code start
//         startLoading(); // Show loading overlay
//         const startRow = (currentPage - 1) * pageSize;
//         const urlParams = new URLSearchParams(window.location.search);
//         // console.log(urlParams);
//         // Get a specific parameter by name
//         const filterid = urlParams.get("id");
//         var url = "getwidgettabledata?start=" + startRow + "&limit=" + pageSize;
//         if (filterid) {
//           // Check if both sourcemodule and sourceid are not null or undefined
//           url += `&filterid=${encodeURIComponent(filterid)}`;
//         }
//         csrfTokenName = $("#csrfTokenName").val();
//         csrfToken = $("#csrfToken").val();
//         // console.log("csrd" + csrfToken);
//         if (filterid) {
//           data = {
//             filterid:filterid,
//             _csrf: csrfToken,
//           };
//         }
//       //  if(validateFliterForm()) {
//           $.ajax({
//             url: url, //"filterbylead",
//             method: "POST",
//             dataType: "json",
//             data: data,
//             success: function (data) {
//               console.log("this data", data);
//               gridOptions.api.setGridOption("rowData", data.RecordList);
//               if (data && data.totalitemcount && data.totalitemcount.noofpages) {
//                 // totalPages = data.totalitemcount.noofpages;
//                 // currentPage = data.totalitemcount.pagejumps;
//                 // console.log("Total pages:", totalPages);  // Log total pages
//                 // console.log("currentPage pages:", currentPage);  // Log total pages
//               }
//               totalPages = Math.ceil(data.totalitemcount.totrecords / pageSize);
//               renderPaginationButtons(); // Render pagination after data is fetched

//               // Update pagination info
//               updatePaginationInfo(data.totalitemcount.totrecords);
//             },
//             error: function (error) {
//               console.error("Error fetching row data:", error);
//             },
//             complete: function () {
//               stopLoading();
//             },
//           });
//         //end filter widget code
//       }
//     }
// });
//to resolve server issue on date 01-07-25 added by ptpatel
$(document).on("change", '.page-size-dropdown', function () {
  // alert("from custome.js");
  changePageSize();
});
$(document).on("click", '.last-page', function () {
  if (currentPage < totalPages) {
    goToPage(currentPage + 1);
  }
});
$(document).on("click", '.first-page', function () {
  if (currentPage > 1) {
    goToPage(currentPage - 1);
  }
});
  // end code for filter widget data in aggrid on date 26-05-25
function getAbsoluteUrl() {
  var newURL = window.location.href;
  var module = jQuery("#module").val();
  var str = newURL.indexOf(module);

  var slicestr = newURL.substring(0, str);
  return slicestr;
}
function startLoading() {
  const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

  // Add the active class to show the overlay
  $("#loading-overlay").addClass("active");

  // Prevent scrolling
  $("body").addClass("loading").css("top", `-${scrollTop}px`);
}

function stopLoading() {
  const scrollTop = Math.abs(parseInt($("body").css("top"), 10));

  // Remove the active class to hide the overlay
  $("#loading-overlay").removeClass("active");

  // Re-enable scrolling
  $("body").removeClass("loading").css("top", "");
  window.scrollTo(0, scrollTop);
}

function getModuleUrl() {
  var newURL = window.location.href;
  var module = jQuery("#module").val();
  var str = newURL.indexOf(module);

  var slicestr = newURL.substring(0, str);
  return slicestr + module;
}

$(document).ready(function () {
  // Hide the modal when close or cancel buttons are clicked
  $(document).on("click", ".btn-close", function () {
    // $(".btn-close, .btn-secondary").click(function () {
    $("#add-lead-modal").modal("hide");
  });

  // // Toggle the "active" class on the toggle switch when clicked
  // $(".toggle-switch").on("click", function () {
  //   alert('sdgsdg');
  //   $(this).toggleClass("active");
  //   toggleRequiredFields();
  // });
  //   // Function to toggle the visibility of required fields
  //   function toggleRequiredFields() {
  //     alert('fgf');
  //     // console.log("toggle");
  //     const isChecked = $('.toggle-switch').hasClass('active');
  //     const requiredFields = $('.not-required-field');

  //     // Show or hide fields based on the toggle state
  //     requiredFields.each(function () {
  //       $(this).css('display', isChecked ? 'none' : 'block');
  //       //alert($(this).isChecked);
  //     });
  //   }

  //modal create

  $("#add-lead-btn").on("click", function () {
    startLoading(); // Show loading overlay
    const urlParams = new URLSearchParams(window.location.search);
    const sourcemodule = urlParams.get("sourcemodule");
    const sourceid = urlParams.get("sourceid");
    var url = "create";
    if (sourcemodule && sourceid) {
      // Check if both sourcemodule and sourceid are not null or undefined
      url += `?sourcemodule=${encodeURIComponent(
        sourcemodule
      )}&sourceid=${encodeURIComponent(sourceid)}`;
    }
    // alert(url);
    $.get(url, function (data) {
      $("#add-lead-modal").modal("show").find(".modal-content").html(data);
      $("#toggle-switch2").removeClass("active");
      //added on 21/12/2024 for back to top
      const modalBody = document.getElementById("modalBody");
      const backToTopButton = document.getElementById("backToTop");
      if (modalBody) {
        modalBody.addEventListener("scroll", function () {
          //alert(modalBody);
          if (modalBody.scrollTop > 200) {
            backToTopButton.style.display = "block";
          } else {
            backToTopButton.style.display = "none";
          }
        });

        // Scroll back to top when the button is clicked
        backToTopButton.addEventListener("click", function () {
          modalBody.scrollTo({
            top: 0,
            behavior: "smooth",
          });
        });
      }
      //end back to top
    }).always(function () {
      stopLoading(); // Hide loading overlay
    });
  });

  //modal for add contact role
  $("#add-contact-role").on("click", function () {
    startLoading(); // Show loading overlay
    const urlParams = new URLSearchParams(window.location.search);
    const sourcemodule = urlParams.get("sourcemodule");
    const sourceid = urlParams.get("sourceid");
    var url = "create";
    if (sourcemodule && sourceid) {
      // Check if both sourcemodule and sourceid are not null or undefined
      url += `?sourcemodule=${encodeURIComponent(
        sourcemodule
      )}&sourceid=${encodeURIComponent(sourceid)}`;
    }
    // alert(url);
    $.get(url, function (data) {
      $("#add-lead-modal").modal("show").find(".modal-content").html(data);
      $("#toggle-switch2").removeClass("active");
      //added on 21/12/2024 for back to top
      const modalBody = document.getElementById("modalBody");
      const backToTopButton = document.getElementById("backToTop");
      if (modalBody) {
        modalBody.addEventListener("scroll", function () {
          //alert(modalBody);
          if (modalBody.scrollTop > 200) {
            backToTopButton.style.display = "block";
          } else {
            backToTopButton.style.display = "none";
          }
        });

        // Scroll back to top when the button is clicked
        backToTopButton.addEventListener("click", function () {
          modalBody.scrollTo({
            top: 0,
            behavior: "smooth",
          });
        });
      }
      //end back to top
    }).always(function () {
      stopLoading(); // Hide loading overlay
    });
  });
  //modal for add contact role
  $("#edit-contact-role").on("click", function () {
    startLoading(); // Show loading overlay
    const urlParams = new URLSearchParams(window.location.search);
    const sourcemodule = urlParams.get("sourcemodule");
    const sourceid = urlParams.get("sourceid");
    var url = "edit";
    if (sourcemodule && sourceid) {
      // Check if both sourcemodule and sourceid are not null or undefined
      url += `?sourcemodule=${encodeURIComponent(
        sourcemodule
      )}&sourceid=${encodeURIComponent(sourceid)}`;
    }
    // alert(url);
    $.get(url, function (data) {
      $("#add-lead-modal").modal("show").find(".modal-content").html(data);
      $("#toggle-switch2").removeClass("active");
      //added on 21/12/2024 for back to top
      const modalBody = document.getElementById("modalBody");
      const backToTopButton = document.getElementById("backToTop");
      if (modalBody) {
        modalBody.addEventListener("scroll", function () {
          //alert(modalBody);
          if (modalBody.scrollTop > 200) {
            backToTopButton.style.display = "block";
          } else {
            backToTopButton.style.display = "none";
          }
        });

        // Scroll back to top when the button is clicked
        backToTopButton.addEventListener("click", function () {
          modalBody.scrollTo({
            top: 0,
            behavior: "smooth",
          });
        });
      }
      //end back to top
    }).always(function () {
      stopLoading(); // Hide loading overlay
    });
  });
  //end contact role

  $(".btn-close, .btn-secondary").click(function () {
    $("#add-lead-modal").modal("hide");
  });

  // Show the column selector modal and populate column options
  $(".col-selector-btn").click(function () {
    $("#columnSelectorModel").modal("show");
  });
//code added by ptpatel on date 03-04-25
  function validateFliterForm() {
    var inputField = document.getElementById("filterValue").value;
    // var regex = /^[a-zA-Z0-9 ]+$/;
    // var regex = /^[a-zA-Z0-9 @.,!]+$/;
    //this allow on date 08-09-2025
    var regex = /^[a-zA-Z0-9 !@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]+$/;
  
    if (inputField.trim() === "") {
        alert("Input should not be blank.");  
        stopLoading();
        return false;
    } else if (!regex.test(inputField)) {
        alert("Input should not contain special characters.");
        stopLoading();
        return false;
    }
    return true;
  }
  $("#apply-filter-by-name").on("click", function() {
    if (validateFliterForm()) {
        applyFilter();
    }
});

$("#filter-save-as").click(function () {
  if(validateFliterForm()){
    $("#saveAsFilterModel").modal("show");
  }
});

$("#filter-save").on("click", function() {
  if (validateFliterForm()) {
    SaveFilter();
  }
});

  //end code here added by ptpatel on date 03-04-25

  $("#saveasbutton").click(function () {
    var filterName = $("#filter_name").val();
    var description = $("#description").val();

    var labelValue = document.getElementById("filterFieldLabel").innerText;
    var inputValue = $("#filterValue").val();
    var filteroperator = document.getElementById("filterOperator").value;
    var fieldId = document
      .getElementById("filterBox")
      .getAttribute("data-field-id");

    //alert(labelValue + inputValue + filteroperator);

    // Validation (Optional)
    if (!filterName.trim()) {
      alert("Please enter a filter name.");
      return;
    }
    if (!description.trim()) {
      alert("Please enter a Description.");
      return;
    }

    // AJAX Request
    $.ajax({
      url: "saveasfilter", // Update with your actual controller action URL
      type: "POST",
      data: {
        _csrf: csrfToken,
        filter_name: filterName,
        description: description,
        labelValue: labelValue,
        inputValue: inputValue,
        fieldId: fieldId,
        filteroperator: filteroperator,
      },
      success: function (response) {
        console.log(response);
        if (response.status == "success") {
          alert("Filter saved successfully!");
          $("#saveAsFilterModel").modal("hide");
          $("#filterByNameModel").modal("hide");
          location.reload();
        } else {
          alert("Error: " + response.message);
        }
      },
      error: function () {
        alert("An error occurred while saving the filter.");
      },
    });
  });

  $("#filterSelectorButton").click(function () {
    // const lead_filterid = document.getElementById("lead_filterid").value;
    // $.ajax({
    //   url: "showsavefilterfeilds", // Update with your actual controller URL
    //   method: "GET", // or "POST" if your server expects it
    //   dataType: "json",
    //   data: {
    //     lead_filterid: lead_filterid,
    //   },

    //   success: function (response) {
    //     if (response.status === "success") {
    //       // Clear previous filter data
    //       $("#filterBox").empty();

    //       // Populate the filterBox with the retrieved data
    //       response.filters.forEach((filter, index) => {
    //         const uniqueId = `filter`;
    //         const operatorId = `filterOperator`;
    //         const inputId = `filterValue`;

    //         const filterHTML = `
    //             <div class="field-label-row">
    //                 <span id="filterFieldLabel" >${filter.fieldlabel}</span>
    //                 <button onclick="removeFilter('${uniqueId}')" class="close-button" style="margin-left: 188px;">
    //                     <i class="fa fa-trash"></i>
    //                 </button>
    //             </div>

    //              <input type="hidden" id="filterFieldName" value="">
    //              <input type="hidden" id="filterFielduitype" value="">
    //              <input type="hidden" id="filterFieldtablename" value="">
    //               <input type="hidden" id="fieldId" value="${filter.fieldid}">

    //              <!-- Dropdown for selecting comparison operators -->
    //             <select id="${operatorId}" class="form-select">
    //                 <option value="Equals" ${
    //                   filter.filteroperator === "Equals" ? "selected" : ""
    //                 }>Equals</option>
    //                 <option value="Not_Equals" ${
    //                   filter.filteroperator === "Not_Equals" ? "selected" : ""
    //                 }>Not Equals</option>
    //                 <option value="Contains" ${
    //                   filter.filteroperator === "Contains" ? "selected" : ""
    //                 }>Contains</option>
    //                 <option value="Not_Contains" ${
    //                   filter.filteroperator === "Not_Contains" ? "selected" : ""
    //                 }>Not Contains</option>
    //                 <option value="In" ${
    //                   filter.filteroperator === "In" ? "selected" : ""
    //                 }>In</option>
    //                 <option value="Not_In" ${
    //                   filter.filteroperator === "Not_In" ? "selected" : ""
    //                 }>Not In</option>
    //                 <option value="is_Empty" ${
    //                   filter.filteroperator === "is_Empty" ? "selected" : ""
    //                 }>is Empty</option>
    //                 <option value="is_Not_Empty" ${
    //                   filter.filteroperator === "is_Not_Empty" ? "selected" : ""
    //                 }>is Not Empty</option>
    //                 <option value="Begins_with" ${
    //                   filter.filteroperator === "Begins_with" ? "selected" : ""
    //                 }>Begins With</option>
    //             </select>
    //             <!-- Input field for the filter value -->
    //             <input type="text" id="${inputId}" class="form-control" value="${
    //           filter.userinput
    //         }" placeholder="Enter value" style="display:block;" />
    //         `;

    //         // Append the generated HTML to the filter box
    //         const filterContainer = `<div id="${uniqueId}" class="filter-item">${filterHTML}</div>`;
    //         $("#filterBox").append(filterContainer);
    //       });

    //       // Show the filter box
    //       $("#filterBox").show();
    //     } else {
    //       alert(response.message);
    //     }
    //   },
    //   error: function (xhr, status, error) {
    //     console.error("An error occurred:", error);
    //     alert("An error occurred while fetching the filter fields.");
    //   },
    // });
     // Get the currently active option
    let activeOption = $("#filterselectbox option.active");
    let activeUserid = activeOption.data("userid");
    
    console.log("Active Option:", activeOption.text());
    console.log("Active UserID:", activeUserid);
    
    if (activeUserid != 1) {
        $("#deleteCustomFilterBtn").show();
        console.log("Showing delete button - active option is custom filter");
    } else {
        $("#deleteCustomFilterBtn").hide();
        console.log("Hiding delete button - active option is system filter (userid=1)");
    }
    $("#filterByNameModel").modal("show");
  });
  $(document).ready(function () {
      let initialSelected = $("#filterselectbox option:selected");
      initialSelected.addClass("active");
      
      let initialUserId = initialSelected.data("userid");
      if (initialUserId != 1) {
          $("#deleteCustomFilterBtn").show();
      } else {
          $("#deleteCustomFilterBtn").hide();
      }
  });
  $(".savasbtn-close-btn").click(function () {
    $("#saveAsFilterModel").modal("hide");
  });
  $(document).on("change", "#filterselectbox", function () {
    startLoading();
    let selectedFilterId = $(this).val(); // Get the selected filter ID
    let selectedOption = $(this).find("option:selected");
    let userIdClass = selectedOption.attr("class"); // Get the class
    let userIdFromData = selectedOption.data("userid");
    //console.log("Selected Filter ID:", selectedFilterId); // Debug: Log the selected filter ID
    // Remove active class from all options
    $("#filterselectbox option").removeClass("active");
    
    // Add active class to selected option
    selectedOption.addClass("active");
    if (userIdFromData != 1) {
        $("#deleteCustomFilterBtn").show();
    } else {
        $("#deleteCustomFilterBtn").hide();
    }
    $.ajax({
      url: "getfilterdetails", // Update to your actual controller action URL
      type: "GET",
      data: { filterId: selectedFilterId },
      success: function (response) {
        console.log("AJAX Response:", response); // Debug: Log the response

        if (response.success) {
          let data = response.data;

          // Populate modal fields with the fetched filter details
          $("#filterFieldLabel").text(data.fieldlabel);
          $("#filterFieldName").val(data.fieldname);
          $("#filterFielduitype").val(data.uitype);
          $("#filterId").val(data.filter_id);
          $("#filterFieldtablename").val(data.tablename);
          $("#filterOperator").val(data.filteroperator);
          $("#filterValue").val(data.userinput);
          $("#filterBox").attr("data-field-id", "");
          document
            .getElementById("filterBox")
            .setAttribute("data-field-id", data.fieldid);
          // Show/hide delete button based on userid from server response
          if (data.userid == 1) {
              $("#deleteCustomFilterBtn").hide();
          } else {
              $("#deleteCustomFilterBtn").show();
          }
          $("#filterBox").show(); // Ensure the filter box is visible

          //if uitype == 8 then get dropdown
          if (data.uitype == 8) {
            $.ajax({
              url: "getfilterdropdown", // Replace with the actual endpoint URL
              type: "POST",
              data: {
                fielduitype: data.uitype,
                fieldname: data.fieldname,
                fieldtablename: data.tablename,
                _csrf: csrfToken,
              },

              success: function (response) {
                if (response) {
                  const inputElement = document.getElementById("filterValue");

                  // inputElement.replaceWith(response);
                  document.getElementById("filterValue").outerHTML = response;
                  $("#filterValue").val(data.userinput);
                } else {
                }
              },
              error: function (error) {
                console.error("Error:", error);
              },
            });
          }
        } else {
          // Show empty modal if no filter details are found
          console.warn(response.message);
          document.getElementById("filterValue").outerHTML = '<input type="text" class="form-control" id="filterValue" placeholder="Enter value" style="display: block;" value="">';

          // Clear modal fields
          $("#filterFieldLabel").text("");
          $("#filterFieldName").val("");
          $("#filterFielduitype").val("");
          $("#filterFieldtablename").val("");
          $("#filterOperator").val("Equals"); // Default operator
          $("#filterValue").val("");
          // added on 14 jan by deepika to empty filter input
          $(".filtercolumnvalues").val("");
          document.getElementById("field_name").style.display = "none";
          // end on 14 jan by deepika to empty filter input
          document
            .getElementById("filterBox")
            .setAttribute("data-field-id", "");
          $("#deleteCustomFilterBtn").hide();
          applyFilter();
          $("#filterBox").hide(); // Hide the filter box
        }
      },
      error: function () {
        alert("Error fetching filter details. Please try again.");
      },
      complete: function () {
        applyFilter();
      },
    });
  });

  $(".fil-btn").click(function () {
    $("#filterByNameModel").modal("hide");
  });

  // Hide the column selector modal
  $(".cs-btn").click(function () {
    $("#columnSelectorModel").modal("hide");
  });

  $("#apply-column-changes").click(function () {
    const selectedColumns = [];
    const deselectedColumns = [];

    // Gather all checked (selected) checkboxes
    $('#columnSelectorModel input[name="column[]"]:checked').each(function () {
      selectedColumns.push({
        columnname: $(this).data("columnname"),
        field_id: $(this).data("field_id"),
      });
    });

    //code added by ptpatel on date 06-11-2025
    if (selectedColumns.length === 0) {
      alert("Please select at least one column before proceeding.");
      return false; // stop further execution
    }
    //end code added by ptpatel on date 06-11-2025
    // Gather all unchecked (deselected) checkboxes
    $('#columnSelectorModel input[name="column[]"]:not(:checked)').each(
      function () {
        deselectedColumns.push($(this).data("field_id"));
      }
    );

    csrfTokenName = $("#csrfTokenName").val();
    csrfToken = $("#csrfToken").val();
    //console.log("csrd" + csrfToken);
    var modulename = jQuery("#module").val();
    // Send selected and deselected columns to the server via AJAX
    $.ajax({
      url: "saveselectedcolumns",
      method: "POST",
      dataType: "json",
      data: {
        _csrf: csrfToken,
        selectedColumns: selectedColumns,
        deselectedColumns: deselectedColumns,
        module: modulename,
      },
      success: function (response) {
        if (response.status === "success") {
          // alert(response.message); // Display the message from the response
          $("#columnSelectorModel").modal("hide");
          // Refresh table to apply the changes
          fetchAndSetColumnDefinitions();
        } else {
          alert(response.message);
        }
      },
      error: function (xhr, status, error) {
        console.error("An error occurred:", error);
        alert("An error occurred while saving columns.");
      },
    });
  });

  $("#exportButton").click(function () {
    exportSelectedRows();
  });

  //code added by ptpatel for exportall inventory
  $("#exportAllButton").click(function () {
    const moduleClass = $(this).attr('class').split(' ').find(function (cls) {
      return cls.startsWith('ModuleName_');
    });

    // Extract module name (e.g., "inventory")
    const moduleName = moduleClass ? moduleClass.replace('ModuleName_', '') : null;

    exportAllRows(moduleName);
  });
  //code added by ptpatel for exportall inventory

  //add code for search tag number of inventory listing page on date 09-12-2025 by ptpatel
  $(document).on("click", "#btnBulkStatusUpload", function () {
      $("#bulkStatusFile").val("");
      $("#bulkStatusMessage").addClass("d-none").html("");
      $("#bulkStatusForm").show();           
      $("#btnSubmitBulkStatus").show();
      $("#bulkStatusModal").modal("show");
  });
  $(document).on("click", "#btnSubmitBulkStatus", function () {
    let $btn = $(this);
      let fileInput = $("#bulkStatusFile")[0];

      if (!fileInput.files.length) {
          alert("Please select a CSV file");
          return;
      }

       $btn.prop("disabled", true);
      startLoading();
      let formData = new FormData();
      formData.append("csvfile", fileInput.files[0]);
      formData.append("_csrf", $("#csrfToken").val());

      $.ajax({
          url: "bulkupdateinventorystatus", 
          type: "POST",
          data: formData,
          contentType: false,
          processData: false,

          success: function (response) {
              console.log(response);

              if (response.success) {
                 let msg='';
                  if(response.updated > 0 )
                 msg = "Bulk update completed successfully!<br>";
                msg += "Updated Records: " + (response.updated || 0) + " of " + (response.totalrows) ;
                if (response.errors && response.errors.length > 0) {
                    msg += "<div class='text-danger'><br>Errors:<br>";
                    response.errors.forEach(err => {
                        msg += "- " + err + "<br>";
                    });
                    msg += "</div>";
                }

                // alert(msg);
                $("#bulkStatusForm").hide();           
                $("#btnSubmitBulkStatus").hide();
                if(response.updated > 0 ){
                    $("#bulkStatusMessage")
                      .removeClass("d-none alert-danger")
                      .addClass("alert alert-success")
                      .html(msg);
                  }
                  else
                  {
                     $("#bulkStatusMessage")
                    .removeClass("d-none alert-success")
                    .addClass("alert alert-danger")
                    .html(msg);
                  }

            } else {

                let msg = "Bulk update failed!<br>";
                if (response.errors && response.errors.length > 0) {
                    msg += "<br>Errors:<br>";
                    response.errors.forEach(err => {
                        msg += "- " + err + "<br>";
                    });
                } else {
                    msg += (response.message || "");
                }

                $("#bulkStatusForm").hide();           
                $("#btnSubmitBulkStatus").hide();
                $("#bulkStatusMessage")
                  .removeClass("d-none alert-success")
                  .addClass("alert alert-danger")
                  .html(msg);
            }

          },

          // error: function (err) {
          //     console.error(err);
          //     alert("Error while uploading CSV.");
          // },
          error: function (xhr) {
              console.error(xhr.responseText);
              alert("Server error. Check console → Network → Response");
          },
          complete: function () {
            // ALWAYS re-enable (success or error)
            
            stopLoading();
            $btn.prop("disabled", false);
        }
      });
  });

 console.log($("#searchTagInput").length);

  $(document).on("focusout", "#searchTagInput", function () {
      let tagNumber = $(this).val().trim();
      filterGridByTagNumber(tagNumber);
  });

// Keep original data
  let __originalRowData = null;

  function filterGridByTagNumber(tagNumber) {
    console.log("from grid by tag number");
      if (!gridApi) {
          console.error("gridApi not initialized");
          return;
      }

      // Save original data once
      if (!__originalRowData) {
          const all = [];
          gridApi.forEachNode(n => all.push(n.data));
          __originalRowData = all.slice();
      }

      // Helper: inject update link inside status column
      function applyStatusUpdateLink(dataArray) {
          return dataArray.map(row => {
              if (!row) return row;

              // create dynamic HTML for status + link
              row.status_html = `
                  <span>${row.status}</span>
                  <a href="javascript:void(0)" 
                    class="status-update-link"
                    data-id="${row.inventory_id}"
                    style="margin-left:8px;color:#007bff;cursor:pointer;">
                    Update
                  </a>
              `;

              return row;
          });
      }

      // --------------------------------------
      // CASE 1: Input empty → restore full grid
      // --------------------------------------
      if (!tagNumber) {

          // restore + add update link
          const restored = applyStatusUpdateLink(__originalRowData);

          gridApi.setGridOption("quickFilterText", "");
          gridApi.setFilterModel(null);
          gridApi.setGridOption("rowData", restored);

          return;
      }

      // --------------------------------------
      // CASE 2: Try quick filter (modern API)
      // --------------------------------------
      gridApi.setGridOption("quickFilterText", tagNumber);

      // detect if any row is visible
      let anyVisible = false;
      gridApi.forEachNodeAfterFilter(n => { anyVisible = true; });

      if (anyVisible) return;

      // --------------------------------------
      // CASE 3: Manual filtering from original data
      // --------------------------------------
      const filtered = __originalRowData.filter(row =>
          row &&
          row.tag_number &&
          row.tag_number.toString().toLowerCase() === tagNumber.toLowerCase()
      );

      if (filtered.length === 0) {
          alert("Tag number not exists");
          $("#searchTagInput").val("");

          const restored = applyStatusUpdateLink(__originalRowData);
          gridApi.setGridOption("rowData", restored);
          return;
      }

      // Inject update link
      const filteredWithLink = applyStatusUpdateLink(filtered);

      // Show filtered rows
      gridApi.setGridOption("rowData", filteredWithLink);
  }

 //add code for search tag number of inventory listing page on date 09-12-2025 by ptpatel
});

class CustomHeader {
  init(params) {
    this.params = params;
    this.eGui = document.createElement("div");
    this.eGui.className = "custom-header";
    this.eGui.innerHTML = `
      <span>${params.displayName}</span>
      <span class="sort-arrow">
        <span class="arrow up dropdown-up"
              onclick="sortbyfunction('asc','${params.column.colDef.field}')"
              title="Sort ASC"></span>
        <span class="arrow down dropdown-down"
              onclick="sortbyfunction('desc','${params.column.colDef.field}')"
              title="Sort DESC"></span>
      </span>
      <img class="dropdown-toggle"
           src="https://c.animaapp.com/4Te5O9cu/img/ic-round-arrow-left-67.svg"
           style="width: 20px; cursor: pointer;">
    `;

    this.dropdownToggle = this.eGui.querySelector(".dropdown-toggle");
    this.portal = document.getElementById("header-dropdown-portal");
    this.createMenu();

    this.handleOutsideClickBound = this.handleOutsideClick.bind(this);
    this.handleScrollBound = () => {this.hideMenu();};

    this.setupEventListeners();
    this.updateSortIcons();
  }

  createMenu() {
    this.menu = document.createElement("div");
    this.menu.className = "header-dropdown-menu";
    this.menu.style.display = "none";
    this.menu.innerHTML = `
      <button class="dropdown-item" data-action="freezeColumn">Freeze Column</button>
      <button class="dropdown-item" data-action="unfreezeColumn">Unfreeze Column</button>
      <button class="dropdown-item" data-action="wrapText">Wrap Text</button>
      <button class="dropdown-item" data-action="clipText">Clip Text</button>
    `;
    this.portal.appendChild(this.menu);

    this.menu.querySelectorAll(".dropdown-item").forEach((item) => {
      item.addEventListener("click", (event) => {
        const action = event.target.getAttribute("data-action");
        this.handleAction(action);
        this.hideMenu();
      });
    });
  }

  setupEventListeners() {
    const sortArrowButton = this.eGui.querySelector(".sort-arrow");

    sortArrowButton.addEventListener("click", () => {
      const currentSort = this.params.column.getSort();
      const nextSort =
        currentSort === "asc" ? "desc" :
        currentSort === "desc" ? null : "asc";
      this.params.setSort(nextSort);
      this.updateSortIcons();
    });

    this.dropdownToggle.addEventListener("click", (e) => {
      e.stopPropagation();
      if (this.menu.style.display === "block") {
        this.hideMenu();
      } else {
        this.showMenu();
      }
    });

    document.addEventListener("click", this.handleOutsideClickBound);
    window.addEventListener("scroll", this.handleScrollBound, true);

  }

  showMenu() {
    const scroller = document.getElementById('tablelist'); 
    let prevTop = 0;

    if (scroller) {
      prevTop = scroller.scrollTop;
      scroller.scrollTop = prevTop + 1;
      scroller.scrollTop = prevTop;
    } else {
      const prevY = window.scrollY || window.pageYOffset;
      window.scrollTo(window.scrollX, prevY + 1);
      window.scrollTo(window.scrollX, prevY);
    }
    this.updateMenuPosition();
    this.menu.style.display = "block";
  }

  hideMenu() {
    this.menu.style.display = "none";
  }

  updateMenuPosition() {
    const rect = this.eGui.getBoundingClientRect();
    this.menu.style.left = rect.left + "px";
    this.menu.style.top  = rect.bottom + "px";
  }


  handleOutsideClick(event) {
    if (!this.eGui.contains(event.target) && !this.menu.contains(event.target)) {
      this.hideMenu();
    }
  }

  updateSortIcons() {
    const currentSort = this.params.column.getSort();
    const upArrow = this.eGui.querySelector(".arrow.up");
    const downArrow = this.eGui.querySelector(".arrow.down");

    if (currentSort === "asc") {
      upArrow.classList.add("active");
      downArrow.classList.remove("active");
    } else if (currentSort === "desc") {
      upArrow.classList.remove("active");
      downArrow.classList.add("active");
    } else {
      upArrow.classList.remove("active");
      downArrow.classList.remove("active");
    }
  }

  handleAction(action) {
    const columnField = this.params.column.getColId();
    const columnApi = this.params.api;
    const allColumnsState = columnApi.getColumnState();
    const clickedColumnIndex = allColumnsState.findIndex(
      (col) => col.colId === columnField
    );

    switch (action) {
      case "freezeColumn":
        const freezeState = allColumnsState
          .slice(0, clickedColumnIndex + 1)
          .map((col) => ({ colId: col.colId, pinned: "left" }));
        columnApi.applyColumnState({ state: freezeState, applyOrder: true });
        break;

      case "unfreezeColumn":
        const unfreezeState = allColumnsState
          .map((col) => ({ colId: col.colId, pinned: null }));
        columnApi.applyColumnState({ state: unfreezeState, applyOrder: true });
        break;

      case "wrapText":
        this.toggleWrapText(true);
        break;

      case "clipText":
        this.toggleWrapText(false);
        break;
    }

    this.params.api.refreshCells({ force: true });
  }

  toggleWrapText(wrap) {
    const columnDef = this.params.column.getColDef();
    columnDef.cellClass = wrap ? "cst-cell-wrap" : "";
  }

  getGui() {
    return this.eGui;
  }

  destroy() {
    document.removeEventListener("click", this.handleOutsideClickBound);
    window.removeEventListener("scroll", this.handleScrollBound, true);
    if (this.menu && this.menu.parentNode) {
      this.menu.parentNode.removeChild(this.menu);
    }
  }

}


let gridApi, gridColumnApi;

// AG Grid options
const gridOptions = {
  columnDefs: [], // Initially empty, to be set dynamically
  rowData: [], // Initially empty, data will be loaded later
  rowSelection: "multiple", // Enables multiple row selection
  suppressRowClickSelection: true, // Prevents row selection when clicking outside the checkbox
  onSelectionChanged: onRowSelectionChanged, // Add event for row selection
  pagination: false,
  // paginationPageSize: 10,
  // as per ERP Point 477 on date 07-10-2025 by ptpatel
  paginationPageSize: 1000,
  paginationPageSizeSelector: [1000,2000,3000,5000,10000], // Include 10 in the dropdown options
  defaultColDef: {
    sortable: true,
    filter: false, //hide filter button
    resizable: true,
    wrapText: true,
    autoHeight: true,
    flex: 1,
    minWidth: 100,
  },
  //added by ptpatel on date 25-07-25 to change rows color alternateivly
  getRowClass: function (params) {
      return params.node.rowIndex % 2 === 0 ? 'ag-row-even' : 'ag-row-odd';
    },
  //end code added by ptpatel on date 25-07-25 to change rows color alternateivly
  onGridReady: (params) => {
    gridApi = params.api; // Set global API reference
    gridColumnApi = params.columnApi; // Set global Column API reference

    fetchAndSetColumnDefinitions(); // Fetch columns on grid ready
    params.api.sizeColumnsToFit();
  },
  processHeaderComponentParams: (params) => {
    return {
      ...params,
      columnApi: gridOptions.columnApi, // Pass `columnApi` to each header component
    };
  },
  //added by deepika on 23/12/24
  onColumnMoved: (event) => {
    // Check if the column move is finalized
    if (event.finished) {
      // Access columnApi from event.api
      const columnApi = event.api; // event.api is the Grid API
      const visibleColumns = columnApi.getAllDisplayedColumns();
      const columnIds = visibleColumns.map((column) => column.getColId());

      // Call setColumnsequence only after the drag is complete
      setColumnsequence(columnIds);
    }
  },
  onGridSizeChanged: (params) => {
    params.api.sizeColumnsToFit();
  }
};

window.addEventListener('resize', function() {
    if (gridOptions.api) {
        gridOptions.api.sizeColumnsToFit();
    }
});

//function to set sequence
function setColumnsequence(columnIds) {
  csrfToken = $("#csrfToken").val();
  data = {
    _csrf: csrfToken,
    columnIds: columnIds,
  };
  $.ajax({
    url: "setcolumnsequence", // Adjust to your endpoint
    type: "POST",
    data: data,
    dataType: "json",
    success: function (data) {
      console.log("this data", data);
      if (!response.success) {
        alert("something went wrong!");
      }
      applyFilter();
    },
    error: function (error) {
      console.error("Error setting sequence:", error);
    },
  });
}
// Function to fetch column definitions and update AG Grid
function fetchAndSetColumnDefinitions() {
  var newURL = window.location.href;
  // var module = jQuery("#module").val();
  var str = newURL.indexOf("/list");

  var geturl = newURL.substring(0, str);
  console.log(geturl);
  // var module = $("#module").val();
  $.ajax({
    url: "getcolumnfields", // Adjust to your endpoint
    type: "GET",
    dataType: "json",
    success: function (columns) {
      // Add the checkbox column manually
      const checkboxColumn = {
        headerName: "",
        getMainMenuItems: () => [], // Removes all menu items for this column
        // field: 'select',
        // headerClass: 'hide-select-header',
        checkboxSelection: true, // Enables checkbox for row selection
        headerCheckboxSelection: true, // Enables "select all" checkbox in the header
        width: 50,
        maxWidth: 50,
        pinned: "left",
        suppressMenu: true,
        suppressRowClickSelection: true,
        flex: 0,
      };
      //detail url
      const urlParams = new URLSearchParams(window.location.search);
      const sourcemodule = urlParams.get("sourcemodule");
      const sourceid = urlParams.get("sourceid");

      // Map and process dynamic columns
      const dynamicColumns = columns
        .filter((col) => col.visible) // Only include visible columns
        .map((col) => ({
          headerName: col.headerName,
          field: col.field,
          headerComponent: CustomHeader,
          resizable: true,
          floatingFilterComponentParams: { suppressFilterButton: true },
          sortable: true, // Enable sorting for each column
          cellRenderer: function (params) {
            if (!params.data) return params.value;

            var url = `/detail?Record=${params.data.RecordId}`;
            if (sourcemodule && sourceid) {
              url += `&sourcemodule=${encodeURIComponent(sourcemodule)}&sourceid=${encodeURIComponent(sourceid)}`;
            }

            // Create a clickable link
            const link = document.createElement("a");
            link.href = geturl + url; //`/controller/action?id=${params.data.id}`; // Replace with your Yii2 route
            link.textContent = params.value;
            link.classList.add("td-cell-link");

            // Make the first column bold like the reference
            const visibleCols = columns.filter(c => c.visible);
            const isFirstColumn = visibleCols.length > 0 && params.column.getColId() === visibleCols[0].field;
            if (isFirstColumn) {
                link.style.fontWeight = "600";
                link.style.color = "#1e293b";
            }

            // Status Badge Logic
            const statusFields = ['status', 'leadstatus', 'lead_status', 'stage'];
            const fieldName = params.column.getColId().toLowerCase();
            
            if (statusFields.some(f => fieldName.includes(f)) && params.value) {
                const badge = document.createElement("span");
                badge.className = "status-badge";
                
                let iconSvg = '';
                const val = params.value.toLowerCase();
                
                if (val.includes('success') || val.includes('active') || val.includes('won') || val.includes('completed') || val.includes('confirmed') || val.includes('healthy') || val.includes('converted')) {
                    badge.classList.add('success');
                    iconSvg = `<svg class="badge-icon" xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>`;
                } else if (val.includes('pending') || val.includes('processing') || val.includes('hold') || val.includes('waiting') || val.includes('approval')) {
                    badge.classList.add('warning');
                    iconSvg = `<svg class="badge-icon" xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>`;
                } else if (val.includes('fail') || val.includes('lost') || val.includes('reject') || val.includes('disqualified') || val.includes('error') || val.includes('cancel')) {
                    badge.classList.add('danger');
                    iconSvg = `<svg class="badge-icon" xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>`;
                } else if (val.includes('info') || val.includes('new') || val.includes('created') || val.includes('lead')) {
                    badge.classList.add('info');
                    iconSvg = `<svg class="badge-icon" xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12" y2="8"></line></svg>`;
                } else {
                    badge.classList.add('secondary');
                }
                
                badge.innerHTML = `${iconSvg}<span style="margin-top: 1px;">${params.value}</span>`;
                return badge;
            }

            // Create a container div to hold both elements
            const container = document.createElement("div");
            container.style.display = "flex";
            container.style.alignItems = "center";
            container.style.gap = "8px";
            container.style.width = "100%";

            // add icon in listview before filename added by ptpatel on date 25-02-2026
            if (col.uitype == 5) {
                const listfileName = params.data[col.field]; // Make sure this exists in your data
                container.innerHTML = fileCellRenderer(listfileName);
                return container;
            }
            //end add icon in listview before filename added by ptpatel on date 25-02-2026
            container.appendChild(link);
            //below uitypes are not allowed to edit
            let uitypeArray = ["2", "3", "11", "25", "26", "27", "28", "29", "53", "70"];
            const editButton = createSingleEditButton(col.uitype, col.tabid, params.data.RecordId, col.field, col.fieldid, col.headerName);
              // || col.tabid == 33 added by ptpatel on date 09-12-2025
            // console.log(col);
            const inv_status = ["Inventory","Tagging Pending","IQC Require"];
            const disallowed = inv_status.includes(params.data.status);
          if(col.tabid == 33 && !disallowed && col.editpermission){
              if (col.visible_permission === 0 && col.readonly_permission === 0 && col.single_edit === 0 &&
                col.fieldid !== 193 && !uitypeArray.includes(col.uitype)) { //is_edit == "1" remove because only check edit permisssion
                container.insertBefore(editButton,container.firstChild);
              }
          } 
          else if(col.editpermission1 == 1){
              if (col.visible_permission === 0 && col.readonly_permission === 0 && col.single_edit === 0 &&
                col.fieldid !== 193 && !uitypeArray.includes(col.uitype) && is_edit == "1") { //if owner than isedit =1
                container.insertBefore(editButton,container.firstChild);
              }
          }
            return container;
            // return link; //commented by ptpatel because it is added in div container
            // code added by ptpatel on date 21-03-25 end  here
          },
        }));

      // Actions column — three dot "..." button at end of each row
      const actionsColumn = {
        headerName: "",
        field: "_actions",
        width: 52,
        minWidth: 52,
        maxWidth: 52,
        pinned: "right",
        suppressMenu: true,
        sortable: false,
        resizable: false,
        suppressMovable: true,
        flex: 0,
        cellRenderer: function(params) {
          const wrapper = document.createElement("div");
          wrapper.style.cssText = "display:flex;align-items:center;justify-content:center;width:100%;height:100%;";

          const btn = document.createElement("button");
          btn.className = "row-action-btn";
          btn.innerHTML = "&#8942;"; // vertical ellipsis
          btn.title = "Actions";

          btn.addEventListener("click", function(e) {
            e.stopPropagation();

            // Remove any existing dropdown
            const existing = document.getElementById("row-action-dropdown");
            if (existing) existing.remove();

            const recordId = params.data ? params.data.RecordId : null;
            const dropdown = document.createElement("div");
            dropdown.id = "row-action-dropdown";
            dropdown.className = "row-action-dropdown";

            const rect = btn.getBoundingClientRect();
            dropdown.style.cssText = `position:fixed;top:${rect.bottom + 4}px;right:${window.innerWidth - rect.right}px;z-index:99999;`;

            const items = [
              { label: "View", icon: `<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path><circle cx="12" cy="12" r="3"></circle></svg>`, action: () => { if (recordId) window.location.href = "view?id=" + recordId; } },
              { label: "Edit", icon: `<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg>`, action: () => { if (recordId) window.location.href = "update?id=" + recordId; } },
            ];

            items.forEach(function(item) {
              const div = document.createElement("div");
              div.className = "row-action-item";
              div.innerHTML = `<span class="row-action-icon">${item.icon}</span>${item.label}`;
              div.addEventListener("click", function(ev) {
                ev.stopPropagation();
                dropdown.remove();
                item.action();
              });
              dropdown.appendChild(div);
            });

            document.body.appendChild(dropdown);

            // Close on outside click
            setTimeout(() => {
              document.addEventListener("click", function handler() {
                dropdown.remove();
                document.removeEventListener("click", handler);
              });
            }, 0);
          });

          wrapper.appendChild(btn);
          return wrapper;
        }
      };

      // Combine: checkbox | dynamic columns | actions
      const columnDefs = [checkboxColumn, ...dynamicColumns, actionsColumn];

      // Set column definitions in the grid
      //gridOptions.api.setColumnDefs(columnDefs);
      gridOptions.api.setGridOption("columnDefs", columnDefs);
      setTimeout(() => {
          gridOptions.api.sizeColumnsToFit();
      }, 0);
      // Fetch row data after setting column definitions
      //  fetchRowData();
      applyFilter();
    },
    error: function (error) {
      console.error("Error fetching column definitions:", error);
    },
  });
}

// Function to fetch row data and update AG Grid
function fetchRowData() {
  const urlParams = new URLSearchParams(window.location.search);
  console.log(urlParams);
  // Get a specific parameter by name
  const sourcemodule = urlParams.get("sourcemodule");
  const sourceid = urlParams.get("sourceid");
  var url = "gettabledata";
  if (sourcemodule && sourceid) {
    // Check if both sourcemodule and sourceid are not null or undefined
    url += `?sourcemodule=${encodeURIComponent(
      sourcemodule
    )}&sourceid=${encodeURIComponent(sourceid)}`;
  }
  // console.log(url);

  $.ajax({
    url: url, // Adjust to your endpoint
    type: "GET",
    dataType: "json",
    success: function (data) {
      console.log("this data", data);
      gridOptions.api.setGridOption("rowData", data);
    },
    error: function (error) {
      console.error("Error fetching row data:", error);
    },
  });
}

// Initialize AG Grid after DOM is loaded
document.addEventListener("DOMContentLoaded", function () {
  const gridDiv = document.querySelector("#myGrid");
  new agGrid.Grid(gridDiv, gridOptions);
});

document.addEventListener("DOMContentLoaded", function () {
  function openfieldName() {
    document.getElementById("field_name").style.display = "block";
  }

  //code added by ptpatel on date 02-04-25
  // Function to remove Select2 properly
function removeSelect2() {
  console.log("removeSelect2 call");
  // if ($("span").hasClass("select2 select2-container")) {
    //  $(".select2").select2("destroy"); // Properly destroy Select2 instance
    // $(".select2-container").remove(); // Destroy Select2 and remove the element
    // $("#multipledddata").remove();
    if ($('#filterValue').data('select2')) { 
    // if ($(".select2").length > 0) {
      // $(".select2").select2("destroy");  // Destroy Select2 instance
      $(".select2").remove();  // Remove select2 elements
      $(".select2-container").remove(); // Remove leftover UI
  }
  // } 
}
//code ended here added by ptpatel on date 02-04-25

  function openFilterBox(
    fieldId,
    fieldname,
    fieldLabel,
    fielduitype,
    fieldtablename
  ) {
    document.getElementById("field_name").style.display = "none";
    // Display the filter box
    document.getElementById("filterBox").style.display = "block";

    // Set the field label in the filter box
    document.getElementById("filterFieldLabel").innerText = fieldLabel;
    document.getElementById("filterFieldName").value = fieldname;
    document.getElementById("filterFielduitype").value = fielduitype;
    document.getElementById("filterFieldtablename").value = fieldtablename;

    // Optionally, you can store the fieldId if you need to process it later
    document.getElementById("filterBox").setAttribute("data-field-id", fieldId);

    //if uitype == 8 then get dropdown
    if (fielduitype == 8) {
      $.ajax({
        url: "getfilterdropdown", // Replace with the actual endpoint URL
        type: "POST",
        data: {
          fielduitype: fielduitype,
          fieldname: fieldname,
          fieldtablename: fieldtablename,
          _csrf: csrfToken,
        },

        success: function (response) {
          if (response) {
            removeSelect2();
            const inputElement = document.getElementById("filterValue");
            // inputElement.replaceWith(response);
            document.getElementById("filterValue").outerHTML = response;
          } else {
          }
        },
        error: function (error) {
          console.error("Error:", error);
        },
      });
    }
    //code added by ptpatel on date 01-04-25
    else if (fielduitype == 22) {
      console.log("fielduitype code executed");
      $.ajax({
        url: "getmultiplefilterdropdown", // Replace with the actual endpoint URL
        type: "POST",
        data: {
          fielduitype: fielduitype,
          fieldname: fieldname,
          fieldtablename: fieldtablename,
          _csrf: csrfToken,
        },
        success: function (response) {
          if (response) {
            const inputElement = document.getElementById("filterValue");
            if ($("#filterValue").length > 0) {
            $("#filterValue").replaceWith(response);
            }
          } else {
          }
        },
        error: function (error) {
          console.error("Error:", error);
        },
      });
    }
    //code ended here added by ptpatel on date 01-04-25
    else {
      removeSelect2();
      document.getElementById("filterValue").outerHTML = '<input type="text" class="form-control" id="filterValue" placeholder="Enter value" style="display: block;" value="">';
    }
    console.log("outside if");
  }

  // Get the filter operator dropdown and input field
  const filterOperator = document.getElementById("filterOperator");
  const filterValue = document.getElementById("filterValue");

  // const multiplefilterValue = document.getElementById("multiplefilterValue");
// console.log("multiplefilterValue"+multiplefilterValue);
  // Show or hide the input field based on the selected operator
  filterOperator.addEventListener("change", function () {
    const selectedOperator = filterOperator.value;
    if (
      selectedOperator === "is_Empty" ||
      selectedOperator === "is_Not_Empty"
    ) {
      filterValue.style.display = "none"; // Hide input field
    } else {
      filterValue.style.display = "block"; // Show input field
    }
  });

  // Trigger the change event to set initial visibility
  filterOperator.dispatchEvent(new Event("change"));

  function closeFilterBox() {
    // Get the filter_id from the hidden input
    const filterId = document.getElementById("filterId").value;
    const deleteCheckbox = document.getElementById("deleteDefaultFilter");
    const isChecked = deleteCheckbox && deleteCheckbox.checked ? 1 : 0;
    if (filterId) {
      $.ajax({
        url: "deletefilter", // Replace with the actual endpoint URL
        type: "POST",
        data: { filter_id: filterId,delete_from_default: isChecked, _csrf: csrfToken },

        success: function (response) {
          if (response.success) {
            // Successfully deleted the row
            console.log("Filter deleted successfully");
            // Hide the filter box
            document.getElementById("filterBox").style.display = "none";
            // added on 13 jan 2025 by deepika
            $("#filterOperator").val("");
            $("#filterValue").val("");
            $(".filtercolumnvalues").val("");
            
            // Uncheck and disable checkbox if it exists
            if (deleteCheckbox) {
              if(isChecked && isChecked ==1){
                $(`#filterselectbox option[value='${filterId}']`).remove();
              }
              deleteCheckbox.checked = false;
            }
            applyFilter();
          } else {
            console.error("Error deleting filter:", response.message);
            // added on 13 jan 2025 by deepika
            // Hide the filter box
            document.getElementById("filterBox").style.display = "none";
            $("#filterOperator").val("");
            $("#filterValue").val("");
            $(".filtercolumnvalues").val("");
          }
        },
        error: function (error) {
          console.error("Error:", error);
        },
      });
    } else {
      console.error("No filter ID provided");
      console.log("Filter deleted successfully");
      // Hide the filter box
      // added on 13 jan 2025 by deepika
      $("#filterOperator").val("");
      $("#filterValue").val("");
      $(".filtercolumnvalues").val("");
      document.getElementById("filterBox").style.display = "none";
      applyFilter();
    }

    // // Hide the filter box
    // document.getElementById("filterBox").style.display = "none";

    // // Clear the input value if needed
    // document.getElementById("filterValue").value = "";
  }

  $(document).on('click', '#deleteCustomFilterBtn', function (e) {
    e.preventDefault();

    const filterId = $('#filterId').val();
    if (!filterId) return;


    $.ajax({
      url: 'deletefilter',
      type: 'POST',
      data: {
        filter_id: filterId,
        delete_from_default: 1,
        _csrf: csrfToken
      },
      success: function (response) {
        if (response.success) {
          console.log('Custom filter deleted from both tables');
          document.getElementById('filterBox').style.display = 'none';
          $('#filterOperator').val('');
          $('#filterValue').val('');
          $('.filtercolumnvalues').val('');
          $(`#filterselectbox option[value='${filterId}']`).remove();
          applyFilter();
        } else {
          console.error('Error deleting custom filter:', response.message);
          alert('Error: ' + response.message);
        }
      },
      error: function (error) {
        console.error('Error:', error);
        alert('An error occurred while deleting the filter');
      }
    });
  });
  function applyFilter() {
    startLoading(); // Show loading overlay
   if(!currentPage)
      currentPage = 1;
    const startRow = (currentPage - 1) * pageSize;
    const urlParams = new URLSearchParams(window.location.search);
    // console.log(urlParams);
    //for widget
    const widgetid = urlParams.get("widgetid");
    // Get a specific parameter by name
    const sourcemodule = urlParams.get("sourcemodule");
    const sourceid = urlParams.get("sourceid");
    var url = "gettabledata?start=" + startRow + "&limit=" + pageSize;
    if (sortorder != "" && sortby != "") {
      url += "&OrderBy=" + sortby + "&SortOrder=" + sortorder;
    }
    if (sourcemodule && sourceid) {
      // Check if both sourcemodule and sourceid are not null or undefined
      url += `&sourcemodule=${encodeURIComponent(
        sourcemodule
      )}&sourceid=${encodeURIComponent(sourceid)}`;
    }
    //for widget filter
      if (widgetid) {
        url += `&widgetid=${encodeURIComponent(
          widgetid
        )}`
    }
    // console.log("apply filter");
    const labelValue = document.getElementById("filterFieldLabel").innerText;
    const filterFieldName = document.getElementById("filterFieldName").value;
    const filterFielduitype =
      document.getElementById("filterFielduitype").value;
    const filterFieldtablename = document.getElementById(
      "filterFieldtablename"
    ).value;
    const inputValue = $("#filterValue").val();
    const filteroperator = document.getElementById("filterOperator").value;
    const fieldId = document
      .getElementById("filterBox")
      .getAttribute("data-field-id");
    const filterselectbox = document.getElementById("filterselectbox").value;

    csrfTokenName = $("#csrfTokenName").val();
    csrfToken = $("#csrfToken").val();
    // console.log("csrd" + csrfToken);
    if (filterFielduitype) {
      data = {
        filterFielduitype: filterFielduitype,
        filterFieldtablename: filterFieldtablename,
        filterFieldName: filterFieldName,
        labelValue: labelValue,
        inputValue: inputValue,
        fieldId: fieldId,
        filteroperator: filteroperator,
        filterselectbox: filterselectbox,
        _csrf: csrfToken,
      };
    } else
      data = {
        filterselectbox: filterselectbox,
        _csrf: csrfToken,
      };
    // Send selected and deselected columns to the server via AJAX
   
  //  if(validateFliterForm()) {
      $.ajax({
        url: url, //"filterbylead",
        method: "POST",
        dataType: "json",
        data: data,
        success: function (data) {
          console.log("this data", data);
          gridOptions.api.setGridOption("rowData", data.RecordList);
          $("#filterByNameModel").modal("hide");
          if (data && data.totalitemcount && data.totalitemcount.noofpages) {
            // totalPages = data.totalitemcount.noofpages;
            // currentPage = data.totalitemcount.pagejumps;
            // console.log("Total pages:", totalPages);  // Log total pages
            // console.log("currentPage pages:", currentPage);  // Log total pages
          }
          totalPages = Math.ceil(data.totalitemcount.totrecords / pageSize);
          renderPaginationButtons(); // Render pagination after data is fetched

          // Update pagination info
          updatePaginationInfo(data.totalitemcount.totrecords);
        },
        error: function (error) {
          console.error("Error fetching row data:", error);
        },
        complete: function () {
          stopLoading();
        },
      });
    // }
  }

  function SaveFilter() {
    const labelValue = document.getElementById("filterFieldLabel").innerText;
    // const inputValue = document.getElementById("filterValue").value;
    const inputValue = $("#filterValue").val();
    const filteroperator = document.getElementById("filterOperator").value;
    const fieldId = document
      .getElementById("filterBox")
      .getAttribute("data-field-id");

    const lead_filterid = document.getElementById("filterselectbox").value;

    console.log("hello");
    console.log(labelValue);
    console.log(inputValue);
    console.log(fieldId);
    console.log(filteroperator);
    console.log(lead_filterid);
    // Send selected and deselected columns to the server via AJAX
      $.ajax({
        url: "savefilterbylead",
        method: "POST",
        dataType: "json",
        data: {
          labelValue: labelValue,
          inputValue: inputValue,
          fieldId: fieldId,
          filteroperator: filteroperator,
          filterid: lead_filterid,

          _csrf: yii.getCsrfToken(),
        },
        success: function (response) {
          console.log(response);
          $("#filterByNameModel").modal("hide");
        },
        error: function (xhr, status, error) {
          console.error("An error occurred:", error);
          alert("An error occurred while applying the filter.");
        },
        complete: function () {
          applyFilter();
        },
      });
  }

  function updateAgGridData(filteredRows) {
    if (gridOptions && gridOptions.api) {
      // Clear existing rows by removing all current rows
      gridOptions.api.forEachNode((node) =>
        gridOptions.api.applyTransaction({ remove: [node.data] })
      );

      // Manually add new filtered rows
      gridOptions.api.applyTransaction({ add: filteredRows });
    } else {
      console.error("Grid API not available.");
    }
  }

  // Expose functions to global scope if necessary
  window.openFilterBox = openFilterBox;
  window.closeFilterBox = closeFilterBox;
  window.openfieldName = openfieldName;
  window.applyFilter = applyFilter;
  window.SaveFilter = SaveFilter;
});

$(".filtercolumnvalues").keyup(function () {
  // alert('jv');
  filterFields($(this).val());
});
// filter field name in list filter
function filterFields(searchTerm) {
  const lowerCaseTerm = searchTerm.toLowerCase();
  const fields = document.querySelectorAll("#field_name .filed-div");
  fields.forEach((field) => {
    const label = field.getAttribute("data-label").toLowerCase() || ""; // Default to an empty string if null

    //console.log(label);

    if (label.includes(lowerCaseTerm)) {
      $("#field_name").css("display", "block"); // Show the field
      field.style.display = "block"; // Show the field
    } else {
      field.style.display = "none"; // Hide the field
    }

    if (searchTerm == "") {
      field.style.display = "none";
      console.log("searchTerm =" + searchTerm);
    }
  });
}

// selection functions
function onRowSelectionChanged() {
  const selectedRows = gridOptions.api.getSelectedRows();
  const selectedCount = selectedRows.length;
  const totalCount = gridOptions.api.getDisplayedRowCount();
  
  const selectionInfo = document.getElementById("selection-info");
  if (selectionInfo) {
    selectionInfo.textContent = `${selectedCount} of ${totalCount} row(s) selected.`;
  }

  const exportButton = document.getElementById("exportButton");
  const deleteButton = document.getElementById("deleteButton");
  const updateButton = document.getElementById("updateButton");
  const exportAllButton = document.getElementById("exportAllButton");
 
  if (selectedRows.length > 0) {
    $(".bulkactions").removeClass("tr-hidden");
    $(".leads-selected").html(selectedCount);
    const selectedLeadIds = selectedRows.map((row) => row.RecordId);
    document.getElementById("hiddenLeadIds").value = selectedLeadIds.join(",");
  } else {
    $(".bulkactions").addClass("tr-hidden");
    $(".leads-selected").html("");
  }
}

function deleteSelectedRows() {
  const selectedRows = gridOptions.api.getSelectedRows(); // Get selected rows
  const leadIds = selectedRows.map((row) => row.RecordId); // Extract lead IDs from selected rows

  if (leadIds.length === 0) {
    alert("No rows selected for Archieve.");
    return;
  }
  csrfTokenName = $("#csrfTokenName").val();
  csrfToken = $("#csrfToken").val();

  if (confirm("Are you sure you want to Archieve the selected rows?")) {
    $.ajax({
      url: "deleteselectedrow", // Replace with your delete endpoint
      type: "POST",
      data: {
        leadIds: leadIds, // Pass lead IDs to the server
        _csrf: csrfToken,
      },
      success: function (response) {
        if (response.status === "success") {
          alert("Selected rows Archieved successfully.");
          // fetchRowData();
          applyFilter();
        } else {
          alert("Error deleting rows: " + response.message);
        }
      },
      error: function (error) {
        console.error("Error during deletion:", error);
        alert("An error occurred while deleting rows.");
      },
    });
  }
}

// Function to export selected rows to Excel

function exportSelectedRows() {
  const selectedRows = gridApi.getSelectedRows(); // Get selected rows
  if (selectedRows.length === 0) {
    alert("Please select rows to export.");
    return;
  }

  const selectedRowIds = selectedRows.map((row) => row.RecordId);
  console.log("Selected Row IDs:", selectedRowIds);

  csrfTokenName = $("#csrfTokenName").val();
  csrfToken = $("#csrfToken").val();

  // Send an AJAX request to fetch data for the selected rows
  $.ajax({
    url: "exportdata", // Update with your server endpoint
    type: "POST",
    data: {
      selectedRowIds: selectedRowIds, // Pass lead IDs to the server
      _csrf: csrfToken,
    },
    success: function (response) {
      console.log("Response from server:", response);

      if (!response.success) {
        alert("Failed to export data. Server responded with an error.");
        return;
      }

      const { headers, rows } = response;

      if (!headers || !rows) {
        alert("Invalid data format received from the server.");
        console.error("Response:", response);
        return;
      }

      // Start creating Excel content as an HTML table
      let excelData = `
          <html xmlns:o="urn:schemas-microsoft-com:office:office"
                xmlns:x="urn:schemas-microsoft-com:office:excel"
                xmlns="http://www.w3.org/TR/REC-html40">
          <head>
            <meta charset="UTF-8">
            <style>
              table { border-collapse: collapse; }
              td, th { border: 1px solid black; padding: 5px; }
              th { background-color: #f2f2f2; font-weight: bold; }
            </style>
          </head>
          <body>
            <table>
              <thead>
                <tr>
                  ${headers.map((header) => `<th>${header}</th>`).join("")}
                </tr>
              </thead>
              <tbody>
        `;

      // Iterate over the rows object
      Object.values(rows).forEach((row) => {
        excelData += `
      <tr>
          ${row.map((cell) => `<td>${cell || ""}</td>`).join("")}
      </tr>
  `;
      });

      excelData += `
              </tbody>
            </table>
          </body>
          </html>
        `;

      console.log("Generated Excel Data:", excelData);

      // Convert the HTML table to a Blob
      const blob = new Blob([excelData], { type: "application/vnd.ms-excel" });

      // Create a download link
      const link = document.createElement("a");
      link.href = URL.createObjectURL(blob);
      link.download = "Selected_Leads.xls"; // Set the file name
      link.click();

      // Clean up
      URL.revokeObjectURL(link.href);
    },
    error: function (error) {
      alert("Failed to export data. Please try again.");
      console.error("AJAX Error:", error);
    },
  });
}

$("#deleteButton").click(function () {
  deleteSelectedRows();
});
$("#updateButton").click(function () {
  startLoading();
  // $("#updateModel").modal("show");
  //show update fields
  var geturl = getModuleUrl();
  //alert(geturl);
  var url = geturl + "/bulkupdateview";
  csrfTokenName = $("#csrfTokenName").val();
  csrfToken = $("#csrfToken").val();
  //[csrfParam]: csrfToken,

  data = {
    _csrf: csrfToken,
  };

  $.ajax({
    type: "POST",
    url: url,
    // async:false,
    data: data,
    success: function (data) {
      $("#updateModel").modal("show").find(".modal-content").html(data);
      stopLoading();
    },
    error: function (data) {
      // if error occured

      alert("Error occured.please try again");
    },
    complete: function () {
      stopLoading();
    },
    dataType: "html",
  });
});

$(".update-close-btn").click(function () {
  $("#updateModel").modal("hide");
});
$(document).on("change", "#updatefiled_names", function () {
  //$("#updatefiled_names").on("change", function () {
  const selectedFieldLabel = $(this).find("option:selected").text(); // Get the selected field label
  const selectedValue = $(this).val(); // Get the selected field ID

  if (selectedValue) {
    $("#field-label").text(`Enter value for ${selectedFieldLabel}`); // Set dynamic label
    $(".field-continer").addClass("tr-hidden"); // Clear the input field

    $(".fieldid_" + selectedValue).removeClass("tr-hidden"); // Show the input container
  } else {
    //$("#field-input-container").hide(); // Hide the input container if no field is selected
    $(".field-continer").addClass("tr-hidden"); // Clear the input field
  }
});

// Handle bulk update submission
$(document).on("click", "#confirmUpdateButton", function () {
  //$("#").on("click", function () {
  const hiddenLeadIds = $("#hiddenLeadIds").val(); // Fetch selected lead IDs
  const selectedValue = $("#updatefiled_names").val(); // Fetch selected field ID
  var userInput = $("#" + selectedValue).val(); // Fetch user input
  if ($("#" + selectedValue + "1").val()) {
    userInput = $("#" + selectedValue + "1").val();
  }

  // Validate inputs
  if (!hiddenLeadIds || !selectedValue || userInput.trim() === "") {
    alert("Please fill in all fields before updating.");
    return;
  }
  csrfTokenName = $("#csrfTokenName").val();
  csrfToken = $("#csrfToken").val();
  // AJAX request
  $.ajax({
    url: "bulkupdate", // Adjust route as needed
    type: "POST",
    data: {
      hiddenLeadIds: hiddenLeadIds,
      selectedValue: selectedValue,
      userInput: userInput,
      _csrf: csrfToken, // Include the CSRF token
    },
    success: function (response) {
      if (response.success) {
        alert(response.message);
        $("#updateModel").modal("hide"); // Close modal on success
        location.reload();
      } else {
        alert("Error: " + response.message);
      }
    },
    error: function (xhr, status, error) {
      console.error("AJAX Error:", error);
      alert("An error occurred while updating.");
    },
  });
});

// kanbar funtion
function toggleDropdown(button) {
  const dropdown = button.nextElementSibling;
  dropdown.classList.toggle("show");
}

// Close dropdown if clicked outside
window.onclick = function (event) {
  if (!event.target.matches(".dropdown-btn")) {
    const dropdowns = document.getElementsByClassName("card-options");
    for (let i = 0; i < dropdowns.length; i++) {
      const openDropdown = dropdowns[i];
      if (openDropdown.classList.contains("show")) {
        openDropdown.classList.remove("show");
      }
    }
  }
};

function allowDrop(event) {
  event.preventDefault(); // Allow dropping
}

function drag(event) {
  // Store the dragged card ID
  event.dataTransfer.setData("text", event.target.id);
}

function drop(event) {
  event.preventDefault();

  const draggedCardId = event.dataTransfer.getData("text"); // ID of the dragged card
  const draggedCard = document.getElementById(draggedCardId); // Get the card element
  const newColumn = event.currentTarget.querySelector(".board-column-1"); // Drop zone
  const newStatusId = event.currentTarget.dataset.statusId; // New status ID
  const tableid = event.currentTarget.dataset.tblId; // New status ID
  const fieldid = event.currentTarget.dataset.fieldId; // New status ID

  // Move the card visually to the new column
  newColumn.appendChild(draggedCard);

  // Extract the lead ID from the card ID
  const leadId = draggedCardId.split("-")[1];
  var geturl = getModuleUrl();

  // AJAX call to update the status on the server
  csrfTokenName = $("#csrfTokenName").val();
  csrfToken = $("#csrfToken").val();
  //[csrfParam]: csrfToken,
  data = {};
  // alert(leadid);
  $.ajax({
    url: "updatestage", // Server endpoint
    method: "POST",
    data: {
      [tableid]: {
        [fieldid]: newStatusId,
      },
      RecordId: leadId,
      mode: "edit",
      module: $("#module").val(),
      _csrf: csrfToken, // Include CSRF token if required
    },
    success: function (response) {
      if (response.success) {
        console.log("Lead status updated successfully.");
        location.reload();
      } else {
        console.error(
          "Error updating lead status:",
          response.errors || "Unknown error."
        );
      }
    },
    error: function (xhr, status, error) {
      console.error("AJAX error:", error);
    },
  });
}
// Save the selected view in localStorage
// document.querySelector(".listview").addEventListener("click", function () { //zitendra changes
//   document.querySelector(".kanbanoption").style.display = "none";
//   document.querySelector(".listoption").style.display = "block";
//   opentoggleDropdown();

//   document.querySelector(".kanban-list").style.display = "none"; // Hide Kanban
//   document.querySelector(".table-list").style.display = "block"; // Show Table

//   localStorage.setItem("selectedView", "table-list"); // Save the selected view
// });
document.addEventListener("click", function (event) {
  if (event.target.matches(".listview")) {
    const kanbanOption = document.querySelector(".kanbanoption");
    const listOption = document.querySelector(".listoption");
    const kanbanList = document.querySelector(".kanban-list");
    const tableList = document.querySelector(".table-list");

    if (kanbanOption) kanbanOption.style.display = "none";
    if (listOption) listOption.style.display = "block";

    opentoggleDropdown(); // Ensure this function exists in your script

    if (kanbanList) kanbanList.style.display = "none"; // Hide Kanban
    if (tableList) tableList.style.display = "block"; // Show Table

    localStorage.setItem("selectedView", "table-list"); // Save the selected view
  }
});
document.querySelector(".kabanview").addEventListener("click", function () {
  document.querySelector(".kanbanoption").style.display = "block";
  document.querySelector(".listoption").style.display = "none";
  opentoggleDropdown();

  document.querySelector(".table-list").style.display = "none"; // Hide Table
  document.querySelector(".kanban-list").style.display = "flex"; // Show Kanban

  localStorage.setItem("selectedView", "kanban-list"); // Save the selected view
});

// Check the stored preference on page load
window.addEventListener("DOMContentLoaded", function () {
  const selectedView = localStorage.getItem("selectedView"); // Get the selected view from localStorage

  if (selectedView === "kanban-list") {
    // Show Kanban view
    document.querySelector(".kanbanoption").style.display = "block";
    document.querySelector(".listoption").style.display = "none";

    document.querySelector(".table-list").style.display = "none"; // Hide Table
    document.querySelector(".kanban-list").style.display = "flex"; // Show Kanban
  } else {
    // Default to Table view
    document.querySelector(".kanbanoption").style.display = "none";
    document.querySelector(".listoption").style.display = "block";

    document.querySelector(".kanban-list").style.display = "none"; // Hide Kanban
    document.querySelector(".table-list").style.display = "block"; // Show Table
  }
});

function opentoggleDropdown() {
  const dropdownContent = document.querySelector(".dropdown-content");
  if (dropdownContent.style.display === "block") {
    dropdownContent.style.display = "none";
  } else {
    dropdownContent.style.display = "block";
  }
}

// for pagination
// Function to render pagination buttons dynamically
function renderPaginationButtons() {
  const paginationContainer = document.getElementById("pagination-buttons");
  paginationContainer.innerHTML = ""; // Clear existing buttons

  const maxVisibleButtons = 5; // Maximum number of visible page buttons
  //alert(currentPage);
  let startPage = Math.max(currentPage - Math.floor(maxVisibleButtons / 2), 1);
  let endPage = Math.min(startPage + maxVisibleButtons - 1, totalPages);

  // Ensure the pagination is within bounds
  if (endPage - startPage + 1 < maxVisibleButtons) {
    startPage = Math.max(endPage - maxVisibleButtons + 1, 1);
  }

  // Generate buttons for visible pages
  for (let i = startPage; i <= endPage; i++) {
    const button = document.createElement("button");
    button.textContent = i;
    if (i === currentPage) {
      button.classList.add("active");
    }
    button.onclick = () => goToPage(i);
    paginationContainer.appendChild(button);
  }
}
// Function to handle page changes
function goToPage(page) {
  if (page >= 1 && page <= totalPages) {
    currentPage = page; // Update current page
    applyFilter(); // Fetch new data for the page
  }
}

// Function to handle page size changes
function changePageSize() {
  pageSize = parseInt(document.getElementById("page-size").value, 10); // Update page size
  //alert(pageSize);
  currentPage = 1; // Reset to the first page
  applyFilter(); // Fetch new data with updated page size
}
function sortbyfunction(sortorderid, sortbyid) {
  sortorder = sortorderid;
  sortby = sortbyid;
  applyFilter();
}

function updatePaginationInfo(totalItems) {
  console.log("updatePaginationInfo called"); // Debugging line

  const paginationInfo = document.getElementById("pagination-info");

  if (!paginationInfo) {
    console.error("Pagination info element NOT found!");
    return;
  }

  const startItem = totalItems === 0 ? 0 : (currentPage - 1) * pageSize + 1;
  const endItem = Math.min(currentPage * pageSize, totalItems);

  console.log(`Updating pagination: Showing ${startItem}-${endItem} of ${totalItems} items`); // Debug

  paginationInfo.textContent = `Showing ${startItem}-${endItem} of ${totalItems} items`;
}


//code added by ptpatel on date 21-03-25
function createSingleEditButton(uitype, tabid, recordId, field, fieldid, headername) {
  const editButton = document.createElement("a");
  editButton.innerHTML = '<i class="fa-solid fa-pen" style="cursor: pointer;color:#5c9cff"></i>';
  editButton.classList.add("edit-btn");
  editButton.style.marginRight = "10px"; // Add spacing
   // Click event listener for editing
    editButton.addEventListener("click", function (event) {
      event.stopPropagation(); // Prevent link click when clicking the button
      //below if condition added for inventory status update to open another model to show data 
      if(tabid == 33 ){
        inventorystatusupdate(uitype, tabid, headername, fieldid, recordId, field, "list");
      }
      else{
          singleEdit(uitype, tabid, headername, fieldid, recordId, field, "list");
      }
    });

  return editButton;
}
//end code added by ptpatel date 21-03-25
//code added by ptpatel on date 10-12-2025
  function inventorystatusupdate(
  uitype,
  tabid,
  fieldlabel,
  fieldid,
  recordid,
  columnname,
  from
) {
  //,ModuleName, fieldname, tableName, columnname, typeofdata,maximumlength,fieldtype,related_mod) {

  // const singleeditsourcemodule = urlParams.get("sourcemodule");
  // const singleeditsourceid = urlParams.get("sourceid");
  var url = "getinventorydata";
  // if (singleeditsourcemodule && singleeditsourceid) {
  //   // Check if both sourcemodule and sourceid are not null or undefined
  //   url += `?sourcemodule=${encodeURIComponent(
  //     singleeditsourcemodule
  //   )}&sourceid=${encodeURIComponent(singleeditsourceid)}`;
  // }
  console.log("in edit js function of inventorystatusupdate"+url);
  startLoading();
  $.ajax({
    url: url, //"singleedit?sourceid=null&sourcemodule=null",
    type: "POST",
    data: {
      columnname: columnname,
      recordid: recordid,
      tabid: tabid,
      uitype: uitype,
      fieldid: fieldid,
      _csrf: yii.getCsrfToken(), // For CSRF protection
      from: from,
    },
    success: function (response) {
      console.log(response);
      if(response.status == "success"){
        let body_part = response.html;
        $("#editModal #modal_label_name").text("Edit " + fieldlabel);
        $("#editModal .modal-body").html("");
        $("#editModal .modal-body").html(body_part);
        $("#editModal").modal("show");
        loadrequiredscript();
      }
      stopLoading();
    },
    error: function (xhr) {
      stopLoading();
      alert("Failed to update " + fieldlabel + " value!");
      console.error(xhr.responseText);
    },
  });
}
//end code added by ptpatel on date 10-12-2025
  // code added by ptpatel on date 08-04-25
  // $(document).ready(function () {
  //   $(".board-column-1").each(function () {

  //     const startRow = (currentPage - 1) * pageSize;
  //     const columnDiv = $(this);
  //     const columnId = $(this).closest('.board-column').data('status-id');
  //     console.log(columnId);
      
  //     if(columnId != 0){
  //       $.ajax({
  //         url: 'getkanbandata?start=' + startRow + "&limit=" + pageSize, // Adjust the URL to your route
  //         method: 'GET',
  //         data: { column_id: columnId ,_csrf: yii.getCsrfToken(),},
  //         success: function (response) {
  //           columnDiv.html(response); // inject returned HTML inside this column
  //           // columnDiv.append(response);
  //         },
  //         error: function (xhr, status, error) {
  //           console.error("Error loading column:", columnId, error);
  //         }
  //       });
  //     }
  //   });

  //   $('.board-column-1').on('scroll', function () {
  //     const el = $(this);
  //     const startRow = (currentPage - 1) * pageSize;
  //     // ?start=" + startRow + "&limit=" + pageSize;
  //     const columnDiv = $(this);
  //     const columnId = columnDiv.attr("id"); // like "board-column-new_leads"
  //     console.log(columnId);
  //     if (el.scrollTop() + el.innerHeight() >= el[0].scrollHeight - 10) {
  //         loadmorekanbandata(columnDiv,el.data('startkanbanRow'), el.data('page'),columnId);
  //         console.log("asd");
  //     }
  //   });
  // });
  // function loadmorekanbandata(columnDiv,startkanbanRow, page,columnId) {
  //   if(columnId != 0){
  //     $.ajax({
  //       url: 'getkanbandata?start=' + startkanbanRow + "&limit=" + pageSize,
  //       method: 'GET',
  //       data: { column_id: columnId ,_csrf: yii.getCsrfToken(),},
  //       success: function (response) {
  //         columnDiv.append(response); // inject returned HTML inside this column
  //         // columnDiv.append(response);
  //       },
  //       error: function (xhr, status, error) {
  //         console.error("Error loading column:", columnId, error);
  //       }
  //     });
  //   }
  // }

  $(document).ready(function () {
    $(".board-column-1").each(function () {
        const columnDiv = $(this);
        const columnId = $(this).closest('.board-column').data('status-id');
        
        // Set initial values
        columnDiv.data('startkanbanRow', 0);
        columnDiv.data('page', 1);
        columnDiv.data('loading', false); // control flag
        columnDiv.data('limit', pageSize); // assuming pageSize is defined globally

        if (columnId != 0) {
            $.ajax({
                url: 'getkanbandata',
                method: 'GET',
                data: {
                    start: 0,
                    limit: pageSize,
                    column_id: columnId,
                    _csrf: yii.getCsrfToken(),
                },
                success: function (response) {
                    columnDiv.html(response);
                    columnDiv.data('startkanbanRow', pageSize);
                    columnDiv.data('page', 2);
                },
                error: function (xhr, status, error) {
                    console.error("Initial load error:", columnId, error);
                }
            });
        }
    });

    $('.board-column-1').on('scroll', function () {
        const el = $(this);

        if (el.data('loading')) return; // skip if already loading

        if (el.scrollTop() + el.innerHeight() >= el[0].scrollHeight - 10) {
            const startRow = el.data('startkanbanRow') || 0;
            const page = el.data('page') || 1;
            const limit = el.data('limit') || 10;
            const columnId = el.closest('.board-column').data('status-id');
            
            el.data('loading', true); // set lock

            loadmorekanbandata(el, startRow, page, columnId, limit);
        }
    });
});

function loadmorekanbandata(columnDiv, startkanbanRow, page, columnId, limit) {
    if (columnId != 0) {
        $.ajax({
            url: 'getkanbandata',
            method: 'GET',
            data: {
                start: startkanbanRow,
                limit: limit,
                column_id: columnId,
                _csrf: yii.getCsrfToken(),
            },
            success: function (response) {
                if (response.trim() !== '') {
                    columnDiv.append(response);
                    columnDiv.data('startkanbanRow', startkanbanRow + limit);
                    columnDiv.data('page', page + 1);
                } else {
                    console.log("No more records to load.");
                }
                columnDiv.data('loading', false); // release lock
            },
            error: function (xhr, status, error) {
                console.error("Error loading more data for column:", columnId, error);
                columnDiv.data('loading', false); // release lock on error
            }
        });
    }
}

  
  // end code added by ptpatel on date 08-04-25
  //code for exportall inventory on date 08-05-25
  function exportAllRows(moduleName) {
    const allRows = [];
    // gridApi.forEachNode((node) => allRows.push(node.data)); // Get all rows
  
    // gridApi.forEachNodeAfterFilterAndSort((node) => {
    //   if (node.data) {
    //     allRows.push(node.data);
    //   }
    // });
    // if (allRows.length === 0) {
    //   alert("No data to export.");
    //   return;
    // }
  
    // const allRowIds = allRows.map((row) => row.RecordId);
    // console.log("All Row IDs:", allRowIds);
  
    csrfTokenName = $("#csrfTokenName").val();
    csrfToken = $("#csrfToken").val();
    startLoading();
  // return false;
    $.ajax({
      url: "exportalldata", // Your server endpoint
      type: "POST",
      data: {
        selectedRowIds: 'all', // You can rename this to `allRowIds` if needed
        _csrf: csrfToken,
      },
      success: function (response) {
        console.log("Response from server:", response);
  
        if (!response.success) {
          alert("Failed to export data. Server responded with an error.");
          return;
        }
        if(response.message)
        {
          alert(response.message);
          return;
        }
  
        const { headers, rows } = response;
  
        if (!headers || !rows) {
          alert("Invalid data format received from the server.");
          console.error("Response:", response);
          return;
        }
      setTimeout(() => {
        let excelData = `
            <html xmlns:o="urn:schemas-microsoft-com:office:office"
                  xmlns:x="urn:schemas-microsoft-com:office:excel"
                  xmlns="http://www.w3.org/TR/REC-html40">
            <head>
              <meta charset="UTF-8">
              <style>
                table { border-collapse: collapse; }
                td, th { border: 1px solid black; padding: 5px; }
                th { background-color: #f2f2f2; font-weight: bold; }
              </style>
            </head>
            <body>
              <table>
                <thead>
                  <tr>
                    ${headers.map((header) => `<th>${header}</th>`).join("")}
                  </tr>
                </thead>
                <tbody>
          `;
  
        Object.values(rows).forEach((row) => {
          excelData += `
        <tr>
            ${row.map((cell) => `<td>${cell || ""}</td>`).join("")}
        </tr>
    `;
        });
  
        excelData += `
                </tbody>
              </table>
            </body>
            </html>
          `;
  
        const blob = new Blob([excelData], { type: "application/vnd.ms-excel" });
        const link = document.createElement("a");
        link.href = URL.createObjectURL(blob);
        link.download = "All_"+moduleName+".xls";
        link.click();
        URL.revokeObjectURL(link.href);
         stopLoading(); // Stop loader after export completes
      }, 100); // 100ms delay allows UI to render loader
      },
      error: function (error) {
        alert("Failed to export data. Please try again.");
        console.error("AJAX Error:", error);
      },
    });
  }
  
  //end code for exportall inventory on date 08-05-25

  //this code is for show icon in listview if uitype = 5(file) 
  function fileCellRenderer(fileName) {
    if (!fileName) return '';
    const pathmatch =  getBaseeUrl().replace(location.origin, "");
    const fileExt = fileName.split('.').pop().toLowerCase();
    const imageTypes = ["jpg", "jpeg", "png", "svg", "webp"];
    let iconImage = "fileicon_img.svg";

    if (fileExt === "pdf") iconImage = "fileicon_pdf.svg";
    else if (["doc","docx"].includes(fileExt)) iconImage = "fileicon_doc.svg";
    else if (["xls"].includes(fileExt)) iconImage = "fileicon_xls.svg";
    else if (["xlsx"].includes(fileExt)) iconImage = "fileicon_xlsx.svg";
    else if (["msg"].includes(fileExt)) iconImage = "fileicon_msg.svg";
    else if (["eml"].includes(fileExt)) iconImage = "fileicon_eml.svg";
    else if (["zip"].includes(fileExt)) iconImage = "fileicon_zip.svg";
    else if (imageTypes.includes(fileExt)) iconImage = "fileicon_img.svg";
    const iconPath = `${pathmatch}thememain/img/file-icon/${iconImage}`;
    // URL of the file (adjust according to your file path)
    const fileUrl = `/uploads/${fileName}`;

    // If image, show thumbnail on hover, else icon
    const hoverThumb = imageTypes.includes(fileExt)
        ? `<img src="${fileUrl}" class="file-hover-thumb" style="width:25px;height:25px;object-fit:cover;position:absolute;top:-60px;left:50%;transform:translateX(-50%);border:1px solid #ccc;border-radius:4px;box-shadow:0 2px 6px rgba(0,0,0,0.3);z-index:10;display:none;">`
        : '';

    return `
        <div class="file-preview-wrapper" style="position:relative; display:inline-block;">
                <img src="${iconPath}" class="file-icon-img" alt="${fileExt} file"> ${fileName}
            ${hoverThumb}
        </div>
    `;
}
 //end code is for show icon in listview if uitype = 5(file) 