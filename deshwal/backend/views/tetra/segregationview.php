<?php

use app\models\Grn;
use app\models\Leadinformation;
use app\models\Quotes;
use app\models\Sourcingdeal;
use yii\helpers\Url;
use yii\helpers\Html;
use backend\assets\AdminAsset;
use backend\models\AccessCheck;
use backend\modules\segregation\Segregation;
use yii\db\Query;
use yii\data\Pagination;

AdminAsset::register($this);
$this->title = Yii::t('app', 'Dashboard ' . $TabLabel);

$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css', ['depends' => [AdminAsset::class]]);

$this->registerCssFile('@web/thememain/css/ag-grid.css', ['depends' => [AdminAsset::class]]);
$this->registerCssFile('@web/thememain/css/ag-theme-alpine.css', ['depends' => [AdminAsset::class]]);
$this->registerCssFile('@web/thememain/css/listview.css', ['depends' => [AdminAsset::class]]);
$this->registerCssFile('@web/thememain/css/dashboard.css', ['depends' => [AdminAsset::class]]);

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

  .custom-header {
    position: relative;
    height: 48px;
    padding: 0 10px;
    background-color: #f4f4f4;
    border-right: 1px solid #ccc;
    text-align: left;
    font-weight: 600;
    vertical-align: middle;
    line-height: 48px;
    white-space: nowrap;
  }

  .ag-row {
    position: absolute;
    display: flex;
    width: 1200px;
    /* total width of all cells */
    box-sizing: border-box;
  }

  .ag-cell {
    position: absolute;
    height: 43px;
    padding: 8px;
    overflow: hidden;
    white-space: nowrap;
    background-color: #fff;
    border-right: 1px solid #ccc;
    display: flex;
    align-items: center;
    font-family: Arial, sans-serif;
    font-size: 14px;
  }

  .ag-cell-wrapper {
    display: flex;
    flex-direction: column;
    justify-content: center;
    width: 100%;
  }

  .ag-cell-value {
    display: block;
  }
</style>
<link href="<?= $baseUrl; ?>thememain/css/multilist-dd.css" rel="stylesheet">
<input type="hidden" value="<?php echo $ModuleName; ?>" id="module" name="module" />
<input type="hidden" name="csrfToken" id="csrfToken" value="<?= $csrfToken; ?>">
<input type="hidden" name="csrfTokenName" id="csrfTokenName" value="<?= $csrfTokenName; ?>">
<div class="page-content">
  <div class="records table-responsiv">
    <div class="record-header">
      <div class="add">
        <img src="<?= $baseUrl; ?>/thememain/img/module-icon/<?= $ModuleName; ?>.png" class=" head-img">
        <span class="sm-modname"><?= $TabLabel; ?></span>
        <br>

        <?php
        if (!empty($allfilter)) {
        ?>
          <select name="filterselectbox" id="filterselectbox">
            <?php
            foreach ($allfilter as $key => $value) {
              if ($defaultfilter['id'] == $value['id'])
                $sel = 'selected';
              else
                $sel = '';
            ?>
              <option value="<?= $value['id']; ?>" <?= $sel; ?>><?= $value['filter_name']; ?></option>
            <?php
            }
            ?>
          </select>
        <?php
        } else {
        ?>

          <select name="filterselectbox" id="filterselectbox"></select>
        <?php
        } ?>
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
        
        <!-- hided import button -->
        <?php if ($importpermission == 1) { ?>
          <!-- <button class="btn-1" id="importButton">
              <img src="<?php echo $baseUrl;
                        ?>/thememain/img/down.png" style="width:32px;" title="Import"> Import
            </button> -->
        <?php } ?>
        <?php //if (Yii::$app->session->hasFlash('success')): 
        ?>
        <!-- <div class="alert alert-success" id="flashMessage">
        <?php  //Yii::$app->session->getFlash('success') 
        ?>
    </div>
    <script>
        // Wait for 3 seconds and then hide the flash message
        setTimeout(function() {
            $('#flashMessage').fadeOut();
        }, 3000); // 3000 milliseconds = 3 seconds
    </script> -->
        <?php //endif; 
        ?>
        
        <!-- <button class="btn" style="background: none;border: 1px solid #5c9cff; color: #585858;font-size: 12px;"> <img src="<?= $baseUrl; ?>/thememain/img/List-view.png" style="width: 37px;"> List view </button> -->
        
        <?php
        if ($createpermission == 1) {
          if ($layout == 'multiple' || $layout == 'single') {
            $sourcemodule = '';
            $sourceid = '';
            if (isset($_GET['sourceid']))
              $sourceid = filter_var($_GET['sourceid'], FILTER_VALIDATE_INT);
            if (isset($_GET['sourcemodule']))
              $sourcemodule = filter_var($_GET['sourcemodule'], FILTER_VALIDATE_INT);
            if (!empty($sourceid) && !empty($sourcemodule))
              $url = "create?sourcemodule=" . $sourcemodule . "&sourceid=" . $sourceid;
            else
              $url = "create";
            //code added by ptpatel on date 07-04-25 
            //if user come from sourcing deal module and it's stage is lost or won then hide add  and if stage is in pricing DONE the add button will show
            if ($sourcemodule == 51) {
              $sourcing_deal_stage = Sourcingdeal::find()
                ->select("stage")
                ->where(['sourcingdeal_id' => $sourceid])
                ->scalar();
              // echo $sourcing_deal_stage; die;
              if (($sourcing_deal_stage != 14 && $sourcing_deal_stage != 27) && $sourcing_deal_stage == 10) {
        ?>
                <!-- <a href="<?= $url; ?>" class="add-lead-btn2" title="Add New"><button>+ Add</button></a> -->
              <?php
              }
            } else {
              ?>
              <!-- <a href="<?= $url; ?>" class="add-lead-btn2" title="Add New"><button>+ Add</button></a> -->
            <?php
            }
            //end code added by ptpatel on date 07-04-25
          } else {
            ?>
            <!-- <button id="add-lead-btn" class="add-lead-btn2" title="Add New"> + Add</button> -->
        <?php
          }
        }
        ?>
      </div>
    </div>
  </div>
</div>


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
    <div class="col-md-12 bulkactions tr-hidden">
      <input type="hidden" id="hiddenLeadIds" name="hiddenLeadIds" value="">

      <h4 class="sel-h4"><span class="leads-selected" style="color: #5c9cff;">12</span> <?= $TabLabel; ?> Selected <a
          href="" style="color: #5c9cff;">unselect All</a></h4>
      <div class="fram-1">
        <!-- <button class="btn-add"><img src="<?= $baseUrl; ?>/thememain/img/Add.png"> Add Tag</button> -->
        <!-- <button class="btn-email"><img src="<?php // $baseUrl; 
                                                  ?>/thememain/img/email.png"> Email</button> -->
        <?php
        if ($editpermission == 1) {
        ?>
          <button class="btn-update" id="updateButton"><img src="<?= $baseUrl; ?>/thememain/img/update.png">
            Update</button>
        <?php }
        if ($exportpermission == 1) { ?>
          <button class="btn-export" id="exportButton"><img src="<?= $baseUrl; ?>/thememain/img/upload.png">
            Export</button>
        <?php }
        if ($deletepermission == 1) { ?>
          <button class="btn-Archive" id="deleteButton"><img src="<?= $baseUrl; ?>/thememain/img/Archive.png">
            Archive</button>
        <?php } ?>
        <!-- <button class="btn-what"><img src="<?= $baseUrl; ?>/thememain/img/what.png"> whatsa.....</button> -->
      </div>
    </div>


    <?php if ($listpermission == 1) { ?>
      <!-- Table -->
      <div class="table-list">
        <div id="" class="ag-theme-alpine">
          <?php

          // Step 1: Count total rows
          $count = (new \yii\db\Query())
            ->from('grn')
            ->innerJoin('grn_asset_detail', 'grn.grn_id = grn_asset_detail.grn_id')
            ->count();

          // Step 2: Create Pagination object
          $pagination = new Pagination([
            'totalCount' => $count,
            'defaultPageSize' => 10, // You can change the page size
          ]);
          $result = (new \yii\db\Query())
            ->select([
              'grn.*',
              'grn_asset_detail.*',
              'vendor_account.acc_name',
              'pickup.pickup_no',
              'vendor_locations.vendor_loc_name',
            ])
            ->from('grn')
            ->innerJoin('grn_asset_detail', 'grn.grn_id = grn_asset_detail.grn_id')
            ->innerJoin('vendor_account', 'grn.account_name = vendor_account.vendoraccid')
            ->innerJoin('vendor_locations', 'grn.location = vendor_locations.vendorloc_id')
            ->innerJoin('pickup', 'grn.pickup_id = pickup.pickup_id')
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->where(['grn_asset_detail.grn_status' => 1]) //1 segregation pending
            // ->asArray()
            ->all();
          // ->select("grn_no,createdtime,lot_number,pickup_id,location,account_name,");
          ?>
          <div class="table-container">
            <table class="table">
              <thead>
                <tr>
                  <th>GRN Date</th>
                  <th>GRN No</th>
                  <th>Lot No</th>
                  <th>Pickup ID</th>
                  <th>Account Name</th>
                  <th>Account Location</th>
                  <th>Sub Category</th>
                  <th>Product Name</th>
                  <th>Qty</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($result as $r) { ?>
                  <tr>
                    <td><?= explode(" ", $r['createdtime'])[0]; ?></td>
                    <td><?= $r['grn_no']; ?></td>
                    <td><?= $r['lot_number']; ?></td>
                    <td><?= $r['pickup_no']; ?></td>
                    <td><?= $r['acc_name']; ?></td>
                    <td><?= $r['vendor_loc_name']; ?></td>
                    <td><?= $r['porduct_name']; ?></td>
                    <td><?= $r['sub_category']; ?></td>
                    <td><?= $r['received_qty']; ?></td>
                    <?php //$isGrn = ::find()->where(['grn_no'=>$r['grn_no']])
                     //$urledit = "edit?Record=" . $Recordid;
                     $isgrnexists = (new \yii\db\Query())
                      ->select(["*"])
                      ->from($TableName)
                      ->where(['grn_no'=>$r['grn_no']])
                      ->one();
                      if($isgrnexists)
                      {
                        $viewurl = "edit?Record=" . $isgrnexists['segregation_id'];
                      }
                      else
                      {
                        $viewurl = "create?itemid=".$r['grn_asset_detail_id'];
                      }
                     ?>
                    <td><a href='<?= $viewurl; ?>'>View</a></td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
          <!-- Pagination Links -->
          <!-- <div class="pagination-container">
        <?= \yii\widgets\LinkPager::widget([
          'pagination' => $pagination,
        ]) ?>
    </div> -->

          <?php if ($pagination->pageCount > 1): ?>
            <div class="custom-pagination">
              <!-- First Page -->
              <?php if (!$pagination->page == 0): ?>
                <a href="<?= \yii\helpers\Url::current(['page' => 1])?>" class="btn">⏮</a>
                <a href="<?= \yii\helpers\Url::current(['page' => $pagination->page]) ?>" class="btn">◀</a>
              <?php else: ?>
                <span class="btn disabled">⏮</span>
                <span class="btn disabled">◀</span>
              <?php endif; ?>

              <!-- Page Numbers -->
              <?php for ($i = 0; $i < $pagination->pageCount; $i++): ?>
                <?php if ($i == $pagination->page): ?>
                  <span class="btn current"><?= $i + 1 ?></span>
                <?php else: ?>
                  <a href="<?= \yii\helpers\Url::current(['page' => $i + 1]) ?>" class="btn"><?= $i + 1 ?></a>
                <?php endif; ?>
              <?php endfor; ?>

              <!-- Next Page -->
              <?php if (!$pagination->page >= $pagination->pageCount - 1): ?>
                <a href="<?= \yii\helpers\Url::current(['page' => $pagination->page + 2]) ?>" class="btn">▶</a>
                <a href="<?= \yii\helpers\Url::current(['page' => $pagination->pageCount]) ?>" class="btn">⏭</a>
              <?php else: ?>
                <span class="btn disabled">▶</span>
                <span class="btn disabled">⏭</span>
              <?php endif; ?>
            </div>
          <?php endif; ?>
          <!-- pagination -->


        </div>
        <!-- End of Table -->
      <?php } ?>

  </div>
</div>




<!--zitendra Button to Open Popup -->
<!-- Zitendra Button to Open Popup -->
<button id="generateCsvButton" class="btn-1">Open CSV Generator</button>

<!-- Popup Content -->
<div id="popup" class="popup" style="display: none;">
  <div class="popup-content">
    <span class="close-btn" id="closePopup">&times;</span>
    <h2>Generate Farmate</h2>
    <button id="generateCsv" class="btn btn-success">click generate</button>

    <pre><br></pre>
    <h2>Upload CSV File</h2>

    <form action="<?= $uploadUrl ?>" method="post" enctype="multipart/form-data">
      <!-- CSRF Token -->
      <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>

      <!-- Input for table name -->
      <label for="tablename">Table Name:</label>
      <input type="hidden" name="tablename" value="<?php
                                                    foreach ($DataImport as $keval) {
                                                      $tablename = $keval['tablename'];
                                                    }
                                                    echo $tablename;
                                                    ?>" id="tablename" required>

      <!-- File input for CSV upload -->
      <label for="file">Choose CSV file:</label>
      <input type="file" name="file" id="file" accept=".csv" required>

      <!-- Submit button -->
      <button type="submit" class="btn btn-primary">Upload</button>
    </form>
  </div>

</div>

<script>
  // Open the popup on button click
  document.getElementById("generateCsvButton").addEventListener("click", function() {
    document.getElementById("popup").style.display = "block";
  });

  // Close the popup on close button click
  document.getElementById("closePopup").addEventListener("click", function() {
    document.getElementById("popup").style.display = "none";
  });

  // Generate CSV on button click
  document.getElementById("generateCsv").addEventListener("click", function() {
    // Column names dynamically populated from PHP
    <?php
    echo "const columns = [";
    foreach ($DataImport as $keval) {
      if ($keval['mandatory'] == 1) {
        // Append "(mandatory)" for mandatory fields
        echo '"' . $keval['fieldlabel'] . ' (mandatory)",';
      } else {
        echo '"' . $keval['fieldlabel'] . '",';
      }
    }
    echo "];";
    ?>

    // Add a sample data row (this should ideally come from your database dynamically)
    const data = [
      ["1", "test", "", "", "", "", "", "", "", "", "", ""]
    ];

    // Combine column names and data into CSV format
    let csvContent = columns.join(",") + "\n"; // Add headers
    data.forEach(row => {
      csvContent += row.join(",") + "\n"; // Add rows
    });

    // Create a Blob and a download link for the CSV
    const blob = new Blob([csvContent], {
      type: "text/csv"
    });
    const link = document.createElement("a");
    link.href = URL.createObjectURL(blob);
    link.download = "data Formate.csv"; // File name for download
    link.style.display = "none";

    // Append the link, trigger download, and remove the link
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    // Close the popup after downloading the CSV
    document.getElementById("popup").style.display = "none";
  });
</script>

<!-- Start Styles Zitendra Rai-->
<style>
  /* General Styles */
  body {
    font-family: Arial, sans-serif;
  }

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



$this->registerJs("
    // $('#add-lead-btn').click(function() {
    //     $('#add-lead-modal').modal('show');
    // });
    

// applyFilter();

 
");

$this->registerJsFile('@web/thememain/js/ag-grid-community.min.js', ['depends' => [AdminAsset::class]]);

$this->registerJsFile('@web/thememain/js/custom.js', ['depends' => [AdminAsset::class]]);

?>

<script>
  document.addEventListener("DOMContentLoaded", () => {
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
  });

  
</script>

<link href="<?= $baseUrl; ?>/thememain/css/select2.min.css" rel="stylesheet">

<script type="text/javascript" src="<?= $baseUrl; ?>thememain/js/select2.min.js"></script>
<script type="text/javascript" src="<?= $baseUrl; ?>thememain/js/tetra/multilist-dd.js"></script>
