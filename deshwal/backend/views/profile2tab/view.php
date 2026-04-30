<section class="content inbox">
    <div class="block-header">
        <div class="form-group">
            <div class="col-lg-7 col-md-6 col-sm-12">
                <h2>Users
                    <small class="text-muted">Welcome to Users</small>
                </h2>
            </div>           
        </div>
    </div>
   <div>

<?php
/* @var $this Profile2tabController */
/* @var $model Profile2tab */

$this->breadcrumbs=array(
	'Profile2tabs'=>array('index'),
	$model->id,
);

//$this->menu=array(
	//array('label'=>'List Profile2tab', 'url'=>array('index')),
	//array('label'=>'Create Profile2tab', 'url'=>array('create')),
	//array('label'=>'Update Profile2tab', 'url'=>array('update', 'id'=>$model->id)),
	//array('label'=>'Delete Profile2tab', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	//array('label'=>'Manage Profile2tab', 'url'=>array('admin')),
//)//;
?>

<!--<h1>View Profile2tab #<?php //echo $model->id; ?></h1>-->

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		//'id',
		'profile_id',
		'tabid',
		'permissions',
	),
)); ?>
</section>