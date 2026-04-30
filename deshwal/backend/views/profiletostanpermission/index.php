<?php
/* @var $this ProfiletostanpermissionController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Profiletostanpermissions',
);

$this->menu=array(
	array('label'=>'Create Profiletostanpermission', 'url'=>array('create')),
	array('label'=>'Manage Profiletostanpermission', 'url'=>array('admin')),
);
?>

<h1>Profiletostanpermissions</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
