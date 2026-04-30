document.addEventListener('DOMContentLoaded', function () {
  let quickSearchValue = '';
  let serverFilterColumn = '';
  let serverFilterOperator = '';
  let serverFilterValue = '';

  const csrfToken = document.getElementById("csrfToken")?.value || '';
  const moduleName = jQuery("#module").val();
  const pathParts = window.location.pathname.split('/');
  const moduleIndex = pathParts.indexOf(moduleName);
  const baseUrl = window.location.origin + pathParts.slice(0, moduleIndex + 1).join('/');
  const urlParams = new URLSearchParams(window.location.search);

  window.fromDate = window.toDate = window.accname = '';
  window.gridApi_dash = null;

  const columnDefs_dash1 = loadDynamicColumns();
  columnDefs_dash1.then((cols) => {
    if (cols.length === 0) {
      console.warn('No columns found — grid not initialized.');
      return;
    }

    const gridOptions_dash = {
      columnDefs: cols,
      defaultColDef: {
        resizable: false,
        sortable: true,
        suppressSizeToFit: true
      },
      pagination: true,
      paginationPageSize: 1000,
      overlayNoRowsTemplate: '<span class="text-bold">No result found.</span>',
      rowModelType: 'infinite',
      cacheBlockSize: 10,
      animateRows: true,
      onGridReady: (params) => {
        params.api.sizeColumnsToFit();
        gridApi_dash = params.api;
      },
      datasource: {
        getRows: function (params) {
          startLoading();
          const page = (params.startRow / gridOptions_dash.paginationPageSize) + 1;
          let url = `${baseUrl}/reportdata?page=${page}&search=${encodeURIComponent(quickSearchValue)}`;

          // Add filters to the URL
          if (window.fromDate) url += `&from_date=${encodeURIComponent(window.fromDate)}`;
          if (window.toDate) url += `&to_date=${encodeURIComponent(window.toDate)}`;
          if (window.accname) url += `&accname=${encodeURIComponent(window.accname)}`;

          const sortModel = params.sortModel;
          if (sortModel?.length > 0) {
            const sortCol = sortModel[0].colId;
            const sortDir = sortModel[0].sort;
            url += `&sort_column=${sortCol}&sort_direction=${sortDir}`;
          }

          fetch(url, { headers: { 'X-CSRF-Token': csrfToken } })
            .then(response => response.json())
            .then(data => {
              if (data.rows?.length > 0) {
                params.successCallback(data.rows, data.total);
                gridApi_dash.hideOverlay();
              } else {
                params.successCallback([], 0);
                gridApi_dash.showNoRowsOverlay();
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

    new agGrid.Grid(document.getElementById('reportGrid'), gridOptions_dash);
  });

  //  Apply Filter Button Click
  document.getElementById('filterRecords').addEventListener('click', function () {
    window.accname = document.getElementById('acc_dropdown').value || '';
    window.fromDate = document.getElementById('from_date').value || '';
    window.toDate = document.getElementById('to_date').value || '';

    const fromDate = new Date(window.fromDate);
    const toDate = new Date(window.toDate);

    if (window.fromDate && window.toDate && fromDate > toDate) {
      // e.preventDefault();
      alert("From Date cannot be greater than To Date!");
      window.toDate = "";
      $("#toDate").focus();
      return false;
    }

    if (window.gridApi_dash) {
      window.gridApi_dash.purgeInfiniteCache(); // reload grid with filters
    }
  });

  //  Export Excel
  document.getElementById('exportExcel').addEventListener('click', function (e) {
    e.preventDefault();
    let moduleName = 'sourcingdeal';
    exportAllRows(moduleName);
  });

  stopLoading();
});
$(document).on("click", "#refresh-icon", function () {
  console.log("refresh call");
  window.fromDate = window.toDate  = window.user  = window.activity  = '';
  $("#from_date").val("");
  $("#to_date").val("");
  $("#acc_dropdown").val("").trigger('change');
      if (window.gridApi_dash) {
        startLoading();
        window.gridApi_dash.purgeInfiniteCache(); // reloads grid from /reportdata
        stopLoading();
    }

  });
