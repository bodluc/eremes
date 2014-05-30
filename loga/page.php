<?PHP
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
@set_time_limit(86400);

require "common.inc.php";

$visual = @$_REQUEST["visual"];
$page = @$_REQUEST["page"];
$para = @$_REQUEST["para"];
$submit = @$_REQUEST["submit"];
$filter = @$_REQUEST["filter"];
/*$whp = @$_REQUEST["whp"];        // Use parameters?
$cic = @$_REQUEST["cic"];*/
if (!isset($_REQUEST["whp"])) {
	$whp=0;
} else {
	$whp = @$_REQUEST["whp"];
}
if (!isset($_REQUEST["cic"])) {
	$cic=" and crawl=0";
} else {
	$cic = @$_REQUEST["cic"];
}
if (!isset($_REQUEST["statuscodes"])) {
	$statuscodes="200,302";
} else {
	$statuscodes = @$_REQUEST["statuscodes"];
}
$statuscodes = str_replace(" ","", $statuscodes);
$status_array= explode(",",$statuscodes);
if($statuscodes != "")
{
	$i=0;
	$status_str="and (";
	while (@$status_array[$i]!="") {
		$status_str.= " status=".$status_array[$i]." or";
		$i++;
	}
	$status_str=substr($status_str, 0, -3).")";
}
else
{
	$status_str = "and status";
}

if ($visual) {
	 $noheader=1;
}

$start=getmicrotime();
if (!$page) {
	 $page='/';
}

///euuhhh
if (strpos($page, "?")!=FALSE) {
    $para=1;   
} else {
    $page=urldecode($page);    
}
//end euhh

if (!$visual) {
	// In non-visual mode, just include top to load the right profile.
	require "top.php";
} else {
	$profile = new SiteProfile($conf);
	if ($validUserRequired) {
		if ((!$session->logged_in) || (!$session->canAccessProfile($conf) && !$session->isAdmin())) {
			// Not logged in or no rights, and in visual mode, then just redirect it to the main page.
			header("location: index.php");
			exit();
		}
	}
	
	// In visual mode, we need to set the base url of the page to the page we're showing.
	// We also need to have all links, forms, css, js and other types of files that *we* need 
	// directly referenced with an absolute path so they are not affected by the base url setting.
	$baseurl = "http://".$profile->confdomain.$page;
	$ourpath = dirname(currentScriptURL()) . "/";
}

$addlabel = "";

//$page = str_replace(" ", "+", $page);
if ($para==1) {
	 //echo "we have params!!";
	 $whp=1;
	 if ($page=="/" || $page=="/$profile->defaultfile") {
		$addlabel=" (searching '/' or '/$profile->defaultfile')";
		$urlselect="(concat(u.url,up.params)='/' or u.url='/$profile->defaultfile')";
		$refselect="((r.referrer='http://$profile->confdomain/' or r.referrer='https://$profile->confdomain/') or (concat(r.referrer,rp.params) like 'http://$profile->confdomain/$profile->defaultfile%' or concat(r.referrer,rp.params) like 'https://$profile->confdomain/$profile->defaultfile%'))";
	} else {
	 
     if (substr($page, -1,1)=="?") {
			$page=substr($page, 0,-1);
	 }
     if (strpos($page,"?")!==FALSE) {
         // if there is a parameter, we'll add a wildcard just to be freindly
        $star="%";
        $match="LIKE";  
     } else {
        $star="";
        $match="=";   
     }
	 $urlselect="(concat(u.url,up.params) $match '$page$star')";
	 $refselect="((concat(r.referrer,rp.params) $match 'http://$profile->confdomain$page$star') or (concat(r.referrer,rp.params) $match 'https://$profile->confdomain$page$star'))";
	}
} else {
	if ($page=="/") {
		$addlabel=" (searching '/' or '/$profile->defaultfile')";
		$urlselect="(u.url='/' or u.url='/$profile->defaultfile')";
		$refselect="((r.referrer='http://$profile->confdomain/' or r.referrer='https://$profile->confdomain/') or (r.referrer='http://$profile->confdomain/$profile->defaultfile' or r.referrer='https://$profile->confdomain/$profile->defaultfile'))";
	} else if ($page=="/$profile->defaultfile") {
	    $addlabel=" (searching '/' or '/$profile->defaultfile')";
		$urlselect="(u.url='/' or u.url='/$profile->defaultfile')";
		$refselect="(r.referrer='http://$profile->confdomain/' or r.referrer='https://$profile->confdomain/') or (r.referrer='http://$profile->confdomain/$profile->defaultfile' or r.referrer='https://$profile->confdomain/$profile->defaultfile')";
	} else {
		$urlselect="u.url='$page'";
		$refselect="(r.referrer='http://$profile->confdomain$page' or r.referrer='https://$profile->confdomain$page')";	
	}
}
$p = explode("/",$page);
$pparts=count($p);
//echo "<br>Path parts:$pparts<P>";
$i=0;
$curpath = "";
while ($i <= ($pparts-2)) {
	if ($p[$i]!=""){
		$curpath=$curpath."/".$p[$i];
	}
	$i++;
}
//echo "Current path: $curpath<P>";

if (!$from) {
	$from   = mktime(0,0,1,date("m"),01,date("Y"));
	$to     = mktime(23,59,59,date("m"),date("d"),date("Y"));
}

// let's get some totals
//total incoming
if ($whp==1) {
    $tq = $db->Execute("select count(distinct visitorid) as hits from $profile->tablename_urls as u,$profile->tablename_urlparams as up,$profile->tablename as a where (timestamp >=$from and timestamp <=$to) and a.url=u.id and a.params=up.id and $urlselect $cic $status_str");
} else {
    if($cic != ' and crawl=0' || $statuscodes != '200,302')
    {
    	$tq = $db->Execute("select count(distinct visitorid) as hits from $profile->tablename_urls as u,$profile->tablename as a where (timestamp >=$from and timestamp <=$to) and a.url=u.id and $urlselect $cic");
    } else {
    $tq = $db->Execute("select sum(visitors) as hits from $profile->tablename_dailyurls as a,$profile->tablename_urls as u where (timestamp >=$from and timestamp <=$to) and a.url=u.id and $urlselect");
    }
}

$total = $tq->FetchRow();
//total outgoing
if ($whp==1) {
    $tq = $db->Execute("select count(distinct visitorid) as hits from $profile->tablename_referrers as r,$profile->tablename_refparams as rp,$profile->tablename as a where timestamp >=$from and timestamp <=$to and a.referrer=r.id and a.refparams=rp.id and $refselect $cic");
} else {
	if ($cic != ' and crawl=0' || $statuscodes != '200,302') {
		$tq = $db->Execute("select count(distinct visitorid) as hits from $profile->tablename_referrers as r,$profile->tablename as a where timestamp >=$from and timestamp <=$to and a.referrer=r.id and $refselect $cic");
	} else {
    		$tq = $db->Execute("select sum(visitors) as hits from $profile->tablename_dailyurls as a,$profile->tablename_referrers as r where timestamp >=$from and timestamp <=$to and a.referrer=r.id and $refselect");
    	}
}

$wtotal = $tq->FetchRow();

if ($whp==1) {
	// where they came from
	$doquery = "select count(distinct visitorid) as hits, concat(u.url,up.params) as url,concat(r.referrer,rp.params) as referrer from $profile->tablename_urls as u,$profile->tablename_urlparams as up,$profile->tablename_referrers as r,$profile->tablename_refparams as rp,$profile->tablename as a where (timestamp >=$from and timestamp <=$to) and a.url=u.id and a.params=up.id and a.referrer=r.id and a.refparams=rp.id and $urlselect $cic group by referrer order by hits desc limit 200";
	$q = $db->Execute($doquery);
	//echo "where they came from: $doquery<P>";
	
	// where they went
	$doquery = "select count(distinct visitorid) as hits, concat(u.url,up.params) as url,concat(r.referrer,rp.params) as referrer, status from $profile->tablename_urls as u,$profile->tablename_urlparams as up,$profile->tablename_referrers as r,$profile->tablename_refparams as rp, $profile->tablename as a where (timestamp >=$from and timestamp <=$to) and a.url=u.id and a.params=up.id and a.referrer=r.id and a.refparams=rp.id and $refselect $cic $status_str group by url order by hits desc limit 200";
	$q2 = $db->Execute($doquery);
} else {
	if($cic != ' and crawl=0' || $statuscodes != '200,302') {
	//where they came from
	$doquery = "select count(distinct visitorid) as hits, u.url as url,r.referrer as referrer from $profile->tablename_urls as u,$profile->tablename_referrers as r,$profile->tablename as a where (timestamp >=$from and timestamp <=$to) and a.url=u.id and a.referrer=r.id and $urlselect $cic group by referrer order by hits desc limit 200";
	$q = $db->Execute($doquery);
	
	// where they went
	$doquery = "select count(distinct visitorid) as hits, u.url as url,r.referrer as referrer, status from $profile->tablename_urls as u,$profile->tablename_referrers as r,$profile->tablename as a where (timestamp >=$from and timestamp <=$to) and a.url=u.id and a.referrer=r.id and $refselect $cic $status_str group by url order by hits desc limit 200";
	$q2 = $db->Execute($doquery);
    } else {
    	// where they came from (we show the referrer)
	//$q = $db->Execute("select count(*) as hits, url,referrer from $profile->tablename ". ($databasedriver == "mysql" ? "use index (timestamp) " : "")." where timestamp >=$from and timestamp <=$to and $urlselect and crawl=0 group by referrer order by hits desc limit 200");
    $q = $db->Execute("select sum(visitors) as hits, u.url,r.referrer from $profile->tablename_dailyurls as a,$profile->tablename_urls as u,$profile->tablename_referrers as r where timestamp >=$from and timestamp <=$to and a.url=u.id and a.referrer=r.id and $urlselect group by a.referrer order by hits desc limit 200");

	// where they went (we show the url)
	//$q2 = $db->Execute("select count(*) as hits, url,referrer from $profile->tablename ". ($databasedriver == "mysql" ? "use index (timestamp) " : "")." where timestamp >=$from and timestamp <=$to and $refselect group by url order by hits desc limit 200");
    $q2 = $db->Execute("select sum(visitors) as hits, u.url,r.referrer from $profile->tablename_dailyurls as a,$profile->tablename_urls as u,$profile->tablename_referrers as r where timestamp >=$from and timestamp <=$to and a.url=u.id and a.referrer=r.id and $refselect group by a.url order by hits desc limit 200");
    }
}
$t=getmicrotime();
$took=$t-$start;
//echo "queries took $took sec";
//get the page
$url = "http://$profile->confdomain$page";

// An extra function can be defined in "user_settings.php" and if it exists, it will be called here to "normalize" the URL based on a site's needs.
if (is_callable("do_extra_visual_mode_url_parse")) {
	$url = do_extra_visual_mode_url_parse($url);
}

//$url = str_replace(" ", "+", $url);
//echo " getting $url";

flush();
if ($visual) {
	// Set the user agent so we can identify hits made by logaholic
	ini_set('user_agent','Logaholic');	
    $url = str_replace(" ", "%20", $url); //dimir   
    if ($source =file_get_contents($url,1024)) {
    } else {
    	if(strtolower(ini_get("allow_url_fopen")) != 'off')
    	{
         echo "<P><b>"._ERROR_REQUEST_TO." $url "._RETURNED_NO_CONTENT."</b>";
        }
    }
	//echo " dne $url";
	flush();
	
	// First, let's get rid of all the comment sections, since there might be tags inside comments that are invalid and cause us problems.
	$source = preg_replace('/<!--(.|\s)*?-->/', '', $source);
	
	// OK, let's modify the pulled page - look for the <head> tag and <body> tag.  If those aren't found, then we'll just stick an *extra* head
	// section in.
	
	// Need to reset the base URL.  (If it already has a base URL, maybe we shouldn't do it, but it will be a relative one and
	// we'll need to "absolute" that - maybe we can do that in the other logic, but for now we'll just stick in a forced one.
	$insertsection = "\n<base href=\"$baseurl\">\n";
	
	// Now, the stylesheet.
	$insertsection .=  "<link rel=\"stylesheet\" type=\"text/css\" name=\"Logaholic_Tracking\" href=\"" . $ourpath ."logaholic_visual_mode.css\">\n";
	
	// Do we have a <head> section?
	//if ($newsource = preg_replace('%(<HEAD[^>]*>)(.*?)(</HEAD>)%si', "$1$2".$insertsection."$3", $source)) {
    if ($newsource = preg_replace('%(<HEAD[^>]*>)(.*?)(</HEAD>)%si', "$1".$insertsection."$2$3", $source)) {
		$source = $newsource;
	} else {
		// We couldn't find a "head" (which seems unlikely), so just stick our own <head> on in front.
		$source = "<head>".$insertsection."</head>".$source;
	}	
	// In visual mode, buffer everything so we can insert it into the full page.
	ob_start();
}

if (!$visual) {

  echo "<div class='form1-wrap'>";
  echo "<form method=\"get\" action=\"page.php\" id=\"form1\" name=\"form1\"><table border=\"0\"><tr><td width=\"100\"><b>"._DATE_RANGE.":</b> </td><td>";
  QuickDate($from,$to);
  echo "</td><td>";
  newDateSelector($from,$to);
  echo "</td><td><input type=submit name=submitbut value=Report class=small><input type=hidden name=but value=Report> <a id=\"moreoptions\" class=graylink href=\"javascript:moreoptions();\">"._MORE_OPTIONS."</a>";
  echo "<input type=hidden name=conf value=\"$conf\">";
  echo "</td></tr><tr><td><b>"._PAGE.":</b></td><td colspan=\"3\"><input type=text name=page id =\"page\" size=30 value=\"$page\" onkeyup=\"popupMenu(event, this.value+'@'+this.id+'@'+'page', 'forminput');\" onclick=\"popupMenu(event, this.value+'@'+this.id+'@'+'page', 'forminput');\">  <span style=\"background-image: url('images/icons/eye.gif'); background-repeat: no-repeat; padding: 0 0 0 20px;\"><b><a title=\""._EXPLAIN_VISUAL_MODE."\" href=\"".$ourpath."page.php?conf=$conf&from=$from&to=$to&visual=1&page=$page&whp=$whp\">
    <font color=blue>"._CLICK_HERE_VISUAL_MODE."</font></a>.</b></span></td>"; 
  echo "</tr></table>";
  ?>
  </table>
  
  
  <?php
  echo "<div id=\"advancedUI\" style=\"display:none;position:relative;\">
  <table border=\"0\"><tr><td width=\"100\"><b><label for=\"statuscodes\">"._STATUS_CODES.": </label></b> </td><td colspan=\"2\">
  <input type=\"text\" title=\"i.e. 200, 302\" id=\"statuscodes\" name=\"statuscodes\" value=\"".$statuscodes."\">
  ("._ONLY_LANDING_PAGES_WITH_THESE_STATUS_CODES_WILL_BE_DISPLAYED.".)</td></tr><tr><td><b><label for=\"cic\">"._TRAFFIC.": </label></b></td><td colspan=\"2\">
  <select id=\"cic\" name=\"cic\"><option "; if($cic == " "){echo"selected=\"selected\"";}echo " value=\" \">"._ALL_HUMANS_AND_BOTS."</option><option "; if(!$cic || $cic == " and crawl=0"){echo"selected=\"selected\"";}echo " value=\" and crawl=0\">"._HUMANS_ONLY."</option><option "; if($cic == " and crawl=1"){echo"selected=\"selected\"";}echo " value=\" and crawl=1\">"._BOTS_ONLY."</option></select></td></tr><tr><td><b><label for=\"para\">"._PARAMETERS.": </label></b></td><td>"; echo "<input type=checkbox value=1 name=\"para\" id=\"para\" ";
  if ($whp==1) {
     echo "Checked>";
  } else {
     echo ">";
  }
  echo"</td><td>"._EXPLAIN_PARAMETERS."</td></tr></table></div>";
  echo "<hr noshade size=1 width=100%  style=\"float:left;\"></div>";
  if($cic == " and crawl=0" || $statuscodes == "200,302" || !empty($whp)){
  echo "<script language=\"javascript\" type=\"text/javascript\">moreoptions();</script>";}
  echo "<table width=100% cellspacing=0 cellpadding=6 border=0 style=\"float:left;\"><tr><td colspan=2 class=toplinegreen bgcolor=#d5ffd5>";
  ?>
    <font size="+1" color=black>
    <?php echo _PAGE_ANALYSIS?></font> <br><b><?php echo _DATE_FROM?></b> <?php echo date("D, d M Y / H:i", $from); ?> <b><?php echo _DATE_TO?></b> <?php echo date("D, d M Y / H:i", $to); ?></font>
  </td></tr><tr><td cospan=2> 
  <?php
/*
  echo "<div style=\"position:relative;width:100%;margin-top:-35px;z-index:10;float:left;\"><form method=get action=\"".$ourpath."page.php\" id=\"form1\" name=\"form1\"><table border=0><tr><td><b>Date Range: </b>";
  QuickDate($from,$to);
  echo "</td><td>";
  newDateSelector($from,$to);
  echo "<input type=hidden name=conf value=\"$conf\">";
  echo "</td><td>";
  echo " Page: <input type=text name=page size=30 value=\"$page\">";
  echo " Parameters ? <input type=checkbox value=1 name=\"para\" ";
  if ($whp==1) {
	 echo "Checked>";
  } else {
	 echo ">";
  }
  echo "</td><td><input type=submit name=submit value=Report>";
  echo "</td></tr></table>";
  ?>
  <table cellpadding=6 width=100% border=0 style="float:left;">
	<tr>
	<td><b><a href="<?php echo $ourpath . "page.php?conf=$conf&from=$from&to=$to&visual=1&page=$page&whp=$whp"; ?>"><img src=images/icons/eye.gif width=16 height=16 align=left border=0> <font color=blue>Click here for Visual Mode</font></a>.</b> (Visual Mode shows the Click Through Rate to each internal link as an overlay on your web page.)</td>
	</tr>
	</table>
	<?php
  echo "</div><hr noshade size=1 width=100% style=\"float:left;\">";
  echo "<table width=100% cellspacing=0 cellpadding=6 border=0 style=\"float:left;\"><tr><td colspan=2 class=toplinegreen bgcolor=#d5ffd5>";
  ?>
    <font size="+1" color=black>
    Page Analysis</font> <br><b>from</b> <?php echo date("D, d M Y / H:i", $from); ?> <b>to</b> <?php echo date("D, d M Y / H:i", $to); ?></font>
  </td></tr><tr><td cospan=2>
  <?php
*/
} else {
	echo "<div class=\"logaholic_page_form\" style=\"width:100%;background-color:#f0f0f0;color: #333;\"><table border=0 width=100% class=\"toplinegreen\"><tr><td><font size=3>"._LOGAHOLIC_VISUAL_PAGE_ANALYSIS."</font></td></tr></table>";

// date selection
echo "<form method=get action=\"".$ourpath."page.php\"><table border=0 bgcolor=#f0f0f0><tr><td>";
DateSelector($from,$to);
if ($filter) {
	 $checked="checked";
}
echo "</td><td><input type=hidden name=conf value=\"$conf\"><input type=hidden name=visual value=\"$visual\">";
QuickDate($from,$to);
echo " "._PAGE.": <input type=text name=page size=30 value=\"$page\">";
echo " "._PARAMETERS."? <input type=checkbox value=1 name=\"para\" ";
if ($whp==1) {
	 echo "checked>";
} else {
	echo ">";
}
echo "</td><td>";
echo "<input type=submit name=submit value=Report></td></tr></table></form>";
}

if (!$visual) {  
?>
	
	</td>
	</tr>
	<tr>
			<td colspan=2 align=center><font size=3><?php echo _VISITORS_TO_PAGE?>: <b><?php echo $page . $addlabel . "</b> - "._TOTAL_REQUESTS.": ".$total["hits"];?>
            <?php
             if ($whp==1) {
                echo " ("._UNIQUE_VISITORS_IN_DATE_RANGE.")";
             } else if ($cic != " and crawl=0") {
             	echo " ("._UNIQUE_VISITORS_IN_DATE_RANGE.")";
             } else if ($statuscodes!="200,302") {
             	echo " ("._UNIQUE_VISITORS_IN_DATE_RANGE.")";
             } else {
                echo " ("._SUM_OF_DAILY_UNIQUE_VISITORS.")";
             }
            
            ?>
            </font></td>
	</tr>
	<tr>
	<td width=50% valign=top class="greenborder">
	<table cellpadding=4>
	<tr><td colspan=3><b><?php echo _VISITORS_CAME_FROM?>:</b><p></td></tr>
	<?php
	while ($came=$q->FetchRow()) {
				if ($came["referrer"]=="-") {
					 $came["referrer"]="- ("._UNKNOWN.")";
				}
				echo "<tr><td>".$came["hits"]."</td>";
				echo "<td>" . @number_format(($came["hits"]/$total["hits"]*100),2) . "% </td>";
				$fd="http://$profile->confdomain/";
				$end=strlen($fd);
				if (substr($came["referrer"], 0, $end)=="http://$profile->confdomain/") {
					 $came["referrer"]=substr($came["referrer"], ($end-1));
					 //$came["referrer"]="<a href='".$ourpath."page.php?conf=$conf&from=$from&to=$to&page=".$came["referrer"]."&statuscodes=$statuscodes&whp=$whp&cic=$cic'>".$came["referrer"]."</a>";
					 //echo "<td>".$came["referrer"]."</td></tr>";
                     echo "<td><a title=\""._CLICK_TO_OPEN_MENU_FOR." {$came["referrer"]}\" onclick=\"popupMenu(event, '".urlencode($came["referrer"])."', 'page');\" href=\"\">{$came["referrer"]}</a></td></tr>";
				} else {
					 if (strlen($came["referrer"]) > 75) {
						 $dispreferrer=substr($came["referrer"], 0, 75) . "...";
					 } else {
						 $dispreferrer=$came["referrer"];
					 }
					 //echo "<td title='".$came["referrer"]."'><a href=\"".$came["referrer"]."\" target=_blank class=\"nodec2\">$dispreferrer</a></td></tr>";
                     echo "<td><a class=nodec2 title=\""._CLICK_TO_OPEN_MENU_FOR." {$came["referrer"]}\" onclick=\"popupMenu(event, '".urlencode($came["referrer"])."', 'referrer');\" href=\"\">$dispreferrer</a></td></tr>";
				}
	}
	?>
	</table>
	</td>
	<td width=50% valign=top class="redborder">
	<table cellpadding=4>
	<tr><td colspan="4"><b><?php echo _VISITORS_WENT_TO?>:</b></td></tr>
	<tr><td><?php echo _VISITORS;?></td><td title="">CTR</td>
	<?php if ($whp==1) {
		echo "<td>"._STATUS."</td>";
	} else if ($cic != " and crawl=0") {
		echo "<td>"._STATUS."</td>";
	} else if ($statuscodes!="200,302") {
		echo "<td>"._STATUS."</td>";
	}?>
	<td><?php echo _LANDING_PAGE;?></td></tr>
	<?php
// end visual if
}

// The page may need to be *modified* by some site specific logic.  If that's the case, then
// that logic can be included in "user_settings.php" and it will be called here.
if (($visual) && (is_callable("do_extra_visual_mode_page_parse"))) {
	$source = do_extra_visual_mode_page_parse($source);
}

// Functions to merge and simplify paths (relative paths to absolute paths, etc).
function cleanPath($path) {
	 $result = array();
	 $pathA = explode('/', $path);
	 if (!$pathA[0])
			 $result[] = '';
	 foreach ($pathA AS $key => $dir) {
			 if ($dir == '..') {
					 if (end($result) == '..') {
							 $result[] = '..';
					 } elseif (!array_pop($result)) {
							 $result[] = '..';
					 }
			 } elseif ($dir && $dir != '.') {
           $result[] = $dir;
       }
   }
	 if (!end($pathA))
			 $result[] = '';
	 return implode('/', $result);
}

function unwind_rel_path($matches){

	global $page;  // Define it global so we can access the Global page variable.
	
	$basepath = str_replace('\\','/',dirname($page."aaa"));
	
	if (substr($basepath, -1, 1) <> '/') {
		$basepath .= "/";
	}

	// Add in the base directory (the "dummy" part is so "dirname" works)
	$fix_url = $basepath . $matches[2];

	return $matches[1] . $fix_url . "\"";
}

function simplify_path($matches){
	// Clean out any ..s
	$fix_url = cleanPath($matches[2]);
	
	return $matches[1] . $fix_url . "\"";
}

if ($visual) {
	
	// Now, we need to "unwind" the relative paths.  This will parse all urls that
	// don't start with / or a protocol specifier so the
	// path can be unwound...
	$source = preg_replace_callback("'(<a[^>]*?href=\")(?!\w*:)([^/][^\"]*)\"'is", "unwind_rel_path", $source);

	// Let's simplify our paths (remove any ./ or ../ stuff)
	$source = preg_replace_callback("'(<a[^>]*?href=\")(?!\w*:)([^\"]*)\"'is", "simplify_path", $source);
}
while ($went=$q2->FetchRow()) {
			// If we don't have any hits, then we shouldn't divide by 0
			if ($total["hits"] > 0) {
				$perc=number_format(($went["hits"]/$total["hits"]*100),1);
			} else {
				$perc =number_format(0,1);
			}	
			
			if ($perc < 25) {
					$xfactor=1;
			} else {
					$xfactor=2;
			}
			// non visual part
			if (!$visual){
				echo "<tr><td>".$went["hits"]."</td>";
				echo "<td>" . $perc . "% </td>";
				if ($whp==1) {
					echo "<td>" . @$went["status"] . "</td>";
             			} else if ($cic != " and crawl=0") {
					echo "<td>" . @$went["status"] . "</td>";
        			} else if ($statuscodes!="200,302") {
					echo "<td>" . @$went["status"] . "</td>";
				}
				if (strlen($went["url"]) > 75) {
					 $dispurl=substr($went["url"], 0, 75) . "...";
				} else {
					$dispurl=$went["url"];
				}
				if (strpos($went["url"], "exitclick.php")!=FALSE) {
					echo "<td title=\"".$went["url"]."\">$dispurl</td></tr>";				 
				} else {
					echo "<td title=\"".$went["url"]."\"><a href='".$ourpath."page.php?conf=$conf&page=".$went["url"]."&from=$from&to=$to&statuscodes=$statuscodes&whp=$whp&cic=$cic' class=\"nodec3\">$dispurl</a></td></tr>";
                    //echo "<td><a title=\""._CLICK_TO_OPEN_MENU_FOR." {$went["url"]}\" onclick=\"popupMenu(event, '".urlencode($went["url"])."', 'page');\" href=\"\">{$went["url"]}</a></td></tr>";
                    // can't do action menu yet due to extra params here, we need to fix that
				}
			// visual part
			} else {
				if (strpos($went["url"], "exitclick.php")!=FALSE) {
					 $redourl=substr($went["url"],strpos($went["url"],"url="));
					 $redourl=substr($redourl,4);
					 //echo $redourl."<br>";
					 $went["url"]=$redourl;
				}
				$turl = substr($went["url"], 1);
				
				//echo "searching for http://*?$profile->confdomain$went[url]<br>";
				
				// Handle logaholic instances running under HTTPS (as long as they are running on port 443).
				if ($_SERVER['SERVER_PORT'] == '443') {
					$gourl = "https://";
				} else {
					$gourl = "http://";
				}
				$gourl.=$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']."?conf=$conf&from=$from&to=$to&visual=1&page=".urlencode($went["url"]);
				
				$esc_confdomain = str_replace(".", "\.", $profile->confdomain); // The domain has .'s in it, which need to be escaped.
					
				$fr=substr($went["url"],1);
				$fr=str_replace("/","\/",$fr);
				$fr=str_replace("?","\?",$fr);
				
				$pwidth=1+(intval((60/100)*$perc)*$xfactor);
				
				// Build the percentage bar
				$ctrspan="<span class=\"logaholic_outer_CTR_bar\">";
				$ctrspan.="<div class=\"logaholic_CTR_pct_bar\" style=\"width:".$pwidth."px;\"></div>";
				$ctrspan.="<div class=\"logaholic_CTR_pct_text\" title=\"$perc % CTR to $went[url]\">$perc&nbsp;%</div></span>";
				
				if ($went["url"]!="/") {
					
					// The *? expression is the non-greedy version of *
					// Modifiers: s = dot maches newline.  i = ignore case
					$html_code = '/(<a[^>]*?href\s*=\s*["\']?)(http:\/\/' . $esc_confdomain . ')?(\/)?(' . $fr . ')([ #?&"\'>].*?)(<\/a>)/si';
					
					$html_replace = "$1$gourl$5$ctrspan$6";          
					$source = preg_replace($html_code, $html_replace, $source);
					
					//do it all again for javascript tags special
					$html_code = "/(<a .*?href=\"javascript:jumpTo\(\')(\/|)(http\:\/\/$esc_confdomain\/|)($fr)(\'\))([ \?\>\"\#])(.*?a>)/si";
					$html_replace = "$ctrspan$1$2$gourl$5$6$7";
					$source = preg_replace($html_code, $html_replace, $source);
				}
	
				// do forms
				if ($went["url"]!="/") {
					$html_code = "/(<form )(.*?)(http\:\/\/$esc_confdomain\/|)($fr\[\^\>]*)/i";
					$html_replace = "$1$2$3$4 $ctrspan";
					$source = preg_replace($html_code, $html_replace, $source);
				}   
			}
}
if ($visual) {
	// OK, now replace any other internal site links with Logaholic links.

	// Handle logaholic instances running under HTTPS (as long as they are running on port 443).
	if ($_SERVER['SERVER_PORT'] == '443') {
		$gourl = "https://";
	} else {
		$gourl = "http://";
	}
	
	$negmatch = str_replace("/","\/",$_SERVER['PHP_SELF']);
	
	$gourl .= $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']."?conf=$conf&from=$from&to=$to&visual=1&page=";

	// Any URLs that directly reference our domain, but *not* matching our script name (the negative lookahead part).
	$source = preg_replace('/(<a[^>]*?href=["\']?)http:\/\/'.$profile->confdomain . '(?!'.$negmatch.')([^"\'>]*["\'>])/si', "$1$gourl$2", $source);
	
	// Or any absolute paths...
	$source = preg_replace('/(<a[^>]*?href=["\']?)(\/[^"\'>]*["\'>])/is', "$1$gourl$2\"", $source);
	
}

if (!$visual) {
	?>
	</table>
	<P>
	<?php
	if($statuscodes != '404'){?><?php echo _PAGE_EXIT_RATE?>: 
	<?php
	$left=$total["hits"]-$wtotal["hits"];
	$leftperc=number_format(@($left/$total["hits"]*100),2);
	echo "$leftperc % ($left)";
	}?>
	</td>
	</tr>
	</table>
	<p>
	<?php 
} else {
	?>
	<div>
	<table cellpadding=6 width=100% bgcolor=#f0f0f0>
	<tr>
	<td>
	<?php echo _VISITORS_TO_PAGE?>: <b><?php echo $page . $addlabel . "</b> - "._TOTAL_REQUESTS.": ".$total["hits"];?>
	 | <?php echo _PAGE_EXIT_RATIO?>: 
	<?php
	$left=$total["hits"]-$wtotal["hits"];
	$leftperc=number_format(@($left/$total["hits"]*100),2);
	echo "$leftperc % ($left)";
	?>
	 | <a href="<?php echo $ourpath . "page.php?conf=$conf&from=$from&to=$to&page=$page"; ?>"><b><?php echo _RETURN_TO_STATS_MODE?></b></a>
	</td>
	</tr>
	</table>
	</div></div>
	<hr>
	<?php
	// date selection
	/*
	echo "<form method=get action=page.php><table border=1><tr><td>";
	DateSelector($from,$to);
	if ($filter) {
		 $checked="CHECKED";
	}
	echo "</td><td><input type=hidden name=conf value=\"$conf\"><input type=hidden name=visual value=\"1\">";
	QuickDate($from,$to);
	echo " Page: <input type=text name=page size=30 value=\"$page\">";
	echo " Parameters ? <input type=checkbox value=1 name=para ";
	if ($whp==1) {
		 echo "Checked>";
	} else {
		echo ">";
	}
	echo "</td><td>";
	echo "<input type=submit name=submit value=Report></td></tr></table>";
	*/
	
	$selector = ob_get_contents();
	ob_end_clean();
	
	if ($newsource = preg_replace('%(<BODY[^>]*>)(.*?)(</BODY>)%si', "$1".$selector."$2$3", $source)) {
		$source = $newsource;
	} else {
//		$source = "<head>".$insertsection."</head>".$source;
	}	
	
	// Stick in whatever we have after the first <body> tag.
	
	echo $source;
}

if (!$visual) { ?>
</form>
<P>

</body>
</html>

<?php } ?>
