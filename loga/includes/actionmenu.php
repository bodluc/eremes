<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 

$conf = @$_GET["conf"];
$from = @$_GET["from"];
$to = @$_GET["to"];
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

include "../common.inc.php"; 
$profile = new SiteProfile($conf);

$url = @$_GET["url"];

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
    echo "<tr><td colspan=2><b>$purl:</td></tr></table>";
    echo "<table width=220 border=0 cellspacing=0 cellpadding=3>";
    echo "<tr><td colspan=2><hr noshade size=1></td></tr>"; 
    
    echo "<tr class=\"profilerow\" onmouseover=\"srrowOverEffect(this)\" onmouseout=\"srrowOutEffect(this)\"><td>";
    echo "<img src=images/icons/chart_line.gif width=16 height=16 alt='Page Trends over time' border=0></td><td><a class=small href=\"trends.php?source=".$url."&from=$trendfrom&to=$to&conf=$conf\">"._SHOW_PAGE_TRENDS."</a><br>";
    echo "</td></tr>";
   
    echo "<tr class=\"profilerow\" onmouseover=\"srrowOverEffect(this)\" onmouseout=\"srrowOutEffect(this)\"><td>";
    echo "<img src=images/icons/page_analysis.gif width=16 heigh=16 border=0></td><td><a class=small href=\"page.php?page=".$url."&from=$from&to=$to&conf=$conf\">"._GO_TO_PAGE_ANALYSIS."</a><br>";
    echo "</td></tr>";
   
    echo "<tr class=\"profilerow\" onmouseover=\"srrowOverEffect(this)\" onmouseout=\"srrowOutEffect(this)\"><td>";
    echo "<img src=images/icons/eye.gif width=16 heigh=16 border=0></td><td><a class=small href=\"page.php?page=".$url."&from=$from&to=$to&conf=$conf&visual=1\" target=_blank>"._GO_TO_PAGE_ANALYSIS_VISUAL_OVERLAY."</a><br>";
    echo "</td></tr>";
    
    if ($addcond=="") {
        echo "<tr class=\"profilerow\" onmouseover=\"srrowOverEffect(this)\" onmouseout=\"srrowOutEffect(this)\"><td>";
        echo "<img src=images/icons/page_attach.gif width=16 height=16 alt='Page Details' border=0></td><td><a class=small href=\"reports.php?search=".$url."&searchmode=like&from=$from&to=$to&conf=$conf&labels=Top Pages - Details\">"._SHOW_ALL_PAGE_VARIATIONS."</a><br>";
        echo "</td></tr>";
    }
    echo "<tr class=\"profilerow\" onmouseover=\"srrowOverEffect(this)\" onmouseout=\"srrowOutEffect(this)\"><td>";
    echo "<img src=images/icons/mouse_add.gif width=16 height=16 alt='View Click Trails to this page' border=0></td><td><a class=small href=\"clicktrail.php?cvalue1=".$urlparts[0]."&from=$from&to=$to&conf=$conf&field1=url&condition1=is$addcond\">"._VIEW_CLICK_TRAIL_TO_PAGE."</a><br> ";
    echo "</td></tr>";
    
    echo "<tr class=\"profilerow\" onmouseover=\"srrowOverEffect(this)\" onmouseout=\"srrowOutEffect(this)\"><td width=16>";
    echo "<img src=images/icons/newpage.gif width=16 heigh=16 border=0></td><td width=100%><a class=small href=\"http://$profile->confdomain".urldecode($url)."\" target=_blank>"._GO_TO_WEB_PAGE."</a><br>";
    echo "</td></tr>";
    
    
    echo "<tr><td colspan=2><hr noshade size=1></td></tr>"; 
    
    echo "<tr class=\"profilerow\" onmouseover=\"srrowOverEffect(this)\" onmouseout=\"srrowOutEffect(this)\"><td>";
    echo "<img src=images/icons/filters.gif width=16 height=16 border=0></td><td><a class=small href=filters.php?conf=$conf&field1=url&condition1=is&cvalue1=".$url.">"._CREATE_FILTER_THIS_PAGE."</a><br>";
    echo "</td></tr>";
    
    echo "<tr class=\"profilerow\" onmouseover=\"srrowOverEffect(this)\" onmouseout=\"srrowOutEffect(this)\"><td>";
    //echo "<img src=images/icons/delete.gif width=16 height=16 border=0></td><td><a class=small href=\"javascript:go_profile('To remove this page from future statistics, please add this page to the skip pages filed in your profile, on the Data Collection tab','profiles.php?conf=$conf&editconf=$conf&edit=1&tab=data');\">Remove this Page from the Statistics</a><br>";
    echo "<img src=images/icons/delete.gif width=16 height=16 border=0></td><td><a class=small href=\"profiles.php?editconf=$conf&del=9&from=$from&to=$to&fldname=page&fldvalue=$url\">"._REMOVE_THIS_PAGE."</a><br>";
    echo "</td></tr>";
    
    echo "<tr><td colspan=2><hr noshade size=1></td></tr>";
    
    echo "<tr class=\"profilerow\" onmouseover=\"srrowOverEffect(this)\" onmouseout=\"srrowOutEffect(this)\"><td>";    
    echo "<img src=images/icons/cancel.gif width=16 height=16 border=0></td><td><a id=\"closeme\" class=small href=\"#close\">"._CANCEL_CLOSE_MENU."</a><br>";
    echo "</td></tr></table>";

} else if ($keyword!="") {
    
    echo "<table width=220 border=0 cellspacing=0 cellpadding=3>";
    echo "<tr><td colspan=2><b>$keyword:</b><hr noshade size=1></td></tr>";
    
    echo "<tr class=\"profilerow\" onmouseover=\"srrowOverEffect(this)\" onmouseout=\"srrowOutEffect(this)\"><td>";
    echo "<img src=images/icons/chart_line.gif width=16 height=16 alt='Keyword Trend over time' border=0></td><td><a class=small href=\"trends.php?source=".urlencode($keyword)."&from=$trendfrom&to=$to&conf=$conf&sourcetype=keyword\">"._KEYWORD_OVER_TIME."</a><br>";
    echo "</td></tr>";
    
    
    echo "<tr class=\"profilerow\" onmouseover=\"srrowOverEffect(this)\" onmouseout=\"srrowOutEffect(this)\"><td>";
    echo "<img src=images/icons/mouse_add.gif width=16 height=16 alt='View Click Trails with this keyword' border=0></td><td width=100%><a class=small href=\"clicktrail.php?cvalue1=".urlencode($keyword)."&from=$from&to=$to&conf=$conf&field1=keywords&condition1=is\">"._VIEW_CLICK_TRAILS_KEYWORD."</a><br> ";
    echo "</td></tr>"; 
    
    
    echo "<tr><td colspan=2><hr noshade size=1></td></tr>"; 
    
    echo "<tr class=\"profilerow\" onmouseover=\"srrowOverEffect(this)\" onmouseout=\"srrowOutEffect(this)\"><td>";
    echo "<img src=images/icons/filters.gif width=16 height=16 border=0></td><td><a class=small href=\"filters.php?conf=$conf&field1=keywords&condition1=contains&cvalue1=".urlencode($keyword)."\">"._CREATE_FILTER_KEYWORD."</a><br>";
    echo "</td></tr>";

    echo "<tr><td colspan=2><hr noshade size=1></td></tr>";
    
    echo "<tr class=\"profilerow\" onmouseover=\"srrowOverEffect(this)\" onmouseout=\"srrowOutEffect(this)\"><td>";    
    echo "<img src=images/icons/cancel.gif width=16 height=16 border=0></td><td><a id=\"closeme\" class=small href=\"#close\">"._CANCEL_CLOSE_MENU."</a><br>";
    echo "</td></tr></table>";
    
} else if ($referrer!="") {
    
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
    echo "<tr><td colspan=2><b>". urldecode($showreferrer).":</b><hr noshade size=1></td></tr>";
    
    echo "<tr class=\"profilerow\" onmouseover=\"srrowOverEffect(this)\" onmouseout=\"srrowOutEffect(this)\"><td>";
	if($new_ui == 1) {
		echo "<img src=images/icons/chart_line.gif width=16 height=16 alt='Referrer Trend over time' border=0></td><td><a class=small href=\"trends.php?source=".urlencode($referrer)."&from=$trendfrom&to=$to&conf=$conf&sourcetype=referrer\">"._REFERRER_TREND_OVER_TIME."</a><br>";
	} else {
		echo "<img src=images/icons/chart_line.gif width=16 height=16 alt='Referrer Trend over time' border=0></td><td><a class=small href=\"trends.php?source=".urlencode($referrer)."&from=$trendfrom&to=$to&conf=$conf&sourcetype=referrer\">"._REFERRER_TREND_OVER_TIME."</a><br>";
	}
    echo "</td></tr>";
    
    echo "<tr class=\"profilerow\" onmouseover=\"srrowOverEffect(this)\" onmouseout=\"srrowOutEffect(this)\"><td>";
    echo "<img src=images/icons/mouse_add.gif width=16 height=16 alt='View Click Trails with this referrer' border=0></td><td><a class=small href=\"clicktrail.php?cvalue1=".urlencode($refparts[0])."&field1=referrer&condition1=contains$addcond&from=$from&to=$to&conf=$conf\">"._VIEW_CLICK_TRAILS_REFERRER."</a><br> ";
    echo "</td></tr>";
    
    echo "<tr class=\"profilerow\" onmouseover=\"srrowOverEffect(this)\" onmouseout=\"srrowOutEffect(this)\"><td width=16>";
    echo "<img src=images/icons/newpage.gif width=16 heigh=16 border=0></td><td width=100%><a class=small href=\"".urldecode($showreferrer)."\" target=_blank>"._GO_TO_REFERRER_IN_NEW_WINDOW."</a><br>";
    echo "</td></tr>";   
    
    echo "<tr><td colspan=2><hr noshade size=1></td></tr>"; 
    
    echo "<tr class=\"profilerow\" onmouseover=\"srrowOverEffect(this)\" onmouseout=\"srrowOutEffect(this)\"><td>";
    echo "<img src=images/icons/filters.gif width=16 height=16 border=0></td><td><a class=small href=filters.php?conf=$conf&field1=referrer&condition1=contains&cvalue1=".$referrer.">"._CREATE_FILTER_THIS_REFERRER."</a><br>";
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
    echo "<tr><td colspan=2><b>". $statuscode.":</b><hr noshade size=1></td></tr>";
    
    
    echo "<tr class=\"profilerow\" onmouseover=\"srrowOverEffect(this)\" onmouseout=\"srrowOutEffect(this)\"><td>";
    echo "<img src=images/icons/summary.gif width=16 height=16 alt='"._VIEW_PAGES_WITH_THIS_STATUSCODE."' border=0></td><td><a class=small href=\"reports.php?status=$code&from=$from&to=$to&conf=$conf&labels=$code "._ERROR_REPORT."\">"._VIEW_PAGES_WITH_THIS_STATUSCODE."</a><br> ";
    echo "</td></tr>";
    
    echo "<tr class=\"profilerow\" onmouseover=\"srrowOverEffect(this)\" onmouseout=\"srrowOutEffect(this)\"><td>";
    echo "<img src=images/icons/mouse_add.gif width=16 height=16 alt='"._VIEW_CLICKTRAILS_WITH_THIS_STATUSCODE."' border=0></td><td><a class=small href=\"clicktrail.php?from=$from&to=$to&conf=$conf&field1=status&condition1=is&cvalue1=$code\">"._VIEW_CLICKTRAILS_WITH_THIS_STATUSCODE."</a><br> ";
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
    echo "<tr><td colspan=2>".ucwords($label)." matching: <b>\"". $val."\"</b><hr noshade size=1></td></tr>";
    
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
        $query = "select name as item from ".$profile->tablename_useragents." where name like '%$val%' order by id limit 10";
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
    $visitorid = @$vip[1]; // if we have a visitorid, we're coming from clicktrial.php
   
    echo "<table width=220 border=0 cellspacing=0 cellpadding=3>";
    echo "<tr><td colspan=2><b>". $ipnumber.":</b><hr noshade size=1></td></tr>";
    
    
    echo "<tr class=\"profilerow\" onmouseover=\"srrowOverEffect(this)\" onmouseout=\"srrowOutEffect(this)\"><td>";
    if (!$visitorid) {
        echo "<img src=images/icons/mouse_add.gif width=16 height=16 alt='"._VIEW_CLICKTRAILS_WITH_THIS_IPNUMBER."' border=0></td><td><a class=small href=\"clicktrail.php?from=$from&to=$to&conf=$conf&field1=ipnumber&condition1=is&cvalue1=$ipnumber\">"._VIEW_CLICKTRAILS_WITH_THIS_IPNUMBER."</a><br> ";
    } else {
		if($new_ui == 1) {
			$visitorid_url = "clicktrail.php?conf=$conf";
			$c = 0;
			foreach($_REQUEST as $req_key => $req_val) {
				$visitorid_url .= "&{$req_key}={$req_val}";
				$c++;
			}
			$visitorid_url = urldecode($visitorid_url);
			echo "<img src=images/icons/mouse_add.gif width=16 height=16 alt='"._VIEW_CLICKTRAILS_WITH_THIS_IPNUMBER."' border=0></td><td><a type='_CLICK_TRAILS' name=\"Click Trails\" rel=\"ClickTrails\" class='small open_in_this_dialog quickopen' href=\"{$visitorid_url}\">"._VIEW_CLICKTRAILS_WITH_THIS_IPNUMBER."</a><br> ";
		} else {
			echo "<img src=images/icons/mouse_add.gif width=16 height=16 alt='"._VIEW_CLICKTRAILS_WITH_THIS_IPNUMBER."' border=0></td><td><a class=small href=\"javascript:ClickTrail('$visitorid')\">"._VIEW_CLICKTRAILS_WITH_THIS_IPNUMBER."</a><br> ";
		}
    }
    echo "</td></tr>";
    
    
    echo "<tr class=\"profilerow\" onmouseover=\"srrowOverEffect(this)\" onmouseout=\"srrowOutEffect(this)\"><td width=16>";
    echo "<img src=images/icons/newpage.gif width=16 heigh=16 border=0></td><td width=100%><a class=small href=\"http://www.db.ripe.net/whois?form_type=simple&full_query_string=&searchtext=$ipnumber&do_search=Search\" target=_blank>"._RIPE_WHOIS_LOOKUP."</a><br>";
    echo "</td></tr>";
    
    echo "<tr><td colspan=2><hr noshade size=1></td></tr>"; 
    
    echo "<tr class=\"profilerow\" onmouseover=\"srrowOverEffect(this)\" onmouseout=\"srrowOutEffect(this)\"><td>";
    echo "<img src=images/icons/filters.gif width=16 height=16 border=0></td><td><a class=small href=filters.php?conf=$conf&field1=ipnumber&condition1=is&cvalue1=".$ipnumber.">"._CREATE_FILTER_THIS_IPNUMBER."</a><br>";
    echo "</td></tr>";
    
    echo "<tr class=\"profilerow\" onmouseover=\"srrowOverEffect(this)\" onmouseout=\"srrowOutEffect(this)\"><td>";
    echo "<img src=images/icons/delete.gif width=16 height=16 border=0></td><td><a class=small href=\"profiles.php?editconf=$conf&del=9&from=$from&to=$to&fldname=ipnumber&fldvalue=$ipnumber\">"._REMOVE_THIS_IPNUMBER."</a><br>";
    echo "</td></tr>";

    echo "<tr><td colspan=2><hr noshade size=1></td></tr>";
    
    echo "<tr class=\"profilerow\" onmouseover=\"srrowOverEffect(this)\" onmouseout=\"srrowOutEffect(this)\"><td>";    
    echo "<img src=images/icons/cancel.gif width=16 height=16 border=0></td><td><a id=\"closeme\" class=small href=\"#close\">"._CANCEL_CLOSE_MENU."</a><br>";
    echo "</td></tr></table>";     
       
}
?>
