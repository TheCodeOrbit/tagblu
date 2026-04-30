<?php
/* @var $this Profile2tabController */
/* @var $model Profile2tab */

$this->breadcrumbs=array(
	'Profile2tabs'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Profile2tab', 'url'=>array('index')),
	array('label'=>'Create Profile2tab', 'url'=>array('create')),
	array('label'=>'View Profile2tab', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Profile2tab', 'url'=>array('admin')),
);
?>

<h1>Update Profile2tab <?php echo $model->id; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>