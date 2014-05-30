<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
$conf = @$_GET["conf"];
$from = @$_GET["from"];
$to = @$_GET["to"];

if(!empty($_GET['minimumDate'])) { $from = strtotime($_GET['minimumDate']); }
if(!empty($_GET['maximumDate'])) { $to = strtotime($_GET['maximumDate']); }

$thediv = @$_GET["thediv"];
$keyword= urldecode(@$_GET["keyword"]);
// it's no use to send someone to the trends screen for 1 day, so we'll override that here
if (($to-$from) <= 86400) {
    $trendfrom=($to - (14*86400));
} else {
    $trendfrom=$from;   
}
//$keyword= iconv("UTF-8", "ISO-8859-1", urldecode(@$_GET["keyword"]));
//urldecode(@$_GET["keyword"]);
$referrer= urldecode(@$_GET["referrer"]);
$statuscode = @$_GET["statuscode"];
$forminput = @$_GET["forminput"];
$ipnumber= @$_GET["ipnumber"];
$useragent= @$_GET["useragent"];  

include "../common.inc.php"; 
$profile = new SiteProfile($conf);

$url = @$_GET["url"];
if(!empty($_GET['clicktrailurl'])) {
	$fromclicktrail = true;
	$url = @$_GET["clicktrailurl"];
}
if(!empty($_GET['clicktrailreferrer'])) {
	$fromclicktrail = true;
	$referrer = @$_GET["clicktrailreferrer"];
}
if(!empty($_GET['clicktrailipnumber'])) {
	$fromclicktrail = true;
	$ipnumber = $_GET['clicktrailipnumber'];
}

$urlbreak = urldecode($url);
$purl = "";
while ($urlbreak > "") {
  if ($purl > "") {
    $purl .= "<br>";
  }
  $purl .= substr($urlbreak, 0, 35);
  $urlbreak = substr($urlbreak, 35); 
}
if ($purl=="/") {
    $purl="/ (Home Page)";
}

if ($url!="") {

    if (strpos(urldecode($url),"?")!=FALSE) {
        $urlparts=explode("?",urldecode($url));
        $addcond="&cvalue2=".urlencode($urlparts[1])."&field2=params&condition2=contains&andor=AND";  
    } else {
        $urlparts[0]=$url;
        $addcond="";   
    }
	
    echo "<table width=220 border=0 cellspacing=0 cellpadding=3>";
    echo "<tr><td colspan=2 class='actionmenu-header'><b>$purl:</td></tr></table>";
    echo "<table width=220 border=0 cellspacing=0 cellpadding=3>";
    echo "<tr><td colspan=2><hr noshade size=1></td></tr>";
    
    echo "<tr class=\"profilerow\" onmouseover=\"srrowOverEffect(this)\" onmouseout=\"srrowOutEffect(this)\"><td>";
    echo "<img src=images/icons/chart_line.gif width=16 height=16 alt='Page Trend Analysis' border=0></td><td><a class='small open_in_new_dialog quickopen' type='_TREND_ANALYSIS' name='"._TREND_ANALYSIS."' rel='TrendAnalysis' href=\"reports.php?labels=_TREND_ANALYSIS&sourcetype=page&source=".urlencode($url)."&conf=$conf&from=$from&to=$to\">"._SHOW_PAGE_TRENDS."</a><br>";
    echo "</td></tr>";
    
	# We want to do this later, right now, actionmenu's are too messy
	// $targets = explode(",",$profile->targetfiles);
	
	// foreach ($targets as $thistarget) {
		// $thistarget = trim($thistarget);
		// if($thistarget == $url) {
			// echo "<tr class=\"profilerow\" onmouseover=\"srrowOverEffect(this)\" onmouseout=\"srrowOutEffect(this)\"><td>";
			// echo "<img src=images/icons/chart_line.gif width=16 height=16 alt='Conversion Trends over time' border=0></td><td><a class='small open_in_new_dialog quickopen' type='_CONVERSION_TRENDS' name='"._CONVERSION_TRENDS."' rel='ConversionTrends' href=\"reports.php?labels=_CONVERSION_TRENDS&roadto=".urlencode($url)."&conf=$conf&from=$from&to=$to&period=auto\">"._SHOW_CONVERSION_TRENDS."</a><br>";
			// echo "</td></tr>";
			// break;
		// }
	// }
	
    echo "<tr class=\"profilerow\" onmouseover=\"srrowOverEffect(this)\" onmouseout=\"srrowOutEffect(this)\"><td>";
    echo "<img src=images/icons/page_analysis.gif width=16 heigh=16 border=0></td><td><a class='small open_in_new_dialog quickopen' type='_PAGE_ANALYSIS' name='"._PAGE_ANALYSIS."' rel='PageAnalysis' href=\"reports.php?labels=_PAGE_ANALYSIS&page=".$url."&conf=$conf&from=$from&to=$to&crawlerselect= \">"._GO_TO_PAGE_ANALYSIS."</a><br>";
    echo "</td></tr>";
   
    echo "<tr class=\"profilerow\" onmouseover=\"srrowOverEffect(this)\" onmouseout=\"srrowOutEffect(this)\"><td>";
    echo "<img src=images/icons/eye.gif width=16 heigh=16 border=0></td><td><a class=small href=\"page.php?page=".$url."&conf=$conf&visual=1\" target=_blank>"._GO_TO_PAGE_ANALYSIS_VISUAL_OVERLAY."</a><br>";
    echo "</td></tr>";
    
    if ($addcond=="") {
        echo "<tr class=\"profilerow\" onmouseover=\"srrowOverEffect(this)\" onmouseout=\"srrowOutEffect(this)\"><td>";
        echo "<img src=images/icons/page_attach.gif width=16 height=16 alt='Page Details' border=0></td><td><a class='small open_in_new_dialog quickopen' type='_TOP_PAGES_DETAILS' name='"._TOP_PAGES_DETAILS."' rel='TopPagesDetails' href=\"reports.php?labels=_TOP_PAGES_DETAILS&search=".$url."&searchmode=like&from=$from&to=$to\">"._SHOW_ALL_PAGE_VARIATIONS."</a><br>";
        echo "</td></tr>";
    }
    echo "<tr class=\"profilerow\" onmouseover=\"srrowOverEffect(this)\" onmouseout=\"srrowOutEffect(this)\"><td>";
	if(!empty($fromclicktrail)) {
		echo "<img src=images/icons/mouse_add.gif width=16 height=16 alt='View Click Trails to this page' border=0></td><td><a class='small open_in_new_dialog quickopen' type='_CLICK_TRAILS' name='"._CLICK_TRAILS."' rel='ClickTrail' href=\"reports.php?labels=_CLICK_TRAILS&from=$from&to=$to&cvalue1=".$urlparts[0]."&conf=$conf&field1=url&condition1=is$addcond\">"._VIEW_CLICK_TRAIL_TO_PAGE."</a><br> ";
	} else {
		echo "<img src=images/icons/mouse_add.gif width=16 height=16 alt='View Click Trails to this page' border=0></td><td><a class='small open_in_new_dialog quickopen' type='_CLICK_TRAILS' name='"._CLICK_TRAILS."' rel='ClickTrail' href=\"reports.php?labels=_CLICK_TRAILS&from=$from&to=$to&cvalue1=".$urlparts[0]."&conf=$conf&field1=url&condition1=is$addcond\">"._VIEW_CLICK_TRAIL_TO_PAGE."</a><br> ";
	}
    echo "</td></tr>";
    
    echo "<tr class=\"profilerow\" onmouseover=\"srrowOverEffect(this)\" onmouseout=\"srrowOutEffect(this)\"><td width=16>";
    echo "<img src=images/icons/newpage.gif width=16 heigh=16 border=0></td><td width=100%><a class=small href=\"http://$profile->confdomain".urldecode($url)."\" target=_blank>"._GO_TO_WEB_PAGE."</a><br>";
    echo "</td></tr>";
    
    
    echo "<tr><td colspan=2><hr noshade size=1></td></tr>"; 
    
    echo "<tr class=\"profilerow\" onmouseover=\"srrowOverEffect(this)\" onmouseout=\"srrowOutEffect(this)\"><td>";
    echo "<img src=images/icons/filters.gif width=16 height=16 border=0></td><td><a class='small open_iframe_window' href=filters.php?conf=$conf&field1=url&condition1=is&from=$from&to=$to&cvalue1=".$url.">"._CREATE_FILTER_THIS_PAGE."</a><br>";
    echo "</td></tr>";
    
    echo "<tr class=\"profilerow\" onmouseover=\"srrowOverEffect(this)\" onmouseout=\"srrowOutEffect(this)\"><td>";
    //echo "<img src=images/icons/delete.gif width=16 height=16 border=0></td><td><a class=small href=\"javascript:go_profile('To remove this page from future statistics, please add this page to the skip pages filed in your profile, on the Data Collection tab','profiles.php?conf=$conf&editconf=$conf&edit=1&tab=data');\">Remove this Page from the Statistics</a><br>";
    echo "<img src=images/icons/delete.gif width=16 height=16 border=0></td><td><a class=small href=\"profiles.php?editconf=$conf&del=9&fldname=page&fldvalue=$url\">"._REMOVE_THIS_PAGE."</a><br>";
    echo "</td></tr>";
    
    echo "<tr><td colspan=2><hr noshade size=1></td></tr>";
    
    echo "<tr class=\"profilerow\" onmouseover=\"srrowOverEffect(this)\" onmouseout=\"srrowOutEffect(this)\"><td>";    
    echo "<img src=images/icons/cancel.gif width=16 height=16 border=0></td><td><a id=\"closeme\" class=small href=\"#close\">"._CANCEL_CLOSE_MENU."</a><br>";
    echo "</td></tr></table>";

} else if ($keyword!="") {
    
    echo "<table width=220 border=0 cellspacing=0 cellpadding=3>";
    echo "<tr><td colspan=2 class='actionmenu-header'><b>$keyword:</b><hr noshade size=1></td></tr>";
    
    echo "<tr class=\"profilerow\" onmouseover=\"srrowOverEffect(this)\" onmouseout=\"srrowOutEffect(this)\"><td>";
    echo "<img src=images/icons/chart_line.gif width=16 height=16 alt='Trend Analysis' border=0></td><td><a class='small open_in_new_dialog quickopen' type='_TREND_ANALYSIS' name='"._TREND_ANALYSIS."' rel='TrendAnalysis' href=\"reports.php?labels=_TREND_ANALYSIS&sourcetype=keyword&source=".urlencode($keyword)."&conf=$conf&from=$from&to=$to\">"._KEYWORD_OVER_TIME."</a><br>";
	echo "</td></tr>";
    
    
    echo "<tr class=\"profilerow\" onmouseover=\"srrowOverEffect(this)\" onmouseout=\"srrowOutEffect(this)\"><td>";
    echo "<img src=images/icons/mouse_add.gif width=16 height=16 alt='View Click Trails with this keyword' border=0></td><td width=100%><a class='small open_in_new_dialog quickopen' type='_CLICK_TRAILS' name='"._CLICK_TRAILS."' rel='ClickTrail' href=\"reports.php?labels=_CLICK_TRAILS&from=$from&to=$to&cvalue1=".urlencode($keyword)."&conf=$conf&field1=keywords&condition1=is\">"._VIEW_CLICK_TRAILS_KEYWORD."</a><br> ";
    echo "</td></tr>"; 
    
	echo "<tr class=\"profilerow\" onmouseover=\"srrowOverEffect(this)\" onmouseout=\"srrowOutEffect(this)\"><td>";
    echo "<img src=images/icons/page.gif width=16 height=16 alt='Show Keyword landing page' border=0></td><td><a class='small open_in_new_dialog quickopen' type='_LANDING_PAGE' name='"._LANDING_PAGE."' rel='LandingPage' href=\"reports.php?labels=_LANDING_PAGE&sourcetype=keywords&from=$from&to=$to&source=".urlencode($keyword)."&conf=$conf\">"."Show Keyword landing page"."</a><br>";
    echo "</td></tr>";
	
    
    echo "<tr><td colspan=2><hr noshade size=1></td></tr>"; 
    
    echo "<tr class=\"profilerow\" onmouseover=\"srrowOverEffect(this)\" onmouseout=\"srrowOutEffect(this)\"><td>";
    echo "<img src=images/icons/filters.gif width=16 height=16 border=0></td><td><a class='small open_iframe_window' href=\"filters.php?conf=$conf&field1=keywords&from=$from&to=$to&condition1=contains&cvalue1=".urlencode($keyword)."\">"._CREATE_FILTER_KEYWORD."</a><br>";
    echo "</td></tr>";

    echo "<tr><td colspan=2><hr noshade size=1></td></tr>";
    
    echo "<tr class=\"profilerow\" onmouseover=\"srrowOverEffect(this)\" onmouseout=\"srrowOutEffect(this)\"><td>";    
    echo "<img src=images/icons/cancel.gif width=16 height=16 border=0></td><td><a id=\"closeme\" class=small href=\"#close\">"._CANCEL_CLOSE_MENU."</a><br>";
    echo "</td></tr></table>";
    
} else if ($referrer!="") {
	$referrer = urldecode($referrer);
    if (strpos($referrer,"?")!=FALSE) {
        $refparts=explode("?",$referrer);
        $addcond="&cvalue2=".urlencode($refparts[1])."&field2=refparams&condition2=contains&andor=AND";  
    } else {
        $refparts[0]=$referrer;
        $addcond="";   
    }
    if (substr($referrer,0,3)=="[G]") {
        $showreferrer = substr($referrer,3);   
    } else {
        $showreferrer = $referrer;   
    }
    
    echo "<table width=220 border=0 cellspacing=0 cellpadding=3>";
    echo "<tr><td colspan=2 class='actionmenu-header'><b>". urldecode($showreferrer).":</b><hr noshade size=1></td></tr>";
    
    echo "<tr class=\"profilerow\" onmouseover=\"srrowOverEffect(this)\" onmouseout=\"srrowOutEffect(this)\"><td>";
    echo "<img src=images/icons/chart_line.gif width=16 height=16 alt='Trend Analysis' border=0></td><td><a class='small open_in_new_dialog quickopen' type='_TREND_ANALYSIS' name='"._TREND_ANALYSIS."' rel='TrendAnalysis' href=\"reports.php?labels=_TREND_ANALYSIS&sourcetype=referrer&source=".urlencode($referrer)."&conf=$conf&from=$from&to=$to\">"._REFERRER_TREND_OVER_TIME."</a><br>";
	echo "</td></tr>";	
	
	echo "<tr class=\"profilerow\" onmouseover=\"srrowOverEffect(this)\" onmouseout=\"srrowOutEffect(this)\"><td>";
    echo "<img src=images/icons/page.gif width=16 height=16 alt='Show Referrer landing page' border=0></td><td><a class='small open_in_new_dialog quickopen' type='_LANDING_PAGE' name='"._LANDING_PAGE."' rel='LandingPage' href=\"reports.php?labels=_LANDING_PAGE&sourcetype=referrer&from=$from&to=$to&source=".urlencode($referrer)."&conf=$conf&sourcetype=referrer\">"."Show Referrer landing page"."</a><br>";
    echo "</td></tr>";
	
    
    echo "<tr class=\"profilerow\" onmouseover=\"srrowOverEffect(this)\" onmouseout=\"srrowOutEffect(this)\"><td>";
	if(!empty($fromclicktrail)) {
		echo "<img src=images/icons/mouse_add.gif width=16 height=16 alt='View Click Trails with this referrer' border=0></td><td><a class='small open_in_new_dialog quickopen' type='_CLICK_TRAILS' name='"._CLICK_TRAILS."' rel='ClickTrail' href=\"reports.php?labels=_CLICK_TRAILS&cvalue1=".urlencode($refparts[0])."&from=$from&to=$to&field1=referrer&condition1=contains$addcond&conf=$conf\">"._VIEW_CLICK_TRAILS_REFERRER."</a><br> ";
	} else {
		echo "<img src=images/icons/mouse_add.gif width=16 height=16 alt='View Click Trails with this referrer' border=0></td><td><a class='small open_in_new_dialog quickopen' type='_CLICK_TRAILS' name='"._CLICK_TRAILS."' rel='ClickTrail' href=\"reports.php?labels=_CLICK_TRAILS&cvalue1=".urlencode($refparts[0])."&from=$from&to=$to&field1=referrer&condition1=contains$addcond&conf=$conf\">"._VIEW_CLICK_TRAILS_REFERRER."</a><br> ";
	}
    echo "</td></tr>";
    
    echo "<tr class=\"profilerow\" onmouseover=\"srrowOverEffect(this)\" onmouseout=\"srrowOutEffect(this)\"><td width=16>";
    echo "<img src=images/icons/newpage.gif width=16 heigh=16 border=0></td><td width=100%><a class=small href=\"".urldecode($showreferrer)."\" target=_blank>"._GO_TO_REFERRER_IN_NEW_WINDOW."</a><br>";
    echo "</td></tr>";   
    
    echo "<tr><td colspan=2><hr noshade size=1></td></tr>"; 
    
    echo "<tr class=\"profilerow\" onmouseover=\"srrowOverEffect(this)\" onmouseout=\"srrowOutEffect(this)\"><td>";
    echo "<img src=images/icons/filters.gif width=16 height=16 border=0></td><td><a class='small open_iframe_window' href=filters.php?conf=$conf&from=$from&to=$to&field1=referrer&condition1=contains&cvalue1=".$referrer.">"._CREATE_FILTER_THIS_REFERRER."</a><br>";
    echo "</td></tr>";

    echo "<tr><td colspan=2><hr noshade size=1></td></tr>";
    
    echo "<tr class=\"profilerow\" onmouseover=\"srrowOverEffect(this)\" onmouseout=\"srrowOutEffect(this)\"><td>";    
    echo "<img src=images/icons/cancel.gif width=16 height=16 border=0></td><td><a id=\"closeme\" class=small href=\"#close\">"._CANCEL_CLOSE_MENU."</a><br>";
    echo "</td></tr></table>";
    
} else if ($statuscode!="") {
    
    if (strpos($statuscode,"Error Trends")!==FALSE) {
        // nothing to do here  
    } 
   
    $code = substr($statuscode,0,3);
   
    echo "<table width=220 border=0 cellspacing=0 cellpadding=3>";
    echo "<tr><td colspan=2 class='actionmenu-header'><b>". $statuscode.":</b><hr noshade size=1></td></tr>";
    
    
    echo "<tr class=\"profilerow\" onmouseover=\"srrowOverEffect(this)\" onmouseout=\"srrowOutEffect(this)\"><td>";
    echo "<img src=images/icons/summary.gif width=16 height=16 alt='"._VIEW_PAGES_WITH_THIS_STATUSCODE."' border=0></td><td><a class='small open_in_new_dialog quickopen' type='_ERROR_REPORT' name='"._ERROR_REPORT."' rel='ErrorReport' href=\"reports.php?labels=_ERROR_REPORT&from=$from&to=$to&status=$code&conf=$conf&status=$code\">"._VIEW_PAGES_WITH_THIS_STATUSCODE."</a><br> ";
    echo "</td></tr>";
    
    echo "<tr class=\"profilerow\" onmouseover=\"srrowOverEffect(this)\" onmouseout=\"srrowOutEffect(this)\"><td>";
    echo "<img src=images/icons/mouse_add.gif width=16 height=16 alt='"._VIEW_CLICKTRAILS_WITH_THIS_STATUSCODE."' border=0></td><td><a class='small open_in_new_dialog quickopen' type='_CLICK_TRAILS' name='"._CLICK_TRAILS."' rel='ClickTrail' href=\"reports.php?labels=_CLICK_TRAILS3&from=$from&to=$to&conf=$conf&field1=status&condition1=is&cvalue1=$code\">"._VIEW_CLICKTRAILS_WITH_THIS_STATUSCODE."</a><br> ";
    echo "</td></tr>";

    echo "<tr><td colspan=2><hr noshade size=1></td></tr>";
    
    echo "<tr class=\"profilerow\" onmouseover=\"srrowOverEffect(this)\" onmouseout=\"srrowOutEffect(this)\"><td>";    
    echo "<img src=images/icons/cancel.gif width=16 height=16 border=0></td><td><a id=\"closeme\" class=small href=\"#close\">"._CANCEL_CLOSE_MENU."</a><br>";
    echo "</td></tr></table>";
    
} else if ($forminput!="") {
    
    $input = explode("@",$forminput);
    $val = $input[0];
    $field = $input[1];
    $type = $input[2];
    
    if ($type=="funnelentry") {
        if ($val=="any") {$val="/";}
        if (substr($val,0,4)!="http") {  //it's a url
            $type="page";
        } else {
            $type="referrer";
        }
        $orival=$val;
        $val=explode(",",$val);
        // this gets only that last value to input into the script
        foreach($val as $valpart) {
            $realval=trim($valpart);
        }
        if ($realval == $orival) {
            $orival="";   
        } else if (count($val) > 1) {
            $orival = str_replace($realval,"",$orival);
        }
        
        $val=$realval;       
    }
    if (substr($type,-1)=="s") {
        $label=$type;   
    } else if ($type=="country") {
        $label="Countries";    
    } else {
        $label=$type."s";   
    }
    
    echo "<table width=220 border=0 cellspacing=0 cellpadding=3>";
    echo "<tr><td colspan=2 class='actionmenu-header'>".ucwords($label)." matching: <b>\"". $val."\"</b><hr noshade size=1></td></tr>";
    
    if ($type=="page" || $type=="url") 
    {
        if (substr($val,0,1)!="/") {
            $val="%".$val;   
        }
        $query = "select url as item from $profile->tablename_urls where url like '$val%' order by id asc limit 10";
        $icon="icons/page.gif";
    } 
    else if ($type=="keyword" || $type=="keywords") 
    {
        $query = "select keywords as item from $profile->tablename_keywords where keywords like '%".$val."%' order by id asc limit 10";
        $icon="icons/searchengines.gif";
    } 
    else if ($type=="referrer") 
    {
        if (substr($val,0,4)!="http") {
            $val="%".$val;   
        }
        $query = "select referrer as item from $profile->tablename_referrers where referrer like '$val%' order by id asc limit 10";
        $icon="icons/link.gif";
    }
    else if ($type=="ipnumber") 
    {
        $query = "select ipnumber as item from $profile->tablename_visitorids where ipnumber like '$val%' order by id asc limit 10";
        $icon="icons/user.gif";
    }
    else if ($type=="params") 
    {
        if ($val=="") {$val="?";}
        if (substr($val,0,1)!="?") {
            $val="%".$val;   
        }
        $query = "select params as item from $profile->tablename_urlparams where params like '$val%' limit 10";
        $icon="icons/page_attach.gif";
    }
    else if ($type=="status") 
    {        
        $query = "select code as realitem, concat(code,\" - \",descr) as item from ".TBL_LGSTATUS." where code like '$val%'";
        $icon="icons/error.gif";
    }
    else if ($type=="refparams") 
    {
        if ($val=="") {$val="?";}
        if (substr($val,0,1)!="?") {
            $val="%".$val;   
        }
        $query = "select params as item from $profile->tablename_refparams where params like '$val%' limit 10";
        $icon="icons/link.gif";
    }
    else if ($type=="useragent") 
    {        
        $query = "select DISTINCT(CONCAT(name, ' ', version)) as item from {$profile->tablename_useragents} where CONCAT(name, ' ', version) like '%$val%' order by id limit 10";
        // $query = "select name as item from ".TBL_USER_AGENTS." where name like '%$val%' order by id limit 10";
        $icon="icons/computer.gif";
    }
    else if ($type=="country") 
    {        
        $query = "select distinct country as item from $profile->tablename where country like '$val%' limit 10";
        $icon="icons/world.gif";
    }
    else if ($type=="crawl") 
    {        
        $query = "select \"0 - Real People (Humans)\" as item, \"0\" as realitem union ";
        $query .= "select \"1 - Bots, Crawlers etc. (Computers)\" as item, \"1\" as realitem union ";
        $query .= "select \"2 - RSS Readers (Hits on Feed URLs)\" as item, \"2\" as realitem";
        $icon="icons/crawler.gif";
    }
    else if ($type=="is_mobile") 
    {        
        $query = "select \"0 = Not a Mobile Device\" as item, \"0\" as realitem union ";
        $query .= "select \"1 = A Mobile Device\" as item, \"1\" as realitem";
        $icon="icons/phone.gif";
    }
    else if ($type=="kpi") 
    {        
        if ($profile->targetfiles) {
            //$targets=explodeTargets($profile->targetfiles);            
            $targets=explode(",",$profile->targetfiles);            
            $icon="icons/cart.gif";
            $query="";
            $i=0;
            foreach ($targets as $thistarget) {
                $thistarget=trim($thistarget);
                if ($thistarget) {
                    if ($val!="") {
                        //echo "$thistarget,$val";
                        if (strpos($thistarget,$val)===FALSE) {
                            continue;
                        }
                    } 
                    if ($i > 0) { $query .= " union "; }
                    $query .= "select \"$thistarget\" as item";
                    $i++;
                }
            }
        }
    }
    
    $i=0;
    if (isset($query)) {    
        $q = $db->Execute($query);
        while ($data=$q->FetchRow()) {
            if (!isset($data['realitem'])) {
                if ($type=="country") {
                    $data['realitem'] = $data['item'];
                    $data['item'] = $data["item"]." - ".$cnames[$data["item"]];
                    $icon = "flags/".strtolower($data['realitem']).".png";        
                } else {
                    $data['realitem']=$data['item'];
                }
            }
            if ($input[2]=="funnelentry") {
                $clickevent ="toFunnelForm('$field','".urldecode($data['realitem'])."','$orival');"; 
            } else {
                $clickevent ="toFormDynamic('$field','".urlencode($data['realitem'])."');return false;";
                // $clickevent ="toForm('$field','".urldecode($data['realitem'])."');";
            }
            echo "<tr class=\"profilerow\" onmouseover=\"srrowOverEffect(this)\" onmouseout=\"srrowOutEffect(this)\" onclick=\"$clickevent\"><td>";
            echo "<img src=images/$icon width=16 height=16 alt='".$data['item']."' border=0></td><td><a class=small href=# onclick=\"$clickevent\">".urldecode($data['item'])."</a><br> ";
            echo"</td></tr>";
            $i++;
        }
        if ($i==0) {
            echo "<tr class=\"profilerow\"><td colspan=2>";
            echo "No $type found for \"$val\"<br> ";
            echo"</td></tr>";
        }
    } else {
        echo "<tr class=\"profilerow\"><td colspan=2>";
        echo "Error: No valid type selected<br> ";
        //echo "<script type=\"text/javascript\">hide_popup_menu();</script>";
        echo"</td></tr>";
    }
    echo "<tr><td colspan=2><hr noshade size=1></td></tr>"; 
    echo "<tr class=\"profilerow\" onmouseover=\"srrowOverEffect(this)\" onmouseout=\"srrowOutEffect(this)\"><td>";    
    echo "<img src=images/icons/cancel.gif width=16 height=16 border=0></td><td><a id=\"closeme\" class=small href=\"#close\">"._CANCEL_CLOSE_MENU."</a><br>";
    echo "</td></tr></table>";
    
} else if ($ipnumber!="") {
    $vip = explode(";",$ipnumber);
    $ipnumber = $vip[0];
    if(isset($vip[1])){
		$visitorid = $vip[1]; // if we have a visitorid, we're coming from clicktrial.php
	}else{
		$visitorid = '';
	}
    echo "<table width=220 border=0 cellspacing=0 cellpadding=3>";
    echo "<tr><td colspan=2 class='actionmenu-header'><b>". $ipnumber.":</b><hr noshade size=1></td></tr>";
    
    echo "<tr class=\"profilerow\" onmouseover=\"srrowOverEffect(this)\" onmouseout=\"srrowOutEffect(this)\"><td>";
    if (!empty($visitorid)) {
		if(!empty($fromclicktrail)) {
			echo "<img src=images/icons/mouse_add.gif width=16 height=16 alt='"._VIEW_CLICKTRAILS_WITH_THIS_IPNUMBER."' border=0></td><td><a class='small open_in_new_dialog quickopen' type='_CLICK_TRAILS' name='"._CLICK_TRAILS."' rel='ClickTrail' href=\"reports.php?labels=_CLICK_TRAILS&conf=$conf&field1=ipnumber&condition1=is&cvalue1=$ipnumber&from=$from&to=$to\">"._VIEW_CLICKTRAILS_WITH_THIS_IPNUMBER."</a><br> ";
		} else {
			echo "<img src=images/icons/mouse_add.gif width=16 height=16 alt='"._VIEW_CLICKTRAILS_WITH_THIS_IPNUMBER."' border=0></td><td><a class='small open_in_new_dialog quickopen' type='_CLICK_TRAILS' name='"._CLICK_TRAILS."' rel='ClickTrail' href=\"reports.php?labels=_CLICK_TRAILS&conf=$conf&field1=ipnumber&condition1=is&cvalue1=$ipnumber&from=$from&to=$to\">"._VIEW_CLICKTRAILS_WITH_THIS_IPNUMBER."</a><br> ";
		}
    } else {
		$visitorid_url = "reports.php?labels=_CLICK_TRAILS";
		$c = 0;
		foreach($_REQUEST as $req_key => $req_val) {
			$visitorid_url .= "&{$req_key}={$req_val}";
			$c++;
		}
		$visitorid_url = urldecode($visitorid_url);
		if(!empty($fromclicktrail)) {
			echo "<img src=images/icons/mouse_add.gif width=16 height=16 alt='"._VIEW_CLICKTRAILS_WITH_THIS_IPNUMBER."' border=0></td><td><a class='small open_in_new_dialog quickopen' type='_CLICK_TRAILS' name='"._CLICK_TRAILS."' rel='ClickTrail' href=\"{$visitorid_url}&from=$from&to=$to\">"._VIEW_CLICKTRAILS_WITH_THIS_IPNUMBER."</a><br> ";
		} else {
			echo "<img src=images/icons/mouse_add.gif width=16 height=16 alt='"._VIEW_CLICKTRAILS_WITH_THIS_IPNUMBER."' border=0></td><td><a class='small open_in_new_dialog quickopen' type='_CLICK_TRAILS' name='"._CLICK_TRAILS."' rel='ClickTrail' href=\"{$visitorid_url}&from=$from&to=$to\">"._VIEW_CLICKTRAILS_WITH_THIS_IPNUMBER."</a><br> ";
		}
    }
    echo "</td></tr>";
    
    
    echo "<tr class=\"profilerow\" onmouseover=\"srrowOverEffect(this)\" onmouseout=\"srrowOutEffect(this)\"><td width=16>";
    echo "<img src=images/icons/newpage.gif width=16 heigh=16 border=0></td><td width=100%><a class=small href=\"http://www.db.ripe.net/whois?form_type=simple&full_query_string=&searchtext=$ipnumber&do_search=Search\" target=_blank>"._RIPE_WHOIS_LOOKUP."</a><br>";
    echo "</td></tr>";
    
    echo "<tr><td colspan=2><hr noshade size=1></td></tr>"; 
    
    echo "<tr class=\"profilerow\" onmouseover=\"srrowOverEffect(this)\" onmouseout=\"srrowOutEffect(this)\"><td>";
    echo "<img src=images/icons/filters.gif width=16 height=16 border=0></td><td><a class='small open_iframe_window' href=filters.php?conf=$conf&from=$from&to=$to&field1=ipnumber&condition1=is&cvalue1=".$ipnumber.">"._CREATE_FILTER_THIS_IPNUMBER."</a><br>";
    echo "</td></tr>";    
	
	echo "<tr class=\"profilerow\" onmouseover=\"srrowOverEffect(this)\" onmouseout=\"srrowOutEffect(this)\"><td>";
    echo "<img src=images/icons/user_edit.gif width=16 height=16 border=0></td><td><a class='small open_iframe_window' href=editvisitors.php?conf=$conf&from=$from&to=$to&visitorid=$visitorid&ipnumber=".$ipnumber.">"._EDIT_VISITORS."</a><br>";
    echo "</td></tr>";
    
    echo "<tr class=\"profilerow\" onmouseover=\"srrowOverEffect(this)\" onmouseout=\"srrowOutEffect(this)\"><td>";
    echo "<img src=images/icons/delete.gif width=16 height=16 border=0></td><td><a class=small href=\"profiles.php?editconf=$conf&del=9&fldname=ipnumber&from=$from&to=$to&fldvalue=$ipnumber\">"._REMOVE_THIS_IPNUMBER."</a><br>";
    echo "</td></tr>";

    echo "<tr><td colspan=2><hr noshade size=1></td></tr>";
    
    echo "<tr class=\"profilerow\" onmouseover=\"srrowOverEffect(this)\" onmouseout=\"srrowOutEffect(this)\"><td>";    
    echo "<img src=images/icons/cancel.gif width=16 height=16 border=0></td><td><a id=\"closeme\" class=small href=\"#close\">"._CANCEL_CLOSE_MENU."</a><br>";
    echo "</td></tr></table>";     
       
}
?>
