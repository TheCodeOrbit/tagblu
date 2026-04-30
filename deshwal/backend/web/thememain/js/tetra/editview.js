
  
$(document).ready(function () {
   
  $(document).on("click", ".mod-close", function () {
    $("#add-lead-modal").modal("hide");
  });
  
    // Toggle the 'active' class on the toggle switch when clicked

    $(document).off('click', '.toggle-switch').on('click', '.toggle-switch', function () {
        event.stopPropagation(); // Prevent bubbling
        console.log("deep ut");
      

      console.log("Clicked element:", this);
  console.log("Element classes before toggle:", $(this).attr('class'));
  $(this).toggleClass('active');
  console.log("Element classes after toggle:", $(this).attr('class'));
  
      toggleRequiredFields2();
    });

    // Function to toggle the visibility of required fields
    // function toggleRequiredFields() {
    //   alert("toggle");
    //   // console.log("toggle");
    //   const isChecked = $('.toggle-switch').hasClass('active');
    //   const requiredFields = $('.not-required-field');

    //   // Show or hide fields based on the toggle state
    //   requiredFields.each(function () {
    //     $(this).css('display', isChecked ? 'none' : 'block');
    //     //alert($(this).isChecked);
    //   });


      
    // }


    function toggleRequiredFields2() {
        const isChecked = $('#toggle-switch2').hasClass('active');
        console.log("Is Checked:", isChecked);
      
        const requiredFields = $('.not-required-field');
        console.log("Fields to toggle:", requiredFields);
      
        requiredFields.each(function () {
          if (isChecked) {
            $(this).hide(); // Hide the element
          } else {
            $(this).show(); // Show the element
          }
        });
      }
  });