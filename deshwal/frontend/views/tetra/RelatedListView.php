<?php 
$ModuleName=$ActionList['ModuleName'];
$SourceModule=$ActionList['SourceModule'];
$RelatedSourceModule=$SourceModule;
$SourceRecordId=$ActionList['SourceRecordId'];
$ActionName=$ActionList['ActionName'];
$OrderBy=$ActionList['OrderBy'];
$SortOrder=$ActionList['SortOrder'];
$val = explode(",",$operation['opt']);
$permod = $operation['name'];
$module = $ModuleName;

if($SortOrder=="ASC")
{
$NextOrder="DESC";
$SortClass="glyphicon glyphicon-chevron-down";
}
else
{
$NextOrder="ASC";
$SortClass="glyphicon glyphicon-chevron-up";
}
//echo "<br>ModuleName=$ModuleName and ActionName=$ActionName";
//echo "<br>Source Module=$SourceModule and Source Record Id=$SourceRecordId";
$ActionUrl=Yii::app()->createAbsoluteUrl($ModuleName)."/";
$SourceActionUrl=Yii::app()->createAbsoluteUrl($SourceModule)."/";

//echo "<br>ActionUrl=$ActionUrl";
//die;
?>

<input type="hidden" name="SourceRecord" value="<?php echo $customer_id ; ?>"/>
<!--echo $form->hiddenField($model,'mode', array ('value'=>$customer_id)); -->
<div class="row" id="fullpage">
	<!-- Left side links -->
	<?php include_once 'LeftSide.php' ?>
	<!-- main details page -->
	<div class="col-sm-10 rightside-page maincontent-details" id="main-collapseing">
		<!-- header part -->
		<div class="topcontent-details">
			<span class="glyphicon glyphicon-chevron-left toggleButton" id="collapsebtn" data-toggle="collapse" data-target="#leftsiede-summary"></span>
			<div class="row">
				<div class="col-sm-4">
					<h4 class="h4 page-heading no-gutter"><?php echo $SourceModule;?></h4>
				</div>
				<div class="col-sm-6">
					<?php if (in_array('2',$val) and $module == $permod){ ?>
					<?php } else if($operation['opt'] =='1') { ?>	
						<button class="btn" onclick="window.location ='<?php echo $SourceActionUrl;?>Edit/Record/<?php echo $SourceRecordId; ?>'">Edit &nbsp;<span class=" glyphicon glyphicon-edit"></span></button> 
					<?php } else { ?>
						<button class="btn" onclick="window.location ='<?php echo $SourceActionUrl;?>Edit/Record/<?php echo $SourceRecordId; ?>'">Edit &nbsp;<span class=" glyphicon glyphicon-edit"></span></button> 
					<?php } ?>
					<!--<button class="btn">Send Email</button>
						<button class="btn">Converted Lead</button>
						<button class="btn">More..</button>
						<button class="btn"> <span class="glyphicon glyphicon-wrench"></span></button> -->
				</div>
				<div class="col-sm-2">
					<div class="btn-group pull-right" role="group" aria-label="...">
						<button type="button" class="btn glyphicon glyphicon-chevron-left"></button>
						<button type="button" class="btn glyphicon glyphicon-chevron-right"></button>
					</div>
				</div> 
			</div>
		</div>
		<!-- body parts of main dtails page --> 
		<div class="recordDetails-container row">
			<div class="col-sm-10">
				<div class="listviewActionDiv ActionDiv">
					<span>
						<?php if (in_array('1',$val) and $module == $permod){ ?>
						<?php } else if($operation['opt'] =='1') { ?>
						<a href="<?php echo $ActionUrl;?>Create/SourceModule/<?php echo $SourceModule; ?>/SourceRecord/<?php echo $SourceRecordId; ?>" class="btn"><span class="glyphicon glyphicon-plus"></span> Add <?php echo $ModuleName;?></a>
						<?php } else { ?>
						<a href="<?php echo $ActionUrl;?>Create/SourceModule/<?php echo $SourceModule; ?>/SourceRecord/<?php echo $SourceRecordId; ?>" class="btn"><span class="glyphicon glyphicon-plus"></span> Add <?php echo $ModuleName;?></a>
						<?php } ?>
					</span>
					<span class="btn-group pull-right" role="group" aria-label="...">
					<div class="col-sm-12">
						<div class="filterDropdown">
							<?php if ($totalitemcount['totrecords'] > 0){
								echo $totalitemcount['pageStartRanges'];?>
								to 
							<?php 	echo $totalitemcount['pageEndRanges']; ?>
								of 
							<?php 	echo $totalitemcount['totrecords'];?>
							<?php } ?>
						</div>

						<div class="btn-group pull-right" role="group" aria-label="..." style="width:125px;">
							<input id="noofpages" name="noofpages" type="hidden" value="<?php echo $totalitemcount['noofpages']; ?>"/>
							<?php if($_REQUEST['pageNumber'] !='' || $_REQUEST['pageNumberpre'] !='' || $_REQUEST['pagejump'] !=''){
								$val1=$totalitemcount['orderby'];
								$val2=$totalitemcount['nextorder'];
								$urls = $ActionUrl.'RelatedList/SourceModule/'.$SourceModule.'/SourceRecord/'.$SourceRecordId.'/OrderBy/'.$val1.'/SortOrder/'.$val2;
								}else{
									$url = $ActionUrl.'RelatedList/SourceModule/'.$SourceModule.'/SourceRecord/'.$SourceRecordId;
								}
							echo CHtml::beginForm($urls, 'POST',array("name"=>"previous","id"=>"previous")); ?>
							<input type="hidden" value="<?php if($OrderBy !='') echo $OrderBy; else echo $totalitemcount['orderby']; ?>" name="orderby" id="orderby" /> 
							<input type="hidden" value="<?php if($SortOrder !='') echo $SortOrder; else echo $totalitemcount['nextorder']; ?>" name="nextorder" id="nextorder" /> 	
							<input id="pageNumberpre" name="pageNumberpre" type="hidden" value="<?php echo $totalitemcount['nextPageNumber']; ?>">
							<button type="button" class="btn" <?php if($totalitemcount['previousPageExists'] == 'FALSE'){ ?> disabled <?php } ?> name="listViewpreviousPageButton" id="listViewpreviousPageButton" onclick="previous.submit();">
							<span class="glyphicon glyphicon-chevron-left"></span>
							</button>
							<?php echo CHtml::endForm();?>

							<button type="button" name="pgjump" id="pgjump" class="btn dropdown-toggle" <?php if($totalitemcount['totrecords'] == 0 ){?> disabled <?php } ?> data-toggle="dropdown">
							<span class=" glyphicon glyphicon-forward"></span>
							</button>
							<div class="dropdown-menu">
								<span style="width:100px; margin-left:14px;"><span style="float:left; margin-left:5px;">Page</span> <span> 
								<?php echo CHtml::beginForm($urls, 'POST',array("name"=>"pagejumpfm","id"=>"pagejumpfm")); ?>
								<input type="hidden" value="<?php if($OrderBy !='') echo $OrderBy; else echo $totalitemcount['orderby']; ?>" name="orderby" id="orderby" /> 
								<input type="hidden" value="<?php if($SortOrder !='') echo $SortOrder; else echo $totalitemcount['nextorder']; ?>" name="nextorder" id="nextorder" /> 
								<input type="text" id="pagejump" name="pagejump" style="width:50px !important; float:left;" value="<?php echo $totalitemcount['pagejumps']; ?>" />
								<?php echo CHtml::endForm();?> 
								<span>of</span> 
								<?php echo $totalitemcount['noofpages']; ?></span> </span>
							</div>

							<?php echo CHtml::beginForm($urls, 'POST',array("name"=>"next","id"=>"next")); ?>
							<input type="hidden" value="<?php if($OrderBy !='') echo $OrderBy; else echo $totalitemcount['orderby']; ?>" name="orderby" id="orderby" /> 
							<input type="hidden" value="<?php if($SortOrder !='') echo $SortOrder; else echo $totalitemcount['nextorder']; ?>" name="nextorder" id="nextorder" /> 
							<input id="pageNumber" name="pageNumber" type="hidden" value="<?php echo $totalitemcount['nextPageNumber']; ?>">
							<button type="button" class="btn" <?php if($totalitemcount['nextPageExists'] == 'FALSE'){?> disabled <?php } ?> name="listViewNextPageButton" id="listViewNextPageButton" onclick="next.submit();">
							<span class="glyphicon glyphicon-chevron-right"></span>
							</button>
							<?php echo CHtml::endForm();?>
						</div>
					</div>
				</div>

				<div class="wrapper1">
					<div class="div1"></div>
				</div>

				<div class="wrapper2">
					<div class="div2">
						<table class="table table-bordered">
							<thead>
								<tr>	<?php //print_r($RecordList);
									$col_span=count($ColumnList)+1;
									foreach ($ColumnList as $key=> $Column): ?>
									<th id="<?php echo $key; ?>" class="shorter" order-data="asc" nowrap=""><a href="<?php echo $ActionUrl.'RelatedList/SourceModule/'.$SourceModule.'/SourceRecord/'.$SourceRecordId;?>/OrderBy/<?php echo $key; ?>/SortOrder/<?php echo $NextOrder;?>"><?php echo $Column;?>
									<?php if($SortOrder!="" and $key==$OrderBy):?>
									<span class ="<?php echo $SortClass;?>"> </span></a>
									<?php endif;?>
									</th>
									<?php endforeach;?>
									<th nowrap=""><a href="#" class="dropdown-toggle">Action</a></th>
								</tr>
							</thead>

							<tbody>
								<?php //print_r($RecordList);
								if(count($RecordList)>0):
								foreach ($RecordList as $Record): ?>
								<tr>
									<?php foreach ($ColumnList as $key=> $Column):?> 
									<!--<td><input type="checkbox"></td>-->
									<td class="cursorPointer" onclick="window.location ='<?php echo $ActionUrl.'Summary/Record/'.$Record['RecordId'].'/SourceModule/'.$SourceModule.'/SourceRecord/'.$SourceRecordId; ?>'"><?php echo $Record[$key];?></td>
									<?php endforeach;?>

									<td nowrap="">
										<span><a href="<?php echo $ActionUrl;?>Detail/Record/<?php echo $Record['RecordId']; ?>/SourceModule/<?php echo $SourceModule; ?>/SourceRecord/<?php echo $SourceRecordId; ?>"><span class="glyphicon glyphicon-list"></span></a></span>

										<?php if(in_array('2',$val) and $module == $permod){ ?>
										<?php } else if($operation =='1') { ?>
										<span><a href="<?php echo $ActionUrl;?>Edit/Record/<?php echo $Record['RecordId']; ?>/SourceModule/<?php echo $SourceModule; ?>/SourceRecord/<?php echo $SourceRecordId; ?>"><span class="glyphicon glyphicon-pencil"></span></a></span>
										<?php } else { ?>
										<span><a href="<?php echo $ActionUrl;?>Edit/Record/<?php echo $Record['RecordId']; ?>/SourceModule/<?php echo $SourceModule; ?>/SourceRecord/<?php echo $SourceRecordId; ?>"><span class="glyphicon glyphicon-pencil"></span></a></span> 
										<?php } ?>

										<?php if(in_array('3',$val) and $module == $permod){ ?>
										<?php } else if($operation =='1') { ?>
										<span><a href="<?php echo $ActionUrl;?>Delete/Record/<?php echo $Record['RecordId']; ?>/SourceModule/<?php echo $SourceModule; ?>/SourceRecord/<?php echo $SourceRecordId; ?>" onclick="return checkDelete()"><span class="glyphicon glyphicon-trash"></span></a></span>
										<?php } else { ?> 
										<span><a href="<?php echo $ActionUrl;?>Delete/Record/<?php echo $Record['RecordId']; ?>/SourceModule/<?php echo $SourceModule; ?>/SourceRecord/<?php echo $SourceRecordId; ?>" onclick="return checkDelete()"><span class="glyphicon glyphicon-trash"></span></a></span>
										<?php } ?>
									</td>
								</tr>
								<?php endforeach; else :?>
								<tr>
								<td class="text-center" colspan="<?php echo $col_span;?>">No Record Found</td><!-- Show when records are empty -->
								</tr>
								<?php endif;?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<!-- right side links -->	
			<?php include_once 'RightSide.php';?>
		</div>
	</div>
</div>