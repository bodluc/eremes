<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
// PHPLOCKITOPT NOENCODE
@ignore_user_abort(TRUE); 
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); 
 
if (isset($_REQUEST)) {
        while(list($varname, $varvalue) = each($_REQUEST)) { $$varname = $varvalue; }
}
if (!@$debug) {
    error_reporting(0);
} else {
    error_reporting(E_ALL);
}
if (!$conf) {
    //no confname
    exit();
} else {
    $confname=$conf;   
}
if (!isset($_REQUEST['trackermode'])) {
	$trackermode=$_REQUEST['trackermode'];
} else {
	$trackermode=1;
}
include("../files/global.php");
include_once("table_definitions.php");

$conn = mysql_connect($mysqlserver,$mysqluname,$mysqlpw);
if (!$conn)     {
    exit();
}
$sel = mysql_select_db($DatabaseName, $conn);

if(empty($_REQUEST['lwa_id'])) {
	$a = mysql_query("select tablename from ".TBL_PROFILES." where profilename=\"$confname\"");
	if ($r = mysql_fetch_row($a)) {
		$confname = $r[0];
	}
	
	$tablename=$mysqlprefix.$confname."_trackerlog";   // e.g. "mytablename"
	$tablename_screenres=$mysqlprefix.$confname."_screenres";
	$tablename_colordepth=$mysqlprefix.$confname."_colordepth";
} else {
	$tablename=$confname."_trackerlog";   // e.g. "mytablename"
	$tablename_screenres=$confname."_screenres";
	$tablename_colordepth=$confname."_colordepth";
}

$newses=false;

// gather data

$logtimestamp = time();
$ipnumber = $_SERVER["REMOTE_ADDR"];
$useragent=$_SERVER['HTTP_USER_AGENT'];

if (!$visitorid) {
    if (!$Logaholic_VID) {
        // New User: if we don't have a visitorid passed from javascript, make one. And add a cookie for backward compatibility 
        $Logaholic_VID = md5($ipnumber . ':' . $useragent);
        SetCookie("Logaholic_VID",$Logaholic_VID,time() + (8 * 7 * 86400000),"/",$HTTP_HOST,0);    
    }    
} else {
    $Logaholic_VID = $visitorid;    
}
if (!$_COOKIE['Logaholic_Screenres']) {
	SetCookie("Logaholic_Screenres",$sessionid);
	$log_screenres = true;

    //Log the screen properties (only do this once per visit, and store it daily)
    $noon = mktime(12,0,0,date("m"),date("d"),date("Y")); 
    if ($w) {
        $screenres=$w."x".$h;
        mysql_query("INSERT INTO $tablename_screenres (timestamp,screenres,visits) VALUES ('$noon','$screenres',1) ON DUPLICATE KEY UPDATE visits=visits+1");
    }
    if ($cd) {
        mysql_query("INSERT INTO $tablename_colordepth (timestamp,colordepth,visits) VALUES ('$noon','$cd',1) ON DUPLICATE KEY UPDATE visits=visits+1");
    }    
}
if ($trackermode==1) {
    
    $method = $_SERVER["REQUEST_METHOD"];
	if(!empty($_SERVER["HTTP_REFERER"])) {
		$path = $_SERVER["HTTP_REFERER"];
	} else {
		$path = "";
	}
    $path = explode ("//", $path);
    $path = $path[1];
    $url = strstr($path, "/");
    if ($url=="")  {
        $url="/";
    }
	if (isset($page)) {
		$url=$page;
	}
	
	$url = strip_tags($url);
	
    if (!isset($docTitle)) {
        $docTitle="";    
    }
    $status='200';

    //construct a log line
    $dateline=date("d/M/Y:H:i:s",$logtimestamp);
    if ($referrer=="") {
      $referrer="-";
    }
    $logline="$ipnumber - - [$dateline +0200] \"$method $url HTTP/1.1\" 200 0 \"$referrer\" \"$useragent\" \"Logaholic_VID=$Logaholic_VID; docTitle=$docTitle\"";
    $logline=addslashes($logline);
    mysql_query("insert into $tablename (logline) values (\"$logline\")");
    if (@$debug) {
        echo mysql_error();
        echo "insert into $tablename (logline) values (\"$logline\")";
        $took = time() - $logtimestamp;
        echo "This took $took seconds";
    }
    // echo transparent pixel
    header("Content-type: image/png");
    echo base64_decode("iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAACXBIWXMAAAsTAAALEwEAmpwYAAAABGdBTUEAALGOfPtRkwAAACBjSFJNAAB6JQAAgIMAAPn/AACA6QAAdTAAAOpgAAA6mAAAF2+SX8VGAAAAEElEQVR42mL4//8/A0CAAQAI/AL+26JNFgAAAABJRU5ErkJggg==");
}
?>