<?php

use yii\helpers\Url;
use yii\helpers\Html;
use backend\assets\AdminAsset;


AdminAsset::register($this);

$this->title = Yii::t('app', 'Table List');
//Add DataTables CSS CDN
$this->registerCssFile('https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css', ['depends' => [AdminAsset::class]]);
$this->registerCssFile('https://cdn.datatables.net/fixedcolumns/4.2.2/css/fixedColumns.dataTables.min.css', ['depends' => [AdminAsset::class]]);
$this->registerCssFile('https://cdn.datatables.net/fixedheader/3.3.1/css/fixedHeader.dataTables.min.css', ['depends' => [AdminAsset::class]]);
$this->registerCssFile('https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css', ['depends' => [AdminAsset::class]]);
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css', ['depends' => [AdminAsset::class]]);

?>

<style>
  /* Apply a font-family to the entire table */

  #datatable td {
    white-space: nowrap;
    /* Prevents the text from wrapping */
    overflow: hidden;
    /* Hides overflowed text */
    text-overflow: ellipsis;
    /* Adds '...' for clipped text */
    max-width: 160px;
    /* Same width as defined in DataTables */
    border-collapse: collapse;

    font-family: "Poppins", Helvetica;
    font-size: 12px;

  }

  #datatable {
    table-layout: fixed;
    /* Fix the layout of the table */
    width: 160%;
    /* Make sure the table occupies the full width */
    border-collapse: collapse;
    /* Collapse borders */

  }



  table.dataTable>thead>tr>th,
  table.dataTable>thead>tr>td {

    background-color: #CACCCD;
  }

  /* Ensure the table takes full width */
  .table-container {
    width: 100%;
    overflow-x: auto;
  }

  table.dataTable thead th,
  table.dataTable tfoot th {
    font-family: "Poppins";
    font-weight: bold;
    font-size: 14px;
  }


  table.dataTable tbody tr .even {
    background-color: #FFFFFF;
  }

  table.dataTable tbody tr.odd {
    background-color: #F3F2F2;
    /* Change this to your desired color */
  }


  /* Style for the dropdown button */

  /* Container for dropdown */
  .dropdown-tablelist {
    position: relative;
    display: inline-block;
    margin-left: 10px;
  }

  /* Dropdown content (hidden by default) */
  .dropdown-content {
    display: none;
    position: fixed;
    /* Changed to fixed for proper positioning */
    background-color: #f9f9f9;
    min-width: 160px;
    box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
    z-index: 9999;
    /* Ensure it appears above the table */
    right: 0;
    /* Align to the right of the button */
    width: 100px;
    
  }

  /* Links inside the dropdown */
  .dropdown-content a {
    color: black;
    padding: 10px 16px;
    text-decoration: none;
    display: block;
  }

  /* Show the dropdown content on hover */
  .dropdown:hover .dropdown-content {
    display: none;
  }

  .th-container {
    display: flex;
  }



  .kanban-board {
    display: flex;
    gap: 20px;
    padding: 20px;
  }

  .kanban-column {
    background-color: #f9f9f9;
    border: 1px solid #ccc;
    border-radius: 8px;
    width: 300px;
    padding: 15px;
    min-height: 400px;
  }

  .kanban-header {
    background-image: url("<?= Yii::getAlias('@web/images/rectangle64381102-0y0o.svg') ?>");
    background-size: cover;
    /* Adjust as needed */
    background-position: center;
    background-repeat: no-repeat;
    width: 100%;
    /* Adjust as needed */
    height: 100%;
    /* Adjust as needed */
    padding: inherit;
  }

  .kanban-card {
    background-color: white;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 15px;
    margin-top: 10px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    cursor: move;
  }

  .kanban-card h4 {
    margin: 0 0 10px;
  }

  .kanban-card p {
    margin: 0;
    font-size: 0.9em;
  }
</style>


<!-- open lead Add button -->
<div class="group-15">
  <div class="group-16">
    <div class="overlap-group-13">
      <div class="text-wrapper-124">Add</div>
      <div class="text-wrapper-125">+</div>
    </div>
  </div>
  <div class="group-17">
    <div class="overlap-16">
      <div class="text-wrapper-126">Import</div>
      <img class="lets-icons-import" src="https://c.animaapp.com/4Te5O9cu/img/lets-icons-import-light.svg" />
    </div>
  </div>
  <img class="typcn-filter" src="https://c.animaapp.com/4Te5O9cu/img/typcn-filter.svg" />


  <div class="frame-22">
    <div class="group-18">
      <div class="overlap-group-14">
        <img class="vector-67" src="https://c.animaapp.com/4Te5O9cu/img/vector-73-1.svg" />
        <div class="text-wrapper-127">
          <select class="view-selector" id="viewSelector">
            <option value="list">List View</option>
            <option value="kanban">Kanban</option>
          </select>
        </div>
        <img class="ic-round-arrow-left-65" src="https://c.animaapp.com/4Te5O9cu/img/ic-round-arrow-left-68.svg" />
        <img class="tdesign-list" src="https://c.animaapp.com/4Te5O9cu/img/tdesign-list.svg" />
      </div>
    </div>
  </div>

  <img
    class="fluent-column-triple"
    src="https://c.animaapp.com/4Te5O9cu/img/fluent-column-triple-edit-24-regular.svg" />
  <img class="flowbite-refresh" src="https://c.animaapp.com/4Te5O9cu/img/flowbite-refresh-outline.svg" />
  <div class="group-19">
    <div class="all-open-lead">All Open Lead</div>
    <img class="ep-arrow-down-8" src="https://c.animaapp.com/4Te5O9cu/img/ep-arrow-down-26.svg" />
  </div>
</div>

<!-- end open lead Add button  -->
<div class="overlap-4">
  <div class="frame-8">
    <div class="overlap-5">



      <!-- Table -->
      <div class="table-list">
        <table id="datatable" class="table table-bordered dt-responsive ">
          <thead>
            <tr>
              <th>ID</th>

              <th>
                <div class="th-container">
                  <div> First Name</div>
                  <div class="dropdown-tablelist">
                    <span class="table-dropbtn"><i class="fa fa-caret-down"></i></span>
                    <div class="dropdown-content">
                      <a href="#" class="freeze-btn" data-col="0" id="wrap-text">freeze</a>
                      <a href="#" class="unfreeze-btn" data-col="0" id="clip-text">Un-freeez</a>
                      <a href="#" class="wrapBtn" data-col="0" id="unfreeze-all">wrap text</a>
                      <a href="#" class=" clipBtn" data-col="0" id="unfreeze-all">clip text</a>
                    </div>
                  </div>
                </div>

              </th>
              <th>

                <div class="th-container">
                  <div>Last Name</div>
                  <div class="dropdown-tablelist">
                    <span class="table-dropbtn"><i class="fa fa-caret-down"></i></span>
                    <div class="dropdown-content">
                      <a href="#" class="freeze-btn" data-col="1" id="wrap-text">freeze</a>
                      <a href="#" class="unfreeze-btn" data-col="1" id="clip-text">Un-freeez</a>
                      <a href="#" class="wrapBtn" data-col="1" id="unfreeze-all">wrap text</a>
                      <a href="#" class=" clipBtn" data-col="1" id="unfreeze-all">clip text</a>
                    </div>
                  </div>
                </div>


              </th>
              <th>
                <div class="th-container">
                  <div>Email</div>
                  <div class="dropdown-tablelist">
                    <span class="table-dropbtn"><i class="fa fa-caret-down"></i></span>
                    <div class="dropdown-content">
                      <a href="#" class="freeze-btn" data-col="2" id="wrap-text">freeze</a>
                      <a href="#" class="unfreeze-btn" data-col="2" id="clip-text">Un-freeez</a>
                      <a href="#" class="wrapBtn" data-col="2" id="unfreeze-all">wrap text</a>
                      <a href="#" class=" clipBtn" data-col="2" id="unfreeze-all">clip text</a>
                    </div>
                  </div>
                </div>

              </th>
              <th>
                <div class="th-container">
                  <div>Phone</div>
                  <div class="dropdown-tablelist">
                    <span class="table-dropbtn"><i class="fa fa-caret-down"></i></span>
                    <div class="dropdown-content">
                      <a href="#" class="freeze-btn" data-col="3" id="wrap-text">freeze</a>
                      <a href="#" class="unfreeze-btn" data-col="3" id="clip-text">Un-freeez</a>
                      <a href="#" class="wrapBtn" data-col="3" id="unfreeze-all">wrap text</a>
                      <a href="#" class=" clipBtn" data-col="3" id="unfreeze-all">clip text</a>
                    </div>
                  </div>
                </div>

              </th>

              <th>
                <div class="th-container">
                  <div> Country</div>

                  <div class="dropdown-tablelist">
                    <span class="table-dropbtn"><i class="fa fa-caret-down"></i></span>
                    <div class="dropdown-content">
                      <a href="#" class="freeze-btn" data-col="4" id="wrap-text">freeze</a>
                      <a href="#" class="unfreeze-btn" data-col="4" id="clip-text">Un-freeez</a>
                      <a href="#" class="wrapBtn" data-col="4" id="unfreeze-all">wrap text</a>
                      <a href="#" class=" clipBtn" data-col="4" id="unfreeze-all">clip text</a>
                    </div>
                  </div>
                </div>

              </th>
              <th>
                <div class="th-container">
                  <div>City</div>
                  <div class="dropdown-tablelist">
                    <span class="table-dropbtn"><i class="fa fa-caret-down"></i></span>
                    <div class="dropdown-content">
                      <a href="#" class="freeze-btn" data-col="5" id="wrap-text">freeze</a>
                      <a href="#" class="unfreeze-btn" data-col="5" id="clip-text">Un-freeez</a>
                      <a href="#" class="wrapBtn" data-col="5" id="unfreeze-all">wrap text</a>
                      <a href="#" class=" clipBtn" data-col="5" id="unfreeze-all">clip text</a>
                    </div>
                  </div>
                </div>
              </th>
              <th>Owner</th>
              <th>Company Name</th>
              <th>Address</th>
              <th>Company Address</th>
              <th>Company Website</th>
              <th>Employee Age</th>
              <th>Employee Name</th>
              <th>Created At</th>



            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>
      <!-- End of Table -->


      <!-- Kanban Board -->
      <div class="kanban-board kanban-view">
        <!-- Open Column -->
        <div class="kanban-column" id="open" ondrop="drop(event)" ondragover="allowDrop(event)">



          <div class="kanban-header-open">

            <span>Open</span>
          </div>

          <?php foreach ($openCards as $card): ?>

            <div class="kanban-card" id="card-<?= $card->id ?>" draggable="true" ondragstart="drag(event)">
              <h4><?= htmlspecialchars($card->title) ?></h4>
              <p><?= htmlspecialchars($card->first_name . ' ' . $card->last_name) ?></p>
            </div>
          <?php endforeach; ?>
        </div>

        <!-- In Progress Column -->
        <div class="kanban-column" id="in_progress" ondrop="drop(event)" ondragover="allowDrop(event)">
          <div class="kanban-header-inprogress">
            <span>In Progress</span>
          </div>
          <?php foreach ($inProgressCards as $card): ?>
            <div class="kanban-card" id="card-<?= $card->id ?>" draggable="true" ondragstart="drag(event)">
              <h4><?= htmlspecialchars($card->title) ?></h4>
              <p><?= htmlspecialchars($card->first_name . ' ' . $card->last_name) ?></p>
            </div>
          <?php endforeach; ?>
        </div>

        <!-- Done Column -->
        <div class="kanban-column" id="done" ondrop="drop(event)" ondragover="allowDrop(event)">
          <div class="kanban-header">
            <span>Done</span>
          </div>
          <?php foreach ($doneCards as $card): ?>
            <div class="kanban-card" id="card-<?= $card->id ?>" draggable="true" ondragstart="drag(event)">
              <h4><?= htmlspecialchars($card->title) ?></h4>
              <p><?= htmlspecialchars($card->first_name . ' ' . $card->last_name) ?></p>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- end kanban -->




    </div>

  </div>

</div>


<?php
// Registering DataTables assets


$this->registerJsFile('https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js', ['depends' => [AdminAsset::class]]);
$this->registerJsFile('https://cdn.datatables.net/fixedcolumns/4.2.2/js/dataTables.fixedColumns.min.js', ['depends' => [AdminAsset::class]]);
$this->registerJsFile('https://cdn.datatables.net/fixedheader/3.3.1/js/dataTables.fixedHeader.min.js', ['depends' => [AdminAsset::class]]);

// <!-- Include colResizable JS for column resizing -->

$this->registerJsFile('https://legacy.datatables.net/extras/thirdparty/ColReorderWithResize/ColReorderWithResize.js', ['depends' => [AdminAsset::class]]);


$this->registerJsFile('https://code.jquery.com/ui/1.13.2/jquery-ui.min.js', ['depends' => [AdminAsset::class]]);
$this->registerJsFile('https://code.jquery.com/ui/1.9.2/jquery-ui.js', ['depends' => [AdminAsset::class]]);


$this->registerJs("

    $('.table-dropbtn').on('click', function(e) {
    e.preventDefault();
    e.stopPropagation();

    // Get the button position relative to the page
    const buttonPosition = $(this).offset();
    
    // Find the dropdown menu
    const dropdownMenu = $(this).next('.dropdown-content');
    
    // Set the position of the dropdown dynamically
    dropdownMenu.css({
        top: buttonPosition.top + $(this).outerHeight() -100 + 'px',  // Position below the button
        left: buttonPosition.left + 'px', // Align horizontally with the button
        display: 'block'
    });
});

// Close dropdown when clicking outside
$(document).on('click', function() {
    $('.dropdown-content').hide();
});  



    $(document).ready(function() {
        var table = $('#datatable').DataTable({
            ajax: {
                url: '" . Url::to(['lead/get-data']) . "',
                type: 'GET',
                dataSrc: ''
            },
            columns: [
                { data: 'id' },
                { data: 'first_name' },
                { data: 'last_name' },
                { data: 'email' },
                { data: 'phone' },
                { data: 'country' },
                { data: 'city' },
                { data: 'owner' },
                { data: 'company_name' },
                { data: 'address' },
                { data: 'company_address' },
                { data: 'company_website' },
                { data: 'employee_age' },
                { data: 'employee_name' },
                { data: 'created_at' } 
            ],
            
            scrollX: true,
            scrollY: '50vh', // Optional: Enable vertical scrolling with fixed height
            paging: true,
            ordering: false,
            fixedHeader: true,
            responsive: true,
            autoWidth: false, // Disable auto width to respect manually set column widths
    
        });
   

        // Wrap button click event
        $('.wrapBtn').on('click', function() {
            var colIdx = $(this).data('col');
            
            $('#datatable tbody td:nth-child(' + (colIdx + 2) + ')').css({
                'white-space': 'normal',   // Enable text wrapping
                'overflow': 'visible'      // Show the overflowing text
            });
        });

        // Clip button click event
        $('.clipBtn').on('click', function() {
            var colIdx = $(this).data('col');
            $('#datatable tbody td:nth-child(' + (colIdx + 2) + ')').css({
                'white-space': 'nowrap',    // Disable wrapping
                'overflow': 'hidden',       // Hide overflowed text
                'text-overflow': 'ellipsis' // Show ellipsis for overflowed text
            });
        });

        function reinitializeDataTable(freezeColumns) {
            // Destroy the current table instance
            table.destroy();

            // Define base options for reinitialization
            var options = {
                ajax: {
                    url: '" . Url::to(['table/get-data']) . "',
                    type: 'GET',
                    dataSrc: ''
                },
                columns: [
                    { data: 'id'},
                    { data: 'first_name' },
                    { data: 'last_name' },
                    { data: 'email' },
                    { data: 'phone' },
                    { data: 'country' },
                    { data: 'city' },
                    { data: 'owner' },
                    { data: 'company_name' },
                    { data: 'address' },
                    { data: 'company_address' },
                    { data: 'company_website' },
                    { data: 'employee_age' },
                    { data: 'employee_name' },
                    { data: 'created_at' }


                    
                ],
            scrollX: true,
            scrollY: '50vh', // Optional: Enable vertical scrolling with fixed height
            
            paging: true,
            ordering: false,
            fixedHeader: true,
            responsive: true,
            autoWidth: false, // Disable auto width to respect manually set column widths
            };

            // Only include fixedColumns if freezing columns
            if (freezeColumns > 1) {
                options.fixedColumns = { leftColumns: freezeColumns };
            }

            // Reinitialize DataTable with or without the fixedColumns option
            table = $('#datatable').DataTable(options);
        }

        // Freeze Button Click Event
        $('.freeze-btn').on('click', function(event) {
            event.preventDefault();
            var colIdx = $(this).data('col');
            reinitializeDataTable(colIdx + 2); // Reinitialize with frozen columns
        });

        // Unfreeze Button Click Event with Data Refresh
        $('.unfreeze-btn').on('click', function(event) {
            event.preventDefault();
            location.reload();
        });
    });
  



    
");



?>

<script>
  function allowDrop(event) {
    event.preventDefault();
  }

  function drag(event) {
    event.dataTransfer.setData("text", event.target.id);
  }

  function drop(event) {
    event.preventDefault();
    var cardId = event.dataTransfer.getData("text");
    var card = document.getElementById(cardId);
    event.target.appendChild(card);

    // Update the pipeline stage in the database via AJAX
    var newStage = event.target.id;
    $.ajax({
      url: '<?= Yii::$app->urlManager->createUrl('lead/update-stage') ?>',
      type: 'POST',
      data: {
        id: cardId.split("-")[1],
        pipeline_stage: newStage,
        _csrf: '<?= Yii::$app->request->getCsrfToken() ?>'
      },
      success: function(response) {
        console.log("Card stage updated");
      }
    });
  }
</script>

<script>
  // Switch to List View
  function switchToListView() {
    document.querySelector('.kanban-view').style.display = 'none'; // Hide Kanban
    document.querySelector('.table-list').style.display = 'block'; // Show Table
    localStorage.setItem('selectedView', 'list'); // Save preference
  }

  // Switch to Kanban View
  function switchToKanbanView() {
    document.querySelector('.table-list').style.display = 'none'; // Hide Table
    document.querySelector('.kanban-view').style.display = 'flex'; // Show Kanban
    localStorage.setItem('selectedView', 'kanban'); // Save preference
  }

  // Load the selected view on page load
  window.onload = function() {
    const selectedView = localStorage.getItem('selectedView') || 'list'; // Default to list if none selected
    const viewSelector = document.getElementById('viewSelector');

    if (selectedView === 'kanban') {
      switchToKanbanView();
      viewSelector.value = 'kanban';
    } else {
      switchToListView();
      viewSelector.value = 'list';
    }

    // Add event listener for view change
    viewSelector.addEventListener('change', function() {
      if (this.value === 'kanban') {
        switchToKanbanView();
      } else {
        switchToListView();
      }
    });
  }
</script>