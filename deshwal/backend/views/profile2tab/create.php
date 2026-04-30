<?php
/* @var $this Profile2tabController */
/* @var $model Profile2tab */

$this->breadcrumbs=array(
	'Profile2tabs'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Profile2tab', 'url'=>array('index')),
	array('label'=>'Manage Profile2tab', 'url'=>array('admin')),
);
?>

<h1>Create Profile2tab</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>