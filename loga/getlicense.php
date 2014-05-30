<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
error_reporting(E_ALL);
ini_set('display_errors','on');
 
require_once 'core_factory.php';
Logaholic_sessionStart();

$lang = Logaholic_setLang();

//include "languages/$lang/getlicense.php";
include_once "languages/$lang.php";
include "includes/version.php"; 

if (@$_POST['license']) {
    if (isset($_COOKIE['update_license'])) {
        $isupgrade="1";
        setcookie("update_license", "", time() - 86400);
    }
}

$regip=$_SERVER['HTTP_HOST'];
if (!$regip) {
  if ($HTTP_SERVER_VARS["SERVER_NAME"] !="") {
      $regip=$HTTP_SERVER_VARS["SERVER_NAME"];
  } else {
      $regip=$_SERVER['SERVER_ADDR'];
  }
}

$fullp="http://".$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title><?php echo _GET_LOGAHOLIC_LICENSE;?></title>
<link rel="stylesheet" type="text/css" href="templates/template_v2.css">
<style>
BODY {
    font-family:arial;
    font-size:12px;
    margin-top:0px;
    margin-left:0px;
}
</style>
</head>
<body>


<table cellpadding=2 width=100% border=0 cellspacing=0>
<tr><td class=dotline bgcolor=#f0f0f0><font face=arial size=3><b><?php echo _STEP2_GET_LOGAHOLIC_LICENSE;?></b></font></td>
<td class=dotline bgcolor=#f0f0f0 align=right></td>
</tr>
<tr>
<td>
<div class="indentbody"> 
&nbsp;
<P>
<font size=3><b><?php echo _LOGAHOLIC_LICENSE;?></b></font><P>

<?php

if (@$_POST['license']) {
    $lic = $_POST['license'];
           
    if (strpos("..".$lic,md5($_SERVER['HTTP_HOST']))==FALSE) {
        if (strpos($lic,"100b8cad7cf2a56f6df78f171f97a1ec")!==FALSE) {
            // we're ok    
        } else {
            echo _LICENSE_CHECK_FAIL;
            exit();
        }
    } 
    if (strlen($lic) < 10) {
      echo _LICENSE_FILE_EMPTY;
      exit();
    } else {
        echo "<b>"._LICENSE_RECEIVED."...</b><br>"; 
        $real_path = realpath("index.php");
	    $path = dirname($real_path);
  		$gfile=$path . "/files/";
        if (is_writable($gfile)) { 
        } else {
          echo _PROBLEM.": <font face=courier color=blue>$path/files</font><p>";
          echo _FILES_DIR_NOT_WRITABLE;
          echo _EXPLAIN_CHANGE_PERMISSIONS;  
          echo _PLEASE_TRY_AGAIN;
          exit();
        }
        $gfile=$path . "/files/licensefile.txt";
  		$fp = fopen ($gfile,"w+") or die(_COULD_NOT_WRITE_TO_LICENSEFILE);
  		fwrite ($fp, trim($_POST['license']));
  		fclose ($fp);
  		echo _REGISTERED_TO.": ".$_POST['licname'].", ".$_POST['e']."<br>";
        echo _PRODUCT.": ".$_POST['product']."<br>";
        if ($_POST['expires'] ==0) {
            echo _EXPIRES_NEVER;
        } else {
            echo _EXPIRES_ON.": ".@date("Y-m-d", $_POST['expires']);   
        }
  		?>
        <br>
        <?php
        if (isset($isupgrade) || file_exists("files/global.php")) {
            $continue_url="profiles.php";
        } else {
            $continue_url="install.php";    
        }
        ?>
  		<form method=post action="<?php echo $continue_url;?>">
        <input type=hidden name=step value="2">
      <input type=submit value="<?php echo _CONTINUE;?>">
      </form>
      </div></td></tr></table>  </body>
      <?php     
      exit();
  	}
}
?>
<b><?php echo _LICENSE_HEADER;?></b><P>
<form method=post action="http://www.logaholic.com/install/makelicense.php">
<table><tr><td>
<?php echo _FIRST_NAME;?>:</td><td> <input type=text name=licname></td></tr><tr><td>
<?php echo _EMAIL_ADDRESS;?>:</td><td> <input type=text name=e></td></tr><tr><td colspan=2 align=center>
<input type=hidden name="regip" value="<?php echo $regip; ?>">
<input type=hidden name="fullp" value="<?php echo $fullp; ?>">
<input type=hidden name="version" value="<?php echo LOGAHOLIC_VERSION_NUMBER .",". LOGAHOLIC_BASE_EDITION; ?>">
<input type=submit value="<?php echo _GET_LICENSE;?>">
</td></tr></table>
</form>
<P>
<?php echo _GET_LICENSE_EXPLAIN_PART1;?> <a target=_new href="http://www.logaholic.com/install/makelicense.php?regip=<?php echo $regip; ?>&manual=1&version=<?php echo LOGAHOLIC_VERSION_NUMBER .",". LOGAHOLIC_BASE_EDITION; ?>"><?php echo _CLICK_HERE;?></a> <?php echo _GET_LICENSE_EXPLAIN_PART2;?> <a href=install.php?step=2><?php echo _CLICK_HERE;?></a> <?php echo _GET_LICENSE_EXPLAIN_PART3;?>
</div></td></tr>
</table>  
</body>
