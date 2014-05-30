<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 

if (file_exists("components/geoip/GeoLiteCity.dat")) {
    require_once("components/geoip/geoipcity.inc");
    $gi = geoip_open("components/geoip/GeoLiteCity.dat",GEOIP_STANDARD);
}

$reporting = true;

require_once "common.inc.php";
@set_time_limit(86400);

// Pull in variables that may have been passed in with the URL.
$print = @$_REQUEST["print"];
$nocache = @$_REQUEST["nocache"];
$notrail = @$_REQUEST["notrail"];
$formemail = @$_REQUEST["formemail"];
$submit = @$_REQUEST["submitbut"];
$showfields = @$_REQUEST["showfields"];
$filter = @$_REQUEST["filter"];
$item = @$_REQUEST["item"];
$item2 = @$_REQUEST["item2"];
$drilldown = @$_REQUEST["drilldown"];
$labels = @urldecode($_REQUEST["labels"]);
$status = @$_REQUEST["status"];
$agent = @$_REQUEST["agent"];
$csvpreview = @$_REQUEST["csvpreview"];
$csvexport = @$_REQUEST["csvexport"];
if(isset($_REQUEST['outputmode']) && $_REQUEST['outputmode'] == 'csv') { $csvexport = 1; }
$country = @$_REQUEST["country"];
$search = @$_REQUEST["search"];
$searchmode = @$_REQUEST["searchmode"];
$statstable_only = @$_REQUEST["statstable_only"];
$xml = @$_REQUEST["xml"];
if(isset($_REQUEST['outputmode']) && $_REQUEST['outputmode'] == 'xml') { $xml = 1; }
$old = @$_REQUEST["old"]; // this is just to be able to compare the speed to the old labels

if (@$_SESSION["trafficsource"]) { $trafficsource = $_SESSION["trafficsource"]; $applytrafficsource = true; } else { $applytrafficsource = false; }

// if (isset($_REQUEST["trafficsource"])) { $trafficsource = $_REQUEST["trafficsource"]; $applytrafficsource = true; } else { $applytrafficsource = false; }
    
if (isset($print)) { $noheader=1;}  
$workspace=@$_REQUEST["workspace"];
$roadto=@$_REQUEST["roadto"];


function DisplayControls() {
    global $from,$to,$conf,$status,$labels,$clabel,$country,$trafficsource,$search,$searchmode,$limit,$roadto,$profile;
  
    echo "<div style=\"position:relative;width:100%;z-index:10;float:left; margin-top: -35px;\">";
    //echo "<div class=\"controls\">";
    echo "<form method=get action=reports.php id=\"form1\" name=\"form1\"><table border=0><tr><td width=72><b>"._DATE_RANGE.": </b></td><td>";
    QuickDate($from,$to);
    echo "</td><td>";
    newDateSelector($from,$to);
    echo "<input type=hidden name=conf value=\"$conf\">";
    echo "<input type=hidden name=status value=\"$status\">";
    if (!empty($clabel)) {
        echo "<input type=hidden name=labels value=\"$clabel\">";
    } else {
        echo "<input type=hidden name=labels value=\"$labels\">";
    }
    if ($country!="") {
        echo "<input type=hidden name=country value=\"$country\">";
    }  
    echo "</td><td><input type=submit name=submitbut value=Report class=small><input type=hidden name=but value=Report> <a id=\"moreoptions\" class=graylink href=\"javascript:moreoptions();\">"._MORE_OPTIONS."</a>"; 

    echo "</td></tr></table>";
    
    if ($labels==_PAGE_CONVERSION || $labels==_REFERRER_CONVERSION || $labels==_KEYWORD_CONVERSION || $labels==_TIME_TO_CONVERSION) {
        echo "<table><tr><td width=70><b>"._TARGET.":</b></td><td>";
        echo "<select name=\"roadto\">";

        if ($profile->targetfiles) {
          $targets=explode(",",$profile->targetfiles);
          foreach ($targets as $thistarget) {
              if ($thistarget) {
                  if (trim($thistarget)==$roadto) { $sel="selected"; } else { $sel=""; }
                  echo "<option $sel value=\"".trim($thistarget)."\">".trim($thistarget)."\n";
              }
          }
          if (!$sel && !$roadto) {
              $roadto=$targets[0];
          }
        }
        echo "</select>";
        echo "</td></tr></table>";    
    }
    
    echo "<div id=\"advancedUI\" style=\"display:none;position:relative;\">";

    echo "<table border=0>";
    echo "<tr><td width=70><b>"._FILTERS.":</b></td><td>";
    echo printTrafficSourceSelect();
    echo "</td></tr>";
    if ($labels==_TOP_PAGES ||$labels==_TOP_PAGES_DETAILS ||$labels==_TOP_REFERRERS ||$labels==_TOP_REFERRERS_DETAILS ||$labels==_MOST_ACTIVE_USERS ||$labels==_TOP_KEYWORDS ||$labels==_TOP_KEYWORDS_DETAILS || $labels==_GOOGLE_RANKINGS || $labels==_RECENT_VISITORS || $labels==_MOST_ACTIVE_CRAWLERS || $labels==_MOST_CRAWLED_PAGES || $labels==_PAGE_CONVERSION || $labels==_REFERRER_CONVERSION || $labels==_KEYWORD_CONVERSION || strpos($labels,_ERROR_REPORT)!==FALSE) {
        echo "<tr><td width=70><b>"._SEARCH.":</b></td><td>";
        echo "<input type=text size=35 name=search value=\"$search\" class=small title=\"You may use 'and' OR 'or' operators here to search multiple items\"> <span class=small> "._SEARCH." $labels "._THAT;
        echo " <select name=searchmode>";
        if ($searchmode=="not like") { 
            echo "<option value=\"like\">"._MATCH;
            echo "<option value=\"not like\" selected>"._DONT_MATCH;
        } else {
            echo "<option value=\"like\" SELECTED>"._MATCH;
            echo "<option value=\"not like\">"._DONT_MATCH;
        }
        echo " </select> ";
        echo " </span>";
        echo "</td></tr>";
    }
    if ($labels==_INTERNAL_SITE_SEARCH) {
        echo "<tr><td width=70><b>"._SEARCH."&nbsp;"._PAGE.":</b></td><td>";
        echo "<input type=hidden name=searchmode value=\"like\">";
        echo "<input type=text size=40 name=search value=\"$search\"  class=small> <span class=small> "._SEARCH_PAGE_EXPLAIN;
        echo " </span>";
        echo "</td></tr>";
    }
    echo " <tr><td colspan=2> </td></tr><tr>";
    echo "<td title=\""._MAX_NUMBER_OF_RESULTS_TO_SHOW."\">";
    if (!$limit) {
       $limit=100;
    }
    echo "<b>"._LIMIT."</b> </td><td><input type=text size=3 name=limit value=\"$limit\"  class=small> <span class=small>("._MAX_NUMBER_OF_RESULTS_TO_SHOW.")</span>";
    echo "</td></tr></table>";
    echo "</div></form></div><div class=\"breaker\"></div>\n";

    if (@$_SESSION["trafficsource"] || $limit!=100 || @$search!="" && $labels!=_INTERNAL_SITE_SEARCH) {
        echo "<script language=\"javascript\" type=\"text/javascript\">moreoptions();</script>";
    }
    if ($labels==_INTERNAL_SITE_SEARCH) {  
        echo "<script language=\"javascript\" type=\"text/javascript\">moreoptions();</script>"; 
    }    
}

function addCacheTrail($cachename) {
    /**
    * @desc Adds a cached report to the workspace
    * @returns Returns an array or cached report names, the most recent one first
    */
    global $profile,$notrail,$nocache;
    
    $cachetrail = getProfileData($profile->profilename, $profile->profilename."cache_trail","");
    if ($notrail == $cachename || $nocache == $cachename) {
        // if we've just deleted it, dont't add it again
        return explode(",",$cachetrail);        
    }
    if (strpos($cachetrail,$cachename)!==FALSE) {
        // it's already in there, do nothing   
    } else {
        $cachetrail=$cachename.",".$cachetrail;
        setProfileData($profile->profilename, $profile->profilename."cache_trail", $cachetrail); 
        $cachetrail = getProfileData($profile->profilename, $profile->profilename."cache_trail","");
    }
    return explode(",",$cachetrail);       
}

function removeCacheTrail($cachename) {
    global $profile;
    $newtrail = getProfileData($profile->profilename,$profile->profilename."cache_trail","");
    $newtrail = str_replace($cachename.",","",$newtrail);
    setProfileData($profile->profilename,$profile->profilename."cache_trail",$newtrail);       
}

function isWorkspace() {
    global $labels;
    if ($labels == _WORKSPACE || $labels == "") {
        $labels= _WORKSPACE;
        return true;    
    } else {
        return false;    
    }
}


/* 
................................................
.......... Main program starts here ............
................................................
*/

if ($csvexport==1) {
    // this creates the CSV export file, no headers or interface needed
    $profile = new SiteProfile($conf);
    REQUIRE "queries.php";
    
    if(defined($labels)) {
        $clabel = $labels;
        $labels = constant($labels);
    }
	
    GetQuery($labels,$showfields,$from,$to,$item,$item2);
    if ($applytrafficsource) { $query = subsetDataToSourceID($query);  }
    $filename=$conf."-".str_replace(" ","-",$labels).".csv";
    ob_start();
    header("Window-target: _blank");
    header("Content-type: application/x-download");
    header("Content-Disposition: attachment; filename=$filename");
    header("Content-Transfer-Encoding: binary");
    CSVStatsTable($from,$to,$showfields,$labels,$query,$drilldown,$filter);
    ob_end_flush();
    exit();
}

//if(!empty($new_ui)) { $statstable_only=1; }
if (@$statstable_only==1) {
    // this creates the Report area only, no headers or interface needed 
    $profile = new SiteProfile($conf);
    REQUIRE "queries.php";
	
	if(!empty($new_ui)) {
		$mini = 3;
	} else {
		$mini=1;
	}
    
    if(defined($labels)) {
        $clabel = $labels;
        $labels = constant($labels);
    }	

	if($mini == 3) {
		# maak de cache string van cache_md5 van de url
		# haal die code op uit de getprofiledata functie
		# als dat er is, echo het, anders doe onderstaande stuk
		
		$cachename = md5(str_replace("&nocache=1","",$_SERVER['QUERY_STRING']));
		
		if(!empty($nocache)) {
			deleteProfileData($profile->profilename, "{$profile->profilename}.cache_{$cachename}");
		}
		
		$contents = getProfileData($profile->profilename, "{$profile->profilename}.cache_{$cachename}", '');
		
		if(!empty($contents)) {
			echo "<span class='cached'></span>";
			echo $contents;
		} else {
			ob_start();
			if (class_exists('Report')) {
				if (isset($clabel) && isset($reports[$clabel])) {
					# there is a class file for this report, let's use it
					$r = new $reports[$clabel]["ClassName"]();
					$r->DisplayReport();
				} else {
					StatsTable($from,$to,$showfields,$labels,$query,$drilldown,$filter);
				}
			}
			$contents = ob_get_clean();
			setProfileData($profile->profilename, "{$profile->profilename}.cache_{$cachename}", $contents);
			echo $contents;
		}
    } else {
		GetQuery($labels,$showfields,$from,$to,$item,$item2);
		if ($applytrafficsource) { $query = subsetDataToSourceID($query);  }
		if (!$query) {
			echo "DUDE, NO QUERY: <pre>$labels</pre>";   
		}
		# this part is experimental
		if (file_exists("includes/report.php")) {
			include "includes/report.php";
			if (isset($reports[$clabel])) {
				# there is a class file for this report, let's use it
				$r = new $reports[$clabel]["ClassName"]();
				$r->DisplayReport();
			} else {
				StatsTable($from,$to,$showfields,$labels,$query,$drilldown,$filter);
			}
		}
	}
    # end of experimental part
	exit();
}
if (@$xml==1) {
    // this creates the Report area only, no headers or interface needed 
    $profile = new SiteProfile($conf);
    REQUIRE "queries.php";
	
    if(defined($labels)) {
        $clabel = $labels;
        $labels = constant($labels);
    }
	
    $mini=3;
    GetQuery($labels,$showfields,$from,$to,$item,$item2);
    if ($applytrafficsource) { $query = subsetDataToSourceID($query);  } 
    if (!$query) {
        echo "DUDE, NO QUERY: <pre>$labels</pre>";   
    }
	
	XMLStatsTable($from,$to,$showfields,$labels,$query,$drilldown,$filter);
    exit(); 
}
if (isset($print)){
	// include_once "templates/template.php";
	// include_once "templates/template_v3.php";
	// $template = new Template_v3();
	echo $template->HTMLheadTag();
		echo $template->BodyStart();	
		
		$icon="<img src=images/icons/logaholiclogo.gif width=16 height=16 align=left style='margin-top:5px;'>";
		echo "<div id='print'>";
		echo "<h1 class='h1-title'>$icon Web Analytics Report for {$profile->confdomain}</h1>";
		
		if (isset($clabel) && isset($reports[$clabel])) {
			# there is a class file for this report, let's use it
			$r = new $reports[$clabel]["ClassName"]();
			$r->displayReportLabel = true;
			$r->displayReportButtons = false;
			echo "<div class='report'>";
			$r->DisplayReport();
			echo "</div>";
		}else{
			REQUIRE "queries.php";
			echo "<div class='report'>";
			GetQuery($labels,$showfields,$from,$to);
			if ($applytrafficsource) { $query = subsetDataToSourceID($query);  } 
			SimpleStatsTable($from,$to,$showfields,$labels,$query);
			echo "</div>";
		}
		echo "</div></body></html>";
	exit();
}

// everything below needs the UI, so lets include it and print the loading box
?>
<script language="javascript" type="text/javascript">
// Define our global variables.
    var conf_name="<?php echo @$conf; ?>";
    var from_date=<?php echo @$from; ?>;
    var to_date=<?php echo @$to; ?>;
</script>
<?php
require "top.php";
require "queries.php";
PrintLoadingBox();

if(defined($labels)) {
    $clabel = $labels;
    $labels = constant($labels);
} else {
    $clabel = $labels;    
}

if ($profile->trackermode==1 && @$labels==_TOP_CLICK_PATHS) { echo "<script type=\"text/javascript\" src=\"includes/get_title.js\"></script>"; }

// Now do some report cache handling
$pstring = $labels.$from.$to.$limit.@$search.@$searchmode.@$_SESSION["trafficsource"].@$country;
$cachename = 'cache_' . md5($pstring);
if ($nocache) {
    deleteProfileData($profile->profilename,$nocache);
    removeCacheTrail($nocache);
}
if ($notrail) {
    removeCacheTrail($notrail);   
}

$rstart=getmicrotime();

 
// Get the Report Query information
if (isWorkspace()==false) {
    GetQuery($labels,$showfields,$from,$to,$item,$item2);
    if ($applytrafficsource) {
	    $query = subsetDataToSourceID($query);
    }
}

// Display the UI controls if we're not in print mode, else just display a simple header
IF (!$print) {
    DisplayControls();
    if (($profile->usepagecache)) {
        if ($nocache) {        
            echo "<span style=\"position:relative;margin-top:-40px;line-height:18px;text-align:right;z-index:200;float:right;font-size:10px;color:red;\">"._DELETED_CACHED_FILE."</span>";
        } else if ($contents = getProfileData($profile->profilename, $cachename, "")) {
            $cached="yes";        
            echo "<span style=\"position:relative;margin-top:-50px;line-height:18px;text-align:right;z-index:200;float:right;font-size:10px;color:gray;\">["._CACHED_REPORT."]<br><a class=graylink href=\"".$_SERVER['PHP_SELF'] ."?". @$_SERVER['QUERY_STRING']."&amp;nocache=$cachename\">"._RECALCULATE."</a></span>";        
        }
    }    
    echo "<div id=\"reportmenu\" style=\"position:absolute;left:10px;width:160px;margin-top:2px;\">";
    SummaryMenu($labels);
    echo "</div>\n";      
} ELSE {
    $icon="<img src=images/icons/logaholiclogo.gif width=16 height=16 align=left>";
	echo "<table cellpadding=8 width=600 border=0><tr><td><b>$icon &nbsp; "._LOGAHOLIC_SUMMARY_REPORT_FOR." $profile->confdomain</b><P>";
}
if ($print) {
    $pad="0px;";   
} else {
    $pad="167px;";
}


//if we want to send this report via email, do it now
if (isset($_REQUEST['formemail'])) {
    include_once "includes/emailalerts.php";
    $email = new EmailAlerts();
    # set up the message
    $mail_contents = "<html><head>";
    $mail_contents.= $email->ApplyReportStyles();
    $mail_contents.= "</head><body>";
    $mail_contents.= $_REQUEST['message']."<br />\n";
    # create all the selected reports
    $mail_contents.= $email->BuildReport($labels,$from,$to); 
    $mail_contents.= "<br /><br />Powered by <a href=\"http://www.logaholic.com/\">Logaholic Web Analytics</a><br /><br /></body></html>";
    # now send it
    $email->HtmlEmail($_REQUEST['formemail'],$_REQUEST['fromemail'],$_REQUEST['subject'],$mail_contents);
    # send a copy to the sender (seperate cus it looks like outlook messes it up)
    $email->HtmlEmail($_REQUEST['fromemail'],$_REQUEST['fromemail'],$_REQUEST['subject'],$mail_contents);
    echoNotice("Sent email to {$_REQUEST['formemail']} with subject: {$_REQUEST['subject']}","margin-left:$pad;");
}

// Now Print Statistics tables
if (@$cached!="yes" && isWorkspace()==false) {
    // Create a new report    
    if ($csvpreview==1) {
        ob_start();
        echo "<pre>";
        CSVStatsTable($from,$to,$showfields,$labels,$query,$drilldown,$filter);
        echo "</pre>";
        $contents = ob_get_contents();
        ob_end_clean();
    } else {
        ob_start();
        
        # this part is experimental
		if($new_ui == 1) {
			if (class_exists('Report')) {
				if (isset($reports[$clabel])) {
					# there is a class file for this report, let's use it
					$r = new $reports[$clabel]["ClassName"]();
					$r->DisplayReport();
				} else {
					StatsTable($from,$to,$showfields,$labels,$query,$drilldown,$filter);
				}
			} else {
				StatsTable($from,$to,$showfields,$labels,$query,$drilldown,$filter);
			}
		} else {
			StatsTable($from,$to,$showfields,$labels,$query,$drilldown,$filter);
		}
        $contents = ob_get_contents();
        ob_end_clean(); 
    }
}
 
// print the fresh report
if (isWorkspace()==false) {
    echo "<div id=\"ReportContainer\" style=\"padding-left:$pad;min-width:630px;\">\n";
    echo $contents;
    echo "</div><br>\n<br>";
}

// now cache the report, add it to the cache trail and display the workspace
if ($profile->usepagecache && !$print) {
    if ($workspace=="del") {
        deleteProfileData($profile->profilename, $profile->profilename."cache_trail");
        echoWarning(_DELETED." "._WORKSPACE,"margin-left:$pad;");    
    }
    if ($workspace=="delcache") {
        deleteProfileData($profile->profilename, "cache\_%");
        deleteProfileData($profile->profilename, $profile->profilename."cache_trail");
        echoWarning(_ALL_CACHE_FILES_DELETED,"margin-left:$pad;");    
    }
    if (@$cached!="yes" && isWorkspace()==false) {
        // save the new cache report
        setProfileData($profile->profilename, $cachename, $contents);
    }
    if (isWorkspace()==false) {
        // add the new report to the cachetrail and get the latest array of cachenames
        $cachetrail = addCacheTrail($cachename);
    } else {
        $cachetrail = getProfileData($profile->profilename, $profile->profilename."cache_trail"); 
        $cachetrail = explode(",",$cachetrail);
    }   
    // now loop through the cached reports on the workspace and display each one
    $i=0;
    echo "<div id=\"Workspace\" style=\"padding-left:$pad;min-width:630px;\">";
    echo "<span style=\"float:right;\"><a id=\"toggleWorkspaceOptions\" href=\"javascript:toggleWorkspaceOptions();\" class=graylink>"._WORKSPACE." "._OPTIONS."</a></span>"; 
    if (@$showWorkspace=='hide' && $labels!=_WORKSPACE) {
         $sel="graylink";
         $display="none";
         echo "<a id=\"toggleWorkspace\" onclick=\"toggleWorkspace()\" href=\"javascript:Report('$labels');\" class=$sel>"._WORKSPACE."</a><br>\n";   
    } else {
        $sel="graylinkselected";
        $display="block";
        echo "<a id=\"toggleWorkspace\" href=\"javascript:toggleWorkspace();\" class=$sel>"._WORKSPACE."</a><br>\n";          
    }
    echo "<div id=\"WorkspaceOptions\" class=\"innerWorkspace\" style=\"display:none;\">";
    echo "<div style=\"width:25%;padding:4px;float:left;\">";
    $style="text-align:center;display:block";
    echoButton("reports.php?conf=$conf&workspace=del",_DEL_WORKSPACE,$style);
    echo "<br>";
    echoButton("reports.php?conf=$conf&workspace=delcache",_DEL_ALL_CACHED_REPORTS,$style); 
    echo "</div>";
    //echoNotice("we have some explaining to do");
    echo "<div class=\"warning ui-state-highlight ui-corner-all\" style=\"width:71%;padding:4px;float:right;\">"._EXPLAIN_WORKSPACE."</div>";
    echo "</div>";
    ?>    
    <script type="text/javascript">
    $(function() {
        $('#innerWorkspace').sortable(
        {
            items: '.workspaceReports', // the type of element to drag
            handle: '.MoveableToplinegreen', // the element that should start the drag event
            opacity: 0.5, // opacity of the element while draging
            placeholder: 'tobox', // class of the element displaying areas to move the element to
            scroll: true, // don't scroll the page while draging
            cursor: 'move',
            forcePlaceholderSize: true ,
            cursorAt: 'top',
            tolerance: 'intersect' ,            
            update: function(event, ui) {    
                var articles = $('.workspaceReports'); // select the list of articles
                var newPosition = articles.index(ui.item); // get the items new position in the article list
                //alert(ui.item.attr('id') + ' was moved to position ' + (newPosition)); // alert the item that was moved and the new position        
                AjaxGet('includes/workspace.php?conf=<?php echo $conf;?>&sort='+ui.item.attr('id')+'&newposition='+newPosition,'ajaxfeedback');
            } // event to run when the order of the elemnts is updated            
        }
        );
        $(".workspaceReports").resizable();
    });
    </script>
    <?php    
        
    echo "<div id=\"innerWorkspace\" class=\"innerWorkspace\" style=\"display:$display;\">\n";
    if (@$showWorkspace!='hide' || $labels==_WORKSPACE) {
        $max=0;
        foreach ($cachetrail as $qreport) {
            if (isWorkspace()==false) { if ($max > 10) { break; } }
            $content="";
            if ($qreport!="" && $qreport!=@$cachename) {
                $content = getProfileData($profile->profilename, $qreport, "");
                if ($content) {
                    // since divs are moveable here, let's change the header css class
                    $content = str_replace("toplinegreen","MoveableToplinegreen",$content);
                    // now display it                    
                    if ($i==2) { echo "<div style=\"clear:both;\"></div>"; $i=0; }
                    echo "<div id=\"$qreport\" class=\"workspaceReports\" style=\"\">\n";
                    echo $content; 
                    echo "</div>";
                    $i++;
                }
                $max++;
            }   
        }
    }
    if ($i==0) {
        echoNotice(_WORKSPACE_EMPTY);
        echoJavascript("toggleWorkspaceOptions();");   
    }
    echo "</div></div><br><br>\n\n";
    
}

// good, we're all done, now close the page		
?>
<div id="ajaxfeedback"></div>
<P>
&nbsp;
<P>
&nbsp;
<?php
$rend=getmicrotime();
$rtook=number_format(($rend-$rstart),3);        
if ($debug || @$_REQUEST['showq']) {        
        echo "<P>&nbsp;<p>&nbsp;<p><table width=500 cellpadding=3 border=0 style=\"margin-left:200px;\"><tr><td rowspan=2>&nbsp;&nbsp;</td><td><font face=\"ms sans serif,arial\" size=1 color=silver>MySQL query:</font></td></tr><tr><td class=dotline2 bgcolor=#F8F8F8><font face=\"ms sans serif,arial\" size=1 color=gray>$query<P>Page took $rtook sec to build</font></td></tr></table></dir>";
        //echo $query;
}
echoFooter("<a class=nodec href=\"credits.php?conf=$conf\">&copy; 2005-".date('Y')." Logaholic BV</a><br>"._ORIGINAL_REPORT_CREATED_IN." ".$rtook." "._SECONDS);

// end of report
?>
</BODY>
</HTML>
