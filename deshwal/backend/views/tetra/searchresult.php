<?php
error_reporting(-1);
ini_set("display_errors", true);

use backend\assets\AdminAsset;
use yii\helpers\Url;

AdminAsset::register($this);

$csrfTokenName = Yii::$app->request->csrfParam;
$csrfToken = Yii::$app->request->csrfToken;

$baseUrl = Yii::$app->HomeUrl;
$scriptPath = $baseUrl . "js/$ModuleName/edit.js";
$fullurl = Yii::$app->request->getUrl();

?>

<?php
// $this->registerJsFile('@web/thememain/js/searchmodule.js', ['depends' => [yii\web\JqueryAsset::class]]);
$this->registerCss("
    .pagination { display: flex; justify-content: center; margin-top: 10px; }
    .pagination li { cursor: pointer; padding: 8px 12px; border: 1px solid #ddd; margin: 2px; }
    .pagination li.active { background-color: #007bff; color: white; }
    a,a:hover{color:#495057;}
    .pagination li {cursor: pointer;padding:0 !important;border:0 !important;margin:0 !important;}
    .page-item.active .page-link{background-color:#007bff;
    border-color: #007bff;}
");
?>

<div class="container">
    <?php //echo "<pre>";print_r($result);die; 
    ?>
    <span class="card" id="searchresult"></span>
    <div id="table-container"></div>
</div>


<script>
    // let currentPage = 0;
    // let paginationData = {};

    // // Function to load all module tables
    // function loadTableData(page) {
    //     currentPage = page === 0 ? page : page - 1;
    //     let searchQuery = "<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>";
    //     let selectedmodule = "<?php echo isset($_GET['selectedmodule']) ? $_GET['selectedmodule'] : 'all'; ?>";
    //     let tabid = "<?php echo isset($_GET['tabid']) ? $_GET['tabid'] : 'all'; ?>";
    //     let url = '<?php echo Url::to(['searchinallmodule']) ?>' +
    //         '?search=' + encodeURIComponent(searchQuery) +
    //         '&selectedmodule=' + encodeURIComponent(selectedmodule) +
    //         '&tabid=' + encodeURIComponent(tabid) +
    //         '&pageNumber=' + currentPage;

    //     console.log("Loading all modules: ", url);
    //     startLoading(); // Get selected module name
    //     if (searchQuery != '') {
    //         $("#searchresult").text(`Search Result for keyword ` + searchQuery + ` in ` + selectedmodule + ` module`);
    //     }
    //     $.ajax({
    //         url: url,
    //         type: "GET",
    //         dataType: "json",
    //         success: function(data) {
    //             let container = document.getElementById('table-container');
    //             container.innerHTML = '';

    //             if (data.search === 'single') {
    //                 renderSingleModuleTable(data.result, container, currentPage, searchQuery);
    //             } else {
    //                 data.result.forEach(module => {
    //                     renderModuleTable(module, container, currentPage, searchQuery);
    //                 });
    //             }
    //             stopLoading();
    //         },
    //         error: function(error) {
    //             console.error("Error fetching row data:", error);
    //             stopLoading();
    //         }
    //     });
    // }

    // // Function to load a single module table with pagination
    // function loadsingleTableData(page, moduleName, tabid, searchQuery) {
    //     console.log(`Loading single table: ${moduleName}, Page: ${page}, TabID: ${tabid}, Search: ${searchQuery}`);

    //     let currentPageKey = `currentPage_${moduleName}_${tabid}`;
    //     paginationData[currentPageKey] = page === 0 ? page : page - 1;

    //     let url = '<?php echo Url::to(['searchinallmodule']) ?>' +
    //         '?search=' + encodeURIComponent(searchQuery) +
    //         '&selectedmodule=' + encodeURIComponent(moduleName) +
    //         '&tabid=' + encodeURIComponent(tabid) +
    //         '&pageNumber=' + paginationData[currentPageKey];

    //     startLoading();

    //     $.ajax({
    //         url: url,
    //         type: "GET",
    //         dataType: "json",
    //         success: function(data) {
    //             let cardContainer = document.querySelector('.card_' + moduleName);
    //             if (cardContainer) {
    //                 cardContainer.innerHTML = '';
    //                 renderSingleModuleTable(data.result, cardContainer, paginationData[currentPageKey], searchQuery);
    //             }
    //             stopLoading();
    //         },
    //         error: function(error) {
    //             console.error("Error fetching row data:", error);
    //             stopLoading();
    //         }
    //     });
    // }

    // // Function to render a single module's table
    // function renderSingleModuleTable(result, container, currentPage, searchQuery) {
    //     let moduleName = result.modulename;
    //     let tabid = result.tabid;
    //     paginationData[`currentPage_${moduleName}_${tabid}`] = currentPage;

    //     let tableHtml = `
    // <div class="card card_${moduleName}">
    //     <h5 >${moduleName} - ${result.totalitemcount.totrecords}</h5>
    //     <div class="table-responsive">
    //         <table class="table table-bordered">
    //             <thead class="thead-light">
    //                 <tr>${Object.values(result.Column).map(value => `<th>${value}</th>`).join('')}</tr>
    //             </thead>
    //             <tbody>
    //                 ${result.RecordList.length > 0 ? result.RecordList.map(record => `
    //                     <tr>${Object.keys(result.Column).map(key => `
    //                         <td><a href='<?= Url::to(['detail']) ?>?Record=${record.RecordId}' target='_blank'>${record[key] || ""}</a></td>
    //                     `).join('')}</tr>
    //                 `).join('') : `<tr><td colspan="${Object.keys(result.Column).length}" class="text-center">No Record Found.</td></tr>`}
    //             </tbody>
    //         </table>
    //         <ul class="pagination" id="pagination_${moduleName}" data-tabid="${tabid}" data-search="${searchQuery}"></ul>
    //     </div>
    // </div>`;

    //     container.innerHTML = tableHtml;
    //     updatePagination(result.totalitemcount.noofpages, currentPage, moduleName, tabid, searchQuery);
    // }

    // // Function to render all modules' tables
    // function renderModuleTable(module, container, currentPage, searchQuery) {
    //     let moduleName = module.modulename;
    //     let tabid = module.tabid;
    //     paginationData[`currentPage_${moduleName}_${tabid}`] = currentPage;

    //     let tableHtml = `
    // <div class=" card card_${moduleName}">
    //     <h5 class="mb-3">${moduleName} - ${module.totalitemcount.totrecords}</h5>
    //     <div class="table-responsive">
    //         <table class="table table-bordered">
    //             <thead class="thead-light">
    //                 <tr>${Object.values(module.Column).map(columnName => `<th>${columnName}</th>`).join('')}</tr>
    //             </thead>
    //             <tbody>
    //                 ${module.RecordList.length > 0 ? module.RecordList.map(record => `
    //                     <tr>${Object.keys(module.Column).map(key => `
    //                         <td><a href='<?= Url::to(['detail']) ?>?Record=${record.RecordId}' target='_blank'>${record[key] || ""}</a></td>
    //                     `).join('')}</tr>
    //                 `).join('') : `<tr><td colspan="${Object.keys(module.Column).length}" class="text-center">No Record Found.</td></tr>`}
    //             </tbody>
    //         </table>
    //         <ul class="pagination" id="pagination_${moduleName}" data-tabid="${tabid}" data-search="${searchQuery}"></ul>
    //     </div>
    // </div>`;

    //     container.innerHTML += tableHtml;
    //     updatePagination(module.totalitemcount.noofpages, currentPage, moduleName, tabid, searchQuery);
    // }

    // // Function to update pagination dynamically
    // function updatePagination(totalPages, currentPage, moduleName, tabid, searchQuery) {
    //     let pagination = document.getElementById('pagination_' + moduleName);
    //     if (!pagination) return;

    //     pagination.innerHTML = '';
    //     pagination.setAttribute("data-tabid", tabid);
    //     pagination.setAttribute("data-search", searchQuery);

    //     if (totalPages <= 1) return;

    //     for (let i = 1; i <= totalPages; i++) {
    //         let pageItem = document.createElement('li');
    //         pageItem.classList.add('page-item');
    //         if (i - 1 === currentPage) {
    //             pageItem.classList.add('active');
    //         }

    //         let pageLink = document.createElement('a');
    //         pageLink.classList.add('page-link');
    //         pageLink.href = "#";
    //         pageLink.textContent = i;
    //         pageLink.setAttribute("data-module", moduleName);
    //         pageLink.setAttribute("data-tabid", tabid);
    //         pageLink.setAttribute("data-search", searchQuery);

    //         pageItem.appendChild(pageLink);
    //         pagination.appendChild(pageItem);
    //     }
    // }

    // // Event delegation for pagination click handling
    // document.addEventListener('click', function(e) {
    //     if (e.target.classList.contains('page-link')) {
    //         e.preventDefault();
    //         let moduleName = e.target.getAttribute("data-module");
    //         let tabid = e.target.getAttribute("data-tabid");
    //         let searchQuery = e.target.getAttribute("data-search");
    //         let page = parseInt(e.target.textContent);

    //         loadsingleTableData(page, moduleName, tabid, searchQuery);
    //     }
    // });

    // // Load data on page load
    // document.addEventListener('DOMContentLoaded', function() {
    //     loadTableData(0);
    // });
</script>