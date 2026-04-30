<!-- <link rel="stylesheet" href="< $baseUrl; ?>/thememain/css/select2.min.css">
<link rel="stylesheet" href="< $baseUrl; ?>/thememain/css/multilist-dd.css"> -->

<?php 
use backend\assets\AdminAsset;

use yii\helpers\Url;

AdminAsset::register($this);
use yii\helpers\Html;
//print_r($fieldoptions);
 // Convert to an indexed array format

//  $this->registerJsFile(Url::to($baseUrl."thememain/js/tetra/single-dd.js"), ['depends' => [yii\web\JqueryAsset::class]]);
 ?>
<br>
<?php

echo Html::{$field["fieldtype"]}(
						$field["tablename"] . '[' . $field["fieldname"] . ']',
						isset($Record) ? $Record :'',
						$field["fieldoptions"],
						$classarray
					);
?>

<!-- <script type="text/javascript" src="< $baseUrl; ?>thememain/js/tetra/single-dd.js"></script> -->
