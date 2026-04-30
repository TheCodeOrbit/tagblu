<?php
/* @var $this Profile2tabController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Profile2tabs',
);

$this->menu=array(
	array('label'=>'Create Profile2tab', 'url'=>array('create')),
	array('label'=>'Manage Profile2tab', 'url'=>array('admin')),
);
?>

<h1>Profile2tabs</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
