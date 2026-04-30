 $(document).ready(function (){

flatpickr('.timepicker', {
    enableTime: true,
    noCalendar: true,
    dateFormat: "H:i",
    time_24hr: true,  
    // defaultDate: new Date()
});
 });