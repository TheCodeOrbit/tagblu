$(document).ready(function () {
    // Collapse all accordions
    $(".accordion-collapse").removeClass("show");

    // Open only the first accordion
    $(".accordion-collapse").first().addClass("show");
    
    $('.singleselect').select2({
        placeholder: "Select",
        allowClear: true,
        width: '100%' // Ensures it spans the full width like Bootstrap form controls
    });

    const validator = new Validator();
    console.log("deep" + validator);

    var form = document.getElementById("pristine-valid-example");

    // Create a MutationObserver to detect changes to input fields with class "ref-form-control"
    // var targetNode = document.querySelectorAll('.ref-form-control');
    // console.log(targetNode);

    // targetNode.forEach(function (inputField) {
    //     var observer = new MutationObserver(function (mutationsList) {
    //         for (var mutation of mutationsList) {
    //             if (
    //                 mutation.type === "attributes" && // We are interested in attribute changes
    //                 mutation.attributeName === "value" // Only the 'value' attribute change
    //             ) {
    //                 // Call validation when the value changes
    //                 console.log("testing deepika");
    //                 validateField($(mutation.target)); // Call validation on the modified element
    //             }
    //         }
    //     });

    //     // Configuration for the observer (observe attribute changes)
    //     var config = { attributes: true };

    //     // Observe the target node for changes in attributes
    //     observer.observe(inputField, config);
    // });

    // Validate on change for all inputs and select2 dropdowns
    $(".form-control, input[type='radio'], input[type='file'], input[type='checkbox'], .leave, .singleselect .multySelect").on("change", function () {
        // For regular inputs and file types, validate directly
        if ($(this).is(":visible") || $(this).hasClass("leave")) {
            validator.validateField($(this));
        }

        // For Select2, use the 'select2:select' event instead
        if ($(this).hasClass("singleselect")) {
            $(this).on("select2:select", function () {
                validator.validateField($(this));
            });
        }
        // For Select2, use the 'select2:select' event instead
        if ($(this).hasClass("multySelect")) {
            $(this).on("select2:select", function () {
                validator.validateField($(this));
            });
        }
    });

    $(".savebutton").on("click", function (e) {
        e.preventDefault(); // Prevent default form submission
         
        let isValid = true;
        const form = $(this).closest("form");
        let firstInvalidField = null;
        const $savebtn = $(this);      
        $savebtn.prop("disabled", true);
    
        // Step 1: Expand any collapsed accordions that have mandatory fields
        $(".accordion-collapse").each(function () {
            const $collapse = $(this);
            const hasMandatory = $collapse.find("[class*='~M']").length > 0;
    
            if (hasMandatory && !$collapse.hasClass("show")) {
                new bootstrap.Collapse($collapse[0], { toggle: true });
            }
        });
        const srcTabId = Number($('.srctabid').val());
        const allowedTabs = [78, 72, 74];
        if ($.inArray(srcTabId, allowedTabs) !== -1) {

            if ($(".product_name").length > 0) {
                $(".product_name").each(function () {
                    if ($(this).val().trim() === "") {
                    isValid = false;
                    $(this).addClass("error");
                    } else {
                    $(this).removeClass("error");
                    }
                });
                } else {
                isValid = false;
                }
                if (!isValid) {
                    showCustomToast('Validation Error', "Product row can't be empty for this type.", 'error');
                    $savebtn.prop("disabled", false);
                    if (firstInvalidField) firstInvalidField.focus();
                    return;                    
                }
        }
        // Step 2: Validate after delay to ensure accordions are expanded
        setTimeout(function () {
            $(".form-control:visible, input[type='radio']:visible, input[type='file']:visible, input[type='checkbox']:visible, .leave:visible, .singleselect:visible, .multySelect:visible").each(function () {
                const $field = $(this);
    
                if (!validator.validateField($field)) {
                    isValid = false;
                    $savebtn.attr("disabled",false);
                    if (!firstInvalidField) {
                        firstInvalidField = $field;
                    }
                }
            });
    
            if (isValid) {
                 var msg = 'Do you want to save the record?';

                $savebtn.prop("disabled", true);  

                showConfirm(msg).then(function (ok) {
                    if (!ok) {
                        $savebtn.prop("disabled", false);
                        return;
                    }
                    form.submit();
                });
            } else {
                if (firstInvalidField) {
                    $('html, body').animate({
                        scrollTop: firstInvalidField.offset().top - 100
                    }, 500);
                    firstInvalidField.focus();                    
                    $savebtn.attr("disabled",false);
                }
            }
        }, 300); // Delay for accordion animation
    });
    function showConfirm(msg) {
        // Delegate to the global premium custom confirm dialog
        if (typeof window.showCustomConfirm === 'function') {
            return window.showCustomConfirm('Confirm Save', msg, 'Yes, Save', 'Cancel', 'primary');
        }
        // Fallback if custom-alerts.js hasn't loaded
        return Promise.resolve(confirm(msg));
    }

    // Validate Select2 fields on change — bind this once, outside the click handler
    $(".singleselect, .multySelect").on("select2:select", function () {
        validator.validateField($(this));
    });
    function updateSaveButtonForProducts() {
        const srcTabId  = Number($('.srctabid').val());
        const validTabs = [78, 72, 74];
        if (Number.isNaN(srcTabId) || !validTabs.includes(srcTabId)) {
            return; 
        }
        let hasAnyProduct = false;
        $(".product_name").each(function () {
            if ($(this).val().trim() !== "") {
                hasAnyProduct = true;
                return false;
            }
        });
        $(".savebutton").prop("disabled", !hasAnyProduct);  // universal toggle
    }
    $(document).ajaxComplete(function () {
        updateSaveButtonForProducts();
    });

});