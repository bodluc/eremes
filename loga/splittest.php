<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
require "common.inc.php";

$submit = @$_REQUEST["submit"];
$targeta = @$_REQUEST["targeta"];
$pagea = @$_REQUEST["pagea"];
$targetb = @$_REQUEST["targetb"];
$pageb = @$_REQUEST["pageb"];
$filter = @$_REQUEST["filter"];
$sptcook = @$_REQUEST["sptcook"];
$status = @$_REQUEST["status"];
$agent = @$_REQUEST["agent"];

$rstart=getmicrotime();
require "top.php";
require "queries.php";

if ($submit) {
        if (!$targeta && !$pagea) {
        }else {
             $testurls="$pagea,$targeta,$pageb,$targetb";
             SetCookie("sptcook",$testurls,time() + 8640000,"/",$_SERVER["HTTP_HOST"],0);
        }
        if ($filter==false) {
            $filter=0;
        } else {
            $filter=0;
            $checked="checked";
        }    
        if (!$from) {
            //$from   = mktime(0,0,0,$fmonth,$fday,$fyear);
            //$to     = mktime(23,59,59,$tmonth,$tday,$tyear);
        }
} else {
        $filter = false;
        $checked="";
        //$from   = mktime(0,0,0,date("m"),01,date("Y"));
        //$to     = mktime(23,59,59,date("m"),date("d"),date("Y"));
}
// Build Screen

// Create Report Area
	
?>
<SCRIPT language="JavaScript">
function submitform()
{
  document.report.submit();
}
</SCRIPT> 
<?php
if (!$targeta && !$pagea) {
	 if ($sptcook) {
			 $tuparts=explode(",", $sptcook);
			 $targeta=trim($tuparts[1]);
			 $pagea=trim($tuparts[0]);
			 $targetb=trim($tuparts[3]);
			 $pageb=trim($tuparts[2]);
			 //echo "From cookie: $pagea,$targeta,$pageb,$targetb";
	 } else {
			 $targeta=trim($profile->targets[0]);
			 $pagea="/";
			 $targetb=trim($profile->targets[0]);
			 $pageb="/";
	}
}

$tabletotalcolor="silver";
$tableheadercolor="d5ffd5";
$tableheaderfontcolor="black";
$tablemaincolor="white";
//$pagacolor=FFFFCC;
//$pagbcolor=DBEDF9;
$pagacolor="#F8F8F8";
$pagbcolor="#F8F8F8";

echo "<div style=\"position:relative;width:100%;z-index:10;float:left;\"><form method=get action=splittest.php id=\"form1\" name=\"form1\">";
    echo "<table border=0><tr><td><b>"._DATE_RANGE.": </b>";
    QuickDate($from,$to);
    echo "</td><td>";
  newDateSelector($from,$to);
    echo "<input type=hidden name=conf value=\"$conf\">";
echo "<input type=hidden name=status value=\"$status\">";
echo "<input type=hidden name=labels value=\"$labels\">";
echo "<input type=hidden name=agent value=\"$agent\">";
    echo "</td><td>";  
  
  echo "</td><td><input type=submit name=submitbut value=Report><input type=hidden name=but value=Report>";
  echo "</td></tr></table></div><hr noshade size=1 width=100% style=\"float:left;\">";
  /*
echo "<table cellpadding=8 border=0><tr><td><form method=get action=splittest.php><b>Date Range:</b><br><table><tr><td>";
DateSelector($from,$to);
echo "<input type=hidden name=conf value=\"$conf\">";
echo "<input type=hidden name=status value=\"$status\">";
echo "<input type=hidden name=labels value=\"$labels\">";
echo "<input type=hidden name=agent value=\"$agent\">";
echo "</td><td title=\"Select a Quick Date range\">";
QuickDate($from,$to);
echo "</td><td><input type=submit name=submit value=Report></td></tr></table></td></tr></table>";
*/
?>
<script language="JavaScript">
		function helpbox(spot) {
			if (spot.style.display=="block") {
				spot.style.display="none";
			} else {
				spot.style.display="block";
			}
		}
</script>
<?php
echo "<table cellpadding=8 border=0>";
echo "<tr><td valign=top>";
echo "<table cellpadding=4 border=0 cellspacing=0>";

echo "<tr><td valign=top class=toplinegreen colspan=2><font size=+1>"._SPLIT_TEST_RESULTS."</font>&nbsp;<a href=\"javascript:helpbox(helptxt)\" class=greenlink><sup>"._HELP."</sup></a>";
?>
		<div id="helptxt" style="display : none; line-height : 18px; position : relative;">
		<?php echo _SPLIT_TEST_RESULTS_HELP;?>
		<div class="indentbody"><a href="javascript:helpbox(helptxt)" class=graylink><?php echo _CLOSE_HELP_TEXT;?></a></div>
		</div>
<?php
echo "</td></tr>";
echo "<tr><td valign=top bgcolor=$pagacolor class=insetborder1>";

echo "<table cellpadding=4><tr><td><font size=+1>"._PAGE." A:</font></td><td> <input type=text name=pagea value=\"$pagea\"></td></tr>";
echo "<tr><td><font size=+1>"._TARGET." A: </font></td><td><input type=text name=targeta value=\"$targeta\"></td></tr></table>";

echo "</td><td valign=top bgcolor=$pagbcolor class=insetborder1>";

echo "<table cellpadding=4><tr><td><font size=+1>"._PAGE." B:</font></td><td> <input type=text name=pageb value=\"$pageb\"></td></tr>";
echo "<tr><td><font size=+1>"._TARGET." B: </font></td><td><input type=text name=targetb value=\"$targetb\"></td></tr></table>";
echo "</form>";

$nicefrom = date("D, d M Y / H:i",$from);
$niceto = date("D, d M Y / H:i",$to);
//echo " - <font size=-1><b>from</b> $nicefrom <b>to</b> $niceto</font><P>";

// 1. Get page A stats
// 2. Get target A stats based on direct conversions (refferer based)
// 3. Repeat step 2 based on total conversion (ai!)
// 4. Display stats & conversion 
// 4. repeat 1,2,3 for page b


// lets het the page and target id's
$query="select id from $profile->tablename_urls where url='$pagea'";
$result=$db->Execute($query);
$data=$result->FetchRow();
$pagea_id=$data["id"];

$query="select id from $profile->tablename_urls where url='$targeta'";
$result=$db->Execute($query);
$data=$result->FetchRow();
$targeta_id=$data["id"];

$query="select id from $profile->tablename_urls where url='$pageb'";
$result=$db->Execute($query);
$data=$result->FetchRow();
$pageb_id=$data["id"];

$query="select id from $profile->tablename_urls where url='$targetb'";
$result=$db->Execute($query);
$data=$result->FetchRow();
$targetb_id=$data["id"];

echo "</td></tr><tr><td bgcolor=$pagacolor class=insetborder>";

//$query="select count(distinct visitorid) as users from $profile->tablename as a,$profile->tablename_urls as u,$profile->tablename_urlparams as up where timestamp >=$from and timestamp <=$to and concat(u.url,up.params) like '$targeta%' and a.url=u.id and a.params=up.id and (status=200 or status=302) and crawl=0";
$query="select count(distinct visitorid) as users from $profile->tablename where timestamp >=$from and timestamp <=$to and url='$targeta_id' and (status=200 or status=302) and crawl=0"; 


$result=$db->Execute($query);
$data=$result->FetchRow();
$conversions=$data["users"];
//echo "Total visitors to target page A:" .$data[users] . "<P>";

//$query="select count(distinct visitorid) as users from $profile->tablename as a,$profile->tablename_urls as u,$profile->tablename_urlparams as up where timestamp >=$from and timestamp <=$to and concat(u.url,up.params) like '$pagea%' and a.url=u.id and a.params=up.id and (status=200 or status=302) and crawl=0";
$query="select count(distinct visitorid) as users from $profile->tablename where timestamp >=$from and timestamp <=$to and url='$pagea_id' and (status=200 or status=302) and crawl=0";
//echo $query . "<P>";
$result=$db->Execute($query);
$data=$result->FetchRow();
$pagea_total=$data["users"];

//direct
$query="select count(distinct visitorid) as users from $profile->tablename as a, $profile->tablename_referrers as r where timestamp >=$from and timestamp <=$to and url='$targeta_id' and a.referrer=r.id and r.referrer like '%$profile->confdomain$pagea' and (status=200 or status=302) and crawl=0";
//$query="select count(distinct visitorid) as users from $profile->tablename where timestamp >=$from and timestamp <=$to and concat(url,params)='$targeta' and referrer like '%$profile->confdomain$pagea' and (status=200 or status=302) and crawl=0"; 
//echo $query . "<P>";
$result=$db->Execute($query);
$data=$result->FetchRow();
$targeta_total=$data["users"];

//direct + indirect
//select count(distinct l.visitorid) from logaholicsite as l, logaholicsite as l2 where l.visitorid=l2.visitorid and l.url='/demo.html' and l2.url='/demo_ok.html' and l.timestamp < l2.timestamp;

$query="select count(distinct l2.visitorid) as users from $profile->tablename as l, $profile->tablename as l2 where l.timestamp >=$from and l2.timestamp <=$to and l.visitorid=l2.visitorid and l.url='$pagea_id' and l2.url='$targeta_id' and l.timestamp < l2.timestamp and (l.status=200 or l.status=302) and l.crawl=0";
//echo $query . "<P>";
$result=$db->Execute($query);
$data=$result->FetchRow();
$targeta_indirect_total=$data["users"];

echo "<table><tr><td>"._PAGE." A: <b>$pagea</b></td></td><td>"._USERS."</td><td>"._CONVERSION."</td></tr>";
echo "<tr bgcolor=FFFFFF><td>"._PAGE." A "._VISITORS.":</td><td> $pagea_total</td><td>&nbsp;</td></tr>";
$cnva=0;
if ($pagea_total > 0) {
	$cnva=number_format(($targeta_total/$pagea_total*100),2);
}
echo "<tr><td>"._PAGE." A "._DIRECT_CONVERSIONS.":</td><td> $targeta_total</td><td> $cnva %</td></tr>";
//$targeta_indirect_total=$targeta_indirect_total-$targeta_total;
if ($pagea_total > 0) {
 $cnva=0;
 $cnva=number_format(($targeta_indirect_total/$pagea_total*100),2);
}
echo "<tr bgcolor=FFFFFF><td>"._PAGE." A "._TIMELAPSE_CONVERSIONS.":</td><td> $targeta_indirect_total</td><td>$cnva %</td></tr>";
if ($conversions > 0) {
	$salesp=number_format(($targeta_indirect_total/$conversions*100),2);
} else {
	$salesp = "0";
}
echo "<tr><td>"._PAGE." A "._WAS_VIEWED_BY." $salesp % "._OF." $conversions "._TOTAL_CONVERTED_USERS."</td></tr></table>";

echo "</td><td bgcolor=$pagbcolor class=insetborder>";

$query="select count(distinct visitorid) as users from $profile->tablename where timestamp >=$from and timestamp <=$to and url='$targetb_id' and (status=200 or status=302) and crawl=0";
$result=$db->Execute($query);
$data=$result->FetchRow();
$conversions="";
$conversions=$data["users"];
//echo "Total visitors to target page B:" .$data[users] . "<P>";

$query="select count(distinct visitorid) as users from $profile->tablename where timestamp >=$from and timestamp <=$to and url='$pageb_id' and (status=200 or status=302) and crawl=0";
//echo $query . "<P>";
$result=$db->Execute($query);
$data=$result->FetchRow();
$pageb_total=$data["users"];

//direct
$query="select count(distinct visitorid) as users from $profile->tablename as a, $profile->tablename_referrers as r where timestamp >=$from and timestamp <=$to and url='$targetb_id' and r.referrer like '%$profile->confdomain$pageb' and a.referrer=r.id and (status=200 or status=302) and crawl=0";
//echo $query . "<P>";
$result=$db->Execute($query);
$data=$result->FetchRow();
$targetb_total=$data["users"];

//direct + indirect
//select count(distinct l.visitorid) from $profile->tablename as l, $profile->tablename as l2 where l.visitorid=l2.visitorid and l.url='/demo.html' and l2.url='/demo_ok.html' and l.timestamp < l2.timestamp;
$query="select count(distinct l2.visitorid) as users from $profile->tablename as l, $profile->tablename as l2 where l.timestamp >=$from and l2.timestamp <=$to and l.visitorid=l2.visitorid and l.url='$pageb_id' and l2.url='$targetb_id' and l.timestamp < l2.timestamp and (l.status=200 or l.status=302) and l.crawl=0";
//echo $query . "<P>";
$result=$db->Execute($query);
$data=$result->FetchRow();
$targetb_indirect_total=$data["users"];

echo "<table><tr><td>"._PAGE." B: <b>$pageb</b></td></td><td>"._USERS."</td><td>"._CONVERSION."</td></tr>";
echo "<tr bgcolor=FFFFFF><td>"._PAGE." B "._VISITORS.":</td><td> $pageb_total</td><td>&nbsp;</td></tr>";
if ($pageb_total > 0) {
 $cnva="";
 $cnva=number_format(($targetb_total/$pageb_total*100),2);
}
echo "<tr><td>"._PAGE." B "._DIRECT_CONVERSIONS.":</td><td> $targetb_total</td><td> $cnva %</td></tr>";
//$targetb_indirect_total=$targetb_indirect_total-$targetb_total;
if ($pageb_total > 0) {
 $cnva=0;
 $cnva=number_format(($targetb_indirect_total/$pageb_total*100),2);
}
echo "<tr><td bgcolor=FFFFFF>Page B timelapse conversions:</td><td> $targetb_indirect_total</td><td>$cnva %</td></tr>";
if ($conversions > 0) {
$salesp=number_format(($targetb_indirect_total/$conversions*100),2);
}
echo "<tr><td>"._PAGE." B "._WAS_VIEWED_BY." $salesp % "._OF. " $conversions "._TOTAL_CONVERTED_USERS."</td></tr></table>";

echo "</td></tr></table>";

//statistically significant ?

if ($pagea_total==0 || $pageb_total==0) {
	 echo "<P><b>"._NOT_ENOUGH_DATA_TO_DETERMINE."</b>";
} else {
	$epop = ($targeta_indirect_total + $targetb_indirect_total) / ($pagea_total + $pageb_total);
	$eerr = sqrt(($epop * (1 - $epop) * ($pagea_total + $pageb_total))/($pagea_total * $pageb_total));
	$cfactor = @(abs((@($targeta_indirect_total/$pagea_total) - @($targetb_indirect_total/$pageb_total)))/$eerr);
	//echo "Factor: $cfactor<br>";


	if ($cfactor > 0.01) {
	 $confidence=1;
	}
	if ($cfactor > 0.06) {
	 $confidence=5;
	}
	if ($cfactor > 0.14) {
	 $confidence=10;
	}
	if ($cfactor > 0.25) {
	 $confidence=20;
	}
	if ($cfactor > 0.52) {
	 $confidence=39;
	}
	if ($cfactor > 0.68) {
	 $confidence=50;
	}
	if ($cfactor > 1.00) {
	 $confidence=68;
	}
	if ($cfactor > 1.64) {
	 $confidence=90;
	}
	if ($cfactor > 1.96) {
	 $confidence=95;
	}
	if ($cfactor > 2.58) {
	 $confidence=99;
	}
	if ($cfactor > 2.81) {
	 $confidence=99.5;
	} else {
		$confidence = 0;
	}

	if (($targeta_indirect_total/$pagea_total) > ($targetb_indirect_total/$pageb_total)) {
			 $winner=_PAGE." A";
			 $loser=_PAGE." B";
	} else {
			 $winner=_PAGE." B";
			 $loser=_PAGE." A";
	}	
	
	if ($confidence >= 90) {
		 echo "<P><table width=50% align=center cellpadding=4 border=0 cellspacing=0 class=smallborder>";
		 echo "<tr><td valign=top>&nbsp;<br><font size=+1>"._SPLIT_TEST_WINNER.": <font color=red><b>$winner</b></font> !</font><br>";
  	 echo _TEST_RESULTS_ARE_SIGNIFICANT." ( > $confidence% "._CONFIDENCE." ). "._YOU_CAN_BE_ABOUT." $confidence% "._CONFIDENT_THAT." $winner "._WILL_PERFORM_BETTER_THAN." $loser";
		 echo "<br>&nbsp;</td></tr></table>";
		 
  } else {
	   echo "<P><table width=50% align=center cellpadding=4 border=0 cellspacing=0 class=smallborder>";
		 echo "<tr><td valign=top>&nbsp;<br><font size=+1>"._SPLIT_TEST_WINNER.": <b>"._INCONCLUSIVE."</b></font><br>";
  	 echo _TEST_RESULTS_ARE_NOT_SIGNIFICANT." ( > $confidence% "._CONFIDENCE." ). "._YOU_CAN_ONLY_BE_ABOUT." $confidence% "._CONFIDENT_THAT." $winner "._WILL_PERFORM_BETTER_THAN." $loser. "._WHEN_THE_LEVEL_DROPS." $winner "._JUST_GOT_LUCKY.".";
		 echo "<br>&nbsp;</td></tr></table>";
  }
}

		
// $rend=getmicrotime();
// $rtook=number_format(($rend-$rstart),2);
// echoDebug("<P>&nbsp;<p>&nbsp;<p><table width=500 cellpadding=3 border=0><tr><td rowspan=2>&nbsp;&nbsp;</td><td><font face=\"ms sans serif,arial\" size=1 color=silver>MySQL query:</font></td></tr><tr><td class=dotline2 bgcolor=#F8F8F8><font face=\"ms sans serif,arial\" size=1 color=gray>$query<P>Page took $rtook sec to build</font></td></tr></table></dir>");
?>
<P>
&nbsp;
<P>


&nbsp;
<div align=center><font face=\"ms sans serif,arial\" size=1 color=#f0f0f0>
&copy; 2005-<?php echo date('Y');?> Logaholic BV</font></div>
<P>
&nbsp;
</body>
</html>
