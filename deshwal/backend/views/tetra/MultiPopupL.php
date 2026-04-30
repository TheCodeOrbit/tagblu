<?php

use backend\assets\AdminAsset;
use yii\helpers\Html;
$ActionName=$ActionList['ActionName'];
$OrderBy=$ActionList['OrderBy'];
$SortOrder=$ActionList['SortOrder'];
$val = explode(",",$operation['opt']);
$permod = $operation['name'];
$module = $ModName;

$refield=$_REQUEST['field'];
$rdisfield=$_REQUEST['rdisfield'];
$hiddenfield = $_REQUEST['hiddenfield'];
$maintabid=$_REQUEST['maintabid'];
$mname=$_REQUEST['mname'];
$sourcemodule	=isset($_REQUEST['sourcemodule'])?$_REQUEST['sourcemodule']:'';	
$sourceid	=isset($_REQUEST['sourceid'])?$_REQUEST['sourceid']:'';		
$baseUrl = Yii::$app->HomeUrl; 
	
// $this->registerJsFile('@web/thememain/js/tetra/setparentforpopup.js', ['depends' => [AdminAsset::class]]);

// print_r($ColumnList);die;

// print_r($totalitemcount);//die;
// Array ( [noofpages] => 1 [defaultrecord] => 10 [totrecords] => 1 [nextPageNumber] => 2 [pageEndRange] => 19 [pageStartRange] => 10 [previousPageExists] => FALSE [nextPageExists] => FALSE [pagejumps] => 2 [pageStartRangepagejump] => [pageStartRanges] => 11 [pageEndRanges] => 1 [orderby] => [nextorder] => ) 
?>
<style type="text/css">

</style>
<script nonce="<?= Yii::$app->params['cspNonce'] ?>">
$('html').bind('keypress', function(e)
{
	if(e.keyCode == 13)
	{
		return false;
	}
});
</script>
<link rel="stylesheet" href="<?= $baseUrl;?>/thememain/css/relatedlist.css">

<div class="pophead"><?= $TabLabel; ?><span class="p-close" >X</span></div>
		
			<div class="inn-tb">
		       <button class="btn btn-default addselected-multiple" >Add Selected</button>
		     <div class="container-add-doc">		
			<div class="button-group-page-1">
		        
				   <div class="pagination-container">
		            <span><?= $totalitemcount['pagejumps'];?> of <?= $totalitemcount['noofpages'];?> </span>
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
		            <select class="page-select-multi">
		            	<?php
		            	for($i=1;$i<=$totalitemcount['noofpages'];$i++)
		            	{
		            		if($i==$totalitemcount['pagejumps'])
		            			$sel="selected";
		            		else $sel='';
		            		?>
		                <option value="<?= $i;?>" <?= $sel;?> onclick="filterTableMulti()"><?= $i;?></option>
		                <?php
		            	}?>
		            </select>
		            
		            <button class="page-nav"></button>
		        </div>
		</div>
        <div class="table-wrapper">
		      
			 
		      <table id="data-tableMulti">
		        <thead class="showinParent_multi_thead"
				data-ref="<?= $refield; ?>"
									data-hidden="<?= $hiddenfield; ?>"
									data-mname="<?= $mname; ?>"
									data-maintabid="<?= $maintabid; ?>"
									data-rdisfield="<?= $rdisfield; ?>"
									data-sourcemodule="<?= $sourcemodule; ?>"
									data-sourceid="<?= $sourceid; ?>">
		        	
		          <tr>
		          	<th></th>
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
		          </tr>
		          <tr>
		        		<td><button class="btn select-btn-multi" onclick="filterTable()">Search</button></td>
                        
		        		 <?php //print_r($RecordList);
								//$modname=$ModName;
								$maintabid=$_REQUEST['maintabid'];
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
										 <!-- change class name v-icon-left to v-icon-left-multi,to resolve issue of X btn by ptpatel on date 19-11-2025 -->
										<svg class="v-icon-left-multi" fill="#2F80ED" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="15" height="15" role="button" tabindex="0" data-removefiltervalue="search-<?php echo $key;?>" aria-label="Remove <?php echo $Column;?>" title="Remove <?php echo $Column;?>">
											<path d="M4.7070312 3.2929688 L3.2929688 4.7070312 L10.585938 12 L3.2929688 19.292969 L4.7070312 20.707031 L12 13.414062 L19.292969 20.707031 L20.707031 19.292969 L13.414062 12 L20.707031 4.7070312 L19.292969 3.2929688 L12 10.585938 L4.7070312 3.2929688 Z"></path>
										</svg>

										<input type="text" class="v-input" placeholder="<?php echo $Column;?>" id="search-<?php echo $key;?>"   value="<?= $searchval;?>">
									</div>
								</td>
								<?php endforeach;?>
		        	</tr>

		        </thead>
		        <tbody>
		        	<tr>

		        		<?php
		        		if(isset($_REQUEST['promod1']) && $_REQUEST['promod1'] == 'promod')
									{ $pno=$_REQUEST['pno']; }

								if(count($RecordList)>0):
									// print_r($ColumnList);die;
								foreach ($RecordList as $Record): ?>
								<?php 
								// if($_REQUEST['promod1'] == 'addinvoice')
								// {$invdate1 =date("Y-m-d",strtotime($Record['invoicedate']));
								// 	$invdate2 =date("d/m/Y",strtotime($Record['invoicedate'])); }
								?>
								<!--<tr id="<?php //echo $customer['customerno'];?>">-->

								<tr tridmulti="<?= $Record['RecordId']?>">
									<td><?= Html::checkbox('record_select[]', false, ['value' => $Record['RecordId'],'class'=>'selectmultid',"data-display"=>htmlspecialchars(addslashes($Record[$rdisfield]), ENT_QUOTES)]) ?></td>
									<?php foreach ($ColumnList as $key=> $Column):?>
									<td class="cursorPointer data-cell showinParent_multi pl"
									data-recordid="<?= $Record['RecordId']; ?>"
									data-display="<?= htmlspecialchars(addslashes($Record[$rdisfield]), ENT_QUOTES); ?>"
									data-ref="<?= $refield; ?>"
									data-hidden="<?= $hiddenfield; ?>"
									data-mname="<?= $mname; ?>"
									data-maintabid="<?= $maintabid; ?>"
									data-rdisfield="<?= $rdisfield; ?>"
									data-sourcemodule="<?= $sourcemodule; ?>"
									data-sourceid="<?= $sourceid; ?>"
									><?php echo $Record[$key];?>
									
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
		

		<?php
		// $js = <<<'JS'
		// 	alert("outside");
		// 	$(document).on("click", ".showinParent_multi", function () {
		// 		alert("inside");

		// 		const $cell = $(this);
		// 		const recordId = $cell.data("recordid");
		// 		const display = $cell.data("display");
		// 		const ref = $cell.data("ref");
		// 		const hidden = $cell.data("hidden");

		// 		showinParent(recordId, display, ref, hidden);
		// 	});
		// JS;

// $this->registerJs($js, \yii\web\View::POS_END);
		die;
		?>