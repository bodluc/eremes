<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
//PHPCIPHER NO ENCRYPT
// PHPLOCKITOPT NOENCODE
//required variables
$DatabaseName="replace_db_name";  // e.g. "logaholic"
$mysqlserver="replace_mysql_servername";   // e.g. "localhost"
$mysqluname="replace_mysql_user";             // e.g. "root"
$mysqlpw="replace_mysql_password";
$conf="replace_profile_name";
$testid="replace_testid";
$visitormethod="replace_visitormethod";
$tablename="replace_tablename";

$cookiename="lg".$conf.$testid;

//get data
$sptablename=$tablename."_splittests";
$sptablename_results=$tablename."_splittests_results";

$conn = mysql_connect($mysqlserver,$mysqluname,$mysqlpw) or die ($db->ErrorMsg());
$sel = mysql_select_db($DatabaseName, $conn);
//$q = $db->Execute("select * from $sptablename where id=$testid");
$q = mysql_query("select * from $sptablename where id=$testid");
//$data=$q->FetchRow();
$data=mysql_fetch_array($q);

//split traffic
if (!isset($_COOKIE[$cookiename])) {
    // New User: determine which content to present and store the choice in a cookie
    if (!isset($data['splitperc'])) {
      $data['splitperc']=50;
    }
  	$stest=rand(0,100);
    if ($stest < (100- $data['splitperc'])) {
	    SetCookie($cookiename,"A",time() + 8640000,"/",$_SERVER['HTTP_HOST'],0);
		$splittester="A";
	} else {
		SetCookie($cookiename,"B",time() + 8640000,"/",$_SERVER['HTTP_HOST'],0);
		$splittester="B";
	}
}

// $page is an overrule parameter
if (strtoupper($page)=="A") {
  echo stripslashes($data['pagea']);
  exit();
} else if (strtoupper($page)=="B") {
  echo stripslashes($data['pageb']);
  exit();
}

// Get some variables to track
$ipnumber = $_SERVER["REMOTE_ADDR"];
$useragent = $_SERVER['HTTP_USER_AGENT'];
$timestamp= time();


if ($visitormethod == 1) {
	$visitorid = md5($ipnumber);
} else if ($visitormethod == 2) {
	$visitorid = md5($ipnumber . ':' . $useragent);
} else if ($visitormethod == 3) {
    if (!isset($_COOKIE['Logaholic_VID'])) { 
        $visitorid = md5($ipnumber . ':' . $useragent);
    } else {
        $visitorid = $_COOKIE['Logaholic_VID'];    
    }
}

//spit content & update stats
if ($_COOKIE[$cookiename]=="A" || $splittester=="A") {
  //$db->Execute("insert into $sptablename_results set testid='$testid', visitorid='$visitorid',timestamp='$timestamp',page='A'") or die ($db->ErrorMsg());
  mysql_query("insert into $sptablename_results set testid='$testid', visitorid='$visitorid',timestamp='$timestamp',page='A'");
  if (isset($_REQUEST['jsmode'])) {
    echo "document.write(\"".$data['pagea']."\");";    
  } else {
    echo stripslashes($data['pagea']);
  }
} else {
  //$db->Execute("insert into $sptablename_results set testid='$testid', visitorid='$visitorid',timestamp='$timestamp',page='B'") or die ($db->ErrorMsg());
  mysql_query("insert into $sptablename_results set testid='$testid', visitorid='$visitorid',timestamp='$timestamp',page='B'") or die ($db->ErrorMsg());
  if (isset($_REQUEST['jsmode'])) {
    echo "document.write(\"".$data['pageb']."\");";
  } else {
    echo stripslashes($data['pageb']);
  }
}
?>
