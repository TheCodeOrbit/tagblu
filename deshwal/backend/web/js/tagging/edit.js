$(document).ready(async function () {
  var newURL = window.location.href;
  var module = "tagging";
  var str = newURL.split(module);
  var action = str[1].split("/")[1].split("?")[0];
  const urlParams = new URLSearchParams(window.location.search);
  const itemid = urlParams.get('itemid').split("_");
  const modeInput = document.getElementById("mode").value;

  // startLoading();
  // if(action != "edit")
  /*var data = {
    Recordid: itemid[0],
    Productid:itemid[1],
    Subcategory:itemid[2],
    _csrf: $("#csrfToken").val(),
  };
  $.ajax({
    type: "POST",
    url: "getdatafrominventory",
    // async:true,
    data: data,
    success: //async 
      function (data) {
        if (data.status === "success") { 
          // console.log(data.data[0]);
          let grn_date = data.data[0].createdtime.split(" ")[0].split("-");
          $("#grn_no").val(data.data[0].grn_no);
          $("#grn_date").val(grn_date[2]+"-"+grn_date[1]+"-"+grn_date[0]);
          // $("#total_quantity").val(data.data.received_qty);
          $("#pickup_id").val(data.data[0].pickup_id);
          $("#lot_no").val(data.data[0].lot_no);
          
          let product_qty = data.data.length;
          let product_name = data.data[0].product_name;
          let product_id = data.data[0].product_id;
          let sub_category = data.data[0].subcategory;
        
          // if(data.length > 0 )
            { 
              for (let i = 0; i < product_qty; i++) {
                // console.log("data.data[i].inventory_id->"+data.data[i].inventory_id);
                //2652 is tagging product details block
                addAutofilledRowBtn("2652", "tagging",data.data[i].inventory_id ,'inventory',i);
              }
            }
          // stopLoading();
        }
      },
    error: function (data) {
      // if error occured

      // alert("Error occured.please try again");
      // stopLoading();
    },
    dataType: "json",
  });
*/
//added on 10 dec 2025 by deepika

if(modeInput == 'Create')
{
  
var data = {
    Recordid: itemid[0],
    Productid: itemid[1],
    Subcategory: itemid[2],
    _csrf: $("#csrfToken").val(),
  };

  startLoading();

  try {
    const response = await $.ajax({
      type: "POST",
      url: "getdatafrominventory",
      data: data,
      dataType: "json",
    });

    if (response.status === "success") {
      let grn_date = response.data[0].createdtime.split(" ")[0].split("-");
      $("#grn_no").val(response.data[0].grn_no);
      $("#grn_date").val(grn_date[2] + "-" + grn_date[1] + "-" + grn_date[0]);
      $("#pickup_id").val(response.data[0].pickup_id);
      $("#lot_no").val(response.data[0].lot_no);

      let product_qty = response.data.length;
      const promises = response.data.map((item, i) =>
        addAutofilledRowBtn("2652", "tagging", item.inventory_id, "inventory", i)
      );

      await Promise.all(promises);
    }
  } catch (err) {
    console.error("Error loading inventory:", err);
  } finally {
    stopLoading(); // ✅ ensure stop after everything done
  }

}
else if(modeInput == 'bulkuplaodtagging')
{
  //get detail of first block
   let data = {
        Recordid: itemid[0],
        Productid: itemid[1],
        Subcategory: itemid[2],
        _csrf: $("#csrfToken").val(),
    };
   startLoading();

  try {
    const response = await $.ajax({
      type: "POST",
      url: "gettaggingdetail",
      data: data,
      dataType: "json",
    });

    if (response.status === "success") {
      // alert(response.status);

      let grn_date = response.data.createdtime.split(" ")[0].split("-");
      // alert(response.data.grn_no);
      $("#grn_no").val(response.data.grn_no);
      // alert($("#grn_no").val());

      $("#grn_date").val(grn_date[2] + "-" + grn_date[1] + "-" + grn_date[0]);
      $("#pickup_id").val(response.data.pickup_id);
      $("#lot_no").val(response.data.lot_no);

    }
  } catch (err) {
    console.error("Error loading inventory:", err);
  } finally {
    stopLoading(); // ✅ ensure stop after everything done
  }
  //download csv
$("#downloadCsvBtn").on("click", function () {
    startLoading();

    let data = {
        Recordid: itemid[0],
        Productid: itemid[1],
        Subcategory: itemid[2],
        _csrf: $("#csrfToken").val(),
    };

    fetch("downloadinventorycsv", {
        method: "POST",
        headers: {
            "X-CSRF-Token": $("#csrfToken").val(),
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: new URLSearchParams(data)   // send POST data
    })
    .then(response => {
        if (!response.ok) {
            throw new Error("Network response was not ok");
        }
        return response.blob();
    })
    .then(blob => {
        let url = window.URL.createObjectURL(blob);

        const a = document.createElement("a");
        a.href = url;
        a.download = "inventory_export.csv";
        document.body.appendChild(a);
        a.click();
        a.remove();
        window.URL.revokeObjectURL(url);
    })
    .catch(err => {
        console.error("Download error:", err);
        alert("Unable to download CSV. Please try again.");
    })
    .finally(() => {
        stopLoading();   // your loader stop function
        console.log("Download finished.");
    });
});

let previewRows = [];
let filteredRows = [];
let currentPage = 1;
let pageSize = 10;
let currentSortCol = null;
let sortDirection = "asc";

function applySearchFilter() {
    let search = $("#csvSearch").val().toLowerCase();

    filteredRows = previewRows.filter(r =>
        Object.values(r).some(v =>
            String(v).toLowerCase().includes(search)
        )
    );

    currentPage = 1;
    renderPreviewPage();
}

function applySorting() {
    if (!currentSortCol) return;

    filteredRows.sort((a, b) => {
        let valA = a[currentSortCol] ?? "";
        let valB = b[currentSortCol] ?? "";

        if (valA < valB) return sortDirection === "asc" ? -1 : 1;
        if (valA > valB) return sortDirection === "asc" ? 1 : -1;
        return 0;
    });
}

function renderPreviewPage() {
    applySorting();

    let tbody = $("#csvPreviewTable tbody");
    tbody.empty();

    let start = (currentPage - 1) * pageSize;
    let end = start + pageSize;

    let rowsToShow = filteredRows.slice(start, end);

    rowsToShow.forEach((row, index) => {
        tbody.append(`
            <tr>
                <td>${row.srno}</td>
                <td>${row.productname}</td>
                <td>${row.category}</td>
                <td>${row.subcategory}</td>
                <td>${row.serial}</td>
                <td>${row.tag}</td>
                <td>${row.bin}</td>
                <td>${row.status}</td>
                <td class="text-success">OK</td>
            </tr>
        `);
    });

    $("#pageInfo").text(`Page ${currentPage} of ${Math.ceil(filteredRows.length / pageSize)}`);
}

$("#uploadCsvBtn").on("click", function () {
    $("#uploadCsvInput").click();
});

// When file selected
$("#uploadCsvInput").on("change", function () {
  $("#downloadCsvBtn").hide();
  $("#uploadCsvBtn").hide();        // hide file input
  $("#uploadCancel").show();          // if you have a button wrapper

    let file = this.files[0];
    if (!file) return;

    let formData = new FormData();
    formData.append("csv_file", file);
    formData.append("_csrf", $("#csrfToken").val());

    // Show progress bar
    $("#csvProgress").show();
    $("#csvProgressBar").css("width", "0%").text("0%");

    $.ajax({
        url: "uploadinventorycsv",
        method: "POST",
        data: formData,
        contentType: false,
        processData: false,

        xhr: function () {
            let xhr = new XMLHttpRequest();
            xhr.upload.addEventListener("progress", function (e) {
                if (e.lengthComputable) {
                    let percent = Math.round((e.loaded / e.total) * 100);
                    $("#csvProgressBar").css("width", percent + "%").text(percent + "%");
                }
            });
            return xhr;
        },

       success: function (res) {

    $("#csvPreviewContainer").show();
    let tbody = $("#csvPreviewTable tbody");
    tbody.empty();

    /*if (res.status === "error") {
        alert("Validation errors found. See preview table.");

        // Show table with error rows
        let errors = res.errors;

        Object.keys(errors).forEach(function (rowIndex, i) {
            let errorList = errors[rowIndex].join("<br>");

            tbody.append(`
                <tr class="table-danger">
                    <td>${parseInt(rowIndex) + 2}</td>
                    <td colspan="8"><b>Error:</b> ${errorList}</td>
                </tr>
            `);
        });

        $("#confirmUpdateBtn").hide();
        return;
    }*/
   let rows = res.rows || [];
  //  window.validCsvRows = res.validRows || [];
    if (res.status === "error") {
        alert("Validation errors found. Showing only error rows.");

        let errors = res.errors;
        let rows = res.rows || [];

        tbody.empty(); // Clear table

        // Loop only error rows
        Object.keys(errors).forEach(function (rowIndex) {
            let row = rows[rowIndex];
            let rowNum = parseInt(rowIndex);
            let rowErrors = errors[rowIndex].join("<br>");

            tbody.append(`
                <tr class="table-danger">
                    <td>${rowNum + 1}</td>
                    <td>${row[1] ?? ''}</td>
                    <td>${row[2] ?? ''}</td>
                    <td>${row[3] ?? ''}</td>
                    <td>${row[4] ?? ''}</td>
                    <td>${row[5] ?? ''}</td>
                    <td>${row[6] ?? ''}</td>
                    <td>${row[7] ?? ''}</td>
                    <td class="text-danger">${rowErrors}</td>
                </tr>
            `);
        });

        $("#csvPagination").hide();
        $("#previewTools").hide();

        // Show preview and hide confirm button
        $("#csvPreviewContainer").show();
        $("#confirmUpdateBtn").hide();

        // Complete progress bar
        $("#csvProgressBar").css("width", "100%").text("Completed");
        setTimeout(() => $("#csvProgress").fadeOut(), 1000);

        return; // stop further execution
    }

    // If success – populate preview rows
    alert("CSV validated. Please review and confirm update.");

    window.validCsvRows = res.validRows; // store for update

    // tbody.append(`
    //         <tr>
    //             <td colspan = "7">
    //                 Total Items : ${res.totalItems}<br/>
    //                 Tagged Items : ${res.taggedItems}<br/>
    //                 Not Tagged Items : ${res.notTaggedItems}<br/>
    //                 Empty Items  : ${res.emptyRows}<br/>
    //             </td>
    //         </tr>
    //     `);
    res.validRows.forEach(function (row, index) {
        tbody.append(`
            <tr>
                <td>${index + 2}</td>
                <td>${row.productname}</td>
                <td>${row.category}</td>
                <td>${row.subcategory}</td>
                <td>${row.serial}</td>
                <td>${row.tag}</td>
                <td>${row.bin}</td>
                <td>${row.status}</td>
                <td class="text-success">OK</td>
            </tr>
        `);
    });

     $("#csvPreviewContainer").show();
    $("#csvPagination").show();   // show pagination
    $("#uploadCsvInput").hide();  // hide upload

    $("#previewTools").show();   // show search + page size
    $("#csvPagination").show();  // show pagination

    $("#uploadCsvInput").hide(); // hide file upload

    previewRows = res.validRows;
    filteredRows = [...previewRows];

    currentPage = 1;
    renderPreviewPage();

    $("#confirmUpdateBtn").show();
}
,

        error: function (xhr) {
          
            if (xhr.responseJSON && xhr.responseJSON.message) {
                msg = xhr.responseJSON.message;
            } else if (xhr.responseText) {
                msg = xhr.responseText;
            }
            alert("Upload failed - "+msg);
             $("#csvPreviewContainer").show();//to show cancle button and user can go back
        },

        complete: function () {
            $("#csvProgressBar").css("width", "100%").text("Completed");
            setTimeout(() => $("#csvProgress").fadeOut(), 1000);
        }
    });
});


$("#confirmUpdateBtn").on("click", function () {
    startLoading();
    $.ajax({
        url: "updateinventorycsv",
        method: "POST",
        data: {
            rows: JSON.stringify(window.validCsvRows),
            _csrf: $("#csrfToken").val(),
            taggingdetails : $("#taggingdetails").val() ?? '',
        },
        success: function (res) {
            if (res.status === "success") {
                alert(res.message);
                 // Redirect to dashboard
              if (res.redirect) {
                  window.location.href = res.redirect;
              }
              stopLoading();

            } else {
                alert("Error: " + res.message);
                stopLoading();


            }
        }
    });

});
//for pagination
$("#prevPage").on("click", function () {
    if (currentPage > 1) {
        currentPage--;
        renderPreviewPage();
    }
});

$("#nextPage").on("click", function () {
    if (currentPage < Math.ceil(previewRows.length / pageSize)) {
        currentPage++;
        renderPreviewPage();
    }
});

$("#csvSearch").on("keyup", function () {
    applySearchFilter();
});
$("#pageSizeSelect").on("change", function () {
    pageSize = parseInt($(this).val());
    currentPage = 1;
    renderPreviewPage();
});
$(document).on("click", "th.sortable", function () {
    let col = $(this).data("col");

    if (currentSortCol === col) {
        sortDirection = (sortDirection === "asc" ? "desc" : "asc");
    } else {
        currentSortCol = col;
        sortDirection = "asc";
    }

    renderPreviewPage();
});



}
  const serialNumbers = [];
  const tagNumbers = [];

  // Collect all serial_number values
  $("input.serial_number").each(function () {
      const val = $(this).val().trim();
      if (val) serialNumbers.push(val);
  });

  // Collect all tag_number values
  $("input.tag_number").each(function () {
      const val = $(this).val().trim();
      if (val) tagNumbers.push(val);
  });


   
// $(document).on('blur', "input[id^='serial_number_']", function () {
//   if(!checkserialduplicates($(this).val().trim()))
//     $(this).val('');
// });
// $(document).on('blur', "input[id^='tag_number_']", function () {
//   if(!checktagduplicates($(this).val().trim()))
//     $(this).val('');
// });

$(document).on('keypress', "input[id^='serial_number_']", function (e) {
   if (e.which === 13){
      e.preventDefault();
      const input = $(this);
      const value = input.val().trim();
      serialcheck(input,value);
  }
});
$(document).on('blur', "input[id^='serial_number_']", function (e) {
      const input = $(this);
      const value = input.val().trim();
      serialcheck(input,value)
});
function serialcheck(input,value){
   if(value != ''){
        validateUniqueInputs().then(isUnique => {
          // startLoading();
          if (!isUnique) {
            input.val('');
            input.closest('.form-group').find('.help-block').text(value +' Serial Number already exists.');
            stopLoading();
          } else {
            input.closest('.form-group').find('.help-block').text('');
          }
          // stopLoading();
        });
        checkserialduplicates(value).then(isUnique => {
          // startLoading();
          if (!isUnique) {
            input.val(''); // Clear the value
            input.closest('.form-group').find('.help-block').text(value +' Serial Number already exists.');
            
          } else {
            input.closest('.form-group').find('.help-block').text('');
          }
          // stopLoading();
        });
      }
}
$(document).on('keypress', "input[id^='tag_number_']", function (e) {
  if (e.which === 13) { // ENTER key pressed
      e.preventDefault();
      const input = $(this);
      const value = input.val().trim();
      tagcheck(input,value);
  }
});

$(document).on('blur', "input[id^='tag_number_']", function (e) {
      const input = $(this);
      const value = input.val().trim();
      tagcheck(input,value);
  // }
});
function tagcheck(input,value){
  if(value != ''){
        validateUniqueInputs().then(isUnique => {
          // startLoading();
          if (!isUnique) {
            input.val('');
            input.closest('.form-group').find('.help-block').text(value +' Tag Number already exists.');
            stopLoading();
          } else {
            input.closest('.form-group').find('.help-block').text('');
          }
          // stopLoading();
        });
        checktagduplicates(value).then(isUnique => {
          // startLoading();
          if (!isUnique) {
            input.val('');
            input.closest('.form-group').find('.help-block').text(value +' Tag Number already exists.');
            stopLoading();
          } else {
            input.closest('.form-group').find('.help-block').text('');
          }
          // stopLoading();
        });
      }
}
/* working code
$(document).on('blur', "input[id^='serial_number_']", function () {
  const input = $(this);
  const value = input.val().trim();
  if(value != ''){
    validateUniqueInputs().then(isUnique => {
      // startLoading();
      if (!isUnique) {
        input.val('');
        input.closest('.form-group').find('.help-block').text(value +' Serial Number already exists.');
        stopLoading();
      } else {
        input.closest('.form-group').find('.help-block').text('');
      }
      // stopLoading();
    });
    checkserialduplicates(value).then(isUnique => {
      // startLoading();
      if (!isUnique) {
        input.val(''); // Clear the value
        input.closest('.form-group').find('.help-block').text(value +' Serial Number already exists.');
        
      } else {
        input.closest('.form-group').find('.help-block').text('');
      }
      // stopLoading();
    });
  }
});

$(document).on('blur', "input[id^='tag_number_']", function () {
  const input = $(this);
  const value = input.val().trim();
  if(value != ''){
    validateUniqueInputs().then(isUnique => {
      // startLoading();
      if (!isUnique) {
        input.val('');
        input.closest('.form-group').find('.help-block').text(value +' Tag Number already exists.');
        stopLoading();
      } else {
        input.closest('.form-group').find('.help-block').text('');
      }
      // stopLoading();
    });
    checktagduplicates(value).then(isUnique => {
      // startLoading();
      if (!isUnique) {
        input.val('');
        input.closest('.form-group').find('.help-block').text(value +' Tag Number already exists.');
        stopLoading();
      } else {
        input.closest('.form-group').find('.help-block').text('');
      }
      // stopLoading();
    });
  }
});
*/
  // $(".add-more-records").hide();
  $(".remove-row-btn").remove()
});


function validateUniqueInputs() {
  return new Promise((resolve) => {
    let isValid = true;

    document.querySelectorAll('.serial-help-block').forEach(el => el.textContent = '');
    document.querySelectorAll('.tag-help-block').forEach(el => el.textContent = '');

    const tagNumbers = [];
    const serialNumbers = [];

    document.querySelectorAll('.tag_number').forEach((input) => {
      const value = input.value.trim();
      const helpBlock = input.closest('.form-group').querySelector('.tag-help-block');
      if (value && tagNumbers.includes(value)) {
        helpBlock.textContent = value+' Tag Number already exists.';
        isValid = false;
      } else {
        tagNumbers.push(value);
      }
    });

    document.querySelectorAll('.serial_number').forEach((input) => {
      const value = input.value.trim();
      const helpBlock = input.closest('.form-group').querySelector('.serial-help-block');
      if (value && serialNumbers.includes(value)) {
        helpBlock.textContent = value+' Serial Number already exists.';
        isValid = false;
      } else {
        serialNumbers.push(value);
      }
    });

    resolve(isValid);
  });
}



// function checkserialduplicates(serialNumbers){
//   startLoading();
//   $.ajax({
//     type: "POST",
//     url: "checkserialduplicates", // or '/controller/checkserialduplicates' if not in same controller
//     data: {
//       serialNumbers: serialNumbers,
//       _csrf: yii.getCsrfToken() // important for Yii2 POST requests
//     },
//     dataType: "json",
//     success: function (data) {
//       console.log("Response:", data);
  
//       if (data.duplicates && (data.duplicates.length > 0)) {
//         // Handle duplicates, maybe show error messages
//         alert("Serial No is already exists.");
//         stopLoading();
//         return false;
//       } else {
//         console.log("No duplicates found.");
//         stopLoading();
//         return true;
//       }
      
//     },
//     error: function (xhr, status, error) {
//       console.error("AJAX error:", error);
//     }
//   });
// }

// function checktagduplicates(tagNumbers){
//   startLoading();
//   $.ajax({
//     type: "POST",
//     url: "checktagduplicates", // or '/controller/checkserialduplicates' if not in same controller
//     data: {
//       tagNumbers: tagNumbers,
//       _csrf: yii.getCsrfToken() // important for Yii2 POST requests
//     },
//     dataType: "json",
//     success: function (data) {
//       console.log("Response:", data);
  
//       if (data.duplicates && (data.duplicates.length > 0)) {
//         // Handle duplicates, maybe show error messages
//         alert("Tag No is already exists.");
//         stopLoading();
//         return false;
//       } else {
//         console.log("No duplicates found.");
//         stopLoading();
//         return true;
//       }
//     },
//     error: function (xhr, status, error) {
//       console.error("AJAX error:", error);
//     }
//   });
// }

function checkserialduplicates(value) {
startLoading();
  return $.ajax({
    type: "POST",
    url: "checkserialduplicates",
    data: {
      serialNumbers: [value],
      _csrf: yii.getCsrfToken()
    },
    dataType: "json"
  }).then(function (data) {
    stopLoading();
    return !(data.duplicates && data.duplicates.includes(value));
  });
}

function checktagduplicates(value) {
startLoading();
  return $.ajax({
    type: "POST",
    url: "checktagduplicates",
    data: {
      tagNumbers: [value],
      _csrf: yii.getCsrfToken()
    },
    dataType: "json"
  }).then(function (data) {
    stopLoading();
    return !(data.duplicates && data.duplicates.includes(value));
  });
}
$(document).ready(function () {
  function openAccordionAfterLoader() {
    const collapseEl = document.getElementById("collapse2652");
    if (!collapseEl) return;

    // Use Bootstrap API if available
    if (typeof bootstrap !== "undefined") {
      const collapseInstance = bootstrap.Collapse.getOrCreateInstance(collapseEl);
      collapseInstance.show();
    }

    // Fallback to manual open
    $("#collapse2652").addClass("show").css("height", "auto");
    $('[data-bs-target="#collapse2652"]')
      .removeClass("collapsed")
      .attr("aria-expanded", "true");
  }

  //  Wait until loader is hidden (class "active" removed)
  const loaderCheck = setInterval(() => {
    if (!$("#loading-overlay").hasClass("active")) {
      clearInterval(loaderCheck);
      setTimeout(openAccordionAfterLoader, 300); // small delay for smoothness
    }
  }, 200);

  /*document.addEventListener("click", function (e) {
      const btn = e.target.closest(".savebutton");
      if (!btn) return;

      e.preventDefault();
      e.stopImmediatePropagation();

      let totalItems = 0;
      let taggedItems = 0;
      let notTaggedItems = 0;
      let allValid = true;

      document.querySelectorAll(".product-row").forEach(row => {
          totalItems++;

          const tagInput = row.querySelector(".tag_number");
          const statusSelect = row.querySelector("select[name$='[status]']");
          const serialInput = row.querySelector(".serial_number");
          const subcategory = row.querySelector("select[name$='[subcategory]']")?.value;

          // ---- reset previous error ----
          [tagInput, statusSelect, serialInput].forEach(field => {
              if (field) {
                  field.classList.remove("V~M");
                  field.classList.add("V~O");
                  $(field).closest('.form-group').find('.help-block').html('');
              }
          });

          const prefixMap = {};

          // ----- Check required fields -----
          if (subcategory === "40") { // Mobile
              if (tagInput && !tagInput.value.trim() && statusSelect && statusSelect.value.trim()) {
                  prefixMap[tagInput.id] = "V~M";
                  allValid = false;
              }
              if (statusSelect && !statusSelect.value.trim() && tagInput && tagInput.value.trim()) {
                  prefixMap[statusSelect.id] = "V~M";
                  allValid = false;
              }
          } else { // Other subcategories
              if (tagInput && !tagInput.value.trim()) { prefixMap[tagInput.id] = "V~M"; allValid = false; }
              if (statusSelect && !statusSelect.value.trim()) { prefixMap[statusSelect.id] = "V~M"; allValid = false; }
              if (serialInput && !serialInput.value.trim()) { prefixMap[serialInput.id] = "V~M"; allValid = false; }
          }

          // ----- Apply error classes -----
          Object.keys(prefixMap).forEach(key => {
              const input = document.getElementById(key);
              if (!input) return;

              input.classList.remove("V~O");
              input.classList.add("V~M");

              const helpBlock = $(input).closest('.form-group').find('.help-block');
              if (helpBlock.length) {
                  helpBlock.html("This field is required").addClass("text-red");
              }
          });

          // Count fully tagged rows
          let isTagged = true;
          if (subcategory === "40") {
              if (!tagInput.value.trim() || !statusSelect.value.trim()) isTagged = false;
          } else {
              if (!tagInput.value.trim() || !statusSelect.value.trim() || !serialInput.value.trim()) isTagged = false;
          }

          if (isTagged) taggedItems++;
          else notTaggedItems++;
      });

      const message =
          `Total Items: ${totalItems}\n` +
          `Tagged Items: ${taggedItems}\n` +
          `Not Tagged Items: ${notTaggedItems}\n\n` +
          `Do you want to continue?`;

      if (!confirm(message)) return false;

      if (!allValid) {
          // alert("Please fill all required fields before saving.");
          return false; // stop save
      }
      else{
        
        btn.disabled = true;
        document.querySelector("form[id='pristine-valid-example']").submit();
      }

      // All valid → disable button and submit
      // btn.disabled = true;
      // btn.innerText = "Saving...";

  }, true);*/

  document.addEventListener("click", function (e) {
      const btn = e.target.closest(".savebutton");
      if (!btn) return;

      e.preventDefault();
      e.stopImmediatePropagation();

      let totalItems = 0;
      let taggedItems = 0;
      let notTaggedItems = 0;
      let allValid = true;

      document.querySelectorAll(".product-row").forEach(row => {
          totalItems++;

          const tagInput = row.querySelector(".tag_number");
          const statusSelect = row.querySelector("select[name$='[status]']");
          const serialInput = row.querySelector(".serial_number");
          // const subcategory = row.querySelector("select[name$='[subcategory]']")?.value;
          // const category = row.querySelector("select[name$='[category]']")?.value;
          const subcategoryText = row.querySelector("select[name$='[subcategory]']")?.selectedOptions[0]?.text || '';
          const categoryText = row.querySelector("select[name$='[category]']")?.selectedOptions[0]?.text || '';

          const tagVal = tagInput?.value.trim() || "";
          const statusVal = statusSelect?.value.trim() || "";
          const serialVal = serialInput?.value.trim() || "";

          // reset errors
          [tagInput, statusSelect, serialInput].forEach(field => {
              if (field) {
                  field.classList.remove("V~M");
                  field.classList.add("V~O");
                  $(field).closest('.form-group').find('.help-block').html('');
              }
          });

          const showError = (field) => {
              field.classList.remove("V~O");
              field.classList.add("V~M");
              $(field).closest('.form-group')
                  .find('.help-block')
                  .html("This field is required")
                  .addClass("text-red");
              allValid = false;
          };

          /* ================= MOBILE ================= */
        const normalize = str => str?.toLowerCase().replace(/\s+/g, '').trim();
        const validSubcategories = ['mobile','mouse','headphones','laptopbag','keyboard','mobilecharger','laptopcharger','laptopbattery','datacard'].map(normalize);// add all IDs here
        const validcategories=['cable&wires'].map(normalize);
        const subcategoryNormalized = normalize(subcategoryText);
        const categoryNormalized = normalize(categoryText);
        if (validSubcategories.includes(subcategoryNormalized) || validcategories.includes(categoryNormalized)) {
              // If one of tag / status is filled → other becomes mandatory
              if (tagVal && !statusVal) showError(statusSelect);
              if (statusVal && !tagVal) showError(tagInput);

              if (tagVal && statusVal) taggedItems++;
              else notTaggedItems++;

          }
          /* ============== NON-MOBILE ================= */
          else {

              const anyFilled = tagVal || statusVal || serialVal;

              if (anyFilled) {
                  if (!tagVal) showError(tagInput);
                  if (!statusVal) showError(statusSelect);
                  if (!serialVal) showError(serialInput);
              }

              if (tagVal && statusVal && serialVal) taggedItems++;
              else notTaggedItems++;
          }
      });

     // stop if no tagged items
      if (taggedItems === 0) {
          alert("At least one item must be tagged.");
          return false;
      }

      // stop if validation failed
      if (!allValid) return false;

      const message =
          `Total Items: ${totalItems}\n` +
          `Tagged Items: ${taggedItems}\n` +
          `Not Tagged Items: ${notTaggedItems}\n\n` +
          `Do you want to continue?`;

      if (!confirm(message)) return false;

      btn.disabled = true;
      document.querySelector("form#pristine-valid-example").submit();
  }, true);
  // Helper: mark field valid
  function markFieldValid(field) {
      if (!field) return;
      field.classList.remove("V~M");
      field.classList.add("V~O");
      $(field).closest('.form-group').find('.help-block').html('');
  }

  // Text inputs (input + blur)
  document.addEventListener("input", function(e) {
      const field = e.target.closest(".product-row input");
      if (!field) return;
      if (field.value.trim()) markFieldValid(field);
  });

  document.addEventListener("blur", function(e) {
      const field = e.target.closest(".product-row input, .product-row select");
      if (!field) return;
      if (field.value.trim()) markFieldValid(field);
  }, true); // capture phase needed for blur

  /* ================= SELECT2 VALIDATION FIX (DYNAMIC SAFE) ================= */

// Normal select (also fires for Select2 fallback)
$(document).on("change", ".product-row select", function () {
    const val = $(this).val();
    if (val && val.toString().trim() !== "") {
      markFieldValid(this);
    }
});

// Select2 specific events (required)
$(document).on(
    "select2:select select2:unselect",
    ".product-row select.select2-hidden-accessible",
    function () {
        const val = $(this).val();
        if (val && val.toString().trim() !== "") {
            markFieldValid(this);
        }
    }
);

});

