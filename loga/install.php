<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 

//error_reporting(E_ALL ^ E_DEPRECATED); 
error_reporting(E_ALL); 

$real_path = realpath("index.php");
$path = dirname($real_path);
$path = str_replace ("\\", "/", $path);
ini_set('display_errors','on'); 


$step = @$_REQUEST["step"];
$save = @$_REQUEST["save"];
$saved = @$_REQUEST["saved"];
$conf = @$_REQUEST["conf"];
$debug = @$_REQUEST["debug"];

require_once 'core_factory.php';
Logaholic_sessionStart();

$lang = Logaholic_setLang();
include_once "languages/$lang.php";

require_once("includes/version.php");
require_once("includes/dateselector.php");

// All database errors will be raised as errors.  No need for special handling.
require_once("components/adodb/adodb-errorhandler.inc.php");   
// Use adodb for database work.
require_once("components/adodb/adodb.inc.php");

if (!$step) {
  $step=1;       
}
function installSecurityCheck($input) {
    $checkcnf=urldecode($input);
	//echo "<hr>checking: $checkcnf<br>";
    if (strpos($checkcnf," ") !== FALSE) {
        echo "Security failue: exiting (Invalid characters found)";
        exit();
    }
    if (strpos($checkcnf,"\"") !== FALSE) {
        echo "Security failue: exiting (Invalid characters found)"; 
        exit();
    }
    if (strpos($checkcnf,"'") !== FALSE) {
        echo "Security failue: exiting (Invalid characters found)";
        exit();
    }
    if (strpos($checkcnf,"<") !== FALSE) {
        echo "Security failue: exiting (Invalid characters found)";
        exit();
    }
	if (strpos($checkcnf,"{") !== FALSE) {
        echo "Security failue: exiting (Invalid characters found)";
        exit();
    }
}
function langUI() {
    global $step;
    $lang_key = Logaholic_getLanguageRequestKey();
    if ($step==1) {
        echo "<div style=\"margin-top:20px;\">Change language: <img src=images/flags/uk.png> <a href=install.php?$lang_key=english>English</a> &nbsp;&nbsp;&nbsp; ";
        echo "<img src=images/flags/de.png> <a href=install.php?$lang_key=german>German</a> &nbsp;&nbsp;&nbsp; ";
        echo "<img src=images/flags/nl.png> <a href=install.php?$lang_key=dutch>Dutch</a>  &nbsp;&nbsp;&nbsp; ";
        echo "<img src=images/flags/it.png> <a href=install.php?$lang_key=italian>Italian</a>  &nbsp;&nbsp;&nbsp; ";
        echo "</div>";
    }
}       

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title><?php echo _LOGAHOLIC_INSTALLATION;?></title>
<link rel="stylesheet" type="text/css" href="templates/template_v2.css">
<style>
BODY {
    font-family:arial;
    font-size:12px;
    margin-top:0px;
    margin-left:0px;
}
</style>
<script language="JavaScript" type="text/javascript" src="loghint.js"></script>
<script language="JavaScript" type="text/javascript">
function dbtype(value) {
    if (value=="sqlite") {
        document.getElementById('dbname').style.display="none"; 
        document.getElementById('dblogin').style.display="none";
        document.getElementById('serverexplain').innerHTML="<?php echo _ENTER_A_PATH_AND_FILENAME;?>: <?php echo $path;?>/files/logaholicdata.sqlite</b>";
        document.dbform.mysqlserver.value="<?php echo $path;?>/files/logaholicdata.sqlite";
    } else {
        document.getElementById('dbname').style.display="block"; 
        document.getElementById('dblogin').style.display="block";
        document.getElementById('serverexplain').innerHTML="<?php echo _NAME_OF_DB_SERVER;?>";
		document.dbform.mysqlserver.value="localhost";
    }
    
}
</script>
</head>

<body>


<table cellpadding=2 width=100% border=0 cellspacing=0>
<tr><td class=dotline bgcolor=#f0f0f0><font face=arial size=3><b><?php echo _INSTALL_LOGAHOLIC;?></b></font></td>
<td class=dotline bgcolor=#f0f0f0 align=right></td>
</tr>
<tr>
<td>
<div class="indentbody">

<?php langUI(); ?>

<?php

if ($step==1) {
    echo "&nbsp;<P>";
    

?>
<?php echo _WELCOME_TO_LOGAHOLIC;?><P>
<ol>
	<?php
	echo _EXPLAIN_INSTALL_STEPS;
	?>
</ol>
<?php
    echo "&nbsp;<P><font size=3><b>"._STEP_1_INSTALL."</b></font><P><ol>";

   $real_path = realpath("index.php");
   $path = dirname($real_path);
     $checkfile=$path."/files/";
     if (is_writable($checkfile)) {
         echo "<li>"._DIR_WRITABLE."<P>";
		$notok = 0;
	 } else {
			echo "<li><b>"._DIR_NOT_WRITABLE.":</b></font><br>";
			echo "<font face=courier color=blue>$path/files</font><p>";
			echo _EXPLAIN_HOW_TO_MAKE_DIR_WRITABLE."<p>";

			$notok=1;
	 }
	 echo "</ol>";	 
	 if ($notok==1) {
		echo "<P><form method=get action=install.php><input type=hidden name=step value=1><input type=submit value="._RETRY."></form>";
	 } else {
         if (strpos(file_get_contents("markup.php"),"LOGAHOLIC_EDITION\",4")!==FALSE) {
            echo "<P><form method=get action=install.php><input type=hidden name=step value=2><input type=submit value="._CONTINUE."></form>";
         } ELSE {
		    echo "<P><form method=get action=install.php><input type=hidden name=step value=2><input type=submit value="._CONTINUE."></form>";
         }
	 }
}

if ($step==2) {
	echo "&nbsp;<P><font size=3><b>"._STEP_2_INSTALL."</b></font>";

	if (isset($save)) {
        echo "<ol>";
		$real_path = realpath("index.php");
		$path = dirname($real_path);
		$gfile=$path . "/files/global.php";
		
		installSecurityCheck($_REQUEST["DatabaseName"]);
		installSecurityCheck($_REQUEST["databasedriver"]);
		installSecurityCheck($_REQUEST["mysqlserver"]);
		installSecurityCheck($_REQUEST["mysqluname"]);
		installSecurityCheck($_REQUEST["mysqlpw"]);
		installSecurityCheck($_REQUEST["mysqlprefix"]);
		//installSecurityCheck($_REQUEST["mysqltmp"]);
		
		$DatabaseName = $_REQUEST["DatabaseName"];
		$mysqlserver = $_REQUEST["mysqlserver"];
		$mysqluname = $_REQUEST["mysqluname"];
		$mysqlpw = $_REQUEST["mysqlpw"];
		$mysqlprefix = $_REQUEST["mysqlprefix"];
		$mysqltmp = $_REQUEST["mysqltmp"];
		$databasedriver = $_REQUEST["databasedriver"];
		
		if (!isset($databasedriver) || (!$databasedriver)) {
                $databasedriver = "mysql";        
		}
		
		if ($databasedriver == "mysql") {
			if (function_exists("mysqli_connect")) {
			
				$db = ADONewConnection("mysqli");		

			} else {
                if (function_exists("mysql_connect")) {
				    $db = ADONewConnection("mysql");
                } else {
                    echo _WRONG_PHP_VERSION; 
                    exit();  
                }
			}
		} else if ($databasedriver = "sqlite") {
			if (function_exists("sqlite_open")) {
                if (phpversion() >= "5.0.0") {
				    $db = ADONewConnection("pdo_sqlite");
			    } else {
			      /**
			      * PhpEd needs some help figuring out the class type, since ADONewConnection returns an abstraction.
			      * @var ADOConnection 
			      */ 
				    $db = ADONewConnection("sqlite");
			    }
            } else {
                    echo _WRONG_PHP_VERSION;
                    exit();  
            }
            
		} else {
			die(_DB_DRIVER.": ".$databasedriver." "._NOT_SUPPORTED.".");
		}
	
		$db->Connect($mysqlserver,$mysqluname,$mysqlpw);		
		if (!$db->IsConnected())     {
			echo "<li>"._NO_DB_CONNECT.".<br>".$db->ErrMsg;
			$error=1;
		} else {
			echo "<li> "._OK_DB_CONNECT." <b>$mysqlserver</b><p>";
			@$db->Execute("create database if not exists $DatabaseName CHARACTER SET = utf8 COLLATE = utf8_general_ci;");
			if (@$sel = @$db->SelectDB(@$DatabaseName)) {
				 echo "<li> "._OK_DB." <b>$DatabaseName</b> "._FOUND.".<p>";
			} else {
				$error=1;
				echo "<li> <font color=red>"._DB_CREATE_FAILED." $DatabaseName</font><P>"._CREATE_DB_MANUAL." $DatabaseName</font><br>"._WHEN_DONE_RELOAD."<P>";
			}
			$ServerInfo = $db->ServerInfo();
			echo "<p>"._DB_DRIVER.": $databasedriver<br>"._DB_TYPE.": " . $ServerInfo["description"] . "<br>"._DB_VERSION.": ".$ServerInfo["version"]."</p>";
			
			if (!isset($error)) {
				$fp = fopen ($gfile,"w+");
				fwrite ($fp, "<?php \$DatabaseName=\"".$_REQUEST["DatabaseName"]."\";\n");
				fwrite ($fp, "\$databasedriver=\"".$_REQUEST["databasedriver"]."\";\n");
				fwrite ($fp, "\$mysqlserver=\"".$_REQUEST["mysqlserver"]."\";\n");
				fwrite ($fp, "\$mysqluname=\"".$_REQUEST["mysqluname"]."\";\n");
				fwrite ($fp, "\$mysqlpw=\"".$_REQUEST["mysqlpw"]."\";\n");
				fwrite ($fp, "\$mysqlprefix=\"".$_REQUEST["mysqlprefix"]."\";\n");
				fwrite ($fp, "\$mysqltmp=\"".logaholic_dir()."files/\";\n");
				fwrite ($fp, "?>");
				fclose ($fp);
				@chmod($gfile, 0666);
				echo "<P><li>"._DB_WRITTEN_IN.":<br> <b>$gfile</b><P>";
				// Include the common routines - we can connect to the database at this point...
				include_once "common.inc.php";  
				
			}
		}
		echo "</ol>";
		if (!isset($error)) {
            echo "<P><form method=get action=install.php><input type=hidden name=step value=\"";
			// if (!IS_PHPDOCK) {	
                echo "3";
            // } else {
                // echo "4";
            // }
            echo "\"><input type=submit value="._CONTINUE."></form>";                                                       
		} else {
            echo _ERROR_OCCURED.": $error";   
        }
	 } else {
		if (file_exists("files/global.php")) {
		
			include("files/global.php");
			include_once "common.inc.php"; 
			
			if($session->isAdmin() == false){				
				die("Security failue: exiting (You must be admin.)");
			}
		}
		if (!isset($DatabaseName)) {
			$DatabaseName = "logaholic";
			$databasedriver = "mysql"; 
			$mysqluname = "";
			$mysqlpw = "";
			$mysqlprefix = "LWA_";
			$mysqltmp = logaholic_dir()."files/";
			$mysqlserver = "127.0.0.1";
		 }
		 
		 
	$mysqlsave = 1;
	include_once "common.inc.php"; 
		 
	?>
	
	&nbsp;<br>
	<form method=get id="dbform" name="dbform" action=install.php>
	<div>
        <table cellpadding=6 width=700 border=0>
	    <tr><td colspan=3 class=toplineblue bgcolor=#BBDDFF><font size=3><b><?php echo _DB_SETTINGS;?></b></font></td</tr>
	    <tr><td width=130 valign=top class=dotline><?php echo _DB_TYPE;?>:</td><td valign=top class=dotline><select name="databasedriver" onChange="dbtype(this.value);">
	    <?php
	      foreach ($supported_databases as $key => $value) {
	  	    echo "<option value=\"$key\"".($key == $databasedriver?" selected":"").">$value</option>\n";
		    }            
	    ?>
	    </select></td>
	    <td width=335 bgcolor=f0f0f0><?php echo _DB_TYPE_EXPLAIN;?></td></tr>
        </table>
    </div>
	<div id="dbname">
        <table cellpadding=6 width=700 border=0>
        <tr><td width=130 valign=top class=dotline><?php echo _DB_NAME;?>:</td><td valign=top class=dotline><input type=text  size=35  name=DatabaseName value="<?php echo @$DatabaseName; ?>"></td>
	    <td width=335 bgcolor=f0f0f0><?php echo _DB_NAME_EXPLAIN;?></td></tr>
        </table>
    </div>
    <div id="dbserver">
        <table cellpadding=6 width=700 border=0>
	    <tr><td width=130 valign=top class=dotline><?php echo _DB_SERVER;?>:</td><td valign=top class=dotline><input type=text name="mysqlserver" id="mysqlserver" size=35 value="<?php echo @$mysqlserver; ?>"></td>
	    <td width=335 id="serverexplain" bgcolor=f0f0f0><?php echo _DB_SERVER_EXPLAIN;?></td></tr>
        </table>
    </div>
    <div id="dblogin">
        <table cellpadding=6 width=700 border=0>
	    <tr><td width=130 valign=top class=dotline><?php echo _DB_USERNAME;?>:</td><td valign=top class=dotline><input type=text  size=35 name=mysqluname value="<?php echo @$mysqluname; ?>"></td>
	    <td width=335 bgcolor=f0f0f0><?php echo _DB_USERNAME_EXPLAIN;?></td></tr>
        </table>
        <table cellpadding=6 width=700 border=0>
	    <tr><td width=130 valign=top class=dotline><?php echo _DB_PASSWORD;?>:</td><td valign=top class=dotline><input  size=35 type=password name=mysqlpw value="<?php echo @$mysqlpw; ?>"></td>
	    <td width=335 bgcolor=f0f0f0><?php echo _DB_PASSWORD_EXPLAIN;?></td></tr>
        </table>
    </div>
	<div id="dbprefix">
        <table cellpadding=6 width=700 border=0>
			<tr>
				<td width=130 valign=top class=dotline><?php echo _DB_PREFIX;?>:</td>
				<td valign=top class=dotline><input type='text' size='35' name='mysqlprefix' value="<?php echo @$mysqlprefix; ?>"></td>
				<td width=335 bgcolor=f0f0f0><?php echo _PLEASE_ENTER_PREFIX;?></td>
			</tr>
        </table>
	</div>
	<div style='display: none;' id="dbtemp">
        <table cellpadding=6 width=700 border=0>
			<tr>
				<td width=130 valign=top class=dotline>Temporary Files Directory:</td>
				<td valign=top class=dotline><input type='hidden' size='35' name='mysqltmp' value="<?php echo @$mysqltmp; ?>"></td>
				<td width=335 bgcolor=f0f0f0>Enter a path to a directory where (potentially large) temporary files will be stored.</td>
			</tr>
        </table>
	</div>
    <div>
        <table cellpadding=6 width=700 border=0> 
	    <tr><td colspan=3><input type=hidden name=step value=2><input name=save type=submit value="<?php echo _CONTINUE; ?>"></td></tr>
        </table>
    </div>

</form>
<?php
        if ($databasedriver=="sqlite") {
            echo "<script language\"javascript\">dbtype('sqlite');</script>";   
        }
	}
}
if ($step==3) {
	 echo "&nbsp;<P><font size=3><b>"._DOWNLOAD_GEOIP."</b></font>";
	 $install=1;
	 $new=1;
	 $profiles=1;
	 $real_path = realpath("index.php");
	 $page=dirname($_SERVER['SCRIPT_NAME'])."/update.php";
	 
     include_once("components/geoip/open_geoip.php");
     if (isset($gi)) {
        echo "<P>"._GEOIP_FOUND."<P>";
     } else {    
        if (file_exists("components/geoip/GeoLiteCity.dat.gz")) {
            echo "<P><B>"._UNZIP_GEOIP."</b><P>";
        }
         
     echo "<P>"._GEOIP_MAXMIND_IS_USED." <b>" .dirname($real_path) . "/components/geoip/GeoLiteCity.dat</b><P>";
	 ?>
	 <ul>
	 <LI> <a href="http://www.maxmind.com/download/geoip/database/GeoLiteCity.dat.gz" target="_new"><b><?php echo _DOWNLOAD_LATEST_GEOIP;?><P>
	 <?php
     }
     ?>
	 <form method="get" action="install.php"><input type=hidden name="step" value="4"><input type=submit value=<?php echo _CONTINUE;?>></form>
	 <?php
}


if ($step==4) {
	// if (!IS_PHPDOCK) {
        echo _DONE_GO_TO_PROFILES;
        include "checksettings.php";
    // } else {
        // echo _DONE_CREATE_PROFILE;
          
    // } 
    
    
    
     if (@$debug){
     phpinfo();
     echo "<br>output_buffering:".ini_get('output_buffering');
     echo "<br>display_errors:".ini_get('display_errors');
     echo "<br>allow_url_fopen:".ini_get('allow_url_fopen');
     }
     /*
     $install=1;
	 $new=1;
	 $profiles=1;
	 $page=dirname($_SERVER['SCRIPT_NAME'])."/update.php";
	 include_once "common.inc.php";  
	 $editprofile = new SiteProfile();
	 include "editprofile.php";
     */
}
/*
if ($step==5) {
	 echo "&nbsp;<P><font size=3><b>Step 5: Create Web Site Profile</b></font><P>";
	 echo $saved;
	 echo "<P><form method=get action=install.php><input type=hidden name=step value=6><input type=hidden name=conf value=$conf><input type=submit value=Continue></form>";
}

if ($step==6) {
	 echo "&nbsp;<P><font size=3><b>Step 6: Import log file</b></font><P>";
	 echo "Congratulations, the installation is now complete.<P>Everything has been set up and you are ready for the last step, importing your webserver log file.<P><font color=red>Please note</font> that if you have a very large log file, this first import <b>can take a very long time</b> to complete. <br>Updates after this initial import will be a lot faster.<P>";
	 //echo "We don't recommend importing log files larger than 10 million lines in this version of Logaholic.";
	 echo "<P><form method=get action=update.php><input type=hidden name=conf value=$conf><input type=submit value=Finish></form>";
}
*/

?>


</div>
</td>
</tr>
</table>


</body>
</html>
