<?php if($_REQUEST['SourceModule']!="")
	{
		$RelatedSourceModule=$_REQUEST['SourceModule'];
		$RelatedSourceRecord=$_REQUEST['SourceRecord'];
	}
	else
	{
		$RelatedSourceModule=$ActionList['ModuleName'];
		//$RelatedSourceRecord=$Record['RecordId'];
		//$RelatedSourceRecord=$Record->{$Record->fieldId};
		$RelatedSourceRecord=$Record->recordId;
	}
?>
<?php 
 $loginrecordid=$_SESSION[Yii::app()->params['dirName'].'_id'];
?>
<div class="rightside-summary col-sm-2"><!-- rightside summary starts -->
	<ul class="nav nav-pills nav-stacked">
		<!-- <li><a href="#" class="list-group-item">Customer Summery</a></li>
		<li><a href="#" class="list-group-item">Enquiry Summary</a></li>
		<li><a href="#" class="list-group-item">Followup</a></li>
		<li><a href="#" class="list-group-item">Documents</a></li> -->
		<?php if($RelatedSourceModule=='Issue') {?>
		<li><a href="<?php echo Yii::app()->baseUrl.'/index.php/'.$RelatedSourceModule.'/Print/Record/'.$RelatedSourceRecord ; ?> "class="list-group-item" target="_blank" >Print</a></li>
		<!--<li><a href="<?php echo Yii::app()->baseUrl.'/index.php/'.$RelatedSourceModule.'/Generateexcel/Record/'.$RelatedSourceRecord ; ?>" class="list-group-item" id="excel" style="cursor:pointer;">Generate Excel</a><br></li>-->
		<li><a href="<?php echo Yii::app()->baseUrl.'/index.php/'.$RelatedSourceModule.'/GeneratePDF/Record/'.$RelatedSourceRecord ; ?>"class="list-group-item" target="_blank" >Generate PDF</a></li>
		<?php }?>
		<?php if($RelatedSourceModule=='Invoice') {?>
		<li><a href="<?php echo Yii::app()->baseUrl.'/index.php/'.$RelatedSourceModule.'/Print/Record/'.$RelatedSourceRecord ; ?> "class="list-group-item" target="_blank" >Print</a></li>
		<!--<li><a href="<?php echo Yii::app()->baseUrl.'/index.php/'.$RelatedSourceModule.'/Generateexcel/Record/'.$RelatedSourceRecord ; ?>" class="list-group-item" id="excel" style="cursor:pointer;">Generate Excel</a></li>-->
		<li><a href="<?php echo Yii::app()->baseUrl.'/index.php/'.$RelatedSourceModule.'/GeneratePDF/Record/'.$RelatedSourceRecord ; ?>"class="list-group-item" target="_blank" >Generate PDF</a></li>
		<?php }?>
		<?php if($RelatedSourceModule=='MRV') {?>
		<li><a href="<?php echo Yii::app()->baseUrl.'/index.php/'.$RelatedSourceModule.'/Print/Record/'.$RelatedSourceRecord ; ?> "class="list-group-item" target="_blank" >Print</a></li>
		<!--<li><a href="<?php echo Yii::app()->baseUrl.'/index.php/'.$RelatedSourceModule.'/Generateexcel/Record/'.$RelatedSourceRecord ; ?>" class="list-group-item" id="excel" style="cursor:pointer;">Generate Excel</a><br></li>-->
		<li><a href="<?php echo Yii::app()->baseUrl.'/index.php/'.$RelatedSourceModule.'/GeneratePDF/Record/'.$RelatedSourceRecord ; ?>"class="list-group-item" target="_blank" >Generate PDF</a></li>
		<?php }?>
		<?php if($RelatedSourceModule=='MCN') {?>
		<li><a href="<?php echo Yii::app()->baseUrl.'/index.php/'.$RelatedSourceModule.'/Print/Record/'.$RelatedSourceRecord ; ?> "class="list-group-item" target="_blank" >Print</a></li>
		<!--<li><a href="<?php echo Yii::app()->baseUrl.'/index.php/'.$RelatedSourceModule.'/Generateexcel/Record/'.$RelatedSourceRecord ; ?>" class="list-group-item" id="excel" style="cursor:pointer;">Generate Excel</a><br></li>-->
		<li><a href="<?php echo Yii::app()->baseUrl.'/index.php/'.$RelatedSourceModule.'/GeneratePDF/Record/'.$RelatedSourceRecord ; ?>"class="list-group-item" target="_blank">Generate PDF</a></li>
		<?php }?>
		<?php if($RelatedSourceModule=='PR' && $prstatus=='1') {?>
		<li><div class="chkdishonour" id="<?php echo $RelatedSourceRecord; ?>"><button id="disho" class="btn">Dishonour</button></div></li>
		<?php }?>	

		<?php if($RelatedSourceModule=='users') {?>
		<li><div class="changepass" id="<?php echo $RelatedSourceRecord; ?>"><button id="changepass" data-toggle="modal" data-target="#changetest" class="btn">Change Password</button></div></li>
		<?php }?>

		<div class="modal fade in" id="changetest" style="padding-right:17px; display:none;"><!-- modal starts -->
			<div class="modal-dialog" id="dpDetails">
				<div class="modal-dialog" id="dpDd">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close pull-right" data-dismiss="modal">&times;</button>
							<h4 class="modal-title">Change Password Detail</h4>
						</div>
						<div class="modal-body depot-date-modal">
					<form class="form form-horizontal" id="depotDateModal" role="form" name="" method="" action="">
			 		<?php if ($loginrecordid==$RelatedSourceRecord){ ?>
						<div class="form-group">
							<label for="oldpassword" class="col-sm-5 text-right">Old Password :</label>
							<div class="col-sm-5">
								<input class="form-control oldpassword" id="oldpassword" value="" type="password">
							</div>
						</div>

<?php } ?>
						<div class="form-group">
							<label for="newpassword" class="col-sm-5 text-right">New Password :</label>
							<div class="col-sm-5">
								<input class="form-control newpassword" id="newpassword" value="" type="password">
								<input class="form-control recordid" id="recordid" value="<?php echo $RelatedSourceRecord; ?>" readonly="" type="hidden">
             					<input class="form-control loginrcordid" id="loginrcordid" value="<?php echo $loginrecordid; ?>" readonly="" type="hidden">							
								<!-- hidden field for public key -->
								<input class="form-control" id="DoLogin_publicKey" type="hidden" value="<?php echo $publickey ?>">
							</div>
						</div>
						<div class="form-group">
							<label for="confirmpassword" class="col-sm-5 text-right">Confirm Password :</label>
							<div class="col-sm-5">
								<input class="form-control confirmpassword" id="confirmpassword" value="" type="password">
							</div>
						</div>
					</form>
				</div>
						<div class="modal-footer">
							<div class="pull-right">
								<button type="button" class="btn changeSave" id="changeSave" data-dismiss="modal">Change Password</button>
								<a href="#" data-dismiss="modal" class="btn addgrpcancel">Cancel</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<?php
			//print_r($ActionList['RelatedModules']);
			if(count($ActionList['RelatedModules'])>0):
			foreach ($ActionList['RelatedModules'] as $key=> $RelatedModule): ?>
			<li><a href="<?php echo Yii::app()->createAbsoluteUrl($RelatedModule->label);?>/RelatedList/SourceModule/<?php echo $RelatedSourceModule;?>/SourceRecord/<?php echo $RelatedSourceRecord; ?>" class="list-group-item"><?php echo $RelatedModule->label; ?></a></li>
		<?php endforeach;endif;?>
	</ul>
</div><!-- rightside summary ends -->