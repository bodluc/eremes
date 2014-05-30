<?php
echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
include_once "../common.inc.php";
include_once("includes/FusionCharts.php");
$profile = new SiteProfile($conf); 
//$color[0]="808080";
$color[0]="BEBFBF";
$color[1]="89A1B6";
$color[2]="5182AC";
//$color[3]="01559D";
$color[3]="FF0000";
$color[4]= "FF8040";
$color[5]="FFFF00";
$color[6]= "FF0080";
$color[7]= "00FF00";
$color[8]= "FFFFCC";
$color[9]= "FF0000";
$color[10]="00CCFF";
$color[11]="CCCCCC"; 
$color[12]="993300";
 
$todaysdate = @$_REQUEST["todaysdate"];

// $from   = mktime(0,0,0,(date("m", $todaysdate)-1),01,date("Y", $todaysdate));
$from   = strtotime("-3 weeks",$todaysdate);
$from     = mktime(0,0,0,date("m", $from),date("d", $from),date("Y", $from));
$to     = mktime(23,59,59,date("m", $todaysdate),date("d", $todaysdate),date("Y", $todaysdate));
$to_week = date("W Y",$to);
$from_week = date("W Y",$from);




$n = 7;
if ($n > 8) {
    $step = round(($n/5),0);
} else {
    $step=1;
}
if ($step==0) {
    $step=1;   
}

/*
$series[0][0]="<dataset seriesname=\"Visitors Week of \" color=\"".$color[0]."\" showValues=\"0\" alpha=\"70\">\n";
$series[1][0]="<dataset seriesname=\"Visitors week 2\" color=\"".$color[1]."\" showValues=\"0\">\n";
$series[2][0]="<dataset seriesname=\"Visitors week 3\" color=\"".$color[2]."\" showValues=\"0\">\n";
$series[3][0]="<dataset seriesname=\"Visitors week 4\" color=\"".$color[3]."\" showValues=\"0\">\n"; 
*/
$categories[0]="<categories>\n";
/*
$categories[1]="  <category name=\"1 ".date("D",$from)."\"/>\n";
$categories[2]="  <category name=\"2 ".date("D",strtotime("+1 day",$from))."\"/>\n";
$categories[3]="  <category name=\"3 ".date("D",strtotime("+2 days",$from))."\"/>\n";
$categories[4]="  <category name=\"4 ".date("D",strtotime("+3 days",$from))."\"/>\n";
$categories[5]="  <category name=\"5 ".date("D",strtotime("+4 days",$from))."\"/>\n";
$categories[6]="  <category name=\"6 ".date("D",strtotime("+5 days",$from))."\"/>\n";
$categories[7]="  <category name=\"7 ".date("D",strtotime("+6 days",$from))."\"/>\n";
*/

$categories[1]="  <category name=\"Mon\"/>\n";
$categories[2]="  <category name=\"Tue\"/>\n";
$categories[3]="  <category name=\"Wed\"/>\n";
$categories[4]="  <category name=\"Thu\"/>\n";
$categories[5]="  <category name=\"Fri\"/>\n";
$categories[6]="  <category name=\"Sat\"/>\n";
$categories[7]="  <category name=\"Sun\"/>\n";
/*
$series[0][0]="<dataset seriesname=\"3 weeks ago\" color=\"".$color[0]."\" showValues=\"0\" alpha=\"70\">\n";
$series[1][0]="<dataset seriesname=\"2 weeks ago\" color=\"".$color[1]."\" showValues=\"0\" alpha=\"80\">\n";
$series[2][0]="<dataset seriesname=\"1 week ago\" color=\"".$color[2]."\" showValues=\"0\" alpha=\"90\">\n";
$series[3][0]="<dataset seriesname=\"This week\" color=\"".$color[3]."\" showValues=\"0\">\n";
*/
$series[0][0]="<dataset seriesname=\"3 weeks ago\" color=\"".$color[0]."\" showValues=\"0\">\n";
$series[1][0]="<dataset seriesname=\"2 weeks ago\" color=\"".$color[1]."\" showValues=\"0\">\n";
$series[2][0]="<dataset seriesname=\"1 week ago\" color=\"".$color[2]."\" showValues=\"0\">\n";
$series[3][0]="<dataset seriesname=\"This week\" color=\"".$color[3]."\" showValues=\"0\">\n";


$s=0;
$thisweek=date("W Y",$from);
$thisfrom=$from;

$prefill = array();
$prefill[1] = "Monday";
$prefill[2] = "Tuesday"; 
$prefill[3] = "Wednesday"; 
$prefill[4] = "Thursday"; 
$prefill[5] = "Friday"; 
$prefill[6] = "Saturday"; 
$prefill[7] = "Sunday";
    
while ($s < 4) {
    
    $query="select FROM_UNIXTIME(timestamp,'%W') as day,visitors from $profile->tablename_vpd where FROM_UNIXTIME(timestamp,'%u %Y')='$thisweek' order by timestamp";
    //$query="select days,visitors from $profile->tablename_vpd where timestamp >= $thisfrom and timestamp < $thisto order by timestamp";
    $q=$db->Execute($query);           
    
    $i=1;
    while ($i <= 7) {
        $series[$s][$i]= "  <set value=\"0\" />\n"; 
        $i++;   
    }
    //$series[$s][0]="<dataset seriesname=\"Visitors Week of ".date("D d/m",$thisfrom)."\" color=\"".$color[$s]."\" showValues=\"0\">\n";
    $i=1; 
    while ($cdata=$q->FetchRow()) {
        while ($prefill[$i]!=$cdata[0]) {
            $i++;
        } 
        if ($prefill[$i]==$cdata[0]) {          
            $series[$s][$i]= "  <set value=\"".$cdata[1]."\" />\n";
        } else {
            echoDebug("NOOOO match for {$cdata[0]},  {$cdata[1]}");
        } 
         //echoDebug("$i: ".$cdata[0]);
         $i++;
    }
    //echoDebug("done with series $s");
    $thisfrom = strtotime("+1 week",$thisfrom);
    $thisweek = date("W Y",$thisfrom);    
    $s++;
    
}
reset($series);
reset($categories);

echo "<graph
    yAxisMinValue=\"0\"
    decimalPrecision=\"0\"
    formatNumberScale=\"0\"
    showNames=\"1\"
    showValues=\"0\"
    showAlternateHGridColor=\"1\"
    AlternateHGridColor=\"CCCCCC\"
    divLineColor=\"B2AEAE\"
    divLineAlpha=\"20\"
    alternateHGridAlpha=\"5\"
    canvasBorderThickness=\"0\"
    rotateNames=\"1\"
    canvasBorderColor=\"cccccc\"
    chartLeftMargin=\"0\"
    chartRightMargin=\"5\"
    chartTopMargin=\"5\"
    chartBottomMargin=\"0\"
    canvasBaseDepth=\"0\"
    canvasBaseWidth=\"0\"
    canvasBgDepth=\"0\"
    canvasBgColor=\"FFFFFF\"
    canvasBaseColor=\"CCCCCC\"
    animation=\"";echo $profile->animate == 1 ? "0" : "1";echo"\"
>\n";

ksort($categories);
$i=0;
$s=0;
foreach($categories as $data) {
    $s++;
     if ($s==$step) {
        $s=0;
        $showname=1;
    } else {
        $showname=0;
        //echo "no because $s != $step in dbloop";   
    } 
    if ($i==1) { 
        $showname=1; 
    }
    //echo str_replace("showName","showName=\"$showname\"",$data);
    echo $data;
    $i++;   
}
echo "</categories>\n";

$s=0;
while ($s < 4) {
    foreach($series[$s] as $data) {
        echo $data;   
    }
    echo "</dataset>\n";
    $s++;
}

echo "</graph>"; ?>