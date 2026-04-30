$(document).ready(function(){
	 var newURL = window.location.href;
	 var newURL = window.location.href;
   var module = "leads";
  var str=newURL.split(module);
  console.log("str"+str[0]);
 // var slicestr=newURL.substring(0,str);
  editusrl = str[0]+"leads/list";
	 console.log("url"+editusrl);

$(document).on('click', '#approvesubmit', function() {
        
        data = {  Recordid: $('#Recordid').val(), _csrf: $('#csrfToken').val(),leadstatus_v:$("#leadstatus_v").val(),approve_reason:$("#approve_comment").val()};
        // {leadstatus_v:$("#leadstatus_v").val(),Recordid: $('#Recordid').val();,approve_reason:$("#approve_reason").val();, _csrf: $('#csrfToken').val();};
        $.ajax({
            type: 'POST',
            url:"approvelead",
            // async:false,
            data:data,
            success:function(data){
			window.location.href = editusrl;
                
            },
             error: function(data) { // if error occured
                          
               alert('Error occured.please try again');
            }, 
          dataType:'html'
        });
         
    });
$(document).on('click', '#delegatesubmit', function() {

        // alert("dfhfdhd");
        data = {  Recordid: $('#Recordid').val(), _csrf: $('#csrfToken').val(),new_vm:$("#new_vm").val(),delegate_reason:$("#delegate_comment").val()};
        // {leadstatus_v:$("#leadstatus_v").val(),Recordid: $('#Recordid').val();,approve_reason:$("#approve_reason").val();, _csrf: $('#csrfToken').val();};
        $.ajax({
            type: 'POST',
            url:"approvelead",
            // async:false,
            data:data,
            success:function(data){
                //  $('#modalreference').modal('show')
                // .find('.modal-content')
                // .html(data);
                // Redirect to a new page
			window.location.href = editusrl;
			

            },
             error: function(data) { // if error occured
                          
               alert('Error occured.please try again');
            }, 
          dataType:'html'
        });
          // alert("dfhfdhd");
    });

$(document).on('click', '#modifysubmit', function() {

        // alert("dfhfdhd");
        data = {  Recordid: $('#Recordid').val(), _csrf: $('#csrfToken').val(),leadstatus_m:$("#leadstatus_m").val(),modify_reason:$("#modify_comment").val()};
        // {leadstatus_v:$("#leadstatus_v").val(),Recordid: $('#Recordid').val();,approve_reason:$("#approve_reason").val();, _csrf: $('#csrfToken').val();};
        $.ajax({
            type: 'POST',
            url:"approvelead",
            // async:false,
            data:data,
            success:function(data){
                //  $('#modalreference').modal('show')
                // .find('.modal-content')
                // .html(data);
                // Redirect to a new page
			window.location.href = editusrl;
			

            },
             error: function(data) { // if error occured
                          
               alert('Error occured.please try again');
            }, 
          dataType:'html'
        });
          // alert("dfhfdhd");
    });
$(document).on('click', '#rejectsubmit', function() {

        // alert("dfhfdhd");
        data = {  Recordid: $('#Recordid').val(), _csrf: $('#csrfToken').val(),leadstatus_v:$("#leadstatus_r").val(),reject_reason:$("#reject_comment").val()};
        // {leadstatus_v:$("#leadstatus_v").val(),Recordid: $('#Recordid').val();,approve_reason:$("#approve_reason").val();, _csrf: $('#csrfToken').val();};
        $.ajax({
            type: 'POST',
            url:"approvelead",
            // async:false,
            data:data,
            success:function(data){
			window.location.href = editusrl;
			
                
            },
             error: function(data) { // if error occured
                          
               alert('Error occured.please try again');
            }, 
          dataType:'html'
        });
          // alert("dfhfdhd");
    });
	let today = new Date().toISOString().split("T")[0];
	$('#contact_future_date').attr('min', today);//only ffuture date
	// alert($(".section-not_contacted_reasons").html());
	$(".section-not_contacted_reasons").addClass("tr-hidden");
	$(".section-disqualified_reason").addClass("tr-hidden");
	$(".section-not_interested_reason").addClass("tr-hidden");
	$(".section-contact_future_date").addClass("tr-hidden");

	$("#leadstatus").change(function(){
		lead_status = $("#leadstatus option:selected").text();
		//alert(lead_status);		
		if(lead_status =="Contact in Future")
		{
			$(".section-contact_future_date").removeClass("tr-hidden");
			$(".section-not_contacted_reasons").addClass("tr-hidden");
			$(".section-disqualified_reason").addClass("tr-hidden");
			$(".section-not_interested_reason").addClass("tr-hidden");
			$("#contact_future_date").val('');
			$("#not_contacted_reasons").val('');
			$("#disqualified_reason").val('');
			$("#not_interested_reason").val('');
		}
		else if(lead_status =="Disqualified")
		{
			$(".section-disqualified_reason").removeClass("tr-hidden");
			$(".section-not_contacted_reasons").addClass("tr-hidden");	
			$(".section-not_interested_reason").addClass("tr-hidden");
			$(".section-contact_future_date").addClass("tr-hidden");
			$("#contact_future_date").val('');
			$("#not_contacted_reasons").val('');
			$("#disqualified_reason").val('');
			$("#not_interested_reason").val('');

		}
		else if(lead_status =="Not Interested")
		{
			$(".section-not_interested_reason").removeClass("tr-hidden");
			$(".section-not_contacted_reasons").addClass("tr-hidden");
			$(".section-disqualified_reason").addClass("tr-hidden");	
			$(".section-contact_future_date").addClass("tr-hidden");
			$("#contact_future_date").val('');
			$("#not_contacted_reasons").val('');
			$("#disqualified_reason").val('');
			$("#not_interested_reason").val('');

		}
		else if(lead_status =="Not Contacted")
		{
			$(".section-not_contacted_reasons").removeClass("tr-hidden");
			$(".section-disqualified_reason").addClass("tr-hidden");
			$(".section-not_interested_reason").addClass("tr-hidden");
			$(".section-contact_future_date").addClass("tr-hidden");
			$("#contact_future_date").val('');
			$("#not_contacted_reasons").val('');
			$("#disqualified_reason").val('');
			$("#not_interested_reason").val('');

		}
		else{
			$(".section-not_contacted_reasons").addClass("tr-hidden");
			$(".section-disqualified_reason").addClass("tr-hidden");
			$(".section-not_interested_reason").addClass("tr-hidden");
			$(".section-contact_future_date").addClass("tr-hidden");
			$("#contact_future_date").val('');
			$("#not_contacted_reasons").val('');
			$("#disqualified_reason").val('');
			$("#not_interested_reason").val('');
		}

	});
$(document).on("click",".leaddurationparent",function()
{
	leadstatusid =	$(this).data("id");
	boxtype = $(this).attr("data-bt");
	cl = $(this).attr("data-cl");
	$(".leaddurationbox").addClass("tr-hidden");
	$(".leaddescbox").addClass("tr-hidden");
	$(".leadduration"+leadstatusid).removeClass("tr-hidden");
	$(".leaddesc"+leadstatusid).removeClass("tr-hidden");
	$(".leaddurationparent").each(function(){
	const pattern = /blue/; // Matches any class containing "blue"
    const classAttr = $(this).attr("class") || "";
    const classes = classAttr.split(" ");
    // console.log("All classes:", classes);

    const filteredClasses = classes.filter(className => pattern.test(className));
    // console.log("Filtered classes:", filteredClasses);

    if (filteredClasses.length > 0) {
        $(this).removeClass(filteredClasses.join(" "));
        // console.log("Classes removed:", filteredClasses.join(" "));
    }

		cl = $(this).attr("data-cl");
		$(this).addClass(cl);
	});
	$(this).removeClass(cl);
	$(this).addClass("rectangle-"+boxtype+"-blue");
	
	
 	//alert("leadduration"+leadstatusid);
}
);

$("#phone").change(function(){
	 // Phone number validation
                var phoneRegex = /^[6-9]\d{9}$/;///^\+?(\d{1,4})?[-.\s]?(\d{10})$/;
                var phone = $('#phone').val();
                if(phone !="")
                {
                	if (!phoneRegex.test(phone)) {
                    $('#phone').next('.help-block').text('Please enter a valid Phone no..').css('color', 'red');
                    
                    console.log("invalid phone");
                	console.log($(this).closest('.help-block').html());

                    isValid = false;
	                } else {
	                    
	                   $('#phone').next('.help-block').text("")
	                    
	                }

                }
});
$("#email").change(function(){
	// Email validation
                var emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
                var email = $('#email').val();
                if(email !='')
                {
                	if (!emailRegex.test(email)) {
                    // $('#emailError').show();
                    isValid = false;
                    console.log("invalid email");
                    $('#email').next('.help-block').text('Please enter a valid email address.').css('color', 'red');
                   
                	console.log($(this).closest('.help-block').html());


	                } else {
	                    
	                   $('#email').next('.help-block').text("")
	                }

                }
});
	 var form = document.getElementById("pristine-valid-example");
  var pristine = new Pristine(form);
$(document).on('click', '.savebutton', function(e) {

  // $('.savebutton').click(function(e){
  				console.log("clicked");

				var isValid = true;
  				console.log("teregdfg fh");

               
                
                
		var valid = pristine.validate();
		if(valid && isValid){
      form.submit();
    }
   
    
});

});

 document.querySelectorAll(".accordion-toggle").forEach(button => {
    button.addEventListener("click", () => {
      const content = button.closest(".accordion-item").querySelector(".accordion-content");
      const upArrow = button.querySelector(".up");
      const downArrow = button.querySelector(".down");
      if (content.style.display === "block") {
        content.style.display = "none"; // Hide content
        upArrow.style.display = "none"; // Hide up arrow
        downArrow.style.display = "inline"; // Show down arrow
      } else {
        content.style.display = "block"; // Show content
        upArrow.style.display = "inline"; // Show up arrow
        downArrow.style.display = "none"; // Hide down arrow
      }
    });
  });
  // Tab Switching Logic
  document.querySelectorAll(".tab").forEach(tab => {
    tab.addEventListener("click", function() {
      // Remove active class from all tabs and contents
      document.querySelectorAll(".tab").forEach(t => t.classList.remove("active"));
      document.querySelectorAll(".tab-content-detail-view").forEach(content => content.classList.remove("active"));
      // Add active class to clicked tab and corresponding content
      this.classList.add("active");
      const tabId = this.getAttribute("data-tab");
      document.getElementById(tabId).classList.add("active");
    });
  });