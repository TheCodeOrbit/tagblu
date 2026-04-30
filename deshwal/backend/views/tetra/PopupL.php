<style type="text/css">
.child-search-row{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%)!important;border-top:3px solid #5a67d8;display:none}.child-search-row td{padding:10px!important;vertical-align:top}.child-search-label{font-size:10px;font-weight:600;color:#fff;margin-bottom:4px;display:block;text-transform:uppercase;letter-spacing:.5px}.child-search-input{background:#fffffff2!important;border:1px solid #ffffff4d!important;color:#333!important;font-size:13px;width:100%}.child-search-input:focus{background:#fff!important;border-color:#ffd700!important;box-shadow:0 0 0 2px #ffd7004d;outline:none}.child-search-input::placeholder{color:#888;font-style:italic}.child-search-row td[colspan]{overflow-x:auto;display:block}.child-search-row .row{display:flex;flex-wrap:nowrap;min-width:max-content}.child-search-row .col-lg-4{flex:0 0 auto;min-width:200px}.child-search-row td[colspan]::-webkit-scrollbar{height:8px}.child-search-row td[colspan]::-webkit-scrollbar-track{background:#ffffff1a;border-radius:4px}.child-search-row td[colspan]::-webkit-scrollbar-thumb{background:#ffffff80;border-radius:4px}.child-search-row td[colspan]::-webkit-scrollbar-thumb:hover{background:#ffffffb3}
</style>

<?php

use backend\assets\AdminAsset;

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

$dependent=isset($_REQUEST['dependent'])? $_REQUEST['dependent']:'';		
$conditionfield	=isset($_REQUEST['conditionfield'])? $_REQUEST['conditionfield']:'';
$dependentval=isset($_REQUEST['dependentval'])? $_REQUEST['dependentval']:'';
	
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
		     <div class="container-add-doc">	
		        	<div class="row">
						<div class="col-6">
	 <?php if (!empty($childSearchConfig) && is_array($childSearchConfig)): ?>
						<button type="button"
								class="btn btn-secondary ms-2 mb-2"
								id="adv-search-btn">
							<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16" style="margin-right:5px;">
								<path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
							</svg>
							Advanced Search
						</button>
					<?php endif; ?>
						</div>
						<div class="col-6">
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
		            <select class="page-select">
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
						</div>
					</div>
        <div class="table-wrapper">
		     
		      <table id="data-table">
		        <thead class="showinParent_server_thead"
				data-ref="<?= $refield; ?>"
									data-hidden="<?= $hiddenfield; ?>"
									data-mname="<?= $mname; ?>"
									data-maintabid="<?= $maintabid; ?>"
									data-rdisfield="<?= $rdisfield; ?>"
									data-sourcemodule="<?= $sourcemodule; ?>"
									data-sourceid="<?= $sourceid; ?>"
									data-dependent="<?=  $dependent; ?>"
									data-conditionfield="<?=  $conditionfield; ?>"
									data-dependentval="<?=  $dependentval; ?>"
									>
		        	
		          <tr>
		          	<th>&nbsp;</th>
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
		        		<td><button class="btn select-btn" onclick="filterTable()">Search</button></td>
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
										<svg class="v-icon-left" fill="#2F80ED" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="15" height="15" role="button" tabindex="0" data-removefiltervalue="search-<?php echo $key;?>" aria-label="Remove <?php echo $Column;?>" title="Remove <?php echo $Column;?>">
											<path d="M4.7070312 3.2929688 L3.2929688 4.7070312 L10.585938 12 L3.2929688 19.292969 L4.7070312 20.707031 L12 13.414062 L19.292969 20.707031 L20.707031 19.292969 L13.414062 12 L20.707031 4.7070312 L19.292969 3.2929688 L12 10.585938 L4.7070312 3.2929688 Z"></path>
										</svg>

										<input type="text" class="v-input" placeholder="<?php echo $Column;?>" id="search-<?php echo $key;?>"   value="<?= $searchval;?>">
									</div>
								</td>
								<?php endforeach;?>
		        	</tr>
					<?php if (!empty($childSearchConfig) && is_array($childSearchConfig)): ?>
    <?php
        $hasChildSearchValues = false;
        if (!empty($searchparam_child) && is_array($searchparam_child)) {
            foreach ($searchparam_child as $val) {
                if (!empty($val)) {
                    $hasChildSearchValues = true;
                    break;
                }
            }
        }
        $autoShowChild = $hasChildSearchValues ? 'table-row' : 'none';
    ?>
    
    <tr id="child-search-row" class="child-search-row" style="display: <?= $autoShowChild ?>;">
        <td style="padding: 10px; vertical-align: middle; min-width: 40px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="white" viewBox="0 0 16 16">
                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                <path d="M5.255 5.786a.237.237 0 0 0 .241.247h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286zm1.557 5.763c0 .533.425.927 1.01.927.609 0 1.028-.394 1.028-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94z"/>
            </svg>
        </td>
        
        <?php foreach ($childSearchConfig as $cfg): ?>
            <?php
            if (!is_array($cfg)) continue;
            
            $childKey       = $cfg['child_table'] . '.' . $cfg['columnname'];
            $fieldLabel     = $cfg['fieldlabel'];
            $childSearchVal = isset($searchparam_child[$childKey]) ? $searchparam_child[$childKey] : '';
            ?>
            
            <td style="padding: 10px; min-width: 150px;">
                <div class="mb-1">
                    <label class="child-search-label">
                        🔍 <?= htmlspecialchars($fieldLabel) ?>
                    </label>
                    <input type="text"
                           class="form-control form-control-sm child-search-input"
                           data-child-key="<?= htmlspecialchars($childKey) ?>"
                           placeholder="<?= htmlspecialchars($fieldLabel) ?>"
                           value="<?= htmlspecialchars($childSearchVal) ?>">
                </div>
            </td>
            
        <?php endforeach; ?>
    </tr>
<?php endif; ?>


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

								<tr>
									<td>&nbsp;</td>
									<?php foreach ($ColumnList as $key=> $Column):?>
									<td class="cursorPointer data-cell showinParent_server pl"
									data-recordid="<?= $Record['RecordId']; ?>"
									data-display="<?= (isset($Record[$rdisfield])) ? htmlspecialchars(addslashes($Record[$rdisfield]), ENT_QUOTES) : ''; ?>"
									data-ref="<?= $refield; ?>"
									data-hidden="<?= $hiddenfield; ?>"
									data-mname="<?= $mname; ?>"
									data-maintabid="<?= $maintabid; ?>"
									data-rdisfield="<?= $rdisfield; ?>"
									data-sourcemodule="<?= $sourcemodule; ?>"
									data-sourceid="<?= $sourceid; ?>"
									data-bs-dismiss="modal"><?php echo $Record[$key];?>
									
									</td>
									<?php endforeach;?>
								</tr>

								<?php endforeach; else :?>
								<tr>
									<td class="text-center" colspan="<?php echo $col_span;?>">No Record Found</td>
								</tr>
								<?php endif;?>
		        	
		        </tbody>
		      </table>

		     
		    </div>
		     
		 </div>
		
<script type="text/javascript" nonce="<?= Yii::$app->params['cspNonce'] ?>">
curpage = 1;
rowsPerPage = 5;

$(".page-select").on("change",function(){
	filterTable();
});
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
  const searchTerms = [];
  <?php foreach ($ColumnList as $key => $Column): ?>
    var sv<?= $key ?> = document.getElementById("search-<?= $key ?>").value.toLowerCase();
    if (sv<?= $key ?> !== '') {
      searchTerms.push(['<?= $key ?>', sv<?= $key ?>]);
    }
  <?php endforeach; ?>

    var pageselectval = $(".page-select").val();
    if (pageselectval && pageselectval != 0)
        pageselectval = pageselectval - 1;
    if (searchTerms.length > 0)
        pageselectval = 0;

    // collect child search as same structure: [ [childKey, value], ... ]
    const childTerms = [];
    $('.child-search-input').each(function(){
        var val = $(this).val().trim();
        var key = $(this).data('child-key'); 
        if (val && key) {
            childTerms.push([key, val]);
        }
    });
	console.log(childTerms,'childTerms');
    showCustomer1(
        '<?= $hiddenfield ?>',
        '<?= $refield ?>',
        '<?= $rdisfield; ?>',
        '<?= $mname; ?>',
        '<?= $maintabid;?>',
        '',
        pageselectval,
        searchTerms,
        '<?= $sourcemodule ?>',
        '<?= $sourceid ?>',
        childTerms
    );
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
// $(document).on("click", ".showinParent_server", function () {
	
// 	console.log("showinparent"+recordId +"-"+display + "-"+ref+"-"+hidden);
// 		const $cell = $(this);
// 		const recordId = $cell.data("recordid");
// 		const display = $cell.data("display");
// 		const ref = $cell.data("ref");
// 		const hidden = $cell.data("hidden");

// 		showinParent(recordId, display, ref, hidden);
// 	});

		</script>
		<?php
		// $js = <<<'JS'
		// 	alert("outside");
		// 	$(document).on("click", ".showinParent_server", function () {
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