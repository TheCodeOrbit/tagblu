$(document).ready(function() {
    $('.singleselect').select2({
        placeholder: "Select",
        allowClear: true,
        width: '100%' // Ensures it spans the full width like Bootstrap form controls
    });
// This targets the first <select> with the class 'mySelect' and sets the first option as selected
// document.querySelector('.singleselect').selectedIndex = 0;


});