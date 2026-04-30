<?php
error_reporting(-1);
ini_set('display_errors', true);
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

use backend\assets\AdminAsset;

AdminAsset::register($this);

// $csrfToken = Yii::$app->request->csrfToken;
// $csrfTokenName = Yii::$app->request->csrfTokenName;

$csrfTokenName = Yii::$app->request->csrfParam;  // This replaces csrfTokenName
$csrfToken = Yii::$app->request->csrfToken;      // Get the CSRF token itself


$siteDir = Yii::$app->params['dirName'];
$ModuleName=$ActionList['ModuleName'];
$TableName = $TableName;

$ActionName=$ActionList['ActionName'];
$ModuleLabel=$ActionList['ModuleLabel'];
$_SESSION['countpro']="";
$_SESSION['taxcounterr']="";
$sesionid=isset($_SESSION[$siteDir.'_id'])?$_SESSION[$siteDir.'_id']:'deshwal';
$relationName = 'quickcreate';

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
$popurl =$fullurl.'/popuplist';
$relativeHomeUrl = Url::home(); 
$absoluteHomeUrl = Url::home(true);
$web = $absoluteHomeUrl."theme/libs/pristinejs/pristinejs.min.js";
//echo $fullurl ; exit ;
?>
<!-- // <script src="<?php //echo Yii::$app->request->baseUrl; ?>/js/chosen.jquery.min.js"></script> -->

<!-- <script src="<?php //echo Yii::$app->request->baseUrl; ?>/js/jquery.inputmask.bundle.js"></script> -->
<!-- <script type="text/javascript">var fullurl = "<?= $fullurl ?>";</script> -->
<!-- // <script src="<?php //echo Yii::$app->request->baseUrl; ?>/js/cookie.js"></script> -->
<!-- <link rel="stylesheet" type="text/css" href="<?php echo Yii::$app->request->baseUrl; ?>/css/jquery.datetimepicker.css"/> -->
<h2 style="background-color: #2d4d7e;
  padding: 7px;
  color: #fff;">Quick Create <?= $ModuleLabel;?></h2>
<div class="create-form1" style="
  padding: 30px;">
   <script  src="<?php echo $web;?>"></script>
	
		<?php $form = ActiveForm::begin(['id' => 'pristine-valid-example']); ?>
		<div class="row">
			<?php 
				$currentdate=date('Y-m-d'); 
				$finaldate =strtolower(date("Y-m-d", strtotime("-1 day" , strtotime( $currentdate ))));

				$currentdate1=date('d-m-Y'); 
				$finaldateshow =strtolower(date("d-m-Y", strtotime("-1 day" , strtotime( $currentdate1 ))));
			?>
			<!-- <span class="note">Fields with <span class="required star">&nbsp;*&nbsp;</span> are required.</span> -->
			<input type="hidden" value="<?php echo $ActionName; ?>" id="mode" name="mode"/>
			<!-- <input type="hidden" value="<?php //echo $RecordID; ?>" id="recordid" name="recordid"/> -->
			<input type="hidden" value="<?php echo $ModuleName; ?>" id="module" name="module"/>
			<input type="hidden" value="<?php echo $sesionid; ?>" id="sesionid" name="sesionid"/>
			<input type="hidden" value="<?php echo $finaldate; ?>" id="finaldate" name="finaldate"/>
			<input type="hidden" value="<?php echo $finaldateshow; ?>" id="finaldateshow" name="finaldateshow"/>
			<!-- <input type="hidden" value="<?php //echo $MineName; ?>" id="mine_name" name="EditModel[mine_name]"/> -->
			<input type="hidden" name="csrfToken" id="csrfToken" value="<?= $csrfToken;?>">
			<input type="hidden" name="csrfTokenName" id="csrfTokenName" value="<?= $csrfTokenName;?>">
			
			<?php 
				
			foreach($ColumnList->blocks as $BlockKey=>$Block)
			{	
			//echo "<pre>";print_r($Block);die;		
				// <!-- UI types
				// 1 : textField
				// 2 :hidden
				// 8 :dropDownList
				// 12 :referencetype
					// 4 :TExtarea 
					// 13 :DateTimePicker -->
				
				if(!empty($Block->quickcreatefields)  && $Block->blocklabel !="SYSTEM GENERATED")
				{
					// print_r($Block->fields);die;
					
					echo '<div class="form-row">
							<div class="col-md-12"><h4>';					
					//echo $Block->blocklabel;die;
					echo '</h4></div></div>';
					if($Block->blocktype=="SimpleTwoCol"){  //echo "Two column";
								require 'SimpleTwoCol-quickcreate.php';
					}
				}
			}//endforeach;d
			?>
		</div>
		<div class="form-group"><br>
        <?= Html::Button('Save', ['class' => 'btn btn-success savebutton']) ?>
        <?= Html::Button('Cancel', ['class' => 'btn btn-danger','name'=>'btncancel','onclick'=>'closeModal();']) ?>
    	</div>
    </div>
   
    <script type="text/javascript">//alert('<?= $web?>');</script>
    <?php ActiveForm::end(); ?>
</div> <!-- fullpage ends -->

<?php //$this->registerJsFile('@web/theme/libs/pristinejs/pristinejs.min.js', ['depends' => [AdminAsset::class]]);
$this->registerJsFile('@web/theme/js/pages/form-validation.init.js', ['depends' => [AdminAsset::class]]);
// $this->registerJsFile('@web/theme/js/app.min.js', ['depends' => [AdminAsset::class]]);
die;?>