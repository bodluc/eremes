<?php
//session_start();

$color[0] = "9999FF";
$color[1] = "CC7777";
$color[2] = "333333";
$color[3] = "009900";

$trafficXML = '<graph
    caption="'._VISITORS.'"
    yAxisMinValue="0"
    decimalPrecision="0"
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
    chartLeftMargin="0"
    chartRightMargin="5"
    chartTopMargin="5"
    chartBottomMargin="0"
    canvasBaseDepth="0"
    canvasBaseWidth="0"
    canvasBgDepth="0"
    canvasBgColor="FFFFFF"
    canvasBaseColor="CCCCCC"
    animation="';$trafficXML .= $profile->animate == 1 ? "0" : "1";$trafficXML .= '">';
  $trafficXML .= '<categories>';
$timespan = ($to - $from) / 86400;
$f = 0;
 for ($g = 0; $g < count($_SESSION['tchart'][0]); $g++)
 {
	$prevWeek = date('W',$from+($g*86400)-86400);
	$newWeek = date('W',$from+($g*86400));
	$trafficXML .= '<category name="'.$_SESSION['tchart'][0][$g].'"';
	if($period == _DAYS){
	if($timespan <= 8)
	{
		$trafficXML .= ' showName="1"/>'."\n";
	}
	else
	{
		if($f == round($timespan/(round($timespan/($timespan/100*6)))))
		{
			$trafficXML .= ' showName="1"/>'."\n";
			$f = 0;
		}
		else
		{
			if($g == 0)
			{
				$trafficXML .= ' showName="1"/>'."\n";
			}
			else
			{
				$trafficXML .= ' showName="0"/>'."\n";
			}
		}
		$f++;
	}
	}else{
	$trafficXML .= ' showName="1"/>'."\n";
	}
 }
   $trafficXML .= '</categories>
   <dataset seriesname="'.$_SESSION['tchart']['names'][1].'" color="'.$color[0].'" showValue="1">';
	for($i = 0; $i < count($_SESSION['tchart'][1]); $i++)
	{      
		$trafficXML .= '<set value="'.$_SESSION['tchart'][1][$i].'" />'."\n";
	}
   $trafficXML .= '</dataset>
   <dataset seriesname="'.$_SESSION['tchart']['names'][2].'" color="'.$color[1].'" showValue="1" alpha="70">';
	for($i = 0; $i < count($_SESSION['tchart'][2]); $i++)
	{      
		$trafficXML .= '<set value="'.$_SESSION['tchart'][2][$i].'" />'."\n";
	}
   $trafficXML .= '</dataset>
   <trendlines>
   	<line startValue="'.$_SESSION['tchart'][3][0].'" endValue="'.$_SESSION['tchart'][3][count($_SESSION['tchart'][3])-1].'" showOnTop="0" color="'.$color[2].'" displayValue=" "/>
   	<line startValue="'.$_SESSION['tchart'][4][0].'" endValue="'.$_SESSION['tchart'][4][count($_SESSION['tchart'][4])-1].'" showOnTop="0" color="'.$color[3].'" displayValue=" "/>
   </trendlines>
</graph>';

echo "<div class=\"trendsborder\" style=\"width:320px;min-height:350px;\" id=\"trafficgraph\">";
	echo renderChartHTML("charts/FCF_MSColumn2D.swf", "", urlencode($trafficXML), "traffic", 300, 250, false, false, "opaque"); 	
echo '<ul class="graphUl">';
for($i = 1; $i < count($_SESSION['tchart']['names']); $i++)
{
	echo '<li class="legend"><strong style="color: #'.$color[$i-1].'!important; font-size: 18px!important; line-height: 10px;">&bull; </strong>'.$_SESSION['tchart']['names'][$i].'</li>';
}
echo '</ul>';
echo "</div>";
?>
