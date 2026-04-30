$(document).ready(function() {
    $('.multySelect').select2({
        placeholder: "Select",
        allowClear: true,
        width: '100%' // Ensures it spans the full width like Bootstrap form controls
    });
    // $('.multySelect').select2({
    //     placeholder: 'Select',
    //     width: '100%' // Ensures it spans the full width like Bootstrap form controls
    // }).next('.select2-container').addClass('form-control');
});