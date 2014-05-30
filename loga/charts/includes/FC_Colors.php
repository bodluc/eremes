<?php
//This page contains an array of colors to be used as default set of colors for FusionCharts
//arr_FCColors is the array that would contain the hex code of colors 
//ALL COLORS HEX CODES TO BE USED WITHOUT #


//We also initiate a counter variable to help us cyclically rotate through
//the array of colors.
$FC_ColorCounter=0;
$arr_FCColors = array
(
"FDFD66",
"00DD22",
"C84239",
"FCD381",
"F971F7",
"3C1F6E",
"2E880A",
"DD1840",
"6DF9FB",
"FCDA00",
"1941A5",
"AFD8F8",
"F6BD0F",
"8BBA00",
"A66EDD",
"F984A1",
"CCCC00",
"999999",
"0099CC",
"FF0000",
"006F00",
"0099FF",
"FF66CC",
"669966",
"7C7CB4",
"FF9933",
"9900FF",
"99FFCC",
"CCCCFF",
"669900"
);

//getFCColor method helps return a color from arr_FCColors array. It uses
//cyclic iteration to return a color from a given index. The index value is
//maintained in FC_ColorCounter
$FC_ColorCounter = 0;
function getFCColor() 
{
	global $arr_FCColors, $FC_ColorCounter;
	//accessing the global variables
	$arr_FCColors = array (
		"FDFD66",
		"00DD22",
		"C84239",
		"FCD381",
		"F971F7",
		"3C1F6E",
		"2E880A",
		"DD1840",
		"6DF9FB",
		"FCDA00",
		"1941A5",
		"AFD8F8",
		"F6BD0F",
		"8BBA00",
		"A66EDD",
		"F984A1",
		"CCCC00",
		"999999",
		"0099CC",
		"FF0000",
		"006F00",
		"0099FF",
		"FF66CC",
		"669966",
		"7C7CB4",
		"FF9933",
		"9900FF",
		"99FFCC",
		"CCCCFF",
		"669900"
	);
	
	//Update index
	$FC_ColorCounter++;
	
	//Return color
	return($arr_FCColors[$FC_ColorCounter % count($arr_FCColors)]);
}
?>
