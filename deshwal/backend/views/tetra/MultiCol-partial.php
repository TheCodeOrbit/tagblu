<?php
error_reporting(-1);
ini_set("display_errors", true);
use yii\helpers\Html;
use yii\widgets\ActiveForm;

use backend\assets\AdminAsset;

AdminAsset::register($this);

$csrfTokenName = Yii::$app->request->csrfParam;  // This replaces csrfTokenName
$csrfToken = Yii::$app->request->csrfToken;      // Get the CSRF token itself

$siteDir = Yii::$app->params["dirName"];
$ModuleName = $ActionList["ModuleName"];

$ActionName = $ActionList["ActionName"];
$ModuleLabel = $ActionList["ModuleLabel"];
$_SESSION["countpro"] = "";
$_SESSION["taxcounterr"] = "";
$sesionid = isset($_SESSION[$siteDir . "_id"])
    ? $_SESSION[$siteDir . "_id"]
    : "deshwal";

$baseUrl = Yii::$app->HomeUrl;
$scriptPath = $baseUrl . "js/$ModuleName/edit.js";
$relationName = $action_name === 'create' ? 'createfields' : 'editfields';
//registerJsFile($scriptPath, ['depends' => [\yii\web\JqueryAsset::class]]);
//print_r($_SESSION);
// $MineNamee=$_SESSION['cms_mine_name'];
// if($MineNamee=='pekb'){
// $MineName=1;
// }elseif($MineNamee=='talabira'){
// $MineName=2;
// }elseif($MineNamee=='gp3'){
// $MineName=3;
// }elseif($MineNamee=='kurmitar'){
// $MineName=4;
// }else{

// $MineName=5;

// }

//echo "<pre>";print_r($invmngrule);exit;
//print_r($_SESSION);
// echo "<br>ModuleName=$ModuleName and ActionName=$ActionName";die;
// echo $ActionUrl=Yii::$app->createAbsoluteUrl($ModuleName)."/";die;
//echo "<br>ActionUrl=$ActionUrl";
// $this->pageTitle=Yii::$app->name . " - $ModuleName";
//$this->breadcrumbs=array('Customer',);
$fullurl = Yii::$app->request->getUrl();
$baseUrl = Yii::$app->HomeUrl;

//echo $fullurl ; exit ;

?>
<style>
    .table-container {
        margin-bottom: 20px;
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 10px;
    }

    th,
    td {
        padding: 8px 12px;
        border: 1px solid #ccc;
        text-align: left;
        position: relative;
    }

    th {
        background-color: #f9f9f9;
    }

    .add-row-btn {
        display: inline-block;
        margin-top: 10px;
        padding: 8px 16px;
        background-color: #007bff;
        color: #fff;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        text-decoration: none;
    }

    .add-row-btn:hover {
        background-color: #0056b3;
    }

    .remove-row-btn {
        background-color: #ff4d4d;
        color: #fff;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
        padding: 4px 8px;
        display: none;
    }

    tr:hover .remove-row-btn {
        display: inline-block;
    }


    .table-container::-webkit-scrollbar {
        height: 8px;
        /* Scrollbar height */
    }

    .table-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        /* Scrollbar track color */
    }

    .table-container::-webkit-scrollbar-thumb {
        background-color: #007bff;
        /* Scrollbar thumb color */
        border-radius: 4px;
        /* Rounded corners */
        border: 1px solid #f1f1f1;
        /* Optional: border around the thumb */
    }

    .table-container::-webkit-scrollbar-thumb:hover {
        background-color: #0056b3;
        /* Darker color on hover */
    }
</style>
<div class="modal-header">
    <h5 class="modal-title base-color" id="addLeadModalLabel"><img
            src="<?= $baseUrl; ?>/thememain/img/module-icon/<?= $ModuleName; ?>.png" class=" head-img">Add
        <?= ucfirst($ModuleName) ?></h5>
    <div class="toggle-container">
        <div class="toggle-switch" onclick="toggleRequiredFields()"></div>
        Show Required & Important Fields
    </div>
    <button type="button" class="btn-close" aria-label="Close"></button>
</div>
<?php $form = ActiveForm::begin([
    "id" => "pristine-valid-example",
    'options' => [
        'enctype' => 'multipart/form-data', // Required for file uploads
    ],
]); ?>
<div class="modal-body">
    <!-- <div class="select-1"> -->
    <div class="row">
        <div class="col-12">
        <div class="title-tab">
            <label class="title-info">LEAD INFORMATION</label>
        </div>
        </div>
        <div class="form-field-cst form-group required-field form-field-cst section-ownerid col-6 mb-2">
            <!--open first col-->
            <div class="   field-uiinputs-ownerid required-field">
                <label class="control-label " for="ownerid">Lead Owner<span class="red"> *</span></label><select
                    id="ownerid" class="form-control DD~M " name="leadinformation[ownerid]"
                    data-pristine-required="true" data-pristine-required-message="Lead Owner is required ">
                    <option value="">Select Lead Owner</option>
                    <option value="1" selected="">Deepika Tetra</option>
                    <option value="2">Ravindra Sales</option>
                    <option value="5">Swati Mange</option>
                    <option value="8">Sales1 Executive1</option>
                    <option value="9">Sales2 Executive2</option>
                    <option value="10">Sales3 Executive3</option>
                    <option value="11">Sales4 Executive4</option>
                    <option value="12">Vertical1 Manager1</option>
                    <option value="13">Vertical2 Manager2</option>
                    <option value="14">Vertical3 Manager3</option>
                    <option value="15">Vertical4 Manager4</option>
                    <option value="16">VendorAccount1 Manager1</option>
                    <option value="17">VendorAccount2 Manager2</option>
                    <option value="18">VendorAccount3 Manager3</option>
                    <option value="19">VendorAccount4 Manager4</option>
                </select>
                <div class="help-block"></div>
            </div><!-- close form group-->
        </div>
        <!-- col-6 -->
        <div class="form-group   form-field-cst section-lastname inner required-field col-6 mb-2">
            <div class="field-uiinputs-lastname">
                <label class="control-label " for="lastname">Last Name<span class="red"> *</span></label><input
                    type="text" id="lastname" class="form-control  AN~M" name="leadinformation[lastname]" value=""
                    maxlength="100" fieldid="9" data-pristine-required="true"
                    data-pristine-required-message="Last Name is required ">
                <div class="help-block"></div>
                <input type="hidden" id="leadname" class="form-control  V~M" name="leadinformation[leadname]" value=""
                    maxlength="10" fieldid="41" data-pristine-required="true"
                    data-pristine-required-message="Lead Name is required ">
            </div><!-- close form group-->
        </div>
        <!-- col 6 -->
        <div class="form-group   form-field-cst section-lastname inner required-field col-6 mb-2">
            <div class="field-uiinputs-lastname">
                <label class="control-label " for="lastname">Last Name<span class="red"> *</span></label><input
                    type="text" id="lastname" class="form-control  AN~M" name="leadinformation[lastname]" value=""
                    maxlength="100" fieldid="9" data-pristine-required="true"
                    data-pristine-required-message="Last Name is required ">
                <div class="help-block"></div>
                <input type="hidden" id="leadname" class="form-control  V~M" name="leadinformation[leadname]" value=""
                    maxlength="10" fieldid="41" data-pristine-required="true"
                    data-pristine-required-message="Lead Name is required ">
            </div><!-- close form group-->
        </div>

    </div>
    <div class="row">
        <div class="col-12">
        <div class="title-tab">
            <label class="title-info">Product Details</label>
        </div>
        </div>
        <div class="col-12">
        <div class="table-container">
            <table id="productTable">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Product Description</th>
                        <th>Category</th>
                        <th>Sub Category</th>
                        <th>Category</th>
                        <th>Sub Category</th>
                        <th>Category</th>
                        <th>Sub Category</th>
                        <th>Category</th>
                        <th>Sub Category</th>
                        <th>Category</th>
                        <th>Sub Category</th>
                        <th>HSN Code</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input type="text" placeholder="UPS Battery"></td>
                        <td><input type="text" placeholder="Battery - UPS Battery"></td>
                        <td><input type="text" placeholder="Battery Waste"></td>
                        <td><input type="text" placeholder="Battery"></td>
                        <td><input type="text" placeholder="85481010"></td>
                        <td><input type="text" placeholder="UPS Battery"></td>
                        <td><input type="text" placeholder="Battery - UPS Battery"></td>
                        <td><input type="text" placeholder="Battery Waste"></td>
                        <td><input type="text" placeholder="Battery"></td>
                        <td><input type="text" placeholder="85481010"></td>
                        <td><input type="text" placeholder="UPS Battery"></td>
                        <td><input type="text" placeholder="Battery - UPS Battery"></td>
                        <td><input type="text" placeholder="Battery Waste"></td>
                        <td><button class="remove-row-btn">X</button></td>
                    </tr>
                    <tr>
                        <td><input type="text" placeholder="DG Set 15KVA"></td>
                        <td><input type="text" placeholder="15KVA"></td>
                        <td><input type="text" placeholder="E-Waste"></td>
                        <td><input type="text" placeholder="E-Waste"></td>
                        <td><input type="text" placeholder="998713"></td>
                        <td><button class="remove-row-btn">X</button></td>
                    </tr>
                    <tr>
                        <td><input type="text" placeholder="Access Point"></td>
                        <td><input type="text" placeholder="Network Accessories - Access Point"></td>
                        <td><input type="text" placeholder="E-Waste"></td>
                        <td><input type="text" placeholder="Network Accessories"></td>
                        <td><input type="text" placeholder="85291011"></td>
                        <td><button class="remove-row-btn">X</button></td>
                    </tr>
                </tbody>
            </table>

        </div>
        </div>
    </div>
    <div class="row">
    <div class="col-3">
        <button class="btn btn-primary" type="button" id="addRowBtn">+ Add row</button>
    </div>
    </div>
</div>
<!-- </div> -->

<script nonce="<?= Yii::$app->params['cspNonce'] ?>">
    document.getElementById('addRowBtn').addEventListener('click', function () {
        // Get the table body
        const tableBody = document.querySelector('#productTable tbody');

        // Create a new row
        const newRow = document.createElement('tr');

        // Define the columns for the new row
        const columns = ['Product Name', 'Product Description', 'Category', 'Sub Category', 'HSN Code', 'Product Name', 'Product Description', 'Category', 'Sub Category', 'HSN Code', 'Sub Category', 'HSN Code', 'HSN Code'];

        columns.forEach(column => {
            const cell = document.createElement('td');
            const input = document.createElement('input');
            input.type = 'text';
            input.placeholder = column;
            cell.appendChild(input);
            newRow.appendChild(cell);
        });

        // Add remove button to the new row
        const actionCell = document.createElement('td');
        const removeButton = document.createElement('button');
        removeButton.className = 'remove-row-btn';
        removeButton.textContent = 'X';
        removeButton.addEventListener('click', function () {
            newRow.remove();
        });
        actionCell.appendChild(removeButton);
        newRow.appendChild(actionCell);

        // Append the new row to the table body
        tableBody.appendChild(newRow);
    });

    // Add remove functionality to existing rows
    document.querySelectorAll('#productTable tbody tr').forEach(row => {
        const actionCell = row.querySelector('td:last-child');
        const removeButton = actionCell.querySelector('.remove-row-btn');
        removeButton.addEventListener('click', function () {
            row.remove();
        });
    });
</script>
</div>
<div class="modal-footer">
    <?= Html::Button("Save", ["class" => "btn btn-primary savebutton",]) ?>
    <?= Html::Button("Cancel", ["class" => "btn mod-close btn-secondary", "name" => "btncancel",]) ?>
</div>
<?php ActiveForm::end(); ?>
<?php
die;
?>