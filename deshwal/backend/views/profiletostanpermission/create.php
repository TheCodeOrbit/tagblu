<?php
/* @var $this ProfiletostanpermissionController */
/* @var $model Profiletostanpermission */

$this->breadcrumbs=array(
	'Profiletostanpermissions'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Profiletostanpermission', 'url'=>array('index')),
	array('label'=>'Manage Profiletostanpermission', 'url'=>array('admin')),
);
?>

<h1>Create Profiletostanpermission</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>