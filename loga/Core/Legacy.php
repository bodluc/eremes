<?php 
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
/**
 * Standard Logaholic application functions - Legacy set
 * 
 * This should be compatible with all PHP versions
 * 
 * override this function by setting $_ENV['LOGAHOLIC_SYSTEM'] = $system, where
 * $system is a file that defines all the same functions. Place customized
 * function set in components/System/$system.php
 */

//////////////////////////////////////////////////////////////
function Logaholic_sessionStart()
{
    @session_start();
}

/**
 * Log out of Logaholic
 * 
 * @return void
 */
function Logaholic_applicationLogout()
{
    $cd = logaholic_dir();
    include_once "{$cd}user_login/process.php";
    $process = new Process;
    $process->procLogout();
    //echo "logged out";
    //exit();
    header("location: ../index.php");
}

//////////////////////////////////////////////////////////////
function lang() {
    return Logaholic_setLang();
}

function Logaholic_setLang()
{
    //set up language
    global $available_langs,$conf;
    $cd = logaholic_dir();
    if ($lhandle = @opendir($cd."languages/")) {       
        $lngs = 0;
        while ($lfile = readdir($lhandle)) {
            if ($lfile[0] != '.' && (strpos($lfile,".php")!==FALSE)) {
                $available_langs[$lngs]=str_replace(".php","",$lfile);
                $lngs++;                
            }
        }
        sort($available_langs);
    }
    if (!empty($_REQUEST['lang']) && (in_array($_REQUEST['lang'],$available_langs)==true)) {
        if (strpos($_SERVER["PHP_SELF"], "install.php")!=TRUE) {
            setCookie("lg_lang",$_REQUEST['lang'],(time()+(365*86400)),"/");
        }
        $lang = $_REQUEST['lang'];
    } else if (!empty($_COOKIE['lg_lang'])) {
        $lang = $_COOKIE['lg_lang'];
    } else {        
        $lang = "english";
    }
    // one last check to be sure
    if (!file_exists($cd."languages/".$lang.".php")) {
        $lang = "english";
    }
    include_once "languages/$lang.php";
    return $lang;
}

function Logaholic_getCurrentLang()
{
    if (isset($_COOKIE['lg_lang']) && !empty($_COOKIE['lg_lang'])) {
        $lang = $_COOKIE['lg_lang'];
    } else {        
        $lang = "english";
    }
    return $lang;
}

function Logaholic_getAvailableLangs()
{
    global $available_langs;
    if (!$available_langs ) {
        $cd = logaholic_dir();
        if ($lhandle = @opendir($cd."languages/")) {       
            $lngs = 0;
            while ($lfile = readdir($lhandle)) {
                if ($lfile[0] != '.' && (strpos($lfile,".php")!==FALSE)) {
                    $available_langs[$lngs]=str_replace(".php","",$lfile);
                    $lngs++;                
                }
            }
            sort($available_langs);
        }
    }
    return $available_langs;
}

function Logaholic_getLanguageRequestKey() {
    return 'lang';
}

/**     
 * Determine if the language is being changed
 */
function hasLangChanged()
{	
	$a = @$_COOKIE['lg_lang'];
	$b = @$_REQUEST['lang'];
	
	if (isset($a) && isset($b)) {
		if ($a != $b) {
			return true;
		}
	}
	return false;
}

//////////////////////////////////////////////////////////////
function logaholic_dir() {
    return Logaholic_logaholicDir();
}

function Logaholic_logaholicDir()
{
    if (@file_exists("version_check.php")) {
        //we're in the base folder
        $cd="";
    } else {
        if (@file_exists("../version_check.php")) {
            //we're in a sub folder
            $cd="../";
        } else {
            if (@file_exists("../../version_check.php")) {
                //we're in a sub sub folder
                $cd="../../";
            } else {
                return false;
            }            
        }
    }      
    $real_path = realpath($cd."version_check.php");
    return str_replace("\\","/",dirname($real_path))."/";
}
?>