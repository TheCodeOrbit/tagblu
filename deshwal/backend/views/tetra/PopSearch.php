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
$maintabid=isset($_REQUEST['maintabid'])?$_REQUEST['maintabid']:'';	
$sourcemodule	=isset($_REQUEST['sourcemodule'])?$_REQUEST['sourcemodule']:'';	
$sourceid	=isset($_REQUEST['sourceid'])?$_REQUEST['sourceid']:'';	
// print_r($ColumnList);die;
?>
<style type="text/css">
 

.p-search-container {
  position: relative;
  width: 250px;
  margin: 20px;
}
.p-close{
	float: right;
	cursor: pointer;
}

/*#search-box {
  padding: 8px 30px 8px 10px;
  width: 100%;
  box-sizing: border-box;
}*/

.p-search-icon {
  position: absolute;
  top: 50%;
  right: 10px;
  transform: translateY(-50%);
  font-size: 16px;
  cursor: pointer;
}



.p-search-filters {
  display: flex;
  gap: 5px;
  margin-bottom: 10px;
}

.p-search-filters input {
  padding: 5px;
  width: 100%;
  box-sizing: border-box;
}

table {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 10px;
}

th, td {
  padding: 8px;
  text-align: left;
  border-bottom: 1px solid #ddd;
}

tr:hover {
  background-color: #f1f1f1;
}

.p-pagination {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.p-pagination button {
  padding: 5px 10px;
}

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
<div style="background-color: #2d4d7e;
  padding: 7px;
  color: #fff;"><?= ucfirst($mname); ?><span class="p-close" onclick="closeModal()">X</span></div>
		
			<div style="padding: 20px;">
		      
		     
		      <!-- Search Filters -->
		    <!--   <div class="p-search-filters">
		      	 <?php //print_r($RecordList);
								//$modname=$ModName;
								$maintabid=$_REQUEST['maintabid'];
								$col_span=count($ColumnList)+1;
								foreach ($ColumnList as $key=> $Column): ?>
								
								<input type="text" placeholder="<?php echo $Column;?>" id="search-<?php echo $key;?>" oninput="filterTable()">
								<?php endforeach;?>
		        
		        
		      </div> -->

		      <!-- Table -->
		      <table id="data-table">
		        <thead class="showinParent_server_thead"
				data-ref="<?= $refield; ?>"
									data-hidden="<?= $hiddenfield; ?>"
									data-mname="<?= $mname; ?>"
									data-maintabid="<?= $maintabid; ?>"
									data-rdisfield="<?= $rdisfield; ?>"
									data-sourcemodule="<?= $sourcemodule; ?>"
									data-sourceid="<?= $sourceid; ?>"
				>
		        	<tr>
		        		<td><button class="btn btn-success"  onclick="filterTable()">Search</button></td>
		        		 <?php //print_r($RecordList);
								//$modname=$ModName;
								$maintabid=$_REQUEST['maintabid'];
								$col_span=count($ColumnList)+1;
								foreach ($ColumnList as $key=> $Column): ?>
								<td>
								<input type="text" placeholder="<?php echo $Column;?>" id="search-<?php echo $key;?>">
								</td>
								<?php endforeach;?>
		        	</tr>

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
									<td>&nbsp;</td>
									<?php foreach ($ColumnList as $key=> $Column):?>
									<td class="cursorPointer data-cell showinParent_server popupsearch" 
									data-recordid="<?= $Record['RecordId']; ?>"
									data-display="<?= htmlspecialchars(addslashes($Record[$rdisfield]), ENT_QUOTES); ?>"
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
		        	
		          <!-- Add more rows as needed -->
		        </tbody>
		      </table>

		      <!-- Pagination -->
		      <div class="p-pagination">
		        <button onclick="prevPage()">Previous</button>
		        <span id="page-info">Page 1 of 1</span>
		        <button onclick="nextPage()">Next</button>
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



// Function to filter the table
function filterTable() {
	
  // Collect search terms
  const searchTerms = [
  <?php foreach ($ColumnList as $key=> $Column):?>
    document.getElementById("search-<?php echo $key;?>").value.toLowerCase(),
   
    <?php endforeach;?>
  ];

  const table = document.getElementById("data-table").getElementsByTagName("tbody")[0];
  const rows = table.getElementsByTagName("tr");

  Array.from(rows).forEach(row => {
    const cells =  row.querySelectorAll("td.data-cell");//row.getElementsByTagName("td");

    // Check if each cell contains its corresponding search term
    const rowMatches = Array.from(cells).every((cell, index) => {
      // Only filter if there's a search term for this column
      if (searchTerms[index]) {
        return cell.innerText.toLowerCase().includes(searchTerms[index]);
      }
      // If no search term is provided, consider it a match
      return true;
    });

    // Show or hide the row based on whether it matches the search criteria
    row.style.display = rowMatches ? "" : "none";
  });
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
// alert("dfsdf search");
// $(document).on("click", ".showinParent_server", function () {
// 	console.log("showinparent");alert("dfsdf");
// 		const $cell = $(this);
// 		const recordId = $cell.data("recordid");
// 		const display = $cell.data("display");
// 		const ref = $cell.data("ref");
// 		const hidden = $cell.data("hidden");

// 		showinParent(recordId, display, ref, hidden);
// 	});
		</script>
		<?php
		die;
		?>