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

  /** --------------------------------------------
   *  REPLACED dynamic loadDynamicColumns()
   *  WITH STATIC COLUMN DEFINITIONS
   * --------------------------------------------- */
  const columnDefs_dash = [
    { headerName: "Sourcing Deal No", field: "sourcing_deal_no", sortable: true },
    { headerName: "Sourcing Deal Owner", field: "sourcing_deal_owner", sortable: true },
    { headerName: "Sourcing Deal Name", field: "sourcing_deal_name", sortable: true },
    { headerName: "Closure Date", field: "closure_date", sortable: true },
    { headerName: "Closure Month", field: "closure_month", sortable: true },
    { headerName: "Closure Week", field: "closure_week", sortable: true },
    { headerName: "Commit Date", field: "commit_date", sortable: true },
    { headerName: "Commit Month", field: "commit_month", sortable: true },
    { headerName: "Commit Week", field: "commit_week", sortable: true },
    { headerName: "Account Name", field: "account_name", sortable: true },
    { headerName: "Account Code", field: "account_code", sortable: true },
    { headerName: "Business Type", field: "business_type", sortable: true },
    { headerName: "Contact Name", field: "contact_name", sortable: true },
    { headerName: "Contact Email", field: "contact_email", sortable: true },
    { headerName: "Role", field: "role", sortable: true },
    { headerName: "Designation", field: "designation", sortable: true },
    { headerName: "Department", field: "department", sortable: true },
    { headerName: "Contact Mobile", field: "contact_mobile", sortable: true },
    { headerName: "Sourcing Deal Tentative Value", field: "sourcing_deal_tentative_value", sortable: true },
    { headerName: "Stage", field: "stage", sortable: true },
    { headerName: "Lost Reason", field: "lost_reason", sortable: true },
    { headerName: "Remarks", field: "remarks", sortable: true },
    { headerName: "Payment Type", field: "payment_type", sortable: true },
    { headerName: "Forecast Category", field: "forecast_category", sortable: true },
    { headerName: "Category", field: "category", sortable: true },
    { headerName: "Pickup Request Id", field: "pickup_request_id", sortable: true },
    { headerName: "Currency", field: "currency", sortable: true },
    { headerName: "Exchange Rate", field: "exchange_rate", sortable: true },
    { headerName: "Terms and Conditions", field: "terms_and_conditions", sortable: true },
    { headerName: "IsContract", field: "iscontract", sortable: true },
    { headerName: "Type of Contract", field: "type_of_contract", sortable: true },
    { headerName: "Lead Source", field: "lead_source", sortable: true },
    { headerName: "OEM", field: "oem", sortable: true },
    { headerName: "OEM Manager", field: "oem_manager", sortable: true },
    { headerName: "OEM Manager Name", field: "oem_manager_name", sortable: true },
    { headerName: "OEM Manager Email", field: "oem_manager_email", sortable: true },
    { headerName: "Opportunity Score", field: "opportunity_score", sortable: true },
    { headerName: "Campaign Source", field: "campaign_source", sortable: true },
    { headerName: "Probability", field: "probability", sortable: true },
    { headerName: "Pricing Type", field: "pricing_type", sortable: true },
    { headerName: "Inspection Required", field: "inspection_required", sortable: true },
    { headerName: "Submit Special Pricing", field: "submit_special_pricing", sortable: true },
    { headerName: "Submit For Pricing", field: "submit_for_pricing", sortable: true },
    { headerName: "Costing Done", field: "costing_done", sortable: true },
    { headerName: "CEO Approval", field: "ceo_approval", sortable: true },
    { headerName: "Created BY", field: "created_by", sortable: true },
    { headerName: "Last Modified By", field: "last_modified_by", sortable: true },
    { headerName: "Created Time", field: "created_time", sortable: true },
    { headerName: "Modified Time", field: "modified_time", sortable: true },
    { headerName: "Total Sourcng Deal Amount", field: "total_sourcing_deal_amount", sortable: true },
    { headerName: "Total Sourcing Deal Cost", field: "total_sourcing_deal_cost", sortable: true },
    { headerName: "Total Sourcing Deal Sale", field: "total_sourcing_deal_sale", sortable: true },
    { headerName: "Service Sale", field: "service_sale", sortable: true },
    { headerName: "Service Cost", field: "service_cost", sortable: true },
    { headerName: "Product Cost", field: "product_cost", sortable: true },
    { headerName: "Product Sale", field: "product_sale", sortable: true },
    { headerName: "Margin", field: "margin", sortable: true },
    { headerName: "Margin%", field: "margin_percentage", sortable: true },
    { headerName: "Deshwal ISR", field: "deshwal_isr", sortable: true },
    { headerName: "Account Manager", field: "account_manager", sortable: true }
];


  const gridOptions_dash = {
    columnDefs: columnDefs_dash,
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

        if(window.fromDate == '' && window.toDate == '' && window.accname == '')
        {
          window.accname = document.getElementById('acc_dropdown').value;
          window.fromDate = document.getElementById('from_date').value;
          window.toDate = document.getElementById('to_date').value;
        }
        else{
          if (window.fromDate) url += `&from_date=${encodeURIComponent(window.fromDate)}`;
          if (window.toDate) url += `&to_date=${encodeURIComponent(window.toDate)}`;
          if (window.accname) url += `&accname=${encodeURIComponent(window.accname)}`;
        }

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

  // Apply Filter
  document.getElementById('filterRecords').addEventListener('click', function () {
    window.accname = document.getElementById('acc_dropdown').value || '';
    window.fromDate = document.getElementById('from_date').value || '';
    window.toDate = document.getElementById('to_date').value || '';

    const fromDate = new Date(window.fromDate);
    const toDate = new Date(window.toDate);

    if (window.fromDate && window.toDate && fromDate > toDate) {
      alert("From Date cannot be greater than To Date!");
      window.toDate = "";
      $("#toDate").focus();
      return false;
    }

    if (window.gridApi_dash) {
      window.gridApi_dash.purgeInfiniteCache();
    }
  });

  // Export Excel
  document.getElementById('exportExcel').addEventListener('click', function (e) {
    e.preventDefault();
    let moduleName = 'sourcingdeal';
    exportAllRows(moduleName);
  });

  stopLoading();
  
$(document).on("click", "#refresh-icon", function () {
  console.log("refresh call");
  window.fromDate = window.toDate = window.user = window.activity = '';
  $("#from_date").val("");
  $("#to_date").val("");
  $("#acc_dropdown").val("").trigger('change');

  if (window.gridApi_dash) {
    startLoading();
    window.gridApi_dash.purgeInfiniteCache();
    stopLoading();
  }
});

});
