<?php

use yii\helpers\Html;
use backend\assets\AdminAsset;

$baseUrl = Yii::$app->HomeUrl;

AdminAsset::register($this);
// $this->registerCssFile($baseUrl . 'thememain/css/bootstrap-timepicker.min.css', ['depends' => [AdminAsset::class]]);
// $this->registerJsFile($baseUrl . "thememain/js/bootstrap-timepicker.min.js", ['depends' => [yii\web\JqueryAsset::class]]);
$this->registerCssFile($baseUrl . 'thememain/css/flatpickr.min.css', ['depends' => [AdminAsset::class]]);
$this->registerJsFile($baseUrl . 'thememain/js/flatpickr.js', ['depends' => [yii\web\JqueryAsset::class]]);

// $this->registerJs("alert('Hello World');", \yii\web\View::POS_END);


?>

<!-- <link rel="stylesheet" href="< $baseUrl; ?>theme/css/bootstrap.min.css"> -->
<!-- <link rel="stylesheet" href="< $baseUrl; ?>thememain/css/bootstrap-timepicker.min.css"> -->
<link rel="stylesheet" href="<?= $baseUrl; ?>thememain/css/flatpickr.min.css">
<!-- <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css" /> -->
 <!-- <script type="text/javascript" src="< $baseUrl; ?>/thememain/js/bootstrap-timepicker.min.js"></script> -->
<script type="text/javascript" src="<?= $baseUrl; ?>thememain/js/flatpickr.js"></script>

<?php
// if(isset($MRecord)){ 
//     // echo $field["columnname"];
//     if(isset($Record->{$field["columnname"]}))
//     echo $MRecord->{$field["columnname"]};
//     die;
// }
// print_r($classarray);
if(isset($cnt_rows))
echo Html::input($field["fieldtype"], $field["tablename"] . '[' . $cnt_rows . ']' . '[' . $field["fieldname"] . ']', isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : "", $classarray);
else
echo Html::input($field["fieldtype"], $field["tablename"] . '[' . $field["fieldname"] . ']', isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : "", $classarray);
// die;
?>

<?php
if($classarray['readonly'] == 1)
{
    if(isset($Record->{$field["columnname"]}))
    $storedDate = isset($Record->{$field["columnname"]}) ? ($Record->{$field["columnname"]} == '0000-00-00' || $Record->{$field["columnname"]} == '0000-00-00 00:00:00')?"":$Record->{$field["columnname"]} : "";
  else if(isset($MRecord->{$field["columnname"]}))
    $storedDate = isset($MRecord->{$field["columnname"]}) ? ($MRecord->{$field["columnname"]} == '0000-00-00' || $MRecord->{$field["columnname"]} == '0000-00-00 00:00:00')?"":$MRecord->{$field["columnname"]} : "";
else $storedDate='';

    ?>
    <script nonce="<?= Yii::$app->params['cspNonce'] ?>">
   $(document).ready(function(){
    // Log the stored date to the console to ensure it's being passed correctly
    console.log("Stored Date from PHP: ", "<?= $storedDate ?>");
    console.log("Field from PHP: ", "<?= $field['columnname'] ?>");

    // Initialize Flatpickr with the desired format
    var dateInput = $('#<?= $classarray["id"]?>');
    
    flatpickr(dateInput[0], {
        dateFormat: "d-m-Y",  // Display format as dd-mm-YYYY
        // Properly check and handle empty storedDate
        defaultDate: "<?= !empty($storedDate) ? $storedDate : 'null' ?>",  // Set default date from PHP (it should be in YYYY-MM-DD format)
        allowInput: false,  // Prevent typing in the input field
        clickOpens: false,  // Prevent the calendar from opening on input click

        onChange: function(selectedDates, dateStr, instance) {
            const mysqlFormattedDate = instance.formatDate(selectedDates[0], "Y-m-d");
            console.log("Formatted MySQL Date: ", mysqlFormattedDate);  // Send this to the server in MySQL-compatible format
        }
    });

    // Check if the stored date is valid before creating a Date object
    if ("<?= $storedDate ?>" !== "") {
        var date = new Date("<?= $storedDate ?>");
        console.log("Date object created from PHP date:", date);  // Log the parsed date

        // Format the date as DD-MM-YYYY
        var formattedDate = ("0" + date.getDate()).slice(-2) + "-" + ("0" + (date.getMonth() + 1)).slice(-2) + "-" + date.getFullYear();
        console.log("Formatted Date (DD-MM-YYYY):", formattedDate);  // Log the manually formatted date
        
        // Set the input value to the correctly formatted date (DD-MM-YYYY)
        dateInput.val(formattedDate);
    } else {
        // If no date is provided, make the input field empty
        dateInput.val('');
    }
     // Make the input field read-only (prevent typing)
     dateInput.prop('readonly', true);
        // Apply background color for readonly state
        dateInput.addClass('readonly-bg'); // Adding the readonly background class
});

</script>
    <?php

}

else {
  // Check if the field value is set and not null, fallback to empty string if not set
  if(isset($Record->{$field["columnname"]}))
  $storedDate = isset($Record->{$field["columnname"]}) ? ($Record->{$field["columnname"]} == '0000-00-00' || $Record->{$field["columnname"]} == '0000-00-00 00:00:00')?"":$Record->{$field["columnname"]} : "";
else if(isset($MRecord->{$field["columnname"]}))
  $storedDate = isset($MRecord->{$field["columnname"]}) ? ($MRecord->{$field["columnname"]} == '0000-00-00' || $MRecord->{$field["columnname"]} == '0000-00-00 00:00:00')?"":$MRecord->{$field["columnname"]} : "";
else $storedDate='';
// echo $storedDate;
?>

  <script nonce="<?= Yii::$app->params['cspNonce'] ?>">
   $(document).ready(function(){
    // Log the stored date to the console to ensure it's being passed correctly
    console.log("Stored Date from PHP: ", "<?= $storedDate ?>");
    console.log("Field from PHP: ", "<?= $field['columnname'] ?>");

    // Initialize Flatpickr with the desired format
    var dateInput = $('#<?= $classarray["id"]?>');
    
    flatpickr(dateInput[0], {
        dateFormat: "d-m-Y",  // Display format as dd-mm-YYYY
        // Properly check and handle empty storedDate
        defaultDate: "<?= !empty($storedDate) ? $storedDate : 'null' ?>",  // Set default date from PHP (it should be in YYYY-MM-DD format)

        onChange: function(selectedDates, dateStr, instance) {
            const mysqlFormattedDate = instance.formatDate(selectedDates[0], "Y-m-d");
            console.log("Formatted MySQL Date: ", mysqlFormattedDate);  // Send this to the server in MySQL-compatible format
        }
    });

    // Check if the stored date is valid before creating a Date object
    if ("<?= $storedDate ?>" !== "") {
        var date = new Date("<?= $storedDate ?>");
        console.log("Date object created from PHP date:", date);  // Log the parsed date

        // Format the date as DD-MM-YYYY
        var formattedDate = ("0" + date.getDate()).slice(-2) + "-" + ("0" + (date.getMonth() + 1)).slice(-2) + "-" + date.getFullYear();
        console.log("Formatted Date (DD-MM-YYYY):", formattedDate);  // Log the manually formatted date
        
        // Set the input value to the correctly formatted date (DD-MM-YYYY)
        dateInput.val(formattedDate);
    } else {
        // If no date is provided, make the input field empty
        dateInput.val('');
    }
});

</script>
<?php
}

?>




