<?php

use yii\helpers\Html;
use backend\assets\AdminAsset;

$baseUrl = Yii::$app->HomeUrl;

AdminAsset::register($this);
// $this->registerCssFile($baseUrl . 'thememain/css/bootstrap-timepicker.min.css', ['depends' => [AdminAsset::class]]);
// $this->registerJsFile($baseUrl . "thememain/js/bootstrap-timepicker.min.js", ['depends' => [yii\web\JqueryAsset::class]]);
$this->registerCssFile($baseUrl . 'thememain/css/flatpickr.min.css', ['depends' => [AdminAsset::class]]);
$this->registerJsFile($baseUrl . 'thememain/js/flatpickr.js', ['depends' => [yii\web\JqueryAsset::class]]);
?>

<!-- <link rel="stylesheet" href="< $baseUrl; ?>theme/css/bootstrap.min.css"> -->
<link rel="stylesheet" href="<?= $baseUrl; ?>thememain/css/bootstrap-timepicker.min.css">
<link rel="stylesheet" href="<?= $baseUrl; ?>thememain/css/flatpickr.min.css">
<!-- <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css" /> -->
<!-- <script type="text/javascript" src="< $baseUrl; ?>/thememain/js/bootstrap-timepicker.min.js"></script>
<script type="text/javascript" src="< $baseUrl; ?>thememain/js/flatpickr.js"></script> -->
<?php
if(isset($cnt_rows))
echo  Html::{$field["fieldtype"]}($field["tablename"] . '[' . $cnt_rows . ']' . '[' . $field["fieldname"] . ']', isset($Record->{$field["columnname"]}) ? empty($Record->{$field["columnname"]}) ? "0:00" : $Record->{$field["columnname"]} : "", $classarray);
else
echo  Html::{$field["fieldtype"]}($field["tablename"] . '[' . $field["fieldname"] . ']', isset($Record->{$field["columnname"]}) ? empty($Record->{$field["columnname"]}) ? "0:00" : $Record->{$field["columnname"]} : "", $classarray);
// die;
?>
<!-- <script type="text/javascript">
  // timepicker
$('.timepicker').timepicker({
            showMeridian: false,
            showInputs: true
});
//end timepicker
</script> -->


<?php
$js = <<<JS
 $(document).ready(function (){

flatpickr('.timepicker', {
    enableTime: true,
    noCalendar: true,
    // dateFormat: "H:i",
    // time_24hr: true,  
    // defaultDate: new Date()
});

});

JS;

$this->registerJs($js, \yii\web\View::POS_END);

?>