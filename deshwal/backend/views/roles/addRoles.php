<?php

use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'ADD ROLE';

use backend\assets\AdminAsset;

AdminAsset::register($this);
// print_r($profilelists);die;
?>
<style>
	:root {
		--space-navigation-image: 0px;
	}

	.header-image {
		position: absolute;
		left: -9999px;
	}

	.action-icon--add {
		width: 1.2rem;
		height: 1.2rem;
	}

	.actions-container:hover .actions,
	.roles-hover:hover+.actions {
		display: block;
	}

	.body-container {
		margin: 0 2rem;
	}

	.simple-table {
		margin: 0;
	}

	.input-heading,
	th {
		text-align: left;
	}

	.gap-1rem {
		gap: 1rem;
	}
</style>
<?php // $url = Yii::app()->getBaseUrl(true);
//$baseurl = Yii::app()->request->hostInfo.Yii::app()->homeUrl;
$url = '';
$baseurl = Url::base();

// print_r($profilelists);die; 
?>
<div class="page-content">
</div>
<div class="select-1">
	<div class="container-d">
		<div class="row">



			<div class="col-md-12 clearfix">
				<div class="col-md-6 text-left">
					<h3><?= $this->title ?></h3>
				</div>

			</div>


			<?php $form = ActiveForm::begin(['action' => 'submitrole']); ?>
			<!-- <form action="<?php echo $baseurl ?>/roles/submitrole" method="post" class="form form-horizontal" id="roleform" > -->



			<div class="col-md-12">
				<label class="" for="">Name</label>
				<div class="form-group field-roles-name required">
					<input type="text" id="user_rolename" class="form-control V~M <?php if (isset($userrolelistS['rolename']))
																						echo "hasvalue"; ?>" name="user_rolename" maxlength="50" aria-required="true" value="<?php if (isset($userrolelistS['rolename'])) {
																													echo $userrolelistS['rolename'];
																												} ?>"><br>

					<div class="help-block"></div>
				</div>



			</div>
			<div class="col-md-12">
				<label class="" for="">Reports To</label>
				<div class="form-group field-roles-rolename required">
					<input type="text" class="hasvalue form-control" name="rolename" maxlength="50" aria-required="true"
						value="<?php echo $orgheaddetails['rolename']; ?>" readonly="">
					<input type="hidden" value="<?php echo $orgheaddetails['roleid']; ?>" name="roleid" id="roleid">
					<?php if ($action == "Edit") { ?>
						<input type="hidden" value="<?php echo $userrolelistS['roleid']; ?>" name="roleiduser"
							id="roleiduser">
					<?php } ?>
					<input type="hidden" value="<?php echo $orgheaddetails['parentrole']; ?>" name="parentrole"
						id="parentrole">
					<input type="hidden" value="<?php echo $orgheaddetails['depth']; ?>" name="depth" id="depth">
					<input type="hidden" value="<?php echo $action; ?>" name="action" id="action">
					<div class="help-block"></div>
				</div>

			</div>
			<div class="col-md-12">

				<label class="" for="">Profile</label>
				<div class="form-group field-roles-profile required">
					<select class="hasvalue form-control singleselect DD~M" id="profile" name="profile" aria-required="true">
						<?php

						foreach ($profilelists as $profilelist) {

						?>
							<option <?php if (!empty($userprofilearray)) {
										if ($profilelist['profileid'] == $userprofilearray['profileid']) {
											echo "selected";
										}
									} ?> value="<?php echo $profilelist['profileid']; ?>">
								<?php echo $profilelist['profilename']; ?>
							</option>
						<?php } ?>
					</select>


					<div class="help-block"></div>
				</div>



			</div>


			<div class="col-md-12">
				<label for="showinaccounts">Show In Account</label>
				<div class="form-group field-roles-showinaccount required">
					<input type="checkbox" id="showinaccounts" name="showinaccounts" value="1"
						<?php echo isset($userrolelistS['showinaccounts']) && $userrolelistS['showinaccounts'] == 1 ? 'checked' : ''; ?>>
					<div class="help-block"></div>
				</div>
			</div>

			<div class="col-md-12">
				<label for="admin_edit_allow">Allow Edit Admin Only</label>
				<div class="form-group field-roles-admin_edit_allow required">
					<input type="checkbox" id="admin_edit_allow" name="admin_edit_allow" value="1"
						<?php echo isset($userrolelistS['admin_edit_allow']) && $userrolelistS['admin_edit_allow'] == 1 ? 'checked' : ''; ?>>
					<div class="help-block"></div>
				</div>
			</div>

			<div class="col-md-12">
				<br>
				<button type="submit" class="btn  btn-primary savebutton">SUBMIT</button>
				<a class="btn btn-danger" href="<?php echo $baseurl; ?>/roles/role">CANCEL</a>
				<br>
			</div>
			<?php ActiveForm::end(); ?>

		</div>
	</div>
</div>


<?php
$this->registerCssFile('@web/thememain/css/select2.min.css', ['depends' => [AdminAsset::class]]);
$this->registerJsFile('@web/thememain/js/select2.min.js', ['depends' => [yii\web\JqueryAsset::class]]);

$this->registerJsFile('@web/thememain/js/tetra/validator.js', ['depends' => [AdminAsset::class]]);
$this->registerJsFile('@web/thememain/js/tetra/validatorcalling.js', ['depends' => [AdminAsset::class]]);
?>
<?php
// Register your jQuery code using registerJs()
$this->registerJs('
    // alert("This is a jQuery alert!");
    $(document).ready(function() {
    
       
    });
', \yii\web\View::POS_READY);
?>