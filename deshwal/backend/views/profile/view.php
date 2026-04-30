<?php

use yii\helpers\Html;
use backend\assets\AdminAsset;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Profile */

$this->title = "Update Profile ID " . $model->profileid;
$this->params['breadcrumbs'][] = ['label' => 'Profiles', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

AdminAsset::register($this);
?>
<style type="text/css">
    /* Basic styling */


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
        text-align: center;
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
<div class="select-1">
    <div class="container-d">

        <!-- <h1>< Html::encode($this->title) ?></h1> -->

        <div class="row">
            <div class="col-12">
                <?= Html::a('Update', ['update', 'profileid' => $model->profileid], ['class' => 'btn btn-primary']) ?>
                <!-- < Html::a('Delete', ['delete', 'profileid' => $model->profileid], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
             ]) ?> -->
                <br><br>


            </div>
            <div class="col-12">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'profileid',
                        'profilename',
                        'description',
                    ],
                ]) ?>
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
                                    $checktab = '';
                                    $checkview = '';
                                    $checkedit = '';
                                    $checkcreate = '';
                                    $checkdelete = '';
                                    $checkapprove = '';
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
                                        <th>
                                            <?php if ($tabid != 80 && $tabid != 77) {  ?>
                                                <span
                                                    <?php //onclick="hideshow(this,<?php echo $tabid; 
                                                    ?>
                                                    data-tabid="<?php echo $tabid; ?>"
                                                    data-handlerfor="fields"
                                                    data-togglehandler="2-fields" class="btn btn-sm btn-default profiletabhideshow"
                                                    style="padding-right: 20px; padding-left: 20px;"><i
                                                        class="<?php echo $tabid; ?> fa fa-chevron-down"></i></span>
                                            <?php } ?>
                                        </th>

                                    </tr>
                                    <tr class="module-content <?php echo $tabid; ?>">
                                        <td colspan="8">
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
                                                continue;
                                            } elseif ($visible == 0 && $readonly == 1) {
                                                $toggleClass = 'state-read-only'; // Orange color
                                            } elseif ($visible == 0 && $readonly == 0) {
                                                $toggleClass = 'state-write'; // Green color
                                            }
                                    ?>

                                        <div class="col-4">
                                            <div class="row">
                                                <div class="col-6">
                                                    <?= htmlspecialchars($row['fieldlabel']); ?>
                                                </div>
                                                <div class="col-6">
                                                    <div class=" toggle <?= $toggleClass ?>"
                                                        data-fieldid="<?= htmlspecialchars($row['fieldid']); ?>"
                                                        data-tabid="<?= $row['tabid'] ?>" data-visible="<?= $visible; ?>"
                                                        data-readonly="<?= $readonly; ?>" onclick="updateToggleState(this);">
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

    </div>
</div>
<?php $this->registerJsFile('@web/js/profile/edit.js', ['depends' => [AdminAsset::class]]); ?>
<script type="text/javascript">
    function hideshow(ele, field) {
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
                            return '<input type="checkbox" class="" value="' + row[0] + '" name="tabs[]" >';
                        else
                            return '<input type="checkbox"  class="" >';
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
            ],



        });






    });
</script>