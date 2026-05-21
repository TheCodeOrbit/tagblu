<?php

use app\models\Leadinformation;
use app\models\Quotes;
use app\models\Sourcingdeal;
use backend\components\SvgRenderHelper;
use yii\helpers\Url;
use yii\helpers\Html;
use backend\assets\AdminAsset;
use backend\models\AccessCheck;
use yii\db\Query;


AdminAsset::register($this);
$this->title = Yii::t('app', 'Add ' . $TabLabel);

$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css', ['depends' => [AdminAsset::class]]);

$this->registerCssFile('@web/thememain/css/ag-grid.css', ['depends' => [AdminAsset::class]]);
$this->registerCssFile('@web/thememain/css/ag-theme-alpine.css', ['depends' => [AdminAsset::class]]);
$this->registerCssFile('@web/thememain/css/listview.css', ['depends' => [AdminAsset::class]]);
// $this->registerCssFile('@web/thememain/css/csvfileupload.css', ['depends' => [AdminAsset::class]]);

// echo "<pre>";echo("create-".$createpermission);
// echo("edit-".$editpermission);
// echo("delete-".$deletepermission);
// echo("list-".$listpermission);
// echo("approve-".$approvepermission);
// echo("import-".$importpermission);
// echo("export-".$exportpermission);die;
$url = Url::to(['create']);
$uploadUrl = Url::to(['csvupload']);
$baseUrl = Yii::$app->HomeUrl;

$csrfTokenName = Yii::$app->request->csrfParam;  // This replaces csrfTokenName
$csrfToken = Yii::$app->request->csrfToken;      // Get the CSRF token itself 

$leadsByStatus = [];

if (!empty($leadInformation)) {
  foreach ($leadInformation as $lead) {

    // $leadsByStatus[$lead[$kanbnacolumn]][] = $lead;
    // code added by ptpatel on date 07-04-25
    if (isset($lead[$kanbnacolumn])) {
      foreach ($eachStatusCounts as $status) {
        if (isset($status[$kanbanstatusvalue]) && $lead[$kanbnacolumn] == $status[$kanbanstatusvalue]) {

          $leadsByStatus[$lead[$kanbnacolumn]]['total'] = isset($status['total']) ? $status['total'] : 0;
          $leadsByStatus[$lead[$kanbnacolumn]]['id'] = isset($status[$kanbanstatusid]) ? $status[$kanbanstatusid] : 0;
          $leadsByStatus[$lead[$kanbnacolumn]][] = $lead;
        }
      }
    }
    // ended code added by ptpatel on date 07-04-25
  }
}
// echo "<pre>";print_r($leadInformation);die;
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
<style type="text/css" nonce="<?= Yii::$app->params['cspNonce'] ?? '' ?>">
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

  .approve-lead-btn {
    background-color: #FFB13C !important;
    color: white;
    border: none;
    padding: 10px 20px;
    cursor: pointer;
    border-radius: 4px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
  }
</style>
<link href="<?= $baseUrl; ?>thememain/css/multilist-dd.css" rel="stylesheet">
<input type="hidden" value="<?php echo $ModuleName; ?>" id="module" name="module" />
<input type="hidden" name="csrfToken" id="csrfToken" value="<?= $csrfToken; ?>">
<input type="hidden" name="csrfTokenName" id="csrfTokenName" value="<?= $csrfTokenName; ?>">
<div class="list-view-header">
  <div class="lv-title-group">
    <div class="lv-module-icon">
      <?= SvgRenderHelper::renderIcon($ModuleName . '.svg', true); ?>
    </div>
    <div class="lv-title-content">
      <span class="lv-module-name"><?= $TabLabel; ?></span>
      <div class="lv-filter-select">
        <?php if (!empty($allfilter)): ?>
          <select name="filterselectbox" id="filterselectbox">
            <?php foreach ($allfilter as $value): 
              $sel = ($defaultfilter['id'] == $value['id']) ? 'selected' : ''; ?>
              <option class="userid_<?= $value['userid']; ?>" data-userid="<?= $value['userid'] ?>" value="<?= $value['id']; ?>" <?= $sel; ?>>
                <?= $value['filter_name']; ?>
              </option>
            <?php endforeach; ?>
          </select>
        <?php else: ?>

          <select name="filterselectbox" id="filterselectbox"></select>
        <?php endif; ?>
      </div>
    </div>
    <!-- Related To Info -->
    <?php if (isset($tabData['name'])): ?>
      <div class="reletedname" style="margin-left: 20px; font-size: 0.8rem; color: var(--text-secondary);">
            Related To: <a
              href="<?= $baseUrl; ?><?= isset($tabData['name']) ? $tabData['name'] : ''; ?>/detail?Record=<?= $sourceId; ?>">

              <span class="sourcemodule"><?= isset($srcheaderfullname) ? $srcheaderfullname : ''; ?></span>
            </a>
          </div>
    <?php endif; ?>

    <!-- Widget Filter Hidden Input -->
    <?php if (!empty($widgetfilterid)): ?>
      <input type="hidden" name="widget_filter_id" id="widget_filter_id" value="<?= $widgetfilterid; ?>" />
    <?php endif; ?>
  </div>

  <div class="lv-action-group">
    <!-- Bulk Status Update (Inventory Only) -->
    <?php if ($TabId == 33 && $editpermission == 1): ?>
      <div class="form-group required-field form-field-cst" style="margin-bottom: 0;">
        <input type="text" id="searchTagInput" class="form-control sm" placeholder="Search Tag..." style="height: 40px;" />
      </div>
      <button class="btn-premium outline" id="btnBulkStatusUpload">Bulk Status Update</button>
    <?php endif; ?>

    <?php if ($TabId != 33): ?>
      <!-- Filter & Column Selectors -->
      <span id="filterSelectorButton" class="lv-icon-btn" title="Filter Selector">
        <?= SvgRenderHelper::renderIcon('typcn-filter.svg'); ?>
      </span>
      <span id="columnSelectorButton" class="lv-icon-btn col-selector-btn" title="Column Selector">
        <?= SvgRenderHelper::renderIcon('fluent-column-triple-edit-24-regular.svg'); ?>
      </span>
    <?php endif; ?>

    <!-- Import / Export Icon Buttons -->
    <?php if ($isimportallowed == 1 && $importpermission == 1): ?>
      <?php if ($TabId != 33 && $TabId == 7): ?>
        <span class="lv-icon-btn" id="importButton" title="Import">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
        </span>
      <?php elseif ($TabId != 7): ?>
        <span class="lv-icon-btn" id="improveimportButton" title="Import">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
        </span>
      <?php endif; ?>
    <?php endif; ?>

    <?php if ($exportpermission == 1): ?>
      <span class="lv-icon-btn" id="exportAllButton" title="Export All">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
      </span>
    <?php endif; ?>

    <!-- Add Button -->
    <?php if ($createpermission == 1): ?>
      <?php 
        $addUrl = "create";
        if (isset($_GET['sourceid'], $_GET['sourcemodule'])) {
          $addUrl .= "?sourcemodule=" . filter_var($_GET['sourcemodule'], FILTER_VALIDATE_INT) . "&sourceid=" . filter_var($_GET['sourceid'], FILTER_VALIDATE_INT);
        }
        
        $showAdd = true;
        if (isset($_GET['sourcemodule']) && $_GET['sourcemodule'] == 51) {
          $sourcing_deal_stage = Sourcingdeal::find()->select("stage")->where(['sourcingdeal_id' => $_GET['sourceid']])->scalar();
          if (($sourcing_deal_stage == 14 || $sourcing_deal_stage == 27) || $sourcing_deal_stage != 10) $showAdd = false;
        } elseif ($TabId == 33 || $TabId == 18) {
          $showAdd = false;
        }

        if ($showAdd):
      ?>
        <a href="<?= $addUrl; ?>" class="btn-premium primary">
          <span>+ Add New</span>
        </a>
      <?php elseif ($layout != 'multiple' && $layout != 'single'): ?>
        <button id="add-lead-btn" class="btn-premium primary">+ Add New</button>
      <?php endif; ?>
    <?php endif; ?>

    <!-- Approve Button -->
    <?php if ($TabId == '7' && $approvepermission == 1): 
      $hasApprove = Leadinformation::find()->where(['ownerid' => Yii::$app->user->id, 'leadstatus' => 4])->exists();
      if ($hasApprove): ?>
        <a href="approvelist" class="btn-premium outline" style="border-color: #FFB13C; color: #FFB13C; text-decoration: none;">
          Approve Lead
        </a>
      <?php endif; ?>
    <?php endif; ?>
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
    $filterFielduserid = $defaultfiltercondition['userid'];
    $filterFieldtablename = $defaultfiltercondition['tablename'];
    if ($filterFielduitype == 8 || $filterFielduitype == 22) {
      //get values from picklist
      $showdropdown = 1;
    }
  } else {
    $filterlabel = '';
    $fieldid = '';
    $filter_id = '';
    $filteroperator = '';
    $filterFielduserid = '';
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
      <div class="modal-header d-flex flex-column align-items-start gap-2">

        <?php if (isset($filterFielduserid) && $filterFielduserid != 1): ?>
          <a id="deleteCustomFilterBtn"
            class="text-danger small fw-semibold text-decoration-none"
            style="cursor:pointer; display:none;">
            <i class="fa fa-trash me-1"></i> Delete custom filter
          </a>
        <?php endif; ?>

        <div class="input-group input-group-sm filter-search-group w-100">
          <input type="text" class="form-control filtercolumnvalues" placeholder="Search" aria-label="Search">
          <button class="btn btn-primary filterbtn" type="button" id="addFieldButton">
            <span class="fa fa-plus"></span>
          </button>
        </div>

      </div>

      <div class="modal-body">
        <div id="field_name" style="display:none">
          <?php foreach ($filed_name as $filed_names):

          ?>
            <div class="filed-div" data-id="<?= $filed_names['fieldid'] ?>" data-name="<?= $filed_names['fieldname'] ?>"
              data-label="<?= $filed_names['fieldlabel'] ?>" data-uitype="<?= $filed_names['uitype'] ?>"
              data-tablename="<?= $filed_names['tablename'] ?>">

              <?php echo $filed_names['fieldlabel']; ?>

            </div>
          <?php endforeach; ?>
        </div>
        <script nonce="<?= Yii::$app->params['cspNonce'] ?>">
          document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.filed-div').forEach(function(el) {
              el.addEventListener('click', function() {
                const id = el.dataset.id;
                const name = el.dataset.name;
                const label = el.dataset.label;
                const uitype = el.dataset.uitype;
                const tablename = el.dataset.tablename;

                openFilterBox(id, name, label, uitype, tablename);
              });
            });
          });
        </script>

        <!-- Container for the filter box (initially hidden) -->
        <div id="filterBox" class="filter-box" style="display:<?= $filterdisplay; ?>;" data-field-id="<?= $fieldid; ?>">
          <div class="field-label-row">
            <div class="filterfieldlabel">
              <span id="filterFieldLabel"><?= $filterlabel; ?></span>
            </div>
            <div class="filtertrashbox  d-flex align-items-center mb-1">

              <i onclick="closeFilterBox()" class="fa fa-trash close-button faclose" style="margin-left: 188px;"></i>
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
        <button type="button" id="filter-save" class="btn btn-primary">Save </button>
        <button type="button" id="apply-filter-by-name" class="btn btn-primary">Apply</button>
        <!-- onclick="applyFilter()" onclick="SaveFilter()"-->
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
        <?php foreach ($columnselector as $column): ?>
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
<!-- Modern Bulk Actions Bar -->
<div class="bulkactions-wrap bulkactions tr-hidden">
  <input type="hidden" id="hiddenLeadIds" name="hiddenLeadIds" value="">
  <div class="bulk-info">
    <span class="leads-selected">0</span> <?= $TabLabel; ?> Selected 
    <a href="javascript:void(0)" onclick="unselectAll()" style="color: var(--color-primary); margin-left: 10px; font-size: 0.8rem; text-decoration: none; font-weight: 600;">Unselect All</a>
  </div>
  <div class="bulk-btns">
    <?php if ($editpermission == 1 && $TabId != 33): ?>
      <button class="btn-bulk" id="updateButton">
        <img src="<?= $baseUrl; ?>/thememain/img/update.png"> Update
      </button>
    <?php endif; ?>
    
    <?php if ($exportpermission == 1): ?>
      <button class="btn-bulk" id="exportButton">
        <img src="<?= $baseUrl; ?>/thememain/img/upload.png"> Export
      </button>
    <?php endif; ?>
    
    <?php if ($deletepermission == 1 && $TabId != 33): ?>
      <button class="btn-bulk delete" id="deleteButton">
        <img src="<?= $baseUrl; ?>/thememain/img/Archive.png"> Archive
      </button>
    <?php endif; ?>
  </div>
</div>


<?php if ($listpermission == 1): ?>
  <div class="table-list-wrap">
      <!-- Table -->
      <!-- id="tablelist"  added to hide show import page view-->
      <div class="table-list" id="tablelist">
        <div id="myGrid" class="ag-theme-alpine"></div>

        <div id="custom-pagination" class="pagination-container">
          <span id="selection-info" class="selection-info"></span>
          <span id="pagination-info" class="pagination-info" style="flex: auto;"></span>

          <label for="page-size" class="results-per-page">Results:</label>
          <select id="page-size" class="page-size-dropdown">
            <option value="1000">1000</option>
            <option value="2000">2000</option>
            <option value="3000">3000</option>
            <option value="5000">5000</option>
            <option value="10000">10000</option>
          </select>

          <button id="first-page" class="pagination-btn first-page">Previous</button>
          <div id="pagination-buttons" class="pagination-buttons"></div>
          <button id="last-page" class="pagination-btn last-page">Next</button>
        </div>
      </div>

      <div id="header-dropdown-portal"></div>
      <!-- End of Table -->
    </div> <!-- Close table-list-wrap -->
<?php endif; ?>

    <!-- below div is added for view import functionality -->
    <div id="improvedimportdiv"></div>

    <!-- Kanban Board -->
    <?php
    if (isset($leadStatuses)) { ?>
      <div class="main-board-1 kanban-list">
        <?php
        foreach ($leadStatuses as $key => $status):
        ?>
          <div class="board-column" data-field-id="<?= $kanbancolumn['fieldname'] ?>"
            data-status-id="<?= $status[$kanbanstatusid] ?>" data-tbl-id="<?= $kanbancolumn['tablename']; ?>"
            ondrop="drop(event)" ondragover="allowDrop(event)">
            <h3 class="bg-card">
              <?= Html::encode($status[$kanbanstatusvalue]) ?>
              <!-- (<?= !empty($leadsByStatus[$status[$kanbanstatusvalue]]) ? count($leadsByStatus[$status[$kanbanstatusvalue]]) : 0; ?>) -->
              <!-- code added by ptpatel on date 07-04-25 -->
              (
              <?= !empty($leadsByStatus[$status[$kanbanstatusvalue]]) ? $leadsByStatus[$status[$kanbanstatusvalue]]['total'] : 0; ?>)
              <?php if (isset($leadsByStatus[$status[$kanbanstatusvalue]]['total'])) {
                unset($leadsByStatus[$status[$kanbanstatusvalue]]['total']);
              } ?>
              <!-- end code added by ptpatel on date 07-04-25 -->
            </h3>
            <div class="board-column-1"
              id="<?= !empty($leadsByStatus[$status[$kanbanstatusvalue]]) ? $leadsByStatus[$status[$kanbanstatusvalue]]['id'] : 0; ?>"
              data-startkanban-row="0" data-page="1">
              <?php if (isset($leadsByStatus[$status[$kanbanstatusvalue]]['id'])) {
                unset($leadsByStatus[$status[$kanbanstatusvalue]]['id']);
              } ?>
              <?php if (!empty($leadsByStatus[$status[$kanbanstatusvalue]])):
                $i = 0; ?>
                <?php foreach ($leadsByStatus[$status[$kanbanstatusvalue]] as $lead):
                  // print_r($leadsByStatus[$status[$kanbanstatusvalue]]);
                ?>

                  <!-- <div class="card" id="lead-<?= $lead['RecordId'] ?>" draggable="true" ondragstart="drag(event)"
                    data-lead-id="<?= $lead['RecordId'] ?>"> -->
                  <!-- code added by ptpatel on date 07-04-25 to remove drag and drop functionality -->
                  <div class="card" id="lead-<?= $lead['RecordId'] ?>">
                    <!-- <h4>< (!empty($lead['firstname']) && !empty($lead['lastname']))?Html::encode($lead['firstname'] . ' ' . $lead['lastname']):'' ?>
                      (#< (!empty($lead['lead_no'])) ? Html::encode($lead['lead_no']) : '' ?>)</h4> -->

                    <?php
                    $i = 0;
                    // echo "<pre>";
                    // print_r($ColumnList);
                    foreach ($leadsByStatus[$lead[$kanbnacolumn]][$i] as $kval => $lval) {
                      if ($kval != 'RecordId') {
                    ?>
                        <!-- code added by ptpatel to show Labels  on date 07-04-25-->
                        <!-- <p><strong><?= $kval; ?></strong>: <?= $lval; ?></p> -->
                        <p><strong><?= isset($ColumnList[$kval]) ? $ColumnList[$kval] . ": " : ""; ?></strong>
                          <?= isset($ColumnList[$kval]) ? $lval : ""; ?></p>
                    <?php
                      }
                    }
                    $i++
                    ?>


                    <button class="dropdown-btn ddlstbtn" onclick="toggleDropdown(this)">
                      <i class="fa-solid fa-ellipsis-vertical"></i>
                    </button>
                    <div class="card-options">
                      <div>Open in new tab</div>
                      <div>Edit</div>
                      <!-- <div>Reassign</div>
                      <div>Convert</div>
                      <div>Clone</div> -->
                      <div>Archive</div>
                    </div>

                  </div>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php
    } ?>

  </div>

  <!-- end kanban -->




<!-- Mass Edit Modal -->
<div class="modal fade" id="updateModel" tabindex="-1" role="dialog" aria-labelledby="updateModelLabel"
  aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>

<!--zitendra Button to Open Popup -->
<!-- Zitendra Button to Open Popup -->
<button id="generateCsvButton" class="btn-1 tr-hidden">Open CSV Generator</button>

<!-- Popup Content -->
<div id="popup" class="popup" style="display: none;">
  <div class="popup-content">
    <span class="close-btn" id="closePopup">&times;</span>
    <h2>Generate CSV Format</h2>
    <button id="generateCsv" class="btn btn-success">click to generate</button>

    <pre><br></pre>
    <h2>Upload CSV File</h2>

    <form action="<?= $uploadUrl ?>" id="csvupload" method="post" enctype="multipart/form-data">
      <!-- CSRF Token -->
      <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
      <!-- Input for Ownerid -->
      <label for="tablename">Select Owner:</label>
      <select name="bulk_ownerid" id="bulk_ownerid" class="form-control DD~M singleselect">
        <option value="">-Select-</option>

        <?php
        foreach ($adminowners as $usersad) {
        ?>
          <option value="<?= $usersad['id']; ?>"><?= $usersad['userid']; ?></option>

        <?php
        }
        ?>
      </select>
      <p class="bulk_ownerid_msg text-danger"></p>
      <!-- Input for table name -->
      <!-- <label for="tablename">Table Name:</label> -->
      <input type="hidden" name="tablename" value="<?php
                                                    foreach ($DataImport as $keval) {
                                                      $tablename = $keval['tablename'];
                                                    }
                                                    echo $tablename;
                                                    ?>" id="tablename" required>

      <!-- File input for CSV upload -->
      <div class="row">
        <div class="col-md-12 col-sm-12">
          <label for="file">Choose CSV file:</label>
        </div>
        <div class="col-md-6 col-sm-12">

          <input type="file" name="file" id="file" accept=".csv"><br>
          <span class="bulk_file_msg text-danger"></span>

        </div>
        <div class="col-md-6 col-sm-12 text-end">
          <!-- Submit button -->
          <button type="submit" id="uploadcsv" class="btn btn-primary">Upload</button>
        </div>
      </div>




    </form>
  </div>

</div>



<!-- Start Styles Zitendra Rai-->
<style nonce="<?= Yii::$app->params['cspNonce'] ?>">
  .faclose {
    margin-left: 188px;
  }

  .select2-container--default .select2-selection--single {
    padding: 0 !important;
  }

  /* General Styles */
  /* body { */
  /* font-family: Arial, sans-serif; */
  /* } */

  /* Button Styles */
  .btn {
    display: inline-block;
    padding: 10px 20px;
    font-size: 16px;
    border: none;
    cursor: pointer;
    border-radius: 5px;
    text-align: center;
  }

  .btn-primary {
    background-color: #007bff;
    color: white;
  }

  .btn-success {
    background-color: #28a745;
    color: white;
  }

  .btn-primary:hover,
  .btn-success:hover {
    opacity: 0.9;
  }

  /* Popup Styles */

  .popup {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 1000;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.4);
    /* Black background with transparency */
  }

  .popup-content {
    background: white;
    padding: 20px;
    border-radius: 10px;
    width: 400px;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
    text-align: center;
  }

  .popup-section {
    margin-bottom: 20px;
  }

  .popup-content h2 {
    margin-top: 0;
    color: #333;
  }

  .popup-content label {
    display: block;
    margin-bottom: 8px;
    font-weight: bold;
    color: #555;
  }

  .popup-content .file-input {
    margin-bottom: 15px;
    width: 100%;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 5px;
  }

  /* Close Button Styles */
  .close-btn {
    position: absolute;
    top: 10px;
    right: 15px;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    color: #aaa;
  }

  .close-btn:hover {
    color: black;
    text-decoration: none;
  }
</style>
<!-- End Styles Zitendra Rai-->


<?php
$js = <<<JS
//onclick
$(".filterbtn").click(function(){
  openfieldName();
});
$(".faclose").click(function(){
closeFilterBox();
});

$(".ddlstbtn").click(function(){
toggleDropdown(this);
});
$(".otd").click(function(){
opentoggleDropdown();
});


  // document.addEventListener("DOMContentLoaded", () => {
    const importButton = document.getElementById("importButton");
    const popup = document.getElementById("popup");
    const closePopup = document.getElementById("closePopup");

    // Show the popup
    importButton.addEventListener("click", () => {
      popup.style.display = "flex";
    });

    // Close the popup
    closePopup.addEventListener("click", () => {
      popup.style.display = "none";
    });

    // Close popup when clicking outside of it
    window.addEventListener("click", (event) => {
      if (event.target === popup) {
        popup.style.display = "none";
      }
    });

   const uploadcsv = document.getElementById("uploadcsv");

uploadcsv.addEventListener("click", function (e) {
    e.preventDefault();

    var isValid = true;

    var bulk_ownerid = $("#bulk_ownerid").val();
    if (!bulk_ownerid) {
        $(".bulk_ownerid_msg").html("Please Select an Owner.");
        isValid = false;
    } else {
        $(".bulk_ownerid_msg").html('');
    }

    var fileupload = $("#file").val() ? $("#file").val().trim() : "";
    if (!fileupload) {
        $(".bulk_file_msg").html("Please Select a csv file to upload.");
        isValid = false;
    } else {
        $(".bulk_file_msg").html('');
    }

    // Stop if validation failed
    // alert(isValid);
    // if (!isValid) return true;
    if (isValid) {
        // ✅ Submit the form if all fields are valid
        $("#csvupload").submit();
    }

    // Submit form manually if needed
    // $("#yourFormId").submit(); // Optional, if it's part of a form
});

  //});
JS;
$js .= <<<JS
// Open the popup on button click
document.getElementById("generateCsvButton").addEventListener("click", function () {
    document.getElementById("popup").style.display = "block";
});

// Close the popup on close button click
document.getElementById("closePopup").addEventListener("click", function () {
    document.getElementById("popup").style.display = "none";
});

// Generate CSV on button click
document.getElementById("generateCsv").addEventListener("click", function () {
    // Column names dynamically populated from PHP
    const columns = [
JS;

// Add dynamic columns from PHP
foreach ($DataImport as $keval) {
  if ($keval['mandatory'] == 1) {
    $js .= '"' . addslashes($keval['fieldlabel']) . ' (mandatory)",';
  } else {
    $js .= '"' . addslashes($keval['fieldlabel']) . '",';
  }
}

// Add static contact fields if module is leads
if ($ModuleName === "leads") {
  $js .= '"contact_first_name1","contact_last_name1","contact_mobile1","contact_email1","designation_1","contact_validation_1",';
  $js .= '"contact_first_name2","contact_last_name2","contact_mobile2","contact_email2","designation_2","contact_validation_2",';
  $js .= '"contact_first_name3","contact_last_name3","contact_mobile3","contact_email3","designation_3","contact_validation_3"';
} else {
  // Remove trailing comma from last item if not leads
  $js = rtrim($js, ',');
}

$js .= <<<JS
];
JS;


$js .= <<<JS

    // Sample data (replace this with dynamic data as needed)
    const data = [
        ["1", "test", "", "", "", "", "", "", "", "", "", ""]
    ];

    // Combine into CSV format
    let csvContent = columns.join(",") + "\\n";
    data.forEach(row => {
        csvContent += row.join(",") + "\\n";
    });

    // Create Blob and download link
    const blob = new Blob([csvContent], { type: "text/csv" });
    const link = document.createElement("a");
    link.href = URL.createObjectURL(blob);
    link.download = "{$TabLabel}_data_Format.csv";
    link.style.display = "none";

    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    // Close popup
    document.getElementById("popup").style.display = "none";
});
JS;

$this->registerJs($js);

$this->registerJsFile(Url::to($baseUrl . "thememain/js/select2.min.js"), ['depends' => [yii\web\JqueryAsset::class]]);

$this->registerJsFile(Url::to($baseUrl . "thememain/js/tetra/single-dd.js"), ['depends' => [yii\web\JqueryAsset::class]]);

$this->registerJsFile('@web/thememain/js/ag-grid-community.min.js', ['depends' => [AdminAsset::class]]);

$this->registerJsFile('@web/thememain/js/custom.js', ['depends' => [AdminAsset::class]]);

$this->registerJsFile('@web/thememain/js/importdata.js', ['depends' => [AdminAsset::class]]);

?>

<script>

</script>

//
<link href="<?= $baseUrl; ?>/thememain/css/select2.min.css" rel="stylesheet">


<script type="text/javascript" src="<?= $baseUrl; ?>thememain/js/select2.min.js"></script>
<script type="text/javascript" src="<?= $baseUrl; ?>thememain/js/tetra/multilist-dd.js"></script>

<!-- added by ptpatel -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalSummeryLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <label id="modal_label_name"> </label>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary savebutton singleeditsavebtn">Save</button>
      </div>
    </div>
  </div>
</div>
<!-- this model added by ptpatel on date 09-12-2025 -->
<div class="modal fade" id="bulkStatusModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Bulk Status Update</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <div id="bulkStatusForm">
          <label><b>Select CSV File</b></label>
          <input type="file" id="bulkStatusFile" accept=".csv" class="form-control">

          <br>
          <?php $samplelink = Yii::$app->urlManager->baseUrl . '/thememain/samples/bulkupdate_inventorystatus.csv'; ?>
          <a href="<?= $samplelink; ?>" download>Download Sample CSV</a>

          <br><br>
        </div>
        <div id="bulkStatusMessage" class="alert d-none"></div>
      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button class="btn btn-primary" id="btnSubmitBulkStatus">Submit</button>
      </div>

    </div>
  </div>
</div>
<!-- this model added by ptpatel on date 09-12-2025 -->