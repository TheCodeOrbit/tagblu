// Validator class definition
class Validator {
    constructor() {
        this.errorMessage = "";
    }
    //////////////////// on the class end validation code zitendra /////////////////////////
    validateField($field) {
        // Ensure $field is a jQuery object
        $field = $($field);

        let value = "";
        // Safely handle different field types, including dropdowns
        if ($field.is("select")) {
            value = $field.val() ? String($field.val()).trim() : ""; // Ensure value is a string
        } else if ($field.is(":checkbox")) {
            value = $field.is(":checked") ? "checked" : ""; // Handle checkbox state
        } else if ($field.is(":radio")) {
            const groupName = $field.attr("name");
            const selectedRadio = $(`input[name="${groupName}"]:checked`);
            value = selectedRadio.length ? selectedRadio.val() : ""; // Get value of selected radio button
        } else if ($field.is(":file")) {
            value = $field.val(); // Get the file name
        } else {
            value = $field.val() ? $field.val().trim() : ""; // Handle text inputs and other elements
        }
        var fieldClass = $field.attr("class") || "";
        var isMandatory = fieldClass.includes("~M");
        var maxlength = parseInt($field.attr("maxlength") || 0, 10);
        var isValid = true;
        var errorMessage = "";

        // Skip validation for optional fields if value is empty
        if (!isMandatory && value === "") {
            $field.removeClass("error");
            $field.next(".help-block").text("").css("color", "");
            return true;
        }

        // Get field name (label)
        //var fieldName = $("label[for='" + $field.attr("id") + "']").text().replace('*', '').trim();

        // Validation logic for various field types
        if (fieldClass.includes("CKB~")) {  // Checkbox
            const checkboxGroupName = $field.attr("name");
            const checkedCheckboxes = $(`input[name="${checkboxGroupName}"]:checked`).length;
            
            // Check if it's mandatory and no checkboxes are selected
            if (isMandatory && checkedCheckboxes === 0) {
                errorMessage = "Please select at least one option."; // Make sure the error message is a string
                isValid = false;
            }
        }
        
        else if (fieldClass.includes("R~")) { // Radio button
            var groupName = $field.attr("name");
            var isChecked = $(`input[name="${groupName}"]:checked`).length > 0;
            if (isMandatory && !isChecked) {
                errorMessage = `Please select an option.`;
                isValid = false;
            }
        } else if (fieldClass.includes("A~")) { // Alphabets
            var alphabetRegex = /^[a-zA-Z\s]+$/;
            if (!alphabetRegex.test(value)) {
                errorMessage = `Please enter a valid(alphabets only).`;
                isValid = false;
            }
        } else if (fieldClass.includes("AN~")) { // Alphanumeric
            var alphanumericRegex = /^[a-zA-Z0-9\s]+$/;
            if (!alphanumericRegex.test(value)) {
                errorMessage = `Please enter a valid (alphanumeric only).`;
                isValid = false;
            }
        } else if (fieldClass.includes("NU~")) { // Numeric
            var numericRegex = /^\d+$/;
            if (!numericRegex.test(value)) {
                errorMessage = `Please enter a valid numeric value.`;
                isValid = false;
            }
        } else if (fieldClass.includes("MOB~")) { // Mobile Number
            var mobileRegex = /^[6-9]\d{9}$/;
            if (!mobileRegex.test(value)) {
                errorMessage = `Please enter a valid 10-digit mobile number.`;
                isValid = false;
            }
        } else if (fieldClass.includes("URL~")) { // URL Validation
            var websiteRegex = /^(https?:\/\/)?([\w\-]+\.)+[\w\-]+(\/[\w\-]*)*$/;
            if (value && !websiteRegex.test(value)) {  // Validate only if not empty
                errorMessage = `Please enter a valid URL.`;
                isValid = false;
            }
        } else if (fieldClass.includes("E~")) { // Email
            var emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            if (!emailRegex.test(value)) {
                errorMessage = `Please enter a valid email address.`;
                isValid = false;
            }
        } else if (fieldClass.includes("V~")) { // Textarea Validation
            if (isMandatory && value === "") {
                errorMessage = `This field is mandatory.`;
                isValid = false;
            } else if (maxlength > 0 && value.length > maxlength) {
                errorMessage = `Please enter no more than  characters.`;
                isValid = false;
            }
        } else if (fieldClass.includes("DC~")) { // Decimal
            // var decimalRegex = /^\d+(\.\d+)?$/;
            var decimalRegex = /^-?\d+(\.\d+)?$/;
            if (fieldClass.includes("DC~M") && value === "") {
                errorMessage = ` This field is mandatory.`;
                isValid = false;
            } else if (!decimalRegex.test(value) && value !== "") {
                errorMessage = `Please enter a valid decimal value.`;
                isValid = false;
            }
        } else if (fieldClass.includes("DD~M")) { // Dropdown Mandatory
            if (value === "" || value === "-1") {
                errorMessage = `Please select an option.`;
                isValid = false;
            }
        } else if (fieldClass.includes("DTT~")) { // Date/Time
            // Updated regex to allow only 'YYYY-MM-DD' or 'YYYY-MM-DD HH:MM AM/PM'
            var dateTimeRegex = /^(([0]?[1-9]|1[0-2])\/([0-2]?[0-9]|3[0-1])\/[1-2]\d{3}) (20|21|22|23|[0-1]?\d{1}):([0-5]?\d{1})\s(AM|PM|am|pm)$/;

            if (fieldClass.includes("DTT~M") && value === "") { // Mandatory Date/Time
                errorMessage = `This field is mandatory.`;
                isValid = false;
            }
        } else if (fieldClass.includes("DT~")) { // Date Validation
            // var dateRegex = /^\d{4}-\d{2}-\d{2}$/;old yyyy-mm-dd
            var dateRegex = /^\d{2}-\d{2}-\d{4}$/;//new dd-mm-yyyy
            if (fieldClass.includes("DT~M")) { // Mandatory Date
                if (value === "") {
                    errorMessage = `This field is mandatory.`;
                    isValid = false;
                } else if (!dateRegex.test(value)) {
                    errorMessage = `Please enter a valid date for in the format DD-MM-YYYY.`;
                    isValid = false;
                }
            } else if (fieldClass.includes("DT~O")) { // Optional Date
                if (value !== "" && !dateRegex.test(value)) {
                    errorMessage = `Please enter a valid date for in the format DD-MM-YYYY.`;
                    isValid = false;
                }
            }
        } else if (fieldClass.includes("PS~")) { // Password
            var passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%?&#])[A-Za-z\d@$!%?&#]{8,}$/;
            if (!passwordRegex.test(value)) {
                errorMessage = `Password must be at least 8 characters long, include uppercase, lowercase, number, and special character.`;
                isValid = false;
            }
        } else if (isValid && fieldClass.includes("CPS~")) { // Confirm Password
            var password = $(".PS~M").val(); // Find the password field with mandatory marker
            if (value !== password) {
                errorMessage = `Passwords do not match.`;
                isValid = false;
            }
        }
        else if (fieldClass.includes("IMGF~")) { // File Input Validation
            var fileInput = $field[0];
            var file = fileInput.files[0];
            if (fieldClass.includes("IMGF~M") && !file) {
                errorMessage = `Please upload a file.`;
                isValid = false;
            } else if (file) {
                var allowedTypes = ["image/jpeg", "image/png"];
                if (!allowedTypes.includes(file.type)) {
                    errorMessage = `Please upload a valid file (JPEG, PNG).`;
                    isValid = false;
                } else if (file.size > 5 * 1024 * 1024) {
                    errorMessage = `The file size for should not exceed 5MB.`;
                    isValid = false;
                }
            }
        }
        else if (fieldClass.includes("F~")) { // File Input Validation
            var fileInput = $field[0];
            if (fileInput.files) {
                var file = fileInput.files[0];
                if (fieldClass.includes("F~M") && !file) {
                    errorMessage = `Please upload a file.`;
                    isValid = false;
                } else if (file) {
                    // Allow these MIME types
                    //allowed XLS file code added on date 04-09-2025 by ptpatel as per client request email 
                    //application/vnd.openxmlformats-officedocument.spreadsheetml.sheet for XLSX 
                    //ERP Point 58 .eml allowed
                    var allowedTypes = ["image/jpeg", "image/png", "application/pdf", "application/zip", "application/vnd.ms-excel", "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
                        // For .eml files
                        "message/rfc822",           // standard MIME type for .eml
                        "application/vnd.ms-outlook", // sometimes used for Outlook .msg/.eml
                    ];
                    
                    // Check file type by MIME type first
                    if (!allowedTypes.includes(file.type)) {
                        // Check file extension if MIME type does not match
                        //fileExtension !== "eml" added for ERP Point 58
                        var fileExtension = file.name.split('.').pop().toLowerCase();
                        if (fileExtension != 'eml' && fileExtension != 'zip'  && fileExtension != 'msg' && !allowedTypes.includes(file.type)) {
                            errorMessage = `Please upload a valid file (JPEG, PNG, PDF, ZIP, XLS or XLSX, .EML, .MSG).`;
                            isValid = false;
                        }
                    }
        
                    // Check file size
                    // if (file.size > 5 * 1024 * 1024) {
                    //change as per ERP finding point no 405 as per sheet V2 by ptpatel on date 02-09-2025
                    //5 MB to 200 MB
                    if (file.size > 200 * 1024 * 1024) {
                        errorMessage = `The file size should not exceed 200MB.`;
                        isValid = false;
                    }
                }
            }
        }
        
       

        // Display error message
        // var errorElement = $field.next(".help-block");
        // if (!errorElement.length) {
        //     $field.after('<span class="help-block"></span>');
        //     errorElement = $field.next(".help-block");
        // }
        // Find the closest form group and insert the error message in the .help-block
        var errorElement = $field.closest(".form-group").find(".help-block");
        errorElement.html(errorMessage); // Replace errorMessage with the actual message
       
        var errorElement = $field.closest(".form-group").next(".help-block");
        errorElement.html(errorMessage); // Replace errorMessage with the actual message
       



        if (isValid) {
            errorElement.html('');
            errorElement.text("").css("color", "");
            $field.removeClass("error");
        } else {
            errorElement.text(errorMessage).css("color", "red");
            $field.addClass("error");
        }
        //  alert(isValid);

        return isValid;
    }
    ////////////////////end validation code zitendra /////////////////////////
}

// Make Validator globally accessible
window.Validator = new Validator();
// console.log(window.Validator); // Check if the Validator instance is created