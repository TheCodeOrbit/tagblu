<?php
/* @var $this ProfiletostanpermissionController */
/* @var $model Profiletostanpermission */

$this->breadcrumbs=array(
	'Profiletostanpermissions'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Profiletostanpermission', 'url'=>array('index')),
	array('label'=>'Create Profiletostanpermission', 'url'=>array('create')),
	array('label'=>'View Profiletostanpermission', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Profiletostanpermission', 'url'=>array('admin')),
);
?>

<h1>Update Profiletostanpermission <?php echo $model->id; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>