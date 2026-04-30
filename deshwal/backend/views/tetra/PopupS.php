<?php

$ActionName=$ActionList['ActionName'];
$OrderBy=$ActionList['OrderBy'];
$SortOrder=$ActionList['SortOrder'];
$val = explode(",",$operation['opt']);
$permod = $operation['name'];
$module = $ModName;

$baseUrl = Yii::$app->HomeUrl; 

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
		            <select class="page-select2">
		            	<?php
		            	for($i=1;$i<=$totalitemcount['noofpages'];$i++)
		            	{
		            		if($i==$totalitemcount['pagejumps'])
		            			$sel="selected";
		            		else $sel='';
		            		?>
		                <option value="<?= $i;?>" <?= $sel;?> onclick="filterTable()"><?= $i;?></option>
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
								// $maintabid=$_REQUEST['maintabid'];
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
		        		<td><button class="btn select-btn" onclick="filterTable()">Search</button></td>
		        		 <?php //print_r($RecordList);
								//$modname=$ModName;
								// $maintabid=$_REQUEST['maintabid'];
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

										<input type="text" class="v-input" placeholder="<?php echo $Column;?>" id="searchi-<?php echo $key;?>" value="<?= $searchval;?>">
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
									// print_r($RecordList);die;
								foreach ($RecordList as $Record): ?>
								<?php 
								// if($_REQUEST['promod1'] == 'addinvoice')
								// {$invdate1 =date("Y-m-d",strtotime($Record['invoicedate']));
								// 	$invdate2 =date("d/m/Y",strtotime($Record['invoicedate'])); }
								?>
								<!--<tr id="<?php //echo $customer['customerno'];?>">-->

								<tr>
									<td><input type="checkbox" name="checklist[]" value="<?php echo $Record[$key];?>"></td>
									<?php foreach ($ColumnList as $key=> $Column):?>
									<td class="cursorPointer data-cell" onclick="" data-bs-dismiss="modal"><?php echo $Record[$key];?>
									
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
		
<script type="text/javascript" nonce="<?= Yii::$app->params['cspNonce'] ?>">
curpage = 1;
rowsPerPage = 5;

// Function to open the modal
function openModal() {
  document.getElementById("modal").style.display = "block";
  displayTable();
}

function removefilterValue(keyid)
	{
			$("#"+keyid).val('');
			filterTable();
	}

// Function to filter the table
function filterTable() {
  // Collect search terms
  const searchTerms = [];
  
  <?php foreach ($ColumnList as $key => $Column): ?>
    const searchValue<?php echo $key; ?> = document.getElementById("searchi-<?php echo $key; ?>").value.toLowerCase();
    if (searchValue<?php echo $key; ?> !== '') {
      searchTerms.push(['<?php echo $key; ?>', searchValue<?php echo $key; ?>]);
    }
  <?php endforeach; ?>
  
  // console.log(searchTerms);
   pageselectval = $(".page-select2").val();
  // alert(pageselectval);

   // alert($(".page-select2").val());
   // console.log('pageselectval'+pageselectval);
  if(pageselectval && pageselectval !=0)
  	pageselectval = pageselectval-1;
  if(searchTerms.length>0)
  	pageselectval = 0;

  // alert(searchTerms.length);
  // alert(pageselectval);
  
  // Here, you can proceed to send searchTerms to your server via AJAX for server-side filtering, as discussed previously.
  showModuleselection('<?= $ModName ?>','<?= $sourcemodule ?>','<?= $sourceid; ?>','',pageselectval,searchTerms);
}



// Function to display the current page
function displayTable() {
  const table = document.getElementById("data-table").getElementsByTagName("tbody")[0];
  const rows = Array.from(table.getElementsByTagName("tr"));
  
  rows.forEach((row, index) => {
    row.style.display = (index >= (curpage - 1) * rowsPerPage && index < curpage * rowsPerPage) ? "" : "none";
  });
  
  document.getElementById("page-info").innerText = `Page ${curpage} of ${Math.ceil(rows.length / rowsPerPage)}`;
}

// Function to go to the previous page
function prevPage() {
  if (curpage > 1) {
    curpage--;
    displayTable();
  }
}

// Function to go to the next page
function nextPage() {
	// alert("vcxv");die;
  const table = document.getElementById("data-table").getElementsByTagName("tbody")[0];
  const rows = Array.from(table.getElementsByTagName("tr"));
  
  if (curpage < Math.ceil(rows.length / rowsPerPage)) {
    curpage++;
    displayTable();
  }
}

// Close modal if user clicks outside the modal content
window.onclick = function(event) {
  const modal = document.getElementById("modal");
  if (event.target == modal) {
    closeModal();
  }
};

		</script>
		<?php
		die;
		?>