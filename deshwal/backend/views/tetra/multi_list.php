<!-- <link rel="stylesheet" href="< $baseUrl; ?>/thememain/css/select2.min.css">
<link rel="stylesheet" href="< $baseUrl; ?>/thememain/css/multilist-dd.css"> -->

<?php 
use backend\assets\AdminAsset;

use yii\helpers\Url;

AdminAsset::register($this);
use yii\helpers\Html;
//print_r($fieldoptions);
 // Convert to an indexed array format
$baseUrl = Yii::$app->HomeUrl;
 
 $this->registerJsFile(Url::to($baseUrl."thememain/js/tetra/multilist-dd.js"), ['depends' => [yii\web\JqueryAsset::class]]);
//print_r($fieldoptions);
 // Convert to an indexed array format
 
        $response = [];
        foreach ($fieldoptions as $id => $name) {
            $response[] = ['id' => $id, 'name' => $name];
        }
        ?>
        <br>
<?php
// print_r($selectedValues);die;
$typeofdata = $field["typeofdata"];
if($readonly)
$typeofdata = str_replace("~M","~O",$field["typeofdata"]);
if ($field["mandatory"] == 1) {
		
					echo Html::{$field["fieldtype"]}(
						$field["tablename"] . '[' . $field["fieldname"] . ']',
						$selectedValues,
						$field["fieldoptions"],
						['multiple' => true,  // Allow multiple selection,
						'class' => 'multySelect form-control '.$typeofdata,'id' => $field["fieldname"], "prompt" => "Select " . $field["fieldlabel"], 'data-pristine-required' => 'true', 'data-pristine-required-message' => $field["fieldlabel"] . ' is required ', 'disabled' => $readonly ? true : false]
					);
				} else {
					echo Html::{$field["fieldtype"]}($field["tablename"] . '[' . $field["fieldname"] . ']',$selectedValues, $field["fieldoptions"], [
						'multiple' => true,  // Allow multiple selection,
						// 'disabled' => $readonly ? true : false,
						 'class' => 'multySelect form-control '.$typeofdata ,
						 'id' => $field["fieldname"],
						  "prompt" => "Select " . $field["fieldlabel"]]);
				}
?>

<!-- <script type="text/javascript" src="<$baseUrl; ?>thememain/js/tetra/multilist-dd.js"></script> -->
