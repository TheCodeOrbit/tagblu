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

<script type="text/javascript" src="<? $baseUrl; ?>thememain/js/flatpickr.js"></script>

<?php
echo Html::input($field["fieldtype"], $field["tablename"] . '[' . $field["fieldname"] . ']', isset($MRecord->{$field["columnname"]}) ? $MRecord->{$field["columnname"]} : "", $classarray);
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
if($classarray['readonly'] == 1)
{
    $storedDate = isset($MRecord->{$field["columnname"]}) ? ($MRecord->{$field["columnname"]} == '0000-00-00 00:00:00') ? "" : $MRecord->{$field["columnname"]} : "";

    ?>
    <script type="text/javascript">
    $(document).ready(function (){
        // Log the stored date to the console to ensure it's being passed correctly
        console.log('Stored Date from PHP: ', '<?= $storedDate ?>');

        // Initialize Flatpickr with the desired format
        var dateInput = $('#<?= $classarray["id"]?>');
        

        // Convert to ISO 8601 format (YYYY-MM-DDTHH:MM:SS)
        var isoDate = "<?= $storedDate?>".replace(' ', 'T');
        if (isoDate && isoDate.length === 16) {
            isoDate += ":00";  // Add seconds if missing
        }
        console.log("ISO Date for flatpickr: ", isoDate);

        
        flatpickr(dateInput[0], {
            enableTime: true,
            time_24hr: true,
            dateFormat: "Y-m-d H:i",          // Internal date format
            allowInput: false,  // Prevent typing in the input field
            clickOpens: false,  // Prevent the calendar from opening on input click
            altFormat: "d-m-Y H:i",           // Display format for users
            defaultDate: isoDate || null,
                
            onReady: function(selectedDates, dateStr, instance) {
                console.log("Flatpickr is ready, default date set: ", dateStr);

                if (!dateStr && isoDate) {
                    dateInput.val(instance.formatDate(new Date(isoDate), "d-m-Y H:i"));
                    console.log("Manually setting date to input field: ", isoDate);
                }
            },

            onChange: function(selectedDates, dateStr, instance) {
                const mysqlFormattedDate = instance.formatDate(selectedDates[0], "Y-m-d H:i");
                console.log("Formatted MySQL Date: ", mysqlFormattedDate);
            }
        });

        // Make the input field read-only (prevent typing)
        dateInput.prop('readonly', true);
        // Apply background color for readonly state
        dateInput.addClass('readonly-bg'); // Adding the readonly background class
    });
    </script>
    <?php
}
else{
    $storedDate = isset($MRecord->{$field["columnname"]}) ? 
        ($MRecord->{$field["columnname"]} == '0000-00-00 00:00:00') ? "" : $MRecord->{$field["columnname"]} : "";

    ?>
    <script type="text/javascript">
        alert('cxz');
    $(document).ready(function (){
        console.log('Stored Date from PHP: ', '<?= $storedDate ?>');
    
        var dateInput = $('#<?= $classarray["id"]?>');
        
        // Ensure the PHP date is formatted correctly, e.g., '2025-05-20T17:00:00'
        var isoDate = '<?= $storedDate ?>'.trim().replace(' ', 'T');  // Replace space with 'T' for ISO 8601 format
        console.log('ISO Date for flatpickr: ', isoDate);  // Check if the date format is correct
        
        flatpickr(dateInput[0], {
            enableTime: true,
            dateFormat: 'd-m-Y H:i',  // Display format (day-month-year hour:minute:second)
            defaultDate: isoDate === '' ? null : isoDate,  // Set default date from PHP (make sure it has both date and time)
    
            onReady: function(selectedDates, dateStr, instance) {
                // Log to ensure that the default date is properly set
                console.log('Flatpickr is ready, default date set: ', dateStr);
    
                // Manually set the input field value if defaultDate didn't set it correctly
                if (!dateStr) {
                    dateInput.val(isoDate);  // Manually set the value in the input field
                    console.log('Manually setting date to input field: ', isoDate);
                }
            },
    
            onChange: function(selectedDates, dateStr, instance) {
                const mysqlFormattedDate = instance.formatDate(selectedDates[0], 'Y-m-d H:i');
                console.log('Formatted MySQL Date: ', mysqlFormattedDate);  // Log the formatted date when it changes
            }
        });
    
    });
    </script>
    <?php
}
?>
