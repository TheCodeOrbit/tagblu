// var validator = new Validator();
// let isValid = true;  // Start with assuming all fields are valid
//       // //   alert("Sdf");
//     // Validate all visible fields
//       $(".form-control, .singleselect .multySelect").each(function () {
//           // For regular inputs and file types, validate directly
//           if ($(this).is(":visible") || $(this).hasClass("leave")) {
//               // Validate field and set isValid to false if any field is invalid
//               if (!validator.validateField($(this))) {
//                   isValid = false;
//                   // alert("Field validation failed: " + $(this).attr('name'));
//               }
//           }
  
//           // For Select2, use the 'select2:select' event instead
//           if ($(this).hasClass("singleselect")) {
//               $(this).on("select2:select", function () {
//                   if (!validator.validateField($(this))) {
//                       isValid = false;
//                       // alert("Select2 validation failed: " + $(this).attr('name'));
//                   }
//               });
//           }
  
//           // For multi-select, use the 'select2:select' event instead
//           if ($(this).hasClass("multySelect")) {
//               $(this).on("select2:select", function () {
//                   if (!validator.validateField($(this))) {
//                       isValid = false;
//                       // alert("Multi-Select validation failed: " + $(this).attr('name'));
//                   }
//               });
//           }
//       });
  