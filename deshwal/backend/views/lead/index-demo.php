<?php

use yii\helpers\Url;
use yii\helpers\Html;
use backend\assets\AdminAsset;


AdminAsset::register($this);
$this->title = Yii::t('app', 'Table List');
//Add DataTables CSS CDN
$this->registerCssFile('https://cdn.datatables.net/v/dt/dt-1.10.16/datatables.min.css', ['depends' => [AdminAsset::class]]);
$this->registerCssFile('https://cdn.datatables.net/fixedcolumns/4.2.2/css/fixedColumns.dataTables.min.css', ['depends' => [AdminAsset::class]]);
$this->registerCssFile('https://cdn.datatables.net/fixedheader/3.3.1/css/fixedHeader.dataTables.min.css', ['depends' => [AdminAsset::class]]);
$this->registerCssFile('https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css', ['depends' => [AdminAsset::class]]);
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css', ['depends' => [AdminAsset::class]]);

$this->registerCssFile('https://unpkg.com/ag-grid-community/styles/ag-grid.css', ['depends' => [AdminAsset::class]]);
$this->registerCssFile('https://unpkg.com/ag-grid-community/styles/ag-theme-alpine.css', ['depends' => [AdminAsset::class]]);

?>

<style>
  .kanban-board {
    display: flex;
    /*gap: 20px;*/
    padding: 20px;
  }

  .kanban-column {
    /* background-color: #f9f9f9;
    border: 1px solid #ccc;
    border-radius: 8px;
    width: 300px;
    padding: 15px;
    min-height: 400px;*/
    min-height: 400px;
    width: 300px;
  }

  .kanban-title {
    position: absolute;
    padding: 15px;
  }

  .inner-kanban {
    position: relative;
    background-color: #d9d9d9;
    min-height: 400px !important;
    margin-right: 18px;
    padding: 10px;

  }

  .dv-kanban-title {
    width: 80%;
    position: relative;
    /*padding: 6px;*/
  }

  .kanban-add-btn {
    padding: -1px;
    position: relative;
    width: 20%;
    top: 20px;
    text-align: center;
  }

  /*open*/
  .kanban-header-open {

    background-image: url("<?= Yii::getAlias('@web/images/rectangle64381102-0y0o.svg') ?>");
    background-size: cover;
    /* Adjust as needed */
    background-position: center;
    background-repeat: no-repeat;
    width: 100%;
    /* Adjust as needed */
    height: 100%;
    color: #fff;
    /* Adjust as needed */
    /*padding: inherit;*/

    /*padding: 0px 0px 60px 0px;*/
    padding: 10px 0px 50px 20px;
    display: flex;
  }

  /*in progress*/
  .kanban-header-inprogress {

    background-image: url("<?= Yii::getAlias('@web/images/rectangle64391102-00e.svg') ?>");

    background-size: cover;
    /* Adjust as needed */
    background-position: center;
    background-repeat: no-repeat;
    width: 100%;
    /* Adjust as needed */
    height: 100%;
    color: #fff;
    /* Adjust as needed */
    /*padding: inherit;*/

    /*padding: 0px 0px 60px 0px;*/
    padding: 10px 0px 50px 20px;
    display: flex;
  }

  /*done*/
  .kanban-header-done {

    background-image: url("<?= Yii::getAlias('@web/images/rectangle64401102-yr1q.svg') ?>");

    background-size: cover;
    /* Adjust as needed */
    background-position: center;
    background-repeat: no-repeat;
    width: 100%;
    /* Adjust as needed */
    height: 100%;
    color: #fff;
    display: flex;
    /* Adjust as needed */
    /*padding: inherit;*/

    /*padding: 0px 0px 60px 0px;*/
    padding: 10px 0px 50px 20px;
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

  .custom-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: relative;
  }

  .dropdown {
    position: relative;
  }

  .dropdown-toggle {
    background: none;
    border: none;
    cursor: pointer;
    font-size: 12px;
    color: #333;
  }

  .dropdown-menu {
    display: none;
    position: fixed;
    /* Fixed position so it renders outside table boundaries */
    background: white;
    border: 1px solid #ccc;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
    z-index: 10000;
    /* High z-index to ensure visibility */
    min-width: 120px;
    white-space: nowrap;
  }

  .dropdown-item {
    padding: 8px 12px;
    cursor: pointer;
    font-size: 12px;
    color: #333;
    background: white;
    border: none;
    width: 100%;
    text-align: left;
  }

  .dropdown-item:hover {
    background: #f0f0f0;
  }

  .sort-arrow {
    font-size: 12px;
    margin-left: 5px;
    cursor: pointer;
    margin: 10px;
  }

  /* Apply to ag-Grid container */
  #myGrid {
    font-family: "Poppins", Helvetica;
    height: 400px;
    /* Set a fixed height to enable scrolling */
    width: 100%;
    /* Adjust width as needed */
    overflow: auto;
    /* Enable both horizontal and vertical scrolling */
  }

  .ag-theme-alpine .ag-cell {
    display: flex;
    align-items: center;
  }

  .ag-theme-alpine .ag-cell.ag-cell-wrap {
    display: block;
    padding: 5px;
    box-sizing: border-box;
    height: auto !important;
  }

  /* Default clipping for all cells */
  .ag-cell {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  /* Wrapping class */
  .ag-cell-wrap {
    white-space: normal;
    overflow-wrap: break-word;
    word-wrap: break-word;
    word-break: break-word;
  }

  .ag-theme-alpine .ag-header {
    background-color: #CACCCD;
    /* Light gray color */
  }

  /* Customize the ag-Grid pagination bar */

  .ag-paging-panel {
    display: flex;
    font-size: 14px;
  }

  .ag-paging-panel .ag-paging-button {
    border: 1px solid #ccc;
    border-radius: 3px;
    padding: 5px 10px;
    cursor: pointer;
    margin: 0 2px;
    color: #007bff;
  }

  .ag-paging-panel .ag-paging-button:hover {
    background-color: #f0f0f0;
  }

  .ag-paging-panel .ag-paging-number {
    border: 1px solid #007bff;
    color: #007bff;
    padding: 5px 10px;
    margin: 0 2px;
    cursor: pointer;
  }

  .ag-paging-panel .ag-paging-number.ag-pagination-active {
    background-color: #007bff;
    color: white;
  }


  .custom-pagination {
    display: flex;
    gap: 8px;
    margin-top: 10px;
  }

  .page-button {
    padding: 5px 10px;
    border: 1px solid #ccc;
    cursor: pointer;
    background-color: #f9f9f9;
  }

  .page-button.active {
    background-color: #007bff;
    color: white;
    border-color: #007bff;
  }


  .modal {
    position: fixed;
    top: 13px;
    left: -173px;
    z-index: 1055;
    display: none;
    width: 106%;
    height: 131%;
    overflow-x: hidden;
    overflow-y: hidden;
    outline: 0;
  }

  .col-md-3 {
    border: 1px solid #e9e9ef;
  }

  .form-row {
    display: flex;
    gap: 18px;
    padding: 8px 8px;
    padding-left: 4px;
    padding-right: 45px;
  }

  .modal-content {
    height: 550px;
    position: relative;
    -webkit-box-orient: vertical;
    -webkit-box-direction: normal;
    -ms-flex-direction: column;
    flex-direction: column;
    width: 140%;
    pointer-events: auto;
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid #f6f6f6;
    border-radius: .25rem;
    outline: 0;
  }

  .toggle-container {
    display: flex;
    right: 90px;
    align-items: center;
    font-family: Arial, sans-serif;
    color: #555;
    font-size: 16px;
    position: absolute;
  }

  .toggle-switch {
    position: relative;
    width: 40px;
    height: 20px;
    margin-right: 10px;
    background-color: #e0e0e0;
    border-radius: 10px;
    cursor: pointer;
    transition: background-color 0.3s;
  }

  .toggle-switch::before {
    content: "";
    position: absolute;
    width: 18px;
    height: 18px;
    top: 1px;
    left: 1px;
    background-color: #fff;
    border-radius: 50%;
    transition: transform 0.3s;
  }

  .toggle-switch.active {
    background-color: #007bff;
  }

  .toggle-switch.active::before {
    transform: translateX(20px);
  }

  .title-tab {
    width: 759px;
    height: 40px;
    background: #d9d9d9;
    margin: 4px;
  }




  /* .left {
    overflow-y: auto;
  }

  .right {
    overflow-y: auto;
  } */

  .col-md-8 {
    width: 75%;
  }

  .title-info {
    padding: 12px;
  }

  .required-field {
    display: none;
  }

  /* Targeting the modal container */
  #columnSelectorModel {

    padding-left: 0;
    margin: 117px auto;
    width: 198%;
    max-height: 85vh;

  }

  /* Modal dialog */
  #columnSelectorModel .modal-dialog {
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  /* Modal content styling */
  #columnSelectorModel .modal-content {
    width: 100%;
    max-height: 100%;
    /* Ensures modal content respects the height */
    display: flex;
    flex-direction: column;
    /* Make it column layout */
  }

  /* Modal body to enable scroll */
  #columnSelectorModel .modal-body {
    max-height: calc(50vh - 100px);
    overflow-y: auto;
    /* Add vertical scroll */
    padding: 10px;
    margin-bottom: 10px;
  }

  /* Modal footer styling to keep it at the bottom */
  #columnSelectorModel .modal-footer {
    position: sticky;
    bottom: 0;
    background-color: #fff;
    /* Ensure background visibility */
    padding: 10px;
  }

  table {
    width: 100%;
    border-collapse: collapse;
  }

  th,
  td {
    padding: 8px;
    border: 1px solid #ddd;
    text-align: left;
  }

  th {
    background-color: #f4f4f4;
  }
</style>


<!-- open lead Add button -->
<div class="group-15">
  <div class="group-16">
    <button type="button" id="add-lead-btn" class="btn add-lead-btn" style="background-color: var(--color-primary) !important; color:white">+ Add</button>
  </div>
  <div class="group-17">
    <div class="overlap-16">
      <div class="text-wrapper-126">Import</div>
      <img class="lets-icons-import" src="https://c.animaapp.com/4Te5O9cu/img/lets-icons-import-light.svg" />
    </div>
  </div>



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

  <img class="typcn-filter" src="https://c.animaapp.com/4Te5O9cu/img/typcn-filter.svg" />

  <!-- Column Selector Button -->



  <button class="col-selector-btn" data-direction="right" id="columnSelectorButton" style="background: none; border: none; cursor: pointer;">
    <img class="fluent-column-triple"
      src="https://c.animaapp.com/4Te5O9cu/img/fluent-column-triple-edit-24-regular.svg"
      alt="Column Selector"
      style="width: 24px; height: 24px;" />
  </button>

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
          <?php foreach ($columns as $column): ?>
            <div>
              <label>
                <input type="checkbox" name="column[]"
                  data-field_id="<?= $column['fieldid'] ?>"
                  data-columnname="<?= $column['columnname'] ?>"
                  value="<?= $column['fieldid'] ?>"
                  <?= $column['visible'] ? 'checked' : '' ?>>
                <?= htmlspecialchars($column['columnname']) ?>
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

  <img class="flowbite-refresh" src="https://c.animaapp.com/4Te5O9cu/img/flowbite-refresh-outline.svg" />
  <div class="group-19">
    <div class="all-open-lead">All Open Lead</div>
    <img class="ep-arrow-down-8" src="https://c.animaapp.com/4Te5O9cu/img/ep-arrow-down-26.svg" />
  </div>
</div>

<!-- end open lead Add button  -->

<!-- Add Modal Structure -->

<div class="modal fade" id="add-lead-modal" tabindex="-1" role="dialog" aria-labelledby="addLeadModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addLeadModalLabel">Add Lead</h5>
        <div class="toggle-container">
          <div class="toggle-switch" onclick="toggleRequiredFields()"></div>
          Show Required & Important Fields
        </div>
        <button type="button" class="btn-close" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <!-- Tabs Section -->


          <div class="col-md-3">
            <div class="left">
              <ul class="nav flex-column nav-pills" role="tablist" aria-orientation="vertical">
                <li class="nav-item">
                  <a class="nav-link active" id="lead-info-tab" data-toggle="pill" href="#lead-info" role="tab">Lead Information</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="communication-tab" data-toggle="pill" href="#communication" role="tab">Communication</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="location-tab" data-toggle="pill" href="#location" role="tab">Location</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="professional-tab" data-toggle="pill" href="#professional" role="tab">Professional</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="requirement-tab" data-toggle="pill" href="#requirement" role="tab">Requirement</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="campaign-tab" data-toggle="pill" href="#campaign" role="tab">Campaign Information</a>
                </li>
              </ul>
            </div>

          </div>

          <!-- Tab Content Section -->

          <div class="col-md-8">
            <div class="right">
              <div class="tab-content">
                <!-- Lead Information Tab -->
                <div class="tab-pane fade show active" id="lead-info" role="tabpanel" aria-labelledby="lead-info-tab">
                  <div class="title-tab">
                    <label class="title-info">Lead Information</label>
                  </div>
                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <label for="lead-owner">Lead Owner</label>
                      <input type="text" class="form-control" id="lead-owner" placeholder="Enter Lead Owner">
                    </div>
                    <div class="form-group col-md-6  required-field">
                      <label for="company">Company <span style="color: red;">*</span></label>
                      <input type="text" class="form-control" id="company" placeholder="Enter Company Name" required>
                    </div>
                  </div>
                  <div class="form-row">
                    <div class="form-group col-md-6  required-field">
                      <label for="first-name">First Name <span style="color: red;">*</span></label>
                      <input type="text" class="form-control" id="first-name" placeholder="Enter First Name" required>
                    </div>
                    <div class="form-group col-md-6">
                      <label for="last-name">Last Name</label>
                      <input type="text" class="form-control" id="last-name" placeholder="Enter Last Name">
                    </div>
                  </div>
                  <div class="form-row">
                    <div class="form-group col-md-6  required-field">
                      <label for="phone">Phone <span style="color: red;">*</span></label>
                      <input type="text" class="form-control" id="phone" placeholder="Enter Phone Number" required>
                    </div>
                    <div class="form-group col-md-6  required-field">
                      <label for="category">Category <span style="color: red;">*</span></label>
                      <select class="form-control" id="category" required>
                        <option value="">Select Category</option>
                        <option value="1">Category 1</option>
                        <option value="2">Category 2</option>
                      </select>
                    </div>
                  </div>

                </div>

                <!-- Communication Tab -->
                <div class="tab-pane fade show active" id="communication" role="tabpanel" aria-labelledby="communication-tab">

                  <div class="title-tab">
                    <label class="title-info">communication</label>
                  </div>
                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <label for="lead-owner">Address</label>
                      <input type="text" class="form-control" id="lead-owner" placeholder="Enter Address">
                    </div>
                    <div class="form-group col-md-6  required-field">
                      <label for="company">email <span style="color: red;">*</span></label>
                      <input type="text" class="form-control" id="company" placeholder="Enter Your Email " required>
                    </div>
                  </div>
                  <div class="form-row">
                    <div class="form-group col-md-6  required-field">
                      <label for="first-name">company <span style="color: red;">*</span></label>
                      <input type="text" class="form-control" id="first-name" placeholder="Enter Company Name" required>
                    </div>
                    <div class="form-group col-md-6">
                      <label for="last-name">city</label>
                      <input type="text" class="form-control" id="last-name" placeholder="Enter City Name">
                    </div>
                  </div>
                </div>

                <!-- Location Tab -->
                <div class="tab-pane fade" id="location" role="tabpanel" aria-labelledby="location-tab">
                  <p>Location-related fields go here.</p>
                </div>

                <!-- Professional Tab -->
                <div class="tab-pane fade" id="professional" role="tabpanel" aria-labelledby="professional-tab">
                  <p>Professional-related fields go here.</p>
                </div>

                <!-- Requirement Tab -->
                <div class="tab-pane fade" id="requirement" role="tabpanel" aria-labelledby="requirement-tab">
                  <p>Requirement-related fields go here.</p>
                </div>

                <!-- Campaign Information Tab -->
                <div class="tab-pane fade" id="campaign" role="tabpanel" aria-labelledby="campaign-tab">
                  <p>Campaign Information-related fields go here.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal Footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary">Save</button>
      </div>
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

        <!-- <table id="dataTable" >
          <thead>
            <tr id="tableHeader"></tr>
          </thead>
          <tbody id="tableBody"></tbody>
        </table> -->

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
// Registering DataTables assets
$this->registerJsFile('https://cdn.datatables.net/v/dt/dt-1.10.16/datatables.min.js', ['depends' => [AdminAsset::class]]);
$this->registerJsFile('https://cdn.datatables.net/fixedcolumns/4.2.2/js/dataTables.fixedColumns.min.js', ['depends' => [AdminAsset::class]]);
$this->registerJsFile('https://cdn.datatables.net/fixedheader/3.3.1/js/dataTables.fixedHeader.min.js', ['depends' => [AdminAsset::class]]);

// <!-- Include colResizable JS for column resizing -->
$this->registerJsFile('https://cdn.jsdelivr.net/gh/jeffreydwalter/ColReorderWithResize@9ce30c640e394282c9e0df5787d54e5887bc8ecc/ColReorderWithResize.js', ['depends' => [AdminAsset::class]]);


$this->registerJsFile('https://code.jquery.com/ui/1.13.2/jquery-ui.min.js', ['depends' => [AdminAsset::class]]);
$this->registerJsFile('https://code.jquery.com/ui/1.9.2/jquery-ui.js', ['depends' => [AdminAsset::class]]);
// $this->registerJsFile('https://unpkg.com/ag-grid-community/dist/ag-grid-community.min.js', ['depends' => [AdminAsset::class]]);

$this->registerJsFile('https://cdn.jsdelivr.net/npm/ag-grid-community/dist/ag-grid-community.min.noStyle.js', ['depends' => [AdminAsset::class]]);

$this->registerJsFile('@web/thememain/js/custom-scripts.js', ['depends' => [AdminAsset::class]]);

?>