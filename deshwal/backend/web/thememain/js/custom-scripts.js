$(document).ready(function () {
  // Show the modal when "Add Lead" button is clicked
  // $("#add-lead-btn").click(function () {
  //   $("#add-lead-modal").modal("show");
  // });

  // // Hide the modal when close or cancel buttons are clicked
  // $(".btn-close, .btn-secondary").click(function () {
  //   $("#add-lead-modal").modal("hide");
  // });

  // // Toggle the "active" class on the toggle switch when clicked
  // $(".toggle-switch").on("click", function () {
  //   $(this).toggleClass("active");
  //   toggleRequiredFields();
  // });

  // Function to toggle the visibility of required fields
  function toggleRequiredFields() {
    const isChecked = $(".toggle-switch").hasClass("active");
    const requiredFields = $(".required-field");

    // Show or hide fields based on the toggle state
    requiredFields.each(function () {
      $(this).css("display", isChecked ? "block" : "none");
    });
  }

  // Show the column selector modal and populate column options
  $(".col-selector-btn").click(function () {
    $("#columnSelectorModel").modal("show");
  });

  $("#filterSelectorButton").click(function () {
    const filterLableName = document.getElementById("filterLableName").value;
    $.ajax({
      url: "show-save-filter-feilds", // Update with your actual controller URL
      method: "GET", // or "POST" if your server expects it
      dataType: "json",
      data: {
        filterLableName: filterLableName, // Example filter name
      },
      success: function (response) {
        if (response.status === "success") {
          // Clear previous filter data
          $("#filterBox").empty();

          // Populate the filterBox with the retrieved data
          response.filters.forEach((filter, index) => {
            const uniqueId = `filter-${index}`;
            const operatorId = `filterOperator-${index}`;
            const inputId = `filterValue-${index}`;

            const filterHTML = `
                <div class="field-label-row">
                    <span>${filter.fieldlabel}</span>
                    <button onclick="removeFilter('${uniqueId}')" class="close-button" style="margin-left: 188px;">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
                <!-- Dropdown for selecting comparison operators -->
                <select id="${operatorId}" class="form-select">
                    <option value="Equals" ${
                      filter.filteroperator === "Equals" ? "selected" : ""
                    }>Equals</option>
                    <option value="Not_Equals" ${
                      filter.filteroperator === "Not_Equals" ? "selected" : ""
                    }>Not Equals</option>
                    <option value="Contains" ${
                      filter.filteroperator === "Contains" ? "selected" : ""
                    }>Contains</option>
                    <option value="Not_Contains" ${
                      filter.filteroperator === "Not_Contains" ? "selected" : ""
                    }>Not Contains</option>
                    <option value="In" ${
                      filter.filteroperator === "In" ? "selected" : ""
                    }>In</option>
                    <option value="Not_In" ${
                      filter.filteroperator === "Not_In" ? "selected" : ""
                    }>Not In</option>
                    <option value="is_Empty" ${
                      filter.filteroperator === "is_Empty" ? "selected" : ""
                    }>is Empty</option>
                    <option value="is_Not_Empty" ${
                      filter.filteroperator === "is_Not_Empty" ? "selected" : ""
                    }>is Not Empty</option>
                    <option value="Begins_with" ${
                      filter.filteroperator === "Begins_with" ? "selected" : ""
                    }>Begins With</option>
                </select>
                <!-- Input field for the filter value -->
                <input type="text" id="${inputId}" class="form-control" value="${
              filter.userinput
            }" placeholder="Enter value" style="display:block;" />
            `;

            // Append the generated HTML to the filter box
            const filterContainer = `<div id="${uniqueId}" class="filter-item">${filterHTML}</div>`;
            $("#filterBox").append(filterContainer);
          });

          // Show the filter box
          $("#filterBox").show();
        } else {
          alert(response.message);
        }
      },
      error: function (xhr, status, error) {
        console.error("An error occurred:", error);
        alert("An error occurred while fetching the filter fields.");
      },
    });
    $("#filterByNameModel").modal("show");
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

    // Gather all unchecked (deselected) checkboxes
    $('#columnSelectorModel input[name="column[]"]:not(:checked)').each(
      function () {
        deselectedColumns.push($(this).data("field_id"));
      }
    );

    // Send selected and deselected columns to the server via AJAX
    $.ajax({
      url: "save-selected-columns",
      method: "POST",
      dataType: "json",
      data: {
        selectedColumns: selectedColumns,
        deselectedColumns: deselectedColumns,
        _csrf: yii.getCsrfToken(),
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

  $("#deleteButton").click(function () {
    deleteSelectedRows();
  });
  $("#updateButton").click(function () {
    $("#updateModel").modal("show");
  });

  $(".update-close-btn").click(function () {
    $("#updateModel").modal("hide");
  });

  $("#updatefiled_names").on("change", function () {
    const selectedFieldLabel = $(this).find("option:selected").text(); // Get the selected field label
    const selectedValue = $(this).val(); // Get the selected field ID

    if (selectedValue) {
      $("#field-label").text(`Enter value for ${selectedFieldLabel}`); // Set dynamic label
      $("#field-input-container").show(); // Show the input container
    } else {
      $("#field-input-container").hide(); // Hide the input container if no field is selected
      $("#field-input").val(""); // Clear the input field
    }
  });

  // Handle bulk update submission
  $("#confirmUpdateButton").on("click", function () {
    const hiddenLeadIds = $("#hiddenLeadIds").val(); // Fetch selected lead IDs
    const selectedValue = $("#updatefiled_names").val(); // Fetch selected field ID
    const userInput = $("#field-input").val(); // Fetch user input

    // Validate inputs
    if (!hiddenLeadIds || !selectedValue || userInput.trim() === "") {
      showCustomToast('Missing Fields', 'Please fill in all fields before updating.', 'error');
      return;
    }

    // AJAX request
    $.ajax({
      url: "bulk-update", // Adjust route as needed
      type: "POST",
      data: {
        hiddenLeadIds: hiddenLeadIds,
        selectedValue: selectedValue,
        userInput: userInput,
        _csrf: yii.getCsrfToken(), // Include the CSRF token
      },
      success: function (response) {
        if (response.success) {
          showCustomToast('Update Successful', response.message, 'success');
          $("#updateModel").modal("hide");
          setTimeout(function(){ location.reload(); }, 1500);
        } else {
          showCustomToast('Update Failed', response.message, 'error');
        }
      },
      error: function (xhr, status, error) {
        console.error("AJAX Error:", error);
        showCustomToast('Error', 'An error occurred while updating.', 'error');
      },
    });
  });
});

class CustomHeader {
  init(params) {
    this.params = params;
    this.eGui = document.createElement("div");
    this.eGui.className = "custom-header";
    this.eGui.innerHTML = `
        <span>${params.displayName}</span>
        <span class="sort-arrow" title="Sort">⬆⬇</span>
        <div class="dropdown">
          <img class="dropdown-toggle" src="https://c.animaapp.com/4Te5O9cu/img/ic-round-arrow-left-67.svg" style="width: 16px; cursor: pointer;">
          <div class="dropdown-menu">
            <button class="dropdown-item" data-action="freezeColumn">Freeze Column</button>
            <button class="dropdown-item" data-action="unfreezeColumn">Unfreeze Column</button>
            <button class="dropdown-item" data-action="wrapText">Wrap Text</button>
            <button class="dropdown-item" data-action="clipText">Clip Text</button>
          </div>
        </div>
      `;
    this.setupEventListeners();
  }

  setupEventListeners() {
    const sortArrowButton = this.eGui.querySelector(".sort-arrow");
    const dropdownToggle = this.eGui.querySelector(".dropdown-toggle");
    const dropdownMenu = this.eGui.querySelector(".dropdown-menu");

    sortArrowButton.addEventListener("click", () => {
      const currentSort = this.params.column.getSort();
      const nextSort =
        currentSort === "asc" ? "desc" : currentSort === "desc" ? null : "asc";
      this.params.setSort(nextSort);
    });

    dropdownToggle.addEventListener("click", () => {
      dropdownMenu.style.display =
        dropdownMenu.style.display === "block" ? "none" : "block";
    });

    this.eGui.querySelectorAll(".dropdown-item").forEach((item) => {
      item.addEventListener("click", (event) => {
        this.handleAction(event.target.getAttribute("data-action"));
        dropdownMenu.style.display = "none";
      });
    });

    document.addEventListener("click", (event) => {
      if (!this.eGui.contains(event.target)) {
        dropdownMenu.style.display = "none";
      }
    });
  }

  handleAction(action) {
    const columnField = this.params.column.getColId();
    const columnApi = this.params.api; // Directly access `columnApi` from params

    // Get all columns in the grid state
    const allColumnsState = columnApi.getColumnState();

    // Find the index of the clicked column
    const clickedColumnIndex = allColumnsState.findIndex(
      (col) => col.colId === columnField
    );

    switch (action) {
      case "freezeColumn":
        // Generate state array for all columns up to the clicked one
        const freezeState = allColumnsState
          .slice(0, clickedColumnIndex + 1)
          .map((col) => ({
            colId: col.colId,
            pinned: "left",
          }));

        columnApi.applyColumnState({
          state: freezeState,
          applyOrder: true,
        });
        break;

      case "unfreezeColumn":
        // Generate state array for all columns, setting pinned to null to unfreeze
        const unfreezeState = allColumnsState.map((col) => ({
          colId: col.colId,
          pinned: null,
        }));

        columnApi.applyColumnState({
          state: unfreezeState,
          applyOrder: true,
        });
        break;
      case "wrapText":
        this.toggleWrapText(true);
        break;
      case "clipText":
        this.toggleWrapText(false);
        break;
    }
    this.params.api.refreshCells({
      force: true,
    });
  }

  toggleWrapText(wrap) {
    const columnDef = this.params.column.getColDef();
    columnDef.cellClass = wrap ? "ag-cell-wrap" : "";
  }

  getGui() {
    return this.eGui;
  }
}
let gridApi, gridColumnApi;

// AG Grid options
const gridOptions = {
  columnDefs: [], // Initially empty, to be set dynamically
  rowData: [], // Initially empty, data will be loaded later
  rowSelection: "multiple", // Enables multiple row selection
  onSelectionChanged: onRowSelectionChanged, // Add event for row selection

  defaultColDef: {
    sortable: true,
    filter: false,
    resizable: true,
    wrapText: true,
    autoHeight: true,
  },

  onGridReady: (params) => {
    gridApi = params.api; // Set global API reference
    gridColumnApi = params.columnApi; // Set global Column API reference

    fetchAndSetColumnDefinitions(); // Fetch columns on grid ready
  },
  processHeaderComponentParams: (params) => {
    return {
      ...params,
      columnApi: gridOptions.columnApi, // Pass `columnApi` to each header component
    };
  },
  onCellClicked: (event) => {
    if (event.colDef.field === "firstname") {
      const leadId = event.data.leadid; // Access the lead_id from the clicked row
      window.location.href = `http://localhost/deshwal/admin/lead/lead-details?leadid=${leadId}`; // Redirect to the details page
    }
  },
};

// Function to fetch column definitions and update AG Grid
function fetchAndSetColumnDefinitions() {
  var newURL = window.location.href;
  // var module = jQuery("#module").val();
  var str = newURL.indexOf("list");

  var geturl = newURL.substring(0, str);
  $.ajax({
    url: "get-column-fields", // Adjust to your endpoint
    type: "GET",
    dataType: "json",
    success: function (columns) {
      // Add the checkbox column manually
      const checkboxColumn = {
        headerName: "Select",
        checkboxSelection: true, // Enables checkbox for row selection
        headerCheckboxSelection: true, // Enables "select all" checkbox in the header
        width: 50, // Optional: Set column width
        pinned: "left", // Optional: Pin column to the left
      };

      // Map and process dynamic columns
      const dynamicColumns = columns
        .filter((col) => col.visible) // Only include visible columns
        .map((col) => {
          // Add a click handler for the "First Name" column
          if (col.field === "firstname") {
            return {
              ...col,
              headerName: col.headerName,
              field: col.field,
              headerComponent: CustomHeader,
              resizable: true,
              cellRenderer: (params) => {
                const firstName = params.value;
                return `<span style="cursor: pointer; color: blue; text-decoration: underline;">${firstName}</span>`;
              },
            };
          }
          // For all other columns, return them as is
          return {
            headerName: col.headerName,
            field: col.field,
            headerComponent: CustomHeader,
            resizable: true,
          };
        });

      // Combine the checkbox column with dynamic columns
      const columnDefs = [checkboxColumn, ...dynamicColumns];

      gridOptions.api.setGridOption("columnDefs", columnDefs);
      // Fetch row data after setting column definitions
      fetchPageData(); // Fetch the first page of data

      // fetchRowData();
    },
    error: function (error) {
      console.error("Error fetching column definitions:", error);
    },
  });
}

let currentPage = 1; // Current page number
let pageSize = parseInt(document.getElementById("page-size").value, 10); // Default page size
let totalPages = 1; // Total pages (calculated dynamically)

// Function to fetch data for the current page
function fetchPageData() {
  const startRow = (currentPage - 1) * pageSize;

  $.ajax({
    url: `get-leads?start=${startRow}&limit=${pageSize}`,
    type: "GET",
    dataType: "json",
    success: function (response) {
      // Set data in AG Grid
      console.log("Fetched data:", response.data);
      gridOptions.api.setGridOption("rowData", response.data);
      // Calculate total pages
      totalPages = Math.ceil(response.total / pageSize);

      // Render pagination buttons dynamically
      renderPaginationButtons();
    },
    error: function (error) {
      console.error("Error fetching data:", error);
    },
  });
}

// Function to render pagination buttons dynamically
function renderPaginationButtons() {
  const paginationContainer = document.getElementById("pagination-buttons");
  paginationContainer.innerHTML = ""; // Clear existing buttons

  const maxVisibleButtons = 5; // Maximum number of visible page buttons
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
    button.style.fontWeight = i === currentPage ? "bold" : "normal";
    button.onclick = () => goToPage(i);
    paginationContainer.appendChild(button);
  }
}

// Function to handle page changes
function goToPage(page) {
  if (page >= 1 && page <= totalPages) {
    currentPage = page; // Update current page
    fetchPageData(); // Fetch new data for the page
  }
}
$(document).on("click", '.last-page', function () {
  goToPage(totalPages);
});
$(document).on("click", '.first-page', function () {
  goToPage(1);
});
// Function to handle page size changes
//to resolve server issue on date 01-07-25 added by ptpatel
$(document).on("change", '.page-size-dropdown', function () {
  // alert("from custome.js");
  changePageSize();
});
function changePageSize() {
  pageSize = parseInt(document.getElementById("page-size").value, 10); // Update page size
  currentPage = 1; // Reset to the first page
  fetchPageData(); // Fetch new data with updated page size
}


// Function to fetch row data and update AG Grid
// function fetchRowData() {
//   var newURL = window.location.href;
//   // var module = jQuery("#module").val();
//   var str = newURL.indexOf("list");

//   var geturl = newURL.substring(0, str);
//   $.ajax({
//     url: "get-leads", // Adjust to your endpoint
//     type: "GET",
//     dataType: "json",
//     success: function (data) {
//       console.log("Fetched data:", data);
//       gridOptions.api.setGridOption("rowData", data);
//     },
//     error: function (error) {
//       console.error("Error fetching row data:", error);
//     },
//   });
// }

// Initialize AG Grid after DOM is loaded
document.addEventListener("DOMContentLoaded", function () {
  const gridDiv = document.querySelector("#myGrid");
  new agGrid.Grid(gridDiv, gridOptions);
});

function deleteSelectedRows() {
  const selectedRows = gridOptions.api.getSelectedRows();
  const leadIds = selectedRows.map((row) => row.leadid);

  if (leadIds.length === 0) {
    showCustomToast('No Selection', 'Please select rows before deleting.', 'info');
    return;
  }

  showCustomConfirm(
    'Delete Records?',
    'This will permanently delete <b>' + leadIds.length + ' selected record(s)</b>. This action cannot be undone.',
    'Delete',
    'Cancel',
    'danger'
  ).then(function(confirmed) {
    if (!confirmed) return;
    $.ajax({
      url: "delete-selected-leads",
      type: "POST",
      data: {
        leadIds: leadIds,
        _csrf: yii.getCsrfToken(),
      },
      success: function (response) {
        if (response.status === "success") {
          showCustomToast('Deleted', 'Selected records have been removed.', 'success');
          setTimeout(function(){ fetchRowData(); }, 1000);
        } else {
          showCustomToast('Delete Failed', response.message, 'error');
        }
      },
      error: function (error) {
        console.error("Error during deletion:", error);
        showCustomToast('Error', 'An error occurred while deleting rows.', 'error');
      },
    });
  });
}

// Function to export selected rows to Excel
function exportSelectedRows() {
  const selectedRows = gridApi.getSelectedRows(); // Get selected rows
  if (selectedRows.length === 0) {
    showCustomToast('No Selection', 'Please select rows to export.', 'info');
    return;
  }

  // Define the header for the table
  const headers = [
    "Lead ID",
    "Lead No",
    "First Name",
    "Last Name",
    "Lead Name",
    "Designation",
    "Website",
    "Description",
  ];

  // Start creating Excel content as an HTML table
  let excelData = `
    <html xmlns:o="urn:schemas-microsoft-com:office:office"
          xmlns:x="urn:schemas-microsoft-com:office:excel"
          xmlns="http://www.w3.org/TR/REC-html40">
    <head>
      <!-- Excel file styles -->
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

  // Add each selected row to the table
  selectedRows.forEach((row) => {
    excelData += `
      <tr>
        <td>${row.leadid || ""}</td>
        <td>${row.lead_no || ""}</td>
        <td>${row.firstname || ""}</td>
        <td>${row.lastname || ""}</td>
        <td>${row.leadname || ""}</td>
        <td>${row.designation || ""}</td>
        <td>${row.website || ""}</td>
        <td>${row.description || ""}</td>
      </tr>
    `;
  });

  excelData += `
        </tbody>
      </table>
    </body>
    </html>
  `;

  // Convert the HTML table to a Blob
  const blob = new Blob([excelData], { type: "application/vnd.ms-excel" });

  // Create a download link
  const link = document.createElement("a");
  link.href = URL.createObjectURL(blob);
  link.download = "Selected_Leads.xls"; // Set the file name
  link.click();

  // Clean up
  URL.revokeObjectURL(link.href);
}

function onRowSelectionChanged() {
  const selectedRows = gridOptions.api.getSelectedRows(); // Get selected rows
  const selectedCount = selectedRows.length; // Get the count of selected rows
  selectedRows.forEach((row) => {
    console.log("Selected Lead ID: " + row.leadid);
  });

  const exportButton = document.getElementById("exportButton");
  const deleteButton = document.getElementById("deleteButton");
  const updateButton = document.getElementById("updateButton");

  if (selectedRows.length > 0) {
    exportButton.style.display = "block"; // Show export button
    deleteButton.style.display = "block"; // Show export button
    updateButton.style.display = "block"; // Show export button
    const selectedCountText = `<strong><span id="selectedCount">${selectedCount}</span> leads Selected</strong>`;

    // Append or update the content in the selected count container
    document.getElementById("selectedCountContainer").innerHTML =
      selectedCountText;
    // Create an array to store the lead IDs of selected rows
    const selectedLeadIds = selectedRows.map((row) => row.leadid);

    // Set the value of the hidden input field with the selected lead IDs
    document.getElementById("hiddenLeadIds").value = selectedLeadIds.join(","); // Join IDs with commas for easy processing

    // For debugging (log the selected lead IDs)
    console.log("Selected Lead IDs: " + selectedLeadIds.join(","));
  } else {
    exportButton.style.display = "none"; // Hide export button
    deleteButton.style.display = "none"; // Hide export button
    updateButton.style.display = "none"; // Show export button
    // Dynamically update the content to show the selected count
    const selectedCountText = `<strong><span id="selectedCount">${selectedCount}</span> leads Selected</strong>`;

    // Append or update the content in the selected count container
    document.getElementById("selectedCountContainer").innerHTML = "";
  }
}

document.addEventListener("DOMContentLoaded", function () {
  function openfieldName() {
    document.getElementById("field_name").style.display = "block";
  }

  function openFilterBox(fieldId, fieldLabel) {
    document.getElementById("field_name").style.display = "none";
    // Display the filter box
    document.getElementById("filterBox").style.display = "block";

    // Set the field label in the filter box
    document.getElementById("filterFieldLabel").innerText = fieldLabel;

    // Optionally, you can store the fieldId if you need to process it later
    document.getElementById("filterBox").setAttribute("data-field-id", fieldId);
  }

  function filterFields(searchTerm) {
    const lowerCaseTerm = searchTerm.toLowerCase();
    const fields = document.querySelectorAll("#field_name .filed-div");

    fields.forEach((field) => {
      const label = field.getAttribute("data-label") || ""; // Default to an empty string if null
      if (label.includes(lowerCaseTerm)) {
        field.style.display = ""; // Show the field
      } else {
        field.style.display = "none"; // Hide the field
      }
    });
  }

  // Get the filter operator dropdown and input field
  const filterOperator = document.getElementById("filterOperator");
  const filterValue = document.getElementById("filterValue");

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
    // Hide the filter box
    document.getElementById("filterBox").style.display = "none";

    // Clear the input value if needed
    document.getElementById("filterValue").value = "";
  }

  function applyFilter() {
    const labelValue = document.getElementById("filterFieldLabel").innerText;
    const inputValue = document.getElementById("filterValue").value;
    const filteroperator = document.getElementById("filterOperator").value;
    const fieldId = document
      .getElementById("filterBox")
      .getAttribute("data-field-id");

    // Send selected and deselected columns to the server via AJAX
    $.ajax({
      url: "filter-by-lead",
      method: "POST",
      dataType: "json",
      data: {
        labelValue: labelValue,
        inputValue: inputValue,
        fieldId: fieldId,
        filteroperator: filteroperator,
        _csrf: yii.getCsrfToken(),
      },
      success: function (data) {
        if (data.success) {
          // Assuming 'data.data' contains rows to display in the table
          if (data.data) {
            // Update the grid with filtered data
            updateAgGridData(data.data);
          }
          $("#filterByNameModel").modal("hide");
        } else {
          console.error("Filter error:", data.message);
          alert(data.message);
        }
      },
      error: function (xhr, status, error) {
        console.error("An error occurred:", error);
        alert("An error occurred while applying the filter.");
      },
    });
  }

  function SaveFilter() {
    const labelValue = document.getElementById("filterFieldLabel").innerText;
    const inputValue = document.getElementById("filterValue").value;
    const filteroperator = document.getElementById("filterOperator").value;
    const filterLableName = document.getElementById("filterLableName").value;
    const fieldId = document
      .getElementById("filterBox")
      .getAttribute("data-field-id");
    // Send selected and deselected columns to the server via AJAX
    $.ajax({
      url: "save-filter-by-lead",
      method: "POST",
      dataType: "json",
      data: {
        labelValue: labelValue,
        inputValue: inputValue,
        fieldId: fieldId,
        filteroperator: filteroperator,
        filterLableName: filterLableName,
        _csrf: yii.getCsrfToken(),
      },
      success: function (response) {
        console.log("success");
        $("#filterByNameModel").modal("hide");
      },
      error: function (xhr, status, error) {
        console.error("An error occurred:", error);
        alert("An error occurred while applying the filter.");
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
  window.filterFields = filterFields;
  window.applyFilter = applyFilter;
  window.SaveFilter = SaveFilter;
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

  // Move the card visually to the new column
  newColumn.appendChild(draggedCard);

  // Extract the lead ID from the card ID
  const leadId = draggedCardId.split("-")[1];

  // AJAX call to update the status on the server
  $.ajax({
    url: "update-stage", // Server endpoint
    method: "POST",
    data: {
      leadId: leadId,
      newStatusId: newStatusId,
      _csrf: yii.getCsrfToken(), // Include CSRF token if required
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

// Switch to List View
function switchToListView() {
  document.querySelector(".kanban-view").style.display = "none"; // Hide Kanban
  document.querySelector(".table-list").style.display = "block"; // Show Table
  localStorage.setItem("selectedView", "list"); // Save preference
}

// Switch to Kanban View
function switchToKanbanView() {
  document.querySelector(".table-list").style.display = "none"; // Hide Table
  document.querySelector(".kanban-view").style.display = "flex"; // Show Kanban
  localStorage.setItem("selectedView", "kanban"); // Save preference
}

// Load the selected view on page load
window.onload = function () {
  const selectedView = localStorage.getItem("selectedView") || "list"; // Default to list if none selected
  const viewSelector = document.getElementById("viewSelector");

  if (selectedView === "kanban") {
    switchToKanbanView();
    viewSelector.value = "kanban";
  } else {
    switchToListView();
    viewSelector.value = "list";
  }

  // Add event listener for view change
  viewSelector.addEventListener("change", function () {
    if (this.value === "kanban") {
      switchToKanbanView();
    } else {
      switchToListView();
    }
  });
};

// Automatically mark all mandatory fields with a red asterisk (*)
$(document).ready(function () {
  function markAllMandatoryFields() {
    $('input, select, textarea').each(function () {
      var fieldClass = $(this).attr('class') || '';
      if (fieldClass.indexOf('~M') !== -1 || $(this).prop('required')) {
        var $field = $(this);
        var id = $field.attr('id');
        var $label = $();

        // 1. Try to find label by 'for' attribute
        if (id) {
          $label = $('label[for="' + id + '"]');
        }

        // 2. Try to find label in the parent container
        if (!$label.length) {
          $label = $field.closest('.form-group, .d-flex, td, tr').find('label');
        }

        // 3. Try to find label inside previous elements/siblings
        if (!$label.length) {
          $label = $field.parent().prev().find('label');
          if (!$label.length) {
            $label = $field.prev('label');
          }
        }

        // If label is found, append the asterisk if not already present
        if ($label.length) {
          $label.each(function () {
            var $lbl = $(this);
            if ($lbl.text().indexOf('*') === -1 && !$lbl.find('.required').length) {
              $lbl.append(' <span class="required text-danger" style="color: red !important; font-weight: bold;">*</span>');
            }
          });
        }
      }
    });
  }

  // Run on load and after short delay to catch dynamic/delayed renders
  markAllMandatoryFields();
  setTimeout(markAllMandatoryFields, 500);
  setTimeout(markAllMandatoryFields, 1500);

  // Run on AJAX complete to catch fields loaded dynamically
  $(document).ajaxComplete(function () {
    setTimeout(markAllMandatoryFields, 200);
  });
});

