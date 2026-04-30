$(document).ready(function () {
  var newURL = window.location.href;
  var module = jQuery("#module").val();
  var str = newURL.indexOf(module);

  const slicestr = newURL.substring(0, str);
// alert('sdf');

$(document).ready(function(){
  // Event listener for the search box
  $('#search').keyup(function(){
      var searchQuery = $(this).val();
      var sourcingdeal_id = $("#sourcingdeal_id1").val();
      
      // Only proceed if the search box isn't empty
      if (searchQuery.length >= 2) {
          $.ajax({
              url: 'search', // PHP file that processes the search
              method: 'GET',
              data: { query: searchQuery,sourcingdeal:sourcingdeal_id },
              success: function(response) {
                  $('#search-results').html(response);
              }
          });
      } else {
          // Clear results when the search box is empty
          $('#search-results').html('');
      }
  });

  // Handle click event for search results
  $(document).on('click', '.result-item', function() {
      var selectedText = $(this).text(); // Get the text of the clicked item
      var selectedId = $(this).data('id'); // Get the ID from the data attribute

      // Set the text in the search box and store the ID in the hidden input
      $('#contacts_id').val(selectedText);
      $('#contacts_id1').val(selectedId);

      // Clear the search results
      $('#search-results').html('');
  });

  /////////////handle 
  $(document).on('click', '.icon-left-contactrole', function() {
    // alert("jjkbk");
    $('#search').val('');

  });
  
});



});
