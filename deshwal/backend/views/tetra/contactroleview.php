<?php

use yii\helpers\Url;
use yii\helpers\Html;
use backend\assets\AdminAsset;


AdminAsset::register($this);
$this->title = Yii::t('app', 'Add ' . $TabLabel);

$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css', ['depends' => [AdminAsset::class]]);

$this->registerCssFile('@web/thememain/css/ag-grid.css', ['depends' => [AdminAsset::class]]);
$this->registerCssFile('@web/thememain/css/ag-theme-alpine.css', ['depends' => [AdminAsset::class]]);
$this->registerCssFile('@web/thememain/css/listview.css', ['depends' => [AdminAsset::class]]);


$url = Url::to(['create']);
$uploadUrl = Url::to(['csvupload']);
$baseUrl = Yii::$app->HomeUrl;

$csrfTokenName = Yii::$app->request->csrfParam;  // This replaces csrfTokenName
$csrfToken = Yii::$app->request->csrfToken;      // Get the CSRF token itself 

$leadsByStatus = [];

if (!empty($leadInformation)) {
  foreach ($leadInformation as $lead) {

    $leadsByStatus[$lead[$kanbnacolumn]][] = $lead;
  }
}

?>
<style type="text/css">
  /* Default clipping for all cells */
  /* .ag-cell {
    white-space: nowrap !important;
    overflow: hidden  !important;
    text-overflow: ellipsis !important;
  }*/
  */

  /* Wrapping class */
  /* .ag-cell-wrap-dp {
    white-space: normal !important;
    overflow-wrap: break-word !important;
    word-wrap: break-word !important;
    word-break: break-word !important;
  }  */
  /* Button styling */
  .btn-1 {
    background-color: #007bff;
    color: white;
    border: none;
    padding: 10px 20px;
    cursor: pointer;
    border-radius: 4px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
  }

  /* Popup styling */
  .popup {
    display: none;
    /* Initially hidden */
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    justify-content: center;
    align-items: center;
    z-index: 1000;
  }

  .popup-content {
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    text-align: center;
    width: 300px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    position: relative;
  }

  .close-btn {
    position: absolute;
    top: 10px;
    right: 10px;
    cursor: pointer;
    font-size: 18px;
    color: #888;
  }



</style>
<input type="hidden" value="<?php echo $ModuleName; ?>" id="module" name="module" />
<input type="hidden" name="csrfToken" id="csrfToken" value="<?= $csrfToken; ?>">
<input type="hidden" name="csrfTokenName" id="csrfTokenName" value="<?= $csrfTokenName; ?>">
<div class="page-content">
  <div class="records table-responsiv">
    <div class="record-header">
      <div class="add">
        <img src="<?= $baseUrl; ?>/thememain/img/module-icon/<?= $ModuleName; ?>.png" class=" head-img">
        <span class="sm-modname"><?= $TabLabel; ?></span>
        <br>

        <?php
        if (!empty($allfilter)) {
        ?>
          <select name="filterselectbox" id="filterselectbox">
            <?php
            foreach ($allfilter as $key => $value) {
              if ($defaultfilter['id'] == $value['id'])
                $sel = 'selected';
              else $sel = '';
            ?>
              <option value="<?= $value['id']; ?>" <?= $sel; ?>><?= $value['filter_name']; ?></option>
            <?php
            }
            ?>
          </select>
        <?php
        } else {
        ?>

          <select name="filterselectbox" id="filterselectbox"></select>
        <?php
        } ?>


      </div>

      <div class="browse">
      
         
        <?php //if (Yii::$app->session->hasFlash('success')): 
        ?>
        <!-- <div class="alert alert-success" id="flashMessage">
        <?php  //Yii::$app->session->getFlash('success') 
        ?>
    </div>
    <script>
        // Wait for 3 seconds and then hide the flash message
        setTimeout(function() {
            $('#flashMessage').fadeOut();
        }, 3000); // 3000 milliseconds = 3 seconds
    </script> -->
        <?php //endif; 
        ?>
      
        <?php
        // if ($layout == 'multiple' || $layout == 'single') {
        //   $sourcemodule = '';
        //   $sourceid = '';
        //   if(isset($_GET['sourceid']))
        //   $sourceid = filter_var($_GET['sourceid'], FILTER_VALIDATE_INT);
        //   if(isset($_GET['sourcemodule']))
        //   $sourcemodule = filter_var($_GET['sourcemodule'], FILTER_VALIDATE_INT);
        //   if(!empty($sourceid) && !empty($sourcemodule))
        //   $url = "create?sourcemodule=".$sourcemodule."&sourceid=".$sourceid;
        //  else $url = "create";
        // ?>
           <!-- <a href="< $url;?>" class="add-lead-btn2" title="Add New"><button>+ Add</button></a> -->
        <?php
        // } else {
        ?>
          <button id="add-contact-role" class="add-lead-btn2" title="Add New"> + Add</button>
        <?php
        // }
        ?>
      </div>
    </div>
  </div>
</div>

<!-- Filter By Name Modal Structure -->

<div class="modal fade " id="filterByNameModel" aria-modal="true" role="dialog">
  <?php
  $showdropdown = 0;
  if (!empty($defaultfiltercondition)) {
    // print_r($defaultfiltercondition);
    // lter_id] => 1 [fieldid] => 8 [fieldlabel] => First Name [filteroperator] => Equals [userinput] => Sai [userid] => 1 [deleted] => 0 ) 
    $filterlabel = $defaultfiltercondition['fieldlabel'];
    $fieldid = $defaultfiltercondition['fieldid'];
    $filter_id = $defaultfiltercondition['filter_id'];
    $filteroperator = $defaultfiltercondition['filteroperator'];
    $userinput = $defaultfiltercondition['userinput'];
    $filterdisplay = "block";
    $filterFieldName = $defaultfiltercondition['columnname'];
    $filterFielduitype = $defaultfiltercondition['uitype'];
    $filterFieldtablename = $defaultfiltercondition['tablename'];
    if($filterFielduitype == 8)
    {
      //get values from picklist
      $showdropdown = 1;
    }
  } else {
    $filterlabel = '';
    $fieldid = '';
    $filter_id = '';
    $filteroperator = '';
    $userinput = '';
    $filterdisplay = "none";
    $filterFieldName = '';
    $filterFielduitype = '';
    $filterFieldtablename = '';
    
  }
  ?>
  <div class="modal-dialog">
    <div class="modal-content" style="width: 73%; height: 69%;">
      <div class="modal-header">
        <h4 class="modal-title">Filter <?= $TabLabel; ?> By </h4>
        <button type="button" class="btn-close fil-btn" aria-label="Close"></button>
      </div>
      <div class="modal-header">
        <div class="input-group mb-3">
          <input type="text" class="form-control filtercolumnvalues" placeholder="Search" aria-label="Search">
          <button class="btn btn-outline-secondary" onclick="openfieldName()" type="button" id="addFieldButton">
            <span class="fa fa-plus"></span>
          </button>
        </div>
      </div>
      <div class="modal-body">
        <div id="field_name" style="display:none">
          <?php foreach ($filed_name as $filed_names):

          ?>
            <div class="filed-div" data-label="<?php echo $filed_names['fieldlabel']; ?>" onclick="openFilterBox('<?php echo $filed_names['fieldid']; ?>', '<?php echo $filed_names['fieldname']; ?>','<?php echo $filed_names['fieldlabel']; ?>','<?php echo $filed_names['uitype']; ?>','<?php echo $filed_names['tablename']; ?>')">

              <?php echo $filed_names['fieldlabel']; ?>

            </div>
          <?php endforeach; ?>
        </div>

        <!-- Container for the filter box (initially hidden) -->
        <div id="filterBox" class="filter-box" style="display:<?= $filterdisplay; ?>;" data-field-id="<?= $fieldid; ?>">
          <div class="field-label-row">
            <div class="filterfieldlabel">
              <span id="filterFieldLabel"><?= $filterlabel; ?></span>
            </div>
            <div class="filtertrashbox">
              <i onclick="closeFilterBox()" class="fa fa-trash close-button" style="margin-left: 188px;"></i>
            </div>
          </div>

          <input type="hidden" id="filterFieldName" value="<?= $filterFieldName; ?>">
          <input type="hidden" id="filterFielduitype" value="<?= $filterFielduitype; ?>">
          <input type="hidden" id="filterFieldtablename" value="<?= $filterFieldtablename; ?>">
          <input type="hidden" id="filterId" value="<?= $filter_id; ?>">
          <!-- Dropdown for selecting comparison operators -->
          <select id="filterOperator" class="form-select">
            <option value="Equals" <?php if ($filteroperator == "Equals") echo "Selected"; ?>>Equals</option>
            <option value="Not_Equals" <?php if ($filteroperator == "Not_Equals") echo "Selected"; ?>>Not Equals</option>
            <option value="Contains" <?php if ($filteroperator == "Contains") echo "Selected"; ?>>Contains</option>
            <option value="Not_Contains" <?php if ($filteroperator == "Not_Contains") echo "Selected"; ?>>Not Contains</option>
            <option value="In" <?php if ($filteroperator == "In") echo "Selected"; ?>>In</option>
            <option value="Not_In" <?php if ($filteroperator == "Not_In") echo "Selected"; ?>>Not In</option>
            <option value="is_Empty" <?php if ($filteroperator == "is_Empty") echo "Selected"; ?>>is Empty</option>
            <option value="is_Not_Empty" <?php if ($filteroperator == "is_Not_Empty") echo "Selected"; ?>>is Not Empty</option>
            <option value="Begins_with" <?php if ($filteroperator == "Begins_with") echo "Selected"; ?>>Begins With</option>
          </select>
          <?php 
          if($showdropdown == 1)
          {
            echo $defaultfiltercondition['opt'];
          }
          else{
            ?>
          <input type="text" class="form-control" id="filterValue" placeholder="Enter value" style="display:<?= $filterdisplay; ?>;" value="<?= $userinput; ?>" />
          <?php
          }
          ?>

        </div>
      </div>

      <div class="modal-footer">
        <button type="button" id="filter-save-as" class="btn btn-primary">Save As </button>
        <button type="button" id="filter-save" onclick="SaveFilter()" class="btn btn-primary">Save </button>
        <button type="button" id="apply-filter-by-name" onclick="applyFilter()" class="btn btn-primary">Apply</button>
      </div>
    </div>
  </div>
</div>
<!-- End Filter By Name -->





<!-- Add Modal Structure -->

<div class="modal fade" id="add-lead-modal" tabindex="-1" role="dialog" aria-labelledby="addLeadModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">



    </div>
  </div>
</div>

<!-- end add model -->
<div class="select-1">
  <div class="container-d">
    



    <!-- Table -->
    <div class="table-list">
      <div id="myGrid" class="ag-theme-alpine"></div>


      <div id="custom-pagination" class="pagination-container">
        <label for="page-size" class="results-per-page">Results Per Page:</label>
        <select id="page-size" class="page-size-dropdown"><!-- onchange="changePageSize()"-->

          <option value="10">10</option>
          <option value="20">20</option>
          <option value="50">50</option>
          <option value="100">100</option>
        </select>

        <button id="first-page" class="pagination-button first-page" onclick="goToPage(1)">First</button>
        <div id="pagination-buttons" class="pagination-buttons"></div>
        <button id="last-page" class="pagination-button last-page" onclick="goToPage(totalPages)">Last</button>
      </div>



      <!-- pagination -->


    </div>
    <!-- End of Table -->


   

  </div>

  <!-- end kanban -->
</div>
</div>




<!-- Mass Edit Modal -->
<div class="modal fade" id="updateModel" tabindex="-1" role="dialog" aria-labelledby="updateModelLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      
    </div>
  </div>
</div>




<script>
  // Open the popup on button click
  document.getElementById("generateCsvButton").addEventListener("click", function() {
    document.getElementById("popup").style.display = "block";
  });

  // Close the popup on close button click
  document.getElementById("closePopup").addEventListener("click", function() {
    document.getElementById("popup").style.display = "none";
  });

  // Generate CSV on button click
  document.getElementById("generateCsv").addEventListener("click", function() {
    // Column names dynamically populated from PHP
    <?php
echo "const columns = [";
foreach ($DataImport as $keval) {
    if ($keval['mandatory'] == 1) {
        // Append "(mandatory)" for mandatory fields
        echo '"' . $keval['fieldlabel'] . ' (mandatory)",';
    } else {
        echo '"' . $keval['fieldlabel'] . '",';
    }
}
echo "];";
?>

    // Add a sample data row (this should ideally come from your database dynamically)
    const data = [
      ["1", "test", "", "", "", "", "", "", "", "", "", ""]
    ];

    // Combine column names and data into CSV format
    let csvContent = columns.join(",") + "\n"; // Add headers
    data.forEach(row => {
      csvContent += row.join(",") + "\n"; // Add rows
    });

    // Create a Blob and a download link for the CSV
    const blob = new Blob([csvContent], {
      type: "text/csv"
    });
    const link = document.createElement("a");
    link.href = URL.createObjectURL(blob);
    link.download = "data Formate.csv"; // File name for download
    link.style.display = "none";

    // Append the link, trigger download, and remove the link
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    // Close the popup after downloading the CSV
    document.getElementById("popup").style.display = "none";
  });
</script>

<!-- Start Styles Zitendra Rai-->
<style>
  /* General Styles */
  body {
    font-family: Arial, sans-serif;
  }

  /* Button Styles */
  .btn {
    display: inline-block;
    padding: 10px 20px;
    font-size: 16px;
    border: none;
    cursor: pointer;
    border-radius: 5px;
    text-align: center;
  }

  .btn-primary {
    background-color: #007bff;
    color: white;
  }

  .btn-success {
    background-color: #28a745;
    color: white;
  }

  .btn-primary:hover,
  .btn-success:hover {
    opacity: 0.9;
  }

  /* Popup Styles */

  .popup {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 1000;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.4);
    /* Black background with transparency */
  }

  .popup-content {
    background: white;
    padding: 20px;
    border-radius: 10px;
    width: 400px;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
    text-align: center;
  }

  .popup-section {
    margin-bottom: 20px;
  }

  .popup-content h2 {
    margin-top: 0;
    color: #333;
  }

  .popup-content label {
    display: block;
    margin-bottom: 8px;
    font-weight: bold;
    color: #555;
  }

  .popup-content .file-input {
    margin-bottom: 15px;
    width: 100%;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 5px;
  }

  /* Close Button Styles */
  .close-btn {
    position: absolute;
    top: 10px;
    right: 15px;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    color: #aaa;
  }

  .close-btn:hover {
    color: black;
    text-decoration: none;
  }
</style>
<!-- End Styles Zitendra Rai-->


<?php



$this->registerJs("
    // $('#add-lead-btn').click(function() {
    //     $('#add-lead-modal').modal('show');
    // });
    

// applyFilter();

 
");

$this->registerJsFile('@web/thememain/js/ag-grid-community.min.js', ['depends' => [AdminAsset::class]]);

$this->registerJsFile('@web/thememain/js/custom.js', ['depends' => [AdminAsset::class]]);

?>

<script>
  document.addEventListener("DOMContentLoaded", () => {
    const importButton = document.getElementById("importButton");
    const popup = document.getElementById("popup");
    const closePopup = document.getElementById("closePopup");

    // Show the popup
    importButton.addEventListener("click", () => {
      popup.style.display = "flex";
    });

    // Close the popup
    closePopup.addEventListener("click", () => {
      popup.style.display = "none";
    });

    // Close popup when clicking outside of it
    window.addEventListener("click", (event) => {
      if (event.target === popup) {
        popup.style.display = "none";
      }
    });
  });
</script>