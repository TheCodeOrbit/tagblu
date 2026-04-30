<?php
error_reporting(-1);
ini_set('display_errors', true);
use yii\helpers\Html;
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
//echo $fullurl ; exit ;
?>


<div class="create-form1" style="position: absolute;
  /*width: 1343px;*/
  /*width: 100%;*/
  padding: 30px;
  /*height: 100vh;*/
  top: 84px;
  left: 98px;
  background-color: #ffffff;">
	<h3>Add <?= $ModuleName ?></h3>
		<?php $form = ActiveForm::begin(['id' => 'pristine-valid-example']); ?>
		
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
				// <!-- UI types
				// 1 : textField
				// 2 :hidden
				// 8 :dropDownList
				// 12 :referencetype
					// 4 :TExtarea 
					// 13 :DateTimePicker -->
				
				if(!empty($Block->fields))
				{
					// print_r($Block->fields);die;
					
					echo '<div class="form-row">
							<div class="col-md-12"><h4>';					
					echo $Block->blocklabel;
					echo '</h4></div></div>';
					if($Block->blocktype=="SimpleTwoCol"){  //echo "Two column";
								require 'SimpleTwoCol-old.php';
					}
				}
			}//endforeach;d
			?>
		
		<div class="form-group"><br>
        <?= Html::Button('Save', ['class' => 'btn btn-success savebutton']) ?>
        <?= Html::SubmitButton('Cancel', ['class' => 'btn btn-danger','name'=>'btncancel']) ?>
    	</div>
    <?php ActiveForm::end(); ?>
</div> <!-- fullpage ends -->


<div class="modal fade" id="modalreference" tabindex="-1" role="dialog" aria-labelledby="modalreferencelabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
     

     
    </div>
  </div>
</div>

<?php $this->registerJsFile('@web/theme/libs/pristinejs/pristinejs.min.js', ['depends' => [AdminAsset::class]]);
$this->registerJsFile('@web/theme/js/pages/form-validation.init.js', ['depends' => [AdminAsset::class]]);
// $this->registerJsFile('@web/theme/js/app.min.js', ['depends' => [AdminAsset::class]]);
//die;?>
