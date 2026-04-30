<?php 
use yii\helpers\Url;
use yii\helpers\Html;
use backend\assets\AdminAsset;

$csrfTokenName = Yii::$app->request->csrfParam;  // This replaces csrfTokenName
$csrfToken = Yii::$app->request->csrfToken; // Get the CSRF token itself
$ModuleName   =   $arrRender['modulename'];
$TabId  =   $arrRender['TabId'];
$uid = $arrRender['uid'];
$baseUrl = Yii::$app->HomeUrl;
?>
<link href="<?= $baseUrl;?>/thememain/css/bootstrap.min.css" rel="stylesheet">
<link href="<?= $baseUrl;?>/thememain/css/multiple.css" rel="stylesheet">
<link href="<?= $baseUrl;?>/thememain/css/select2.min.css" rel="stylesheet">
<link href="<?= $baseUrl;?>/thememain/css/multilist-dd.css" rel="stylesheet">
<link href="<?= $baseUrl;?>/thememain/css/singleedit.css" rel="stylesheet">
<link href="<?= $baseUrl;?>/thememain/css/bootstrap-timepicker.min.css" rel="stylesheet">
<link href="<?= $baseUrl; ?>thememain/css/flatpickr.min.css" rel="stylesheet">

<script type="text/javascript" src="<?= $baseUrl;?>thememain/jquery/jquery.min.js"></script>
<script type="text/javascript" src="<?= $baseUrl;?>thememain/bootstrap/bootstrap.min.js"></script>
<script type="text/javascript" src="<?= $baseUrl;?>thememain/js/select2.min.js"></script>
<script type="text/javascript" src="<?= $baseUrl;?>thememain/js/tetra/single-dd.js"></script>
<script type="text/javascript" src="<?= $baseUrl;?>thememain/js/tetra/multilist-dd.js"></script>
<script type="text/javascript" src="<?= $baseUrl;?>thememain/js/tetra/singleeditvalidation.js"></script>
<script type="text/javascript" src="<?= $baseUrl;?>thememain/js/tetra/singleeditvalidate.js"></script>
<script type="text/javascript" src="<?= $baseUrl;?>thememain/js/flatpickr.js"></script>

<form id="editsummerydetails" enctype="multipart/form-data">
    <input type="hidden" name="csrfToken" id="csrfToken" value="<?= $csrfToken; ?>">
    <input type="hidden" name="csrfTokenName" id="csrfTokenName" value="<?= $csrfTokenName; ?>">
    <input type="hidden" value="<?= $arrRender['field']['recordid']; ?>" id="recordid" name="recordid"/>
    <input type="hidden" value="<?= $arrRender['modulename']; ?>" id="ModuleName" name="module"/>
    <input type="hidden" value="edit" id="mode" name="mode"/>
    <input type="hidden" value="singleedit" id="singleedit" name="singleedit"/>
    <!-- for part refresh after editing is done -->
    <input type="hidden" value="<?= $arrRender['tablename']?>" id="tablename" name="tablename"/>
    <input type="hidden" value="<?= $arrRender['columnname']?>" id="columnname" name="columnname"/>
    <input type="hidden" value="<?= $arrRender['TabId']; ?>" id="TabId" name="TabId"/>
    <input type="hidden" value="<?= $from_page;?>" name="from_page" id="from_page"/>
    <?php if($from_page != "list"){?>
        <input type="hidden" value="<?= $arrRender['field']['uitype']; ?>" id="uitype" name="uitype"/>
        <input type="hidden" value="<?= $arrRender['field']['fieldlabel']; ?>" id="fieldlabel" name="fieldlabel"/>
        <input type="hidden" value="<?= $arrRender['field']['fieldid']; ?>" id="fieldid" name="fieldid"/>
    <?php } ?>
    
    
    <!-- for document -->
    <input type="hidden" value="<?= date('Y-m-d H:i:s') ?>" id="modifiedtime" name="<?= $arrRender['tablename']; ?>[modifiedtime]"/>
    <!-- for document folderid is required -->
     <?php if($arrRender['tablename']   == 'documents' ){?>
        <input type="hidden" value="<?=  $Record['folderid']; ?>" id="folderid" name="<?= $arrRender['tablename']; ?>[folderid]"/>
    <?php } ?>
        <input type="hidden" value="edit" id="mode" name="mode"/>
    <div class="mb-3">
        <!-- <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-12"> -->
                <?php require 'editSummeryCol.php'; ?>
            <!-- </div>
        </div> -->
         
    </div>
</form>
<?php die; ?>