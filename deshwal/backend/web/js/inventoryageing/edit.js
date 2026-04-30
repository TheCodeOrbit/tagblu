
document.addEventListener('DOMContentLoaded', function () {
  let quickSearchValue = '';
  let serverFilterColumn = '';
  let serverFilterOperator = '';
  let serverFilterValue = '';
  const csrfToken = document.getElementById("csrfToken")?.value || '';
  var moduleName = jQuery("#module").val();
  const pathParts = window.location.pathname.split('/');
  const moduleIndex = pathParts.indexOf(moduleName);
  const baseUrl = window.location.origin + pathParts.slice(0, moduleIndex + 1).join('/');
  const urlParams = new URLSearchParams(window.location.search);
  const subcategory_id = urlParams.get('subcategory') ?? 0;
  const col_width = 120;
  const columnDefs_dash = [
    { headerName: "Sub-Category", field: "sub_catagory_value", minWidth: 150, flex: 1 },
    { headerName: "Quantity", field: "qty", minWidth: col_width, flex: 1 },
    { headerName: "UOM", field: "uom_value", minWidth: 70, flex: 1 },
    { headerName: "0-15 Days", field: "amt_0_15", minWidth: col_width, flex: 1 },
    { headerName: "16-30 Days", field: "amt_16_30", minWidth: col_width, flex: 1 },
    { headerName: "31-60 Days", field: "amt_31_60", minWidth: col_width, flex: 1 },
    { headerName: "61-90 Days", field: "amt_61_90", minWidth: col_width, flex: 1 },
    { headerName: "91-180 Days", field: "amt_91_180", minWidth: col_width, flex: 1 },
    { headerName: ">180 Days", field: "amt_180_plus", minWidth: col_width, flex: 1 },
    { headerName: "Total Value", field: "total_value", minWidth: col_width, flex: 1 },
  ];
  let gridApi_dash = null;

  const gridOptions_dash = {
    columnDefs: columnDefs_dash,
    defaultColDef: {
      resizable: false,
      sortable: true,
      // filter: true,      
      suppressSizeToFit: true
    },
    pagination: true,
    paginationPageSize: 1000,
    overlayNoRowsTemplate: '<span class="text-bold">No result found.</span>',
    paginationPageSizeSelector: [1000,2000,3000,5000,10000],
    rowModelType: 'infinite',
    cacheBlockSize: 10,
    animateRows: true,
    onRowClicked: handleRowClick,
    getRowClass: function (params) {
      return params.node.rowIndex % 2 === 0 ? 'ag-row-even' : 'ag-row-odd';
    },
    // onFilterChanged: updateFilterIcon,
    onGridReady: (params) => {
      // params.api.sizeColumnsToFit();
      params.api.sizeColumnsToFit();
      gridApi_dash = params.api;
      // updateFilterIcon();
    },
    datasource: {
      getRows: function (params) {
        startLoading();
        const page = (params.startRow / gridOptions_dash.paginationPageSize) + 1;
        let url = `${baseUrl}/reportdata?page=${page}&search=${encodeURIComponent(quickSearchValue)}`;

        // Add filters to the URL
        if (serverFilterColumn && serverFilterValue) {
          url += `&filter_column=${serverFilterColumn}&filter_operator=${serverFilterOperator}&filter_value=${encodeURIComponent(serverFilterValue)}`;
        }


        const sortModel = params.sortModel;
        if (sortModel && sortModel.length > 0) {
          const sortCol = sortModel[0].colId;
          const sortDir = sortModel[0].sort;
          url += `&sort_column=${sortCol}&sort_direction=${sortDir}`;
        }

        fetch(url, {
          headers: {
            'X-CSRF-Token': csrfToken
          }
        })
          .then(response => response.json())
          .then(data => {
            // params.successCallback(data.rows, data.total);
            if (data.rows && data.rows.length > 0) {
              params.successCallback(data.rows, data.total);
              gridApi_dash.hideOverlay(); // hide any overlay
            } else {
              params.successCallback([], 0); // tell AG Grid no rows
              gridApi_dash.showNoRowsOverlay(); // show "No result found"
            }
            stopLoading();
          })
          .catch(error => {
            console.error('Error fetching data:', error);
            params.failCallback();
            stopLoading();
          });
      }
    }
  };

  const reportGrid = document.getElementById('reportGrid');
  new agGrid.Grid(reportGrid, gridOptions_dash);



 
  // Fetch & Load initial data (only for full set, not infinite)
  if (!subcategory_id) {
    fetch(`${baseUrl}/getinventoryageing`)
      .then(response => response.json())
      .then(response => {
        if (response.status === "success") {
          gridOptions_dash.api.setGridOption("rowData", response.data);
        } else {
          console.error("API returned error:", response.message);
        }
      })
      .catch(error => {
        console.error("Fetch failed:", error);
      });
  }

  // Export Excel
  document.getElementById('exportExcel').addEventListener('click', function (e) {
    e.preventDefault();
    // gridOptions_dash.api.exportDataAsExcel({
    //   fileName: 'inventory-ageing.xlsx',
    //   sheetName: 'Inventory'
    // });
    let moduleName = 'inventory-ageing';
    exportAllRows(moduleName);
  });


  document.getElementById('quickSearch').addEventListener('input', function () {
    quickSearchValue = this.value.trim(); // Store the value globally
    if (gridApi_dash) {
      gridApi_dash.purgeInfiniteCache(); // Refresh data with new filter
      // stopLoading();
    }
  });

  // Filter modal logic
  const filterBtn = document.getElementById('applyFilter');
  if (filterBtn) {
    filterBtn.addEventListener('click', function () {
      const column = document.getElementById('filterColumn').value;
      const operator = document.getElementById('filterOperator').value;
      const value = document.getElementById('filterValue').value.trim();

      if (!column || !value) {
        alert("Please select column and enter a value");
        return;
      }

      serverFilterColumn = column;
      serverFilterOperator = operator;
      serverFilterValue = value;

      if (gridApi_dash) {
        gridApi_dash.purgeInfiniteCache(); // this will trigger new server call with filters
      }

      const filterModalEl = document.getElementById('filterModal');
      const filterModal = bootstrap.Modal.getInstance(filterModalEl);
      filterModal.hide();
    });
  }


 

  // Back to main grid
  // document.getElementById('backToGrid').addEventListener('click', () => {
  //   document.getElementById('detailPanel').style.display = 'none';
  //   document.getElementById('gridPanel').style.display = 'block';
  // });

  stopLoading(); // Call at end
});
function getAbsoluteUrl() {
  var newURL = window.location.href;
  var module = jQuery("#module").val();
  var str = newURL.indexOf(module);

  var slicestr = newURL.substring(0, str);
  return slicestr;
}
// Handle row click for detail panel
function handleRowClick(event) {
  startLoading();
  const subcategory_id = event.data.subcategory;
  const url = `reportdetailview?subcategory=${subcategory_id}`;
  document.getElementById('detailContent').innerHTML = '<p>Loading…</p>';

  /*fetch(url)
    .then(res => res.json())
    .then(response => {
      document.getElementById('detailContent').innerHTML = response.html;
      $.getScript(getAbsoluteUrl() + 'js/inventoryageing/detailedit.js')
        .done(() => {
          initDetailGrid(response.gridData);
          stopLoading();
        })
        .fail(() => {
          console.error('Failed to load detailedit.js');
        });

      document.getElementById('gridPanel').style.display = 'none';
      document.getElementById('detailPanel').style.display = 'block';
    })
    .catch(err => {
      console.error(err);
      document.getElementById('detailContent').innerHTML = '<p class="text-danger">Failed to load details.</p>';
      stopLoading();
    });*/

      fetch(url)
      .then(res => res.json())
      .then(response => {
        document.getElementById('detailContent').innerHTML = response.html;

        const scriptUrl = getAbsoluteUrl() + 'js/inventoryageing/detailedit.js?v=' + Date.now();
        console.log("Loading detail script securely:", scriptUrl);

        // Remove any previously loaded version
        const existing = document.querySelector(`script[src*="inventoryageing/detailedit.js"]`);
        if (existing) existing.remove();

        // Create secure script tag
        const script = document.createElement("script");
        script.src = scriptUrl;
        script.type = "text/javascript";
        script.async = false;

        // Copy nonce if present on existing scripts (for CSP compliance)
        const nonce = document.querySelector('script[nonce]')?.nonce;
        if (nonce) script.setAttribute("nonce", nonce);

        script.onload = () => {
          console.log("✅ detailedit.js loaded via <script>");
          console.log("typeof initDetailGrid =", typeof initDetailGrid);

          if (typeof initDetailGrid === "function") {
            initDetailGrid(response.gridData);
          } else {
            console.error("initDetailGrid still undefined after <script> load");
          }

          stopLoading();
        };

        script.onerror = (err) => {
          console.error(" Failed to load detailedit.js:", err);
          stopLoading();
        };

        document.head.appendChild(script);

        // Switch panels
        document.getElementById('gridPanel').style.display = 'none';
        document.getElementById('detailPanel').style.display = 'block';
      })
      .catch(err => {
        console.error(err);
        document.getElementById('detailContent').innerHTML = '<p class="text-danger">Failed to load details.</p>';
        stopLoading();
      });

}

// function updateFilterIcon() {
//   const icon = document.getElementById('openFilterModal');

//   if (gridOptions.api.isAnyFilterPresent()) {
//     console.log("filter prsent");
//     // Change icon or highlight
//     icon.src = '/path/to/active-filter-icon.svg';  // use a different icon
//     icon.classList.add('active-filter'); // or style it with a class
//   } else {
//      console.log("filter remove");
//     icon.src = '/thememain/img/typcn-filter.svg';  // default
//     icon.classList.remove('active-filter');
//   }
// }


// end code added by ptpatel on date 08-04-25