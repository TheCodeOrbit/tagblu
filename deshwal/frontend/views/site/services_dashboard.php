<?php

/** @var yii\web\View $this */
$static_logic = false;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs; 
use yii\widgets\LinkPager;
use frontend\assets\AppAsset;
$this->title = 'Dashboard';
$this->params['breadcrumbs'][] = $this->title;
AppAsset::register($this);

function serviceName($service){
    if(empty($service)) return "";
    if($service == "degaussing") return "Degaussing";
    if($service == "drilling") return "Drilling";
    if($service == "datawiping") return "Data Wiping";
    if($service == "shredding") return "Shredding";
    if($service == "weighing") return "Weighing";
    return $service;
}
?>
<!-- <?= Breadcrumbs::widget([
        'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
    ]) 
?> -->
<h4 class="mt-3">Dashboard</h4>
    <div class="table-container mt-2">
        <table class="table table-hover custom-table-border">
            <thead>
                <tr>
                    <th>Activity Type</th>
                    <th>Total Activities</th>
                    <th>Completed</th>
                    <th>Pending</th>
                    <th>In Process</th>
                </tr>
            </thead>
            <tbody>
                <?php if($static_logic){ ?>
                    <tr>
                    <td>Degaussing</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                </tr>
                <tr>
                    <td>Drilling</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                </tr>
                <tr>
                    <td>Shredding</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                </tr>
                <tr>
                    <td>Data Wiping</td>
                    <td>3</td>
                    <td>1</td>
                    <td>1</td>
                    <td>1</td>
                </tr>
                <tr>
                    <td>Weighing</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                </tr>
                <?php }else{
                foreach($data as $key =>$value){ 
                    $service_name = serviceName($key);
                ?>
                    <tr>
                        <td><?php echo $service_name??"";?></td>
                        <td><?php echo $value["total_count"]??"";?></td>
                        <td><?php echo $value["completed_count"]??"";?></td>
                        <td><?php echo $value["pending_count"]??"";?></td>
                        <td><?php echo $value["in_process_count"]??"";?></td>
                    </tr>
                    <?php } 
                } ?>
                
            </tbody>
        </table>
    </div>

<!-- Custom Pagination Links -->

<?php
// Register the external JS file
$this->registerJsFile('@web/js/about/edit.js', ['depends' => [AppAsset::class]]);
// Register your jQuery code to display an alert
$this->registerJs('
    $(document).ready(function() {
        console.log("This is a jQuery alert!");
    });
');
?>

