<?php
/* @var $this Profile2tabController */
/* @var $model Profile2tab */
/* @var $form CActiveForm */
?>

<section class="content inbox">
    <div class="block-header">
        <div class="form-group">
            <div class="col-lg-7 col-md-6 col-sm-12">
                <h2>Profile Manage
                    <small class="text-muted"></small>
                </h2>
            </div>           
        </div>
    </div>

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'profile2tab-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php //echo $form->errorSummary($model); ?>

	<div class="form-group">
		<?php echo $form->labelEx($model,'profile_id'); ?>
		<?php echo $form->dropDownList($model, 'profile_id', CHtml::listData(Profile::model()->findAll(), 'profile_id','profilename'), array('empty'=>'---Select---','class' => "form-control")); ?>
		<?php //echo $form->textField($model,'profile_id',array('class' => "form-control")); ?>
		<?php echo $form->error($model,'profile_id'); ?>
	</div>
	<div class="form-group">
		<?php echo $form->labelEx($model,'tabid'); ?></br>
		<label>Mines</label>
		<?php echo $form->checkbox($model, 'tabid',$listData, array('Value'=>'Mines','class' => "form-control")); ?>
		</br>
        <?php //echo $form->checkbox($model,'tabid',array('class' => "form-control")); ?>
		<label>Equipment</label>
		<?php echo $form->checkbox($model, 'tabid',$listData, array('Value'=>'Equipment','class' => "form-control")); ?></br>
		<label>Contactor</label>
		<?php echo $form->checkbox($model, 'tabid',$listData, array('Value'=>'Contactor','class' => "form-control")); ?></br>
	    <label>Enty Point</label>
		<?php echo $form->checkbox($model, 'tabid',$listData, array('Value'=>'Enty Point','class' => "form-control")); ?></br>
		<?php //echo $form->error($model,'tabid'); ?>
	</div>
	<div class="form-group">
		<?php echo $form->labelEx($model,'permissions'); ?>
		<?php //echo $form->dropDownList($model,'permissions',$listData, array('empty'=>'select','0'=>'Yes','1'=>'No','class' => "form-control")); ?></br>
		<?php echo $form->textField($model,'permissions',array('class' => "form-control")); ?>
		<?php echo $form->error($model,'permissions'); ?>
	</div>

	<div class="form-group">	
	    <?php echo CHtml::submitButton($model->isNewRecord ? 'Submit' : 'Save', array('class' => "btn btn-raised btn-primary btn-round waves-effect")); ?>
	</div>

<?php $this->endWidget(); ?>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'profile2tab-grid',
	'dataProvider'=>$model->search(),
	//'filter'=>$model,
	'columns'=>array(
		//'id',
		'profile_id',
		'tabid',
		'permissions',
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>

</section>