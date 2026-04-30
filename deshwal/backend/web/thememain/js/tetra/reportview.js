var moduleName = jQuery("#module").val();
  const pathParts = window.location.pathname.split('/');
  const moduleIndex = pathParts.indexOf(moduleName);
  const baseUrl = window.location.origin + pathParts.slice(0, moduleIndex + 1).join('/');
  const col_width = 120;
//code for exportall inventory on date 08-05-25
function exportAllRows(moduleName) {
  const allRows = [];
  csrfTokenName = $("#csrfTokenName").val();
  csrfToken = $("#csrfToken").val();
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

      const { headers, rows } = response;

      if (!headers || !rows) {
        alert("Invalid data format received from the server.");
        console.error("Response:", response);
        return;
      }

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
            ${row.map((cell) => `<td>${cell != null ? cell : ""}</td>`).join("")}
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
      link.download = "All_" + moduleName +"_"+ getDateTime()+ ".xls";
      link.click();
      URL.revokeObjectURL(link.href);
    },
    error: function (error) {
      alert("Failed to export data. Please try again.");
      console.error("AJAX Error:", error);
    },
  });
}

function getDateTime() {
  var now = new Date();

  var dd = String(now.getDate()).padStart(2, '0');
  var mm = String(now.getMonth() + 1).padStart(2, '0');
  var yyyy = now.getFullYear();

  var hours = String(now.getHours()).padStart(2, '0');
  var minutes = String(now.getMinutes()).padStart(2, '0');
  var seconds = String(now.getSeconds()).padStart(2, '0');

  var formattedDateTime = dd + '-' + mm + '-' + yyyy + '_' + hours + ':' + minutes + ':' + seconds;
  return formattedDateTime;
}

//column in filter
  const filterColumnSelect = document.getElementById('filterColumn');

  if (filterColumnSelect) {
    fetch(`${baseUrl}/filteroptioncolumn`)
      .then(res => {
        if (!res.ok) throw new Error('Network response was not ok');
        return res.json();
      })
      .then(columns => {
        console.log(columns);
        filterColumnSelect.innerHTML = '<option value="">-- Select Column --</option>';
        columns.forEach(col => {
          const option = document.createElement('option');
          option.value = col.value;
          option.textContent = col.label;
          filterColumnSelect.appendChild(option);
        });
      })
      .catch(error => {
        console.error('Error loading filter columns:', error);
        filterColumnSelect.innerHTML = '<option value="">Failed to load columns</option>';
      });
  }
 
  //opertor in filter
  const filterColumnOperator = document.getElementById('filterOperator');

   if (filterColumnOperator) {
    fetch(`${baseUrl}/filtercolumnoperator`)
      .then(res => {
        if (!res.ok) throw new Error('Network response was not ok');
        return res.json();
      })
      .then(columns => {
        console.log(columns);
        columns.forEach(col => {
          const option = document.createElement('option');
          option.value = col.value;
          option.textContent = col.label;
          filterColumnOperator.appendChild(option);
        });
      })
      .catch(error => {
        console.error('Error loading filter columns:', error);
        filterColumnOperator.innerHTML = '<option value="">Failed to load columns</option>';
      });
  }

  //  document.getElementById('openFilterModal').addEventListener('click', function () {
  //   const modal = new bootstrap.Modal(document.getElementById('filterModal'));
  //   modal.show();
  // });
  const openFilterBtn = document.getElementById('openFilterModal');
  if (openFilterBtn) {
    openFilterBtn.addEventListener('click', function () {
      const modalEl = document.getElementById('filterModal');
      if (modalEl) {
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
      }
    });
  }


  // Dropdown toggle
  document.getElementById('exportAllButton').addEventListener('click', function () {
    document.getElementById('exportDropdown').classList.toggle('show');
  });

  window.addEventListener('click', function (e) {
    if (!e.target.matches('#exportAllButton')) {
      document.getElementById('exportDropdown').classList.remove('show');
    }
  });



  // Export PDF Placeholder
  document.getElementById('exportPDF').addEventListener('click', function (e) {
    e.preventDefault();
    alert('PDF export not implemented yet');
  });

  /**
   * get account code start from here
   */
    fetch(`${baseUrl}/getaccountsforreport`)
      .then(response => response.json())
      .then(response => {
        if (response.status === "success") {
          const dropdown = document.getElementById("acc_dropdown");
          dropdown.innerHTML = '<option value="">Select Account</option>'; // reset first
          dropdown.innerHTML = '<option value="all">All</option>';
          response.data.forEach(account => {
            const option = document.createElement("option");
            option.value = account.vendoraccid;
            option.textContent = account.acc_name;
            dropdown.appendChild(option);
          });
          fapplySelect2();
          // If you're using Select2, refresh it
          // if ($(dropdown).data('select2')) {
          //   $(dropdown).trigger('change.select2');
          // }
        } else {
          console.error("API returned error:", response.message);
        }
      })
      .catch(error => {
        console.error("Fetch failed:", error);
      });
  /*
  *get account code end here
  /*
  /**
   *  Load dynamic columns from backend
   * 
   * */
  function loadDynamicColumns() {
    return fetch(`${baseUrl}/getcolumnfieldsforreport`, {
      method: 'GET',
      headers: { 'Content-Type': 'application/json' },
    })
      .then(res => res.json())
      .then(columns => {
        if (!columns || !Array.isArray(columns)) {
          console.error("Invalid column response:", columns);
          return [];
        }

        const newColumnDefs = buildColumnDefs(columns, col_width);
        return newColumnDefs; // return the array from promise
      })
      .catch(error => {
        console.error('Error fetching column defs:', error);
        return [];
      });
  }

  /*
  *this function make column in given format while fetching column dynamically
  */
  function buildColumnDefs(columns, colWidth = 100) {
    return columns.map(col => ({
      headerName: col.headerName,   // visible column title
      field: col.field,        // data field name (from backend)
      minWidth: colWidth,
      flex: 1
    }));
  }
  
  $(document).on("click", "#clearfilter", function () {
  $("#from_date").val("");
  $("#to_date").val("");
  $("#acc_dropdown").val("all").trigger('change');
  });
/**HELPER function to add select2 if not apply */
function fensureSelect2Loaded(baseUrl) {
  return new Promise((resolve) => {
    if (typeof $.fn.select2 !== 'undefined') {
      // console.log("Select2 already loaded");
      resolve();
    } else {

      // Load CSS
      const css1 = document.createElement('link');
      css1.rel = 'stylesheet';
      css1.href = baseUrl + "thememain/css/select2.min.css";
      document.head.appendChild(css1);

      // Load JS
      const script1 = document.createElement('script');
      script1.src = baseUrl + "thememain/js/select2.min.js";
      script1.onload = function () {
        resolve();
      };
      document.head.appendChild(script1);
    }
  });
}
function fapplySelect2() {
  $('.singleselect').each(function () {
    if (!$(this).hasClass('select2-hidden-accessible')) {
      $(this).select2({
        placeholder: "Select",
        allowClear: true,
        width: '100%'
      });
    }
  });
  $('.multySelect').each(function () {
    if (!$(this).hasClass('select2-hidden-accessible')) {
      $(this).select2({
        placeholder: "Select",
        allowClear: true,
        width: '100%'
      });
    }
  });
}
function getAbsoluteUrl() {
  var newURL = window.location.href;
  var module = jQuery("#module").val();
  var str = newURL.indexOf(module);

  var slicestr = newURL.substring(0, str);
  return slicestr;
}
/**HELPER function to end */