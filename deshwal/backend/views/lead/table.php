<?php

use yii\helpers\Url;
use yii\helpers\Html;
use backend\assets\AdminAsset;


AdminAsset::register($this);

$this->title = Yii::t('app', 'Table List');

//Add DataTables CSS CDN
$this->registerCssFile('https://cdn.datatables.net/v/dt/dt-1.10.16/datatables.min.css', ['depends' => [AdminAsset::class]]);
?>

<style>
 
</style>


<!-- open lead Add button -->
<div class="group-15">
    <div class="group-16">
        <div class="overlap-group-13">
            <div class="text-wrapper-124">Add</div>
            <div class="text-wrapper-125">+</div>
        </div>
    </div>
    <div class="group-17">
        <div class="overlap-16">
            <div class="text-wrapper-126">Import</div>
            <img class="lets-icons-import" src="https://c.animaapp.com/4Te5O9cu/img/lets-icons-import-light.svg" />
        </div>
    </div>
    <img class="typcn-filter" src="https://c.animaapp.com/4Te5O9cu/img/typcn-filter.svg" />


    <div class="frame-22">
        <div class="group-18">
            <div class="overlap-group-14">
                <img class="vector-67" src="https://c.animaapp.com/4Te5O9cu/img/vector-73-1.svg" />
                <div class="text-wrapper-127">
                    <select class="view-selector" id="viewSelector">
                        <option value="list">List View</option>
                        <option value="kanban">Kanban</option>
                    </select>
                </div>
                <img class="ic-round-arrow-left-65" src="https://c.animaapp.com/4Te5O9cu/img/ic-round-arrow-left-68.svg" />
                <img class="tdesign-list" src="https://c.animaapp.com/4Te5O9cu/img/tdesign-list.svg" />
            </div>
        </div>
    </div>

    <img
        class="fluent-column-triple"
        src="https://c.animaapp.com/4Te5O9cu/img/fluent-column-triple-edit-24-regular.svg" />
    <img class="flowbite-refresh" src="https://c.animaapp.com/4Te5O9cu/img/flowbite-refresh-outline.svg" />
    <div class="group-19">
        <div class="all-open-lead">All Open Lead</div>
        <img class="ep-arrow-down-8" src="https://c.animaapp.com/4Te5O9cu/img/ep-arrow-down-26.svg" />
    </div>
</div>

<!-- end open lead Add button  -->
<div class="overlap-4">
    <div class="frame-8">
        <div class="overlap-5">



            <!-- Table -->

            <h3><a target="_blank" href="https://www.gyrocode.com/articles/jquery-datatables-column-reordering-and-resizing/">jQuery DataTables: Column reordering and resizing</a></h3>

            <table id="example" class="display" cellspacing="0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Position</th>
                        <th>Office</th>
                        <th>Extn</th>
                        <th>Start date</th>
                        <th>Salary</th>
                        <th>Name</th>
                        <th>Position</th>
                        <th>Office</th>
                        <th>Extn</th>
                        <th>Start date</th>
                        <th>Salary</th>
                    </tr>
                </thead>
            </table>
            <!-- End of Table -->

        </div>

    </div>

</div>


<?php
// Registering DataTables assets


$this->registerJsFile('https://cdn.datatables.net/v/dt/dt-1.10.16/datatables.min.js', ['depends' => [AdminAsset::class]]);

$this->registerJsFile('https://cdn.jsdelivr.net/gh/jeffreydwalter/ColReorderWithResize@9ce30c640e394282c9e0df5787d54e5887bc8ecc/ColReorderWithResize.js', ['depends' => [AdminAsset::class]]);


// Inline JavaScript to initialize the DataTable and handle resizing
$url = Url::to(['lead/get-table']);
$js = <<<JS
 $(document).ready(function (){
    var table = $('#example').DataTable({
        'ajax': 'https://gyrocode.github.io/files/jquery-datatables/arrays.json',
        'dom': 'Rlfrtip',
    	'scrollX': true,        
        'columnDefs': [
           {
              'targets': [6,7,8,9,10,11],
              'data': 0
           }
        ],
        'scrollY': '45vh',
        
        
    });
});

JS;

$this->registerJs($js);
?>