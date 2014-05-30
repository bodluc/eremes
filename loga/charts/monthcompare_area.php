<?php
require_once '../core_factory.php';
Logaholic_sessionStart();
echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
include_once "../common.inc.php";
$profile = new SiteProfile($conf); 

//$color[0]="CCFFCC";
//$color[1]="9999FF";
$color[0]="EEEEEE";
$color[1]="9999FF";

$color[2]="0080C0";
$color[3]="FF3399";
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

$from   = mktime(0,0,0,(date("m", $todaysdate)-1),01,date("Y", $todaysdate));
$to     = mktime(23,59,59,date("m", $todaysdate),date("d", $todaysdate),date("Y", $todaysdate));
$lastmonth_q=date("M Y",$from);
$thismonth_q=date("M Y",$to);
$lmn=get_month_lastday(date("m",$from),date("Y",$from));
$tmn=get_month_lastday(date("m",$to),date("Y",$to)); 
if ($lmn > $tmn) {
    $n=$lmn;   
} else {
    $n=$tmn;
}

if ($n > 8) {
    $step = round(($n/5),0);
} else {
    $step=1;
}
if ($step==0) {
    $step=1;   
}
$i=1;
$categories[0]="<categories>\n";

$lastmonth[0]="<dataset seriesname=\"Visitors ".date("F",$from)."\" color=\"".$color[0]."\" showValues=\"0\" areaAlpha=\"70\" showAreaBorder=\"1\" areaBorderThickness=\"3\" areaBorderColor=\"006600\">\n";
$thismonth[0]="<dataset seriesname=\"Visitors ".date("F",$to)."\" color=\"".$color[1]."\" showValues=\"0\" areaAlpha=\"75\" showAreaBorder=\"1\" areaBorderThickness=\"3\" areaBorderColor=\"FF0000\">\n";
 
while ($i <= $n) {
    $categories[$i]="  <category name=\"$i\" />\n";                                                                         
    $lastmonth[$i]= "  <set value=\"\" />\n";
    $thismonth[$i]= "  <set value=\"\" />\n";
    $i++;
}

$query="select FROM_UNIXTIME(timestamp,'%d') as day,visitors from $profile->tablename_vpd where FROM_UNIXTIME(timestamp,'%b %Y')='$lastmonth_q' order by timestamp";
$q=$db->Execute($query);
$i=0;
$lmavg=0;
while ($cdata=$q->FetchRow()) {
     $d=intval($cdata[0]);
     $lastmonth[$d]= "  <set value=\"".$cdata[1]."\" />\n";
     $lmavg=$lmavg+$cdata[1];
     $i++;
}
$lmavg=@($lmavg/$i);

$query="select FROM_UNIXTIME(timestamp,'%d') as day,visitors from $profile->tablename_vpd where FROM_UNIXTIME(timestamp,'%b %Y')='$thismonth_q' order by timestamp";
$q=$db->Execute($query);
$i=0;
$tmavg=0;
while ($cdata=$q->FetchRow()) {
    $d=intval($cdata[0]);
    $thismonth[$d]= "  <set value=\"".$cdata[1]."\" />\n";
    $tmavg=$tmavg+$cdata[1];
    $i++;
}
$tmavg=@($tmavg/$i);

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
//while (list ($key, $val) = each ($dset)) {
ksort($lastmonth);
foreach($lastmonth as $data) {
    echo $data;   
}
echo "</dataset>\n";
ksort($thismonth);
foreach($thismonth as $data) {
    echo $data;   
}
echo "</dataset>\n";
echo "<trendLines>";
echo "<line startValue=\"$lmavg\" endValue=\"$tmavg\" color=\"0000FF\" displayValue=\" \" thickness=\"2\" alpha=\"20\" isTrendZone=\"0\" showOnTop=\"1\" />";
echo "</trendLines>"; 
echo "</graph>"; ?>