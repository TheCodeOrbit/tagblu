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

  .head-img {
    position: relative;
    top: 0px !important;
    padding: 10px;
    width: 55px;
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

        <!-- <button class="btn" style="background: none;border: 1px solid var(--color-primary) !important; color: #585858;font-size: 12px;"> <img src="<?= $baseUrl; ?>/thememain/img/List-view.png" style="width: 37px;"> List view </button> -->

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

      <h4 class="sel-h4"><span class="leads-selected" style="color: var(--color-primary) !important;">12</span> <?= $TabLabel; ?> Selected <a
          href="" style="color: var(--color-primary) !important;">unselect All</a></h4>
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


    <?php
    // echo "bjhbj".$listpermission;die;
    if ($listpermission == 1) {
      // echo "bjhbj".$TabId;die;
    ?>
      <!-- Table -->
      <div class="table-list">
        <div id="" class="ag-theme-alpine">
          <?php
          if ($TabId == 67) { //segregation
            // Step 1: Count total rows
            $count = (new \yii\db\Query())
              ->from('grn')
              ->innerJoin('grn_asset_detail', 'grn.grn_id = grn_asset_detail.grn_id')
              ->where(['grn_asset_detail.grn_status' => 1])
              ->count();

            // Step 2: Create Pagination object
            $pagination = new Pagination([
              'totalCount' => $count,
              'defaultPageSize' => 10, // You can change the page size
            ]);
            $result = (new \yii\db\Query())
              ->select([
                'grn.*',
                'grn_asset_detail.grn_id',
                'grn_asset_detail.grn_asset_detail_id',
                'grn_asset_detail.sub_category',
                'grn_asset_detail.received_qty',
                'grn_asset_detail.grn_status',
                'vendor_account.acc_name',
                'pickup.pickup_no',
                'warehouse.warehouse_name',
                'products.product_name',
              ])
              ->from('grn')
              ->innerJoin('grn_asset_detail', 'grn.grn_id = grn_asset_detail.grn_id')
              ->innerJoin('vendor_account', 'grn.account_name = vendor_account.vendoraccid')
              //change on date 27-10-2025 
              // ->innerJoin('vendor_locations', 'grn.location = vendor_locations.vendorloc_id')
              ->leftJoin('warehouse', 'grn.location = warehouse.warehouse_id')
              ->innerJoin('pickup', 'grn.pickup_id = pickup.pickup_id')
              ->leftJoin('products', 'products.products_id = grn_asset_detail.porduct_name')
              ->offset($pagination->offset)
              ->limit($pagination->limit)
              ->where(['grn_asset_detail.grn_status' => 1]) //1 segregation pending
              // ->asArray()
              ->all();
              // echo "<pre>";print_r($result);die;
          ?>
            <div class="table-container table-responsive">
              <table class="table">
                <thead>
                  <tr>
                    <th>GRN Date</th>
                    <th>GRN No</th>
                    <th>Lot No</th>
                    <th>Pickup ID</th>
                    <th>Account Name</th>
                    <th>Warehouse Location</th>
                    <th>Sub Category</th>
                    <th>Product Name</th>
                    <th>Qty</th>
                    <!-- $createpermission added on date 03-01-2026 by ptpatel -->
                    <?=  $createpermission == 1 ? '<th>Action</th>' : ''; ?>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  if (count($result) > 0) {
                    foreach ($result as $r) {
                      $grndate = explode("-", explode(" ", $r['createdtime'])[0]); ?>
                      <tr>
                        <td><?= $grndate[2] . "-" . $grndate[1] . "-" . $grndate[0]; ?></td>
                        <td><?= $r['grn_no']; ?></td>
                        <td><?= $r['lot_number']; ?></td>
                        <td><?= $r['pickup_no']; ?></td>
                        <td><?= $r['acc_name']; ?></td>
                        <td><?= $r['warehouse_name']; ?></td>
                        <td><?= $r['sub_category']; ?></td>
                        <td><?= $r['product_name']; ?></td>
                        <td><?= $r['received_qty']; ?></td>
                        <?php //$isGrn = ::find()->where(['grn_no'=>$r['grn_no']])
                        //$urledit = "edit?Record=" . $Recordid;
                        $isgrnexists = (new \yii\db\Query())
                          ->select(["*"])
                          ->from($TableName)
                          ->where(['grn_asset_detail_id' => $r['grn_asset_detail_id']])
                          ->one();
                        if ($isgrnexists) {
                          $viewurl = "edit?Record=" . $isgrnexists['segregation_id'];
                        } else {
                          $viewurl = "create?itemid=" . $r['grn_asset_detail_id'];
                        }
                        ?>
                         <!-- $createpermission added on date 03-01-2026 by ptpatel -->
                        <?php if($createpermission == 1){ ?><td><a href='<?= $viewurl; ?>'>View</a></td>
                      </tr>
                    <?php }}
                  } else {
                    ?><tr>
                      <td colspan="10" style="text-align: center;">No records found.</td>
                    </tr><?php
                        } ?>
                </tbody>
              </table>
            </div>
          <?php
          } else if ($TabId == 68) { //tagging
            // $count = (new \yii\db\Query())
            //   ->from('inventory')
            //   ->leftJoin('prod_sub_catagory', 'prod_sub_catagory.sub_catagory_id = inventory.subcategory')
            //   ->leftJoin('prod_model', 'prod_model.prod_model_id = inventory.model')
            //   ->leftJoin('prod_make', 'prod_make.prod_make_id = inventory.make')
            //   ->leftJoin('prod_category', 'prod_category.prod_category_id = inventory.category')
            //   ->where(['inventory.status' => 2])              
            //   ->groupBy(['inventory.grn_no', 'inventory.product_name', 'inventory.subcategory'])
            //   ->count();
            $search = Yii::$app->request->get('search', '');
          $rowsQuery = (new \yii\db\Query())
                ->select(['inventory.grn_id', 'inventory.product_name', 'inventory.subcategory'])
                ->from('inventory')
                ->leftJoin('prod_sub_catagory', 'prod_sub_catagory.sub_catagory_id = inventory.subcategory')
                ->leftJoin('prod_model', 'prod_model.prod_model_id = inventory.model')
                ->leftJoin('prod_make', 'prod_make.prod_make_id = inventory.make')
                ->leftJoin('prod_category', 'prod_category.prod_category_id = inventory.category')
                ->innerJoin('vendor_account', 'inventory.account_name = vendor_account.vendoraccid')
                ->leftJoin('warehouse', 'inventory.location = warehouse.warehouse_id')
                ->innerJoin('products', 'products.products_id = inventory.product_name')
                ->where(['inventory.status' => 2])
                ->groupBy(['inventory.grn_id', 'inventory.product_name', 'inventory.subcategory']);

            if ($search !== '') {
                $rowsQuery->andWhere([
                    'or',
                    ['like', 'inventory.grn_no', $search],
                    ['like', 'inventory.lot_no', $search],
                    ['like', 'inventory.pickup_id', $search],
                    ['like', 'vendor_account.acc_name', $search],
                    ['like', 'warehouse.warehouse_name', $search],
                    ['like', 'prod_sub_catagory.sub_catagory_value', $search],
                    ['like', 'products.product_name', $search],
                ]);
            }

            // execute for count (no Query::count() here)
            $rows  = $rowsQuery->all();
            $count = count($rows);

            // pagination
            $pagination = new Pagination([
                'totalCount'      => $count,
                'defaultPageSize' => 10,
            ]);
            //   $result = (new \yii\db\Query())
            //   ->select([
            //     'inventory.*',
            //     'vendor_account.acc_name',
            //     'vendor_locations.vendor_loc_name',
            //     'prod_sub_catagory.sub_catagory_value',
            //     'prod_model.prod_model_value',
            //     'prod_make.prod_make_value',
            //     'prod_category.prod_category_value',
            //     'products.product_name',
            // ])
            // ->from('inventory')
            //   ->leftJoin('prod_sub_catagory', 'prod_sub_catagory.sub_catagory_id = inventory.subcategory')
            //   ->leftJoin('prod_model', 'prod_model.prod_model_id = inventory.model')
            //   ->leftJoin('prod_make', 'prod_make.prod_make_id = inventory.make')
            //   ->leftJoin('prod_category', 'prod_category.prod_category_id = inventory.category')
            //   ->innerJoin('vendor_account', 'inventory.account_name = vendor_account.vendoraccid')
            //   ->innerJoin('vendor_locations', 'inventory.location = vendor_locations.vendorloc_id')
            //   ->innerJoin('products', 'products.products_id = inventory.product_name')
            //   ->where(['inventory.status' => 2]) //2 is tagging pending
            //     ->offset($pagination->offset)
            //     ->limit($pagination->limit)
            //     ->all();


           $resultQuery = (new \yii\db\Query())
            ->select([
                'inventory.*',
                'inventory.grn_no',
                'inventory.product_name as product_id',
                'inventory.subcategory',
                'inventory.createdtime',
                'COUNT(*) AS qty',
                'vendor_account.acc_name',
                'warehouse.warehouse_name',
                'prod_sub_catagory.sub_catagory_value',
                'prod_model.prod_model_value',
                'prod_make.prod_make_value',
                'prod_category.prod_category_value',
                'products.product_name',
            ])
            ->from('inventory')
            ->leftJoin('prod_sub_catagory', 'prod_sub_catagory.sub_catagory_id = inventory.subcategory')
            ->leftJoin('prod_model', 'prod_model.prod_model_id = inventory.model')
            ->leftJoin('prod_make', 'prod_make.prod_make_id = inventory.make')
            ->leftJoin('prod_category', 'prod_category.prod_category_id = inventory.category')
            ->innerJoin('vendor_account', 'inventory.account_name = vendor_account.vendoraccid')
            ->leftJoin('warehouse', 'inventory.location = warehouse.warehouse_id')
            ->innerJoin('products', 'products.products_id = inventory.product_name')
            ->where(['inventory.status' => 2]);

        if ($search !== '') {
            $resultQuery->andWhere([
                'or',
                ['like', 'inventory.grn_no', $search],
                ['like', 'inventory.lot_no', $search],
                ['like', 'inventory.pickup_id', $search],
                ['like', 'vendor_account.acc_name', $search],
                ['like', 'warehouse.warehouse_name', $search],
                ['like', 'prod_sub_catagory.sub_catagory_value', $search],
                ['like', 'products.product_name', $search],
            ]);
        }

        $result = $resultQuery
    ->groupBy(['inventory.grn_id', 'inventory.product_name', 'inventory.subcategory'])
    ->offset($pagination->offset)
    ->limit($pagination->limit)
    ->all();
          ?>
          <div style="display:flex; justify-content:flex-end; margin-bottom:10px;">
            <form method="get" action="<?= Url::current(); ?>" style="display:flex; gap:4px;">
                <input
                    type="text"
                    name="search"
                    value="<?= htmlspecialchars($search, ENT_QUOTES); ?>"
                    placeholder="Search..."
                    style="padding:6px 10px; border:1px solid #ccc; border-radius:4px; min-width:220px;"
                >
                <button type="submit" class="btn btn-primary btn-sm">
                    Search
                </button>
                <?php if ($search !== ''): ?>
                    <a href="<?= Url::current(['search' => null, 'page' => null]); ?>" class="btn btn-default btn-sm">
                        Clear
                    </a>
                <?php endif; ?>
            </form>
        </div>

            <div class="table-container table-responsive">
              <table class="table">
                <thead>
                  <tr>
                    <th>GRN Date</th>
                    <th>GRN No</th>
                    <th>Lot No</th>
                    <th>Pickup ID</th>
                    <th>Account Name</th>
                    <th>Warehouse Location</th>
                    <th>Sub Category</th>
                    <th>Product Name</th>
                    <th>Qty</th>
                     <!-- $createpermission added on date 03-01-2026 by ptpatel -->
                    <?=  $createpermission == 1 ? '<th>Action</th>' : ''; ?>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  if (count($result) > 0) {
                    foreach ($result as $r) {
                      // echo "<pre>";print_r($r);die;
                  ?>
                      <tr>
                        <td><?= explode(" ", $r['createdtime'])[0]; ?></td>
                        <td><?= $r['grn_no']; ?></td>
                        <td><?= $r['lot_no']; ?></td>
                        <td><?= $r['pickup_id']; ?></td>
                        <td><?= $r['acc_name']; ?></td>
                        <td><?= $r['warehouse_name']; ?></td>
                        <td><?= $r['sub_catagory_value']; ?></td>
                        <td><?= $r['product_name']; ?></td>
                        <td><?= $r['qty']; ?></td>
                        <?php //$isGrn = ::find()->where(['grn_no'=>$r['grn_no']])
                        //$urledit = "edit?Record=" . $Recordid;
                        // $isgrnexists = (new \yii\db\Query())
                        //   ->select(["*"])
                        //   ->from($TableName)
                        //   ->where(['grn_no'=>$r['grn_no']])
                        //   ->one();
                        //   if($isgrnexists)
                        //   {
                        //     $viewurl = "edit?Record=" . $isgrnexists['segregation_id'];
                        //   }
                        //   else
                        {
                          $viewurl = "create?itemid=" . $r['grn_id'] . "_" . $r['product_id'] . "_" . $r['subcategory'];
                          $burl = "bulkuplaodtagging?itemid=" . $r['grn_id'] . "_" . $r['product_id'] . "_" . $r['subcategory'];
                        }
                        ?>
                         <!-- $createpermission added on date 03-01-2026 by ptpatel -->
                        <?php if($createpermission == 1){ ?>
                          <td style="text-align: center;">
                          <a href='<?= $viewurl; ?>' title= 'View'><i class="fa-solid fa-eye " style="cursor: pointer;color:#5c9cff"></i></a>&nbsp;&nbsp;<a href='<?= $burl; ?>' title= 'Bulk Upload'><i class="fa-solid fa-arrow-up-from-bracket" style="cursor:pointer;color:#5c9cff"></i>
</a></td>
<?php } ?>
                      </tr>
                    <?php } 
                  } else {
                    ?><tr>
                      <td colspan="10" style="text-align: center;">No records found.</td>
                    </tr><?php
                        } ?>
                </tbody>
              </table>
            </div>
          <?php
          } else if ($TabId == 69) { //sticker removal
            $count = (new \yii\db\Query())
              ->from('inventory')
              ->leftJoin('prod_sub_catagory', 'prod_sub_catagory.sub_catagory_id = inventory.subcategory')
              ->leftJoin('prod_model', 'prod_model.prod_model_id = inventory.model')
              ->leftJoin('prod_make', 'prod_make.prod_make_id = inventory.make')
              ->leftJoin('prod_category', 'prod_category.prod_category_id = inventory.category')
              ->where(['inventory.status' => 3])
              ->count();

            // Step 2: Create Pagination object
            $pagination = new Pagination([
              'totalCount' => $count,
              'defaultPageSize' => 10, // You can change the page size
            ]);

            $result = (new \yii\db\Query())
              ->select([
                'inventory.*',
                'inventory.grn_no',
                'inventory.product_name  as product_id',
                'inventory.subcategory',
                'inventory.createdtime',
                'COUNT(*) AS qty',
                'vendor_account.acc_name',
                // 'vendor_locations.vendor_loc_name',
                'warehouse.warehouse_name',
                'prod_sub_catagory.sub_catagory_value',
                'prod_model.prod_model_value',
                'prod_make.prod_make_value',
                'prod_category.prod_category_value',
                'products.product_name',
                'tag_bin_number.bin_number_value',
              ])
              ->from('inventory')
              ->leftJoin('prod_sub_catagory', 'prod_sub_catagory.sub_catagory_id = inventory.subcategory')
              ->leftJoin('prod_model', 'prod_model.prod_model_id = inventory.model')
              ->leftJoin('prod_make', 'prod_make.prod_make_id = inventory.make')
              ->leftJoin('prod_category', 'prod_category.prod_category_id = inventory.category')
              ->innerJoin('vendor_account', 'inventory.account_name = vendor_account.vendoraccid')              
              // ->innerJoin('vendor_locations', 'inventory.location = vendor_locations.vendorloc_id')
              //change on date 27-10-2025
              ->leftJoin('warehouse', 'inventory.location = warehouse.warehouse_id')
              ->innerJoin('products', 'products.products_id = inventory.product_name')
              ->innerJoin('tag_bin_number', 'tag_bin_number.bin_number_id = inventory.bin_number')
              ->where(['inventory.status' => 3]) // 3 is stickerremoval
              ->groupBy(['inventory.grn_no', 'inventory.product_name', 'inventory.subcategory'])
              ->offset($pagination->offset)
              ->limit($pagination->limit)
              ->all();
          ?>
            <div class="table-container table-responsive">
              <table class="table">
                <thead>
                  <tr>
                    <th>GRN Date</th>
                    <th>GRN No</th>
                    <th>Lot No</th>
                    <th>Pickup ID</th>
                    <th>Tag No</th>
                    <th>Bin No</th>
                    <th>Cleaning Required</th>
                    <th>Removal Required</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  if (count($result) > 0) {
                    foreach ($result as $r) {
                  ?>
                      <tr>
                        <td><?= explode(" ", $r['createdtime'])[0]; ?></td>
                        <td><?= $r['grn_no']; ?></td>
                        <td><?= $r['lot_no']; ?></td>
                        <td><?= $r['pickup_id']; ?></td>
                        <td><?= $r['tag_number']; ?></td>
                        <td><?= $r['bin_number_value']; ?></td>
                        <td>-</td>
                        <td>-</td>
                        <?php {
                          // $viewurl = "edit?RecordId=" . $r['sticker_removal_id'];
                          $viewurl = "create?itemid=" . $r['grn_no'] . "_" . $r['product_id'] . "_" . $r['subcategory'];
                        }
                        ?>
                        <td><a href='<?= $viewurl; ?>'>View</a></td>
                      </tr>
                    <?php }
                  } else {
                    ?><tr>
                      <td colspan="10" style="text-align: center;">No records found.</td>
                    </tr><?php
                        } ?>
                </tbody>
              </table>
            </div>
          <?php
          } else if ($TabId == 70) { //cleaning
            $count = (new \yii\db\Query())
              ->from('inventory')
              ->leftJoin('prod_sub_catagory', 'prod_sub_catagory.sub_catagory_id = inventory.subcategory')
              ->leftJoin('prod_model', 'prod_model.prod_model_id = inventory.model')
              ->leftJoin('prod_make', 'prod_make.prod_make_id = inventory.make')
              ->leftJoin('prod_category', 'prod_category.prod_category_id = inventory.category')
              ->where(['inventory.status' => 4])
              ->count();

            // Step 2: Create Pagination object
            $pagination = new Pagination([
              'totalCount' => $count,
              'defaultPageSize' => 10, // You can change the page size
            ]);
            $result = (new \yii\db\Query())
              ->select([
                'inventory.*',
                'inventory.grn_no',
                'inventory.product_name  as product_id',
                'inventory.subcategory',
                'inventory.createdtime',
                'COUNT(*) AS qty',
                'vendor_account.acc_name',
                // 'vendor_locations.vendor_loc_name',
                'warehouse.warehouse_name',
                'prod_sub_catagory.sub_catagory_value',
                'prod_model.prod_model_value',
                'prod_make.prod_make_value',
                'prod_category.prod_category_value',
                'products.product_name',
                'tag_bin_number.bin_number_value',
              ])
              ->from('inventory')
              ->leftJoin('prod_sub_catagory', 'prod_sub_catagory.sub_catagory_id = inventory.subcategory')
              ->leftJoin('prod_model', 'prod_model.prod_model_id = inventory.model')
              ->leftJoin('prod_make', 'prod_make.prod_make_id = inventory.make')
              ->leftJoin('prod_category', 'prod_category.prod_category_id = inventory.category')
              ->innerJoin('vendor_account', 'inventory.account_name = vendor_account.vendoraccid')
              // ->innerJoin('vendor_locations', 'inventory.location = vendor_locations.vendorloc_id')
              //change on date 27-10-2025 
              ->leftJoin('warehouse', 'inventory.location = warehouse.warehouse_id')
              ->innerJoin('products', 'products.products_id = inventory.product_name')
              ->innerJoin('tag_bin_number', 'tag_bin_number.bin_number_id = inventory.bin_number')
              ->where(['inventory.status' => 4]) // 4 is cleaning
              ->groupBy(['inventory.grn_no', 'inventory.product_name', 'inventory.subcategory'])
              ->offset($pagination->offset)
              ->limit($pagination->limit)
              ->all();
          ?>
            <div class="table-container table-responsive">
              <table class="table">
                <thead>
                  <tr>
                    <th>GRN Date</th>
                    <th>GRN No</th>
                    <th>Lot No</th>
                    <th>Pickup ID</th>
                    <th>Tag No</th>
                    <th>Bin No</th>
                    <th>Cleaning Required</th>
                    <th>Removal Required</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  if (count($result) > 0) {
                    foreach ($result as $r) {
                      // echo "<pre>";print_r($r);die;
                  ?>
                      <tr>
                        <td><?= explode(" ", $r['createdtime'])[0]; ?></td>
                        <td><?= $r['grn_no']; ?></td>
                        <td><?= $r['lot_no']; ?></td>
                        <td><?= $r['pickup_id']; ?></td>
                        <td><?= $r['tag_number']; ?></td>
                        <td><?= $r['bin_number_value']; ?></td>
                        <td>-</td>
                        <td>-</td>
                        <?php {
                          // $viewurl = "edit?RecordId=" . $r['sticker_removal_id'];
                          $viewurl = "create?itemid=" . $r['grn_no'] . "_" . $r['product_id'] . "_" . $r['subcategory'];
                        }
                        ?>
                        <td><a href='<?= $viewurl; ?>'>View</a></td>
                      </tr>
                    <?php }
                  } else {
                    ?><tr>
                      <td colspan="10" style="text-align: center;">No records found.</td>
                    </tr><?php
                        } ?>
                </tbody>
              </table>
            </div>
          <?php
          } else if ($TabId == 77) { //inventory ageing
            $count = (new \yii\db\Query())
              ->from('rep_inventory_ageing')->groupBy('subcategory')
              ->count();

            // Step 2: Create Pagination object
            $pagination = new Pagination([
              'totalCount' => $count,
              'defaultPageSize' => 10, // You can change the page size
            ]);

            // $result = (new Query())
            //     ->select([
            //         'rep_inventory_ageing.inventory_ageing_id',
            //         'rep_inventory_ageing.subcategory',
            //         'SUM(rep_inventory_ageing.qty) AS qty',
            //         'SUM(rep_inventory_ageing.amount) AS total_value',
            //         'prod_sub_catagory.sub_catagory_value',
            //         'prod_uom.uom_value',
            //         'DATEDIFF(CURDATE(), rep_inventory_ageing.grn_date) AS ageing',
            //     ])
            //     ->from('rep_inventory_ageing')
            //     ->leftJoin('prod_sub_catagory', 'prod_sub_catagory.sub_catagory_id = rep_inventory_ageing.subcategory')
            //     ->leftJoin('prod_uom', 'prod_uom.uom_id = rep_inventory_ageing.uom')
            //     ->innerJoin('vendor_account', 'rep_inventory_ageing.account_name = vendor_account.vendoraccid')
            //     ->groupBy('rep_inventory_ageing.subcategory')
            //     ->offset($pagination->offset)
            //     ->limit($pagination->limit)
            //     ->all();

            $result = (new Query())
              ->select([
                'rep_inventory_ageing.subcategory',
                'prod_sub_catagory.sub_catagory_value',
                'SUM(rep_inventory_ageing.qty) AS qty',
                'SUM(rep_inventory_ageing.amount) AS total_value',
                'prod_uom.uom_value',
                'SUM(CASE WHEN DATEDIFF(CURDATE(), rep_inventory_ageing.grn_date) BETWEEN 0 AND 15 THEN rep_inventory_ageing.amount ELSE 0 END) AS amt_0_15',
                'SUM(CASE WHEN DATEDIFF(CURDATE(), rep_inventory_ageing.grn_date) BETWEEN 16 AND 30 THEN rep_inventory_ageing.amount ELSE 0 END) AS amt_16_30',
                'SUM(CASE WHEN DATEDIFF(CURDATE(), rep_inventory_ageing.grn_date) BETWEEN 31 AND 60 THEN rep_inventory_ageing.amount ELSE 0 END) AS amt_31_60',
                'SUM(CASE WHEN DATEDIFF(CURDATE(), rep_inventory_ageing.grn_date) BETWEEN 61 AND 90 THEN rep_inventory_ageing.amount ELSE 0 END) AS amt_61_90',
                'SUM(CASE WHEN DATEDIFF(CURDATE(), rep_inventory_ageing.grn_date) BETWEEN 91 AND 180 THEN rep_inventory_ageing.amount ELSE 0 END) AS amt_91_180',
                'SUM(CASE WHEN DATEDIFF(CURDATE(), rep_inventory_ageing.grn_date) > 180 THEN rep_inventory_ageing.amount ELSE 0 END) AS amt_180_plus',
              ])
              ->from('rep_inventory_ageing')
              ->leftJoin('prod_sub_catagory', 'prod_sub_catagory.sub_catagory_id = rep_inventory_ageing.subcategory')
              ->leftJoin('prod_uom', 'prod_uom.uom_id = rep_inventory_ageing.uom')
              ->innerJoin('vendor_account', 'rep_inventory_ageing.account_name = vendor_account.vendoraccid')
              ->groupBy('rep_inventory_ageing.subcategory')
              ->offset($pagination->offset)
              ->limit($pagination->limit)
              ->all();


          ?>
            <div class="table-container table-responsive">
              <div class="table-list">
                <div id="myGrid" class="ag-theme-alpine"></div>

                <div id="custom-pagination" class="pagination-container">

                  <span id="pagination-info" class="pagination-info" style="flex: auto;"></span>


                  <label for="page-size" class="results-per-page">Results Per Page:</label>
                  <select id="page-size" class="page-size-dropdown"><!-- onchange="changePageSize()" -->

                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                  </select>
<!-- onclick="goToPage(1)" onclick="goToPage(totalPages)"-->
                  <button id="first-page" class="pagination-button first-page"   >First</button>
                  <div id="pagination-buttons" class="pagination-buttons"></div>
                  <button id="last-page" class="pagination-button last-page" >Last</button>
                </div>
              </div>
            <?php
          } ?>
            <!-- Pagination Links -->

            <?= \yii\widgets\LinkPager::widget([
              'pagination' => $pagination,
            ]) ?>

            <?php if ($pagination->pageCount > 1): ?>
              <div class="custom-pagination">
                <!-- First Page -->
                <?php if (!$pagination->page == 0): ?>
                  <a href="<?= \yii\helpers\Url::current(['page' => 1]) ?>" class="btn">⏮</a>
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

      // $this->registerJsFile('@web/thememain/js/custom.js', ['depends' => [AdminAsset::class]]);

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

      <!-- <link href="<?= $baseUrl; ?>/thememain/css/select2.min.css" rel="stylesheet">

      <script type="text/javascript" src="<?= $baseUrl; ?>thememain/js/select2.min.js"></script>
      <script type="text/javascript" src="<?= $baseUrl; ?>thememain/js/tetra/multilist-dd.js"></script> -->
<?php
        $scriptPath = $baseUrl . "js/$ModuleName/edit.js";
  ?>
  <script type="text/javascript" src="<?= $scriptPath ?>"></script>
