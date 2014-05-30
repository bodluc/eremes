<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 

require_once "top.php";
include_once("components/geoip/open_geoip.php");

if (!$from) {
	$from   = mktime(0,0,0,date("m"),date("d"),date("Y"));
	$to     = mktime(23,59,59,date("m"),date("d"),date("Y"));
}
if (!@$_REQUEST["dhistory"]) {
    $_REQUEST["dhistory"]="daterange";   
}

$filter = @$_GET["filter"];
$resolveon = @$_GET["resolveon"];

if (!@$_REQUEST["andor"]) {
    $andor="AND";
} else {
    $andor = @$_REQUEST["andor"];
}
if (!@$_REQUEST["limit"]) {
    $limit="100";
} else {
    $limit = @$_REQUEST["limit"];
}

if (isset($_REQUEST["submit"])) {
	if (($_REQUEST["submit"] == "Report") && (isset($_GET["filter_top"]))) {
		$filter = $_GET["filter_top"];
	}
}
$ip = @$_REQUEST["ip"];
$visitorid = @$_REQUEST["visitorid"];
$firstip = @$_REQUEST["firstip"];
$stop = @$_REQUEST["stop"];

if (isset($_REQUEST["trafficsource"])) { $_SESSION["trafficsource"] = $_REQUEST["trafficsource"]; }

if (($visitorid > "") && @$_REQUEST["cvalue1"]=="") {
	$_REQUEST["cvalue1"]=$visitorid;
	$_REQUEST["condition1"]="is";
	$_REQUEST["field1"]="visitorid";
} else if ($ip!="" && @$_REQUEST["cvalue1"]=="") {
   $_REQUEST["cvalue1"]=$ip;
   $_REQUEST["condition1"]="is";
   $_REQUEST["field1"]="ipnumber";
}

PrintLoadingBox(_CLICKTRAILS);  

// date selection
echo "<div class='form1-wrap'><form method=get action=clicktrail.php id=\"form1\" name=\"form1\">";
echo "<table border=0><tr>";
echo "<td width=72><b>"._DATE_RANGE.":</b></td><td><table border=0><tr><td>";
QuickDate($from,$to);
echo "</td><td>";
newDateSelector($from,$to);
echo "<input type=hidden name=conf value=\"$conf\"> ";
echo "<input type=hidden name=ip value=\"$ip\"> ";
echo "<input type=hidden name=visitorid value=\"$visitorid\"> ";
echo "</td><td> "._VISITOR_LIMIT.":</td><td><input type=text size=1 name=limit value=$limit></td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=submit name=submitbut value=Report>  <a id=\"moreoptions\" class=graylink href=\"javascript:moreoptions();\">"._MORE_OPTIONS."</a></td>";
echo "</tr></table></td></tr><tr>";

$fieldselect = LoadFieldSelectFull(); 
echo "<td valign=top><span style=\"position:absolute;margin-top:8px;\"><b>"._SEARCH.":</b></span></td><td>";
?>
    
    <?php
    if (@$_REQUEST["field1"]!="") {
      $fieldselect = SelectField($fieldselect,@$_REQUEST["field1"]);
      ?>
      <table cellpadding=3 border=0><tr>
      <td width=132>(<?php echo _FIELD;?>, <?php echo _CONDITION;?>, <?php echo _VALUE;?>)</td>
      <td><select name="field1"><?php echo $fieldselect;?></select></td>
      <td><select name="condition1">
            <?php echo "<option value=\"".@$_REQUEST["condition1"]."\" SELECTED>".@$_REQUEST["condition1"];?>
            <option value="is"><?php echo _SELECT_IS;?></option>
            <option value="contains"><?php echo _CONTAINS;?></option>
            <option value="nocontain"><?php echo _NOT_CONTAINS;?></option>
            <option value="start"><?php echo _STARTS_WITH;?></option>
            <option value="end"><?php echo _ENDS_WITH;?></option>
            <option value="isnot"><?php echo _SELECT_IS_NOT;?></option>
          </select>
      </td>
      <td><input type=text name="cvalue1" id="cvalue1" value="<?php echo @$_REQUEST["cvalue1"] ?>" <?php echo "onkeyup=\"QBuilderHelpForms(document.form1.elements['field1'].value, event, this.value+'@'+this.id+'@'+document.form1.elements['field1'].value, 'forminput');\" onclick=\"QBuilderHelpForms(document.form1.elements['field1'].value, event, this.value+'@'+this.id+'@'+document.form1.elements['field1'].value, 'forminput');\"";?> autocomplete="off"></td>
      <td>
        <input type=button value=" + " onclick="add('2')">&nbsp;<input type=button value=" - " disabled> 
      </td>
      </tr></table>
      <?php
      $i=2;
    } else {
      $i=1;
    }
    $fieldselect = LoadFieldSelectFull();

    while ($i < 25) {
      if (@$_REQUEST["field$i"]!="") {
        $display="block";
      } else {
        $display="none";
      }
      if ($i==1) {
        echo "<div><table cellpadding=3 border=0><tr><td width=132>("._FIELD.", "._CONDITION.", "._VALUE.")</td>";
      } else if ($i==2) {
        echo "<div id=\"$i\" style=\"display:$display;\"><table cellpadding=3 border=0><tr><td width=132>"._MODE.": ";
        ?>
        <input type=radio name=andor id=AND value="AND" <?php if ($andor=="AND") { echo "checked"; }?> > <label for=AND><?php echo _AND?></label>
        <input type=radio name=andor id=OR value="OR" <?php if ($andor=="OR") { echo "checked"; }?> > <label for=OR><?php echo _CAP_OR?></label>
        
        <?php        
        echo "</td>";          
      } else {
        echo "<div id=\"$i\" style=\"display:$display;\"><table cellpadding=3 border=0><tr><td width=132></td>";
      }
      
      ?>
      <td><select id="<?php echo "field".$i; ?>" name="<?php echo "field".$i; ?>"><?php $fieldselect = SelectField($fieldselect,@$_REQUEST["field$i"]); echo $fieldselect;?></select></td>
      <td><select name="<?php echo "condition".$i; ?>">
            <option value="is" <?php if (@$_REQUEST["condition$i"]=="is") { echo "selected"; }?>><?php echo _SELECT_IS?></option>
      	    <option value="contains" <?php if (@$_REQUEST["condition$i"]=="contains") { echo "selected"; }?>><?php echo _CONTAINS?></option>
            <option value="nocontain" <?php if (@$_REQUEST["condition$i"]=="nocontain") { echo "selected"; }?>><?php echo _NOT_CONTAINS?></option>
            <option value="start" <?php if (@$_REQUEST["condition$i"]=="start") { echo "selected"; }?>><?php echo _STARTS_WITH?></option>
            <option value="end" <?php if (@$_REQUEST["condition$i"]=="end") { echo "selected"; }?>><?php echo _ENDS_WITH?></option>
            <option value="isnot" <?php if (@$_REQUEST["condition$i"]=="isnot") { echo "selected"; }?>><?php echo _SELECT_IS_NOT?></option>
          </select>
      </td>
      <td><input type=text name="<?php echo "cvalue".$i; ?>" id="<?php echo "cvalue".$i; ?>" value="<?php echo @$_REQUEST["cvalue$i"];?>" <?php echo "onkeyup=\"QBuilderHelpForms(document.form1.elements['field$i'].value, event, this.value+'@'+this.id+'@'+document.form1.elements['field$i'].value, 'forminput');\" onclick=\"QBuilderHelpForms(document.form1.elements['field$i'].value, event, this.value+'@'+this.id+'@'+document.form1.elements['field$i'].value, 'forminput');\"";?> autocomplete="off"></td>
      <td>
        <input type=button value=" + " onclick="add('<?php $i++; echo $i; ?>')">&nbsp;<input type=button value=" - " onclick="remove2('<?php echo ($i-1); ?>')" <?php if ($i==2) { echo "disabled"; }?>> 
      </td>
      </tr></table></div>
      <?php
    }
    ?> 
    
<?php

echo "</td></tr></table>";

echo "<div id=\"advancedUI\" style=\"display:none;\"><table><tr>";

echo "<td colspan=2><table border=0><tr><td><b>"._FILTERS.": </b></td><td colspan=2>";

echo printTrafficSourceSelect();

echo  "</td></tr><tr><td colspan=3><img src=images/pixel.gif height=5></td></tr>";
echo "<tr><td><b>"._RESOLVE_IP."   :</b></td><td><select name=\"resolveon\">";
echo "<option value=\"1\" ";
if (@$_REQUEST["resolveon"]=="1") { echo "selected"; }
echo ">"._ANSWER_NO."</option>";
echo "<option value=\"0\" ";
if (@$_REQUEST["resolveon"]!="1") { echo "selected"; }
echo ">"._ANSWER_YES."</option></select>";
echo "<td>&nbsp;&nbsp;&nbsp;<b>"._DISPLAY.":</b> <select name=dhistory>";
echo "<option value=\"all\"";
echo ">"._FULL_VISITOR_HISTORY."</option>";
echo "<option value=\"daterange\"";
if (@$_REQUEST["dhistory"]=="daterange") { echo "selected"; }
echo ">"._SHOW_HISTORY_IN_DATE_RANGE."</option></select>";

echo "</td></tr></table></td></tr></table></div><hr noshade size=1 width=100% style=\"float:left;\"></div><table border=0 width=100%  style=\"float:left;\"><tr><td>";

if (@$_REQUEST["cvalue1"]!="") {
  $i=1;
  $andor=@$_REQUEST["andor"];
  while ($i < 25) {
    $cvalue="";
    $cvalue=@$_REQUEST["cvalue$i"];
    if ($cvalue!="") {
      $field=@$_REQUEST["field$i"];
      if ($field=="") {
        echoWarning("Unknown type: ".$cvalue."<br>Please select a field in the form above!");
        exit();    
      }
      $condition=@$_REQUEST["condition$i"];
      if ($field=="keywords") {
      }
      if ($field=="refparams" || $field=="params") {
        $cvalue=str_replace(" ","+",$cvalue);
      }
       
      //make sql string
      if ($condition=="contains") {
         $op = "$field LIKE '%$cvalue%'";
      } else if ($condition=="nocontain") {
        $op = "$field NOT LIKE '%$cvalue%'";
      } else if ($condition=="start") {
        $op = "$field LIKE '$cvalue%'";
      } else if ($condition=="end") {
        $op = "$field LIKE '%$cvalue'";
      } else if ($condition=="is") {
        $op = "$field ='$cvalue'";
      } else if ($condition=="isnot") {
        $op = "$field !='$cvalue'";
      }
      if ($i==1) {
        $sqlstring=" $op";
      } else {
        $sqlstring.=" $andor $op";
      }
    }
    $i++;
  }
  $filter=1;
} 

//we need to rewrite the sqlstring to the new style tables
$joincond="";
$addtables="";
if (@$sqlstring) {

    if (strpos("  ".$sqlstring," url ")!=FALSE) {
        $sqlstring=str_replace("url =","u.url=",$sqlstring);
        $sqlstring=str_replace("url !=","u.url!=",$sqlstring);
        $sqlstring=str_replace("url LIKE","u.url LIKE",$sqlstring);
        $sqlstring=str_replace("url NOT LIKE","u.url NOT LIKE",$sqlstring); 
        $addtables=",$profile->tablename_urls as u";
        $joincond="and a.url=u.id";       
    }

    if (strpos("  ".$sqlstring," params ")!=FALSE) {
        $sqlstring=str_replace("params =","up.params=",$sqlstring);
        $sqlstring=str_replace("params !=","up.params!=",$sqlstring);
        $sqlstring=str_replace("params LIKE","up.params LIKE",$sqlstring);
        $sqlstring=str_replace("params NOT LIKE","up.params NOT LIKE",$sqlstring);
        $addtables.=",$profile->tablename_urlparams as up";
        $joincond.=" and a.params=up.id";       
    }

    if (strpos("  ".$sqlstring," referrer ")!=FALSE) {
        //echo "rewriting referrer";
        $sqlstring=str_replace("referrer =","r.referrer=",$sqlstring);
        $sqlstring=str_replace("referrer !=","r.referrer!=",$sqlstring);
        $sqlstring=str_replace("referrer LIKE","r.referrer LIKE",$sqlstring);
        $sqlstring=str_replace("referrer NOT LIKE","r.referrer NOT LIKE",$sqlstring);
        $addtables.=",$profile->tablename_referrers as r";
        $joincond.=" and a.referrer=r.id";       
    }

    if (strpos("  ".$sqlstring," refparams ")!=FALSE) {
        $sqlstring=str_replace("refparams =","rp.params=",$sqlstring);
        $sqlstring=str_replace("refparams !=","rp.params!=",$sqlstring);
        $sqlstring=str_replace("refparams LIKE","rp.params LIKE",$sqlstring);
        $sqlstring=str_replace("refparams NOT LIKE","rp.params NOT LIKE",$sqlstring);
        $addtables.=",$profile->tablename_refparams as rp";
        $joincond.=" and a.refparams=rp.id";       
    }

    if (strpos("  ".$sqlstring," keywords ")!=FALSE) {
        $sqlstring=str_replace("keywords =","k.keywords=",$sqlstring);
        $sqlstring=str_replace("keywords !=","k.keywords!=",$sqlstring);
        $sqlstring=str_replace("keywords LIKE","k.keywords LIKE",$sqlstring);
        $sqlstring=str_replace("keywords NOT LIKE","k.keywords NOT LIKE",$sqlstring);
        $addtables.=",$profile->tablename_keywords as k";
        $joincond.=" and a.keywords=k.id";       
    }
    if (strpos("  ".$sqlstring," ipnumber ")!=FALSE) {
        $sqlstring=str_replace("ipnumber =","v.ipnumber=",$sqlstring);
        $sqlstring=str_replace("ipnumber !=","v.ipnumber!=",$sqlstring);
        $sqlstring=str_replace("ipnumber LIKE","v.ipnumber LIKE",$sqlstring);
        $sqlstring=str_replace("ipnumber NOT LIKE","v.ipnumber NOT LIKE",$sqlstring);
        //$addtables.=",$profile->tablename_visitorids as v";
        //$joincond.=" and a.visitorid=v.id";       
    }
    if (strpos("  ".$sqlstring," useragent ")!=FALSE) {
        $sqlstring=str_replace("useragent =","ua.useragent=",$sqlstring);
        $sqlstring=str_replace("useragent !=","ua.useragent!=",$sqlstring);
        $sqlstring=str_replace("useragent LIKE","ua.useragent LIKE",$sqlstring);
        $sqlstring=str_replace("useragent NOT LIKE","ua.useragent NOT LIKE",$sqlstring);
        //$addtables.=",$profile->tablename_visitorids as v";
        //$joincond.=" and a.visitorid=v.id";       
    }
}     
if ($filter) {
	echo "<table cellpadding=2><tr><td valign=top>\n";
	if (@$sqlstring!="") {
        $query="select v.visitorid as visitorid, max(v.ipnumber) ipnumber,count(*) as hits, FROM_UNIXTIME(min(timestamp),'%d-%b-%Y %a') as day,max(country) country, max(ua.useragent) AS useragent from $profile->tablename as a left outer join ".$profile->tablename_useragents." ua on (useragentid = ua.id),$profile->tablename_visitorids as v $addtables where timestamp >=$from and timestamp <=$to and a.visitorid=v.id $joincond and ($sqlstring) and a.crawl='0' group by a.visitorid order by timestamp desc limit $limit";
        $query = subsetDataToSourceID($query);
        $logfile = $db->Execute($query);
        //echo $query;
        //echo "cval method....select ipnumber,FROM_UNIXTIME(timestamp,'%d-%b-%Y %a') as day,country,useragent,status from $profile->tablename where timestamp >=$from and timestamp <=$to and $sqlstring group by ipnumber order by timestamp desc limit 500";
    } else {
        //this one doesn't happen right
        echo "euhh";
        /*
        $query="select v.visitorid, min(v.ipnumber) ipnumber,FROM_UNIXTIME(min(timestamp),'%d-%b-%Y %a') as day,max(country) country,max(AGENTS.name) useragent from $profile->tablename as a,$profile->tablename_visitorids as v left outer join ".TBL_USER_AGENTS." AGENTS on (useragentid = AGENTS.id) where a.visitorid=v.id and (concat(u.url,'?',p.params) like '%$filter%' or concat(referrer,'?',refparams) like '%$filter%') and timestamp >=$from and timestamp <=$to and crawl=0 group by visitorid order by timestamp desc limit $limit";
        $query = subsetDataToSourceID($query);
        $logfile = $db->Execute($query);
        */
    }
	$i=0;
	echo "<table cellpadding=3 width=100% cellspacing=0 border=0 align=center class=smallborder>\n";
	echo "<tr><td colspan=2 width=200><img src=\"images/icons/user.gif\" width=16 height=16 align=left> <b>"._VISITOR_LIST.":<br></b></td></tr>\n";
	while ($loglines=$logfile->FetchRow()) {
		// If a visitor ID wasn't passed in, then select the first one as the one we want to highlight.
		if (!$visitorid) {
			$visitorid = $loglines["visitorid"];
		}
			$s = "";
			if ($loglines["visitorid"] == $visitorid) {
				 $s = "bgcolor=#CCFFCC";
			}
			if ($loglines["country"]) {
				$cparts=explode(", ", $loglines["country"]);
				$ccode = strtolower(((count($cparts) > 1) && ($cparts[1] > "")) ? $cparts[1] : $cparts[0]);
			} else {
				$ccode = "";
			}
			if (isset($gi)) {
				$area=geoip_record_by_addr($gi, $loglines["ipnumber"]);
				if ($area) {
					$city=$area->country_name .", " . $area->city;
				} else {
					$city = "City unknown";
				}
				$image= "<img hspace=3 width=14  height=11 src=\"images/flags/$ccode.png\" border=0>";
			} else {
				$image = "";
				$city = "";
				$area = "";
			}
			echo "<tr><td $s>". $image ."</td>";
			echo "<td title=\"".$loglines["useragent"]." $city\" $s>";
            //echo "<a class=navlinks href=\"javascript:ClickTrail('".$loglines["visitorid"]."')\">".$loglines["ipnumber"]."</a><br>\n";
            echo "<span><a class=navlinks href=\"#\" onclick=\"popupMenu(event, '".$loglines["ipnumber"].";".$loglines["visitorid"]."','ipnumber')\">".$loglines["ipnumber"]."</a></span><br>\n";
            
            echo "<font color=gray size=1>".$loglines["day"]."</font><br>";
            
            if ($resolveon!=1) {
                echo "<div id=\"ip$i\" style=\"font-size:10px;color:gray;\">"._RESOLVING."...</div>\n";
                echo "<script language=\"javascript\" type=\"text/javascript\"> resolveIP('".$loglines["ipnumber"]."', 'ip$i'); </script>\n";  
            } else {
               echo "<font color=gray size=1>".$loglines["ipnumber"]."</font>";
            }
            
            echo "</td></tr>\n";
		$i++;
	}
	echo "</table><P><div align=center>$i "._VISITORS_FOUND.".</div>";
    
    echo "</td><td valign=top>";
	//echo "<P>$i Visitors found.";
    if ($i==0) {
	    $stop=1;
    }
} 	

if (($visitorid) || ($ip)) {
	$lasttimestamp = 0;
	if ($visitorid) {
		$selector = "v.visitorid = '$visitorid'";
	} else {
		$selector = "v.ipnumber = '$ip'";
	}
	
  if (@$_REQUEST["dhistory"]=="daterange") {
  	$selector .= " and timestamp >=$from and timestamp <=$to";
	}
	$query = $db->Execute("select timestamp,v.ipnumber,u.url,up.params,r.referrer,rp.params refparams,ua.useragent,status,crawl from $profile->tablename as a left outer join ".$profile->tablename_useragents." ua on (useragentid = ua.id),$profile->tablename_urls as u,$profile->tablename_urlparams as up,$profile->tablename_referrers as r,$profile->tablename_refparams as rp,$profile->tablename_visitorids as v where a.visitorid=v.id and $selector and a.url=u.id and a.params=up.id and a.referrer=r.id and a.refparams=rp.id order by timestamp");
	
	$i=0;
	$gr=1;
	while ($loglines=$query->FetchRow()) {
		// Put in the header row...
		if ($i==0) {
            $ip= $loglines['ipnumber']; 
			if (isset($gi)) {
				$area=geoip_record_by_addr($gi, $loglines["ipnumber"]);
				if ($area) {
					$city=$area->country_name .", " . $area->city;
				} else {
					$city = _UNKNOWN_CITY;
				}
			}
			?>
			<table cellspacing=0 cellpadding=4 border=0 width=100%>
			<tr height=28><td colspan=15 class=toplineyellow bgcolor=#ffffcc>
			<font size="+1" color="<?php echo $tableheaderfontcolor; ?>">
			Click-Trail Analysis for <?php echo $ip ."</font> ".$loglines["useragent"] . ", ".@$city; if ($loglines["crawl"]!=0) { echo " [Bot/Crawler]"; } 
            echo " <sup><a target=_blank class=greenlink href=\"http://www.db.ripe.net/whois?form_type=simple&full_query_string=&searchtext=$ip&do_search=Search\">"._RIPE_WHOIS_LOOKUP."</a></sup>";
            ?>
			</td></tr>
			<?php 
			//echo "<table cellpadding=3 cellspacing=0 border=1 bgcolor=#f0f0f0 align=center>";
			echo "<tr bgcolor=silver><td><b>"._LOGTIME."</b></td><td><b>"._REFERRING_PAGE."</b></td><td></td><td><b>"._REQUESTED_PAGE."</b></td><td><b>"._STATUS."</b></td><td><b>"._TIME_SPENT."</b></td></tr>";
		}
					
		$ipnumber=$loglines["ipnumber"];
		$logtimestamp=$loglines["timestamp"];
		$logtime=date("D, d-m-Y H:i:s",$logtimestamp);
		$geturl=$loglines["url"];
		if ($loglines["params"]!="") {
			$geturl=$loglines["url"].$loglines["params"];
		}
		$refurl=$loglines["referrer"];
		if ($loglines["refparams"]!="?") {
			$refurl=$loglines["referrer"].$loglines["refparams"];
		}
		if (strlen($geturl) > 50) {
			$geturln=substr($geturl,0,50) . "...";
		} else {
			$geturln=$geturl;
		}
		if (strlen($refurl) > 50) {
			$refurln=substr($refurl,0,50) . "...";
		} else {
			$refurln=$refurl;
		}
		$timespent=$logtimestamp-$lasttimestamp;
		$min=number_format(($timespent/60),1);
		if (($logtimestamp-$lasttimestamp) > 72000) {
			$logtime="<b>$logtime</b>";
			$newday=1;
		} else {
			$newday=0;
		}

		$lasttimestamp=$logtimestamp;

		if ($newday==0) {
			echo "<td>$timespent sec ($min min)</td></tr>";
		} else {
			if ($i!=0){
		  	echo "<td><font color=Red>"._USER_LEFT."</font></td></tr>";
			}
		}			

		if (strpos($refurl, $profile->confdomain)==FALSE) {
			if (strpos($refurl, "G]")!=FALSE) {
				$gsyn=substr($refurl,3);
				$refurln=$gsyn;
				$gsyn="<img src=images/google.png border=0 alt='Via Google Ads: $refurl'>&nbsp;";
				$refurl=$refurln;
			} else {
				$gsyn="";
			}
			//$refurln="$gsyn<a class=greenlink2 href=\"javascript:menu('".urlencode($refurl)."','$from', '$to', '$conf', 'referrer', 'ref$i');\">$refurln</a>";
			$refurln="$gsyn<a class=greenlink2 href=\"\" onclick=\"popupMenu(event, '".urlencode($refurl)."', 'referrer');\">$refurln</a>";
			
		}
		if ($gr==3) {
			$gr=1;
			$bgcol="bgcolor=#F8F8F8";
		} else {
			$gr++;
			$bgcol="";
		}
		// this is to make the searched element stick out, but it ned work (all the other cvalues should be check, maybe load in array and do array search)
		if ($geturl==@$_REQUEST["cvalue1"] || $refurl==@$_REQUEST["cvalue1"]) {
			$bgcol="bgcolor=yellow";
		}
		echo "<tr $bgcol class=small><td>$logtime</td><td title=\"$refurl\"><font size=1>$refurln</font><br><div id=\"ref$i\" class=actionmenu style=\"display:none;\"></div></td><td><img src=images/icons/link_go.gif width=16 height=16></td><td title=\"$geturl\"><a href=\"\" onclick=\"popupMenu(event, '".urlencode($geturl)."', 'page');\">$geturln</a><br></div></td><td>".$loglines['status']."</td>";
		$i++;
	}
	echo "<td><font color=Red>"._USER_LEFT."</font></td></tr>";

	echo "</table>";
	$stop=1;
}		
			
if (!$stop) {
?>
<table cellpadding=8 border=0 width=100%>
<tr>
  <td class=smallborder valign=top width=150><img src="images/icons/user.gif" width=16 height=16 align=left> <b><?php echo _VISITOR_LIST?><br></b><?php echo _NONE?> <?php echo _SELECTED?></td>
	<td valign=top>
	<table cellspacing=0 cellpadding=0 border=0 width=100%>
	<tr><td valign=top>
    <h3 style="margin-top:0px;"><?php echo _CLICK_TRAIL_ANALYSIS?> </h3>
    
	<?php echo _EXPLAIN_CLICK_TRAILS?>
    
    <div style="margin-left:15px;background:#CCCCCC;padding:10px;"><?php echo _TIP?></div>
    
    
	</td></tr></table>
	
</td>
<td valign=top width=250 style='border-style : solid; border-width:0px; border-left-width : thin;'>
<?php echo _LIST_SEARCHABLE_FIELDS."<br/><br/>"?>
   <?php echo _EXPLAIN?>
</td>
</tr>
</table>

<?php
}
if (file_exists("components/geoip/GeoLiteCity.dat")) {
geoip_close($gi);
}
?>
</td>
</tr>
</table>
<?php
 if (@$_SESSION["trafficsource"] || @$_REQUEST["dhistory"]=="all" || @$_REQUEST["resolveon"]=="1") {
    echo "<script language=\"javascript\" type=\"text/javascript\">moreoptions();</script>";
}
?>
</body>
</html>
