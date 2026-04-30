<?php

use backend\assets\AdminAsset;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Profile */
/* @var $form yii\widgets\ActiveForm */

$this->registerCssFile('@web/thememain/css/profile.css', ['depends' => [AdminAsset::class]]);
?>
<style type="text/css">
    /* Basic styling */
    .text-right {
        text-align: right;
    }

    .module {
        border: 1px solid #ddd;
        margin-bottom: 10px;
        border-radius: 5px;
        overflow: hidden;
    }

    /* Header styling */
    .module-header {
        background-color: #f9f9f9;
        padding: 10px;
        font-weight: bold;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* Arrow styling */
    .module-header .arrow {
        font-size: 14px;
        transition: transform 0.3s;
    }

    /* Rotate arrow when active */
    .module-header.active .arrow {
        transform: rotate(180deg);
    }

    /* Collapsible content */
    .module-content {
        display: none;
        /* Initially hidden */
        padding: 10px;
        background-color: #ffffff;
    }

    /* Table styling */
    table {
        width: 100%;
        border-collapse: collapse;
    }

    th,
    td {
        padding: 8px;
        border: 1px solid #ddd;
        /* text-align: center; */
    }

    .write {
        color: green;
        font-weight: bold;
    }

    .read-only {
        color: orange;
    }

    .invisible {
        color: black;
    }

    /* Add color legends based on your design */


    /* toogle css */


    .toggle {
        display: inline-block;
        width: 60px;
        height: 20px;
        border-radius: 10px;
        background-color: lightgray;
        position: relative;
        cursor: pointer;
    }

    .indicator {
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background-color: gray;
        position: absolute;
        top: 2px;
        transition: all 0.3s ease;
    }

    /* State styles */
    .state-invisible {
        background-color: lavender;
    }

    .state-invisible .indicator {
        background-color: black;
        left: 2px;
    }

    .state-read-only {
        background-color: lavender;
    }

    .state-read-only .indicator {
        background-color: orange;
        left: 22px;
    }

    .state-write {
        background-color: lavender;
    }

    .state-write .indicator {
        background-color: green;
        left: 42px;
    }
</style>
<?php $form = ActiveForm::begin(["id" => "pristine-valid-example"]); ?>

<div class="profile-form">


    <div class="row mb-2">
        <div class="col-md-6">

            <?= $form->field($model, 'profilename', [
                'template' => "{label}<span class='red'> *</span><br>{input}\n{hint}\n{error}",
                'labelOptions' => ['class' => '', 'for' => '']
            ])->textInput(['maxlength' => true, 'class' => $classadd . ' form-control V~M'])->label('Profile Name') ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'description', [
                'template' => "{label}<span class='red'> *</span><br>{input}\n{hint}\n{error}",
                'labelOptions' => ['class' => '', 'for' => '']
            ])->textInput(['maxlength' => true, 'class' => $classadd . ' form-control V~M'])->label('Description') ?>
        </div>
        <!-- code added by ptpatel -->
         <?php
                $selectedwidgets = [];
                if (!empty($model->profileid)) {
                    // Fetch all widget access records for this profile
                    $widgetsaccess = Yii::$app->db->createCommand("
                        SELECT widgetid FROM profile2widget WHERE profileid = :profileid
                    ")
                    ->bindValue(':profileid', $model->profileid)
                    ->queryAll();
                    

                    // profile2widget likely has one widgetid per row
                    foreach ($widgetsaccess as $access) {
                        $selectedwidgets[] = $access['widgetid'];
                    }
                }
                ?>
        <div class="col-md-12">
            <label class="" for="">widgets</label>
            <div class="form-group field-roles-profile required">
                <select class="form-control multySelect DD~M" id="widgets" name="widgets[]" multiple = 'true'>
                    <?php 
                    if(!empty($selectedwidgets[0])){
                                foreach ($widgets as $widget): ?>
                                    <option value="<?= $widget['id'] ?>"
                                        <?php
                                          echo  in_array($widget['id'], explode(",",$selectedwidgets[0])) ? 'selected' : '';
                                        ?>>
                                        <?= $widget['title'] ?>
                                    </option>
                            <?php endforeach; 
                    }
                    else{
                        ?>
                        <?php foreach ($widgets as $widget): ?>
                            <option value="<?= $widget['id'] ?>"><?= $widget['title'] ?></option>
                        <?php
                        endforeach; 
                    }?>
                                
                </select>

                <div class="help-block"></div>
            </div>
        </div>
        <!-- end code added by ptpatel for maindashboard -->
    </div>
    <div class="row">
        <div class="col-md-12 " style="">
            <div class="table-responsive">
                <table id="dtrecord1" class="table table-striped table-bordered" width="100%" cellspacing="0"
                    style="text-align: left !important">

                    <thead>
                        <tr>
                            <th><input type="checkbox" name="" class="alltabs"></th>
                            <th>All Profiles</th>
                            <th>View</th>
                            <th>Create</th>
                            <th>Edit</th>
                            <th>Delete</th>
                            <th>Approve</th>
                            <th>Import</th>
                            <th>Export</th>
                            <th>Fields & Tools Privilige</th>
                            <!-- <th><input type="text" value="<?= $model->profileid ?>"> </th> -->

                        </tr>
                    </thead>
                    <tbody>
                        <?php

                        $sql = "select tabid,tablabel from tab where presence=0";
                        $data = Yii::$app->db->createCommand($sql)->queryAll();
                        $sql .= " ORDER BY tablabel asc";
                        $result = Yii::$app->db->createCommand($sql)->queryAll();
                        foreach ($result as $key => $value) {
                            //Added by vishwas sinha to hide tab Profile, Roles, Picklist, Picklist dependence, All system Controller tabs 31-01-2026
                            if(isset($value['tabid']) && is_int($value['tabid']) && in_array($value['tabid'],[37,38,93,94,105,107,108,115,116,117,118,119])){
                                continue;
                            }
                            $checktab = '';
                            $checkview = '';
                            $checkedit = '';
                            $checkcreate = '';
                            $checkdelete = '';
                            $checkapprove = '';
                            $checkimport = '';
                            $checkexport = '';
                            $tabid = $value['tabid'];

                            $tablabel = $value['tablabel'];

                            //get from profile to tab
                            if (!empty($model->profileid)) {
                                $tabtoprofile = Yii::$app->db->createCommand("select * from profile2tab where profileid=" . $model->profileid . " and tabid=" . $tabid)->queryOne();
                                if ($tabtoprofile)
                                    $checktab = "checked";

                                //get permissions
                                // echo "select * from  profile2standardpermissions where profileid=" . $model->profileid . " and tabid=" . $tabid . " and operation=1 and permissions=0 ";
                                $tabtoview = Yii::$app->db->createCommand("select * from  profile2standardpermissions where profileid=" . $model->profileid . " and tabid=" . $tabid . " and operation=3 and permissions=0 ")->queryOne();
                                if ($tabtoview)
                                    $checkview = "checked";

                                $tabtocreate = Yii::$app->db->createCommand("select  * from   profile2standardpermissions where profileid=" . $model->profileid . " and tabid=" . $tabid . " and operation=0 and permissions=0 ")->queryOne();
                                if ($tabtocreate)
                                    $checkcreate = "checked";

                                $tabtoedit = Yii::$app->db->createCommand("select  * from   profile2standardpermissions where profileid=" . $model->profileid . " and tabid=" . $tabid . " and operation=1 and permissions=0 ")->queryOne();
                                if ($tabtoedit)
                                    $checkedit = "checked";

                                $tabtodelete = Yii::$app->db->createCommand("select  * from   profile2standardpermissions where profileid=" . $model->profileid . " and tabid=" . $tabid . " and operation=2 and permissions=0 ")->queryOne();
                                if ($tabtodelete)
                                    $checkdelete = "checked";

                                $tabtoapprov = Yii::$app->db->createCommand("select  * from   profile2standardpermissions where profileid=" . $model->profileid . " and tabid=" . $tabid . " and operation=5 and permissions=0 ")->queryOne();
                                if ($tabtoapprov)
                                    $checkapprove = "checked";

                                $tabtoimport = Yii::$app->db->createCommand("select  * from   profile2standardpermissions where profileid=" . $model->profileid . " and tabid=" . $tabid . " and operation=4 and permissions=0 ")->queryOne();
                                if ($tabtoimport)
                                    $checkimport = "checked";

                                $tabtoexport = Yii::$app->db->createCommand("select  * from   profile2standardpermissions where profileid=" . $model->profileid . " and tabid=" . $tabid . " and operation=6 and permissions=0 ")->queryOne();
                                if ($tabtoexport)
                                    $checkexport = "checked";
                            }


                        ?>
                            <tr>
                                <th><input type="checkbox" class="" <?= $checktab; ?> value="<?= $value['tabid'] ?>"
                                        name="tabs[]"></th>
                                <th><?php echo $tablabel . (($tabid == 80 || $tabid == 77) ? ' (Report)' : '');?></th>
                                <th><input type="checkbox" class="" <?= $checkview; ?> value="0"
                                        name="3_<?= $value['tabid'] ?>"></th>
                                <th><input type="checkbox" class="" <?= $checkcreate; ?> value="0"
                                        name="0_<?= $value['tabid'] ?>"></th>
                                <th><input type="checkbox" class="" <?= $checkedit; ?> value="0"
                                        name="1_<?= $value['tabid'] ?>"></th>
                                <th><input type="checkbox" class="" <?= $checkdelete; ?> value="0"
                                        name="2_<?= $value['tabid'] ?>"></th>
                                <th><input type="checkbox" class="" <?= $checkapprove; ?> value="0"
                                        name="5_<?= $value['tabid'] ?>"></th>
                                <th><input type="checkbox" class="" <?= $checkimport; ?> value="0"
                                        name="4_<?= $value['tabid'] ?>"></th>
                                <th><input type="checkbox" class="" <?= $checkexport; ?> value="0"
                                        name="6_<?= $value['tabid'] ?>"></th>
                                <!-- if condition code added by ptpatel on date 13-06-25 when report module is there -->
                                
                                <th>
                                    <?php if($tabid != 80 && $tabid != 77){ 
                                        // onclick="hideshow(this,<?php echo $tabid)"; ?>
                                        <span 
                                        data-tabid = "<?php echo $tabid; ?>"
                                         data-handlerfor="fields"
                                        data-togglehandler="2-fields" class="btn btn-sm btn-default profiletabhideshow"
                                        style="padding-right: 20px; padding-left: 20px;"><i
                                            class="<?php echo $tabid; ?> fa fa-chevron-down"></i></span>
                                    <?php } ?>
                                </th>
                                
                                    <!-- end if condition code added by ptpatel on date 13-06-25 when report module is there -->
                                
                            </tr>
                            <tr class="module-content <?php echo $tabid; ?>">
                                <td colspan="10">
                                    <div>
                                        <table>
                                            <!-- <thead>
                                                <tr>
                                                    <th>Fields</th>
                                                    <th>View</th>
                                                    <th>Create</th>
                                                    <th>Edit</th>
                                                    <th>Delete</th>
                                                    <th>Field and Tool Privileges</th>
                                                </tr>
                                            </thead> -->
                                            <tbody>
                                                <!-- <tr>
                                                    <td><?= $tablabel ?></td>
                                                    <td><span class="write">✔</span></td>
                                                    <td><span class="write">✔</span></td>
                                                    <td><span class="write">✔</span></td>
                                                    <td><span class="write">✔</span></td>
                                                    <td><span class="write">Write</span></td>
                                                </tr> -->
                                                <!-- More rows as needed -->




                            </tr>
                    </tbody>

                </table>

                <table>
                    <!-- <thead>
                        <tr>
                            <th>Fields</th>
                            <th>Invisible</th>
                            <th>Read Only</th>
                            <th>Write</th>
                        </tr>
                    </thead> -->
                    <tbody>
                        <!-- Legend -->
                        <div style="margin-bottom: 10px; text-align:right">
                            <span
                                style="background-color: black; color: white; padding: 2px 6px; border-radius:10px;">&nbsp;&nbsp;</span>
                            Invisible
                            <span
                                style="background-color: orange; color: white; padding: 2px 6px; border-radius: 10px;">&nbsp;&nbsp;</span>
                            Readonly
                            <span
                                style="background-color: green; color: white; padding: 2px 6px; border-radius: 10px;">&nbsp;&nbsp;</span>
                            Visible & Editable
                        </div>
                        <?php

                            // Fetch the blockids for 'SYSTEM GENERATED'
                            $blockIds = Yii::$app->db->createCommand("
                             SELECT blockid ,blocklabel
                             FROM blocks  
                             WHERE blocklabel != 'SYSTEM GENERATED' and  tabid = :tabid and edit_view=1
                                                ")
                                ->bindValue(':tabid', $tabid)
                                ->queryAll();

                            // Check if $blockIds is empty to prevent errors
                            // if (!empty($blockIds)) {
                            foreach ($blockIds as $rowblock): //block loop
                                // Prepare the main query
                                // $sql = "SELECT * FROM field 
                                // WHERE tabid = :tabid AND uitype != 2 AND uitype !=11
                                // AND block NOT IN (" . implode(',', $blockIds) . ") 
                                // ORDER BY sequence ASC";
                                echo "<div class='row'><div class='col-12 mt-2 mb-2'><h4 class='bg-default border  p-3 '>" . $rowblock['blocklabel'] . "</h4></div><div class='row'>";
                                $sql = "SELECT * FROM field 
                                WHERE tabid = :tabid AND uitype != 2 AND uitype !=11 
                                AND block =:block 
                                ORDER BY sequence ASC ";
                                // }
                                // Execute the main query
                                $fields = Yii::$app->db->createCommand($sql)
                                    ->bindValue(':tabid', $tabid)
                                    ->bindValue(':block', $rowblock['blockid'])
                                    ->queryAll();

                        ?>
                            <?php foreach ($fields as $row):
                                    // Fetch initial toggle state from `profile2field` table
                                    $profileField = Yii::$app->db->createCommand("
                                     SELECT visible, readonly 
                                     FROM profile2field 
                                     WHERE fieldid = :fieldid AND tabid = :tabid AND profileid=:profileid
                                    ")
                                        ->bindValue(':fieldid', $row['fieldid'])
                                        ->bindValue(':tabid', $tabid)
                                        ->bindValue(':profileid', $model->profileid)
                                        ->queryOne();

                                    $visible = $profileField['visible'] ?? 0;
                                    $readonly = $profileField['readonly'] ?? 0;

                                    // Determine the toggle class based on state
                                    if ($visible == 1 && $readonly == 1) {
                                        // Skip rendering this field
                                        $toggleClass = 'state-invisible';
                                        // continue;
                                    } elseif ($visible == 0 && $readonly == 1) {
                                        $toggleClass = 'state-read-only'; // Orange color
                                    } elseif ($visible == 0 && $readonly == 0) {
                                        $toggleClass = 'state-write'; // Green color
                                    }
                            //Added functionality to hide the field for a particular section if the create,edit,detail view is set to 0 //28/01/2026 Vishwas
                            if(isset($row['edit_view']) && $row['edit_view'] == 0 && isset($row['detail_view']) && $row['detail_view'] == 0 && isset($row['create_view']) && $row['create_view'] == 0){
                                continue;
                            }
                            ?>

                                <div class="col-4">
                                    <div class="row">
                                        <div class="col-6">
                                            <?= htmlspecialchars($row['fieldlabel']); ?>
                                        </div>
                                        <div class="col-6">
                                            <div class=" toggle <?= $toggleClass ?>"                                            
                                                data-fieldname="<?= htmlspecialchars($row['fieldname']); ?>"
                                                data-fieldlabel="<?= htmlspecialchars($row['fieldlabel']); ?>"
                                                data-fieldid="<?= htmlspecialchars($row['fieldid']); ?>"
                                                data-tabid="<?= $row['tabid'] ?>" data-visible="<?= $visible; ?>"
                                                data-readonly="<?= $readonly; ?>" onclick="updateToggleState(this);">
                                                <input type="hidden" name="fields[<?= $row['fieldid']; ?>][fieldname]"
                                                    value="<?= $row['fieldname']; ?>">
                                                <input type="hidden" name="fields[<?= $row['fieldid']; ?>][fieldlabel]"
                                                    value="<?= $row['fieldlabel']; ?>">
                                                
                                                <input type="hidden" name="fields[<?= $row['fieldid']; ?>][fieldid]"
                                                    value="<?= htmlspecialchars($row['fieldid']); ?>">
                                                <input type="hidden" name="fields[<?= $row['fieldid']; ?>][tabid]"
                                                    value="<?= $row['tabid']; ?>">
                                                <input type="hidden" class="toggle-visible"
                                                    name="fields[<?= $row['fieldid']; ?>][visible]" value="<?= $visible; ?>">
                                                <input type="hidden" class="toggle-readonly"
                                                    name="fields[<?= $row['fieldid']; ?>][readonly]" value="<?= $readonly; ?>">
                                                <div class="indicator"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>




                            <?php endforeach; ?>
                        <?php echo "</div>";
                            endforeach; //end block loop 
                        ?>
                    </tbody>
                </table>
            </div>
            </td>
            </tr>





        <?php
                        }
        ?>
        </tbody>

        <tfoot>

            </table>

        </div>
    </div>
</div>
<div class="form-group">
    <?= Html::Button('SUBMIT', ['class' => 'savebutton btn btn-success']) ?>
    <?= Html::submitButton('DELETE', ['class' => 'btn btn-danger']) ?>
</div>

<?php ActiveForm::end(); ?>


</div>

<?php $this->registerJsFile('@web/js/profile/edit.js', ['depends' => [AdminAsset::class]]); ?>

<script type="text/javascript">
   /* function hideshow(ele, field) {
        const content = $(ele).closest('tr').siblings("tr." + field);
        //alert($(ele).html());
        content.fadeToggle();
        $(ele).children('.fa.' + field).toggleClass('fa-chevron-up fa-chevron-down');


    }

    function toggleContent(element) {
        const content = element.nextElementSibling;
        content.style.display = content.style.display === "block" ? "none" : "block";
        element.classList.toggle("active");
    }



    $(document).ready(function() {


        var dataTable = $('#dtrecord').DataTable({
            "processing": true,
            "serverSide": true,
            "paging": false,
            "ajax": {
                url: "tabs",
                data: {
                    '<?= Yii::$app->request->csrfParam ?>': '<?= Yii::$app->request->getCsrfToken() ?>'
                },
                type: "post",

                error: function() {
                    alert('error');
                }

            },
            "columns": [{
                    "data": "null",
                    "render": (data, type, row, meta) => {
                        if (row['1'] != null)
                            return '<input type="checkbox" class="tabs" value="' + row[0] + '" name="tabs[]" >';
                        else
                            return '<input type="checkbox"  class="tabs" >';
                    }
                },
                {
                    "data": "null",
                    "render": (data, type, row, meta) => {
                        if (row['2'] != null)
                            return row[1] + "(" + row[2] + ")";
                        else
                            return row[1];
                    }
                },
                {
                    "data": "null",
                    "render": (data, type, row, meta) => {
                        if (row['1'] != null)
                            return '<input type="checkbox"  class="" value="1"  name="1_' + row[0] + '" >';
                        else
                            return '<input type="checkbox"  class="" >';
                    }
                },
                {
                    "data": "null",
                    "render": (data, type, row, meta) => {
                        if (row['1'] != null)
                            return '<input type="checkbox"   class=""  value="1"  name="2_' + row[0] + '" >';
                        else
                            return '<input type="checkbox"  class="" >';
                    }
                },
                {
                    "data": "null",
                    "render": (data, type, row, meta) => {
                        if (row['1'] != null)
                            return '<input type="checkbox"  class="" value="1"  name="3_' + row[0] + '"  >';
                        else
                            return '<input type="checkbox"  class="" >';
                    }
                },
                {
                    "data": "null",
                    "render": (data, type, row, meta) => {
                        if (row['1'] != null)
                            return '<input type="checkbox"  class="" value="1"  name="4_' + row[0] + '" >';
                        else
                            return '<input type="checkbox"  class="" >';
                    }
                },
                {
                    "data": "null",
                    "render": (data, type, row, meta) => {
                        if (row['1'] != null)
                            return '<input type="checkbox"  class=""  value="1"  name="5_' + row[0] + '" >';
                        else
                            return '<input type="checkbox"  class="" >';
                    }
                },
                {
                    "data": "null",
                    "render": (data, type, row, meta) => {
                        if (row['1'] != null)
                            return '<input type="checkbox"  class=""  value="1"  name="6_' + row[0] + '" >';
                        else
                            return '<input type="checkbox"  class="" >';
                    }
                },
            ],



        });






    });*/
</script>