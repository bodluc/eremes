<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
if(isset($_SESSION['debug'])) { $debug = $_SESSION['debug']; }
if(isset($_REQUEST['debug'])) { $debug = $_REQUEST['debug']; }
if(empty($debug)) { $debug = 0; }
?>
function debugToConsole(NewBlockDebugMessage) {
	<?php if(!empty($debug)) { ?>
	$("#DebugConsole").append("<div class='DebugBlockHead'>" + NewBlockDebugMessage + "</div>");
	$(".debug:not('#DebugConsole .debug')").each(function() {
		$("#DebugConsole").append("<div style='width: 100%;' class='debug ui-draggable'>" + $(this).html() + "</div>");
		$(this).remove();
	});
	<?php } ?>
}

function resizeDebugConsole() {
	$("#DebugConsole").css('height', ($(window).height() - $("#DebugConsole .dragline").offset().top - 7));
}

$(document).ready(function() {
	$("#DebugConsole .dragline").draggable({
		axis: 'y',
		stop: function() {
			resizeDebugConsole();
		}
	});
	
	debugToConsole("Core JS loaded.");
	
<?php if(!empty($conf)) { ?>
	// $("#notifications_and_warnings").show();
	if($("#notifications_and_warnings").children(":not(.close_warning)").length >= 1) {
		$("#notifications_and_warnings .close_warning").show();
		try {
			fixAfterWindowResize();
		} catch(err) {
			
		}
	}
<?php } ?>
	
	$(".close_warning").live("click", function(event) {
		$(this).closest("#notifications_and_warnings").hide();
	});
});