$(document).ready(function () {
  var newURL = window.location.href;
  var module = "segregation";
  var str = newURL.split(module);
  var action = str[1].split("/")[1].split("?")[0];
  // var slicestr=newURL.substring(0,str);
  editusrl = str[0] + "segregation/list";
  console.log("action" + action);
  const urlParams = new URLSearchParams(window.location.search);
  const itemid = urlParams.get('itemid'); // "GRN0001"
  // startLoading();
  // if(action != "edit")
  // $('.savebutton').prop('disabled', true);
  
});