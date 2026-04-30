<?php

use yii\helpers\Url;
use yii\helpers\Html;
use backend\assets\AdminAsset;


AdminAsset::register($this);
$this->title = Yii::t('app', 'Add '.$Tabname);

$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css', ['depends' => [AdminAsset::class]]);

$this->registerCssFile('@web/thememain/css/ag-grid.css', ['depends' => [AdminAsset::class]]);
$this->registerCssFile('@web/thememain/css/ag-theme-alpine.css', ['depends' => [AdminAsset::class]]);
$this->registerCssFile('@web/thememain/css/listview.css', ['depends' => [AdminAsset::class]]);

$url =Url::to(['create']);
$baseUrl = Yii::$app->HomeUrl; 
?>

<div class="group-15">
      <div class="d-flex1">
        <!-- <div class="div-group-36">
                    <div class="div-vector-37"></div>
                </div>
                <div class="div-ellipse"></div> -->
        <div class="all-open-lead">All <?= $Tabname; ?> </div>
        <img class="ep-arrow-down-8" src="https://c.animaapp.com/4Te5O9cu/img/ep-arrow-down-26.svg">
      </div>
      <div class="d-flex2">
        <!-- <img class="flowbite-refresh" src="https://c.animaapp.com/4Te5O9cu/img/flowbite-refresh-outline.svg"> -->
        <img class="" src="https://c.animaapp.com/4Te5O9cu/img/flowbite-refresh-outline.svg">
      </div>
      <div class="d-flex3">
        <div class="overlap-16">
        <div class="text-wrapper-126">Import</div>
        <img class="lets-icons-import" src="https://c.animaapp.com/4Te5O9cu/img/lets-icons-import-light.svg">
      </div>
      </div>
      <div class="d-flex4">
        <div class="overlap-group-14">
          
          <div class="text-wrapper-127">
            <!-- <select class="view-selector" id="viewSelector">
              <option value="list">List View</option>
              <option value="kanban">Kanban</option>
            </select> -->
            <div class="kl-dropdown">
            <button class="dropdown-button">
                <div class="listoption">
                  <span class="icon"><img class="tdesign-list" src="<?php echo  $baseUrl;?>/thememain/img/List-view.png"><img class="vector-67" src="<?php echo  $baseUrl;?>/thememain/img/HR.png" ></span> List View
                  <span class="btn-arrow"  onclick="toggleDropdown()"><img class="ic-round-arrow-left-65" src="https://c.animaapp.com/4Te5O9cu/img/ic-round-arrow-left-68.svg"></span>
                </div>
                <div class="kanbanoption">
                  
                  <img src="<?php echo  $baseUrl;?>/thememain/img/kanben.png"/> Kanben
                  <span class="btn-arrow"  onclick="toggleDropdown()"><img class="ic-round-arrow-left-65" src="https://c.animaapp.com/4Te5O9cu/img/ic-round-arrow-left-68.svg"></span>
                </div>
            </button>
            <div class="dropdown-content">
                <!-- <a href="#"><span class="icon">📋</span> List View</a>
                <a href="#"><span class="icon">📊</span> Kanban</a> -->
                <a href="#" class="listview"><span class="icon"><img class="tdesign-list-inner" src="<?php echo  $baseUrl;?>/thememain/img/List-view.png" style="width: 25px;"></span> List View</a>
                <a href="#" class="kabanview"><span class="icon"><img src="<?php echo  $baseUrl;?>/thememain/img/kanben.png"/></span> Kanban</a>
            </div>
        </div>
          </div>
          
        </div>
      </div>
      <div class="d-flex5">
         <!-- Filter By Name Button -->

        <button class="filter-selector-btn" data-direction="right" id="filterSelectorButton" style="background: none; border: none; cursor: pointer;">
          <img class="filter-selector-btn" src="https://c.animaapp.com/4Te5O9cu/img/typcn-filter.svg"
            alt="filter Selector" />
        </button>


        <!-- Filter By Name Modal Structure -->

        <div class="modal fade " id="filterByNameModel" aria-modal="true" role="dialog">
          <div class="modal-dialog">
            <div class="modal-content" style="width: 73%; height: 69%;">
              <div class="modal-header">
                <h4 class="modal-title">Filter Lead By</h4>
                <button type="button" class="btn-close fil-btn" aria-label="Close"></button>
              </div>
              <div class="modal-header">
                <div class="input-group mb-3">
                  <input type="text" class="form-control" placeholder="Search" aria-label="Search">
                  <button class="btn btn-outline-secondary" onclick="openfieldName()" type="button" id="addFieldButton">
                    <span class="fa fa-plus"></span>
                  </button>
                </div>
              </div>
              <div class="modal-body">
                <div id="field_name" style="display:none;">
                  <?php foreach ($filed_name as $filed_names): ?>
                    <div class="filed-div" onclick="openFilterBox('<?php echo $filed_names['fieldid']; ?>', '<?php echo $filed_names['fieldname']; ?>','<?php echo $filed_names['fieldlabel']; ?>','<?php echo $filed_names['uitype']; ?>','<?php echo $filed_names['tablename']; ?>')">
                      
                        <?php echo $filed_names['fieldlabel']; ?>
                     
                    </div>
                  <?php endforeach; ?>
                </div>

                <!-- Container for the filter box (initially hidden) -->
                <div id="filterBox" class="filter-box"  style="display:none;">
                  <div class="field-label-row">
                    <div class="filterfieldlabel">
                    <span id="filterFieldLabel"></span>
                    </div>
                    <div class="filtertrashbox" >
                    <i onclick="closeFilterBox()" class="fa fa-trash close-button" style="margin-left: 188px;"></i>
                    </div>
                  </div>
                  
                  <input type="hidden" id="filterFieldName" value="">
                  <input type="hidden" id="filterFielduitype" value="">
                  <input type="hidden" id="filterFieldtablename" value="">
                  <!-- Dropdown for selecting comparison operators -->
                  <select id="filterOperator" class="form-select">
                    <option value="Equals">Equals</option>
                    <option value="Not_Equals">Not Equals</option>
                    <option value="Contains">Contains</option>
                    <option value="Not_Contains">Not Contains</option>
                    <option value="In">In</option>
                    <option value="Not_In">Not In</option>
                    <option value="is_Empty">is Empty</option>
                    <option value="is_Not_Empty">is Not Empty</option>
                    <option value="Begins_with">Begins With</option>
                  </select>

                  <input type="text" class="form-control" id="filterValue" placeholder="Enter value" style="display:block;" />
               
                  
                </div>
              </div>

              <div class="modal-footer">

                <button type="button" id="apply-filter-by-name" onclick="applyFilter()" class="btn btn-primary">Apply</button>
              </div>
            </div>
          </div>
        </div>
        <!-- End Filter By Name -->
      </div>
      <div class="d-flex6">

      <!-- Column Selector Button -->
        <button class="col-selector-btn" data-direction="right" id="columnSelectorButton" style="background: none; border: none; cursor: pointer;">
         <img class="" src="https://c.animaapp.com/4Te5O9cu/img/fluent-column-triple-edit-24-regular.svg" alt="Column Selector" style="width: 24px; height: 24px;">
        </button>
        <!-- <img class="" src="https://c.animaapp.com/4Te5O9cu/img/fluent-column-triple-edit-24-regular.svg" alt="Column Selector" style="width: 24px; height: 24px;"> -->
        <!-- Column Selector Modal Structure -->
  <div class="modal fade " id="columnSelectorModel" aria-modal="true" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content" style="width: 73%; height: 69%;">
        <div class="modal-header">

          <h4 class="modal-title">Choose Columns</h4>
          <button type="button" class="btn-close cs-btn" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <!-- Loop through each column and create a checkbox -->
          <!-- Loop through each column and create a checkbox -->
          <?php foreach ($columns as $column): ?>
            <div>
              <label>
                <input type="checkbox" name="column[]"
                  data-field_id="<?= $column['fieldid'] ?>"
                  data-columnname="<?= $column['columnname'] ?>"
                  value="<?= $column['fieldid'] ?>"
                  <?= $column['visible'] ? 'checked' : '' ?>>
                <?= htmlspecialchars($column['headerName']) ?>
              </label>
            </div>
          <?php endforeach; ?>
        </div>
        <div class="modal-footer">

          <button type="button" id="apply-column-changes" class="btn btn-primary">Apply Changes</button>
        </div>
      </div>
    </div>
  </div>


  <!-- End Column Selector Modal -->

      </div>
      <div class="d-flex7">
        <button type="button" id="add-lead-btn" class="btn add-lead-btn" style="background-color: var(--color-primary) !important; color:white">+ Add</button>
       <!--  <a href="create" type="button"  class="btn add-lead-btn" style="background-color: var(--color-primary) !important; color:white">+ Add</a> -->
      </div>
</div>



<!-- Add Modal Structure -->

<div class="modal fade" id="add-lead-modal" tabindex="-1" role="dialog" aria-labelledby="addLeadModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
     

     
    </div>
  </div>
</div>

<!-- end add model -->

<div class="overlap-4">
  <div class="frame-8">
    <div class="overlap-5">



      <!-- Table -->
      <div class="table-list">
        <div id="myGrid" class="ag-theme-alpine"></div>
        <div id="customPagination" class="custom-pagination"></div>

        <!-- <div class="page-div">
          <div class="result-page">
            <label for="page-size" class="page-size">Results Per Page</label>
            <select id="page-size" onchange="updatePageSize(this.value)">
              <option value="5">5</option>
              <option value="10">10</option>
              <option value="20">20</option>
              <option value="50">50</option>
              <option value="100">100</option>
            </select>

          </div>

          <div class="custom-pagination">
            <button onclick="goToFirstPage()">First</button>
            <button onclick="goToPreviousPage()">&#8249;</button>
            <span id="current-page"></span>
            <button onclick="goToNextPage()">&#8250;</button>
            <button onclick="goToLastPage()">Last</button>
          </div>
        </div> -->




        <!-- pagination -->


      </div>
      <!-- End of Table -->


      <!-- Kanban Board -->
      <div class="kanban-board kanban-view">
        <!-- Open Column -->

        <div class="outer-kanban" id="open" ondrop="drop(event)" ondragover="allowDrop(event)">


          <div class="kanban-header-open">
            <div class="dv-kanban-title">
              <span class="kanban-title">Open</span>
            </div>
            <div class="kanban-add-btn">+</div>
          </div>

          <div class="kanban-column">
            <div class="inner-kanban" id="open">
              <?php foreach ($openCards as $card): ?>
                <div class="kanban-card" id="card-<?= $card->id ?>" draggable="true" ondragstart="drag(event)">
                  <h4><?= htmlspecialchars($card->title) ?></h4>
                  <p><?= htmlspecialchars($card->first_name . ' ' . $card->last_name) ?></p>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>


        <!-- In Progress Column -->
        <div class="outer-kanban" id="in_progress" ondrop="drop(event)" ondragover="allowDrop(event)">
          <div class="kanban-header-inprogress">
            <div class="dv-kanban-title">
              <span class="kanban-title">In Progress</span>
            </div>
            <div class="kanban-add-btn">+</div>
          </div>

          <div class="kanban-column">
            <div class="inner-kanban" id="in_progress">
              <?php foreach ($inProgressCards as $card): ?>
                <div class="kanban-card" id="card-<?= $card->id ?>" draggable="true" ondragstart="drag(event)">
                  <h4><?= htmlspecialchars($card->title) ?></h4>
                  <p><?= htmlspecialchars($card->first_name . ' ' . $card->last_name) ?></p>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>


        <!-- Done Column -->
        <div class="outer-kanban" id="done" ondrop="drop(event)" ondragover="allowDrop(event)">

          <div class="kanban-header-done">
            <div class="dv-kanban-title">
              <span class="kanban-title">Done</span>
            </div>
            <div class="kanban-add-btn">+</div>
          </div>

          <div class="kanban-column">
            <div class="inner-kanban" id="done">
              <?php foreach ($doneCards as $card): ?>
                <div class="kanban-card" id="card-<?= $card->id ?>" draggable="true" ondragstart="drag(event)">
                  <h4><?= htmlspecialchars($card->title) ?></h4>
                  <p><?= htmlspecialchars($card->first_name . ' ' . $card->last_name) ?></p>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>

      </div>

    </div>

    <!-- end kanban -->




  </div>

</div>



</div>


<?php



$this->registerJs("
    // $('#add-lead-btn').click(function() {
    //     $('#add-lead-modal').modal('show');
    // });
    $('.btn-close, .btn-secondary').click(function() {
       $('#add-lead-modal').modal('hide');
    });

    //modal create
    
    $('#add-lead-btn').on('click', function () {
        // $.get('edit?Record=1', function(data) {

        $.get('{$url}', function(data) {
            $('#add-lead-modal').modal('show')
                .find('.modal-content')
                .html(data);
        });
    });



 
");
$this->registerJsFile('@web/thememain/js/ag-grid-community.min.js', ['depends' => [AdminAsset::class]]);
$this->registerJsFile('@web/thememain/js/custom.js', ['depends' => [AdminAsset::class]]);
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

  // Add event listener for view change
    document.querySelector(".listview").addEventListener('click', function() {
     
        switchToListView();
        document.querySelector('.kanbanoption').style.display = "none";
        document.querySelector('.listoption').style.display = "block";
        toggleDropdown();

      
    });
     document.querySelector(".kabanview").addEventListener('click', function() {
      
        switchToKanbanView();
         document.querySelector('.kanbanoption').style.display = "block";
        document.querySelector('.listoption').style.display = "none";
        toggleDropdown();
     
    });
    function toggleDropdown() {
    const dropdownContent = document.querySelector('.dropdown-content');
    if (dropdownContent.style.display === "block") {
        dropdownContent.style.display = "none";
    } else {
        dropdownContent.style.display = "block";
    }
}

  // Load the selected view on page load
  window.onload = function() {
     switchToListView();
    // const selectedView = localStorage.getItem('selectedView') || 'list'; // Default to list if none selected
    // const viewSelector = document.getElementById('viewSelector');

    // if (selectedView === 'kanban') {
    //   switchToKanbanView();
    //   viewSelector.value = 'kanban';
    // } else {
    //   switchToListView();
    //   viewSelector.value = 'list';
    // }

    // // Add event listener for view change
    // viewSelector.addEventListener('change', function() {
    //   if (this.value === 'kanban') {
    //     switchToKanbanView();
    //   } else {
    //     switchToListView();
    //   }
    // });
  }
</script>