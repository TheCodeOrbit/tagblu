<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

$baseurl = Url::base();
use backend\assets\AdminAsset;

AdminAsset::register($this);

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'ROLES';
$this->params['breadcrumbs'][] = $this->title;

$this->registerCssFile('@web/thememain/css/roles.css', ['depends' => [AdminAsset::class]]);

// $url = Yii::app()->getBaseUrl(true);
// $baseurl = Yii::app()->request->hostInfo.Yii::app()->homeUrl;

$q_orgheaddetails = "SELECT * FROM role WHERE depth='0'";

$orgheaddetails = Yii::$app->db->createCommand($q_orgheaddetails)->queryAll();
$addbtn = '<div class="action-icon-container-label ">
   <span>Add</span>
</div>
<svg viewBox="0 0 16 16" class="action-icon action-icon--add crole">
   <path d="M6 16H10V10H16V6H10V0H6V6H0V10H6V16Z" fill="var(--color-primary)"></path>
</svg>';

$deletebtn = '<div class="action-icon-container-label">
<span>Delete</span>
</div>
<svg viewBox="0 0 18 19" class="action-icon action-icon--delete dlt-btn">
<path d="M5.14414 15.2656H12.8539L13.2793 6.26562H4.71875L5.14414 15.2656Z"></path>
<path d="M15.1875 5H12.9375V3.59375C12.9375 2.97324 12.433 2.46875 11.8125 2.46875H6.1875C5.56699 2.46875 5.0625 2.97324 5.0625 3.59375V5H2.8125C2.50137 5 2.25 5.25137 2.25 5.5625V6.125C2.25 6.20234 2.31328 6.26562 2.39062 6.26562H3.45234L3.88652 15.459C3.91465 16.0584 4.41035 16.5312 5.00977 16.5312H12.9902C13.5914 16.5312 14.0854 16.0602 14.1135 15.459L14.5477 6.26562H15.6094C15.6867 6.26562 15.75 6.20234 15.75 6.125V5.5625C15.75 5.25137 15.4986 5 15.1875 5ZM6.32812 3.73438H11.6719V5H6.32812V3.73438ZM12.8549 15.2656H5.14512L4.71973 6.26562H13.2803L12.8549 15.2656Z"></path>
</svg>';

$editbtn = '<div class="action-icon-container-label">
<span>Edit</span>
</div>
<svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="action-icon edit-btn">
<path d="M17.1102 5.40807L19.2322 7.52507L17.1102 5.40807ZM18.4748 3.54307L12.7368 9.27007C12.4404 9.56557 12.2382 9.94205 12.1557 10.3521L11.6257 13.0001L14.2788 12.4701C14.6896 12.3881 15.0663 12.1871 15.3628 11.8911L21.1008 6.16407C21.2732 5.99197 21.41 5.78766 21.5033 5.56281C21.5966 5.33795 21.6446 5.09695 21.6446 4.85357C21.6446 4.61019 21.5966 4.36919 21.5033 4.14433C21.41 3.91948 21.2732 3.71517 21.1008 3.54307C20.9284 3.37097 20.7237 3.23446 20.4984 3.14132C20.2731 3.04818 20.0316 3.00024 19.7878 3.00024C19.5439 3.00024 19.3025 3.04818 19.0772 3.14132C18.8519 3.23446 18.6472 3.37097 18.4748 3.54307V3.54307Z" stroke="#198754" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
<path d="M19.6409 15V18C19.6409 18.5304 19.4298 19.0391 19.054 19.4142C18.6782 19.7893 18.1686 20 17.6371 20H6.61612C6.08468 20 5.575 19.7893 5.19921 19.4142C4.82342 19.0391 4.6123 18.5304 4.6123 18V7C4.6123 6.46957 4.82342 5.96086 5.19921 5.58579C5.575 5.21071 6.08468 5 6.61612 5H9.62185" stroke="#198754" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
</svg>';
?>

<div class="page-content">
</div>
<div class="select-1">
<div class="container-d">
      <div class="row clearfix">
         <div class="col-md-12 text-left">
            <h2> <?= $this->title ?> </h2>
         </div>
         <div class="col-lg-12">
            <!-- role started -->
            <?php

function renderRoles($parentRole, $depth, $baseurl, $addbtn, $editbtn, $deletebtn) {
    // Prepare the pattern to find children
    $parentRolePattern = $parentRole ? $parentRole . "::%" : "%";
    $sql = "SELECT * FROM role WHERE depth = :depth AND parentrole LIKE :parentRole";
    $roles = Yii::$app->db->createCommand($sql)
        ->bindValue(':depth', $depth)
        ->bindValue(':parentRole', $parentRolePattern . '%')
        ->queryAll();

    if (empty($roles)) return;

    echo '<ul class="roles-ul">';

    foreach ($roles as $role) {
        $roleId = $role['roleid'];
        $roleLike = $parentRole ? $parentRole . "::" . $roleId : $roleId;
        ?>
        <li>
            <div class="actions-container d-flex justify-content-between align-items-center gap-3rem">
                <button class="header-elements roles-hover"><?php echo $role['rolename']; ?></button>
                <div class="actions">
                    <div class="d-flex gap-3rem">
                        <a href="<?php echo $baseurl; ?>/roles/addroles?roleid=<?php echo base64_encode($roleId); ?>"
                           class="action-icon-container d-flex justify-content-center align-items-center">
                            <?php echo $addbtn; ?>
                        </a>
                        <a href="<?php echo $baseurl; ?>/roles/editroles?roleid=<?php echo base64_encode($roleId); ?>"
                           class="action-icon-container d-flex justify-content-center align-items-center">
                            <?php echo $editbtn; ?>
                        </a>
                        <a disabled href="<?php echo $baseurl; ?>/roles/roledelete?roleid=<?php echo base64_encode($roleId); ?>"
                           onclick="return confirm('Are You sure?')"
                           class="action-icon-container d-flex justify-content-center align-items-center " style="pointer-events: none;cursor: not-allowed;opacity: 0.5;">
                            <?php echo $deletebtn; ?>
                        </a>
                    </div>
                </div>
            </div>
        </li>
        <?php
        // Recursive call for children
        renderRoles($roleLike, $depth + 1, $baseurl, $addbtn, $editbtn, $deletebtn);
    }

    echo '</ul>';
}
?>

<!-- Initial Call -->
<?php
foreach ($orgheaddetails as $orgheaddetail) {
    $roleId = $orgheaddetail['roleid'];
    ?>
    <ul class="roles-ul">
        <li>
            <div class="actions-container d-flex justify-content-between align-items-center gap-3rem">
                <button class="header-elements roles-hover"><?php echo $orgheaddetail['rolename']; ?></button>
                <div class="actions">
                    <div class="d-flex gap-3rem">
                        <a href="<?php echo $baseurl; ?>/roles/addroles?roleid=<?php echo base64_encode($roleId); ?>"
                           class="action-icon-container d-flex justify-content-center align-items-center">
                            <?php echo $addbtn; ?>
                        </a>
                    </div>
                </div>
            </div>
        </li>
        <?php
        renderRoles($roleId, 1, $baseurl, $addbtn, $editbtn, $deletebtn);
        ?>
    </ul>
<?php } ?>

            <!--- role ended -->
         </div>
      </div>
      </div>
   </div>
