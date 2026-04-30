// function initDetailGrid(rowData) {
console.log(">>> detailedit.js executing...");
window.initDetailGrid = function(rowData) { 
  console.log("initDetailGrid globally available", rowData);
  const col_width = 120;  
  const day_col_width = 100;  
  const columnDefs = [
        { headerName: "GRN Date", field: "grn_date",minWidth: col_width,flex: 1 },
        { headerName: "Lot Number", field: "lot_no" ,minWidth: col_width,flex: 1},
        { headerName: "Account Name", field: "account_name",minWidth: col_width,flex: 1 },
        { headerName: "Product Name", field: "product_name" ,minWidth: col_width,flex: 1},
        { headerName: "Sub-Category", field: "sub_catagory_value",minWidth: col_width,flex: 1 },
        { headerName: "Quantity", field: "qty" ,minWidth: 70,flex: 1},
        { headerName: "0-15 Days", field: "day_0_15" ,minWidth: day_col_width,flex: 1},
        { headerName: "16-30 Days", field: "day_16_30" ,minWidth: day_col_width,flex: 1},
        { headerName: "31-60 Days", field: "day_31_60" ,minWidth: day_col_width,flex: 1},
        { headerName: "61-90 Days", field: "day_61_90",minWidth: day_col_width,flex: 1 },
        { headerName: "91-180 Days", field: "day_91_180",minWidth: day_col_width,flex: 1 },
        { headerName: ">180 Days", field: "day_180_plus",minWidth: day_col_width,flex: 1 },
        { headerName: "Total Value", field: "total_value",minWidth: day_col_width,flex: 1 }
    ];

    /*const gridOptions = {
        columnDefs: columnDefs,
        rowData: rowData,
        // pagination: true,
        // paginationPageSize: 10,
        // domLayout: 'autoHeight',
    };*/

    const gridOptions = {
    columnDefs: columnDefs,
    rowData: rowData,
    defaultColDef: {
      sortable: true,
    //   filter: true,
      resizable: false,
    suppressSizeToFit: true
    },
    onGridReady: (params) => {
      params.api.sizeColumnsToFit();
    },
    pagination: true,
    paginationPageSize: 1000,
    paginationPageSizeSelector: [1000,2000,3000,5000,10000],
    animateRows: true,
  };

    const gridDiv = document.getElementById("reportdetailsGrid");
    if (gridDiv) {
        new agGrid.Grid(gridDiv, gridOptions);
    } else {
        console.error("Grid div not found");
    }
}

document.getElementById('backToGrid').addEventListener('click', () => {
  document.getElementById('detailPanel').style.display = 'none';
  document.getElementById('gridPanel').style.display = 'block';
});

document.getElementById('detailexportExcel').addEventListener('click', function (e) {
    e.preventDefault();
    console.log("detailexportExcel click");
    let moduleName = 'inventory-ageing-detail';
    exportAllRows(moduleName);
  });
   // Export PDF Placeholder
  document.getElementById('detailexportPDF').addEventListener('click', function (e) {
    e.preventDefault();
    alert('PDF export not implemented yet');
  });
function exportAllRows(moduleName) {
  const subcategory_id = $("#subcategory_id").val();
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
        subcategory_id : subcategory_id
      },
      success: function (response) {
        console.log("Response from server:", response.rows[0][5]);
  
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
        link.download = "All_"+moduleName+"_subcategory_"+response.rows[0][5]+"_"+ getDateTime()+ ".xls";
        link.click();
        URL.revokeObjectURL(link.href);
      },
      error: function (error) {
        alert("Failed to export data. Please try again.");
        console.error("AJAX Error:", error);
      },
    });
  }