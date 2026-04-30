<link rel="stylesheet" href="<?= $baseUrl; ?>/thememain/css/multilist.css">
<?php //print_r($fieldoptions);
 // Convert to an indexed array format
        $response = [];
        foreach ($fieldoptions as $id => $name) {
            $response[] = ['id' => $id, 'name' => $name];
        }
        ?>
<div class="multi-list-container">
    <!-- Search Box -->
    <input type="text" id="searchInput" class="form-control <?= $field["typeofdata"];?>" placeholder="Search Users..." onkeyup="fetchFilteredList()" />

    <!-- Available Items List -->
    <div class="available-items">
        <!-- <h4>Available Items</h4> -->
        <ul id="availableList" class="availableList">
            <!-- Items will be dynamically added here -->
        </ul>
    </div>

    <!-- Added Items List -->
    <div id="added-items">
        <!-- <h4>Added Items</h4> -->
       <!--  <ul id="addedList">
            Added items will appear here
        </ul> -->
        <?php
        // print_r($Recordlist);
    if($Recordlist !='')
    {
       
      foreach($Recordlist as $key => $value)
        {
           
          
           ?>
        <span data-id="<?=$key;?>" class="attendee"><?=$value;?><span style="margin-left: 10px; border: medium; background: none; cursor: pointer;" class="remitem">X</span></span>
        <?php
        }
    }?>
    </div>
</div>
<?php //echo $Record;die;?>
<!-- Hidden input to store selected items for form submission -->
<input type="hidden" id="<?= $field["fieldname"];?>" name="<?= $field["tablename"] . '[' . $field["fieldname"] . ']' ;?>" class="<?= $field["typeofdata"];?>" value="<?= ($Record !='') ? $Record :'' ?>"/>
<script type="text/javascript">
    var typeofdata ='<?= $field["typeofdata"];?>';
    var fieldname ='<?= $field['fieldname']; ?>';
    <?php
    if(!empty($Recordlist)) 
    {?>
    var Recordlist =1 ;
    <?php
    }    
    else {?>Recordlist = '';<?php }?>
	// Fetch filtered data from the backend
    
 
</script>
<script type="text/javascript" src="<?= $baseUrl; ?>thememain/js/tetra/multilist.js"></script>