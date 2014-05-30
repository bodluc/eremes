<?php
//@session_start();

$color[0] = "9999FF";
$color[1] = "FF0000";
$color[2] = "009900";


$conversionXML = '<graph
    caption="'._CONVERSION_RATE.'"
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
    canvasBorderColor="EEEDED"';
    //PYAxisName="Conversion Rate"';
    //SYAxisName="Conversion Rate"';
    if(max($_SESSION["schart"][1]) != 0){ 
        $conversionXML .= 'PYAxisMaxValue="'.(max($_SESSION["schart"][1])*1.1).'"';
        $conversionXML .= 'SYAxisMaxValue="'.(max($_SESSION["schart"][1])*1.1).'"';
    } else {
        $conversionXML .= 'PYAxisMaxValue="5"';
        $conversionXML .= 'SYAxisMaxValue="5"';
    } 
    //if(min($_SESSION["schart"][1]) < 0){ 
        $conversionXML .= 'PYAxisMinValue="0"
    SYAxisMinValue="0"';
    //} else {
        //$conversionXML .= 'PYAxisMinValue="'.min($_SESSION["schart"][1]).'"
    //SYAxisMinValue="'.min($_SESSION["schart"][1]).'"';
    //}
    $conversionXML .= 'chartLeftMargin="0"
    chartRightMargin="5"
    chartTopMargin="5"
    chartBottomMargin="0"
    canvasBaseDepth="0"
    canvasBaseWidth="0"
    canvasBgDepth="0"
    canvasBgColor="FFFFFF"
    canvasBaseColor="CCCCCC"
    showDivLineSecondaryValue="0"
    showSecondaryLimits="0"
    decimalPrecision="2"
    divLineDecimalPrecision="2"
    limitsDecimalPrecision="2"
    numberSuffix="%"
    animation="';$conversionXML .= $profile->animate == 1 ? "0" : "1";$conversionXML .= '">';
 $conversionXML .= '<categories>'."\n";
$timespan = ($to - $from) / 86400;
$f = 0;
 for ($g = 0; $g < count($_SESSION['schart'][0]); $g++)
 {
	$prevWeek = date('W',$from+($g*86400)-86400);
	$newWeek = date('W',$from+($g*86400));
	$conversionXML .= '<category name="'.$_SESSION['schart'][0][$g].'"';
	if($period == _DAYS)
	{
	if($timespan <= 8)
	{
		$conversionXML .= ' showName="1"/>'."\n";
	}
	else
	{
        if($f == round($n/(round($n/($n/100*6)))))
		{
			$conversionXML .= ' showName="1"/>'."\n";
			$f = 0;
		}
		else
		{
			if($g == 0)
			{
				$conversionXML .= ' showName="1"/>'."\n";
			}
			else
			{
				$conversionXML .= ' showName="0"/>'."\n";
			}
		}
		$f++;
	}
	}else{$conversionXML .= ' showName="1"/>'."\n";}
 }
 $conversionXML .= '</categories>'."\n";
 $conversionXML .= '<dataset seriesName="'.$_SESSION['schart']['names'][1].'" color="'.$color[0].'">'."\n";
 for ($i = 0; $i < count($_SESSION['schart'][1]); $i++)
 {
 	$conversionXML .= '<set value="'.$_SESSION['schart'][1][$i].'"/>'."\n";
 }
 $conversionXML .= '</dataset>'."\n";
 $conversionXML .= '<dataset seriesName="'.$_SESSION['schart']['names'][2].'" color="'.$color[1].'" parentYAxis="S">'."\n";
 for ($i = 0; $i < count($_SESSION['schart'][2]); $i++)
 {
	$conversionXML .= '<set value="'.$_SESSION['schart'][2][$i].'" alpha="75"/>'."\n";
 }
 $conversionXML .= '</dataset>';
 $conversionXML .= '<dataset seriesName="'.$_SESSION['schart']['names'][3].'" color="'.$color[2].'" anchorAlpha="0" parentYAxis="S">'."\n";
 for ($i = 0; $i < count($_SESSION['schart'][3]); $i++)
 {
	$conversionXML .= '<set value="'.$_SESSION['schart'][3][$i].'" alpha="75"/>'."\n";
 }
 $conversionXML .= '</dataset>
</graph>';

echo "<div class=\"trendsborder\" style=\"width:320px;min-height:350px;\" id=\"conversion\">";
	echo renderChartHTML("charts/FCF_MSColumn2DLineDY.swf", "", urlencode($conversionXML), "Conversion", 300, 250, false, false, "opaque"); 	
echo '<ul class="graphUl">';
for($i = 1; $i < count($_SESSION['schart']['names']); $i++)
{
	echo '<li class="legend"><strong style="color: #'.@$color[$i-1].'!important; font-size: 18px!important; line-height: 10px;">&bull; </strong>'.$_SESSION['schart']['names'][$i].'</li>';
}
echo '</ul>';
echo "</div>";
?>
