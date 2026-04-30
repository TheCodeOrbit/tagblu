<?php

use yii\helpers\Url;
use yii\helpers\Html;
use backend\assets\AdminAsset;


AdminAsset::register($this);
$this->title = Yii::t('app', 'Table List');
//Add DataTables CSS CDN

$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css', ['depends' => [AdminAsset::class]]);
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.0/css/all.min.css', ['depends' => [AdminAsset::class]]);

$this->registerCssFile('@web/thememain/css/ag-theme-alpine.css', ['depends' => [AdminAsset::class]]);

$leadsByStatus = [];
foreach ($leadInformation as $lead) {
  $leadsByStatus[$lead->leadstatus][] = $lead;
}
?>


<style>
  .kanban-board {
    display: flex;
    gap: 15px;
    padding: 20px;
  }

  .kanban-list {
    border: 1px solid #ccc;
    border-radius: 5px;
    width: 250px;
    background: #f9f9f9;
    display: flex;
    flex-direction: column;
  }

  .kanban-header {
    background: #007bff;
    color: #fff;
    padding: 10px;
    text-align: center;
    border-bottom: 1px solid #ccc;
  }

  .kanban-items {
    padding: 10px;
    min-height: 200px;
  }

  .kanban-item {
    background: #fff;
    padding: 10px;
    margin-bottom: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    cursor: move;
  }

  .kanban-container {
    display: flex;
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



  /* filterByNameModel the modal container */
  #filterByNameModel {

    padding-left: 0;
    margin: 117px auto;
    width: 198%;
    max-height: 85vh;

  }

  /* Modal dialog */
  #filterByNameModel .modal-dialog {
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  /* Modal content styling */
  #filterByNameModel .modal-content {
    width: 100%;
    max-height: 100%;
    /* Ensures modal content respects the height */
    display: flex;
    flex-direction: column;
    /* Make it column layout */
  }

  /* Modal body to enable scroll */
  #filterByNameModel .modal-body {
    max-height: calc(50vh - 100px);
    overflow-y: auto;
    /* Add vertical scroll */
    padding: 10px;
    margin-bottom: 10px;
  }

  /* Modal footer styling to keep it at the bottom */
  #filterByNameModel .modal-footer {
    position: sticky;
    bottom: 0;
    background-color: #fff;
    /* Ensure background visibility */
    padding: 10px;
  }

  .filed-div {
    background-color: #f8f8f8E2;
    padding: 12px;
    border: 1px solid white;
  }

  .search-input {
    transition: border-color 0.3s ease;
  }

  .search-input:focus {
    border-color: #007bff;
    /* Change to your desired highlight color */

    box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
    /* Adds a subtle shadow */
  }

  .btn {
    background-color: #007bff;
  }

  /* Styling for the filter box container */
  .filter-box {
    /* Visualization only, remove if needed */
    padding: 10px;
    display: flex;
    flex-direction: column;


  }

  /* Styling for dropdown and input fields */
  .filter-box .form-select,
  .filter-box .form-control {
    margin: 0;
    /* Remove extra margins */
    padding: 8px;
    /* Add some padding for readability */
    box-sizing: border-box;
    width: 100%;
  }

  /* Optional: Adjust spacing between the dropdown and input */
  .filter-box .form-select {
    margin-bottom: 5px;
    /* Space between dropdown and input */
  }


  .board-column-1 {
    background-color: #d9d9d9;
    width: 300px;
    padding: 15px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    height: 460px;
    overflow: scroll;
  }

  .board-column h3 {

    padding: 15px 0px;
    border-radius: 8px;
    color: white;
    text-align: center;
  }

  .card {
    background-color: white;
    border-radius: 8px;
    padding: 15px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    margin-bottom: 15px;
    position: relative;
  }

  .card h4 {
    margin-bottom: 10px;
    color: #333;
  }

  .card p {
    margin-bottom: 5px;
    color: #555;
    font-size: 14px;
  }

  .dropdown-btn {
    position: absolute;
    top: 10px;
    right: 10px;
    background-color: #f1f1f1;
    border: none;
    border-radius: 4px;
    padding: 5px;
    cursor: pointer;
    font-size: 12px;
  }

  .card-options {
    position: absolute;
    top: 40px;
    right: 10px;
    background-color: #fff;
    border-radius: 4px;
    padding: 5px;
    font-size: 12px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
    display: none;
    z-index: 10;
  }

  .card-options div {
    padding: 5px;
    cursor: pointer;
  }

  .card-options div:hover {
    background-color: #eee;
  }

  .bg-card {
    background-image: url("<?= Yii::getAlias('@web/thememain/images/bg-card.png') ?>");
    background-position: center;
    margin-left: -7px;
    background-size: cover;
    width: 320px;
  }

  .main-board-1 {
    padding: 20px;
    display: flex;
    overflow: scroll;
    align-items: flex-start;
    gap: 20px;
    width: 100%;
  }


  /* pagination css */
  /* Container styling */
  .pagination-container {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-top: 20px;
    justify-content: flex-end;
  }

  /* Dropdown styling */
  .results-per-page {
    font-size: 14px;
    color: #555;
  }

  .page-size-dropdown {
    padding: 5px 10px;
    font-size: 14px;
    border: 1px solid #ddd;
    border-radius: 4px;
  }

  /* Button styling */
  .pagination-button {
    background-color: #007bff;
    color: #fff;
    border: none;
    padding: 5px 10px;
    font-size: 14px;
    border-radius: 4px;
    cursor: pointer;
  }

  .pagination-button:hover {
    background-color: #0056b3;
  }

  /* Active page button */
  .pagination-buttons button {
    background-color: #e9ecef;
    color: #000;
    border: none;
    padding: 5px 10px;
    font-size: 14px;
    border-radius: 4px;
    cursor: pointer;
  }

  .pagination-buttons button.active {
    font-weight: bold;
    background-color: #007bff;
    color: white;
  }

  .pagination-buttons button:hover {
    background-color: #d6d6d6;
  }
</style>

<!-- open lead Add button -->
<div class="group-15">
  <div class="group-16">
    <button type="button" id="add-lead-btn" class="btn add-lead-btn" style="background-color: var(--color-primary) !important; color:white">+ Add</button>
  </div>
  <!-- <div class="group-17">
    <div class="overlap-16">
      <div class="text-wrapper-126">Import</div>
      <img class="lets-icons-import" src="https://c.animaapp.com/4Te5O9cu/img/lets-icons-import-light.svg" />
    </div>
  </div> -->



  <!-- <div class="frame-22">
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
  </div> -->

  <!-- Filter By Name Button -->

  <button class="filter-selector-btn" data-direction="right" id="filterSelectorButton" style="background: none; border: none; cursor: pointer;">
    <img class="typcn-filter filter-selector-btn" src="https://c.animaapp.com/4Te5O9cu/img/typcn-filter.svg"
      alt="filter Selector" />
  </button>


  <!-- Filter By Name Modal Structure -->

  <div class="modal fade " id="filterByNameModel" aria-modal="true" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content" style="width: 73%; height: 69%;">
        <div class="modal-header">
          <h4 class="modal-title">Filter Lead By</h4>
          <button type="button" class="btn-close fil-btn" aria-label="Close"></i></button>
        </div>
        <div class="modal-header">
          <div class="input-group mb-3 search-input" onclick="openfieldName()">
            <input
              type="text"
              class="form-control"
              placeholder="Search"
              aria-label="Search"
              onkeyup="filterFields(this.value)">
            <button class="btn" type="button" id="addFieldButton" style="background-color: var(--color-primary) !important; color:white">
              <span class="fa fa-plus"></span>
            </button>
          </div>
        </div>
        <div class="modal-body">
          <div id="field_name" style="display:none;">
            <?php foreach ($filed_name as $filed_names): ?>
              <div class="filed-div" onclick="openFilterBox('<?php echo $filed_names['fieldid']; ?>', '<?php echo $filed_names['fieldlabel']; ?>')"
                data-label="<?php echo strtolower($filed_names['fieldlabel']); ?>">
                <?php echo $filed_names['fieldlabel']; ?>
              </div>
            <?php endforeach; ?>
          </div>

          <!-- Container for the filter box (initially hidden) -->
          <div id="filterBox" class="filter-box" style="display:none;">
            <div class="field-label-row">
              <span id="filterFieldLabel"> </span>
              <button onclick="closeFilterBox()" class="close-button" style="margin-left: 188px;"><i class="fa fa-trash"></i></button>
            </div>
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
          <button type="button" id="filter-save" onclick="SaveFilter()" style="background-color: var(--color-primary) !important; color:white" class="btn">Save </button>
          <button type="button" id="apply-filter-by-name" onclick="applyFilter()" style="background-color: var(--color-primary) !important; color:white" class="btn">Apply Changes</button>
        </div>
      </div>
    </div>
  </div>
  <!-- End Filter By Name -->


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
    <div class="all-open-lead">
      <select id="filterLableName" class="form-select">
        <option value="all_open_lead">All Open Lead</option>
        <option value="all_lead">All Lead</option>
      </select>
    </div>



  </div>
  <img class="ep-arrow-down-8" src="https://c.animaapp.com/4Te5O9cu/img/ep-arrow-down-26.svg" />
</div>
</div>

<!-- end open lead Add button  -->




<!-- Add Modal Structure -->

<div class="modal fade" id="add-lead-modal" tabindex="-1" role="dialog" aria-labelledby="addLeadModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addLeadModalLabel">Add Lead1</h5>
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

        <!-- Hidden input field to store the selected lead IDs -->
        <input type="hidden" id="hiddenLeadIds" name="hiddenLeadIds" value="">
        <div id="selectedCountContainer">

        </div>

        <button id="exportButton" class="btn btn-primary" style="display: none;">Export</button>

        <button id="deleteButton" class="btn btn-danger" style="display: none;">Delete </button>

        <!-- Update Button -->
        <button type="button" id="updateButton" style="display: none;" class="btn btn-primary" data-toggle="modal" data-target="#updateModel">
          Update
        </button>

        <!-- Modal -->
        <div class="modal fade" id="updateModel" tabindex="-1" role="dialog" aria-labelledby="updateModelLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Bulk Update</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <!-- Dropdown to Select Field -->
                <div class="update-field-name">
                  <label for="updatefiled_names">Select Field to Update</label>
                  <select name="updatefiled_names" id="updatefiled_names" class="form-control">
                    <option value="">Select lead field</option>
                    <?php foreach ($filed_name as $filed_names): ?>
                      <option value="<?php echo $filed_names['fieldid']; ?>"><?php echo $filed_names['fieldlabel']; ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>

                <!-- Input Field (Dynamic) -->
                <div id="field-input-container" style="display: none; margin-top: 10px;">
                  <label id="field-label" for="field-input"></label>
                  <input type="text" id="field-input" class="form-control" placeholder="Enter value">
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary update-close-btn" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="confirmUpdateButton">Update</button>
              </div>
            </div>
          </div>
        </div>



        <!-- <div id="custom-pagination" style="display: inline;margin: 1000px;">
          <button id="prev-page" onclick="prevPage()">Previous</button>
          <span id="current-page">1</span> / <span id="total-pages">1</span>
          <button id="next-page" onclick="nextPage()">Next</button>
          <select id="page-size" onchange="changePageSize()">
            <option value="10">10</option>
            <option value="20">20</option>
            <option value="50">50</option>
          </select>
        </div> -->

        <!-- pagination -->




        <div id="myGrid" class="ag-theme-alpine"></div>



        <div id="custom-pagination" class="pagination-container">
          <label for="page-size" class="results-per-page">Results Per Page:</label>
          <select id="page-size" class="page-size-dropdown" ><!--onchange="changePageSize()" -->

            <option value="10">10</option>
            <option value="20">20</option>
            <option value="50">50</option>
            <option value="100">100</option>
          </select>

          <button id="first-page" class="pagination-button first-page" onclick="goToPage(1)">First</button>
          <div id="pagination-buttons" class="pagination-buttons"></div>
          <button id="last-page" class="pagination-button last-page"  onclick="goToPage(totalPages)">Last</button>
        </div>









      </div>
      <!-- End of Table -->


      <!-- Kanban Board -->

      <div class="main-board-1 kanban-view">
        <?php foreach ($leadStatuses as $status): ?>
          <div class="board-column" data-status-id="<?= $status->leadstatusid ?>" ondrop="drop(event)" ondragover="allowDrop(event)">
            <h3 class="bg-card">
              <?= Html::encode($status->leadstatus_value) ?>
              (<?= !empty($leadsByStatus[$status->leadstatusid]) ? count($leadsByStatus[$status->leadstatusid]) : 0; ?>)
            </h3>
            <div class="board-column-1">
              <?php if (!empty($leadsByStatus[$status->leadstatusid])): ?>
                <?php foreach ($leadsByStatus[$status->leadstatusid] as $lead): ?>
                  <div class="card" id="lead-<?= $lead->leadid ?>" draggable="true" ondragstart="drag(event)" data-lead-id="<?= $lead->leadid ?>">
                    <h4><?= Html::encode($lead->firstname . ' ' . $lead->lastname) ?> (#<?= Html::encode($lead->lead_no) ?>)</h4>
                    <p><strong>First Name:</strong> <?= Html::encode($lead->firstname) ?></p>
                    <p><strong>Last Name:</strong> <?= Html::encode($lead->lastname) ?></p>
                    <p><strong>Owner:</strong> <?= Html::encode($lead->firstname) ?></p>
                    <p><strong>Pipeline Stage:</strong> <?= Html::encode($status->leadstatus_value) ?></p>
                    <p><strong>Company Name:</strong> <?= Html::encode($lead->leadname) ?></p>
                    <p><strong>Phone Number:</strong> <?= Html::encode($lead->phone ?: 'N/A') ?></p>
                    <p><strong>Emails:</strong> <?= Html::encode($lead->website ?: 'N/A') ?></p>
                    <button class="dropdown-btn" onclick="toggleDropdown(this)">
                      <i class="fa-solid fa-ellipsis-vertical"></i>
                    </button>
                    <div class="card-options">
                      <div>Open in new tab</div>
                      <div>Edit</div>
                      <div>Reassign</div>
                      <div>Convert</div>
                      <div>Clone</div>
                      <div>Archive</div>
                    </div>
                  </div>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>



    </div>

    <!-- end kanban -->




  </div>

</div>



</div>


<?php


// $this->registerJsFile('@web/thememain/js/ag-grid-community.min.js', ['depends' => [AdminAsset::class]]);

$this->registerJsFile('https://unpkg.com/ag-grid-community/dist/ag-grid-community.min.js', ['depends' => [AdminAsset::class]]);



$this->registerJsFile('@web/thememain/js/custom-scripts.js', ['depends' => [AdminAsset::class]]);

?>