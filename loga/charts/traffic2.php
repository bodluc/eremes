<?php
//@session_start(); //This can also be solved by putting the function "ob_start();" at the top of the script.

$color[0] = "9999FF";
$color[1] = "CC7777";

$traffic2XML = '<graph
    caption="'._VISITORS.'"
    yAxisMinValue="0"
    yAxisname="Unique Visitors"';
    if (max($_SESSION['tchart'][1]) < 5) {
        $traffic2XML .= 'yAxisMaxValue="5"';   
    }
    $traffic2XML .= 'decimalPrecision="0"
    formatNumberScale="0"
    showNames="1"
    showValues="0"
    showLegend="0"
    showAlternateHGridColor="1"
    AlternateHGridColor="CCCCCC"
    divLineColor="EEEDED"
    divLineAlpha="100"
    alternateHGridAlpha="5"
    canvasBorderThickness="0"
    rotateNames="1"
    canvasBorderColor="EEEDED"
    chartLeftMargin="4"
    chartRightMargin="5"
    chartTopMargin="5"
    chartBottomMargin="0"
    canvasBaseDepth="0"
    canvasBaseWidth="0"
    canvasBgDepth="0"
    canvasBgColor="FFFFFF"
    canvasBaseColor="CCCCCC"
    animation="';$traffic2XML .= $profile->animate == 1 ? "0" : "1";$traffic2XML .= '">';
  $traffic2XML .= '<categories>';
$f = 0;
 for ($g = 0; $g < count($_SESSION['tchart'][0]); $g++)
 {
	$prevWeek = date('W',$from+($g*86400)-86400);
	$newWeek = date('W',$from+($g*86400));
	$traffic2XML .= '<category name="'.$_SESSION['tchart'][0][$g].'"';
	if($period == _DAYS){
	if($n <= 8)
	{
		$traffic2XML .= ' showName="1"/>';
	}
	else
	{
        if($f == round($n/(round($n/($n/100*6)))))
		{
			$traffic2XML .= ' showName="1"/>';
			$f = 0;
		}
		else
		{
			if($g == 0)
			{
				$traffic2XML .= ' showName="1"/>';
			}
			else
			{
				$traffic2XML .= ' showName="0"/>';
			}
		}
		$f++;
	}
	}else{
	$traffic2XML .= ' showName="1"/>'."\n";
	}
 }
   $traffic2XML .= '</categories> ';
   if (array_sum($_SESSION['tchart'][1])!=0) {
       $traffic2XML .= '<dataset seriesname="'.$_SESSION['tchart']['names'][1].'" color="'.$color[0].'" showValue="1" alpha="75">';
	    for($i = 0; $i < count($_SESSION['tchart'][1]); $i++)
	    {      
		    $traffic2XML .= '<set value="'.$_SESSION['tchart'][1][$i].'" />';
	    }
       $traffic2XML .= '</dataset>';
   }
   $traffic2XML .= '
   <trendlines>
   	<line startValue="'.$_SESSION['tchart'][2][0].'" endValue="'.$_SESSION['tchart'][2][count($_SESSION['tchart'][2])-1].'" showOnTop="0" color="'.$color[1].'" displayValue=" "/>
   </trendlines>';
   $traffic2XML .= '</graph>';

echo "<div class=\"trendsborder\" style=\"width:625px;\" id=\"trafficgraph2\">";
	echo renderChartHTML("charts/FCF_MSColumn2D.swf", "", urlencode($traffic2XML), "traffic2", 600, 250, false, false, "opaque"); 	
echo '<ul class="graphUl">';
for($i = 1; $i < count($_SESSION['tchart']['names']); $i++)
{
	echo '<li class="legend"><strong style="color: #'.$color[$i-1].'!important; font-size: 18px!important; line-height: 10px;">&bull; </strong>'.$_SESSION['tchart']['names'][$i].'</li>';
}
echo '</ul>';
echo "</div>";
?>
