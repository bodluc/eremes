<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 

// Define all report types
$lang = Logaholic_getCurrentLang();

include_once "languages/$lang.php";
define("_TOP_PAGES_TEST","Top Pages test");
if (!isset($labels)) {
	$labels = _TODAY;
}

// this is the list of reports we'll include in the report menu
$lno=1;
$l_constant[$lno++] = "_WORKSPACE";
$l_constant[$lno++] = "_VISITORS_PER_DAY";
$l_constant[$lno++] = "_VISITORS_PER_MONTH";
$l_constant[$lno++] = "_VISITORS_PER_HOUR";
$l_constant[$lno++] = "_DAYS_OF_THE_WEEK";
$l_constant[$lno++] = "_TOP_COUNTRIES_CITIES";
$l_constant[$lno++] = "_MOST_ACTIVE_USERS";
$l_constant[$lno++] = "_RECENT_VISITORS";
$l_constant[$lno++] = "_AUTHENTICATED_VISITORS";
$l_constant[$lno++] = "_VISITORS_AND_VISITS";
$l_constant[$lno++] = "_VISIT_DURATION";
$l_constant[$lno++] = "_TOTAL_DURATION";
$l_constant[$lno++] = "_TOP_PAGES";
//$l_constant[$lno++] = "_TOP_PAGES_TEST";
$l_constant[$lno++] = "_TOP_PAGES_DETAILS";
$l_constant[$lno++] = "_TOP_FEEDS";
$l_constant[$lno++] = "_FEEDBURNER"; 
$l_constant[$lno++] = "_TOP_ENTRY_PAGES";
$l_constant[$lno++] = "_TOP_EXIT_PAGES";
$l_constant[$lno++] = "_TOP_CLICK_PATHS";
$l_constant[$lno++] = "_INTERNAL_SITE_SEARCH";
$l_constant[$lno++] = "_TRAFFIC_BREAKDOWN";
$l_constant[$lno++] = "_TOP_REFERRERS";
$l_constant[$lno++] = "_TOP_REFERRERS_DETAILS";
$l_constant[$lno++] = "_TOP_KEYWORDS";
$l_constant[$lno++] = "_TOP_KEYWORDS_DETAILS";
$l_constant[$lno++] = "_SEARCH_ENGINES";
$l_constant[$lno++] = "_GOOGLE_RANKINGS";
//$l_constant[$lno++] = "Yahoo Rankings";
$l_constant[$lno++] = "_MOST_ACTIVE_CRAWLERS";
$l_constant[$lno++] = "_MOST_CRAWLED_PAGES";
$l_constant[$lno++] = "_BROWSERS";
$l_constant[$lno++] = "_OPERATING_SYSTEMS";
$l_constant[$lno++] = "_SCREEN_RESOLUTION";
$l_constant[$lno++] = "_COLOR_PALETTE";
//$l_constant[$lno++] = "_USER_AGENTS";
$l_constant[$lno++] = "_MOBILE_AGENTS";
$l_constant[$lno++] = "_ALL_TRAFFIC_BY_DAY";       
$l_constant[$lno++] = "_ALL_TRAFFIC_BY_MONTH";
$l_constant[$lno++] = "_ALL_TRAFFIC_BY_HOUR";
$l_constant[$lno++] = "_ERROR_REPORT"; 
$l_constant[$lno++] = "_OVERALL_PERFORMANCE";
$l_constant[$lno++] = "_PAGE_CONVERSION";
$l_constant[$lno++] = "_REFERRER_CONVERSION";
$l_constant[$lno++] = "_KEYWORD_CONVERSION";
$l_constant[$lno++] = "_TIME_TO_CONVERSION";
//this weird stuff is needed for the constants to work properly
$lno=1;
foreach ($l_constant as $constantName) {
	if(defined($constantName)) {
		$l[$lno++]=constant($constantName);
	}
}
// Any report not in the above list is not listed in the main report menu

// Now, the list for reports designed specifically for the dashboard / today screen
$tno=0;
$t_constant[$tno++]="_TODAY_TRENDS"; // this is a completely seperate one, see function todaytrends() in index.php 
$t_constant[$tno++]="_PERFORMANCE_TODAY";
$t_constant[$tno++]="_PERFORMANCE_THIS_MONTH";
$t_constant[$tno++]="_TODAYS_TOP_PAGES";
$t_constant[$tno++]="_TODAYS_TOP_KEYWORDS";
$t_constant[$tno++]="_TODAYS_TOP_REFERRERS";
$t_constant[$tno++]="_TODAYS_TOP_COUNTRIES"; 
$t_constant[$tno++]="_TODAYS_TOP_VISITORS";
$t_constant[$tno++]="_TIME_ON_SITE_TODAY";
$t_constant[$tno++]="_TOP_PAGES_LAST_HOUR";
//this weird stuff is needed for the constants to work properly
$tno=0;
foreach ($t_constant as $constantName) {
	$t[$tno++]=constant($constantName);	
}

function MakeSearchString($search,$field,$searchmode) {
    //this function turns the search field input into valid sql
    
    if (strpos($search," and ")!=FALSE) {
        $searchitems=explode(" and ", $search);
        $andor="AND";
    } else if (strpos($search," or ")!=FALSE) {
        $searchitems=explode(" or ", $search);
        $andor="OR";
    } else {
        $searchitems[0]= $search;
        $andor="AND";
    }
    $i=0;
    $searchst="and ($field $searchmode '%$searchitems[$i]%' ";
    $i++;
    while (@$searchitems[$i]!="") {
        $searchst.="$andor $field $searchmode '%$searchitems[$i]%' ";
        $i++;
    }
    $searchst.=")";
    return $searchst;    
}

function FindMatchingIDs($search,$field,$searchmode,$tables) {
    global $db;
    //this function searches the search input and returns matching id's n a temp table, then joins it into the query
    
    if (strpos($search," and ")!=FALSE) {
        $searchitems=explode(" and ", $search);
        $andor="AND";
    } else if (strpos($search," or ")!=FALSE) {
        $searchitems=explode(" or ", $search);
        $andor="OR";
    } else {
        $searchitems[0]= $search;
        $andor="AND";
    } 
    $i=0;
    $searchst="($field $searchmode '%$searchitems[$i]%' ";
    $i++;
    while (@$searchitems[$i]!="") {
        $searchst.="$andor $field $searchmode '%$searchitems[$i]%' ";
        $i++;
    }
    $searchst.=")";
    
    //echo "<div class=debug>";
    // now we shoud do something like ......
    // create temp table with a visitorid (like in subsettosourceid)     
    // insert into temp table select id from $profile->tablename_urls where url like '%dude%'
    // then, match that back to original query with a join
    $query = "CREATE TEMPORARY TABLE searchtable_temp (searchid varchar(32), KEY `search_id` (`searchid`))";
    $db->Execute($query);
    
    $query = "INSERT INTO searchtable_temp select id from $tables where $searchst";
    $db->Execute($query);
    //$searchquery = "and $field IN (select id from $tables where $searchst)";
    
    $searchst = " JOIN searchtable_temp on url=searchid ";
    //return $searchquery;
    //echo "</div>";
    return $searchst;    
}

function SearchMatchingIDs($search,$field,$searchmode,$tables) {
    global $db;
    //this function makes a subquery that returns matching id's
    
    if (strpos($search," and ")!=FALSE) {
        $searchitems=explode(" and ", $search);
        $andor="AND";
    } else if (strpos($search," or ")!=FALSE) {
        $searchitems=explode(" or ", $search);
        $andor="OR";
    } else {
        $searchitems[0]= $search;
        $andor="AND";
    } 
    $i=0;
    $searchst="($field $searchmode '%$searchitems[$i]%' ";
    $i++;
    while (@$searchitems[$i]!="") {
        $searchst.="$andor $field $searchmode '%$searchitems[$i]%' ";
        $i++;
    }
    $searchst.=")";
   
    $searchst = "and $field IN (select id from $tables where $searchst)";
    return $searchst;    
}


function GetQuery($labels,$showfields,$from,$to,$item="",$item2=""){
	global $conf;
	global $from;
	global $to;
	global $query;
	global $report_graph;	global $l,$t;
	global $submit;
	global $showfields,$addlabel,$nograph,$status,$agent,$limit,$ptarget,$targets,$vnum,$help,$data,$gi;
	global $profile;
	global $db;
	global $databasedriver;
	global $search, $searchmode;
    global $summary;
    global $old;
    global $applytrafficsource;
    global $cnames;
    global $roadto;
	global $opengraph;
	global $reportoptions;
	$searchst="";
	$drilldown = "reports.html?submit=yes&from=$from&to=$to&item=;xx&tag=;yy&";
	//$nc = "SQL_NO_CACHE";
    $nc="";     
    
	if (!$limit) {
		 $limit=100;
	}
	if ($vnum < 4) {
		$sqlmethod="use index (timestamp)";
		
	} else {
		$sqlmethod="force index (timestamp)";
	}
    
	if (!isset($gi)) {
        include_once("components/geoip/open_geoip.php");
    }
	
	if ($labels==_VISITORS_AND_VISITS) {
        
        
        if ($databasedriver=="sqlite") {
            $showfields = _DATE.','._VISITORS.','._VISITS.','._VISITS_PER_USER; 
            //$query  = "select FROM_UNIXTIME(timestamp,'%d-%b-%Y %a') AS days, count(distinct visitorid) as visitors,count(distinct sessionid) as visits from $profile->tablename where timestamp >=$from and timestamp <=$to and crawl=0 group by days order by timestamp";
            $query  = "select FROM_UNIXTIME(timestamp,'%d-%b-%Y %a') AS days, count(distinct visitorid) as visitors,count(distinct sessionid) as visits, ((count(distinct sessionid)*1.00)/count(distinct visitorid)) as vpu from $profile->tablename  where timestamp >=$from and timestamp <=$to and crawl=0 group by days order by timestamp";     
        }  else {
            $showfields = _DATE.','._VISITORS.','._VISITS.','._VISITS_PER_USER; 
            
            if (isset($_SESSION["trafficsource"]) && ($_SESSION["trafficsource"] > 0)) {
                // If we're using a traffic source, then we can't use the summary tables.            
                $query  = "select FROM_UNIXTIME(timestamp,'%d-%b-%Y %a') AS days, count(distinct visitorid) as visitors,count(distinct sessionid) as visits, (count(distinct sessionid)/count(distinct visitorid)) as vpu from $profile->tablename $sqlmethod where timestamp >=$from and timestamp <=$to and crawl=0 group by days order by timestamp";
            } else {
                // Use the summary table.
                $query = "select days,visitors,visits,(visits/visitors) vpu from $profile->tablename_vpd where timestamp >=$from and timestamp <=$to order by timestamp";
            }
        }
        $help=_VISITORS_AND_VISITS_DESC;
		
		$reportoptions = "daterangeField,trafficsource";
	}
    
    if ($labels==_VISIT_DURATION) {
        $showfields = _TIME_SPENT.','._VISIT_SHARE.','._VISITS.','._AVERAGE_DURATION_IN_MINUTES;
        
        $help=_VISIT_DURATION_DESC;
        
        $ServerInfo = $db->ServerInfo(); 
        if ($ServerInfo["version"] < 4 && $databasedriver!="sqlite") {
            echo _ONLY_WORKS_WITH_MYSQL_4_PLUS.":" .$ServerInfo["description"].$ServerInfo["version"];
            exit();   
        } else {
        
        //we can't do a temporary table in mysql 5, so drop and 
        $prequery = "drop table ".$profile->tablename."_vlength";
        @$db->Execute($prequery);
        
        $prequery = "create table ".$profile->tablename."_vlength (length int(11), visitorid char(32)) ENGINE=MyISAM CHARSET=utf8";
        $db->Execute($prequery);
       
        $query = subsetDataToSourceID("insert into ".$profile->tablename."_vlength select (max(timestamp)-min(timestamp)), visitorid from $profile->tablename force index (timestamp) where timestamp >=$from and timestamp <=$to and status=200 and crawl=0 group by sessionid");
         
        $db->Execute($query);
        
        $range = $db->Execute("select min(length), max(length),count(*) from ".$profile->tablename."_vlength");
        $range_data = $range->FetchRow();
        $min = $range_data[0];
        $max = $range_data[1];
        $total_visitors=$range_data[2];
        $blocksize=($max-$min)/8;
        $query  = subsetDataToSourceID("select \"0 to 10 seconds            \", ((count(*)*1.0)/$total_visitors*100),count(*), avg(length)/60, \"1\" as ord from ".$profile->tablename."_vlength where length >=0 and length <=10 union ");
        $query  .= subsetDataToSourceID("select \"10 to 60 seconds            \", ((count(*)*1.0)/$total_visitors*100),count(*), avg(length)/60, \"2\" as ord from ".$profile->tablename."_vlength where length >=10 and length <=60 union ");
        $query  .= subsetDataToSourceID("select \"1 to 5 minutes            \", ((count(*)*1.0)/$total_visitors*100),count(*), avg(length)/60, \"3\" as ord from ".$profile->tablename."_vlength where length >=60 and length <=300 union ");
        $query  .= subsetDataToSourceID("select \"5 to 15 minutes            \", ((count(*)*1.0)/$total_visitors*100),count(*), avg(length)/60, \"4\" as ord from ".$profile->tablename."_vlength where length >=300 and length <=900 union ");
        $query  .= subsetDataToSourceID("select \"15 to 30 minutes            \", ((count(*)*1.0)/$total_visitors*100),count(*), avg(length)/60, \"5\" as ord from ".$profile->tablename."_vlength where length >=900 and length <=1800 union ");
        $query  .= subsetDataToSourceID("select \"30 to 1 hour            \", ((count(*)*1.0)/$total_visitors*100),count(*), avg(length)/60, \"6\" as ord from ".$profile->tablename."_vlength where length >=1800 and length <=3600 union ");
        $query  .= subsetDataToSourceID("select \"more than 1 hour            \", ((count(*)*1.0)/$total_visitors*100),count(*), avg(length)/60, \"7\" as ord from ".$profile->tablename."_vlength where length >=3600");
        
        $query .= " order by ord";
        
        
        
        $applytrafficsource = false;
        }
        
		$reportoptions = "daterangeField,trafficsource";
	}
    if ($labels==_TOTAL_DURATION) {
        $showfields = _TIME_SPENT.','._VISIT_SHARE.','._VISITS.','._AVERAGE_DURATION_IN_MINUTES;
        
        $help=_TOTAL_DURATION_DESC;
        
        $ServerInfo = $db->ServerInfo(); 
        if ($ServerInfo["version"] < 4 && $databasedriver!="sqlite") {
            echo _ONLY_WORKS_WITH_MYSQL_4_PLUS.":" .$ServerInfo["version"];
            exit();   
        } else {
        //we can't do a temporary table in mysql 5, so drop and 
        $prequery = "drop table ".$profile->tablename."_vlength";
        @$db->Execute($prequery);
        $prequery = "drop table ".$profile->tablename."_tlength";
        @$db->Execute($prequery);
        
        $prequery = "create table ".$profile->tablename."_vlength (length int(11), visitorid int(11), KEY vl (visitorid,length)) ENGINE=MyISAM CHARSET=utf8";
        $db->Execute($prequery);
        $prequery = "create table ".$profile->tablename."_tlength (length int(11), visitorid int(11), KEY vl (visitorid,length)) ENGINE=MyISAM CHARSET=utf8";
        $db->Execute($prequery);
        $prequery = subsetDataToSourceID("insert into ".$profile->tablename."_vlength select (max(timestamp)-min(timestamp)), visitorid from $profile->tablename force index (timestamp) where timestamp >=$from and timestamp <=$to and status=200 and crawl=0 group by sessionid");
        $db->Execute($prequery);
        $db->Execute("insert into ".$profile->tablename."_tlength select sum(length), visitorid from ".$profile->tablename."_vlength group by visitorid");
       
        $range = $db->Execute("select min(length), max(length),count(*) from ".$profile->tablename."_tlength");
        $range_data = $range->FetchRow();
        $min = $range_data[0];
        $max = $range_data[1];
        $total_visitors=$range_data[2];
        $blocksize=($max-$min)/8;
        $query  = subsetDataToSourceID("select \"0 to 10 seconds            \", ((count(*)*1.0)/$total_visitors*100),count(*), avg(length)/60, \"1\" as ord from ".$profile->tablename."_tlength where length >=0 and length <=10 union ");
        $query  .= subsetDataToSourceID("select \"10 to 60 seconds            \", ((count(*)*1.0)/$total_visitors*100),count(*), avg(length)/60, \"2\" as ord from ".$profile->tablename."_tlength where length >=10 and length <=60 union ");
        $query  .= subsetDataToSourceID("select \"1 to 5 minutes            \", ((count(*)*1.0)/$total_visitors*100),count(*), avg(length)/60, \"3\" as ord from ".$profile->tablename."_tlength where length >=60 and length <=300 union ");
        $query  .= subsetDataToSourceID("select \"5 to 15 minutes            \", ((count(*)*1.0)/$total_visitors*100),count(*), avg(length)/60, \"4\" as ord from ".$profile->tablename."_tlength where length >=300 and length <=900 union ");
        $query  .= subsetDataToSourceID("select \"15 to 30 minutes            \", ((count(*)*1.0)/$total_visitors*100),count(*), avg(length)/60, \"5\" as ord from ".$profile->tablename."_tlength where length >=900 and length <=1800 union ");
        $query  .= subsetDataToSourceID("select \"30 to 1 hour            \", ((count(*)*1.0)/$total_visitors*100),count(*), avg(length)/60, \"6\" as ord from ".$profile->tablename."_tlength where length >=1800 and length <=3600 union ");
        $query  .= subsetDataToSourceID("select \"more than 1 hour            \", ((count(*)*1.0)/$total_visitors*100),count(*), avg(length)/60, \"7\" as ord from ".$profile->tablename."_tlength where length >=3600");
        $query .=" order by ord";
        global $applytrafficsource;
        $applytrafficsource = false;
        }
        
		$reportoptions = "daterangeField,trafficsource";
	}
    
    if ($labels==_TIME_ON_SITE_TODAY) {
        $todaysdate=$to; 
        $from   = mktime(0,0,0,date("m", $todaysdate),date("d", $todaysdate),date("Y", $todaysdate));
        $to     = mktime(23,59,59,date("m", $todaysdate),date("d", $todaysdate),date("Y", $todaysdate));
        $showfields = _TIME_SPENT.','._VISITORS;
        
        $help=_TIME_ON_SITE_TODAY_DESC;
        
        $ServerInfo = $db->ServerInfo(); 
        if ($ServerInfo["version"] < 4 && $databasedriver!="sqlite") {
            echo _ONLY_WORKS_WITH_MYSQL_4_PLUS.":" .$ServerInfo["version"];
            exit();   
        } else {
        //we can't do a temporary table in mysql 5, so drop and 
        $prequery = "drop table ".$profile->tablename."_vlength";
        @$db->Execute($prequery);
        $prequery = "drop table ".$profile->tablename."_tlength";
        @$db->Execute($prequery);
        
        $prequery = "create table ".$profile->tablename."_vlength (length int(11), visitorid char(32)) ENGINE=MyISAM CHARSET=utf8";
        $db->Execute($prequery);
        $prequery = "create table ".$profile->tablename."_tlength (length int(11), visitorid char(32)) ENGINE=MyISAM CHARSET=utf8";
        $db->Execute($prequery);
        $prequery = subsetDataToSourceID("insert into ".$profile->tablename."_vlength select (max(timestamp)-min(timestamp)), visitorid from $profile->tablename where timestamp >=$from and timestamp <=$to and status=200 and crawl=0 group by sessionid");
        $db->Execute($prequery);
        $db->Execute("insert into ".$profile->tablename."_tlength select sum(length), visitorid from ".$profile->tablename."_vlength group by visitorid");
       
        $range = $db->Execute("select min(length), max(length),count(*) from ".$profile->tablename."_tlength");
        $range_data = $range->FetchRow();
        $min = $range_data[0];
        $max = $range_data[1];
        $total_visitors=$range_data[2];
        $blocksize=($max-$min)/8;
        $query  = subsetDataToSourceID("select \"0 to 10 seconds            \", count(*), \"1\" as ord from ".$profile->tablename."_tlength where length >=0 and length <=10 union ");
        $query  .= subsetDataToSourceID("select \"10 to 60 seconds            \", count(*), \"2\" as ord from ".$profile->tablename."_tlength where length >=10 and length <=60 union ");
        $query  .= subsetDataToSourceID("select \"1 to 5 minutes            \", count(*), \"3\" as ord from ".$profile->tablename."_tlength where length >=60 and length <=300 union ");
        $query  .= subsetDataToSourceID("select \"5 to 15 minutes            \", count(*), \"4\" as ord from ".$profile->tablename."_tlength where length >=300 and length <=900 union ");
        $query  .= subsetDataToSourceID("select \"15 to 30 minutes            \", count(*), \"5\" as ord from ".$profile->tablename."_tlength where length >=900 and length <=1800 union ");
        $query  .= subsetDataToSourceID("select \"30 to 1 hour            \", count(*), \"6\" as ord from ".$profile->tablename."_tlength where length >=1800 and length <=3600 union ");
        $query  .= subsetDataToSourceID("select \"more than 1 hour            \", count(*), \"7\" as ord from ".$profile->tablename."_tlength where length >=3600");
        $query .=" order by ord";
        
        $applytrafficsource = false;
        }
	}
    if ($labels==_VISITORS_PER_DAY) {
	    $showfields = _DATE.','._VISITORS.','._PAGEVIEWS.','._PAGES_PER_USER;
		if (isset($_SESSION["trafficsource"]) && ($_SESSION["trafficsource"] > 0)) {
			// If we're using a traffic source, then we can't use the summary tables.
            
			$query  = "select FROM_UNIXTIME(timestamp,'%d-%b-%Y %a') AS days, count(distinct visitorid) as visitors,count(*) as pages, (count(*)/count(distinct visitorid)) as ppu from $profile->tablename $sqlmethod where timestamp >=$from and timestamp <=$to and crawl=0 group by days order by timestamp";
		} else {
			// Use the summary table.
			$query="select days,visitors,pages,(pages/visitors) ppu from $profile->tablename_vpd where timestamp >=$from and timestamp <=$to order by timestamp";
		}
		$help=_VISITORS_PER_DAY_DESC;
		$reportoptions = "daterangeField,trafficsource";
	}
    	
    if ($labels==_VISITORS_PER_MONTH) {
		$showfields = _DATE.','._VISITORS.','._PAGEVIEWS.','._PAGES_PER_USER;
        if (isset($_SESSION["trafficsource"]) && ($_SESSION["trafficsource"] > 0)) {
			// If we're using a traffic source, then we can't use the summary tables.
			$query  = "select FROM_UNIXTIME(timestamp,'%M %Y') AS month, count(distinct visitorid) as visitors,count(*) as pages, (count(*)/count(distinct visitorid)) as ppu from $profile->tablename where timestamp >=$from and timestamp <=$to and crawl=0 group by month order by timestamp";
		} else {
			// Use the summary table.
			$query="select month,visitors,pages,(pages/visitors) as ppu from $profile->tablename_vpm where timestamp >=$from and timestamp <=$to order by timestamp";
		}
		$help=_VISITORS_PER_MONTH_DESC;
		$reportoptions = "daterangeField,trafficsource";
	}
    
    /* this is an extended version of the visitors per month report, with more columns
    if ($labels==_VISITORS_PER_MONTH) {
        $showfields = _DATE.','._VISITORS.','._VISITS.','._PAGEVIEWS.','._PAGES_PER_USER.','._VISITS_PER_USER;
        if (isset($_SESSION["trafficsource"]) && ($_SESSION["trafficsource"] > 0)) {
            // If we're using a traffic source, then we can't use the summary tables.
            $query  = "select FROM_UNIXTIME(timestamp,'%M %Y') AS month, count(distinct visitorid) as visitors,count(*) as pages, (count(*)/count(distinct visitorid)) as ppu from $profile->tablename where timestamp >=$from and timestamp <=$to and crawl=0 group by month order by timestamp";
        } else {
            // Use the summary table.
            $query="select month,visitors,visits,pages,(pages/visitors) as ppu,(visits/visitors) as vpu from $profile->tablename_vpm where timestamp >=$from and timestamp <=$to order by timestamp";
        }
        $help=_VISITORS_PER_MONTH_DESC;
    }
    */	
	if ($labels==_VISITORS_PER_HOUR) {
		$showfields = _HOUR.','._VISITORS.','._PAGEVIEWS.','._PAGES_PER_USER;
		$query  = "select FROM_UNIXTIME(timestamp,'%H') AS hours, count(distinct visitorid) as visitors,count(*) as pages, (count(*) * 1.00) / count(distinct visitorid) as ppu from $profile->tablename where timestamp >=$from and timestamp <=$to and crawl=0 group by hours order by hours";
		$help=_VISITORS_PER_HOUR_DESC;
		$reportoptions = "daterangeField,trafficsource";
	}
        
    if ($labels==_TODAYS_TOP_PAGES) {
        $todaysdate=$to; 
        $from   = mktime(0,0,0,date("m", $todaysdate),date("d", $todaysdate),date("Y", $todaysdate));
        $to     = mktime(23,59,59,date("m", $todaysdate),date("d", $todaysdate),date("Y", $todaysdate));
        $showfields = _PAGE.','._VISITORS.','._PAGEVIEWS;
        
        $subquery = "select url,count(distinct visitorid) as visitors,count(*) as hits from $profile->tablename where timestamp >=$from and timestamp <=$to and crawl=0 $searchst group by url order by visitors desc limit $limit";                        
        $query = "select CONCAT(r.url,'##',r.title) as urlinfo, sq.visitors, sq.hits from ($subquery) as sq, $profile->tablename_urls as r where sq.url=r.id";
        $help=_TODAYS_TOP_PAGES_DESC;
	}
    
    if ($labels==_TOP_PAGES_LAST_HOUR) {
        $q = $db->Execute("select timestamp from $profile->tablename order by timestamp desc limit 1");
        $data=$q->FetchRow();
        $to = $data['timestamp'];
        $from = $to-(60*60);

        $showfields = _PAGE.','._VISITORS.','._PAGEVIEWS;
        
        $subquery = "select url,count(distinct visitorid) as visitors,count(*) as hits from $profile->tablename where timestamp >=$from and timestamp <=$to and crawl=0 $searchst group by url order by visitors desc limit $limit";                        
        $query = "select CONCAT(r.url,'##',r.title) as urlinfo, sq.visitors, sq.hits from ($subquery) as sq, $profile->tablename_urls as r where sq.url=r.id";
        $help=_TODAYS_TOP_PAGES_DESC;
    }
    
	if ($labels==_TOP_PAGES) {
	    $showfields = _PAGE.','._VISITORS.','._PAGEVIEWS;
        if (@$old) {
            if ($search) {
                $searchst = MakeSearchString($search,"r.url",$searchmode);
            }
            $query  = "select r.url,count(distinct visitorid) as visitors,count(*) as hits from $profile->tablename, $profile->tablename_urls as r where timestamp >=$from and timestamp <=$to $searchst and r.id=$profile->tablename.url and crawl=0 group by $profile->tablename.url order by visitors desc limit $limit";
        } else {
            if ($search) {
                //$searchst = MakeSearchString($search,"r.url",$searchmode);
                //$searchst = FindMatchingIDs($search,"url",$searchmode,$profile->tablename_urls);
                $searchst = SearchMatchingIDs($search,"url",$searchmode,$profile->tablename_urls);
            }
            //$subquery = subsetDataToSourceID("select url,count(distinct visitorid) as visitors,count(*) as hits from $profile->tablename $searchst where timestamp >=$from and timestamp <=$to and crawl=0 group by url order by visitors desc limit $limit");
            $subquery = subsetDataToSourceID("select $nc url,count(distinct visitorid) as visitors,count(*) as hits from $profile->tablename where timestamp >=$from and timestamp <=$to and crawl=0 $searchst group by url order by visitors desc limit $limit");
                        
            $query = "select $nc CONCAT(r.url,'##',r.title) as urlinfo, sq.visitors, sq.hits from ($subquery) as sq, $profile->tablename_urls as r where sq.url=r.id";
            $applytrafficsource=false;  
        }
		$help=_TOP_PAGES_DESC;

		$reportoptions = "daterangeField,trafficsource,search,limit";
		$searchmode = "like";
	}
    
    // this is a test report to check the new estimates system - work in progress
    if ($labels==_TOP_PAGES_TEST) {
        $showfields = _PAGE.','._VISITORS.',Corrected,'._PAGEVIEWS;
        
        $start=getmicrotime(); 
        $premem = memory_get_usage();
        echo "we are using $premem<br>"; 
        
        /* this is an interesting little test that lets php do most of the work 
        $q = "select url,visitorid from $profile->tablename where timestamp >=$from and timestamp <=$to and crawl=0";
        $db->SetFetchMode(ADODB_FETCH_NUM);
        $result = $db->Execute($q);
        $rawdata = $result->GetArray();
        $db->SetFetchMode(ADODB_FETCH_BOTH);
        
        //print_r($rawdata);
        foreach ($rawdata as $row) {
            @$url_hits[$row[0]]++;             
        }
        arsort($url_hits);
        $url_hits = array_slice($url_hits, 0, 100, true);
        foreach($url_hits as $key => $val) {
            echo "urlid:$key - hits: $val<br>";    
        }        
        $usemem = (memory_get_usage()-$premem);
        $took = getmicrotime() - $start;  
        $query = "select \"we have ".count($data). " rows. used " . $usemem ." bytes / ". ($usemem/1024/1024). " MB (That took $took seconds)\"";
        */
        
        # we need to know how many days are in the date range
        $days = round(($to-$from)/86400);
        $lmonth = date("m Y",$to);
        //echoDebug("number of days in range is $days, we will check this with month $lmonth<br>");
        
        # first we get actual unique number for the most recent month in the range month from the month sum
        $q = "select $nc timestamp,url,visitors,inflation  from ".$profile->tablename."_urlsum_month where from_unixtime(timestamp, '%m %Y')='$lmonth' order by visitors desc limit $limit";  
        $result = $db->Execute($q);
        while ($data=$result->FetchRow()) {
            $msg = "actual count is ".$data['visitors'].", url =".$data['url']."<br>";
            $merge[$data['url']][0]=$data['visitors'];
            $n = date("d",$data['timestamp'])*1;
            $log = $data['inflation']-1;
            $msg= "log = ".$log;
            # we get a base for that
            $b = GetBase($n,$log);          
            $merge[$data['url']][1]=$b;
            $msg.="days in month (n) for this url is $n, log=$log and base is $b<br>";
            //echoDebug($msg);    
        }
        //echo "<pre>";
        //var_dump($merge);
        //echo "</pre>";
            
        # next we do our query based on daily uniques
        $q = "select $nc url,sum(visitors) visitors,sum(pageviews) as hits from ".$profile->tablename."_urlsum where (timestamp >=$from and timestamp <=$to) group by url order by visitors desc limit $limit";
        $result = $db->Execute($q);
        $i=0;
        while ($data=$result->FetchRow()) {
            $newdata[$i][0]=$data['url'];
            # for each url, we find our inflation factor (which is log)
            if (!isset($merge[$data['url']][0])) {
                echo "missing url ".$data['url'];    
            }
            $b = $merge[$data['url']][1];
            $msg.= " and the base is $b<br>";
            # then we adjust the daily unique sum to the log for the number of days in this date range
            $msg.= "so we correct with factor (log($days,$b)+1)".(log($days,$b)+1);
            $newdata[$i][1]=$data['visitors'];
            $newdata[$i][2]=round($data['visitors']/(log($days,$b)+1));
            $newdata[$i][3]=$data['hits'];
            $i++;
            //echoDebug($msg);                
        }
        $data = $newdata;
        $query="data array";

        $help=_TOP_PAGES_DESC;

    }
    
    // this report is used to check how correct the estimates are - work in progress
    if ($labels=="Integrity") {
        $showfields = _PAGE.',Sum daily uniques,Corrected,Actual,Difference';
                
        # we need to know how many days are in the date range 
        #(we should really make sure that $to is a date that actaully has data in the db!!)
        $days = round(($to-$from)/86400);
        $lmonth = date("m Y",$to);
        //echoDebug("number of days in range is $days, we will check this with month $lmonth<br>");
        
        # first we get actual unique number for the most recent month in the range month from the month sum
        $q = "select $nc timestamp,url,visitors,inflation  from ".$profile->tablename."_urlsum_month where from_unixtime(timestamp, '%m %Y')='$lmonth' order by visitors desc limit $limit";  
        $result = $db->Execute($q);
        while ($data=$result->FetchRow()) {
            $msg = "actual count is ".$data['visitors'].", url =".$data['url']."<br>";
            $merge[$data['url']][0]=$data['visitors'];
            $n = date("d",$data['timestamp'])*1;
            $log = $data['inflation']-1;
            $msg= "log = ".$log;
            # we get a base for that
            $b = GetBase($n,$log);          
            $merge[$data['url']][1]=$b;
            $msg.="days in month (n) for this url is $n, log=$log and base is $b<br>";
            //echoDebug($msg);    
        }
        //echo "<pre>";
        //var_dump($merge);
        //echo "</pre>";
            
        # next we do our query based on daily uniques
        $q = "select $nc url,sum(visitors) visitors,sum(pageviews) as hits from ".$profile->tablename."_urlsum where (timestamp >=$from and timestamp <=$to) group by url order by visitors desc limit $limit";
        $result = $db->Execute($q);
        $i=0;
        while ($data=$result->FetchRow()) {
            $u = $data['url'];
            $newdata[$u][0]=$data['url'];
            # for each url, we find our inflation factor (which is log)
            if (!isset($merge[$data['url']][0])) {
                echo "missing url ".$data['url'];    
            }
            $b = $merge[$data['url']][1];
            $msg.= " and the base is $b<br>";
            # then we adjust the daily unique sum to the log for the number of days in this date range
            $msg.= "so we correct with factor (log($days,$b)+1)".(log($days,$b)+1);
            $newdata[$u][1]=$data['visitors'];
            $newdata[$u][2]=(round($data['visitors']/(log($days,$b)+1)));
            $newdata[$u][3]=$data['hits'];
            $i++;
            //echoDebug($msg);                
        }
        //echo "<pre>";
        //var_dump($newdata);
        //echo "</pre>";
        
        # now we merge it with actual counts so we can check how good our estimates are
        $subquery = "select $nc url,count(distinct visitorid) as visitors,count(*) as hits from $profile->tablename where timestamp >=$from and timestamp <=$to and crawl=0 group by url order by visitors desc limit $limit";
        $query = "select $nc CONCAT(r.url,'##',r.title) as urlinfo, sq.url as u, sq.visitors as visitors from ($subquery) as sq, $profile->tablename_urls as r where sq.url=r.id";
        $result = $db->Execute($query);
        $i=0;
        while ($real=$result->FetchRow()) {
            $u = $real['u'];
            $data[$i][0]=$real['urlinfo'];
            $data[$i][1]=$newdata[$u][1];
            $data[$i][2]=number_format($newdata[$u][2],0);
            $data[$i][3]=$real['visitors'];
            //$data[$i][4]=number_format(((($newdata[$u][2]/$real['visitors'])*100)-100),3) . "%";
            $data[$i][4]=number_format(((($newdata[$u][2]/$real['visitors'])*100)-100),3) . "%";
            $i++;
        }
        $query="data array";

        $help=_TOP_PAGES_DESC;

    }
    // this 'report' really updates the monthly sum table for urls to contain the inflation factor - work in progress
    // at a later stage oif development, this should be moved to the update_summaries process
    if ($labels=="Inflation") {
        $showfields="Page,real count,sum count,inflation factor";
        $lmonth = date("m Y",$to);
        $addlabel = " for the month $lmonth";
        $subquery = "select $nc m.url as url,m.visitors as realcount, sum(d.visitors) as sumcount, sum(d.visitors)/m.visitors as inflation, m.timestamp as ts from ".$profile->tablename."_urlsum_month as m, ".$profile->tablename."_urlsum as d where from_unixtime(m.timestamp, '%m %Y')='$lmonth' and from_unixtime(d.timestamp, '%m %Y')='$lmonth' and m.url=d.url group by url";  
        $result = $db->Execute($subquery);
        $i=0;
        while ($data=$result->FetchRow()) {
            $db->Execute("update ".$profile->tablename."_urlsum_month set inflation=".$data['inflation']." where timestamp=".$data['ts']." and url=".$data['url']);    
            $i++;
        }        
        echoDebug("updated $i urls with inflation factors");
        $subquery .= " order by m.visitors desc limit $limit";
        $query = "select $nc CONCAT(r.url,'##',r.title) as urlinfo, sq.realcount, sq.sumcount, sq.inflation from ($subquery) as sq, $profile->tablename_urls as r where sq.url=r.id";

    }
	
	if ($labels==_TOP_PAGES_DETAILS) {
		$showfields = _PAGE.','._UNIQUE_IDS.','._PAGEVIEWS.','._TOTAL_REQUESTS.','._CRAWLED_PERC;
		if ($search) {
            //$searchst="and concat(r.url,rp.params) $searchmode '%$search%'";
            //$searchst = MakeSearchString($search,"concat(r.url,rp.params)",$searchmode);
            //$searchstr = FindMatchingIDs($search,"concat(r.url,rp.params)",$searchmode);
            //$searchst = SearchMatchingIDs($search,"concat(r.url,rp.params)",$searchmode,"$profile->tablename_urls as r, $profile->tablename_params as rp");
            //$searchst = "and concat(r.url,rp.params) IN (select concat(r.id,'.',rp.id) from $profile->tablename_urls as r, $profile->tablename_urlparams as rp where $searchst)";            
            //OK, if we have a search, it looks like the old way of doing the query is actually faster, in fact, this query seems to be faster with a search string than without ..., so, we'll just do the old method for now
            $old=1;
        }
        if (@$old) {
            if ($search) {
                $searchst = MakeSearchString($search,"concat(r.url,rp.params)",$searchmode);
            }
            $query  = "select concat(r.url,rp.params) as furl,count(distinct visitorid) as visitors,(count(*)-sum(crawl)) as viewed,count(*) as hits,(sum(crawl)/(count(*)*1.00)*100) as crawled from $profile->tablename, $profile->tablename_urls as r,$profile->tablename_urlparams as rp where timestamp >=$from and timestamp <=$to $searchst and $profile->tablename.url=r.id and $profile->tablename.params=rp.id and crawl!='2' group by furl order by visitors desc limit $limit";
        } else {
            
            $subquery  = subsetDataToSourceID("select concat(url,'.',params) as numfurl,url,params,count(distinct visitorid) as visitors,(count(*)-sum(crawl)) as viewed,count(*) as hits,(sum(crawl)/(count(*)*1.00)*100) as crawled from $profile->tablename where timestamp >=$from and timestamp <=$to and crawl!='2' $searchst group by numfurl order by visitors desc limit $limit");
                        
            $query = "select concat(r.url,rp.params,'##',r.title) as furl, sq.visitors, sq.viewed, sq.hits, sq.crawled FROM ($subquery) as sq, $profile->tablename_urls as r, $profile->tablename_urlparams as rp WHERE sq.url=r.id and sq.params=rp.id";
            $applytrafficsource=false;             
        }
		$help=_TOP_PAGES_DETAILS_DESC;
		$reportoptions = "daterangeField,trafficsource,search,limit";
		$searchmode = "like";
	}
    
    if ($labels==_TOP_FEEDS) {
        $showfields = _PAGE.','._VISITORS.','._PAGEVIEWS;
        if ($search) {
            //$searchst="and r.url $searchmode '%$search%'";
            $searchst = MakeSearchString($search,"r.url",$searchmode); 
        }
        $query  = "select r.url,count(distinct visitorid) as visitors,count(*) as hits from $profile->tablename, $profile->tablename_urls as r where timestamp >=$from and timestamp <=$to $searchst and r.id=$profile->tablename.url and crawl=2 group by $profile->tablename.url order by visitors desc limit $limit";
        
        $help=_TOP_FEEDS_DESC;

		$reportoptions = "daterangeField,trafficsource,limit";
	}
    
    if ($labels==_FEEDBURNER) {
        if (!function_exists('simplexml_load_file')) {
            $showfields = "Error";
            $query = "select \"This report requires the PHP 5 function 'simplexml_load_file' to be available\"";   
        } else if (!extension_loaded('openssl')) {
            $showfields = "Error";
            $query = "select \"This report requires the PHP extension 'openssl' to be loaded\"";     
        } else {
            $help=_FEEDBURNER_DESC;
            $showfields = "Date,Circulation,Hits,Reach";
            $dates = date("Y-m-d",$from).",".date("Y-m-d",$to);
            $xml = simplexml_load_file("https://feedburner.google.com/api/awareness/1.0/GetFeedData?uri={$profile->feedburneruri}&dates=$dates");
            $i=0;
            $data = array();
            while ($i < count($xml->feed->entry)) {
               $data[$i][0] = $xml->feed->entry[$i]['date'];
               $data[$i][1] = $xml->feed->entry[$i]['circulation'];
               $data[$i][2] = $xml->feed->entry[$i]['hits'];
               $data[$i][3] = $xml->feed->entry[$i]['reach'];
               $i++;       
            }
            $query="data array";
        }
		$reportoptions = "daterangeField,trafficsource,limit";
	}
    if ($labels=="Twitter Stats") {
        if (!function_exists('simplexml_load_file')) {
            $showfields = "Error";
            $query = "select \"This report requires the PHP 5 function 'simplexml_load_file' to be available\"";   
        } else {
            $help=_FEEDBURNER_DESC;
            $showfields = "Followers,Friends,Tweets";
            $xml = @simplexml_load_file("http://twitter.com/users/show/{$search}.xml");
            $i=0;
            $data = array();
            
            //while ($i < count($xml)) {
            if ($xml->followers_count) {
               $data[$i][0] = $xml->followers_count;
               $data[$i][1] = $xml->friends_count;
               $data[$i][2] = $xml->statuses_count;
               $i++;       
            }
            $query="data array";
        }
		$reportoptions = "daterangeField";
	}
    if ($labels==_TODAYS_TOP_REFERRERS) {
        $todaysdate=$to; 
        $from   = mktime(0,0,0,date("m", $todaysdate),date("d", $todaysdate),date("Y", $todaysdate));
        $to     = mktime(23,59,59,date("m", $todaysdate),date("d", $todaysdate),date("Y", $todaysdate));
        
        $showfields = _VISITORS.','._REFERRER;
        //$query  = "select count(distinct visitorid) as visitors, r.referrer from $profile->tablename,$profile->tablename_referrers as r  where timestamp >=$from and timestamp <=$to and $profile->tablename.referrer=r.id and r.referrer NOT like '%$profile->confdomain/%' and crawl=0 and status=200 $searchst group by r.referrer order by visitors desc limit $limit";
        $subquery = "select count(distinct visitorid) as visitors, referrer from $profile->tablename where timestamp >=$from and timestamp <=$to and crawl=0 and status=200 and referrer IN (select id from $profile->tablename_referrers where referrer NOT like 'http://$profile->confdomain/%') group by referrer order by visitors desc limit $limit";           
        $query = "select sq.visitors, r.referrer from ($subquery) as sq, $profile->tablename_referrers as r where sq.referrer=r.id";    
		$reportoptions = "daterangeField";
	}
    
	if ($labels==_TOP_REFERRERS) {
        $showfields = _VISITORS.','._HITS.','._REFERRER.','._LANDING_PAGE;
        if (@$old) {
            if ($search) {
                $searchst = MakeSearchString($search,"r.referrer",$searchmode); 
            }
            $query  = "select $nc count(distinct visitorid) as visitors, count(*) as hits,r.referrer,u.url from $profile->tablename,$profile->tablename_urls as u,$profile->tablename_referrers as r  where timestamp >=$from and timestamp <=$to and $profile->tablename.url=u.id and $profile->tablename.referrer=r.id and r.referrer NOT like '%$profile->confdomain/%' and crawl=0 and status=200 $searchst group by r.referrer order by visitors desc limit $limit";
        } else {
            if ($search) {
                $searchst = SearchMatchingIDs($search,"referrer",$searchmode,$profile->tablename_referrers);
            }
            //$subquery = subsetDataToSourceID("select count(distinct visitorid) as visitors,count(*) as hits, referrer, url from $profile->tablename where timestamp >=$from and timestamp <=$to and crawl=0 and status=200 $searchst and referrer IN (select id from $profile->tablename_referrers where referrer NOT like 'http://$profile->confdomain%' and referrer NOT like 'https://$profile->confdomain%') group by referrer order by visitors desc limit $limit");           
            $subquery = subsetDataToSourceID("select $nc count(distinct visitorid) as visitors,count(*) as hits, referrer, Substring_Index(Group_Concat(url order by timestamp desc),',',1) as url from $profile->tablename where timestamp >=$from and timestamp <=$to and crawl=0 and status=200 $searchst and referrer IN (select id from $profile->tablename_referrers where referrer NOT like 'http://$profile->confdomain%' and referrer NOT like 'https://$profile->confdomain%') group by referrer order by visitors desc limit $limit");           
            $query = "select $nc sq.visitors, sq.hits, r.referrer, u.url from ($subquery) as sq, $profile->tablename_urls as u,$profile->tablename_referrers as r where sq.referrer=r.id and sq.url=u.id";
            $applytrafficsource=false;
        }
        
		$help=_TOP_REFERRERS_DESC;
		$reportoptions = "daterangeField,trafficsource,search,limit";
		$searchmode = "like";
	}
	
    if ($labels=="Top Referrers test") {
        $showfields = _VISITORS.','._HITS.','._REFERRER.','._LANDING_PAGE;
        if (@$old) {
            if ($search) {
                $searchst = MakeSearchString($search,"r.referrer",$searchmode); 
            }
            $query  = "select $nc count(distinct visitorid) as visitors, count(*) as hits,r.referrer,u.url from $profile->tablename,$profile->tablename_urls as u,$profile->tablename_referrers as r  where timestamp >=$from and timestamp <=$to and $profile->tablename.url=u.id and $profile->tablename.referrer=r.id and r.referrer NOT like '%$profile->confdomain/%' and crawl=0 and status=200 $searchst group by r.referrer order by visitors desc limit $limit";
        } else {
            if ($search) {
                $searchst = SearchMatchingIDs($search,"referrer",$searchmode,$profile->tablename_referrers);
            }
            //$subquery = subsetDataToSourceID("select count(distinct visitorid) as visitors,count(*) as hits, referrer, url from $profile->tablename where timestamp >=$from and timestamp <=$to and crawl=0 and status=200 $searchst and referrer IN (select id from $profile->tablename_referrers where referrer NOT like 'http://$profile->confdomain%' and referrer NOT like 'https://$profile->confdomain%') group by referrer order by visitors desc limit $limit");           
            $subquery = subsetDataToSourceID("select $nc count(distinct visitorid) as visitors,count(*) as hits, referrer, Substring_Index(Group_Concat(url order by timestamp desc),',',1) as url from $profile->tablename force index (reftime) where timestamp >=$from and timestamp <=$to and crawl=0 and status=200 $searchst and referrer IN (select id from $profile->tablename_referrers where referrer NOT like 'http://$profile->confdomain%' and referrer NOT like 'https://$profile->confdomain%') group by referrer order by visitors desc limit $limit");           
            $query = "select $nc sq.visitors, sq.hits, r.referrer, u.url from ($subquery) as sq, $profile->tablename_urls as u,$profile->tablename_referrers as r where sq.referrer=r.id and sq.url=u.id";
            $applytrafficsource=false;
        }
        
        $help=_TOP_REFERRERS_DESC;
    }
	
	if ($labels==_TOP_REFERRERS_DETAILS){
	    if ($search) {
            //$searchst="and concat(r.referrer,rp.params) $searchmode '%$search%'";
            $searchst = MakeSearchString($search,"concat(r.referrer,rp.params)",$searchmode);
        }
		$showfields = _VISITORS.','._HITS.','._REFERRER.','._LANDING_PAGE;
		//$query  = "select count(distinct visitorid) as visitors,count(*) as hits,concat(referrer,refparams) as furl,concat(url,params) as nurl from $profile->tablename where timestamp >=$from and timestamp <=$to and referrer NOT like '%$profile->confdomain/%' and crawl=0 and status=200 $searchst group by furl order by visitors desc limit $limit";
        $query  = "select count(distinct visitorid) as visitors,count(*) as hits,concat(r.referrer,rp.params) as furl,concat(Substring_Index(Group_Concat(u.url order by timestamp desc),',',1),Substring_Index(Group_Concat(up.params order by timestamp desc),',',1)) as nurl from $profile->tablename as pt,$profile->tablename_referrers as r, $profile->tablename_refparams as rp,$profile->tablename_urls as u, $profile->tablename_urlparams as up where timestamp >=$from and timestamp <=$to and pt.url=u.id and pt.params=up.id and pt.referrer=r.id and pt.refparams=rp.id and r.referrer NOT like 'http://$profile->confdomain/%' and crawl=0 and status=200 $searchst group by furl order by visitors desc limit $limit";
		$help=_TOP_REFERRERS_DETAILS_DESC;
		$reportoptions = "daterangeField,trafficsource,search,limit";
		$searchmode = "like";
	}
    
    if ($labels==_GOOGLE_RANKINGS) {
        if ($search) {
            //$search=str_replace(" ", "%",$search);
            $searchst="and k.keywords $searchmode '%".str_replace(" ", "%",$search)."%'";
            //$searchst = MakeSearchString($search,"k.keywords",$searchmode);
        }
        $showfields = _KEYWORDS.','._VISITORS.','._SEARCH_RESULT_PAGE;
        //$query  = "select count(distinct visitorid) as visitors,count(keywords) as hits,keywords, url, params from $profile->tablename where timestamp >=$from and timestamp <=$to and keywords!='' and crawl=0 $searchst group by keywords order by visitors desc limit $limit";
        $query  = "select k.keywords,count(distinct visitorid) as visitors,(SUBSTR(rp.params,(LOCATE('start',rp.params)+6),2))/10+1 as page, rp.params from $profile->tablename as a,$profile->tablename_keywords as k,$profile->tablename_urls as u,$profile->tablename_urlparams as up,$profile->tablename_refparams as rp, $profile->tablename_referrers as r where timestamp >=$from and timestamp <=$to and a.keywords=k.id and a.url=u.id and a.params=up.id and a.refparams=rp.id and a.referrer=r.id and k.keywords!='' and up.params not like '?gclid%' and r.referrer like 'http://www.google.%' and crawl=0 $searchst group by a.keywords,page order by visitors desc limit $limit";
        $help=_GOOGLE_RANKINGS_DESC;
        
      
		$reportoptions = "daterangeField,trafficsource,search,limit";
		$searchmode = "like";
	}
	
    if ($labels=="Yahoo Rankings") {
        if ($search) {
            //$search=str_replace(" ", "%",$search);
            $searchst="and k.keywords $searchmode '%".str_replace(" ", "%",$search)."%'";
        }
        $showfields = _KEYWORDS.','._SEARCH_RESULT_PAGE.','._VISITORS.','._PAGE;
        //$query  = "select count(distinct visitorid) as visitors,count(keywords) as hits,keywords, url, params from $profile->tablename where timestamp >=$from and timestamp <=$to and keywords!='' and crawl=0 $searchst group by keywords order by visitors desc limit $limit";
        $query  = "select k.keywords, rp.params,count(distinct visitorid) as visitors,(SUBSTR(rp.params,(LOCATE('&b',rp.params)+3),2))/10+1 as page from $profile->tablename as a,$profile->tablename_keywords as k,$profile->tablename_urls as u,$profile->tablename_urlparams as up,$profile->tablename_refparams as rp, $profile->tablename_referrers as r where timestamp >=$from and timestamp <=$to and a.keywords=k.id and a.url=u.id and a.params=up.id and a.refparams=rp.id and a.referrer=r.id and k.keywords!='' and r.referrer like 'http://search.yahoo.%' and crawl=0 $searchst group by a.keywords,page order by visitors desc limit $limit";
	}
	
    if ($labels==_INTERNAL_SITE_SEARCH) {
		if (!$search || (strpos($search,"?")==FALSE)) {
			$search=@getProfileData($profile->profilename, "$profile->profilename.sitesearch");
		} 
		if ($search) {
			$showfields = _INTERNAL_KEYWORD.','._VISITORS.','._PAGE;
			//$search=str_replace(" ", "%",$search);
			//$searchst="and k.keywords $searchmode '%".str_replace(" ", "%",$search)."%'";
			
			$searche=explode("?",$search);
			$searchpage=$searche[0];
			if (@$searche[1]) {
				setProfileData($profile->profilename, "$profile->profilename.sitesearch", $search);
				$searchparam=$searche[1];
				$searchparamlen=strlen($searchparam);
				$searchparamlen2=strlen($searchparam)+2;
				$query  = "select (SUBSTR(up.params,(LOCATE('$searchparam',up.params)+$searchparamlen),(LOCATE('&',CONCAT(up.params,'&'))-$searchparamlen2))) as kw, count(distinct visitorid) as visitors,CONCAT(u.url,'?%$searchparam',(SUBSTR(up.params,(LOCATE('$searchparam',up.params)+$searchparamlen),(LOCATE('&',CONCAT(up.params,'&'))-$searchparamlen2)))) from $profile->tablename as a,$profile->tablename_urls as u,$profile->tablename_urlparams as up where timestamp >=$from and timestamp <=$to and a.url=u.id and a.params=up.id and up.params like '%$searchparam%' and u.url = '$searchpage' and crawl=0 $searchst group by kw order by visitors desc limit $limit";
			} else {
				$showfields = _INFO;

				$query="select \"Error: No ? character was found in your entry.\"";     
			}
		} else {
			$showfields = _INFO;

			$query="select \""._NO_INTERNAL_SITE_SEARCH_FOUND."\"";
		}
		$help=_INTERNAL_SITE_SEARCH_DESC;

		$reportoptions = "daterangeField,trafficsource,internal_site_search,limit";
	}
    
	if ($labels==_TOP_KEYWORDS) {
	    if ($search) {
            $searchst="and k.keywords $searchmode '%".str_replace(" ", "%",$search)."%'";
        }
		$showfields = _VISITORS.','._HITS.','._KEYWORDS.','._LANDING_PAGE.','._PARAMETERS;
		//$query  = "select count(distinct visitorid) as visitors,count(keywords) as hits,keywords, url, params from $profile->tablename where timestamp >=$from and timestamp <=$to and keywords!='' and crawl=0 $searchst group by keywords order by visitors desc limit $limit";
        $query  = "select $nc count(distinct visitorid) as visitors,count(a.keywords) as hits,k.keywords, Substring_Index(Group_Concat(u.url order by timestamp desc),',',1) as url, Substring_Index(Group_Concat(up.params order by timestamp desc),',',1) as params from $profile->tablename as a,$profile->tablename_keywords as k,$profile->tablename_urls as u,$profile->tablename_urlparams as up where timestamp >=$from and timestamp <=$to and a.keywords=k.id and a.url=u.id and a.params=up.id and k.keywords!='' and crawl=0 $searchst group by a.keywords order by visitors desc limit $limit";
        
		$help=_TOP_KEYWORDS_DESC;
		$reportoptions = "daterangeField,trafficsource,search,limit";
		$searchmode = "like";
	}
	
    if ($labels=="Top Keywords test") {
        if ($search) {
            $searchst="and k.keywords $searchmode '%".str_replace(" ", "%",$search)."%'";
        }
        $showfields = _VISITORS.','._HITS.','._KEYWORDS.','._LANDING_PAGE.','._PARAMETERS;
        //$query  = "select count(distinct visitorid) as visitors,count(keywords) as hits,keywords, url, params from $profile->tablename where timestamp >=$from and timestamp <=$to and keywords!='' and crawl=0 $searchst group by keywords order by visitors desc limit $limit";
        $query  = "select $nc count(distinct visitorid) as visitors,count(a.keywords) as hits,k.keywords, Substring_Index(Group_Concat(u.url order by timestamp desc),',',1) as url, Substring_Index(Group_Concat(up.params order by timestamp desc),',',1) as params from $profile->tablename as a force index(kwtime),$profile->tablename_keywords as k,$profile->tablename_urls as u,$profile->tablename_urlparams as up where timestamp >=$from and timestamp <=$to and a.keywords=k.id and a.url=u.id and a.params=up.id and k.keywords!='' and crawl=0 $searchst group by a.keywords order by visitors desc limit $limit";
        
        $help=_TOP_KEYWORDS_DESC;
    }
	
    if ($labels==_TODAYS_TOP_KEYWORDS) {
        $todaysdate=$to; 
        $from   = mktime(0,0,0,date("m", $todaysdate),date("d", $todaysdate),date("Y", $todaysdate));
        $to     = mktime(23,59,59,date("m", $todaysdate),date("d", $todaysdate),date("Y", $todaysdate));
        $showfields = _VISITORS.','._KEYWORDS;
        //$query  = "select count(distinct visitorid) as visitors,count(keywords) as hits,keywords, url, params from $profile->tablename where timestamp >=$from and timestamp <=$to and keywords!='' and crawl=0 $searchst group by keywords order by visitors desc limit $limit";
        $query  = "select count(distinct visitorid) as visitors,k.keywords from $profile->tablename as a,$profile->tablename_keywords as k where timestamp >=$from and timestamp <=$to and a.keywords=k.id and k.keywords!='' and crawl=0 $searchst group by a.keywords order by visitors desc limit $limit";
        
		$reportoptions = "daterangeField";
	}
	
	if ($labels==_TOP_KEYWORDS_DETAILS) {
	 
        if ($search) {
            if ($searchmode=="NOT LIKE") {
                $op="and";   
            } else {
                $op="or";   
            }
          $searchst="and (k.keywords $searchmode '%".str_replace(" ", "%",$search)."%' $op r.referrer $searchmode '%$search%' $op concat(u.url,up.params) $searchmode '%$search%')";
        }
  	    $showfields = _REFERRER.','._VISITORS.','._HITS.','._KEYWORDS.','._LANDING_PAGE.','._PARAMETERS;
		//$query  = "select referrer, count(distinct visitorid) as visitors, count(keywords) as hits,keywords, url, params from $profile->tablename where timestamp >=$from and timestamp <=$to and keywords!='' and crawl=0 $searchst group by keywords,referrer order by visitors desc limit $limit";
        
		$query  = "select r.referrer, count(distinct visitorid) as visitors, count(a.keywords) as hits,k.keywords, Substring_Index(Group_Concat(u.url order by timestamp desc),',',1) as url, Substring_Index(Group_Concat(up.params order by timestamp desc),',',1) as params from $profile->tablename as a,$profile->tablename_referrers as r,$profile->tablename_keywords as k, $profile->tablename_urls as u, $profile->tablename_urlparams as up where timestamp >=$from and timestamp <=$to and a.referrer=r.id and a.keywords=k.id and a.url=u.id and a.params=up.id and k.keywords!='' and crawl=0 $searchst group by a.keywords,a.referrer order by visitors desc limit $limit";
        //echo $query;                                                                            
        $help=_TOP_KEYWORDS_DETAILS_DESC;
		$reportoptions = "daterangeField,trafficsource,search,limit";
		$searchmode = "like";
	}

	if ($labels==_MOST_ACTIVE_USERS) {
	    if ($search) {
            //$searchst="and ipnumber $searchmode '%$search%'";
            $searchst = MakeSearchString($search,"v.ipnumber",$searchmode);  
        }
  	    $showfields = _REQUESTS.','._IP_NUMBER.','._COUNTRY.','._MEGABYTES;
		//$query  = "select count(*) as hits,v.ipnumber,country,(sum(bytes*1.00)/1024.0)/1024.0 as mb from $profile->tablename as a, $profile->tablename_visitorids as v where timestamp >=$from and timestamp <=$to and a.visitorid=v.id and crawl=0 $searchst group by a.visitorid order by hits desc limit $limit";
        $subquery  = "select count(*) as hits,visitorid,country,(sum(bytes*1.00)/1024.0)/1024.0 as mb from $profile->tablename where timestamp >=$from and timestamp <=$to and crawl=0 $searchst group by visitorid order by hits desc limit $limit";
        $subquery = subsetDataToSourceID($subquery);
        $applytrafficsource = false;
        $query = "SELECT hits, v.ipnumber, country, mb FROM ($subquery) as a, $profile->tablename_visitorids as v where a.visitorid=v.id  "; 
		$help=_MOST_ACTIVE_USERS_DESC;
		$reportoptions = "daterangeField,trafficsource,search,limit";
		$searchmode = "like";
	}
	
    if ($labels==_RECENT_VISITORS) {
        if ($search) {
            //$searchst="and ipnumber $searchmode '%$search%'";
            $searchst = MakeSearchString($search,"v.ipnumber",$searchmode);  
        }
        $showfields = _REQUESTS.','._IP_NUMBER.','._LAST_REQUESTS.','._COUNTRY;
        //$query  = "select count(*) as hits,visitorid,FROM_UNIXTIME(max(timestamp),'%d-%b-%Y %a %H:%i:%s') as time,country from $profile->tablename where timestamp >=$from and timestamp <=$to and crawl=0 group by visitorid order by timestamp desc limit $limit";
       // $query  = "select count(*) as hits,v.ipnumber,FROM_UNIXTIME(max(timestamp),'%d-%b-%Y %a %H:%i:%s') as time,country from $profile->tablename as a, $profile->tablename_visitorids as v where timestamp >=$from and timestamp <=$to and crawl=0 $searchst and a.visitorid=v.id group by a.visitorid order by time desc limit $limit";
        
        $subquery  = "select count(*) as hits,visitorid,FROM_UNIXTIME(max(timestamp),'%d-%b-%Y %a %H:%i:%s') as time,country from $profile->tablename where timestamp >=$from and timestamp <=$to and crawl=0 $searchst group by visitorid order by time desc limit $limit";
        $subquery = subsetDataToSourceID($subquery);
        $applytrafficsource = false;
        $query = "SELECT hits, v.ipnumber, time, country FROM ($subquery) as a, $profile->tablename_visitorids as v where a.visitorid=v.id";
        
        $help=_RECENT_VISITORS_DESC;
		$reportoptions = "daterangeField,trafficsource,search,limit";
		$searchmode = "like";
	}
	
    if ($labels==_TODAYS_TOP_VISITORS) {
        // this one if for the today page
        $todaysdate=$to; 
        $from   = mktime(0,0,0,date("m", $todaysdate),date("d", $todaysdate),date("Y", $todaysdate));
        $to     = mktime(23,59,59,date("m", $todaysdate),date("d", $todaysdate),date("Y", $todaysdate));
        
        $showfields = _REQUESTS.','._IP_NUMBER.','._COUNTRY;
        //$query  = "select count(*) as hits,visitorid,FROM_UNIXTIME(max(timestamp),'%d-%b-%Y %a %H:%i:%s') as time,country from $profile->tablename where timestamp >=$from and timestamp <=$to and crawl=0 group by visitorid order by timestamp desc limit $limit";
        $query  = "select count(*) as hits,v.ipnumber,country from $profile->tablename as a, $profile->tablename_visitorids as v where timestamp >=$from and timestamp <=$to and a.visitorid=v.id and crawl=0 $searchst group by a.visitorid order by hits desc limit $limit";
        
        $nograph=1;
	}
	
    if ($labels==_AUTHENTICATED_VISITORS) {
        $showfields = _REQUESTS.','._USERID.','._IP_NUMBER.','._COUNTRY;
        //$query  = "select count(*) as hits,authuser,ipnumber,country from $profile->tablename where timestamp >=$from and timestamp <=$to and crawl=0 and authuser!='' group by authuser order by hits desc limit $limit";
        $query  = "select count(*) as hits,v.authuser,v.ipnumber,country from $profile->tablename as a, $profile->tablename_visitorids as v where timestamp >=$from and timestamp <=$to and crawl=0 and a.visitorid=v.id and v.authuser!='' group by v.authuser order by hits desc limit $limit";                           
        $help=_AUTHENTICATED_VISITORS_DESC; 
		$reportoptions = "daterangeField,trafficsource,limit";
	}    
	
	if ($labels==_MOST_ACTIVE_CRAWLERS) {
        if ($search) {
            //$searchst="and ipnumber $searchmode '%$search%'";
            $searchst = MakeSearchString($search,"ua.useragent",$searchmode);  
        }
  	    $showfields = _REQUESTS.','._USER_AGENT.','._COUNTRY.','._MEGABYTES;
		$query  = "select count(*) as hits, ua.useragent,country,(sum(bytes*1.00)/1024.0)/1024.0 as mb from $profile->tablename left outer join ".$profile->tablename_useragents." ua on (useragentid = ua.id) where timestamp >=$from and timestamp <=$to and crawl=1 $searchst group by useragent order by hits desc limit $limit";
		$help=_MOST_ACTIVE_CRAWLERS_DESC;
		$reportoptions = "daterangeField,search,limit";
		$searchmode = "like";
	}
	
    if ($labels==_MOST_CRAWLED_PAGES) {
        //$showfields = "Page,Visitors,Pageviews,Total Requests,Crawled %";
        $showfields = _PAGE.','._BOTS.','._REQUESTS;
        if ($search) {
            //$searchst="and u.url $searchmode '%$search%'";
            $searchst = MakeSearchString($search,"u.url",$searchmode);
        }
        //$query  = "select url,count(distinct visitorid) as visitors,(count(*)-sum(crawl)) as viewed,count(*) as hits,(sum(crawl)/(count(*) * 1.00) *100) as crawled from $profile->tablename where timestamp >=$from and timestamp <=$to $searchst group by url order by visitors desc limit $limit";
        //$help="Definitions for this report:<ul><li>Page: The requested page, excluding any parameters (e.g. ?variable=1)<li>Pageviews: The total number of times this page was viewed by humans (excluding bots and crawlers)<li>Total requests: The total number of times this page was requested<li>Crawled %: The percentage of requests that were generated by bots or crawlers (like the googlebot indexer)</ul>";
        $query  = "select u.url,count(distinct visitorid) as visitors,count(*) as hits from $profile->tablename as a, $profile->tablename_urls as u where timestamp >=$from and timestamp <=$to and a.url=u.id $searchst and crawl=1 group by a.url order by visitors desc limit $limit";
        $help=_MOST_CRAWLED_PAGES_DESC;
		$reportoptions = "daterangeField,trafficsource,search,limit";
		$searchmode = "like";
	}
	
    if ($labels==_USER_AGENTS) {
		$showfields = _BROWSER.','._VISITORS;
		$addlabel=" - Also check the <a href=trends.php?conf=$profile->profilename&labels=Client%20Browser%20Trends>Client Browser Trends</a> Report for more browser details";
        $query  = "select ua.useragent, count(distinct visitorid) as visitors from $profile->tablename left outer join {$profile->tablename_useragents} AS ua on (useragentid = ua.id) where timestamp >=$from and timestamp <=$to and crawl=0 group by useragent order by visitors desc limit $limit";
        $help=_BROWSERS_DESC;
	}
    
    if ($labels==_MOBILE_AGENTS) {
		$showfields = _BROWSER.','._VISITORS;
		
        $query  = "select ua.useragent, count(distinct visitorid) as visitors from $profile->tablename left outer join {$profile->tablename_useragents} AS ua on (useragentid = ua.id) where ua.is_mobile=1 and timestamp >=$from and timestamp <=$to and crawl=0 group by useragent order by visitors desc limit $limit";
        //echo $query;
        $help=_BROWSERS_DESC;
		$reportoptions = "daterangeField,trafficsource,limit";
	}
    
	if ($labels==_BROWSERS) {
		$showfields = _BROWSER.','._VISITORS;
		$addlabel=" - Also check the <a href=trends.php?conf=$profile->profilename&labels=Client%20Browser%20Trends>Client Browser Trends</a> Report for more browser details";
		//$query  = "select count(distinct visitorid) as visitors,count(*) as hits,AGENTS.name useragent from $profile->tablename left outer join ".TBL_USER_AGENTS." AGENTS on (useragentid = AGENTS.id) where timestamp >=$from and timestamp <=$to and crawl=0 group by useragent order by visitors desc limit $limit";
		$help=_BROWSERS_DESC;
        $query = "SELECT CONCAT(ua.name, ' ', ua.version) AS agent, COUNT(distinct a.visitorid) AS visitors FROM {$profile->tablename} as a,{$profile->tablename_useragents} as ua where a.useragentid = ua.id and a.timestamp >= {$from} AND a.timestamp <={$to} AND a.status = 200 AND a.crawl = 0 AND ua.is_mobile = 0 GROUP BY agent ORDER BY visitors DESC LIMIT {$limit}";
        // $query= "select \"Internet Explorer 4\" useragent, count(distinct visitorid) as visitors from $profile->tablename left join ".TBL_USER_AGENTS." AGENTS on (useragentid = AGENTS.id) where timestamp >=$from and timestamp <=$to and status=200 and crawl=0 and (AGENTS.name like \"Internet Explorer 4%\") union ";
        
        // $query.= "select \"Internet Explorer 5\" useragent, count(distinct visitorid) as visitors from $profile->tablename left join ".TBL_USER_AGENTS." AGENTS on (useragentid = AGENTS.id) where timestamp >=$from and timestamp <=$to and status=200 and crawl=0 and (AGENTS.name like \"Internet Explorer 5%\") union ";
        
        // $query.= "select \"Internet Explorer 6\" useragent, count(distinct visitorid) as visitors from $profile->tablename left join ".TBL_USER_AGENTS." AGENTS on (useragentid = AGENTS.id) where timestamp >=$from and timestamp <=$to and status=200 and crawl=0 and (AGENTS.name like \"Internet Explorer 6%\") union ";
        
        // $query.= "select \"Internet Explorer 7\" useragent, count(distinct visitorid) as visitors from $profile->tablename left join ".TBL_USER_AGENTS." AGENTS on (useragentid = AGENTS.id) where timestamp >=$from and timestamp <=$to and status=200 and crawl=0 and (AGENTS.name like \"Internet Explorer 7%\") union ";
        
        // $query.= "select \"Internet Explorer 8\" useragent, count(distinct visitorid) as visitors from $profile->tablename left join ".TBL_USER_AGENTS." AGENTS on (useragentid = AGENTS.id) where timestamp >=$from and timestamp <=$to and status=200 and crawl=0 and (AGENTS.name like \"Internet Explorer 8%\") union "; 
        
        // $query.= "select \"Internet Explorer 9\" useragent, count(distinct visitorid) as visitors from $profile->tablename left join ".TBL_USER_AGENTS." AGENTS on (useragentid = AGENTS.id) where timestamp >=$from and timestamp <=$to and status=200 and crawl=0 and (AGENTS.name like \"Internet Explorer 9%\") union "; 
        
        // $query.= "select \"Internet Explorer (unknown version)\" useragent, count(distinct visitorid) as visitors from $profile->tablename left join ".TBL_USER_AGENTS." AGENTS on (useragentid = AGENTS.id) where timestamp >=$from and timestamp <=$to and status=200 and crawl=0 and (AGENTS.name like \"Internet Explorer%\") and (AGENTS.name NOT like \"Internet Explorer 7%\" and AGENTS.name not like \"Internet Explorer 6%\" and AGENTS.name not like \"Internet Explorer 5%\" and AGENTS.name not like \"Internet Explorer 4%\" and AGENTS.name not like \"Internet Explorer 8%\" and AGENTS.name not like \"Internet Explorer 9%\") union ";

		// $query.= "select \"Firefox 8\" useragent, count(distinct visitorid) as visitors from $profile->tablename left join ".TBL_USER_AGENTS." AGENTS on (useragentid = AGENTS.id) where timestamp >=$from and timestamp <=$to and status=200 and crawl=0 and (AGENTS.name like \"Firefox 8%\") union ";
		// $query.= "select \"Firefox 7\" useragent, count(distinct visitorid) as visitors from $profile->tablename left join ".TBL_USER_AGENTS." AGENTS on (useragentid = AGENTS.id) where timestamp >=$from and timestamp <=$to and status=200 and crawl=0 and (AGENTS.name like \"Firefox 7%\") union ";
		// $query.= "select \"Firefox 6\" useragent, count(distinct visitorid) as visitors from $profile->tablename left join ".TBL_USER_AGENTS." AGENTS on (useragentid = AGENTS.id) where timestamp >=$from and timestamp <=$to and status=200 and crawl=0 and (AGENTS.name like \"Firefox 6%\") union ";
		// $query.= "select \"Firefox 5\" useragent, count(distinct visitorid) as visitors from $profile->tablename left join ".TBL_USER_AGENTS." AGENTS on (useragentid = AGENTS.id) where timestamp >=$from and timestamp <=$to and status=200 and crawl=0 and (AGENTS.name like \"Firefox 5%\") union ";

        // $query.= "select \"Firefox 4\" useragent, count(distinct visitorid) as visitors from $profile->tablename left join ".TBL_USER_AGENTS." AGENTS on (useragentid = AGENTS.id) where timestamp >=$from and timestamp <=$to and status=200 and crawl=0 and (AGENTS.name like \"Firefox 4%\") union ";
		
		// $query.= "select \"Firefox 3\" useragent, count(distinct visitorid) as visitors from $profile->tablename left join ".TBL_USER_AGENTS." AGENTS on (useragentid = AGENTS.id) where timestamp >=$from and timestamp <=$to and status=200 and crawl=0 and (AGENTS.name like \"Firefox 3%\") union ";
        
        // $query.= "select \"Firefox 2\" useragent, count(distinct visitorid) as visitors from $profile->tablename left join ".TBL_USER_AGENTS." AGENTS on (useragentid = AGENTS.id) where timestamp >=$from and timestamp <=$to and status=200 and crawl=0 and (AGENTS.name like \"Firefox 2%\") union ";
        
        // $query.= "select \"Firefox 1\" useragent, count(distinct visitorid) as visitors from $profile->tablename left join ".TBL_USER_AGENTS." AGENTS on (useragentid = AGENTS.id) where timestamp >=$from and timestamp <=$to and status=200 and crawl=0 and (AGENTS.name like \"Firefox 1%\") union ";
        
        // $query.= "select \"Firefox (unknown version)\" useragent, count(distinct visitorid) as visitors from $profile->tablename left join ".TBL_USER_AGENTS." AGENTS on (useragentid = AGENTS.id) where timestamp >=$from and timestamp <=$to and status=200 and crawl=0 and (AGENTS.name like \"Firefox%\") and (AGENTS.name NOT like \"Firefox 1%\" and AGENTS.name not like \"Firefox 2%\" and AGENTS.name not like \"Firefox 3%\" and AGENTS.name not like \"Firefox 4%\" and AGENTS.name not like \"Firefox 5%\" and AGENTS.name not like \"Firefox 6%\" and AGENTS.name not like \"Firefox 7%\" and AGENTS.name not like \"Firefox 8%\") union ";      
        
        // $query.= "select \"Opera\" useragent, count(distinct visitorid) as visitors from $profile->tablename left join ".TBL_USER_AGENTS." AGENTS on (useragentid = AGENTS.id) where timestamp >=$from and timestamp <=$to and status=200 and crawl=0 and (AGENTS.name like \"Opera%\") union ";
        
        // $query.= "select \"Safari\" useragent, count(distinct visitorid) as visitors from $profile->tablename left join ".TBL_USER_AGENTS." AGENTS on (useragentid = AGENTS.id) where timestamp >=$from and timestamp <=$to and status=200 and crawl=0 and (AGENTS.name like \"Safari%\") union ";
        
        // $query.= "select \"Chrome\" useragent, count(distinct visitorid) as visitors from $profile->tablename left join ".TBL_USER_AGENTS." AGENTS on (useragentid = AGENTS.id) where timestamp >=$from and timestamp <=$to and status=200 and crawl=0 and (AGENTS.name like \"Chrome%\") union ";        
        
        // $query.= "select \"Mozilla Gecko Based\" useragent, count(distinct visitorid) as visitors from $profile->tablename left join ".TBL_USER_AGENTS." AGENTS on (useragentid = AGENTS.id) where timestamp >=$from and timestamp <=$to and status=200 and crawl=0 and (AGENTS.name like \"Mozilla%\" and AGENTS.name like \"%Gecko%\" and AGENTS.name not like \"%Safari%\" and AGENTS.name not like \"%Firefox%\") union ";
        
        // $query.= "select \"Mozilla Other (Non Gecko)\" useragent, count(distinct visitorid) as visitors from $profile->tablename left join ".TBL_USER_AGENTS." AGENTS on (useragentid = AGENTS.id) where timestamp >=$from and timestamp <=$to and status=200 and crawl=0 and (AGENTS.name like \"Mozilla%\" and AGENTS.name not like \"%Gecko%\" and AGENTS.name not like \"%Safari%\" and AGENTS.name not like \"%Firefox%\") union ";
        
        // $query.= "select \"Other Browsers\" useragent, count(distinct visitorid) as visitors from $profile->tablename left join ".TBL_USER_AGENTS." AGENTS on (useragentid = AGENTS.id) where timestamp >=$from and timestamp <=$to and status=200 and crawl=0 and (AGENTS.name NOT like \"Safari%\" and AGENTS.name not like \"Internet Explorer%\" and AGENTS.name not like \"Firefox%\" and AGENTS.name not like \"Opera%\" and AGENTS.name!=\"-\" and AGENTS.name not like \"%Mozilla%\" and AGENTS.name not like \"Chrome%\")";

        // $query.= " order by  visitors desc"; 
        
		$reportoptions = "daterangeField,trafficsource";
	}
	
    if ($labels==_OPERATING_SYSTEMS) {
		$showfields = _OPERATING_SYSTEM.','._VISITORS;
		$addlabel=" - Also check the <a href=trends.php?conf=$profile->profilename&labels=Client%20Browser%20Trends>Client Browser / OS Trends</a> Report for more details";
        //$query  = "select count(distinct visitorid) as visitors,count(*) as hits,AGENTS.name useragent from $profile->tablename left outer join ".TBL_USER_AGENTS." AGENTS on (useragentid = AGENTS.id) where timestamp >=$from and timestamp <=$to and crawl=0 group by useragent order by visitors desc limit $limit";
        $help=_OPERATING_SYSTEMS_DESC;
		
		$query = "SELECT CONCAT(ua.os,' ',ua.os_version) AS os_name, COUNT(DISTINCT a.visitorid) AS visitors FROM {$profile->tablename} AS a, {$profile->tablename_useragents} as ua WHERE a.useragentid = ua.id AND a.timestamp BETWEEN {$from} AND {$to} AND a.status = 200 AND a.crawl = 0 AND ua.is_mobile != 1 GROUP BY os_name ORDER BY visitors DESC";
        
        // $query= "select \"Windows NT\" useragent, count(distinct visitorid) as visitors from $profile->tablename left join ".TBL_USER_AGENTS." AGENTS on (useragentid = AGENTS.id) where timestamp >=$from and timestamp <=$to and status=200 and crawl=0 and (AGENTS.name like \"%Windows NT%\") union ";
        
        // $query.= "select \"Windows 9x\" useragent, count(distinct visitorid) as visitors from $profile->tablename left join ".TBL_USER_AGENTS." AGENTS on (useragentid = AGENTS.id) where timestamp >=$from and timestamp <=$to and status=200 and crawl=0 and (AGENTS.name like \"%Windows 9%\") union ";
        
        // $query.= "select \"Windows XP\" useragent, count(distinct visitorid) as visitors from $profile->tablename left join ".TBL_USER_AGENTS." AGENTS on (useragentid = AGENTS.id) where timestamp >=$from and timestamp <=$to and status=200 and crawl=0 and (AGENTS.name like \"%Windows XP%\") union "; 
        
        // $query.= "select \"Windows Vista\" useragent, count(distinct visitorid) as visitors from $profile->tablename left join ".TBL_USER_AGENTS." AGENTS on (useragentid = AGENTS.id) where timestamp >=$from and timestamp <=$to and status=200 and crawl=0 and (AGENTS.name like \"%Windows Vista%\") union ";
        
        // $query.= "select \"Windows 2000\" useragent, count(distinct visitorid) as visitors from $profile->tablename left join ".TBL_USER_AGENTS." AGENTS on (useragentid = AGENTS.id) where timestamp >=$from and timestamp <=$to and status=200 and crawl=0 and (AGENTS.name like \"%Windows 2000%\") union "; 
        
        // $query.= "select \"Windows 7\" useragent, count(distinct visitorid) as visitors from $profile->tablename left join ".TBL_USER_AGENTS." AGENTS on (useragentid = AGENTS.id) where timestamp >=$from and timestamp <=$to and status=200 and crawl=0 and (AGENTS.name like \"%Windows 7%\") union "; 
        
        // $query.= "select \"Windows (Other versions)\" useragent, count(distinct visitorid) as visitors from $profile->tablename left join ".TBL_USER_AGENTS." AGENTS on (useragentid = AGENTS.id) where timestamp >=$from and timestamp <=$to and status=200 and crawl=0 and (AGENTS.name like \"%Windows%\") and (AGENTS.name  NOT like \"%Windows Vista%\" and AGENTS.name  not like \"%Windows NT%\" and AGENTS.name not like \"%Windows 9%\" and AGENTS.name  not like \"%Windows XP%\" and AGENTS.name  not like \"%Windows 2000%\" and AGENTS.name  not like \"%Windows 7%\") union ";    

        // $query.= "select \"Linux\" useragent, count(distinct visitorid) as visitors from $profile->tablename left join ".TBL_USER_AGENTS." AGENTS on (useragentid = AGENTS.id) where timestamp >=$from and timestamp <=$to and status=200 and crawl=0 and (AGENTS.name like \"%Linux%\") union ";
        
        // $query.= "select \"Free BSD\" useragent, count(distinct visitorid) as visitors from $profile->tablename left join ".TBL_USER_AGENTS." AGENTS on (useragentid = AGENTS.id) where timestamp >=$from and timestamp <=$to and status=200 and crawl=0 and (AGENTS.name like \"%FreeBSD%\") union "; 
        
        
        // $query.= "select \"Apple Mac OSX\" useragent, count(distinct visitorid) as visitors from $profile->tablename left join ".TBL_USER_AGENTS." AGENTS on (useragentid = AGENTS.id) where timestamp >=$from and timestamp <=$to and status=200 and crawl=0 and (AGENTS.name  like \"%OSX%\" or AGENTS.name like \"OS X\") union ";
        
        // $query.= "select \"Apple Macintosh\" useragent, count(distinct visitorid) as visitors from $profile->tablename left join ".TBL_USER_AGENTS." AGENTS on (useragentid = AGENTS.id) where timestamp >=$from and timestamp <=$to and status=200 and crawl=0 and (AGENTS.name  like \"%Macintosh%\") union ";
        
        // $query.= "select \"Other/Unknown OS\" useragent, count(distinct visitorid) as visitors from $profile->tablename left join ".TBL_USER_AGENTS." AGENTS on (useragentid = AGENTS.id) where timestamp >=$from and timestamp <=$to and status=200 and crawl=0 and (AGENTS.name  NOT like \"%Windows%\" and AGENTS.name  not like \"%Linux%\" and AGENTS.name not like \"%Apple%\" and AGENTS.name not like \"%FreeBSD%\" and AGENTS.name!=\"-\")";

        // $query.= " order by  visitors desc";
        
		$reportoptions = "daterangeField,trafficsource";
	}
	
    if ($labels==_SCREEN_RESOLUTION) {
        $showfields = _SCREEN_RESOLUTION.','._VISITS;
        $query  = "select screenres,sum(visits) as hits from $profile->tablename_screenres where timestamp >=$from and timestamp <=$to group by screenres order by hits desc";
        
        $help=_SCREEN_RESOLUTION_DESC;
		$reportoptions = "daterangeField,trafficsource";
	}
    
    if ($labels==_COLOR_PALETTE) {
        $showfields = _COLOR_DEPTH.','._VISITS;
        $query  = "select concat(colordepth, ' bit color'),sum(visits) as hits from $profile->tablename_colordepth where timestamp >=$from and timestamp <=$to group by colordepth order by hits desc";
        
        $help=_COLOR_PALETTE_DESC;
		$reportoptions = "daterangeField,trafficsource";
	}
	
	if ($labels==_DAYS_OF_THE_WEEK) {
		$showfields = _DATE.','._VISITORS.','._PAGEVIEWS;
		$query  = "select FROM_UNIXTIME(timestamp,'%W') AS days, count(distinct visitorid),count(*) as hits,(count(*)/(count(distinct visitorid)*1.00)) as ppu from $profile->tablename where timestamp >=$from and timestamp <=$to and crawl=0 group by days order by FROM_UNIXTIME(timestamp,'%w')";
		
		$help=_DAYS_OF_THE_WEEK_DESC;
		$reportoptions = "daterangeField,trafficsource";
	}
	
	if ($labels==_TOP_ENTRY_PAGES) {
		$showfields = _PAGE.','._VISITORS;
		if ($databasedriver == "sqlite") {
			$db->Execute("BEGIN TRANSACTION");
		}
		        
        $pg="create temporary table temptable_mindates (min_visitorid char(32), min_timestamp int(11))";
        $db->Execute($pg);
        
        $pg="insert into temptable_mindates select visitorid min_visitorid, min(timestamp) min_timestamp from $profile->tablename where (timestamp >=$from and timestamp <=$to) and crawl=0 group by visitorid";
		$db->Execute($pg);
        $subquery = "select a.url, count(distinct visitorid) as hits from temptable_mindates, $profile->tablename as a where (visitorid = min_visitorid) and (timestamp = min_timestamp) group by a.url order by hits desc limit $limit";
		$query = "select u.url, count(visitorid) as hits from temptable_mindates, $profile->tablename as a, $profile->tablename_urls as u where (visitorid = min_visitorid) and (timestamp = min_timestamp) and (a.url=u.id) and (crawl=0) group by a.url order by hits desc limit $limit";
        $query = "select CONCAT(u.url,'##',u.title) as urlinfo, hits from ($subquery) as a, $profile->tablename_urls as u where a.url=u.id"; 
        
		$help=_TOP_ENTRY_PAGES_DESC;
		$reportoptions = "daterangeField,trafficsource,limit";
	}
	
	if ($labels==_TOP_EXIT_PAGES) {
		$showfields = _PAGE.','._EXITS;
		if ($databasedriver == "sqlite") {
			$db->Execute("BEGIN TRANSACTION");
		}
		$pg="create temporary table temptable_maxdates (max_visitorid char(32), max_timestamp int(11))";
		$db->Execute($pg);
		$pg="insert into temptable_maxdates select yt1.visitorid max_visitorid, max(yt1.timestamp) max_timestamp from $profile->tablename as yt1 where (yt1.timestamp >=$from and yt1.timestamp <=$to) and (yt1.crawl=0) group by yt1.visitorid";
		$db->Execute($pg);
		$query="select url, count(visitorid) as hits from temptable_maxdates, $profile->tablename where (visitorid = max_visitorid) and (timestamp = max_timestamp) and (crawl=0) group by url order by hits desc limit $limit";
        $query="select CONCAT(u.url,'##',u.title) as urlinfo, count(visitorid) as hits from temptable_maxdates, $profile->tablename as a,$profile->tablename_urls as u where (visitorid = max_visitorid) and (timestamp = max_timestamp) and (a.url=u.id) and (crawl=0) group by a.url order by hits desc limit $limit";
		$help=_TOP_EXIT_PAGES_DESC;
		$reportoptions = "daterangeField,trafficsource,limit";
	}
	
    if ($labels==_TODAYS_TOP_COUNTRIES) {
		if (!isset($gi)) {
			include_once("components/geoip/open_geoip.php");
		}        
        $todaysdate=$to; 
        $from   = mktime(0,0,0,date("m", $todaysdate),date("d", $todaysdate),date("Y", $todaysdate));
        $to     = mktime(23,59,59,date("m", $todaysdate),date("d", $todaysdate),date("Y", $todaysdate));        
        $showfields = _COUNTRY.','._VISITORS;
        $query  = "select country, count(distinct visitorid) as ips from $profile->tablename where timestamp >=$from and timestamp <=$to and crawl=0 and country!='' group by country order by ips desc limit $limit";
        $help=_TODAYS_TOP_COUNTRIES_DESC;
		$reportoptions = "daterangeField,trafficsource,limit";
	}
	
	if ($labels==_TOP_COUNTRIES_CITIES || $labels==_TOP_COUNTRIES) {
		$showfields = _COUNTRY.','._VISITORS.','._PAGEVIEWS.','._PAGES_PER_USER;
		$query  = "select country, count(distinct visitorid) as ips,count(*) as hits, (count(*)/(count(distinct visitorid)*1.00)) as ppu from $profile->tablename where timestamp >=$from and timestamp <=$to and crawl=0 and country!='' group by country order by ips desc limit $limit";
		$help=_TOP_COUNTRIES_CITIES_DESC;
		
		$reportoptions = "daterangeField,trafficsource,limit";
	}
	
	if ($labels==_TOP_CITIES) {
		$showfields = _CITY.','._VISITORS;
		$addlabel=" - ".@$cnames[$_REQUEST["country"]]. " (".$_REQUEST["country"].")";
		$preq  = "select v.ipnumber from $profile->tablename as a, $profile->tablename_visitorids as v where timestamp >=$from and timestamp <=$to and crawl=0 and country='".$_REQUEST["country"]."' and a.visitorid=v.id group by a.visitorid";
		//echo $preq;
		
		$loop = $db->Execute($preq);
		$newdata = array();
		while ($loopdata=$loop->FetchRow()) {
			if (is_numeric(substr($loopdata["ipnumber"],-1))) {
                $area=geoip_record_by_addr($gi, $loopdata["ipnumber"]);
			    //$city=$area->city;
                $city = iconv("ISO-8859-1","UTF-8", $area->city);
			    if ($city=="") {
				    $city="unknown";
			    }
			    //echo "lala $city";
			    $newdata[$city] = @$newdata[$city] + 1;
            }
		}
		arsort($newdata);
		
		// now merge it in the stats table format
		$i=0;
		while (list ($key, $val) = each ($newdata)) {
			//echo "$key $val<br>";
			$data[$i][0] = $key;
			$data[$i][1] = $val;
			$i++;
		}
		$help=_TOP_CITIES_DESC;
		$query="data array";
	}
	
	if ($labels=="Top Cities Map") {
		$showfields = _CITY.','._VISITORS.',longitude,latitude';
		$addlabel=" - ".@$cnames[$_REQUEST["country"]]. " (".$_REQUEST["country"].")";
		$preq  = "select v.ipnumber from $profile->tablename as a, $profile->tablename_visitorids as v where timestamp >=$from and timestamp <=$to and crawl=0 and country='".$_REQUEST["country"]."' and a.visitorid=v.id group by a.visitorid";
		//echo $preq;
		
		$loop = $db->Execute($preq);
		$newdata = array();
		while ($loopdata=$loop->FetchRow()) {
			//echo $loopdata["ipnumber"]."<br>";
            if (is_numeric(substr($loopdata["ipnumber"],-1))) {
                $area=geoip_record_by_addr($gi, $loopdata["ipnumber"]);
			    $city = iconv("ISO-8859-1","UTF-8", $area->city);
			    $longitude=$area->longitude;
			    $latitude=$area->latitude;
			    if ($city=="") {
				    $city="unknown";
			    }
			    //echo "lala $city";
			    $newdata[$city] = @$newdata[$city] + 1;
			    $posdata[$city]['longitude'] = @$longitude;
			    $posdata[$city]['latitude'] = @$latitude;
            }
		}
		arsort($newdata);
		
		// now merge it in the stats table format
		$i=0;
		if(empty($_REQUEST['limit'])) { $limit = 10; }
		while (list ($key, $val) = each ($newdata)) {
			if($i > $limit) { break; }
			//echo "$key $val<br>";
			$data[$i][0] = $key;
			$data[$i][1] = $val;
			$data[$i][2] = $posdata[$key]['longitude'];
			$data[$i][3] = $posdata[$key]['latitude'];
			$i++;
		}
		
		$help=_TOP_CITIES_DESC;
		$query="data array";
	}
	
	if ($labels=="Top Continents") {
        
		$continents = array(
            "North America" => "AI,AG,AW,BS,BB,BZ,BM,VG,CA,KY,CR,CU,DM,DO,SV,GL,GD,GP,GT,HT,HN,JM,MQ,MX,MS,AN,NI,PA,PR,BL,KN,LC,MF,PM,VC,TT,TC,US,VI",
            "South America" => "AR,BO,BR,CL,CO,EC,FK,GF,GY,PY,PE,SR,UY,VE",
            "Europe" => "AX,AL,AD,AT,BY,BE,BA,BG,HR,CZ,DK,EE,FO,FI,FR,DE,GI,GR,GG,VA,HU,IS,IE,IM,IT,JE,LV,LI,LT,LU,MK,MT,MD,MC,ME,NL,NO,PL,PT,RO,RU,SM,RS,SK,SI,ES,SJ,SE,CH,UA,GB",
            "Africa" => "DZ,AO,BJ,BF,BI,CM,CV,CF,TD,KM,CD,CG,CI,DJ,EG,GQ,ER,ET,GA,GM,GH,GN,GW,KE,LS,LR,LY,MR,MU,YT,MG,MW,ML,MA,MZ,NA,NE,NG,RE,SH,RW,ST,SN,SC,SL,SO,ZA,SD,SZ,TZ,TG,TN,UG,EH,ZM,ZW",
            "Asia" => "AF,AM,AZ,BH,BD,BT,IO,BN,KH,CN,CX,CC,CY,GE,HK,IN,ID,IR,IQ,IL,JP,JO,KZ,KP,KR,KW,KG,LA,LB,MO,MY,MV,MN,MM,NP,OM,PK,PS,PH,QA,SA,SG,LK,SY,TW,TJ,TH,TL,TR,TM,AE,UZ,VN,YE",
            "Oceania" => "AS,AU,CK,FJ,PF,GU,KI,MH,FM,NR,NC,NZ,NU,NF,MP,PW,PG,PN,WS,SB,TK,TO,TV,UM,VU,WF",
            "Antarctica" => "AQ,BV,TF,HM,GS"
        );
        
        $showfields = "Continent".','._VISITORS.','._PAGEVIEWS.','._PAGES_PER_USER;
		$prequery  = "select country, count(distinct visitorid) as ips,count(*) as hits, (count(*)/(count(distinct visitorid)*1.00)) as ppu from $profile->tablename where timestamp >=$from and timestamp <=$to and crawl=0 and country!='' group by country order by ips desc limit $limit";
		if (@$_SESSION["trafficsource"]) { $prequery = subsetDataToSourceID($prequery); }
        
        $q = $db->Execute($prequery);
		while($result = $q->FetchRow()) {
			foreach($continents as $continent=>$lands) {
				if(strpos($lands,$result['country']) !== false) {
					$c[$continent]['name'] = $continent;
					$c[$continent]['ips'] = (@$c[$continent]['ips'] + $result['ips']);
					$c[$continent]['hits'] = (@$c[$continent]['hits'] + $result['hits']);
					$c[$continent]['ppu'] = (@$c[$continent]['ppu'] + $result['ppu']);					
				}
			}
		}
        
        # check for continents that have no stats, and add them with (or the flash map will get messed up)
        foreach($continents as $continent=>$lands) {
            if (!isset($c[$continent]['name'])) {
                $c[$continent]['name'] = $continent;
                $c[$continent]['ips'] = 0;
                $c[$continent]['hits'] = 0;
                $c[$continent]['ppu'] = 0;                
            }    
        }
        
		$i = 0;
		$data = array();
		foreach($c as $continent_name=>$value_array) {
			$ii = 0;
			foreach($value_array as $key=>$value) {
				$data[$i][$ii] = $value;
				$ii++;
			}
			$i++;
		}
		$query="data array";
		$help=_TOP_COUNTRIES_CITIES_DESC;
	}
	
	if ($labels==_OVERALL_PERFORMANCE) {
		$query = "select count(distinct visitorid) from $profile->tablename where timestamp >=$from and timestamp <=$to and crawl=0 and status=200";
		if (@$_SESSION["trafficsource"]) { $query = subsetDataToSourceID($query); }
		$prequery= $db->Execute($query);
		$ptot=$prequery->FetchRow();
		$addlabel=" - ($ptot[0] Visitors)";
		//$nograph=1;
		$showfields = _REQUESTS.','._VISITORS.','._PAGE.','._CONVERSION;
		//$query  = "select count(*) as hits,count(distinct visitorid),url,(count(distinct visitorid)/$ptot[0])*100 as ctr from $profile->tablename where timestamp >=$from and timestamp <=$to and $target and crawl=0 and status=200 group by url order by hits desc";
		$query  = "select count(*) as hits,count(distinct visitorid),u.url,((count(distinct visitorid)*1.00)/$ptot[0])*100 as ctr from $profile->tablename_conversions as a,$profile->tablename_urls as u where timestamp >=$from and timestamp <=$to and a.url=u.id group by a.url order by hits desc";
		
		$help=_OVERALL_PERFORMANCE_DESC;
		$reportoptions = "daterangeField,trafficsource";
		$searchmode = "like";
	}
	
    if ($labels=="wildcard performance") {
        $query = "select count(distinct visitorid) from $profile->tablename where timestamp >=$from and timestamp <=$to and crawl=0 and status=200";
        if (@$_SESSION["trafficsource"]) { $query = subsetDataToSourceID($query); }
        $prequery= $db->Execute($query);
        $ptot=$prequery->FetchRow();
        $addlabel=" - ($ptot[0] Visitors)";
        //$nograph=1;
        $showfields = _REQUESTS.','._VISITORS.','._PAGE.','._CONVERSION;
        //$query  = "select count(*) as hits,count(distinct visitorid),url,(count(distinct visitorid)/$ptot[0])*100 as ctr from $profile->tablename where timestamp >=$from and timestamp <=$to and $target and crawl=0 and status=200 group by url order by hits desc";
        $query  = "select count(*) as hits,count(distinct visitorid),u.url,((count(distinct visitorid)*1.00)/$ptot[0])*100 as ctr from $profile->tablename as a,$profile->tablename_urls as u where timestamp >=$from and timestamp <=$to and a.url=u.id and u.url like '".str_replace("*","%",$search)."' group by a.url order by hits desc";
            
        $help=_OVERALL_PERFORMANCE_DESC;
		$reportoptions = "daterangeField";
	}
	
    if ($labels==_PERFORMANCE_TODAY || $labels==_PERFORMANCE_THIS_MONTH) {
        if ($labels==_PERFORMANCE_TODAY) {
            $todaysdate=$to; 
            $from   = mktime(0,0,0,date("m", $todaysdate),date("d", $todaysdate),date("Y", $todaysdate));
            $to     = mktime(23,59,59,date("m", $todaysdate),date("d", $todaysdate),date("Y", $todaysdate));
        }
        $query = "select count(distinct visitorid) from $profile->tablename where timestamp >=$from and timestamp <=$to and crawl=0 and status=200";
        $prequery= $db->Execute($query);
        $ptot=$prequery->FetchRow();
        $addlabel=" - ($ptot[0] Visitors)";
        
        //$labels="$labels</font> - ($uvtoday Visitors)";
        $showfields = _VISITORS.','._PAGE.','._CONVERSION;
        $query  = "select count(distinct visitorid) as hits,u.url,((count(distinct visitorid)*1.00)/$ptot[0])*100 as ctr from $profile->tablename_conversions as a,$profile->tablename_urls as u where timestamp >=$from and timestamp <=$to and a.url=u.id group by a.url order by hits desc";
        $nototal=1;
        $nograph=1; 
        
		$reportoptions = "daterangeField";
	}
	
    if ($labels==_ALL_TRAFFIC_BY_HOUR) {
        $showfields = _DATE.','._UNIQUE_IPS.','._TOTAL_PAGES.','._VIEWED_PAGES.','._MEGABYTES.','._CRAWLED_PERC.','._PAGES_PER_IP;
        $query  = "select FROM_UNIXTIME(timestamp,'%H') AS days, count(distinct visitorid) as visitors,count(*) as requests,(count(*)-sum(crawl)) as viewed,(sum(bytes*1.00)/1024.0)/1024.0 as mb,(sum(crawl)/(count(*)*1.00)*100) as crawled, (count(*)/(count(distinct visitorid)*1.00)) as ppu from $profile->tablename where timestamp >=$from and timestamp <=$to group by days order by timestamp";
		$reportoptions = "daterangeField,trafficsource";
	}
	
	if ($labels==_ALL_TRAFFIC_BY_DAY) {
		$showfields = _DATE.','._UNIQUE_IPS.','._TOTAL_PAGES.','._VIEWED_PAGES.','._MEGABYTES.','._CRAWLED_PERC.','._PAGES_PER_IP;
		$query  = "select FROM_UNIXTIME(timestamp,'%d-%b-%Y %a') AS days, count(distinct visitorid) as visitors,count(*) as requests,(count(*)-sum(crawl)) as viewed,(sum(bytes*1.00)/1024.0)/1024.0 as mb,(sum(crawl)/(count(*)*1.00)*100) as crawled, (count(*)/(count(distinct visitorid)*1.00)) as ppu from $profile->tablename where timestamp >=$from and timestamp <=$to group by days order by timestamp";
		$help=_ALL_TRAFFIC_BY_DAY_DESC;
		$reportoptions = "daterangeField,trafficsource";
	}
	
	if ($labels==_ALL_TRAFFIC_BY_MONTH) {
		$showfields = _DATE.','._UNIQUE_IPS.','._TOTAL_PAGES.','._VIEWED_PAGES.','._MEGABYTES.','._CRAWLED_PERC.','._PAGES_PER_IP;
		$query  = "select FROM_UNIXTIME(timestamp,'%M') AS month, count(distinct visitorid) as visitors,count(*) as requests,(count(*)-sum(crawl)) as viewed,(sum(bytes*1.00)/1024.0)/1024.0 as mb,(sum(crawl)/(count(*)*1.00)*100) as crawled, (count(*)/(count(distinct visitorid)*1.00)) as ppu from $profile->tablename where timestamp >=$from and timestamp <=$to group by month order by timestamp";
		$help=_ALL_TRAFFIC_BY_MONTH_DESC;
		$reportoptions = "daterangeField,trafficsource";
	}
	
	if ($labels==_ERROR_REPORT) {
		if ($search) {
			//$searchst="and l.url $searchmode '%$search%'";
			$searchst = MakeSearchString($search,"l.url",$searchmode);
		}
		$showfields = _STATUS.','._DESCRIPTION.','._VIEWED_PAGES.','._CRAWLED_PAGES.','._TOTAL_HITS;
		$query  = "select l.status,s.descr,(count(*)-sum(crawl)) as viewed, sum(crawl),count(*) from $profile->tablename as l, ".TBL_LGSTATUS." as s where l.timestamp >=$from and l.timestamp <=$to and l.status=s.code and l.status!=200 $searchst group by l.status order by viewed desc";
		$help=_ERROR_REPORT_DESC;
		$reportoptions = "daterangeField,trafficsource,search";
		$searchmode = "like";
	}

	if ($labels=="$status "._ERROR_REPORT) {
		if ($search) {
			//$searchst="and u.url $searchmode '%$search%'";
			$searchst = MakeSearchString($search,"u.url",$searchmode); 
		}
		$showfields = _PAGE.','._VIEWED_PAGES.','._CRAWLED_PAGES.','._TOTAL_HITS;
		$query  = "select u.url,(count(*)-sum(crawl)) as viewed, sum(crawl),count(*) from $profile->tablename as a,$profile->tablename_urls as u where status='$status' and timestamp >=$from and timestamp <=$to and a.url=u.id $searchst group by a.url order by viewed desc limit $limit";
		$reportoptions = "daterangeField";
	}
	
	if ($labels==_DETAILED_CRAWLER_REPORT) {
		//$addlabel=" - ".$agent;
		$showfields = _CRAWLER.','._PAGE.','._PARAMETERS.','._REQUESTS;
		$query  = "select ua.useragent,u.url,up.params,count(*) as hits from $profile->tablename as a,$profile->tablename_urls as u,$profile->tablename_urlparams as up,".$profile->tablename_useragents." as ua where md5(ua.useragent)='$agent' and timestamp >=$from and timestamp <=$to and a.url=u.id and a.params=up.id and a.useragentid = ua.id group by concat(u.url,up.params) order by hits desc limit $limit";
		$reportoptions = "daterangeField";
	}
	
	if ($labels==_DETAILED_REFERRER_REPORT) {
		$addlabel=" - ".$status;
		$showfields = _DATE.','._REFERRER.','._PAGE.','._BROWSER;
		$query  = "select FROM_UNIXTIME(timestamp,'%d-%c-%Y %a %H:%i:%s') as ptime, r.referrer,concat(u.url,up.params),ua.useragent from $profile->tablename as a,$profile->tablename_referrers as r,$profile->tablename_urls as u,$profile->tablename_urlparams as up left outer join ".$profile->tablename_useragents." ua on (useragentid = ua.id) where timestamp >=$from and timestamp <=$to and visitorid='$status' and a.url=u.id and a.params=up.id and a.referrer=r.id order by timestamp limit $limit";
		$reportoptions = "daterangeField";
	}
	
	if ($labels==_REFERRER_DETAIL_REPORT) {
		$showfields = _HITS.','._URL.','._LANDING_PAGE;
		$query  = "select count(*) as hits,referrer,url from $profile->tablename where timestamp >=$from and timestamp <=$to and referrer NOT like 'http://$profile->confdomain/%' and crawl=0 group by referrer order by hits desc limit $limit";
		$reportoptions = "daterangeField";
	}
	
	if ($labels==_TOP_CLICK_PATHS) {
		$showfields = _PATH.','._HITS;
		$help=_TOP_CLICK_PATHS_DESC;
        
		@$db->Execute("SET SESSION group_concat_max_len = 65535");
		
		$ServerInfo = $db->ServerInfo(); 
        if ($ServerInfo["version"] < 4.1) {
            echo "<script>  loading.style.visibility=\"hidden\";</script> "._ONLY_WORKS_WITH_MYSQL_4_PLUS.":" .$ServerInfo["description"].$ServerInfo["version"];
            exit();
        } else {
             //@$db->Execute("CREATE TEMPORARY TABLE temp_toppaths (visitorid varchar(32), trail varchar(1050))") or die ("Error:".mysql_error()."<script>  loading.style.visibility=\"hidden\";</script>");
            //@$db->Execute("INSERT INTO temp_toppaths SELECT visitorid, SubStr(Group_Concat(distinct u.url order by timestamp SEPARATOR ' <font color=red>--></font> '),1,1024) trail FROM $profile->tablename as a,$profile->tablename_urls as u where timestamp >=$from and timestamp <=$to and a.url=u.id group by visitorid") or die ("Error:".mysql_error()."<script>  loading.style.visibility=\"hidden\";</script>");    
            
            //$query="select trail, count(*) from temp_toppaths group by trail order by 2 desc limit $limit";
            // try this instead
            ///$query="select trail, count(*) as hits from (SELECT visitorid, Group_Concat(u.url order by id SEPARATOR ' -> ') trail FROM $profile->tablename as a,$profile->tablename_urls as u where timestamp >=$from and timestamp <=$to and crawl=0 and a.url=u.id group by visitorid) as paths group by trail order by hits desc limit $limit";
            //yes, order by id, even though it doesn't exist - or should we just ditch anything other than status 200
            // yes, ditch anything other than 200 for now, ordering by id actually orders it by the id of the urls table, so that is WRONG!
            //$query="select trail, count(*) as hits from (SELECT visitorid, Group_Concat(u.url SEPARATOR ' <img src=images/icons/arrow_right.gif> ') trail FROM $profile->tablename as a,$profile->tablename_urls as u where timestamp >=$from and timestamp <=$to and crawl=0 and status=200 and a.url=u.id group by visitorid) as paths group by trail order by hits desc limit $limit";   
            $query="select trail, count(*) as hits from ";
            //$query.=subsetDataToSourceID("(SELECT visitorid, Group_Concat(DISTINCT u.url SEPARATOR ' <img src=images/icons/arrow_right.gif> ') trail FROM $profile->tablename as a,$profile->tablename_urls as u where timestamp >=$from and timestamp <=$to and crawl=0 and status=200 and a.url=u.id group by visitorid) ");
            $query.=subsetDataToSourceID("(SELECT visitorid, Group_Concat(DISTINCT u.url order by timestamp SEPARATOR ' <img src=images/icons/arrow_right.gif> ') trail FROM $profile->tablename as a,$profile->tablename_urls as u where timestamp >=$from and timestamp <=$to and crawl=0 and status=200 and a.url=u.id group by visitorid) ");
            $query.="as paths group by trail order by hits desc limit $limit";   
            $applytrafficsource=false;
            //probably best to leave it in the temp table though cus them we can do searches on files that are in the trail, i,.e show all trails that contain url x
            
        }
    
		$reportoptions = "daterangeField,trafficsource,limit";
	}
	
	if ($labels==_SEARCH_ENGINES) {
    
        $showfields = _SEARCH_ENGINE.','._VISITORS.','._HITS.','._SEARCHES_PER_USER;
        
        //$help="Definitions for this report:<ul><li>Search Engine: The search engine,Visitors,Hits,Searches Per UserPath: The sequence of pages requested in this order<li>Hits: the number of times this path requested </ul>";
        
        if ($from < mktime(12,0,0,6,3,2009)) { // This is when Bing was launched. If report is in an earlier date, we still display these old buggers 
            $query= subsetDataToSourceID(" select r.referrer, count(distinct visitorid) as visitors, count(*) as hits, (count(*)/(count(distinct visitorid)*1.00)) as ppu from $profile->tablename as a, $profile->tablename_referrers as r,$profile->tablename_keywords as k where timestamp >=$from and timestamp <=$to and a.keywords=k.id and k.keywords!='' and r.referrer not like \"http://www.google.%\" and r.referrer not like \"%search.yahoo.%\" and r.referrer not like \"%.search.msn.%\" and r.referrer not like \"%.live.com%\" and r.referrer not like \"%.search.aol.%\" and r.referrer not like \"%.ask.com%\" and r.referrer not like \"http://www.dogpile.com%\" and crawl=0 and a.referrer=r.id group by a.referrer union ");
            $query.= subsetDataToSourceID("select \"MSN Search\" referrer, count(distinct visitorid) as visitors, count(*) as hits, (count(*)/(count(distinct visitorid)*1.00)) as ppu from $profile->tablename as a, $profile->tablename_referrers as r where timestamp >=$from and timestamp <=$to and (r.referrer like \"%search.msn.%\" or r.referrer like  \"%.live.com%\") and crawl=0 and a.referrer=r.id union ");
        } else {
            $query= subsetDataToSourceID(" select r.referrer, count(distinct visitorid) as visitors, count(*) as hits, (count(*)/(count(distinct visitorid)*1.00)) as ppu from $profile->tablename as a, $profile->tablename_referrers as r,$profile->tablename_keywords as k where timestamp >=$from and timestamp <=$to and a.keywords=k.id and k.keywords!='' and r.referrer not like \"http://www.google.%\" and r.referrer not like \"%search.yahoo.%\" and r.referrer!=\"http://www.bing.com/search\" and r.referrer not like \"%.search.aol.%\" and r.referrer not like \"%.ask.com%\" and r.referrer not like \"http://www.dogpile.com%\" and crawl=0 and a.referrer=r.id group by a.referrer union ");   
        }
        //echo "<P>$query<P>";
        
        $query.= subsetDataToSourceID("select \"Google (Natural Search)\" referrer, count(distinct visitorid) as visitors, count(*) as hits, (count(*)/(count(distinct visitorid)*1.00)) as ppu from $profile->tablename as a, $profile->tablename_referrers as r,$profile->tablename_urlparams as up where timestamp >=$from and timestamp <=$to and (r.referrer like \"http://www.google.%\" and up.params NOT like \"?gclid=%\") and crawl=0 and a.referrer=r.id and a.params=up.id union ");

        $query.= subsetDataToSourceID("select \"Google (Paid Search)\" referrer, count(distinct visitorid) as visitors, count(*) as hits, (count(*)/(count(distinct visitorid)*1.00)) as ppu from $profile->tablename as a, $profile->tablename_referrers as r,$profile->tablename_urlparams as up where timestamp >=$from and timestamp <=$to and r.referrer like \"http://www.google.%\" and up.params like \"?gclid=%\" and crawl=0 and a.referrer=r.id and a.params=up.id union ");

        $query.= subsetDataToSourceID("select \"Yahoo\" referrer, count(distinct visitorid) as visitors, count(*) as hits, (count(*)/(count(distinct visitorid)*1.00)) as ppu from  $profile->tablename as a, $profile->tablename_referrers as r where timestamp >=$from and timestamp <=$to and r.referrer like \"%search.yahoo.%\" and crawl=0 and a.referrer=r.id union ");

        $query.= subsetDataToSourceID("select \"Bing\" referrer, count(distinct visitorid) as visitors, count(*) as hits, (count(*)/(count(distinct visitorid)*1.00)) as ppu from $profile->tablename as a, $profile->tablename_referrers as r where timestamp >=$from and timestamp <=$to and r.referrer = \"http://www.bing.com/search\" and crawl=0 and a.referrer=r.id union ");
        
        $query.= subsetDataToSourceID("select \"AOL Search\" referrer, count(distinct visitorid) as visitors, count(*) as hits, (count(*)/(count(distinct visitorid)*1.00)) as ppu from $profile->tablename as a, $profile->tablename_referrers as r where timestamp >=$from and timestamp <=$to and r.referrer like \"%search.aol.%\" and crawl=0 and a.referrer=r.id union ");

        //$query.= subsetDataToSourceID("select \"My Way Search\" referrer, count(distinct visitorid) as visitors, count(*) as hits, (count(*)/(count(distinct visitorid)*1.00)) as ppu from $profile->tablename as a, $profile->tablename_referrers as r where timestamp >=$from and timestamp <=$to and r.referrer like \"http://search.myway.com%\" and crawl=0 and a.referrer=r.id union ");

        $query.= subsetDataToSourceID("select \"Ask.com\" referrer, count(distinct visitorid) as visitors, count(*) as hits, (count(*)/(count(distinct visitorid)*1.00)) as ppu from $profile->tablename as a, $profile->tablename_referrers as r where timestamp >=$from and timestamp <=$to and r.referrer like \"%.ask.com%\" and crawl=0 and a.referrer=r.id union ");

        $query.= subsetDataToSourceID("select \"Dogpile.com\" referrer, count(distinct visitorid) as visitors, count(*) as hits, (count(*)/(count(distinct visitorid)*1.00)) as ppu from $profile->tablename as a, $profile->tablename_referrers as r where timestamp >=$from and timestamp <=$to and r.referrer like \"http://www.dogpile.com%\" and crawl=0 and a.referrer=r.id ");

        $query.= " order by  visitors desc"; 
        
        //echo $query;
        global $applytrafficsource;
        $applytrafficsource = false;
		
		$reportoptions = "daterangeField,trafficsource,limit";
    }
  
    if ($labels==_TRAFFIC_BREAKDOWN) {  
        $showfields = _TRAFFIC_SOURCES.','._VISIT_SHARE.','._VISITORS;
        $help=_TRAFFIC_BREAKDOWN_DESC;
        
        $subq=$profile->tablename."_breakdown";
        $q = "create temporary table $subq select visitorid, SUBSTRING_INDEX(GROUP_CONCAT(referrer),',',1) as referrer, SUBSTRING_INDEX(GROUP_CONCAT(keywords),',',1) as keywords from $profile->tablename where timestamp >=$from and timestamp <=$to and crawl=0 group by visitorid";
        $db->Execute($q);
        
        # get the search traffic
        $q = "select count(*) from $subq where keywords!=(select id from $profile->tablename_keywords where keywords='')";
        $q = $db->Execute($q);
        $data = $q->FetchRow();
        $search_traffic = $data[0];
        
        # get the direct traffic
        $q = "select count(*) from $subq where referrer=(select id from $profile->tablename_referrers where referrer='-')";
        $q = $db->Execute($q);
        $data = $q->FetchRow();
        $direct_traffic = $data[0];
        
        # get the referrer traffic (which is the rest) 
        $q = "select (count(*)-($search_traffic+$direct_traffic)) from $subq";
        $q = $db->Execute($q);
        $data = $q->FetchRow();
        $referrer_traffic = $data[0];
        
        $db->Execute("drop table $subq");
        
        $total=$referrer_traffic+$direct_traffic+$search_traffic;
        
        $data = array();
        $data[0][0]="Referring Sites";
        $data[0][1]=(($referrer_traffic/$total)*100);
        $data[0][2]=$referrer_traffic;
        $data[1][0]="Direct Traffic";
        $data[1][1]=(($direct_traffic/$total)*100); 
        $data[1][2]=$direct_traffic;
        $data[2][0]="Search Engines";
        $data[2][1]=(($search_traffic/$total)*100); 
        $data[2][2]=$search_traffic;
        
        $query="data array";
		$reportoptions = "daterangeField";
  }
  
  // the convcersion reports below have 'full' variations (i.e. including non-converting elements) in the following include .... but we have decided not to use these.
  // include "includes/full_conversion_reports.php";
    
  if ($labels==_PAGE_CONVERSION) {
        $showfields = ""._PAGE.","._VISITORS.","._CONVERTED_VISITORS.","._CONVERSION_RATE."";
        $help=_PAGE_CONVERSION_DESC;         
        
        if (!$roadto) {
            $showfields = _INFO;
            $query = "select \"Please choose a target file\"";
			$reportoptions = "daterangeField,trafficsource,roadto,search,limit";
            return;
        }
        $addlabel= " for $roadto";
        
        # first, get the ID of the target page
        $kpi = getID($roadto,"urls");        
        
        $top_entry_converted_search="";
        if ($search) {        
            $searchst = SearchMatchingIDs($search,"url",$searchmode,$profile->tablename_urls);
            $top_entry_converted_search = str_replace("and url IN", "where entry IN", $searchst);            
        }
                
        # get the entry page of each converted user for a certain KPI, then count the ocurrences
        $entrypages_for_conversion = subsetDataToSourceID("SELECT $nc a.visitorid as cvisitor, SUBSTRING_INDEX(Group_Concat(a.url order by a.timestamp),',',1) as entry FROM $profile->tablename as a, $profile->tablename_conversions as c where (c.timestamp >=$from and c.timestamp <=$to) and (a.timestamp >=$from and a.timestamp <=$to) and c.url='$kpi' and c.visitorid=a.visitorid and crawl=0 and (status=200 or status=302) group by a.visitorid");
        $converted = "create temporary table top_entry_converted SELECT $nc entry, count(distinct cvisitor) as conversions from ($entrypages_for_conversion) as cvisitors $top_entry_converted_search group by entry";
        $db->Execute($converted);        
        
        # next, get the entry page for each visitor
        $entrypages = "create temporary table entrypages ". subsetDataToSourceID("SELECT $nc visitorid, SUBSTRING_INDEX(Group_Concat(url order by timestamp),',',1) as url FROM $profile->tablename where (timestamp >=$from and timestamp <=$to) and crawl=0 and (status=200 or status=302) group by visitorid");
        $db->Execute($entrypages);
         
        # now count the top entry pages
        $toppages = "create temporary table top_entry SELECT $nc url, count(distinct visitorid) as visitors from entrypages, top_entry_converted where entry=url $searchst group by url order by visitors desc limit $limit";       
        $db->Execute($toppages);         
                      
        # now join it all together
        $query = "select $nc u.url, visitors, conversions, CONCAT(FORMAT((conversions/visitors)*100,2),' %') as crate from top_entry as a LEFT JOIN top_entry_converted as b on (b.entry=a.url) LEFT JOIN $profile->tablename_urls as u on (u.id=a.url) order by visitors desc";
        $applytrafficsource = false;
		$reportoptions = "daterangeField,trafficsource,roadto,search,limit";
  } 
  
  if ($labels==_REFERRER_CONVERSION) {
        $showfields = ""._REFERRER.","._VISITORS.","._CONVERTED_VISITORS.","._CONVERSION_RATE."";
        $help=_REFERRER_CONVERSION_DESC;
        
        if (!$roadto) {
            $showfields = _INFO;
            $query = "select \"Please choose a target file\"";
			$reportoptions = "daterangeField,trafficsource,roadto,search,limit";
            return;
        }
        $addlabel= " for $roadto";
        
        # first, get the ID of the target page
        $kpi = getID($roadto,"urls");         
       
        $top_entry_converted_search="";
        if ($search) {        
            $searchst = SearchMatchingIDs($search,"referrer",$searchmode,$profile->tablename_referrers);
            $top_entry_converted_search = str_replace("and referrer IN", "where entry IN", $searchst);           
        }              
               
        # get the entry referrer of each converted user for a certain KPI, then count the ocurrences
        $entrypages_for_conversion = subsetDataToSourceID("SELECT a.visitorid as cvisitor, SUBSTRING_INDEX(Group_Concat(a.referrer order by a.timestamp),',',1) as entry FROM $profile->tablename as a, $profile->tablename_conversions as c where (c.timestamp >=$from and c.timestamp <=$to) and (a.timestamp >=$from and a.timestamp <= c.timestamp) and c.url='$kpi' and c.visitorid=a.visitorid and crawl=0 and (status=200 or status=302) group by a.visitorid");
        $converted = "create temporary table top_entry_converted SELECT $nc entry, count(distinct cvisitor) as conversions from ($entrypages_for_conversion) as cvisitors $top_entry_converted_search group by entry order by conversions desc limit $limit";
        $db->Execute($converted);        
        
        # next, get the entry referrer for each visitor
        $entrypages = "create temporary table entrypages ". subsetDataToSourceID("SELECT $nc visitorid, SUBSTRING_INDEX(Group_Concat(referrer order by timestamp),',',1) as referrer FROM $profile->tablename where (timestamp >=$from and timestamp <=$to) and crawl=0 and (status=200 or status=302) group by visitorid");
        $db->Execute($entrypages);
         
        # now count the top entry referrers
        $toppages = "create temporary table top_entry SELECT $nc referrer, count(distinct visitorid) as visitors from entrypages, top_entry_converted where entry=referrer $searchst group by referrer order by visitors desc limit $limit";       
        $db->Execute($toppages);
                        
        # now join it all together
        $query = "select $nc r.referrer, visitors, conversions, CONCAT(FORMAT((conversions/visitors)*100,2),' %') as crate from top_entry as a LEFT JOIN top_entry_converted as b on (b.entry=a.referrer) LEFT JOIN $profile->tablename_referrers as r on (r.id=a.referrer) order by visitors desc";
        $applytrafficsource = false;
		$reportoptions = "daterangeField,trafficsource,roadto,search,limit";
  }
  
  if ($labels==_KEYWORD_CONVERSION) {
        $showfields = ""._KEYWORDS.","._VISITORS.","._CONVERTED_VISITORS.","._CONVERSION_RATE."";
        $help=_KEYWORD_CONVERSION_DESC;
        
        if (!$roadto) {
            $showfields = _INFO;
            $query = "select \"Please choose a target file\"";
			$reportoptions = "daterangeField,trafficsource,roadto,search,limit";
            return;
        }
        $addlabel= " for $roadto";
        
        # first, get the ID of the target page
        $kpi = getID($roadto,"urls");         
       
        $top_entry_converted_search="";
        if ($search) {        
            $searchst = SearchMatchingIDs($search,"keywords",$searchmode,$profile->tablename_referrers);
            $top_entry_converted_search = str_replace("and keywords IN", "where entry IN", $searchst);          
        }
                
        # get the entry keyword of each converted user for a certain KPI, then count the ocurrences
        $entrypages_for_conversion = subsetDataToSourceID("SELECT a.visitorid as cvisitor, SUBSTRING_INDEX(Group_Concat(a.keywords order by a.timestamp),',',1) as entry FROM $profile->tablename as a, $profile->tablename_conversions as c where (c.timestamp >=$from and c.timestamp <=$to) and (a.timestamp >=$from and a.timestamp <= c.timestamp) and c.url='$kpi' and c.visitorid=a.visitorid and crawl=0 and (status=200 or status=302) group by a.visitorid");
        $converted = "create temporary table top_entry_converted SELECT $nc entry, count(distinct cvisitor) as conversions from ($entrypages_for_conversion) as cvisitors $top_entry_converted_search group by entry";
        $db->Execute($converted);        
        
        # next, get the entry keyword for each visitor
        $entrypages = "create temporary table entrypages ". subsetDataToSourceID("SELECT $nc visitorid, SUBSTRING_INDEX(Group_Concat(keywords order by timestamp),',',1) as keywords FROM $profile->tablename where (timestamp >=$from and timestamp <=$to) and crawl=0 and (status=200 or status=302) group by visitorid");
        $db->Execute($entrypages);
         
        # now count the top entry keywords
        $nokw = getID("","keyword");
        $toppages = "create temporary table top_entry SELECT $nc keywords, count(distinct visitorid) as visitors from entrypages, top_entry_converted where entry=keywords $searchst and keywords!='$nokw' group by keywords order by visitors desc limit $limit";       
        $db->Execute($toppages);
                
        # now join it all together
        $query = "select $nc k.keywords, visitors, conversions, CONCAT(FORMAT((conversions/visitors)*100,2),' %') as crate from top_entry as a LEFT JOIN top_entry_converted as b on (b.entry=a.keywords) LEFT JOIN $profile->tablename_keywords as k on (k.id=a.keywords) order by visitors desc";
		
        $applytrafficsource = false;
		$reportoptions = "daterangeField,trafficsource,roadto,search,limit";
  }
  
  if ($labels==_TIME_TO_CONVERSION) {
        $showfields = _TIME_SPENT.','._VISIT_SHARE.','._VISITORS.','._AVERAGE_DURATION_IN_MINUTES;
        
        $help=_TIME_TO_CONVERSION_DESC;
        
        if (!$roadto) {
            $showfields = _INFO;
            $query = "select \"Please choose a target file\"";
			$reportoptions = "daterangeField,trafficsource,roadto,search,limit";
            return;
        }
        $addlabel= " for $roadto";
        
        $kpi = getID($roadto,"urls");
        //we need to do a real table because of the union query
        $table = $profile->tablename."_clength"; 
        $prequery = "drop table $table";
        @$db->Execute($prequery);        
        $prequery = "create table $table (length int(11), visitorid char(32)) ENGINE=MyISAM CHARSET=utf8";
        $db->Execute($prequery);
        
        #get the entry time for converted users
        $entrytime = subsetDataToSourceID("SELECT (c.timestamp-min(a.timestamp)) as length, a.visitorid  FROM $profile->tablename as a, $profile->tablename_conversions as c where (c.timestamp >=$from and c.timestamp <=$to) and (a.timestamp >=$from and a.timestamp <= c.timestamp) and c.url='$kpi' and c.visitorid=a.visitorid and crawl=0 and (status=200 or status=302) group by a.visitorid");
        $converted = "insert into $table $entrytime";
        $db->Execute($converted);
        
        $range = $db->Execute("select min(length), max(length),count(*) from $table");
        $range_data = $range->FetchRow();
        $min = $range_data[0];
        $max = $range_data[1];
        $total_visitors=$range_data[2];
        $blocksize=($max-$min)/8;
        //$query  = subsetDataToSourceID("select \"0 to 10 seconds            \", ((count(*)*1.0)/$total_visitors*100),count(*), avg(length)/60, \"1\" as ord from $table where length >=0 and length <=10 union ");
        $query  .= subsetDataToSourceID("select \"0 to 60 seconds            \", ((count(*)*1.0)/$total_visitors*100),count(*), avg(length)/60, \"1\" as ord from $table where length >=0 and length <=60 union ");
        $query  .= subsetDataToSourceID("select \"1 to 10 minutes            \", ((count(*)*1.0)/$total_visitors*100),count(*), avg(length)/60, \"2\" as ord from $table where length >60 and length <=600 union ");
        $query  .= subsetDataToSourceID("select \"10 to 30 minutes            \", ((count(*)*1.0)/$total_visitors*100),count(*), avg(length)/60, \"3\" as ord from $table where length >600 and length <=1800 union ");
        $query  .= subsetDataToSourceID("select \"30 min to 1 hour            \", ((count(*)*1.0)/$total_visitors*100),count(*), avg(length)/60, \"4\" as ord from $table where length >1800 and length <=3600 union ");
        $query  .= subsetDataToSourceID("select \"1 hour to 24 hours            \", ((count(*)*1.0)/$total_visitors*100),count(*), CONCAT(FORMAT(((avg(length)/60)/60),1),' hours'), \"5\" as ord from $table where length >3600 and length<=86400 union ");
        $query  .= subsetDataToSourceID("select \"1 day to 7 days            \", ((count(*)*1.0)/$total_visitors*100),count(*), CONCAT(FORMAT((((avg(length)/60)/60)/24),1),' days'), \"6\" as ord from $table where length >86400 and length<=(86400*7) union ");
        $query  .= subsetDataToSourceID("select \"more than 1 week            \", ((count(*)*1.0)/$total_visitors*100),count(*), CONCAT(FORMAT((((avg(length)/60)/60)/24),1),' days'), \"7\" as ord from $table where length >(86400*7) ");
        $query .= " order by ord";        
        $applytrafficsource = false;        
		
		$reportoptions = "daterangeField,trafficsource,roadto,limit";
	}
	if($labels == "Today XML"){
		$data = array();
		$import = array();
		
		$today = $to;
		$tor = date("F Y",$to);
		$from = strtotime("01 ".$tor);
		$range = $today - $from;
		$prevTo = $today - $range - 1;
		$prevFrom = $from - $range;
		$p = ""; // Prev Range
		$t = ""; // This Range
		if($range < (31 * 86400) || $range < (30 * 86400)){
			$prevFrom = mktime(0,0,0,date("m", $prevTo),01,date("Y", $prevTo));
			$t = date("F Y",$today);	
			$p = date("F Y",$prevTo);
		}else{
			$t = date("M Y",$from)." - ".date("M Y",$to);	
			$p = date("M Y",$prevFrom)." - ".date("M Y",$prevTo);
		}
		// Today
		$query = "select visitors, pages, (pages/visitors) as ppu from {$profile->tablename_vpd} where days = FROM_UNIXTIME('{$today}','%d-%b-%Y %a')";
		$result = $db->Execute($query);
		while($row = $result->fetchRow()){
			$import["visitors"]["today"] = $row["visitors"];
			$import["pages"]["today"] = $row["pages"];
			$import["pagespervisitors"]["today"] = number_format($row["ppu"],2);
		}
		
		// This Range
		$query="select month,visitors,pages,(pages/visitors) as ppu,visits,(visits/visitors) as vpu from ".$profile->tablename_vpm." where timestamp >= {$from} and timestamp <= {$to} order by timestamp desc";
		$result = $db->Execute($query);
		while($row = $result->fetchRow()){
			$import["visitors"][$t] = $row["visitors"];
			$import["pages"][$t] = $row["pages"];
			$import["pagespervisitors"][$t] = number_format($row["ppu"],2);
			$import["visitsPerUser"][$t] = number_format($row["vpu"],2);
		}
		
		$query="select FROM_UNIXTIME(timestamp,'%m') as month,avg(visitors * 1.00) as avgvisitors,avg(visits * 1.00) as avgvisits from {$profile->tablename_vpd} 
		where timestamp >= {$from} and timestamp <= ". ($to - 86400)." group by month order by timestamp";
		$result = $db->Execute($query);
		while($row = $result->fetchRow()){
			$import["visitorsperday"][$t] = $row["avgvisitors"];
			$import["visitsperday"][$t] = $row["avgvisits"];
		}
		$query  = "select ((count(distinct visitorid) * 1.00) / {$import["visitors"][$t]}) * 100 as ctr from {$profile->tablename_conversions} where timestamp >= {$from} and timestamp <= ". ($to - 86400)." group by url";
		$a=0;
		$totctr = 0;
		$result=$db->Execute($query);
		if ($result) {
			while ($row = $result->FetchRow()) {
				$totctr=$totctr+$row["ctr"];
				$a++;
			}
		}
		if ($a <> 0) {
			$import["conversionRate"][$t] = number_format(($totctr/$a),2);
		} else {
			$import["conversionRate"][$t] = number_format(0,2);
		}

		// Prev Range
		$query="select month,visitors,pages,(pages/visitors) as ppu,visits,(visits/visitors) as vpu from ".$profile->tablename_vpm." where timestamp >= {$prevFrom} and timestamp <= {$prevTo} order by timestamp desc";
		$result = $db->Execute($query);
		while($row = $result->fetchRow()){
			$import["visitors"][$p] = $row["visitors"];
			$import["pages"][$p] = $row["pages"];
			$import["pagespervisitors"][$p] = number_format($row["ppu"],2);
			$import["visitsPerUser"][$p] = number_format($row["vpu"],2);
		}
		$query="select FROM_UNIXTIME(timestamp,'%m') as month,avg(visitors * 1.00) as avgvisitors,avg(visits * 1.00) as avgvisits from {$profile->tablename_vpd} 
		where timestamp >= {$prevFrom} and timestamp <= {$prevTo} group by month order by timestamp";
		$result = $db->Execute($query);
		while($row = $result->fetchRow()){
			$import["visitorsperday"][$p] = $row["avgvisitors"];
			$import["visitsperday"][$p] = $row["avgvisits"];
		}
		$query  = "select ((count(distinct visitorid) * 1.00) / {$import["visitors"][$p]}) * 100 as ctr from {$profile->tablename_conversions} where timestamp >= {$prevFrom} and timestamp <= {$prevTo} group by url";
		$a=0;
		$totctr = 0;
		$result=$db->Execute($query);
		if ($result) {
			while ($row = $result->FetchRow()) {
				$totctr=$totctr+$row["ctr"];
				$a++;
			}
		}
		if ($a <> 0) {
			$import["conversionRate"][$p] = number_format(($totctr/$a),2);
		} else {
			$import["conversionRate"][$p] = number_format(0,2);
		}
		
		if (date("m", $to)=="1") {
			$prevyear=(date("Y", $to)-1);
			$prevmonth=12; 
		} else { 
			$prevyear=date("Y", $tto);
			$prevmonth=(date("m", $to)-1);
		}
		$archn = ($prevmonth) . $prevyear;	
		$import["bounceRate"][$t] = number_format(((BounceVisitors("redo") / $import["visitors"][$t]) * 100),2);
		$import["bounceRate"][$p] = number_format(((BounceVisitors($archn) / $import["visitors"][$p]) * 100),2);	
		
		$i = 0;
		foreach($import as $k => $v){
			$data[$i][0] = $k;
			$data[$i][1] = $v["today"];
			$data[$i][2] = $v[$t];
			$data[$i][3] = $v[$p];
			$i++;
		}
		
		$showfields = "item,"._TODAY.",{$t},{$p}";
		
		
		$query = "data array";
	}
}



function SummaryMenu($labels)
{
    global $multisel,$from,$to,$conf,$quickdate,$limit;
    global $l,$l_constant;
    global $tag;
    //echo "<pre>".var_dump($labels)."</pre>";
    $labels=urldecode($labels);
    ?>
     
    <div id="accordion">
    <?php
    

    $i=1;
    if (strpos($_SERVER["PHP_SELF"], "index.php")!=FALSE) {
            $s = "background: #CCFFCC no-repeat left;";
            $class="";
            $labels=_TODAY;
        } else {
            $s = "";
            $class="onmouseover=\"srrowOverEffect(this)\" onmouseout=\"srrowOutEffect(this)\"";
    }
    echo "<h3 class=\"accordion_header_first\"><a href=\"#\">"._SUMMARY_OVERVIEW."</a></h3>";
    echo "<div class=\"reportmenu\"><ul>";
    $icon = "images/icons/date.gif";
    echo " <li> <a href=\"index.php?from=$from&amp;to=$to&amp;conf=$conf&amp;quickdate=$quickdate&amp;submit=Report\" class=\"sidelinks\" style=\"$s background-image: url($icon);\">"._TODAY_OVERVIEW."</a> </li>";    
    $active=0;
    foreach ($l as $key => $thislabel) {
        $thislabel=urldecode($thislabel);
        $icon="";      
        /*
        if ((strpos($thislabel, "isitors")!=FALSE) || (strpos($thislabel, "Users")!=FALSE) || (strpos($thislabel, "Week")!=FALSE)) {
            $icon="images/icons/user.gif";
        } else if (strpos($thislabel, "Pages")!=FALSE && strpos($thislabel, "Crawl")===FALSE) {
            $icon="images/icons/page.gif";
        } else if (strpos($thislabel, "Referrers")!=FALSE) {
            $icon="images/icons/link.gif";
        } else if (strpos($thislabel, "Keyword")!=FALSE) {
            $icon="images/icons/search.gif";
        } else if (strpos($thislabel, "oogle")!=FALSE) {
            $icon="images/icons/searchengines.gif";
        } else if (strpos($thislabel, "nternal")!=FALSE) {
            $icon="images/icons/searchengines.gif";
        } else if (strpos($thislabel, "Engines")!=FALSE) {
            $icon="images/icons/searchengines.gif";
        } else if (strpos($thislabel, "Paths")!=FALSE) {
            $icon="images/icons/toppaths.gif";
        } else if (strpos($thislabel, "Countries")!=FALSE) {
            $icon="images/icons/world.gif";
        } else if (strpos($thislabel, "rror")!=FALSE) {
            $icon="images/icons/error.gif";
        } else if (strpos($thislabel, "perating")!=FALSE) {
            $icon="images/icons/computer.gif";
        } else if (strpos($thislabel, "rowsers")!=FALSE) {
            $icon="images/icons/computer.gif";
        } else if (strpos($thislabel, "Resolution")!=FALSE) {
            $icon="images/icons/computer.gif";
        } else if (strpos($thislabel, "Palette")!=FALSE) {
            $icon="images/icons/computer.gif";
        } else if (strpos($thislabel, "Traffic")!=FALSE) {
            $icon="images/icons/calendar.gif";
        } else if (strpos($thislabel, "Crawl")!=FALSE) {
            $icon="images/icons/crawler.gif";
        } else if ((strpos($thislabel, "erformance")!=FALSE) || (strpos($thislabel, "Conversion")!=FALSE)) {
            $icon="images/icons/cart.gif";
        } 
        else {
            $icon="";
        }
        */
        if ($thislabel==_TODAY_OVERVIEW) {
            $icon="images/icons/calendar.gif";
        }
        if ($thislabel==_VISITORS_PER_DAY) {
            $icon="images/icons/day.gif";
        }
        if ($thislabel==_VISITORS_PER_MONTH) {
            $icon="images/icons/month.gif";
        }
        if ($thislabel==_VISITORS_PER_HOUR) {
            $icon="images/icons/time.gif";
        }
        if ($thislabel==_DAYS_OF_THE_WEEK) {
            $icon="images/icons/week.gif";
        }
        if ($thislabel==_TOP_COUNTRIES_CITIES) {
            $icon="images/icons/world.gif";
        }
        
        if ($thislabel==_MOST_ACTIVE_USERS) {
            $icon="images/icons/user_red.gif";
            $active=1; 
            echo "</ul></div>";
            echo "<h3 class=\"accordion_header\"><a href=\"#\">"._VISITOR_DETAILS."</a></h3>";
            echo "<div class=\"reportmenu\"><ul>";
        }
        if ($thislabel==_RECENT_VISITORS) {
            $icon="images/icons/user_add.gif";
        }
        if ($thislabel==_AUTHENTICATED_VISITORS) {
            $icon="images/icons/user_green.gif";
        }
        if ($thislabel==_VISITORS_AND_VISITS) {
            $icon="images/icons/user_go.gif";
        }
        if ($thislabel==_VISIT_DURATION) {
            $icon="images/icons/duration.gif";
        }
        if ($thislabel==_TOTAL_DURATION) {
            $icon="images/icons/tduration.gif";
        }
          
        if ($thislabel==_TOP_PAGES) {
            $icon="images/icons/page.gif";
            $active=2; 
            echo "</ul></div>";
            echo "<h3 class=\"accordion_header\"><a href=\"#\">"._POPULAR_CONTENT."</a></h3>";
            echo "<div class=\"reportmenu\"><ul>";
        }
        if ($thislabel==_TOP_PAGES_DETAILS) {
            $icon="images/icons/page_attach.gif";
        }
        if ($thislabel==_TOP_FEEDS) {
            $icon="images/icons/rss-icon.gif";
        }
        if ($thislabel==_FEEDBURNER) {
            $icon="images/icons/rss-icon.gif";
        }
        if ($thislabel==_TOP_ENTRY_PAGES) {
            $icon="images/icons/page_add.gif";
        }
        if ($thislabel==_TOP_EXIT_PAGES) {
            $icon="images/icons/page_delete.gif";
        }
        if ($thislabel==_TOP_CLICK_PATHS) {
            $icon="images/icons/toppaths.gif";
        }
        if ($thislabel==_INTERNAL_SITE_SEARCH) {
            $icon="images/icons/searchengines.gif";
        }
        
        //if ($thislabel==_TOP_REFERRERS) {
        if ($thislabel==_TRAFFIC_BREAKDOWN) {
            $icon="images/icons/traffic-breakdown.png";
            $active=3; 
            echo "</ul></div>";
            echo "<h3 class=\"accordion_header\"><a href=\"#\">"._INCOMING_TRAFFIC."</a></h3>";
            echo "<div class=\"reportmenu\"><ul>";
        }
        if ($thislabel==_TOP_REFERRERS) {
            $icon="images/icons/link.gif";
        }
        if ($thislabel==_TOP_REFERRERS_DETAILS) {
            $icon="images/icons/link.gif";
        }
        if ($thislabel==_TOP_KEYWORDS) {
            $icon="images/icons/search.gif";
        }
        if ($thislabel==_TOP_KEYWORDS_DETAILS) {
            $icon="images/icons/search.gif";
        }
        if ($thislabel==_SEARCH_ENGINES) {
            $icon="images/icons/searchengines.gif";
        }
        if ($thislabel==_GOOGLE_RANKINGS) {
            $icon="images/icons/searchengines.gif";
        }
        if ($thislabel==_MOST_ACTIVE_CRAWLERS) {
            $icon="images/icons/crawler.gif";
        }
        if ($thislabel==_MOST_CRAWLED_PAGES) {
            $icon="images/icons/crawler.gif";
        }
               
        if ($thislabel==_BROWSERS) {
            $icon="images/icons/computer.gif";
            $active=4; 
            echo "</ul></div>";
            echo "<h3 class=\"accordion_header\"><a href=\"#\">"._CLIENT_SYSTEM."</a></h3>";
            echo "<div class=\"reportmenu\"><ul>";
        }
        if ($thislabel==_OPERATING_SYSTEMS) {
            $icon="images/icons/computer.gif";
        }
        if ($thislabel==_SCREEN_RESOLUTION) {
            $icon="images/icons/computer.gif";
        }
        if ($thislabel==_COLOR_PALETTE) {
            $icon="images/icons/computer.gif";
        }
        if ($thislabel==_MOBILE_AGENTS) {
            $icon="images/icons/phone.gif";
        }
        
        if ($thislabel==_ALL_TRAFFIC_BY_DAY) {
            $icon="images/icons/calendar.gif";
            $active=5; 
            echo "</ul></div>";
            echo "<h3 class=\"accordion_header\"><a href=\"#\">"._VARIOUS."</a></h3>";
            echo "<div class=\"reportmenu\"><ul>";
        }
        if ($thislabel==_ALL_TRAFFIC_BY_MONTH) {
            $icon="images/icons/calendar.gif";
        }
        if ($thislabel==_ALL_TRAFFIC_BY_HOUR) {
            $icon="images/icons/calendar.gif";
        }
        if ($thislabel==_ERROR_REPORT) {
            $icon="images/icons/error.gif";
        }
        
        if ($thislabel==_OVERALL_PERFORMANCE) {
            $icon="images/icons/cart.gif";
            $active=6; 
            echo "</ul></div>";
            echo "<h3 class=\"accordion_header\"><a href=\"#\">"._CONVERSION_RATE."</a></h3>";
            echo "<div class=\"reportmenu\"><ul>";
            //echo "</ul></div></div>"; return;  
        }
        if ($thislabel==_WORKSPACE) {
            $icon="images/icons/briefcase.png";
        } 
        if ($thislabel==_PAGE_CONVERSION) {
            $icon="images/icons/cart.gif"; 
        }
        if ($thislabel==_REFERRER_CONVERSION) {
            $icon="images/icons/cart.gif"; 
        }
        if ($thislabel==_KEYWORD_CONVERSION) {
            $icon="images/icons/cart.gif"; 
        }
        if ($thislabel==_TIME_TO_CONVERSION) {
            $icon="images/icons/tduration.gif"; 
        }
        
        if ($thislabel == urldecode($labels)) {
            $s = "background: #CCFFCC no-repeat left;"; 
            $openactive=$active;
            //echo "openactive is $openactive";
        } else if ($thislabel == substr($labels,4)) { // this one is for the error reports that get appended a 3 charter status code, plus a space
            $s = "background: #CCFFCC no-repeat left;"; 
            $openactive=$active;  
        } else {
            $s="";
        }
        
        /*
        echo " <li> ";?><a href="javascript:Report(escape('<?php echo addslashes($thislabel);?>'));" class="sidelinks" style="$s background-image: <?php echo "url($icon);\">".$thislabel."</a> </li>";       
        */
        //echo " <li> <a href='javascript:Report(\"".urlencode($thislabel)."\");'  class=\"sidelinks\" style=\"$s background-image: url($icon);\">".$thislabel."</a> </li>"; 
        echo " <li> <a href='javascript:Report(\"".$l_constant[$key]."\");'  class=\"sidelinks\" style=\"$s background-image: url($icon);\">".$thislabel."</a> </li>"; 
        
        $i++;
    }
    /*
    if (urldecode($labels)==_PAGE_CONVERSION) {
        $s = "background: #CCFFCC no-repeat left;"; 
        $openactive=$active;
    } else {
        $s="";
    }        
    $icon="images/icons/cart.gif";
    echo " <li> <a href=\"performance.php?labels="._PAGE_CONVERSION."&amp;from=$from&amp;to=$to&amp;conf=$conf&amp;quickdate=$quickdate&amp;report=Pages&amp;submit=Report\" class=\"sidelinks\" style=\"$s background-image: url($icon);\">"._PAGE_CONVERSION."</a> </li>"; 

    if (urldecode($labels)==_REFERRER_CONVERSION) {
        $s = "background: #CCFFCC no-repeat left;"; 
        $openactive=$active;
    } else {
        $s="";
    }
    $icon="images/icons/cart.gif";
    echo " <li> <a href=\"performance.php?labels="._REFERRER_CONVERSION."&amp;from=$from&amp;to=$to&amp;conf=$conf&amp;quickdate=$quickdate&amp;report=Referrers&amp;submit=Report\" class=\"sidelinks\" style=\"$s background-image: url($icon);\">"._REFERRER_CONVERSION."</a> </li>";
    
    if (urldecode($labels)==_KEYWORD_CONVERSION) {
        $s = "background: #CCFFCC no-repeat left;"; 
        $openactive=$active;
    } else {
        $s="";
    }
    $icon="images/icons/cart.gif";
    echo " <li> <a href=\"performance.php?labels="._KEYWORD_CONVERSION."&amp;from=$from&amp;to=$to&amp;conf=$conf&amp;quickdate=$quickdate&amp;report=Keywords&amp;submit=Report\" class=\"sidelinks\" style=\"$s background-image: url($icon);\">"._KEYWORD_CONVERSION."</a> </li>";
    */
    if (urldecode($labels)==_THE_ROAD_TO_SALES) {
        $s = "background: #CCFFCC no-repeat left;"; 
        $openactive=$active;
    } else {
        $s="";
    }
    $icon="images/icons/cart_add.gif";
    echo " <li> <a href=\"roadtosales.php?labels="._THE_ROAD_TO_SALES."&amp;from=$from&amp;to=$to&amp;conf=$conf&amp;quickdate=$quickdate&amp;submit=Report\" class=\"sidelinks\" style=\"$s background-image: url($icon);\">"._THE_ROAD_TO_SALES."</a> </li>";

    echo "</ul></div></div><div id=\"expand\" style=\"padding:4px;margin-left:5px;color:silver;\"><a href=\"javascript:expandall();\" class=graylink>"._EXPAND_ALL."</a>  <a href=\"javascript:collapseall();\" class=graylink>"._RESTORE."</a></div>";
    echo "<script type=\"text/javascript\">";
    if (@$openactive) { 
        ?>
        function openaccordion() {
         /*alert(<?php echo ($openactive); ?>);*/
         $("#accordion").accordion('activate', <?php echo ($openactive); ?>);        
        }
        <?php
    } else {
        
        echo "function openaccordion() { /* alert('no openactive'); $(\"#accordion\").accordion('activate', 0);*/ }";
    }
    ?>
      function expandall() {
          $("#accordion").accordion('destroy');
          $("#accordion").css("border","1px solid silver");
          $("#accordion").css("padding","8px");
      }
      function collapseall() {
            $("#accordion").css("border","none");
            $("#accordion").css("padding","0px");
          $("#accordion").accordion({autoHeight: false, collapsible: true });
      }
    <?php
    echo "</script>";

}
function GetBase($n,$log) {
    $b = exp(log($n)/$log);
    return $b;        
}

?>