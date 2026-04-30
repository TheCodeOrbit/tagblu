<?php

$ActionName=$ActionList['ActionName'];
$OrderBy=$ActionList['OrderBy'];
$SortOrder=$ActionList['SortOrder'];
$val = explode(",",$operation['opt']);
$permod = $operation['name'];
$module = $ModuleName;

$refield=$_REQUEST['field'];
$name=$_REQUEST['rdisfield'];
//$name=$_REQUEST['rdisfield'];
$mname=$_REQUEST['mname'];	

?>
<style>
	.anchor-table-header {
		color: var(--clr-white);
	}

	.anchor-table-header:hover {
		color: var(--clr-grey);
	}

	.table-bordered>:not(caption)>*>*,
	.table-bordered>:not(caption)>* {
		border-width: 0 0;
	}

	.modal-body {
		border: 1px solid var(--clr-prim);
		border-radius: 10px;
		padding: 0;
	}
</style>
<script>
$('html').bind('keypress', function(e)
{
	if(e.keyCode == 13)
	{
		return false;
	}
});
</script>
		<div class="modal-dialog modal-dialog-centered" role="document" id="PopupL-modal">
			<!-- Modal content-->
			<div class="modal-content">
				<!-- <div class="modal-header" id="close-button-container">
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div> -->
				<!-- <div class="model-form">
					<form id="search" method="get" class="form-inline" role="form">
						<div class="form-group">
							<input type="text" id="textsearch" name="textsearch" class="form-control" placeholder="Type to Search">
						</div>
						&nbsp; In &nbsp; -->
						<!-- <div class="form-group">
							<select id="selectsearch" class="styled-select">
								<option value="">--Please select--</option>
								<?php foreach ($ColumnList as $key=> $Column): ?>								
								<option value="<?php echo $key; ?>"><?php echo $Column; ?></option>								
								<?php endforeach;?>
							</select>
						</div> -->

						<!-- <div class="form-group">
							<?php //print_r($RecordList);
							$modname=$ModName; ?>
							<a href="#"><span class="glyphicon glyphicon-search" onclick="popsearch('<?php echo $modname;?>','<?php echo $_GET['textsearch']; ?>','<?php echo $refield;?>','<?php echo $name;?>')";></span></a>
							<a href="#"><span class="glyphicon glyphicon-search" onclick="<?php if($_REQUEST['promod1'] == 'promod'){ 
							?>
							productpopsearch('<?php echo $modname;?>','<?php echo $_GET['textsearch']; ?>','<?php echo $refield;?>','<?php echo $name;?>','<?php echo $_REQUEST['promod1'];?>','<?php echo $_REQUEST['pno'];?>','<?php echo $_REQUEST['key'];?>')
							<?php							
							}
							else
							{ ?>
							popsearch('<?php echo $modname;?>','<?php echo $_GET['textsearch']; ?>','<?php echo $refield;?>','<?php echo $name;?>')
							<?php } ?>";></span></a>
						</div> -->

						<!-- <span aria-label="..." role="group" class="btn-group pull-right">
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

							<div class="btn-group pull-right searchBtn" role="group" aria-label="...">
								<?php
								if($mname=='Customer' && $ModName == 'Transporter'){
								$url = Yii::app()->createAbsoluteUrl($module).'/'.$ModName.'/PopupList?field='.$refield.'&rdisfield='.$name.'&custid=customername&mname='.$mname.'&maintabid='.$maintabid.'&mode=Customer&depoid='.$_REQUEST['depoid'];
								}else if($mname=='Receipt' && $ModName == 'Transporter'){
								$url = Yii::app()->createAbsoluteUrl($module).'/'.$ModName.'/PopupList?field='.$refield.'&rdisfield='.$name.'&custid=customername&mname='.$mname.'&maintabid='.$maintabid.'&documenttype=2&depoid='.$_REQUEST['depoid'];
								}else if(($mname=='Receipt' or $mname='Issue' ) && $ModName == 'Depot'){
								$url = Yii::app()->createAbsoluteUrl($module).'/'.$ModName.'/PopupList?field='.$refield.'&rdisfield='.$name.'&custid=customername&mname='.$mname.'&maintabid='.$maintabid.'&documenttype=2';
								}else if($ModName == 'Product' && $mname=='Scheme'){
								$url = Yii::app()->createAbsoluteUrl($module).'/'.$ModName.'/PopupList?promod1=promod&pno='.$_REQUEST['pno'].'&key='.$_REQUEST['key'].'&divson='.$_REQUEST['divson'];
								}else if($ModName == 'Product' && $_REQUEST['key']=='productid'){
								$url = Yii::app()->createAbsoluteUrl($module).'/'.$ModName.'/PopupList?promod1=promod&pno='.$_REQUEST['pno'].'&key='.$_REQUEST['key'].'&divson='.$_REQUEST['divson'];
								}else if($ModName == 'Invoice'){
								$url = Yii::app()->createAbsoluteUrl($module).'/'.$ModName.'/PopupList?promod1=addinvoice&pno='.$_REQUEST['pno'].'&key='.$_REQUEST['key'].'&custname='.$_REQUEST['custname'];
								}else{
								$url = Yii::app()->createAbsoluteUrl($module).'/'.$ModName.'/PopupList?field='.$refield.'&rdisfield='.$name.'&custid=customername&mname='.$mname.'&maintabid='.$maintabid;
								}
								echo CHtml::beginForm($url, 'POST',array("name"=>"previous","id"=>"previous")); ?>
								<input id="pageNumberpre" name="pageNumberpre" type="hidden" value="<?php echo $totalitemcount['nextPageNumber']; ?>">
								<button type="button" class="btn" <?php if($totalitemcount['previousPageExists'] == 'FALSE'){ ?> disabled <?php } ?> name="listViewpreviousPageButton" id="listViewpreviousPageButton" onclick="seacrhresultpre('<?php echo $url ?>','<?php echo $totalitemcount['nextPageNumber'] ?>')">
									<span class="glyphicon glyphicon-chevron-left"></span>
								</button>
								<?php echo CHtml::endForm();?>

								<button type="button" name="pgjump" id="pgjump" class="btn dropdown-toggle" <?php if($totalitemcount['totrecords'] == 0 ){?> disabled <?php } ?> data-toggle="dropdown">
									<span class=" glyphicon glyphicon-forward"></span>
								</button>

								<div class="dropdown-menu">
									<span style="width:100px; margin-left:14px;">
										<span style="float:left; margin-left:14px;">Page</span>
										<span> 
											<?php echo CHtml::beginForm($url, 'POST',array("name"=>"pagejumpfm","id"=>"pagejumpfm")); ?>
											<input type="text" id="pagejump" name="pagejump" style="width:40px !important; float:left; color:#555;" value="<?php echo $totalitemcount['pagejumps']; ?>" onchange="seacrhresultjump('<?php echo $url ?>','<?php echo $totalitemcount['nextPageNumber'] ?>','<?php echo $totalitemcount['noofpages'] ?>')" />
											<?php echo CHtml::endForm();?> 
											<span>of</span> 
											<?php echo $totalitemcount['noofpages']; ?>
										</span>
									</span>
								</div>

								<?php echo CHtml::beginForm($url, 'POST',array("name"=>"next","id"=>"next")); ?>
								<input id="pageNumber" name="pageNumber" type="hidden" value="<?php echo $totalitemcount['nextPageNumber']; ?>">
								<button type="button" class="btn" <?php if($totalitemcount['nextPageExists'] == 'FALSE'){?> disabled <?php } ?> name="listViewNextPageButton" id="listViewNextPageButton" onclick="seacrhresultnext('<?php echo $url ?>','<?php echo $totalitemcount['nextPageNumber'] ?>')">
									<span class="glyphicon glyphicon-chevron-right"></span>
								</button>
								<?php echo CHtml::endForm();?>
							</div>
						</div>
					</form>
				</div> -->

				<div id="test1" class="modal-body" data-simplebar>
					<table id="cust_pop" class="table-view table table-bordered table-striped table-hover">
						<thead>
							<tr class="customerForm-header list-general table-primary">
								<!--<th width="5%"><input type="checkbox"></th>-->
								<?php //print_r($RecordList);
								//$modname=$ModName;
								$maintabid=$_REQUEST['maintabid'];
								$col_span=count($ColumnList)+1;
								foreach ($ColumnList as $key=> $Column): ?>
								<th id="<?php echo $key; ?>" class="shorter" order-data="asc" nowrap=""><a href="<?php echo $ActionUrl.'List'; ?>/OrderBy/<?php echo $key; ?>/SortOrder/<?php echo $NextOrder;?>" class="anchor-table-header"><?php echo $Column;?>
								<?php if($SortOrder!="" and $key==$OrderBy):?>
								<span class ="<?php echo $SortClass;?>"> </span></a>
								<?php endif;?>
								</th>
								<?php endforeach;?>
								<!--<th nowrap=""><a href="#">ActionPOPupl</a></th>-->
							</tr>
						</thead>

						<tbody id="pop_tbody">
							<tr id="pop_rows" >
								<?php //print_r($ModName);
								/*$refield=$_REQUEST['field'];
								$name=$_REQUEST['rdisfield'];
													$name=$_REQUEST['rdisfield'];
								$mname=$_REQUEST['mname'];		*/
								//print_r($_SESSION);
								if($_REQUEST['promod1'] == 'promod')
									{ $pno=$_REQUEST['pno']; }

								if(count($RecordList)>0):
								foreach ($RecordList as $Record): ?>
								<?php 
								if($_REQUEST['promod1'] == 'addinvoice')
								{$invdate1 =date("Y-m-d",strtotime($Record['invoicedate']));
									$invdate2 =date("d/m/Y",strtotime($Record['invoicedate'])); }
								?>
								<!--<tr id="<?php echo $customer['customerno'];?>">-->

								<tr>
									<?php foreach ($ColumnList as $key=> $Column):?>
									<td class="cursorPointer" onclick="<?php if($ModName == 'ManualNote' && ($refield == 'cc_note_amt' or $refield == 'db_note_amt' )) { ?> ccdbnote('<?php echo $Record['RecordId'];?>','<?php echo $refield;?>','<?php echo $Record['creditnoteno'];?>','<?php echo $Record['bal_adj'];?>','<?php echo $Record['modulename'];?>'); <?php } else if($ModName == 'ManualNote' && $refield == 'credit_note') { ?> cramount('<?php echo $Record['creditnoteno'];?>','<?php echo $Record['RecordId'];?>','<?php echo $refield;?>','<?php echo $Record['bal_adj'];?>','<?php echo $Record['modulename'];?>'); <?php } else { ?> showParentCust('<?php echo $Record[$name];?>','<?php echo $Record['RecordId'];?>','<?php echo $refield;?>','<?php echo $Record['mrvdate'];?>','<?php echo $Record['itemstotal'];?>','<?php echo $Record['invoicedate'];?>','<?php echo $Record['grdate'];?>','<?php echo $Record['orderdate'];?>');<?php if($ActionList['ModuleName'] =='Customer') { ?> enableCSTC(<?php echo $_SESSION[Yii::app()->params['dirName'].'_id']; ?>); <?php } ?><?php 
									//if($mname=='Invoice' && $ActionList['ModuleName'] =='Order' ) 
									if($ActionList['ModuleName'] =='Order' ) 
										{ ?> showOProduct('<?php echo $Record['RecordId'];?>','<?php echo $maintabid; ?>'); <?php } 

									if($_REQUEST['promod1'] == 'promod') { ?> showParentProduct('<?php echo $pno; ?>','<?php echo $Record['productname'];?>','<?php echo $Record['RecordId'];?>','<?php echo $_REQUEST['key']; ?>'); <?php } if($_REQUEST['promod1'] == 'addinvoice') { ?> showParentrelatedmod('<?php echo $_REQUEST['pno']; ?>','<?php echo $Record['invoiceno'];?>','<?php echo $Record['RecordId'];?>','<?php echo $_REQUEST['key']; ?>','<?php echo $invdate1; ?>','<?php echo $invdate2; ?>','<?php echo $Record['outstanding_amt']; ?>');<?php } if($_REQUEST['promod1'] == 'addsubacc') { ?> showParentaddsubacc('<?php echo $_REQUEST['pno']; ?>','<?php echo $Record['sac_nm'];?>','<?php echo $Record['RecordId'];?>','<?php echo $_REQUEST['key']; ?>');<?php } }?>" data-bs-dismiss="modal"><?php echo $Record[$key];?>
									<?php if($ModName=='ManualNote'){ ?>
									<input type="hidden" name="modulename" id="modulename" value="<?php echo $Record['modulename'];?> " />
									<?php } ?>
									</td>
									<?php endforeach;?>
								</tr>

								<?php endforeach; else :?>
								<tr>
									<td class="text-center" colspan="<?php echo $col_span;?>">No Record Found</td>
								</tr>
								<?php endif;?>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>