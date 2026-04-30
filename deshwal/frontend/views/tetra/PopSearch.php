<?php


$ActionName=$ActionList['ActionName'];
$OrderBy=$ActionList['OrderBy'];
$SortOrder=$ActionList['SortOrder'];
$val = explode(",",$operation['opt']);
$permod = $operation['name'];
$module = $ModuleName;
$refield=$_REQUEST['field'];
$name=$_REQUEST['rdisfield'];
echo $count['pageStartRanges'].'/##/'.$count['pageEndRanges'].'/##/'.$count['totrecords'].'/##/'; 
?>

<thead>
						<tr>
							<!--<th width="5%"><input type="checkbox"></th>-->
							<?php //print_r($RecordList);
							    print_r($ModName);
							    $maintabid=$_REQUEST['maintabid'];
								$col_span=count($ColumnList)+1;
								foreach ($ColumnList as $key=> $Column): ?>
								<th id="<?php echo $key; ?>" class="shorter" order-data="asc" nowrap=""><a href="<?php echo $ActionUrl.'List'; ?>/OrderBy/<?php echo $key; ?>/SortOrder/<?php echo $NextOrder;?>"><?php echo $Column;?>
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

					$refield=$_REQUEST['field'];
					$name=$_REQUEST['rdisfield'];
                    //$name=$_REQUEST['rdisfield'];
					$mname=$_REQUEST['mname'];	
					print_r($_REQUEST); 
					
					

				   if($_REQUEST['promod1'] == 'promod')
					 { $pno=$_REQUEST['pno']; }


					if(count($RecordList)>0):
					foreach ($RecordList as $Record): ?>

					<?php 
					//echo "**".$modulepermission['shareid'];

					//print_r($ColumnList);
					?>
					
					<!--<tr id="<?php echo $customer['customerno'];?>">-->

					<?php foreach ($ColumnList as $key=> $Column):?> 
					
					 <td class="cursorPointer" onclick="showParentCust('<?php echo $Record[$name];?>','<?php echo $Record['RecordId'];?>','<?php echo $refield;?>','<?php echo $Record['mrvdate'];?>','<?php echo $Record['itemstotal'];?>','<?php echo $Record['invoicedate'];?>','<?php echo $Record['grdate'];?>','<?php echo $Record['orderdate'];?>');<?php  if($mname=='Invoice' && $ActionList['ModuleName'] =='Order' )
						 { ?> showOProduct('<?php echo $Record['RecordId'];?>','<?php echo $maintabid; ?>'); <?php } if($_REQUEST['promod1'] == 'promod')
							 {?>
                              showParentProduct('<?php echo $pno; ?>','<?php echo $Record['productname'];?>','<?php echo $Record['RecordId'];?>','<?php echo $_REQUEST['key']; ?>');
							<?php } ?>"  data-dismiss="modal"><?php echo $Record[$key];?></td>
							
					<?php endforeach;?>
					
					</tr>
					

					<?php endforeach; else :?>
					<tr>
					<td class="text-center" colspan="<?php echo $col_span;?>">No Record Found</td>
					</tr>
					<?php endif;?>
					</tr>
					
					</tbody>
