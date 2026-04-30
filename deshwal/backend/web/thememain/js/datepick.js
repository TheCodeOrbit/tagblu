var scriptsrc = document.currentScript.src;
var url = new URL(scriptsrc);
var storedDate = url.searchParams.get("storedDate");
var readonly = url.searchParams.get("readonly");
var id = url.searchParams.get("id");

$(document).ready(function () {
    console.log("Stored Date from URL:", storedDate);
    console.log("Read-only flag:", readonly);
    console.log("Field ID:", id);

    var dateInput = $('#' + id);

    // Validate if the input exists
    if (!dateInput.length) {
        console.warn("No input found with id:", id);
        return;
    }

    // Format storedDate to dd-mm-yyyy
    let formattedDate = '';
    if (storedDate && storedDate.trim() !== "") {
        const date = new Date(storedDate);
        if (!isNaN(date)) {
            formattedDate = ("0" + date.getDate()).slice(-2) + "-" +
                            ("0" + (date.getMonth() + 1)).slice(-2) + "-" +
                            date.getFullYear();
            console.log("Formatted Date (DD-MM-YYYY):", formattedDate);
        } else {
            console.warn("Invalid storedDate:", storedDate);
        }
    }

    // ✅ IF: readonly mode
    if (readonly == "1") {
        flatpickr(dateInput[0], {
            dateFormat: "d-m-Y",
            defaultDate: storedDate || null,
            allowInput: false,
            clickOpens: false,
            onChange: function (selectedDates, dateStr, instance) {
                if (selectedDates.length > 0) {
                    const mysqlFormattedDate = instance.formatDate(selectedDates[0], "Y-m-d");
                    console.log("Formatted MySQL Date (readonly):", mysqlFormattedDate);
                }
            }
        });

        // Set value and style
        dateInput.val(formattedDate || '');
        dateInput.prop('readonly', true);
        dateInput.addClass('readonly-bg'); // Apply readonly styles

    } else {
        // ✅ ELSE: editable mode
        console.log("deep else");
        console.log("readonly param value:", readonly, "Type:", typeof readonly);


    flatpickr(dateInput[0], {
        dateFormat: "d-m-Y",
        defaultDate: storedDate || null,
        onChange: function (selectedDates, dateStr, instance) {
            if (selectedDates.length > 0) {
                const mysqlFormattedDate = instance.formatDate(selectedDates[0], "Y-m-d");
                console.log("Formatted MySQL Date (editable):", mysqlFormattedDate);
            }
        }
    });

    dateInput.val(formattedDate || '');
    }
});
