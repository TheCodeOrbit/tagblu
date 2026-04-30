
document.addEventListener('DOMContentLoaded', function () {
  let quickSearchValue = '';
  let serverFilterColumn = '';
  let serverFilterOperator = '';
  let serverFilterValue = '';
  var module = jQuery("#module").val();
  const csrfToken = document.getElementById("csrfToken")?.value || '';
  const moduleName = module;
  const pathParts = window.location.pathname.split('/');
  const moduleIndex = pathParts.indexOf(moduleName);
  const baseUrl = window.location.origin + pathParts.slice(0, moduleIndex + 1).join('/');
  const urlParams = new URLSearchParams(window.location.search);
  const subcategory_id = urlParams.get('subcategory') ?? 0;
  const col_width = 120;
  const columnDefs_dash = [
    { headerName: "Category", field: "prod_category_value", minWidth: 150, flex: 1 },
    { headerName: "Sub-Category", field: "sub_catagory_value", minWidth: 150, flex: 1 },
    { headerName: "Quantity", field: "qty", minWidth: col_width, flex: 1 },
    { headerName: "UOM", field: "uom_value", minWidth: 70, flex: 1 },
    { headerName: "Purchase Value", field: "purchase_value", minWidth: col_width, flex: 1 },
    { headerName: "Location Code", field: "location_code_value", minWidth: col_width, flex: 1 },
    { headerName: "Location Floor", field: "location_floor_value", minWidth: col_width, flex: 1 },
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
    overlayNoRowsTemplate: '<span class="text-bold">No result found.</span>',
    pagination: true,
    paginationPageSize: 1000,
    paginationPageSizeSelector: [1000,2000,3000,5000,10000],
    rowModelType: 'infinite',
    cacheBlockSize: 10,
    animateRows: true,
    // getRowClass: function (params) {
    //   return params.node.rowIndex % 2 === 0 ? 'ag-row-even' : 'ag-row-odd';
    // },
    rowClassRules: {
      'ag-row-even': (params) => params.node.rowIndex % 2 === 0,
      'ag-row-odd': (params) => params.node.rowIndex % 2 !== 0
    },
    onGridReady: (params) => {
      // params.api.sizeColumnsToFit();
      params.api.sizeColumnsToFit();
      gridApi_dash = params.api;
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

  // Export Excel
  document.getElementById('exportExcel').addEventListener('click', function (e) {
    e.preventDefault();
    exportAllRows(module);
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


// end code added by ptpatel on date 08-04-25

//end code for exportall inventory ageing on date 07-06-25