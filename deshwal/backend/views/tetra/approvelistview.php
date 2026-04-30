<?php

use yii\helpers\Url;
use yii\helpers\Html;
use backend\assets\AdminAsset;
use yii\db\Query;

AdminAsset::register($this);
$this->title = Yii::t('app', 'Approve ' . $TabLabel);

// $this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css', ['depends' => [AdminAsset::class]]);

$this->registerCssFile('@web/thememain/css/ag-grid.css', ['depends' => [AdminAsset::class]]);
$this->registerCssFile('@web/thememain/css/ag-theme-alpine.css', ['depends' => [AdminAsset::class]]);
$this->registerCssFile('@web/thememain/css/listview.css', ['depends' => [AdminAsset::class]]);


$url = Url::to(['create']);
$uploadUrl = Url::to(['csvupload']);
$baseUrl = Yii::$app->HomeUrl;

$csrfTokenName = Yii::$app->request->csrfParam;  // This replaces csrfTokenName
$csrfToken = Yii::$app->request->csrfToken;      // Get the CSRF token itself 

$leadsByStatus = [];

if (!empty($leadInformation)) {
  foreach ($leadInformation as $lead) {

    $leadsByStatus[$lead[$kanbnacolumn]][] = $lead;
  }
}

?>


<?php
// Retrieve the 'sourcemodule' parameter from the URL

$sourceModule = Yii::$app->request->get('sourcemodule');
$sourceId = Yii::$app->request->get('sourceid');

if (isset($sourceModule)) {


  $query = new Query();
  $tabData = $query->select('name,tablabel')
    ->from('tab')
    ->where(['tabid' => $sourceModule])
    ->one();
}


// $tableName = 'leadinformation';

// // Get the table schema
// $tableSchema = Yii::$app->db->schema->getTableSchema($tableName);

// // Check if the table exists and has a primary key
// if ($tableSchema !== null && !empty($tableSchema->primaryKey)) {
//     echo "Primary key column(s) for table '$tableName': " . implode(', ', $tableSchema->primaryKey);
//     exit;
// } else {
//     echo "The table '$tableName' does not exist or does not have a primary key.";
//     exit;
// }

?>
<style type="text/css">
  /* Default clipping for all cells */
  /* .ag-cell {
    white-space: nowrap !important;
    overflow: hidden  !important;
    text-overflow: ellipsis !important;
  }*/
  */

  /* Wrapping class */
  /* .ag-cell-wrap-dp {
    white-space: normal !important;
    overflow-wrap: break-word !important;
    word-wrap: break-word !important;
    word-break: break-word !important;
  }  */
  /* Button styling */
  .btn-1 {
    background-color: #007bff;
    color: white;
    border: none;
    padding: 10px 20px;
    cursor: pointer;
    border-radius: 4px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
  }

  /* Popup styling */
  .popup {
    display: none;
    /* Initially hidden */
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    justify-content: center;
    align-items: center;
    z-index: 1000;
  }

  .popup-content {
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    text-align: center;
    width: 300px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    position: relative;
  }

  .close-btn {
    position: absolute;
    top: 10px;
    right: 10px;
    cursor: pointer;
    font-size: 18px;
    color: #888;
  }
  .remove-top{top:0px;}
  a{
   color: var(--color-primary) !important;
  }
  .pagination-in-center{
    display: flex; justify-content: center;
  }
</style>
<input type="hidden" value="<?php echo $ModuleName; ?>" id="module" name="module" />
<input type="hidden" name="csrfToken" id="csrfToken" value="<?= $csrfToken; ?>">
<input type="hidden" name="csrfTokenName" id="csrfTokenName" value="<?= $csrfTokenName; ?>">
<div class="page-content">
  <div class="records table-responsiv">
    <div class="record-header">
      <div class="add">
        <img src="<?= $baseUrl; ?>/thememain/img/module-icon/<?= $ModuleName; ?>.png" class=" head-img remove-top" >
        <span class="sm-modname"><?= $TabLabel; ?> / Pending for Approval</span>
        <br>

        <?php
        // if (!empty($allfilter)) {
          ?>
          <!-- <select name="filterselectbox" id="filterselectbox"> -->
            <?php
            // foreach ($allfilter as $key => $value) {
            //   if ($defaultfilter['id'] == $value['id'])
            //     $sel = 'selected';
            //   else
            //     $sel = '';
              ?>
              <!-- <option value="<?php //echo $value['id']; ?>" <?php //echo $sel; ?>><?php //echo $value['filter_name']; ?></option> -->
              <?php
            // }
            ?>
          <!-- </select> -->
          <?php
        // } else {
          ?>

          <!-- <select name="filterselectbox" id="filterselectbox"></select> -->
          <?php
        // } ?>
      <!-- show source module name -->
        <?php if (isset($tabData['name'])) { ?>
          <div class="reletedname" style="margin-left:12px">
            Related To: <a
              href="<?= $baseUrl; ?><?= isset($tabData['name']) ? $tabData['name'] : ''; ?>/detail?Record=<?= $sourceId; ?>">

              <span class="sourcemodule"><?= isset($srcheaderfullname) ? $srcheaderfullname : ''; ?></span>
            </a>
          </div>
          <?php
        } ?>


      </div>

      <div class="browse">
        
       <!-- commented by ptpatel on date 31-03-25 -->
        <!-- <button class="btn" style="background: none;border: 1px solid var(--color-primary) !important; color: #585858;font-size: 12px;"> <img src="<?= $baseUrl; ?>/thememain/img/List-view.png" style="width: 37px;"> List view </button> -->
        <!-- <img id="filterSelectorButton" src="<?= $baseUrl; ?>/thememain/img/typcn-filter.svg" class="filter-selector-btn"
          style="cursor: pointer;" title="Filter Selector">
        <img src="<?= $baseUrl; ?>/thememain/img/fluent-column-triple-edit-24-regular.svg" id="columnSelectorButton"
          class="col-selector-btn" style="cursor: pointer;" title="Column Selector"> -->
        <?php
        
            $url = "list";
          ?>
          <a href="<?= $url; ?>" title="Add New"><button>Back To Listing</button></a>
          
      </div>
    </div>
  </div>
</div>

<!-- Filter By Name Modal Structure -->

<div class="modal fade " id="filterByNameModel" aria-modal="true" role="dialog">
  <?php
  $showdropdown = 0;
  if (!empty($defaultfiltercondition)) {
    // print_r($defaultfiltercondition);
    // lter_id] => 1 [fieldid] => 8 [fieldlabel] => First Name [filteroperator] => Equals [userinput] => Sai [userid] => 1 [deleted] => 0 ) 
    $filterlabel = $defaultfiltercondition['fieldlabel'];
    $fieldid = $defaultfiltercondition['fieldid'];
    $filter_id = $defaultfiltercondition['filter_id'];
    $filteroperator = $defaultfiltercondition['filteroperator'];
    $userinput = $defaultfiltercondition['userinput'];
    $filterdisplay = "block";
    $filterFieldName = $defaultfiltercondition['columnname'];
    $filterFielduitype = $defaultfiltercondition['uitype'];
    $filterFieldtablename = $defaultfiltercondition['tablename'];
    if ($filterFielduitype == 8) {
      //get values from picklist
      $showdropdown = 1;
    }
  } else {
    $filterlabel = '';
    $fieldid = '';
    $filter_id = '';
    $filteroperator = '';
    $userinput = '';
    $filterdisplay = "none";
    $filterFieldName = '';
    $filterFielduitype = '';
    $filterFieldtablename = '';
  }
  ?>
  <div class="modal-dialog">
    <div class="modal-content" style="width: 73%; height: 69%;">
      <div class="modal-header">
        <h4 class="modal-title">Filter <?= $TabLabel; ?> By </h4>
        <button type="button" class="btn-close fil-btn" aria-label="Close"></button>
      </div>
      <div class="modal-header">
        <div class="input-group mb-3">
          <input type="text" class="form-control filtercolumnvalues" placeholder="Search" aria-label="Search">
          <button class="btn btn-outline-secondary" onclick="openfieldName()" type="button" id="addFieldButton">
            <span class="fa fa-plus"></span>
          </button>
        </div>
      </div>
      <div class="modal-body">
        <div id="field_name" style="display:none">
          <?php foreach ($filed_name as $filed_names):

            ?>
            <div class="filed-div" data-label="<?php echo $filed_names['fieldlabel']; ?>"
              onclick="openFilterBox('<?php echo $filed_names['fieldid']; ?>', '<?php echo $filed_names['fieldname']; ?>','<?php echo $filed_names['fieldlabel']; ?>','<?php echo $filed_names['uitype']; ?>','<?php echo $filed_names['tablename']; ?>')">

              <?php echo $filed_names['fieldlabel']; ?>

            </div>
          <?php endforeach; ?>
        </div>

        <!-- Container for the filter box (initially hidden) -->
        <div id="filterBox" class="filter-box" style="display:<?= $filterdisplay; ?>;" data-field-id="<?= $fieldid; ?>">
          <div class="field-label-row">
            <div class="filterfieldlabel">
              <span id="filterFieldLabel"><?= $filterlabel; ?></span>
            </div>
            <div class="filtertrashbox">
              <i onclick="closeFilterBox()" class="fa fa-trash close-button" style="margin-left: 188px;"></i>
            </div>
          </div>

          <input type="hidden" id="filterFieldName" value="<?= $filterFieldName; ?>">
          <input type="hidden" id="filterFielduitype" value="<?= $filterFielduitype; ?>">
          <input type="hidden" id="filterFieldtablename" value="<?= $filterFieldtablename; ?>">
          <input type="hidden" id="filterId" value="<?= $filter_id; ?>">
          <!-- Dropdown for selecting comparison operators -->
          <select id="filterOperator" class="form-select">
            <option value="Equals" <?php if ($filteroperator == "Equals")
              echo "Selected"; ?>>Equals</option>
            <option value="Not_Equals" <?php if ($filteroperator == "Not_Equals")
              echo "Selected"; ?>>Not Equals</option>
            <option value="Contains" <?php if ($filteroperator == "Contains")
              echo "Selected"; ?>>Contains</option>
            <option value="Not_Contains" <?php if ($filteroperator == "Not_Contains")
              echo "Selected"; ?>>Not Contains
            </option>
            <option value="In" <?php if ($filteroperator == "In")
              echo "Selected"; ?>>In</option>
            <option value="Not_In" <?php if ($filteroperator == "Not_In")
              echo "Selected"; ?>>Not In</option>
            <option value="is_Empty" <?php if ($filteroperator == "is_Empty")
              echo "Selected"; ?>>is Empty</option>
            <option value="is_Not_Empty" <?php if ($filteroperator == "is_Not_Empty")
              echo "Selected"; ?>>is Not Empty
            </option>
            <option value="Begins_with" <?php if ($filteroperator == "Begins_with")
              echo "Selected"; ?>>Begins With
            </option>
          </select>
          <?php
          if ($showdropdown == 1) {
            echo $defaultfiltercondition['opt'];
          } else {
            ?>
            <input type="text" class="form-control" id="filterValue" placeholder="Enter value"
              style="display:<?= $filterdisplay; ?>;" value="<?= $userinput; ?>" />
            <?php
          }
          ?>

        </div>
      </div>

      <div class="modal-footer">
        <button type="button" id="filter-save-as" class="btn btn-primary">Save As </button>
        <button type="button" id="filter-save" onclick="SaveFilter()" class="btn btn-primary">Save </button>
        <button type="button" id="apply-filter-by-name" onclick="applyFilter()" class="btn btn-primary">Apply</button>
      </div>
    </div>
  </div>
</div>
<!-- End Filter By Name -->

<!-- Save as modal -->
<div class="modal fade " id="saveAsFilterModel" aria-modal="true" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content" style="width: 73%; height: 69%;">
      <div class="modal-header">

        <h4 class="modal-title">New <?= $Tabname; ?> filter </h4>
        <button type="button" class="btn-close savasbtn-close-btn" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <label class="control-label">Filter Name </label>
        <input type="text" id="filter_name" value="" class="form-control">
        <label>Description </label>
        <textarea id="description"></textarea>

      </div>
      <div class="modal-footer">

        <button type="button" id="saveasbutton" class="btn btn-primary">Save</button>
      </div>
    </div>
  </div>
</div>

<!-- Save as modal  -->



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
              <input type="checkbox" name="column[]" data-field_id="<?= $column['fieldid'] ?>"
                data-columnname="<?= $column['columnname'] ?>" value="<?= $column['fieldid'] ?>" <?= $column['visible'] ? 'checked' : '' ?>>
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
<!-- Add Modal Structure -->

<div class="modal fade" id="add-lead-modal" tabindex="-1" role="dialog" aria-labelledby="addLeadModalLabel"
  aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">



    </div>
  </div>
</div>

<!-- end add model -->
<div class="select-1">
  <div class="container-d">
   



    <!-- Table -->
    <!-- <div class="table-list">
      <div id="myGrid" class="ag-theme-alpine"></div>

      <div id="custom-pagination" class="pagination-container">

        <span id="pagination-info" class="pagination-info" style="flex: auto;"></span>


        <label for="page-size" class="results-per-page">Results Per Page:</label>
        <select id="page-size" class="page-size-dropdown" onchange="changePageSize()">

          <option value="10">10</option>
          <option value="20">20</option>
          <option value="50">50</option>
          <option value="100">100</option>
        </select>

        <button id="first-page" class="pagination-button" onclick="goToPage(1)">First</button>
        <div id="pagination-buttons" class="pagination-buttons"></div>
        <button id="last-page" class="pagination-button" onclick="goToPage(totalPages)">Last</button>
      </div> -->



      <!-- pagination -->


    <!-- </div> -->
    <!-- End of Table -->
     <!-- below line added by ptpatel on date 29-03-25 -->
    <div id="table-container"></div>

  </div>

 
</div>
</div>




<script> 
//added by ptpatel on date 29-03-25
const approvelistdetailUrl= `<?= Url::to(['detail']) ?>`;
</script>

<?php



$this->registerJs("
    // $('#add-lead-btn').click(function() {
    //     $('#add-lead-modal').modal('show');
    // });
    

// applyFilter();

 
");
$this->registerJsFile('@web/thememain/js/ag-grid-community.min.js', ['depends' => [AdminAsset::class]]);

$this->registerJsFile('@web/thememain/js/custom.js', ['depends' => [AdminAsset::class]]);
// below line added by ptpatel on date 29-03-25
$this->registerJsFile('@web/thememain/js/approvelistview.js');
?>

