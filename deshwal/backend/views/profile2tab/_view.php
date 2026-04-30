<?php
/* @var $this Profile2tabController */
/* @var $data Profile2tab */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('profile_id')); ?>:</b>
	<?php echo CHtml::encode($data->profile_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('tabid')); ?>:</b>
	<?php echo CHtml::encode($data->tabid); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('permissions')); ?>:</b>
	<?php echo CHtml::encode($data->permissions); ?>
	<br />


</div>