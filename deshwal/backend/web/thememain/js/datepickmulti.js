var scriptsrc = document.currentScript.src;
var url = new URL(scriptsrc);
var storedDate = url.searchParams.get("storedDate");
var readonly = url.searchParams.get("readonly");
var id = url.searchParams.get("id");

if(readonly == 1)
{
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

}else{
    

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

}
