<?php

$ActionName=$ActionList['ActionName'];
$OrderBy=$ActionList['OrderBy'];
$SortOrder=$ActionList['SortOrder'];
$val = explode(",",$operation['opt']);
$permod = $operation['name'];
$module = $ModName;

$refield=$_REQUEST['field'];
$rdisfield=$_REQUEST['rdisfield'];
$hiddenfield = $_REQUEST['hiddenfield'];
//$name=$_REQUEST['rdisfield'];
$mname=$_REQUEST['mname'];	
// print_r($ColumnList);die;
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
								<th id="<?php echo $key; ?>" class="shorter" order-data="asc" nowrap=""><a href="<?php //echo $ActionUrl.'List'; ?>/OrderBy/<?php echo $key; ?>/SortOrder/<?php //echo //$NextOrder;?>" class="anchor-table-header"><?php echo $Column;?>
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
								if(isset($_REQUEST['promod1']) && $_REQUEST['promod1'] == 'promod')
									{ $pno=$_REQUEST['pno']; }

								if(count($RecordList)>0):
									// print_r($RecordList);die;
								foreach ($RecordList as $Record): ?>
								<?php 
								// if($_REQUEST['promod1'] == 'addinvoice')
								// {$invdate1 =date("Y-m-d",strtotime($Record['invoicedate']));
								// 	$invdate2 =date("d/m/Y",strtotime($Record['invoicedate'])); }
								?>
								<!--<tr id="<?php //echo $customer['customerno'];?>">-->

								<tr>
									<?php foreach ($ColumnList as $key=> $Column):?>
									<td class="cursorPointer" onclick="
									<?php

									?>showinParent('<?php echo $Record['RecordId'];?>','<?php echo $Record[$rdisfield];?>','<?php echo $refield;?>','<?php echo $hiddenfield;?>')
									<?php
								//}?>" data-bs-dismiss="modal"><?php echo $Record[$key];?>
									
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
		<?php
		die;
		?>