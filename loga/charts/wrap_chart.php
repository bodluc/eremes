<?php
require_once '../core_factory.php';
Logaholic_sessionStart();

$gname=@$_GET['name'];
$from=@$_REQUEST['from'];
$to=@$_REQUEST['to'];
$conf=@$_REQUEST['conf'];
$todaysdate=@$_REQUEST['todaysdate'];  

if ($gname!="") {
    setcookie("selectedTodayChart",$gname, @strtotime("+1 year"),"/");
}
include_once("includes/FusionCharts.php");

$lang = Logaholic_getCurrentLang();

include_once "../languages/$lang.php";

function graphTypeMenu($selected) {
    global $to, $from, $conf, $todaysdate;
    
    $r[0] = "<a class=graylink href=\"javascript:TodayGraphSelect('months','$to','$from','$conf','$todaysdate');\">"._MONTH."</a>  "; 
    $r[1] = "<a class=graylink href=\"javascript:TodayGraphSelect('weeks','$to','$from','$conf','$todaysdate');\">"._WEEK."</a>  ";
    $r[2] = "<a class=graylink href=\"javascript:TodayGraphSelect('more','$to','$from','$conf','$todaysdate');\">"._MORE."</a>";

    for ($i=0;$i < count($r);$i++) {
        if (strpos($r[$i],$selected)!==FALSE) {
            $r[$i] = str_replace("graylink","graylinkselected",$r[$i]);
        }  
    }
    echo "<span style=\"position:absolute;margin-top: 7px; margin-left:238px;\">";
    foreach ($r as $row) {
        echo $row;   
    }
    echo "</span>";
} 



if (@$gname=="") {
    $gname=@$_COOKIE['selectedTodayChart'];    
}
$unique="uniqueID=" . uniqid(rand(),true);

if (@$gname=="") {
    $gname="months";    
}
graphTypeMenu($gname);
if ($gname == "weeks") {
    $charturl=urlencode("charts/weekcompare.php?to=$to&from=$from&conf=$conf&todaysdate=$todaysdate");
    echo renderChartHTML("charts/FCF_MSColumn2D.swf", "$charturl", "", "monthcompare", 365, 170, false, false, "opaque"); 
} if ($gname == "oldmonths") {
    $charturl=urlencode("charts/monthcompare.php?to=$to&from=$from&conf=$conf&todaysdate=$todaysdate&$unique");  
    echo renderChartHTML("charts/FCF_MSColumn3D.swf", "$charturl", "", "monthcompare", 365, 170, false, false, "opaque"); 
} else if ($gname == "months") {
    $charturl=urlencode("charts/monthcompare_area.php?to=$to&from=$from&conf=$conf&todaysdate=$todaysdate&$unique"); 
    echo renderChartHTML("charts/FCF_MSArea2D.swf", "$charturl", "", "monthcompare", 365, 170, false, false, "opaque");
} else if ($gname == "years") {
    $charturl=urlencode("charts/yearcompare_area.php?to=$to&from=$from&conf=$conf&todaysdate=$todaysdate&$unique"); 
    echo renderChartHTML("charts/FCF_MSArea2D.swf", "$charturl", "", "yearcompare", 365, 170, false, false, "opaque");   
} else if ($gname=="more") {
    echo "<div class=\"MoreTodayGraphOptions\">"._MORE_GRAPHS.":<ul>";
    echo "<li><a href=\"javascript:TodayGraphSelect('oldmonths','$to','$from','$conf','$todaysdate');\">"._MONTH_COMPARE_BAR."</a></li>";
    echo "<li><a href=\"javascript:TodayGraphSelect('years','$to','$from','$conf','$todaysdate');\">"._THISYEAR_VS_LASTYEAR."</a></li>";
    echo "</ul></div>";   
}
   
?>
