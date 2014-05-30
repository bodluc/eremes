<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 

include_once("components/useragents/techpattern.php");

function GetCorrectFromDate($table) {
    //we need to make protection against holes in the summary tables if update.php has failed halfway
    // so, we should always update from the last record in the table, and set 'from' based on that
    global $db, $profile, $orist, $first_ever_time, $force;
    echoConsoleSafe("<script>pstatus('"._CHECKING_TABLE." $table ...                                                            ')</script>\n");
    lgflush();

    if ($force==true && isset($first_ever_time)) {
        return mktime(0,0,0,date("m",$first_ever_time),date("d",$first_ever_time),date("Y",$first_ever_time)); 
    } 
    
    $q = $db->Execute("select * from $table order by timestamp desc limit 1");
    if ($data=$q->FetchRow()) {
       /* 
       echo "<br>the last record in $table is timestamp ". $data['timestamp'] . ", and orist is $orist,";
        if (date("Y-m-d",$data['timestamp'])==date("Y-m-d",$orist)) {
            echo " that is the same day (".date("Y-m-d H:i:s",$data['timestamp']).") so our data is integer";
        } else {
            echo " that is NOT the same day (orist: ".date("Y-m-d H:i:s",$orist).", database: ".date("Y-m-d H:i:s",$data['timestamp']).", ) so our data is wrong!!";
        }
        echo "<P>";
        */
        return mktime(0,0,0,date("m",$data['timestamp']),date("d",$data['timestamp']),date("Y",$data['timestamp']));
    } else {
        return 0; // if no data, try to fill the table from the start
    }      
}

function UpdateUseragents() {
	global $db, $profile;
	
	$useragent_time = time();
	
	$q = $db->Execute("SELECT * FROM {$profile->tablename_useragents} WHERE name IS NULL");
	while($row = $q->FetchRow()) {
		$agent = get_useragent($row['useragent']);
			
		if($agent['agent_is_bot'] == 1) {
			$crawl = 1;
		} else {
			$crawl = 0;
		}
		
		if($agent['agent_is_mobile'] == 1) {
			$ismobile = 1;
		} else {
			$ismobile = 0;
		}
		
		// Prepare the query to prevent MySQL Injection attacks
		$stmt = $db->Prepare("UPDATE {$profile->tablename_useragents} SET 
			`name` = ?,
			`version` = ?,
			`os` = ?,
			`os_version` = ?,
			`engine` = ?,
			`hash` = ?,
			`is_bot` = ?,
			`is_mobile` = ?,
			`device` = ?
		WHERE hash = '".$row['hash']."'");
		
		$db->Execute($stmt, array(
			$agent['agent_name'],
			$agent['agent_version'],
			$agent['agent_os'],
			$agent['agent_os_version'],
			$agent['agent_engine'],
			$row['hash'],
			$agent['agent_is_bot'],
			$agent['agent_is_mobile'],
			$agent['agent_device']
		));
	}
	
	// echo "Updating useragents table took: ".(time() - $useragent_time)." seconds<br/>";
}

/*
 we could use this concept to speed up unique calculation
 for the current month total
 while (list ($key,$val)=each ($known)) {
  echo "$key<br>";
 }
*/
 
if ($vnum < 4) {
    $sqlmethod="use";
} else {
	$sqlmethod="force";
}
// note, we have removed this:
// ".(($databasedriver == "mysql")? $sqlmethod ." index (timestamp)":"")."
// from vpd, vpm, dailyurls
// because it seems that it's faster to let mysql decide when to use or not to use the index, see this for more info
// http://www.mysqlperformanceblog.com/2007/08/28/do-you-always-need-index-on-where-column/


ob_start(); 
echoConsoleSafe("<script>pstatus('"._SUMMARIES."...                                                                       ')</script>\n");
lgflush();

if (@$force==true && isset($last_inserted_time)) {
    $from = mktime(0,0,0,date("m",$first_ever_time),date("d",$first_ever_time),date("Y",$first_ever_time));
    $to = mktime(23,59,59,date("m",$last_inserted_time),date("d",$last_inserted_time),date("Y",$last_inserted_time));
    echoConsoleSafe(_FORCED_UPDATE_RECALCING_SUMMARIES." ".date("Y-m-d H:i:s",$from)." to ".date("Y-m-d H:i:s",$to), true);
} else {
    $to = mktime(23,59,59,date("m"),date("d"),date("Y"));
}

/* this part is in development
//Update the daily sumstore tables
$sumstore = $profile->tablename."_sumstore";
$from = GetCorrectFromDate($sumstore);
echo "Updateing sumstore urls";
// now clear whatever is in the timerange
$db->Execute("delete from $sumstore where timestamp >=$from and timestamp <=$to");
// now update
$db->Execute("insert into $sumstore select \"url\", min(timestamp),count(distinct visitorid) as visitors, count(distinct sessionid) as visits,count(*) as pageviews,url from $profile->tablename where timestamp >=$from and timestamp <=$to and crawl=0 and (status=200 or status=302) group by FROM_UNIXTIME(timestamp,'%d-%b-%Y %a'),url order by timestamp");
*/

//echo "Updating Daily Summary Table";
echoConsoleSafe("<script>pstatus('"._UPDATING_DAILY_SUMMARY."...')</script>");
echoConsoleSafe("<script>pbar(25); self.document.forms.progress.progbar.value='|||||||||||||||||||||||';</script>\n");
lgflush();

//first figure out the from date
$from = GetCorrectFromDate($profile->tablename_vpd);

// now clear whatever is in the timerange
$db->Execute("delete from $profile->tablename_vpd where timestamp >=$from and timestamp <=$to");
// now update
$db->Execute("insert into $profile->tablename_vpd select min(timestamp),FROM_UNIXTIME(timestamp,'%d-%b-%Y %a') AS days, count(distinct visitorid) as visitors,count(*) as pages,count(distinct sessionid) as visits from $profile->tablename ".(($databasedriver == "mysql")? $sqlmethod ." index (timestamp)":"")." where timestamp >=$from and timestamp <=$to and crawl=0 group by days order by timestamp");

//echo "Updating Daily Compressed Table";
echoConsoleSafe("<script>pstatus('"._UPDATING_DAILY_COMPRESSED."...')</script>");
echoConsoleSafe("<script>pbar(25); self.document.forms.progress.progbar.value='|||||||||||||||||||||||';</script>\n");
lgflush();

//first figure out the from date
$from = GetCorrectFromDate($profile->tablename_dailyurls);

// now clear whatever is in the timerange
$db->Execute("delete from $profile->tablename_dailyurls where timestamp >=$from and timestamp <=$to");
// now update    
$db->Execute("insert into $profile->tablename_dailyurls select min(a.timestamp),FROM_UNIXTIME(a.timestamp,'%d-%b-%Y %a') AS days, count(distinct a.visitorid) as uvisitors, a.url, a.referrer ".
          "from $profile->tablename as a ".(($databasedriver == "mysql")? $sqlmethod ." index (timestamp)":"")." where a.timestamp >=$from and a.timestamp <=$to and a.crawl=0 and (a.status=200 or a.status=302) group by days,a.url,a.referrer order by a.timestamp");        

//first figure out the from date
$from = GetCorrectFromDate($profile->tablename_vpm);

if ($from!=0) {
    $from = mktime(0,0,0,date("m",$from),01,date("Y",$from));
}
if (@$force==true && isset($last_inserted_time)) {
    $lday=get_month_lastday(date("m",$last_inserted_time),date("Y",$last_inserted_time));
    $to = mktime(23,59,59,date("m",$last_inserted_time),$lday,date("Y",$last_inserted_time));   
}
//echo "Updating Month Summary";
echoConsoleSafe("<script>pstatus('"._UPDATING_MONTH_SUMMARY."...')</script>");
echoConsoleSafe("<script>pbar(50); self.document.forms.progress.progbar.value='||||||||||||||||||||||||||||||||||||||||||||||';</script>");
lgflush();

$db->Execute("delete from $profile->tablename_vpm where timestamp >=$from and timestamp <=$to");
//echo "<br>delete from $profile->tablename_vpm where timestamp >=$from and timestamp <=$to<br>";

$db->Execute("insert into $profile->tablename_vpm select min(timestamp),FROM_UNIXTIME(timestamp,'%M %Y') AS month, count(distinct visitorid) as visitors,count(*) as pages,count(distinct sessionid) as visits from $profile->tablename ".(($databasedriver == "mysql")? $sqlmethod ." index (timestamp)":"")." where timestamp >=$from and timestamp <=$to and crawl=0 group by month order by timestamp");
//echo "<br>insert into $profile->tablename_vpm select min(timestamp),FROM_UNIXTIME(timestamp,'%M %Y') AS month, count(distinct visitorid) as visitors,count(*) as pages, (count(*)/(count(distinct visitorid)*1.00)) as ppu from $profile->tablename where timestamp >=$from and timestamp <=$to and crawl=0 group by month order by timestamp<br>";

if (@$force==true && isset($last_inserted_time)) {
    $to = mktime(23,59,59,date("m",$last_inserted_time),date("d",$last_inserted_time),date("Y",$last_inserted_time));  
}

//now do the conversions table
if ($profile->targetfiles!="") { 
    //first figure out the from date
    $from = GetCorrectFromDate($profile->tablename_conversions);
    
    
    //$from = mktime(0,0,0,$startmonth,$startday,$startyear);
    //echo "Updating Conversion Summary";
    echoConsoleSafe("<script>pstatus('"._UPDATING_CONVERSION_SUMMARY."...')</script>");
    echoConsoleSafe("<script>pbar(75); self.document.forms.progress.progbar.value='||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||';</script>");
    lgflush();

    $db->Execute("delete from $profile->tablename_conversions where timestamp >=$from and timestamp <=$to") or die($db->ErrorMsg());
    $db->Execute("insert into $profile->tablename_conversions select timestamp,visitorid,a.url from $profile->tablename as a,$profile->tablename_urls as u where (timestamp >=$from) and (timestamp <=$to) and a.url=u.id and $profile->targets_sql and (crawl=0) and (status=200 or status=302) order by timestamp")  or die("Error updating conversion stats. ".$db->ErrorMsg());
    //echo "insert into $profile->tablename_conversions select timestamp,visitorid,url from $profile->tablename where timestamp >=$from and timestamp <=$to and $profile->targets_sql and crawl=0 and (status=200 or status=302) order by timestamp<P>";
    //echo "$orist, $startday, $startmonth";
    //exit();
}

//create the returning visitors number
$todaysdate=time();
//if (@$newlines > 20) {
    
    echoConsoleSafe("<script>pstatus('"._CALCING_NEW_RETURN_VISITORS."...')</script>");
    echoConsoleSafe("<script>pbar(85); self.document.forms.progress.progbar.value='||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||';</script>");  
    lgflush();

    
    $from   = mktime(0,0,0,date("m", $todaysdate),date("d", $todaysdate),date("Y", $todaysdate));
    $to     = mktime(23,59,59,date("m", $todaysdate),date("d", $todaysdate),date("Y", $todaysdate));
    ReturnVisitors("today");
//}


if (@$startday!=date("d",time()) || getProfileData($profile->profilename, "$profile->profilename.rv_thismonth", 0)=="") {
    
    echoConsoleSafe("<script>pstatus('"._CALCING_NEW_RETURN_VISITORS_THIS_MONTH."...')</script>"); 
    
    echoConsoleSafe("<script>pbar(95); self.document.forms.progress.progbar.value='||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||';</script>");  
    lgflush();
    $from   = mktime(0,0,0,date("m", $todaysdate),01,date("Y", $todaysdate));
    $to     = mktime(23,59,59,date("m", $todaysdate),date("d", $todaysdate),date("Y", $todaysdate));
    ReturnVisitors("thismonth");
}

//find feed urls and update to crawl=2
if ($profile->feedurl!="") {
    if (!@$orist) {
        $orist=$from;    
    }
    $db->Execute("update $profile->tablename set crawl='2' where timestamp >=$orist and crawl='0' and url IN (select id from $profile->tablename_urls where url like '$profile->feedurl%')") or die($db->ErrorMsg());
}

foreach($reports as $k => $v){
	$r = new $reports[$k]["ClassName"]();
	if($r->UpdateStats()){
		echoConsoleSafe("<script>pstatus('Updated $k...')</script>");
	}
}

if(!empty($update_preference) && $update_preference == 'perl') {
	UpdateUseragents();
}


//wipe the session table, it's useless now
echoConsoleSafe("<script>pstatus('"._CLEARING_SESSIONS."...')</script>");
$db->Execute("delete from $profile->tablename_sessionids") or die($db->ErrorMsg());

echoConsoleSafe("<script>pstatus('"._FINISHED_UPDATE."')</script>");
echoConsoleSafe("<script>pbar(100); self.document.forms.progress.progbar.value='||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||';</script>");
lgflush();

?>
