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
    var searchResults = document.getElementById('search-results-dropdown');
    var searchInput = document.getElementById('searchinallmodule');

    if (dropdown && !dropdown.contains(event.target) && toggle && !toggle.contains(event.target)) {
        dropdown.style.display = 'none';
    }
    
    if (searchResults && !searchResults.contains(event.target) && searchInput && !searchInput.contains(event.target)) {
        searchResults.style.display = 'none';
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
// Global variables for search
let searchTimeout = null;
let currentSuggestion = "";
let activeResultIndex = -1;
let searchCache = {}; // Cache for instant suggestions

$(document).on("input", "#searchinallmodule", function(e) {
    const query = $(this).val();
    const predictiveOverlay = $("#predictive-suggestion");
    const dropdown = $("#search-results-dropdown");
    
    // Clear previous timeout
    if (searchTimeout) clearTimeout(searchTimeout);
    
    // Check local cache for instant feedback
    if (searchCache[query]) {
        renderSearchDropdown(searchCache[query], query);
        handlePredictiveSuggestion(searchCache[query], query);
    } else {
        // Show loading state if not in cache
        if (!dropdown.is(':visible')) {
            dropdown.html('<div class="search-loading-state"><div class="search-spinner"></div><span>Searching...</span></div>').show();
        }
    }

    // Set a debounce timeout
    searchTimeout = setTimeout(() => {
        performRealTimeSearch(query);
    }, 150);
});

$(document).on("submit", "#searchForminallmodule", function(e) {
    e.preventDefault(); // Prevent old results page
    const query = $("#searchinallmodule").val();
    if (query.length >= 2) {
        performRealTimeSearch(query);
    }
});

$(document).on("keydown", "#searchinallmodule", function(e) {
    const dropdown = $("#search-results-dropdown");
    const results = dropdown.find(".search-result-item");
    
    if (e.key === "Tab" || e.key === "ArrowRight") {
        if (currentSuggestion && $(this).val().length < currentSuggestion.length) {
            e.preventDefault();
            $(this).val(currentSuggestion);
            $("#predictive-suggestion").text("");
        }
    } else if (e.key === "ArrowDown") {
        e.preventDefault();
        if (results.length > 0) {
            activeResultIndex = (activeResultIndex + 1) % results.length;
            updateActiveResult(results);
        }
    } else if (e.key === "ArrowUp") {
        e.preventDefault();
        if (results.length > 0) {
            activeResultIndex = (activeResultIndex - 1 + results.length) % results.length;
            updateActiveResult(results);
        }
    } else if (e.key === "Enter") {
        if (activeResultIndex >= 0) {
            e.preventDefault();
            results.eq(activeResultIndex).click();
        }
    } else if (e.key === "Escape") {
        dropdown.hide();
    }
});

function updateActiveResult(results) {
    results.removeClass("active");
    const activeItem = results.eq(activeResultIndex);
    activeItem.addClass("active");
    
    // Scroll into view if needed
    const dropdown = $("#search-results-dropdown");
    const itemTop = activeItem.position().top;
    const itemBottom = itemTop + activeItem.outerHeight();
    const dropdownHeight = dropdown.height();
    
    if (itemBottom > dropdownHeight) {
        dropdown.scrollTop(dropdown.scrollTop() + (itemBottom - dropdownHeight));
    } else if (itemTop < 0) {
        dropdown.scrollTop(dropdown.scrollTop() + itemTop);
    }
}

function performRealTimeSearch(query) {
    const selectedModule = $("#selectedmodule").val() || 'all';
    const tabid = $("#selectedid").val() || 'all';
    const baseUrl = window.APP_BASE_URL || '/';
    
    // Use leads module as the base for global search action
    const url = baseUrl + 'leads/searchinallmodule' +
        '?search=' + encodeURIComponent(query) +
        '&selectedmodule=' + encodeURIComponent(selectedModule) +
        '&tabid=' + encodeURIComponent(tabid) +
        '&limit=5' + 
        '&mode=quick'; // Request optimized quick results

    $.ajax({
        url: url,
        type: "GET",
        dataType: "json",
        success: function(data) {
            searchCache[query] = data; // Save to local cache
            renderSearchDropdown(data, query);
            handlePredictiveSuggestion(data, query);
        },
        error: function() {
            // Fallback to site/searchinallmodule if leads fails
            const fallbackUrl = baseUrl + 'site/searchinallmodule' +
                '?search=' + encodeURIComponent(query) +
                '&selectedmodule=' + encodeURIComponent(selectedModule) +
                '&tabid=' + encodeURIComponent(tabid) +
                '&limit=5' +
                '&mode=quick';
            $.ajax({
                url: fallbackUrl,
                type: "GET",
                dataType: "json",
                success: function(data) {
                    renderSearchDropdown(data, query);
                    handlePredictiveSuggestion(data, query);
                }
            });
        }
    });
}

function handlePredictiveSuggestion(data, query) {
    const predictiveOverlay = $("#predictive-suggestion");
    let bestSuggestion = "";

    if (data.status === 'success' && data.result) {
        let allRecords = [];
        if (data.search === 'single') {
            allRecords = data.result.RecordList;
        } else {
            data.result.forEach(m => {
                if (m.RecordList) allRecords = allRecords.concat(m.RecordList);
            });
        }

        // Find best suggestion (starts with query, and is shortest among matches)
        let matches = [];
        allRecords.forEach(record => {
            for (let key in record) {
                if (key === 'RecordId' || typeof record[key] !== 'string') continue;
                let val = record[key].trim();
                if (val.toLowerCase().startsWith(query.toLowerCase()) && val.length > query.length) {
                    matches.push(val);
                }
            }
        });

        if (matches.length > 0) {
            matches.sort((a, b) => a.length - b.length);
            bestSuggestion = matches[0];
        }
    }

    if (bestSuggestion) {
        currentSuggestion = bestSuggestion;
        const typedPart = query;
        const suggestedPart = bestSuggestion.substring(query.length);
        // Use a span for the typed part to maintain exact spacing
        predictiveOverlay.html(`<span style="opacity: 0;">${typedPart}</span>${suggestedPart}`);
    } else {
        predictiveOverlay.text("");
        currentSuggestion = "";
    }
}

function renderSearchDropdown(data, query) {
    const dropdown = $("#search-results-dropdown");
    dropdown.empty();
    activeResultIndex = -1;

    if (!data.result || (data.search === 'all' && data.result.length === 0) || (data.search === 'single' && data.result.RecordList.length === 0)) {
        dropdown.html('<div class="search-no-results"><i class="fa fa-search"></i>No results found for "' + query + '"</div>');
        dropdown.show();
        return;
    }

    if (data.search === 'single') {
        renderModuleSection(data.result, dropdown);
    } else {
        data.result.forEach(module => {
            renderModuleSection(module, dropdown);
        });
    }

    // Add "See all results" footer
    const footer = $(`
        <div class="search-dropdown-footer">
            <a href="#" id="see-all-search-results">See all results for "${query}" <i class="fa fa-arrow-right"></i></a>
        </div>
    `);
    
    footer.find('a').on('click', function(e) {
        e.preventDefault();
        // Use native submit to bypass the jQuery e.preventDefault() listener
        const form = document.getElementById("searchForminallmodule");
        if (form) {
            form.submit();
        }
    });

    dropdown.append(footer);
    dropdown.show();
}

function renderModuleSection(module, container) {
    if (!module.RecordList || module.RecordList.length === 0) return;

    const section = $('<div class="search-module-section"></div>');
    section.append('<div class="search-module-header">' + module.modulename + '</div>');

    module.RecordList.forEach(record => {
        const title = findDisplayTitle(record);
        const meta = findDisplayMeta(record, title);
        const baseUrl = window.APP_BASE_URL || '/';
        const url = baseUrl + module.ori_modulename.toLowerCase() + '/detail?Record=' + record.RecordId + '&tabid=' + module.tabid;

        const item = $(`
            <div class="search-result-item" data-url="${url}">
                <div class="search-result-icon">
                    <i class="fa ${getModuleIcon(module.ori_modulename)}"></i>
                </div>
                <div class="search-result-info">
                    <div class="search-result-title">${title}</div>
                    <div class="search-result-meta">${meta}</div>
                </div>
            </div>
        `);

        item.on("click", function() {
            window.location.href = $(this).data("url");
        });

        section.append(item);
    });

    container.append(section);
}

function findDisplayTitle(record) {
    // Try to find a name-like field
    const nameKeys = ['leadname', 'subject', 'accountname', 'vendorname', 'productname', 'title', 'name', 'first_name'];
    for (let key of nameKeys) {
        if (record[key]) return record[key];
    }
    // Fallback to first non-RecordId field
    for (let key in record) {
        if (key !== 'RecordId' && record[key]) return record[key];
    }
    return "Untitled Record";
}

function findDisplayMeta(record, excludeTitle) {
    const metaKeys = ['email', 'phone', 'mobile', 'status', 'createdtime', 'modifiedtime'];
    let meta = [];
    for (let key of metaKeys) {
        if (record[key] && record[key] !== excludeTitle) {
            meta.push(record[key]);
            if (meta.length >= 2) break;
        }
    }
    return meta.join(' • ');
}

function getModuleIcon(moduleName) {
    const icons = {
        'Leads': 'fa-user-plus',
        'Accounts': 'fa-building',
        'Contacts': 'fa-address-book',
        'Opportunities': 'fa-briefcase',
        'Products': 'fa-box',
        'Tasks': 'fa-tasks',
        'Meetings': 'fa-calendar',
        'Quotes': 'fa-file-invoice-dollar',
        'Invoices': 'fa-file-invoice',
        'SalesOrder': 'fa-shopping-cart',
        'PurchaseOrder': 'fa-shopping-bag',
        'Vendors': 'fa-truck'
    };
    return icons[moduleName] || 'fa-file';
}

$(document).on("click", "#search_btn", function(e) {
    console.log("search_btn clicked");
    e.preventDefault();
    var searchValue = $("#searchinallmodule").val().trim();
    if (searchValue === "") {
        alert("Search input cannot be blank");
        return false;
    } else {
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
        // paginationData[currentPageKey] = page === 0 ? page : page - 1;
        paginationData[currentPageKey] = page;
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
                let cardContainer = document.querySelector('.card_' + tabid);
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
    <div class="card card_${tabid}">
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
            <ul class="pagination" id="pagination_${tabid}" data-tabid="${tabid}" data-search="${searchQuery}"></ul>
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
    <div class=" card card_${tabid}">
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
            <ul class="pagination" id="pagination_${tabid}" data-tabid="${tabid}" data-search="${searchQuery}"></ul>
        </div>
    </div>`;

        container.innerHTML += tableHtml;
        updatePagination(module.totalitemcount.noofpages, currentPage, moduleName, tabid, searchQuery);
    }

    // Function to update pagination dynamically
    /*function updatePagination(totalPages, currentPage, moduleName, tabid, searchQuery) {
        let pagination = document.getElementById('pagination_' + tabid);
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
    }*/
    function updatePagination(totalPages, currentPage, moduleName, tabid, searchQuery) {
        const pagination = document.getElementById('pagination_' + tabid);
        if (!pagination) return;

        pagination.innerHTML = '';
        pagination.dataset.tabid = tabid;
        pagination.dataset.search = searchQuery;

        if (totalPages <= 1) return;

        const maxVisible = 10;
        const half = Math.floor(maxVisible / 2);

        let startPage = Math.max(0, currentPage - half);
        let endPage = startPage + maxVisible - 1;

        if (endPage >= totalPages) {
            endPage = totalPages - 1;
            startPage = Math.max(0, endPage - maxVisible + 1);
        }

        // ⏮ First
        pagination.appendChild(
            createPageItem('⏮', 0, moduleName, tabid, searchQuery, currentPage === 0)
        );

        // ‹ Previous
        pagination.appendChild(
            createPageItem('<', Math.max(currentPage - 1, 0), moduleName, tabid, searchQuery, currentPage === 0)
        );

        // 🔵 Page Numbers
        for (let i = startPage; i <= endPage; i++) {
            pagination.appendChild(
                createPageItem(
                    i + 1,
                    i,
                    moduleName,
                    tabid,
                    searchQuery,
                    false
                )
            );

            // add active class ONLY here
            if (i === currentPage) {
                pagination.lastChild.classList.add('active');
            }
        }

        // › Next
        pagination.appendChild(
            createPageItem('>', Math.min(currentPage + 1, totalPages - 1), moduleName, tabid, searchQuery, currentPage === totalPages - 1)
        );

        // ⏭ Last
        pagination.appendChild(
            createPageItem('⏭', totalPages - 1, moduleName, tabid, searchQuery, currentPage === totalPages - 1)
        );
    }


    function createPageItem(label, pageIndex, moduleName, tabid, searchQuery, isDisabled = false) {
        const li = document.createElement('li');
        li.className = 'page-item' + (isDisabled ? ' disabled' : '');

        const a = document.createElement('a');
        a.className = 'page-link';
        a.href = '#';
        a.textContent = label;

        a.dataset.page = pageIndex;

        a.dataset.module = moduleName;
        a.dataset.tabid = tabid;
        a.dataset.search = searchQuery;

        if (isDisabled) {
            a.tabIndex = -1;
            a.style.pointerEvents = 'none';
        }

        li.appendChild(a);
        return li;
    }



    // Event delegation for pagination click handling
    document.addEventListener('click', function(e) {
        const link = e.target.closest('.page-link');
        if (!link) return;

        e.preventDefault();

        //  Convert to 0-based internally
        const page = parseInt(link.dataset.page, 10);
        if (isNaN(page)) return;

        loadsingleTableData(
            page,
            link.dataset.module,
            link.dataset.tabid,
            link.dataset.search
        );
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