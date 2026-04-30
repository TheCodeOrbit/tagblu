<?php

use yii\helpers\Url;
$ActionName=$ActionList['ActionName'];
$OrderBy=$ActionList['OrderBy'];
$SortOrder=$ActionList['SortOrder'];
$val = explode(",",$operation['opt']);
$permod = $operation['name'];
$module = $ModName;

// $sourcemodule=$_REQUEST['sourcemodule'];
// $sourceid=$_REQUEST['sourceid'];
$baseUrl = Yii::$app->HomeUrl;
	
// print_r($ColumnList);die;

// print_r($totalitemcount);//die;
// Array ( [noofpages] => 1 [defaultrecord] => 10 [totrecords] => 1 [nextPageNumber] => 2 [pageEndRange] => 19 [pageStartRange] => 10 [previousPageExists] => FALSE [nextPageExists] => FALSE [pagejumps] => 2 [pageStartRangepagejump] => [pageStartRanges] => 11 [pageEndRanges] => 1 [orderby] => [nextorder] => ) 
?>
<style type="text/css">

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
<link rel="stylesheet" href="<?= $baseUrl;?>/thememain/css/relatedlist.css">

<div class="comments-container">
<h3><?= ucfirst($ModName); ?></h3>
<div class="container-add-doc">	
		<div class="button-group-page-1">
        <div class="button-group">
        	<?php
        	if(!empty($relatedactions))
        	{
        		// print_r($relatedactions);die;  
        		// $relatedactions['moduleactions'];      		
        		$arrlist = explode(",",$relatedactions['moduleactions']);
        		// print_r($arrlist);die;
        		foreach ($arrlist as $value) 
        		{
        			if($ModName == "documents" && $value == "Add")
        			{
        				$id = "attach-doc-btn";
        				// $onclick = '';
        				$onclick = "location.href='".$baseUrl.$ModName."/list?sourcemodule=$sourcemodule&sourceid=$sourceid'";

        			}
        			else {
        				$id="";
        				$onclick = "showModuleselection('".$ModName."','".$sourcemodule."','".$sourceid."');";

        			}

        		
        	?>
            <button class="btn select-btn" id="<?= $id;?>" onclick="<?= $onclick;?>"><?= $value;?> <?= ucfirst($ModName); ?></button>
            <?php
        		}
        }?>
        </div>
		   

		        
				   <div class="pagination-container">
		            <span><?= $totalitemcount['pagejumps'];?> of <?= $totalitemcount['noofpages'];?> of</span>
					<button class="page-nav"></button>
					 <?php
		        if(isset($_REQUEST['pageNumberpre']))
		        {
		        	$pagepre = $_REQUEST['pageNumberpre'];
		        }
		        else $pagepre = 0;
		        		if($totalitemcount['nextPageNumber'] > 1)
		        		$prev = $totalitemcount['nextPageNumber']-2;
		        		else $prev = 0;
		        if($totalitemcount['pagejumps'] > 1)
		        {
		        	
		        	}	?>
		            <select class="page-select">
		            	<?php
		            	for($i=1;$i<=$totalitemcount['noofpages'];$i++)
		            	{
		            		if($i==$totalitemcount['pagejumps'])
		            			$sel="selected";
		            		else $sel='';
		            		?>
		                <option value="<?= $i;?>" <?= $sel;?> onclick="filtertablelist()"><?= $i;?></option>
		                <?php
		            	}?>
		            </select>
		            
		            <button class="page-nav"></button>
		        </div>
		</div>
        <div class="table-wrapper">
		     
		      <table id="data-table">
		        <thead>
		        	<tr>
		          	<th>&nbsp;</th>
		           <?php //print_r($RecordList);
								//$modname=$ModName;
								$maintabid=$sourcemodule;
								$col_span=count($ColumnList)+1;
								foreach ($ColumnList as $key=> $Column): 
									?>
								<th id="<?php echo $key; ?>" class="shorter" order-data="asc" nowrap=""><a href="<?php //echo $ActionUrl.'List'; ?>/OrderBy/<?php echo $key; ?>/SortOrder/<?php //echo //$NextOrder;?>" class="anchor-table-header"><?php echo $Column;?>
								<?php if($SortOrder!="" and $key==$OrderBy):?>
								<span class ="<?php echo $SortClass;?>"> </span></a>
								<?php endif;?>
								</th>
								<?php endforeach;?>
		          </tr>
		        	<tr>
		        		<td><button class="btn select-btn" onclick="filtertablelist()">Search</button></td>
		        		 <?php //print_r($RecordList);
								//$modname=$ModName;
								$maintabid=$sourcemodule;
								$col_span=count($ColumnList)+1;
								foreach ($ColumnList as $key=> $Column):
									$searchval= '';
									if(isset($searchparam) && $searchparam !='')
									{ 
										if(isset($searchparam[$key]))
										$searchval = $searchparam[$key];
										// print_r($searchparam);die;

									}
								 ?>
								<td>
									<div class="v-input-wrapper">
										<!-- Cross Icon on the Left -->
										<svg class="v-icon-left" fill="#2F80ED" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="15" height="15" role="button" tabindex="0" data-removefiltervalue="search-<?php echo $key;?>" aria-label="Remove <?php echo $Column;?>" title="Remove <?php echo $Column;?>">
											<path d="M4.7070312 3.2929688 L3.2929688 4.7070312 L10.585938 12 L3.2929688 19.292969 L4.7070312 20.707031 L12 13.414062 L19.292969 20.707031 L20.707031 19.292969 L13.414062 12 L20.707031 4.7070312 L19.292969 3.2929688 L12 10.585938 L4.7070312 3.2929688 Z"></path>
										</svg>

										<input type="text" class="v-input" placeholder="<?php echo $Column;?>" id="search-<?php echo $key;?>" value="<?= $searchval;?>">
									</div>
								</td>
								<?php endforeach;?>
		        	</tr>

		          
		        </thead>
		        <tbody>
		        	

		        		<?php
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
									 <td>
                            			<button class="icon-btn edit-btn">✏️</button>
                            			<button class="icon-btn delete-btn">🗑️</button>
                           
                        			</td>
									<?php foreach ($ColumnList as $key=> $Column):?>
									<td class="cursorPointer data-cell" onclick="
									<?php

									?>//showinParent('<?php //echo $Record['RecordId'];?>','<?php //echo $Record[$sourceid];?>','<?php //echo $sourcemodule;?>','<?php //echo $hiddenfield;?>')
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
		        	
		          <!-- Add more rows as needed -->
		        </tbody>
		      </table>

		     
		</div>
	
</div>
</div>
<script type="text/javascript">
	function removefilterValue(keyid)
	{
			$("#"+keyid).val('');
			filtertablelist();
	}
		     	// Function to filter the table
function filtertablelist() {
  // Collect search terms
  const searchTerms = [];
  
  <?php foreach ($ColumnList as $key => $Column): ?>
    const searchValue<?php echo $key; ?> = document.getElementById("search-<?php echo $key; ?>").value.toLowerCase();
    if (searchValue<?php echo $key; ?> !== '') {
      searchTerms.push(['<?php echo $key; ?>', searchValue<?php echo $key; ?>]);
    }
  <?php endforeach; ?>
  
  // console.log(searchTerms);
  pageselectval = $(".page-select").val();
  // console.log(pageselectval);
  
  if(pageselectval && pageselectval !=0)
  	pageselectval = pageselectval-1;

   if(searchTerms.length>0)
  	pageselectval = 0;
  
  // Here, you can proceed to send searchTerms to your server via AJAX for server-side filtering, as discussed previously.
  showRelatedlst('<?= $module ?>','<?= $sourcemodule ?>','<?= $sourceid; ?>','<?= $divcontainer ?>','',pageselectval,searchTerms)
}
		     </script>

<?php
die;
?>