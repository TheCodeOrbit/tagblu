<tbody>
<tr>
<td><b><?php //echo CHtml::encode($data->getAttributeLabel('mobile')); ?></b>
	<?php echo CHtml::encode($data->profilename); ?>
	<br /></td>
<td><b><?php //echo CHtml::encode($data->getAttributeLabel('fatherName')); ?></b>
	<?php echo CHtml::encode($data->description); ?>
	<br /></td>
<td>
<a href="<?php echo Yii::app()->createUrl('Profiletostanpermission/create/'.$data->profileid); ?>" class=""><img src="<?php echo Yii::app()->baseUrl; ?>/img/export-icon.png" alt="export"></a>
<a href="<?php echo Yii::app()->createUrl('profiletostanpermission/delete/'.$data->profileid); ?>" class=""><img src="<?php echo Yii::app()->baseUrl; ?>/img/delete-icon.png" alt="delete"></a></td>
</tr>
</tbody>

<?php
/* @var $this ProfileController */
/* @var $data Profile */
?>

<!--<div class="view">

	<b><?php //echo CHtml::encode($data->getAttributeLabel('profile_id')); ?>:</b>
	<?php //echo CHtml::link(CHtml::encode($data->profile_id), array('view', 'id'=>$data->profile_id)); ?>
	<br />

	<b><?php //echo CHtml::encode($data->getAttributeLabel('profilename')); ?>:</b>
	<?php //echo CHtml::encode($data->profilename); ?>
	<br />

	<b><?php //echo CHtml::encode($data->getAttributeLabel('description')); ?>:</b>
	<?php //echo CHtml::encode($data->description); ?>
	<br />

	<b><?php //echo CHtml::encode($data->getAttributeLabel('created_on')); ?>:</b>
	<?php //echo CHtml::encode($data->created_on); ?>
	<br />


</div>-->

