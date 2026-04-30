<div class="listviewActionDiv row">
	<div class="col-sm-6">
		<!--<button class="btn dropdown-toggle" data-toggle="dropdown" style="margin-left:25px;">Actions <span class="caret"></span></button>
			<ul class="dropdown-menu">
				<li><a href="<?php echo $ActionUrl;?>Edit/Record/<?php echo $Record['RecordId']; ?>">Edit</a></li>
				<li><a href="<?php echo $ActionUrl;?>Delete/Record/<?php echo $Record['RecordId']; ?>" onclick="return checkDelete()">Delete</a></li>
			</ul>-->
		<?php $orderby=Yii::app()->request->getParam('OrderBy');
			$sortorder=Yii::app()->request->getParam('SortOrder');
			$val = explode(",",$operation['opt']); 
			$permod = $operation['name'];
			$module = $ModuleName;
		?>
		<?php if($ModuleName!="log_user_details"){?>
		<?php if (in_array('1',$val) and $module == $permod){ ?>
		<?php } else if($operation['opt'] =='1') { ?>
			<a class="btn create-btn" href="<?php echo $ActionUrl;?>Create"><span class="glyphicon glyphicon-plus"></span> Add <?php echo $ModuleLabel;?></a>
		<?php } else { ?>
			<a class="btn create-btn" href="<?php echo $ActionUrl;?>Create"><span class="glyphicon glyphicon-plus"></span> Add <?php echo $ModuleLabel;?></a>
		<?php } ?>
	<?php }?>
	</div>

	<div class="col-sm-2">
		<!--<span class="glyphicon glyphicon-filter ListViewHeader-filter-icon"></span>
		<button class="btn btn-block dropdown-toggle" data-toggle="dropdown" style="margin-top:-15px; height:36px; border:1px solid #ddd; background:#fff;"></button>
		<ul class="dropdown-menu" style="position:absolute; left:27px; width:317px;">
			<li>
				<div class="filterDropdown" style="height:45px;">
					<input type="text" class="form-control input-sm"/>
					<a href="#" class="input-group-addon glyphicon glyphicon-search" style="width:0px; position:relative; top:-29px; right:-256px;"></a>
				</div>
			</li>
			<li>
				<div class="filterDropdown">
					<a href="#">All Customers </a><span class="pull-right"><i class="glyphicon glyphicon-pencil"></i><i class="glyphicon glyphicon-trash"></i></span>
				</div>
			</li>
			<li>
				<div class="filterDropdown">
					<a href="#">New This Week </a><span class="pull-right"><i class="glyphicon glyphicon-pencil"></i><i class="glyphicon glyphicon-trash"></i></span>
				</div>
			</li>
			<li class="divider"></li>
			<li>
				<div class="filterDropdown">
					<span><i class="glyphicon glyphicon-plus-sign"></i></span>
					<a href="#">Create New Filter</a>
				</div>
			</li>
		</ul>-->
	</div>
	
	<div class="col-sm-4">
		<div class="filterDropdown">
			<?php if ($totalitemcount['totrecords'] > 0){
				echo $totalitemcount['pageStartRanges'];?>
				to
			<?php echo $totalitemcount['pageEndRanges']; ?>
				of
			<?php echo $totalitemcount['totrecords'];?>
			<?php } ?>
		</div>

		<div class="btn-group pull-right" role="group" aria-label="..." style="width:125px;">
			<input id="noofpages" name="noofpages" type="hidden" value="<?php echo $totalitemcount['noofpages']; ?>"/>
			<?php if(($_POST['pageNumber'] !='' || $_POST['pageNumberpre'] !='' || $_POST['pagejump'] !='') && $_POST['orderby'] !='') {
					$val1=$listserchvals['orderby'];
					$val2=$listserchvals['nextorder'];
					$urls = Yii::app()->createAbsoluteUrl($module)."/List/OrderBy/".$val1."/SortOrder/".$val2;
				}else if($_POST['pageNumber'] !=''){
					$url = Yii::app()->createAbsoluteUrl($module)."/List";
				}else{
					$url = Yii::app()->createAbsoluteUrl($module)."/List";
				}
				echo CHtml::beginForm($urls, 'POST',array("name"=>"previous","id"=>"previous")); ?>
				<tr>
					<input type="hidden" value="<?php if($orderby !='') echo $orderby; else echo $listserchvals['orderby']; ?>" name="orderby" id="orderby" /> 
					<input type="hidden" value="<?php if($sortorder !='') echo $sortorder;  else echo $listserchvals['nextorder']; ?>" name="nextorder" id="nextorder" />
					<?php echo $listserchvals['hidevals'];?>
				</tr>
			<input id="pageNumberpre" name="pageNumberpre" type="hidden" value="<?php echo $totalitemcount['nextPageNumber']; ?>">
			<button type="button" class="btn" <?php if($totalitemcount['previousPageExists'] == 'FALSE'){ ?> disabled <?php } ?> name="listViewpreviousPageButton" id="listViewpreviousPageButton" onclick="previous.submit();">
			<span class="glyphicon glyphicon-chevron-left"></span>
			</button>
			<?php echo CHtml::endForm();?>

			<button type="button" name="pgjump" id="pgjump" class="btn dropdown-toggle" <?php if($totalitemcount['totrecords'] == 0 ){?> disabled <?php } ?> data-toggle="dropdown">
			<span class=" glyphicon glyphicon-forward"></span>
			</button>
			<div class="dropdown-menu">
				<span style="width:100px; margin-left:14px;">
					<span style="float:left;margin-left:14px;">Page</span>
					<span>
						<?php echo CHtml::beginForm($urls, 'POST',array("name"=>"pagejumpfm","id"=>"pagejumpfm")); ?>
						<tr>
							<input type="hidden" value="<?php if($orderby !='') echo $orderby; else echo $listserchvals['orderby']; ?>" name="orderby" id="orderby" />
							<input type="hidden" value="<?php if($sortorder !='') echo $sortorder;  else echo $listserchvals['nextorder']; ?>" name="nextorder" id="nextorder" />
							<?php echo $listserchvals['hidevals'];?>
						</tr> 
						<input type="text" id="pagejump" name="pagejump" style="width:40px !important; float:left; color:#555;" value="<?php echo $totalitemcount['pagejumps']; ?>" />
						<?php echo CHtml::endForm();?> 
						<span>of</span>
						<?php echo $totalitemcount['noofpages']; ?>
					</span>
				</span>
			</div>

			<?php echo CHtml::beginForm($urls, 'POST',array("name"=>"next","id"=>"next")); ?>
				<tr>
					<input type="hidden" value="<?php if($orderby !='') echo $orderby; else echo $listserchvals['orderby']; ?>" name="orderby" id="orderby" />
					<input type="hidden" value="<?php if($sortorder !='') echo $sortorder;  else echo $listserchvals['nextorder']; ?>" name="nextorder" id="nextorder" />
					<?php echo $listserchvals['hidevals'];?>
				</tr>
			<input id="pageNumber" name="pageNumber" type="hidden" value="<?php echo $totalitemcount['nextPageNumber']; ?>">
			<button type="button" class="btn" <?php if($totalitemcount['nextPageExists'] == 'FALSE'){?> disabled <?php } ?> name="listViewNextPageButton" id="listViewNextPageButton" onclick="next.submit();"> <span class="glyphicon glyphicon-chevron-right"></span> </button>
			<?php echo CHtml::endForm();?>
		</div>
	</div>
</div>