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
if ($classarray['readonly'] != 1) 
$classarray['class']=$classarray['class']." dttnoread";
// echo $Record->{$field["columnname"]};
// if (isset($Record->{$field["columnname"]})) {
//     $datetime = $Record->{$field["columnname"]};
//     $date = new DateTime($datetime);
//     $formattedDate = $date->format('Y-m-d H:i');
//     $Record->{$field["columnname"]} = $formattedDate;
// }
// print_r($classarray);

if (isset($cnt_rows))
    echo Html::input($field["fieldtype"], $field["tablename"] . '[' . $cnt_rows . ']' . '[' . $field["fieldname"] . ']', isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : "", $classarray);
else
    echo Html::input($field["fieldtype"], $field["tablename"] . '[' . $field["fieldname"] . ']', isset($Record->{$field["columnname"]}) ? $Record->{$field["columnname"]} : "", $classarray);

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
// $js = <<<JS
//  $(document).ready(function (){
//   flatpickr('#{$field["fieldname"]}', {
//     enableTime: true,
//     dateFormat: "Y-m-d H:i",
//     // defaultDate: new Date()
// });


// });

// JS;

// $this->registerJs($js);
?>

<?php
if ($classarray['readonly'] == 1) {
    // $storedDate = isset($Record->{$field["columnname"]}) ? ($Record->{$field["columnname"]} == '0000-00-00 00:00:00')?"":$Record->{$field["columnname"]} : "";
    // Check if the field value is set and not null, fallback to empty string if not set
    if (isset($Record->{$field["columnname"]}))
        $storedDate = isset($Record->{$field["columnname"]}) ? ($Record->{$field["columnname"]} == '0000-00-00 00:00:00') ? "" : $Record->{$field["columnname"]} : "";
    else if (isset($MRecord->{$field["columnname"]}))
        $storedDate = isset($MRecord->{$field["columnname"]}) ? ($MRecord->{$field["columnname"]} == '0000-00-00 00:00:00') ? "" : $MRecord->{$field["columnname"]} : "";
else $storedDate='';


    $js = <<<JS
    $(document).ready(function (){ 
        console.log("Stored Date from PHP: ", "{$storedDate}");
    
    var dateInput = $('#{$classarray["id"]}');
    // Remove readonly if present
    dateInput.removeAttr('readonly');
    
    // Convert to ISO 8601 format (YYYY-MM-DDTHH:MM:SS)
    var isoDate = "{$storedDate}".trim().replace(' ', 'T');
    if (isoDate && isoDate.length === 16) {
        isoDate += ":00";  // Add seconds if missing
    }

    console.log("ISO Date for flatpickr: ", isoDate);
    
    flatpickr(dateInput[0], {
        enableTime: true,
        time_24hr: true,
        dateFormat: "Y-m-d H:i",          // Internal date format
        altInput: true,
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



});
JS;


    $this->registerJs($js);
} else {

    // Check if the field value is set and not null, fallback to empty string if not set
    if (isset($Record->{$field["columnname"]})) {
        $storedDate = ($Record->{$field["columnname"]} == '0000-00-00 00:00:00') ? "" : $Record->{$field["columnname"]};
    } elseif (isset($MRecord->{$field["columnname"]})) {
        $storedDate = ($MRecord->{$field["columnname"]} == '0000-00-00 00:00:00') ? "" : $MRecord->{$field["columnname"]};
    } else {
        $storedDate = "";
    }

    $js = <<<JS
    $(document).ready(function () {
        console.log("Stored Date from PHP: ", "{$storedDate}");
    
        var dateInput = $('#{$classarray["id"]}');
        // Remove readonly if present
    dateInput.removeAttr('readonly');
        
        // Convert to ISO 8601 format (YYYY-MM-DDTHH:MM:SS)
        var isoDate = "{$storedDate}".trim().replace(' ', 'T');
        if (isoDate && isoDate.length === 16) {
            isoDate += ":00";  // Add seconds if missing
        }
         var minDateOption = null;
        var isMeetingUrl  = window.location.href.indexOf('meeting') !== -1;
        var isFromOrTo    = dateInput.is('#from_location, #to_location');

        if (isMeetingUrl && isFromOrTo) {
            minDateOption = "today";
        }
        console.log("ISO Date for flatpickr: ", isoDate);
        
        flatpickr(dateInput[0], {
            enableTime: true,
            time_24hr: true,
            dateFormat: "Y-m-d H:i",          // Internal date format
            altInput: true,
            altFormat: "d-m-Y H:i",           // Display format for users
            defaultDate: isoDate || null,
            minDate: minDateOption,  // For when need to restrict the user from selecting back date (Vishwas)
            
                
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

        
        // Find the nearest hidden input to the input with id 'to' and remove the readonly attribute
        // alert($('#{$classarray["id"]}').next('input[class^=\"DTT\"]').html());
        // Find all readonly input elements with class starting with 'DTT' and remove background color
    //     setTimeout(function() {
            
    //         try{
    //     $('input[class^="DTT"]').each(function() {
    //         alert("Found input with readonly and class DTT: ", this);
    //         $(this).css('background-color', 'transparent !important');
    //     });
    // }catch(e)
    // {
    //     console.error("error occured: ",e);
        
    // }
    
    
    // }, 500);  // delay to ensure dynamic elements are loaded

    });
    JS;

    $this->registerJs($js, \yii\web\View::POS_END);




}
?>