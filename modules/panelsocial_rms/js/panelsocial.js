
   function panelfb_show() {
       $('#panelsocial').stop();
        $('#panelsocial').animate({left:3},500,'easeOutBounce');
   }
 function panelfb_hide() {
     $('#panelsocial').stop();
        $('#panelsocial').animate({left:-256},700,'easeOutBounce');
   } 
 function showDiv() {
     $('#ceneo-widget').stop();
     $('#ceneo-widget').animate({right:20},150);
 }
 function hideDiv() {
     $('#ceneo-widget').stop();
     $('#ceneo-widget').animate({right:-252},0,'linear');
 }
$(document).ready(function() {
$('#panelsocial').mouseenter(function() {panelfb_show()});
$('#ceneo-widget').mouseenter(function() {showDiv()});
$('#panelsocial').mouseleave(function() {panelfb_hide()});
$('#ceneo-widget').mouseleave(function() {hideDiv()});
})