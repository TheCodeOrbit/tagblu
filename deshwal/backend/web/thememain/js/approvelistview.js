let currentPage = 0; 

// Function to load table data
function loadTableData(page = 0) {
    currentPage = page;
    let url = 'approvelist?pageNumber=' + page; // Ensure correct page query
    startLoading();
    $.ajax({
        url: url,
        type: "GET",
        dataType: "json",
        success: function(data) {
            let container = document.getElementById('table-container');
            container.innerHTML = ''; // Clear old content
            renderTable(data.result, container, currentPage);
            stopLoading();
        },
        error: function(error) {
            console.error("Error fetching row data:", error);
            stopLoading();
        }
    });
}

// Function to render a single table
// Function to render table for lead information
function renderTable(result, container, currentPage) {
    // Filter columns where 'visible' is true
    let visibleColumns = result.columns.filter(col => col.visible);

    // Generate table headers dynamically
    let tableHtml = `
        <div class="card">
            <h5>${result.leadInformation.length} Records Found</h5>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="thead-light">
                        <tr>${visibleColumns.map(col => `<th>${col.headerName}</th>`).join('')}</tr>
                    </thead>
                    <tbody>
                        ${result.leadInformation.length > 0 
                            ? result.leadInformation.map(record => `
                                <tr>${visibleColumns.map(col => `
                                    <td>
                                        <a href='${approvelistdetailUrl}?Record=${record.RecordId}' target='_blank'>${record[col.field] || ""}</a>
                                    </td>
                                `).join('')}</tr>
                            `).join('') 
                            : `<tr><td colspan="${visibleColumns.length}" class="text-center">No Record Found.</td></tr>`}
                    </tbody>
                </table>
                <div class="pagination-in-center">
                    <ul class="pagination" id="pagination"></ul>
                </div>
            </div>
        </div>`;

    container.innerHTML = tableHtml;
    updatePagination(result.totalitemcount.noofpages, currentPage);
}



// Function to update pagination dynamically
function updatePagination(totalPages, currentPage) {
    let pagination = document.getElementById('pagination');
    pagination.innerHTML = ''; // Clear previous pagination

    if (totalPages <= 1) return; // Hide pagination if only one page

    for (let i = 0; i < totalPages; i++) {
        let pageItem = document.createElement('li');
        pageItem.classList.add('page-item');
        if (i === currentPage) {
            pageItem.classList.add('active');
        }

        let pageLink = document.createElement('a');
        pageLink.classList.add('page-link');
        pageLink.href = "#";
        pageLink.textContent = i + 1;

        pageLink.addEventListener('click', function (event) {
            event.preventDefault();
            loadTableData(i);
        });

        pageItem.appendChild(pageLink);
        pagination.appendChild(pageItem);
    }
}

// Load data on page load
document.addEventListener('DOMContentLoaded', function() {
    loadTableData(0);
});
