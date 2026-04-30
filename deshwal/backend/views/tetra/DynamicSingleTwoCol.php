<?php
// use Yii;
//get all roles with is account
if ($role == "deshwal") {
    echo "</div><!-- end deshwal-->";
    $tablnamee = 'vendor_account_orgaisation_section';
    $sql = "select * from role where showinaccounts=1 and (parentrole like '%H2::%' || parentrole like '%H57::%')";
} else {
    $tablnamee = 'vendor_account_oem_manager_detail';

    echo '<div class="row blockrow' . $block->blockid . '">';

    $sql = "select * from role where showinaccounts=1 and (parentrole not like '%H2::%' and parentrole not like '%H57::%')";
}

$result = Yii::$app->db->createCommand($sql)->queryAll();
// print_r($result);
$i = 1;
$count = count($result);
// echo $count;die;
foreach ($result as $value) {
    //get admin_edit_allow from role table to do readonly dd if admin_edit_allow = 0 added by ptpatel on date 26-03-2206
    $admin_edit_allow = $readonly_edit_allow = '';
    if ($hasadminpower == 0 && $value['admin_edit_allow'] == 1) {
		$admin_edit_allow = 'readonly-dd';
        $readonly_edit_allow = 'readonly';
	}
    //get admin_edit_allow from role table to do readonly dd if admin_edit_allow = 0 
    //get all the user of this role
    $sql = "select * from user join user2role on user2role.userid = user.id where user2role.roleid=:roleid and deleted =0 and status=10 group by userid";
    $userresult = Yii::$app->db->createCommand($sql)
        ->bindValue(":roleid", $value['roleid'])
        ->queryAll();
        // echo $RecordId;die;
    if(isset($RecordId))
    {
    $sqlv = "select userid from $tablnamee where roleid=:roleid and vendoraccid=:vendoraccid";
    $vresult = Yii::$app->db->createCommand($sqlv)
            // ->bindParam(":tablnamee", $tablnamee)
            ->bindValue(":roleid", $value['roleid'])
            ->bindValue(":vendoraccid", $RecordId)
            ->queryOne();
           if(!empty($vresult))
            $roleid = $vresult['userid'];
        else $roleid='';
    }
    else $roleid = '';
    ?>

    <div class="form-group  not-required-field form-field-cst section-<?= $value['rolename'] ?> col-lg-3 col-md-6 mb-2">
        <label class="control-label " title="<?= $value['rolename'] ?>"
            for="<?= $value['rolename'] ?>"><?= $value['rolename'] ?></label><!-- <link rel="stylesheet" href="< $baseUrl; ?>/thememain/css/select2.min.css">
<link rel="stylesheet" href="< $baseUrl; ?>/thememain/css/multilist-dd.css"> -->

        <br>
        <input type="hidden" value="<?= $value['roleid'] ?>" name="<?= $tablnamee;?>[<?= $i; ?>][roleid]">
        <select id="<?= $value['rolename'] ?>" class="form-control 0 DD~O singleselect <?=  $admin_edit_allow; ?>"
            name="<?= $tablnamee;?>[<?= $i; ?>][userid]" value=""  <?= $readonly_edit_allow; ?>>
            <option value="">Select</option>
            <?php
            foreach ($userresult as $val) {
                if($roleid == $val['id']) 
                $sel = "selected";
                else $sel = '' ?>
                <option value="<?= $val['id']; ?>" <?= $sel; ?> ><?= $val['first_name'] . " " . $val['last_name'] ?></option>
                <?php
            } ?>

        </select>
        <!-- <script type="text/javascript" src="< $baseUrl; ?>thememain/js/tetra/single-dd.js"></script> -->
        <div class="help-block"></div>
    <?php
    if($i < $count)
    {?>
    </div>
    <!-- end col-6 -->
    <?php
    }
    $i++;
}
if ($role != "deshwal")
echo "</div></div><!--deep-->";
?>