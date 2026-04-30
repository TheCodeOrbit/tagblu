 // code added by ptpatel date 26-03-
 document.getElementById('moduleDropdownToggle').addEventListener('click', function(event) {
    // event.stopPropagation(); // Prevents the click from propagating to the document
    var dropdown = document.getElementById('moduleDropdown_search');
    // Toggle dropdown visibility
    if (dropdown.style.display === 'block') {
        dropdown.style.display = 'none';
    } else {
        dropdown.style.display = 'block';
    }
});

// Hide dropdown when clicking outside
document.addEventListener('click', function(event) {
    var dropdown = document.getElementById('moduleDropdown_search');
    var toggle = document.getElementById('moduleDropdownToggle');

    if (!dropdown.contains(event.target) && !toggle.contains(event.target)) {
        dropdown.style.display = 'none';
    }
});

// Handle selection and update display text
document.querySelectorAll(".module-option").forEach(li => {
    li.addEventListener("click", function() {
        // Remove previous selection
        document.querySelectorAll(".module-option").forEach(el => el.classList.remove("liselected"));
        this.classList.add("liselected");

        // Update selected text in the dropdown label
        let selectedText = this.innerText.trim();
        let selectedValue = this.getAttribute("data-value");
        let tabid = this.getAttribute('id') ? this.getAttribute('id') : 'all';
        // alert(tabid);
        document.getElementById('selectedModuleText').innerText = selectedText;
        document.getElementById('selectedmodule').value = selectedValue;
        document.getElementById('selectedid').value = tabid;

        // Hide dropdown after selection
        document.getElementById('moduleDropdown').style.display = 'none';
    });
});
// $("#search_btn").on("click",function(e){
$(document).on("click","#search_btn",function(e){
    console.log("search_btn clicked");
    e.preventDefault();
    var searchValue = $("#searchinallmodule").val().trim();
    if(searchValue === ""){
      alert("Search input cannot be blank");
      return false;
    }
    else{
      $("#searchForminallmodule").submit(); 
    }
  
  });
   //end code added by ptpatel date 26-03-25

//    code added on date 02-07-25 to resolve staging issue

    // let currentPage = currentPage || 0;
    // let paginationData =  paginationData || {};;
    if (typeof window.paginationData === 'undefined') {
        window.paginationData = {};
    }

    // Function to load all module tables
    function loadTableData(page) {
        currentPage = page === 0 ? page : page - 1;
        // let searchQuery = "<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>";
        // let selectedmodule = "<?php echo isset($_GET['selectedmodule']) ? $_GET['selectedmodule'] : 'all'; ?>";
        // let tabid = "<?php echo isset($_GET['tabid']) ? $_GET['tabid'] : 'all'; ?>";
        // let url = '<?php echo Url::to(['searchinallmodule']) ?>' +
        let urlParams = new URLSearchParams(window.location.search);
        let searchQuery = urlParams.get('search') || '';
        let selectedmodule = urlParams.get('selectedmodule') || 'all';
        let tabid = urlParams.get('tabid') || 'all';
        let baseUrl = window.location.pathname;
        let url = baseUrl +
            '?search=' + encodeURIComponent(searchQuery) +
            '&selectedmodule=' + encodeURIComponent(selectedmodule) +
            '&tabid=' + encodeURIComponent(tabid) +
            '&pageNumber=' + currentPage;

        console.log("Loading all modules: ", url);
        startLoading(); // Get selected module name
        if (searchQuery != '') {
            $("#searchresult").text(`Search Result for keyword ` + searchQuery + ` in ` + selectedmodule + ` module`);
        }
         if (searchQuery != '') {
                $.ajax({
                    url: url,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        console.log("data.result"+data.result);
                        let container = document.getElementById('table-container');
                        container.innerHTML = '';
                        if(data.result == '')
                        {
                           $("#searchresult").append(`<br> No Records Found.`);
                        }
                        else if (data.search === 'single') {
                            renderSingleModuleTable(data.result, container, currentPage, searchQuery);
                        } else {
                            data.result.forEach(module => {
                                renderModuleTable(module, container, currentPage, searchQuery);
                            });
                        }
                        stopLoading();
                    },
                    error: function(error) {
                        console.error("Error fetching row data:", error);
                        stopLoading();
                    }
                });
            }
            else
            {stopLoading();}
    }

    // Function to load a single module table with pagination
    function loadsingleTableData(page, moduleName, tabid, searchQuery) {
        console.log(`Loading single table: ${moduleName}, Page: ${page}, TabID: ${tabid}, Search: ${searchQuery}`);

        let currentPageKey = `currentPage_${moduleName}_${tabid}`;
        paginationData[currentPageKey] = page === 0 ? page : page - 1;
        let urlParams = new URLSearchParams(window.location.search);
        let baseUrl = window.location.pathname;
        let url = baseUrl +
            '?search=' + encodeURIComponent(searchQuery) +
            '&selectedmodule=' + encodeURIComponent(moduleName) +
            '&tabid=' + encodeURIComponent(tabid) +
            '&pageNumber=' + paginationData[currentPageKey];

        startLoading();

        $.ajax({
            url: url,
            type: "GET",
            dataType: "json",
            success: function(data) {
                let cardContainer = document.querySelector('.card_' + moduleName);
                if (cardContainer) {
                    cardContainer.innerHTML = '';
                    renderSingleModuleTable(data.result, cardContainer, paginationData[currentPageKey], searchQuery);
                }
                stopLoading();
            },
            error: function(error) {
                console.error("Error fetching row data:", error);
                stopLoading();
            }
        });
    }

    // Function to render a single module's table
    function renderSingleModuleTable(result, container, currentPage, searchQuery) {
        let urlParams = new URLSearchParams(window.location.search);
        let selectedModule = urlParams.get('selectedmodule');
        // Remove everything after and including 'searchinallmodule'
        let basePath = window.location.href.split('searchinallmodule')[0];
        let moduleName = result.modulename;
        let ori_modulename = result.ori_modulename;
        let tabid = result.tabid;
        paginationData[`currentPage_${moduleName}_${tabid}`] = currentPage;
         basePath = basePath.replace("leads", ori_modulename);
        let tableHtml = `
    <div class="card card_${moduleName}">
        <h5 >${moduleName} - ${result.totalitemcount.totrecords}</h5>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="thead-light">
                    <tr>${Object.values(result.Column).map(value => `<th>${value}</th>`).join('')}</tr>
                </thead>
                <tbody>
                    ${result.RecordList.length > 0 ? result.RecordList.map(record => `
                        <tr>${Object.keys(result.Column).map(key => `
                            <td><a href='${basePath}detail?Record=${record.RecordId}' target='_blank'>${truncateText(record[key]) || ""}</a></td>
                        `).join('')}</tr>
                    `).join('') : `<tr><td colspan="${Object.keys(result.Column).length}" class="text-center">No Record Found.</td></tr>`}
                </tbody>
            </table>
            <ul class="pagination" id="pagination_${moduleName}" data-tabid="${tabid}" data-search="${searchQuery}"></ul>
        </div>
    </div>`;

        container.innerHTML = tableHtml;
        updatePagination(result.totalitemcount.noofpages, currentPage, moduleName, tabid, searchQuery);
    }

    // Function to render all modules' tables
    function renderModuleTable(module, container, currentPage, searchQuery) {
        let moduleName = module.modulename;
        let tabid = module.tabid;
        let ori_modulename = module.ori_modulename;
        paginationData[`currentPage_${moduleName}_${tabid}`] = currentPage;
        let fullUrl = window.location.href;
        let index = fullUrl.indexOf('/admin/') + '/admin/'.length;
        let basePath = fullUrl.substring(0, index);
        let tableHtml = `
    <div class=" card card_${moduleName}">
        <h5 class="mb-3">${moduleName} - ${module.totalitemcount.totrecords}</h5>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="thead-light">
                    <tr>${Object.values(module.Column).map(columnName => `<th>${columnName}</th>`).join('')}</tr>
                </thead>
                <tbody>
                    ${module.RecordList.length > 0 ? module.RecordList.map(record => `
                        <tr>${Object.keys(module.Column).map(key => `
                            <td><a href='${basePath + ori_modulename}/detail?Record=${record.RecordId}' target='_blank'>${truncateText(record[key]) || ""}</a></td>
                        `).join('')}</tr>
                    `).join('') : `<tr><td colspan="${Object.keys(module.Column).length}" class="text-center">No Record Found.</td></tr>`}
                </tbody>
            </table>
            <ul class="pagination" id="pagination_${moduleName}" data-tabid="${tabid}" data-search="${searchQuery}"></ul>
        </div>
    </div>`;

        container.innerHTML += tableHtml;
        updatePagination(module.totalitemcount.noofpages, currentPage, moduleName, tabid, searchQuery);
    }

    // Function to update pagination dynamically
    function updatePagination(totalPages, currentPage, moduleName, tabid, searchQuery) {
        let pagination = document.getElementById('pagination_' + moduleName);
        if (!pagination) return;

        pagination.innerHTML = '';
        pagination.setAttribute("data-tabid", tabid);
        pagination.setAttribute("data-search", searchQuery);

        if (totalPages <= 1) return;

        for (let i = 1; i <= totalPages; i++) {
            let pageItem = document.createElement('li');
            pageItem.classList.add('page-item');
            if (i - 1 === currentPage) {
                pageItem.classList.add('active');
            }

            let pageLink = document.createElement('a');
            pageLink.classList.add('page-link');
            pageLink.href = "#";
            pageLink.textContent = i;
            pageLink.setAttribute("data-module", moduleName);
            pageLink.setAttribute("data-tabid", tabid);
            pageLink.setAttribute("data-search", searchQuery);

            pageItem.appendChild(pageLink);
            pagination.appendChild(pageItem);
        }
    }

    // Event delegation for pagination click handling
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('page-link')) {
            e.preventDefault();
            let moduleName = e.target.getAttribute("data-module");
            let tabid = e.target.getAttribute("data-tabid");
            let searchQuery = e.target.getAttribute("data-search");
            let page = parseInt(e.target.textContent);

            loadsingleTableData(page, moduleName, tabid, searchQuery);
        }
    });

    // Load data on page loads
    document.addEventListener('DOMContentLoaded', function() {
        loadTableData(0);
    });

    function truncateText(text, maxLength = 100, checkLength = 100) {
    if (!text) return '';
    return text.length > checkLength
        ? text.substring(0, maxLength) + '...'
        : text;
}
// end code added on date 02-07-25 