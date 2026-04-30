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
                    alert("Product row can't be empty for this type.");
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
        return new Promise(function (resolve) {
            
            var modal = document.createElement('div');
            modal.id = 'confirmModal';
            modal.style.cssText = `
                position: fixed; top: 0; left: 0; right: 0; bottom: 0;
                background: rgba(0,0,0,0.5); z-index: 99999;
                display: flex; align-items: center; justify-content: center;
                font-family: Arial, sans-serif;
            `;
            
            var content = document.createElement('div');
            content.style.cssText = `
                background: white; padding: 24px; border-radius: 8px;
                min-width: 320px; max-width: 400px; box-shadow: 0 4px 20px rgba(0,0,0,0.3);
                text-align: center;
            `;
            
            var text = document.createElement('p');
            text.id = 'confirmText';
            text.textContent = msg;
            text.style.cssText = 'margin: 0 0 20px 0; font-size: 16px; line-height: 1.4;';
            
            var yesBtn = document.createElement('button');
            yesBtn.textContent = 'Yes';
            yesBtn.style.cssText = `
                background: var(--color-primary) !important; color: #fff; border: none; padding: 10px 24px;
                margin-right: 12px; border-radius: 4px; cursor: pointer; font-size: 14px;
                min-width: 70px;
            `;
            
            var noBtn = document.createElement('button');
            noBtn.textContent = 'No';
            noBtn.classList = 'mod-close btn btn-secondary';
            noBtn.style.cssText = `
                background: #ffffff; color: black; border: none; padding: 10px 24px;
                border-radius: 4px; cursor: pointer; font-size: 14px; min-width: 70px;
            `;
            
            content.appendChild(text);
            content.appendChild(yesBtn);
            content.appendChild(noBtn);
            modal.appendChild(content);
            document.body.appendChild(modal);
            
            function cleanup(result) {
                document.body.removeChild(modal);
                resolve(result);
            }
            
            yesBtn.onclick = () => cleanup(true);
            noBtn.onclick = () => cleanup(false);
            
            modal.onclick = (e) => { if (e.target === modal) cleanup(false); };
            
            var escHandler = (e) => { if (e.key === 'Escape') cleanup(false); };
            document.addEventListener('keydown', escHandler);
            
            modal._cleanup = () => {
                document.removeEventListener('keydown', escHandler);
                cleanup(false);
            };
        });
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