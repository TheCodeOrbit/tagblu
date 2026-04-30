$(document).ready(function () {
  // $("#related_columns").select2({
  //     maximumSelectionLength: 3
  // });
  $(document).on("click", "#refresh-icon", function (e) {
    location.reload(); // Reload the current page
  });

  $(document).on("change", "#source_module", function () {
    let tabid = $(this).val();

    $.ajax({
      url: "getrelatedmodules",
      type: "GET",
      data: { tabid: tabid },
      success: function (response) {
        let dropdown = $("#related_module");
        dropdown.empty();
        dropdown.append('<option value="">Select</option>');

        $.each(response, function (i, item) {
          dropdown.append(
            `<option value="${item.related_module}">
                          ${item.tablabel}
                      </option>`
          );
        });
        // 2️⃣ Fill table
        let tableBody = $("#relatedtable tbody");

        // Create <tbody> if missing
        if (tableBody.length === 0) {
          $("#relatedtable").append("<tbody></tbody>");
          tableBody = $("#relatedtable tbody");
        }

        tableBody.empty(); // clear previous rows
        console.log(response);
        if (!response || response.length === 0) {
          tableBody.append(`
              <tr>
                  <td colspan="3" class="text-center">
                      No Related Modules found.
                  </td>
              </tr>
          `);
          return; // stop execution
      }
        // <td><a href="<?= Yii::$app->urlManager->createUrl(['modulerelation/update', 'id' => `+item.id+`]) ?>" class="btn btn-primary btn-sm">Edit</a></td>
        $.each(response, function (i, item) {
          tableBody.append(`
                      <tr 
                          data-related_module_id="${item.related_module}"
                          data-source_module_id="${item.source_module}"
                          data-id="${item.id}"
                          data-related_columns="${item.related_columns}"
                      >
                          <td>${item.tablabel}</td>
                          <td>${item.related_column_labels ?? '-'}</td>
                          <td><button class="btn btn-primary btn-sm editrelatedcolumns" id="editrelatedcolumns">Edit</button></td>
                      </tr>
                  `);
        });
        // pagination start
        // pagination end
      }
    });
  });

  $(document).on("change", "#related_module", function () {
    let tabid = $(this).val();
    getFields(tabid);
  });

   //save data
  $(document).on("click", ".savemodulerelation", function () {

    let selectedCols = $("#related_columns").val();
     if (!selectedCols || selectedCols.length === 0) {
        alert("Please select at least one Related Column.");
        return;  
    }
    console.log($("#related_columns").val());
    let data = {
      _csrf: $("#csrfToken").val(),
      id:$("#editRecordId").val(),
      related_columns : selectedCols
    };

    $.ajax({
      type: "POST",
      url: "saverelationmodulecolumns",
      data: data,
      success: function (data) {
        if (data.status === "success") {
          $("#relatedcolumnModal").modal("hide");
          $("#source_module").trigger("change");
          alert(data.message);
        }
        else  alert(data.message);
      },
      error: function (data) {
        alert("Error occured.please try again");
      },
      dataType: "json",
    });

  });
  // $("#dtrecord").DataTable({
  //   processing: true,
  //   serverSide: false,
  //   ajax: "modulerealtiondata",
  //   columns: [
  //     // { data: "id" },
  //     { data: "source_module_name" },
  //     { data: "related_module_name" },
  //     { data: "related_fieldname" },
  //     { data: "related_recordfieldnme" },
  //     { data: "related_column_labels" },
  //     { data: "action" },
  //   ],
  // });
  const modeInput = document.getElementById("mode");
  if (modeInput && modeInput.value === "Create") {
    // getmoduledata();
  }
  else if (modeInput && modeInput.value === "edit") {
    // getmoduledata();
  }

  $(document).on("click", ".editrelatedcolumns", function (e) {


    let row = $(this).closest("tr");
    let related_module_id = row.data("related_module_id");
    let source_module_id = row.data("source_module_id");
    let id = row.data("id");
    let related_columns = row.data("related_columns");
    getFields(related_module_id,id,related_columns,source_module_id);
    // show modal
    // $("#relatedcolumnModal").modal("show");
  });

  function getFields(related_module_id,id,related_columns,source_module_id) {
    
    $("#editRecordId").val(id);
    $("#sourcemoduleid").val(source_module_id);
    $.ajax({
      url: "getfields",
      type: "GET",
      data: { tabid: related_module_id },
      success: function (response) {
        let dropdown = $("#related_columns");
        dropdown.empty();
        dropdown.append('<option value="">Select Field</option>');

        $.each(response, function (i, item) {
          dropdown.append(
            `<option value="${item.fieldname}">
                            ${item.fieldlabel}
                        </option>`
          );
        });
        if (related_columns) {
                let arr = related_columns.toString().split(",");
                dropdown.val(arr).trigger("change");
            }
            console.log("from getfields"+$("#related_columns").val());
        $("#relatedcolumnModal").modal("show");
      }
    });
  }
});