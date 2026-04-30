<?php

use yii\helpers\Html;

// echo "<pre>";print_r($leads);die;<?php
$i = 0;
// echo "<pre>";
// print_r($leads);
foreach ($leads['leadInformation'] as $lead) {
    // echo "<pre>";print_r($leads['ColumnList']);die;
   
?>
    <div class="card">
        <?php  foreach ($leads['ColumnList'] as $key => $column) {?>
        <p><strong><?= $column ?> </strong> : <?= $lead[$key]; ?></p>
        <?php }?>
        <button class="dropdown-btn" onclick="toggleDropdown(this)">
            <i class="fa-solid fa-ellipsis-vertical"></i>
        </button>
        <div class="card-options">
            <div>Open in new tab</div>
            <div>Edit</div>
            <!-- <div>Reassign</div>
                      <div>Convert</div>
                      <div>Clone</div> -->
            <div>Archive</div>
        </div>

    </div>
<?php
}
$i++;
?>