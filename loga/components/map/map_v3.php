<?php
/**
* @desc This file will display a geographic map of the world and plot stats on it* 
*/ 

include("../../common.inc.php");
$xmlstr = str_replace("reports.php", "../../reports.php", makeMapXMLstr($_GET['conf'],$_GET['from'],$_GET['to']));
?>
<style type='text/css'>body { margin: 0; padding: 0; }</style>
<script language="JavaScript" type="text/javascript" src="flash-world-map.js"></script>
<script language="JavaScript" type="text/javascript">
    var flashObjectWidth = '100%';//$(document).find("#mapArea").outerWidth();
    var flashObjectHeight = 400;//(flashObjectWidth / 2);
    if(flashObjectWidth == undefined) {
        var flashObjectWidth = $("#mapArea:first").outerWidth();
        var flashObjectHeight = (flashObjectWidth / 2);
    }
	
	AC_FL_RunContent(
		'codebase', 'http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=10,0,0,0',
		'width', flashObjectWidth,
		'height', flashObjectHeight,
		'src', 'map',
		'quality', 'high',
		'pluginspage', 'http://www.adobe.com/go/getflashplayer',
		'align', 'middle',
		'play', 'true',
		'loop', 'true',
		'scale', 'showall',
		'wmode', 'transparent',
		'devicefont', 'false',
		'id', 'map',
		'bgcolor', '#FFFFFF',
		'name', 'map',
		'menu', 'true',
		'allowFullScreen', 'false',
		'allowScriptAccess','sameDomain',
		'movie', 'map',
		'salign', '',
        'FlashVars', '<?php echo $xmlstr; ?>'
	); //end AC code
</script>
<noscript>
	<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" wmode="transparent" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=10,0,0,0" width="100%" height="400" id="map" align="middle">
	<param name="allowScriptAccess" value="sameDomain" />
	<param name="allowFullScreen" value="false" />
	<param name="wmode" value="transparent" /> 
    <param name="movie" value="map.swf?<?php echo $xmlstr; ?>" /><param name="quality" value="high" /><param name="bgcolor" value="#FFFFFF" />	<embed src="map.swf?<?php echo $xmlstr; ?>" quality="high" bgcolor="#FFFFFF" width="800" height="400" name="map" align="middle" allowScriptAccess="sameDomain" allowFullScreen="false" type="application/x-shockwave-flash" pluginspage="http://www.adobe.com/go/getflashplayer" />
	</object>
</noscript>
