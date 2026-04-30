<?php

/** @var yii\web\View $this */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs; 
use yii\widgets\LinkPager;
use frontend\assets\AppAsset;
$this->title = 'Escalation Matrix';
$this->params['breadcrumbs'][] = $this->title;
AppAsset::register($this);

?>
<nav aria-label="breadcrumb" class="mt-2">
    <ol class="breadcrumb rounded-3">
        <li class="breadcrumb-item">
            <a class="link-body-emphasis" href="index">
                <i class="fa-solid fa-bars gray-shade-1-breadcrum"></i>
                <span class="visually-hidden">Home</span>
            </a>
        </li>
        <li class="breadcrumb-item">
            <a class="text-decoration-none text-dark-emphasis gray-shade-1-breadcrum" href="index">Dashboard</a>
        </li>
        <li class="breadcrumb-item">
            <a class="text-decoration-none text-dark-emphasis gray-shade-1-breadcrum" href="index">Escalation</a>
        </li>
       
    </ol>
</nav>

<h4>Escalation Matrix</h4>
  
    <div class="top-scroll">
        <div></div>
    </div>
    <div class="table-container mt-1">
        <!-- <div class="table-responsive"> -->
            <table class="table table-hover custom-table-border">
                <thead>
                    <tr>
                        <th>Level </th>
                         <th>Name </th>
                        <th>Department </th>
                       
                        <th>Designation</th>
                        <th>Email Address</th>
                        <th>Mobile Number</th>
                    </tr>
                </thead>
                <tbody>
                   
                        <tr>
                            <td>Level 1</td>
                            <td><?php echo $deshwal_isr["fullname"]??"";?></td>
                            <td><?php echo $deshwal_isr["user_department_value"]??"";?></td>
                            <td><?php echo $deshwal_isr["user_designation_value"]??"";?></td>
                            <td><?php echo $deshwal_isr["email"]??"";?></td>
                            <td><?php echo $deshwal_isr["mobile"]??"";?></td>
                        
                        </tr>
                         <tr>
                            <td>Level 2</td>
                            <td><?php echo $acc_manager["fullname"]??"";?></td>
                            <td><?php echo $acc_manager["user_department_value"]??"";?></td>
                            <td><?php echo $acc_manager["user_designation_value"]??"";?></td>
                            <td><?php echo $acc_manager["email"]??"";?></td>
                            <td><?php echo $acc_manager["mobile"]??"";?></td>
                        
                        </tr>
                   

                </tbody>
            </table>
        <!-- </div> -->
    </div>

<?php
$this->registerJs('
    $(document).ready(function() {
        console.log("This is a jQuery alert!");
    });
');
?>

