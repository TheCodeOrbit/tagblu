<?php
/* @var $this ProfiletostanpermissionController */
/* @var $model Profiletostanpermission */

$this->breadcrumbs=array(
	'Profiletostanpermissions'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List Profiletostanpermission', 'url'=>array('index')),
	array('label'=>'Create Profiletostanpermission', 'url'=>array('create')),
	array('label'=>'Update Profiletostanpermission', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Profiletostanpermission', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Profiletostanpermission', 'url'=>array('admin')),
);
?>

<h1>View Profiletostanpermission #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'profile_id',
		'tabid',
		'operation',
		'permissions',
	),
)); ?>
