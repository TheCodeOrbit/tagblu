$(document).ready(function() {
    var $btn = $(".add-more-records");
    if ($btn.length) {
        $btn.prop("disabled", true);
    }
    $('th.col-80').hide();
    $('.remove-row-btn').hide();
});