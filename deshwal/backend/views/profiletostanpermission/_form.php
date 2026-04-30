<section class="content inbox">
    <div class="block-header">
        <div class="form-group">
            <div class="col-lg-7 col-md-6 col-sm-12">
                <h2>Create Profile
                    <small class="text-muted"></small>
                </h2>
            </div>           
        </div>
		<div class="hidden-print col-md-12 text-right">
<a href="<?php echo Yii::app()->createUrl('Profile/index'); ?>" class="btn btn-primary btn-round"><img src="<?php echo Yii::app()->baseUrl; ?>/img/create.png" alt="create">Back To List View</a>
</div>
    </div>

<?php
/* @var $this ProfiletostanpermissionController */
/* @var $model Profiletostanpermission */
/* @var $form CActiveForm */
?>


<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'profiletostanpermission-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>

	<!--<p class="note">Fields with <span class="required">*</span> are required.</p>-->
	<?php //echo $form->errorSummary($model); ?>
<div class="container-fluid">
<div class="row clearfix">
<div class="col-lg-12">
<div class="card">
<div class="row">
<div class="col-md-12">
<div class="table-responsive">	
<table class="table table-hover">
<thead>
 <tr>
<th>All Modules</th>
<th>Modules Name</th>
<th width="60px">View</th>
<th width="60px">Create</th>
<th width="60px">Edit</th>
<th width="60px">Delete</th>
<th width="60px">Apporved</th>
</tr>
</thead>
<tbody>
<?php

foreach($model as $list){
	$permission=Profile2tab::model()->findByAttributes(array('profile_id'=>Yii::app()->user->user_id,'tabid'=>$list->id));
	if(empty($permission))
	{
		$permissionData=array(
				"view"=>0,
				"create"=>0,
				"update"=>0,
				"delete"=>0,
				"approve"=>0
				);
		
	}
	else
	{
		$permissionData= (json_decode($permission['permissions'],true));
		
	}
	?>
<tr>
<td><input type="checkbox" name="permission[]" <?php echo ($permission->tabid==$list->id)?'checked':'';?> value="<?php echo $list->id;?>"></td>
<td><?php echo $list->operation.$permission->tabid;?></td>
<td><input type="checkbox" name="view<?php echo $list->id;?>" value="<?php echo $list->id;?>" <?php echo ($permissionData['view']==$list->id)?'checked':'';?>></td>
<td><input type="checkbox" name="create<?php echo $list->id;?>" value="<?php echo $list->id;?>" <?php echo ($permissionData['create']==$list->id)?'checked':'';?>></td>
<td><input type="checkbox" name="edit<?php echo $list->id;?>" value="<?php echo $list->id;?>" <?php echo ($permissionData['edit']==$list->id)?'checked':'';?>></td>
<td><input type="checkbox" name="delete<?php echo $list->id;?>" value="<?php echo $list->id;?>" <?php echo ($permissionData['delete']==$list->id)?'checked':'';?>></td>
<td><input type="checkbox" name="approve<?php echo $list->id;?>" value="<?php echo $list->id;?>" <?php echo ($permissionData['approve']==$list->id)?'checked':'';?>></td>


</tr>
<?php } ?>
</tbody>
</table>
</div>
</div>
</div>
</div>
</div>
</div>
<div class="form-group">	
	    <?php echo CHtml::submitButton($model->isNewRecord ? 'Submit' : 'Save', array('class' => "btn btn-raised btn-primary btn-round waves-effect")); ?>
	</div>
</div>

	<!--<div class="form-group">
		<?php //echo $form->labelEx($model,'profile_id'); ?>
		<?php //echo $form->checkbox($model,'profile_id'); ?>
		<?php //echo $form->error($model,'profile_id'); ?>
	</div>

	<div class="form-group">
		<?php //echo $form->labelEx($model,'tabid'); ?>
		<?php //echo $form->textField($model,'tabid'); ?>
		<?php //echo $form->error($model,'tabid'); ?>
	</div>

	<div class="form-group">
		<?php //echo $form->labelEx($model,'operation'); ?>
		<?php //echo $form->textField($model,'operation'); ?>
		<?php //echo $form->error($model,'operation'); ?>
	</div>

	<div class="form-group">
		<?php //echo $form->labelEx($model,'permissions'); ?>
		<?php //echo $form->textField($model,'permissions'); ?>
		<?php //echo $form->error($model,'permissions'); ?>
	</div>-->

	

<?php $this->endWidget(); ?>


</section>