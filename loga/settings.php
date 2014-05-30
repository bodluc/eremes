<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
require_once 'core_factory.php';
Logaholic_sessionStart();

$path = dirname(realpath("index.php"));
$path = str_replace ("\\", "/", $path);

if (@$_REQUEST["uninstall"]=="confirm") {
	include_once("common.inc.php");
	if(getGlobalSetting("IsWordPressInstall") == true) {
		if(!empty($mysqlprefix)) {
			 if ($q = $db->Execute("SHOW TABLES FROM {$DatabaseName} LIKE '{$mysqlprefix}%'")) {
				while($tbl = $q->FetchRow()) {
					$db->Execute("DROP TABLE {$tbl[0]};");
				}
				
				$active_plugins = $db->Execute("SELECT * FROM wp_options WHERE option_name = 'active_plugins'");
				$plugindata = $active_plugins->FetchRow();
				
				$plugins = unserialize($plugindata['option_value']);
				
				foreach($plugins as $pluginid => $plugin) {
					if(strpos($plugin, "logaholic") !== false) {
						unset($plugins[$pluginid]);
					}
				}
				
				$plugindata['option_value'] = serialize($plugins);
				$active_plugins = $db->Execute("UPDATE wp_options SET option_value = '{$plugindata['option_value']}' WHERE option_name = 'active_plugins'");
				
				$wp_url = $db->Execute("SELECT * FROM wp_options WHERE option_name = 'siteurl'");
				$wp_url = $wp_url->FetchRow();
				$wp_url = $wp_url['option_value'];
				header("Location: {$wp_url}");
			}
		}
		exit;
	}
	
	echo "<div class='indentbody'>&nbsp;<br>";
	if ($db->Execute("drop database $DatabaseName")) {
	echo _DATABASE." $DatabaseName "._REMOVED.".<br><br>";
	}  else {
	echo _PROBLEM_DELETING_DATABASE.'.';
	}
	echo "<font color=red><b>"._WHAT_TO_DO_TO_UNINSTALL.".</b></font><br><br>";
	echo "</div></body></html>";
	exit();
}

if ((isset($_REQUEST["mysql"])) && $_REQUEST["mysql"]==1) {
	include_once "languages/english/interface.php";
    if (isset($_REQUEST["mysqlsave"]) && !isset($_REQUEST['mysqlsaved'])) {
        saveMysqlsettings();
        echo "<a href=\"settings.php?mysql=1&edit=1&mysqlsaved=1\">Continue to try to connect</a>";
        exit();
    }
}

function saveMysqlsettings() {
    global $lang,$path,$gfile;
    if (!isset($lang)) {
        $lang="english";       
    } 
    $gfile=$path . "/files/global.php";
    //$gfile=exec("pwd") . "/files/global.php";
    $fp = fopen ($gfile,"w+");
    if (!$fp) {
        echo _ERROR_CREATING_FILE.": $gfile.  "._CHECK_PERMISSIONS_FILE.".";
        return false;
    } else {
        fwrite ($fp, "<?php \$DatabaseName=\"".@$_REQUEST["DatabaseName"]."\";\n");
        fwrite ($fp, "\$databasedriver=\"".@$_REQUEST["databasedriver"]."\";\n");
        fwrite ($fp, "\$mysqlserver=\"".@$_REQUEST["mysqlserver"]."\";\n");
        fwrite ($fp, "\$mysqluname=\"".@$_REQUEST["mysqluname"]."\";\n");
        fwrite ($fp, "\$mysqlpw=\"".@$_REQUEST["mysqlpw"]."\";\n");
        fwrite ($fp, "\$mysqlprefix=\"".@$_REQUEST["mysqlprefix"]."\";\n");
        fwrite ($fp, "\$mysqltmp=\"".logaholic_dir()."files/\";\n");
        fwrite ($fp, "?>");
        fclose ($fp);
        echo "<P><li> "._OK_DATABASE_WRITTEN.":<br> <b>$gfile</b><P>";
        return true;
    }
}
        

//$conf="Profiles";
require "top.php";

?>
<script language="JavaScript" type="text/javascript">
function dbtype(value) {
    if (value=="sqlite") {
        document.getElementById('dbname').style.display="none"; 
        document.getElementById('dbuname').style.display="none";
        document.getElementById('dbpw').style.display="none"; 
        document.getElementById('serverexplain').innerHTML="Enter a path and file name for your SQLite database, For example: <?php echo $path;?>/files/logaholicdata.sqlite</b>";
        document.dbform.mysqlserver.value="<?php echo $path;?>/files/logaholicdata.sqlite";
    } else {
        document.getElementById('dbname').style.display="block"; 
        document.getElementById('dbuname').style.display="block";
        document.getElementById('dbpw').style.display="block"; 
        document.getElementById('serverexplain').innerHTML="The name of the database server i.e. <b>localhost</b>";
        document.dbform.mysqlserver.value="localhost";
    }
    
}
</script>
<?php

if (($validUserRequired) && (!$session->isAdmin())) {
	echo _SORRY_NO_PERMISSIONS.".<br>";
	exit;
}

if (@$_REQUEST["uninstall"]=="yes") { 
     echo "<div class='indentbody'>&nbsp;<br>";
     echo "<font color=red><b>"._ARE_YOU_SURE_ABOUT_UNINSTALLING."?<br><br>"._UNINSTALL_WARNING_PART1." \"$DatabaseName\" "._UNINSTALL_WARNING_PART2."</font><br><br>";
     echo "<a id='uninstall_confirm' href=\"settings.php?uninstall=confirm\" onclick=\"return confirm('"._LAST_CHANCE_TO_CANCEL."')\">"._YES_IM_100_PERC_SURE." '$DatabaseName'</a>";
     echo "</div></body></html>"; 
     exit();
}

if (@$_REQUEST["deleterange"]==1) {
    //if ($editprofile->profileloaded) {
        echo "<div class=\"indentbody\">"._DELETE_DATE_RANGE_FROM." <b>all</b><P><form method=get action=\"settings.php\">";
        echo "<input type=hidden name=\"deleterange\" value=2>";
        DateSelector($from,$to);
        echo "<input type=radio name=what value=2 checked> "._DELETE_ALL_LOG_DATA."<br>";
        echo "<input type=radio name=what value=1> "._DELETE_ONLY_DETAIL_DATA."<br>";
        echo "<input type=radio name=what value=3> "._DELETE_ONLY_SUMMARY_DATA."<br>";
        echo "<input type=submit name=submit value=Delete>  "._PLEASE_BE_PATIENT_DELETING_TAKES_A_WHILE."</div>";

        exit();
    //}
}
// if (isset($_REQUEST["deleterange"])==2) {
    // PrintLoadingBox("Delete range from database");
    // $q= $db->Execute("select profilename from ".TBL_PROFILES);
    // $i=0;
    // while ($data=$q->FetchRow()) {
        // $pname=$data[0];
        // $profile = new SiteProfile($pname);
        // echo "<div class=\"warning ui-state-error ui-corner-all\">Deleting from $pname...<br>\n"; 
        // DeleteRange($profile,$what);
        // echo "</div>";
        // flush();  
        // $i++;   
    // }
// }

if (isset($_REQUEST["globalstats"])=="yes") { 
    echo "<div class='indentbody'>&nbsp;<br>";
    include "includes/globalstats.php";    
    echo "</div></body></html>";
    exit();
}

if (isset($_REQUEST["toggle_ipencode"])==1) {
    $msg = toggleIPencoding();
    if ($msg!==false) {
        echoWarning($msg);
        include("files/global.php");
    } else {
        echoWarning("There was a problem saving this setting");
    }    
}
	
if ((isset($_REQUEST["mysql"])) && $_REQUEST["mysql"]==1) {
	if (isset($_REQUEST["mysqlsaved"])) {
		echo "<div class=\"indentbody\"><ul>";			
			// Load up the file!
			@include($gfile);
			
			if (!isset($databasedriver) || (!$databasedriver)) {
				$databasedriver = "mysql";
			}
			
			if ($databasedriver == "mysql") {
				if ((phpversion() >= "5.0.0")  && (function_exists("mysqli_connect"))) {
					$db = ADONewConnection("mysqli");
				} else {
					$db = ADONewConnection("mysql");
				}
			} elseif ($databasedriver = "sqlite") {
				if (phpversion() >= "5.0.0") {
					$db = ADONewConnection("pdo_sqlite");
				} else {
					$db = ADONewConnection("sqlite");
				}
			} else {
				die(_DATABASE_DRIVER.": ".$databasedriver." "._NOT_SUPPORTED.".");
			}
			
			$nconn = $db->Connect($mysqlserver,$mysqluname,$mysqlpw,$DatabaseName);
			
			if (!$db->IsConnected())     {
				echo "<li> "._ERROR_NO_CONN_GO_BACK.".<br>".$db->ErrorMsg();
				$error=1;
			} else {
				
				echo "<li> "._OK_SUCCESSFULLY_CONNECTED." <b>$mysqlserver</b><p>";
				
				$ServerInfo = $db->ServerInfo();
				echo "<p>"._DATABASE_DRIVER.": $databasedriver<br>"._DATABASE_TYPE.": " . $ServerInfo["description"] . "<br>"._VERSION.": ".$ServerInfo["version"]."</p>";
				
				include_once "version_check.php";
			}			

		echo "</ul></div>";
		if (!isset($error)) {
			if (!isset($_REQUEST["edit"])) {
				echo "<P><form method=get action=\"install.php\"><input type=hidden name=step value=3><input type=submit value="._CONTINUE."></form>";
			} else {
				echo "<P><form method=get action=\"settings.php\"><input type=submit value="._CONTINUE."></form>";
			}
		}
	} else {

?>
	<div class="indentbody">
	&nbsp;<br>
    
	<form method=get id="dbform" name="dbform" action=settings.php>
    <table cellpadding=6 width=600 border=0>
    <tr><td colspan=3 class=toplineblue bgcolor=#BBDDFF><font size=3><b><?php echo _SETTINGS;?></b></font></td</tr>
    <tr><td width=130 valign=top class=dotline><?php echo _DATABASE_TYPE;?>:</td><td valign=top class=dotline><select name="databasedriver" onChange="dbtype(this.value);">
    <?php
      foreach ($supported_databases as $key => $value) {
          echo "<option value=\"$key\"".($key == $databasedriver?" selected":"").">$value</option>\n";
        }
        
    ?>
    </select></td>
    <td bgcolor=f0f0f0><?php echo _DB_TYPE_TO_USE;?>.</td></tr>
    
    <tr id="dbname"><td width=130 valign=top class=dotline><?php echo _DB_NAME;?>:</td><td valign=top class=dotline><input type=text  size=35  name=DatabaseName value="<?php echo @$DatabaseName; ?>"></td>
    <td bgcolor=f0f0f0><?php echo _DB_NAME_EXPLAIN;?></td></tr>
    <tr id="dbserver"><td width=130 valign=top class=dotline><?php echo _DB_SERVER;?>:</td><td valign=top class=dotline><input type=text name="mysqlserver" id="mysqlserver" size=35 value="<?php echo @$mysqlserver; ?>"></td>
    <td id="serverexplain" bgcolor=f0f0f0><?php echo _DB_SERVER_EXPLAIN;?></td></tr>
    <tr id="dbuname"><td valign=top class=dotline><?php echo _DB_USERNAME;?>:</td><td valign=top class=dotline><input type=text  size=35 name=mysqluname value="<?php echo @$mysqluname; ?>"></td>
    <td bgcolor=f0f0f0><?php echo _DB_USERNAME_EXPLAIN;?></td></tr>
    <tr id="dbpw"><td valign=top class=dotline><?php echo _DB_PASSWORD;?>:</td><td valign=top class=dotline><input  size=35 type=password name=mysqlpw value="<?php if (!empty($debug)) { echo @$mysqlpw; } ?>"></td>
    <td bgcolor=f0f0f0>The database password</td></tr>
    <tr id="dbprefix"><td valign=top class=dotline>MySQL Prefix:</td><td valign=top class=dotline><input size=35 type=text name=mysqlprefix value="<?php echo @$mysqlprefix; ?>"></td>
    <td bgcolor=f0f0f0>A prefix for each table Logaholic uses.</td></tr>
    <tr style='display: none;' id="dbtmp"><td valign=top class=dotline>Directory for Temporary Files:</td><td valign=top class=dotline><input size=35 type=hidden name=mysqltmp value="<?php echo logaholic_dir()."files/"; ?>"></td>
    <td bgcolor=f0f0f0>Enter a valid directory where temporary files will be saved and used.</td></tr>
    <tr><td colspan=3>
    <?php if (isset($_REQUEST["edit"])) { echo "<input type=hidden name=\"edit\" value=1>"; } ?> 
    <input type=hidden name=mysql value=1><input name=mysqlsave type=submit value="Change Settings"></td></tr>
</table>

</form>
	</div>

<?php
        if ($databasedriver=="sqlite") {
            echo "<script language\"javascript\">dbtype('sqlite');</script>";   
        }
	}
} else {


?>
<div class="indentbody">
<table width=600><tr><td>
<?php
if (((!$loginSystemExists) || ($userAuthenticationType == USER_AUTHENTICATION_NONE)) ||  (($validUserRequired) && $session->isAdmin())) {
    echo "<div style=\"width:220px;border:1px solid silver;color:gray;float:right;padding:10px;background-color:#F8F8F8;margin-left:10px;margin-top:20px;\">";
    $lo->PrintInfo();
    echo "<br><a href=getlicense.php>"._GET_LICENSE."</a>";
    echo "</div>"; 
}
?>
<h3>Logaholic <?php echo _GLOBAL_SETTINGS;?></h3>
<?php echo '<P>'._WELCOME_YOU_ARE_USING_LOGAHOLIC.' '._LOGAHOLIC_PRODUCTNAME.' '.LOGAHOLIC_VERSION_NUMBER . ' '; ?>
<?php echo _WITH_PHP_VERSION.' '.phpversion();?> <?php echo _AND_DATABASE.' '.$databasedriver. " "._VERSION." " .$vnumfull. "</P>";?>
<br>
<?php
  /*
  if ($q = @$db->Execute("SELECT table_schema, sum( data_length + index_length ) / 1024 / 1024 \"Data Base Size in MB\" FROM information_schema.TABLES where table_schema='$DatabaseName' GROUP BY table_schema")) {
        $chk=$q->FetchRow();
        echo _SIZE_OF_LOGAHOLIC_DB_IS." ".number_format($chk[1],0)." MB";
    }
    */
?>

<div class="indentbody">
        <a href="profiles.php"><img width=16 height=16 src=images/icons/profiles.gif border=0 align=left> <?php echo _CHANGE_PROFILE_SETTINGS;?></a><P>
		<?php
		// Does the login system exist and is turned off, *or*
		// is this a valid user with admin rights.
		if ((($loginSystemExists) && ($userAuthenticationType == USER_AUTHENTICATION_NONE)) ||  (($validUserRequired) && $session->isAdmin())) {
		?>
		<a href="user_login/admin.php"><img  width=16 height=16 src=images/icons/group_key.gif border=0 align=left> <?php echo _USER_ADMINISTRATION;?></a><P>
		<?php
		}
		if ((!$validUserRequired) || ($session->canEditProfiles())) {
			echo "<a href='get_reports.php'><img width='16' height='16' src='images/icons/report-install.png' border='0' align='left'>"._INSTALL_REPORTS."</a><p>";
		}
		if($_SERVER['HTTP_HOST'] == "localhost"){
			echo "<a href=http://www.logaholic.com><img  width=16 height=16 src=images/icons/home.gif border=0 align=left>"._CHECK_FOR_UPDATES."</a><P>";
        }else{
			echo "<a href='upgrade.php'><img  width=16 height=16 src=images/icons/home.gif border=0 align=left>"._CHECK_FOR_UPGRADE_LOGAHOLIC."</a><P>";
			
		}
        // Does the login system exist and is turned off, *or*
        // is this a valid user with admin rights.
		
        if (((!$loginSystemExists) || ($userAuthenticationType == USER_AUTHENTICATION_NONE)) ||  (($validUserRequired) && $session->isAdmin())) {
        ?>
        <a href="settings.php?mysql=1&edit=1"><img  width=16 height=16 src=images/icons/database.gif border=0 align=left> <?php echo _CHANGE_DATABASE_SETTINGS;?></a><P>
        <a href="settings.php?deleterange=1"><img  width=16 height=16 src=images/icons/delete.gif border=0 align=left> <?php echo _DELETE_RANGE_FROM_DATABASE;?></a><P> 
        <?php 
        if (@$ipencoding==true) {
            $encoding = _DISABLE;
            $confirm = _IP_ENCODING_CONFIRM_DISABLE;
        } else {
            $encoding = _ENABLE;
            $confirm = _IP_ENCODING_CONFIRM_ENABLE;    
        }
        ?>
        <a href="settings.php?toggle_ipencode=1" onclick="return confirm('<?php echo $confirm; ?>');"><img  width=16 height=16 src=images/icons/lock-de.png border=0 align=left> <?php echo "$encoding IP Encoding";?></a><P> 
		
		
		
		<a id="open_change_affiliate" href="#"><img width='16' height='16' src='images/icons/surveys.gif' border='0' align='left'><?php echo _CHANGE_AFFILIATE_ID; ?></a><p>
		<div id="change_affiliate_id" title="<?php echo _CHANGE_AFFILIATE_ID; ?>">
			<P><?php echo _CHANGE_AFFILIATE_ID; ?></p>
			<div id="affiliate_feedback"></div>
			<form id="change_affiliate_form">
				<input type=text name="new_affiliate_id" id="new_affiliate_id" size="60" value="<?php echo getGlobalSetting("affiliate_id"); ?>">
				<button id="save_new_affiliate"><?php echo _SAVE; ?></button>
			</form>
		</div>		
		 <script language="javascript" type="text/javascript">
        $( "#change_affiliate_id" ).dialog({
            autoOpen: false,
            modal: true,
            width:600,
            height:350
        });
		
        $( "#open_change_affiliate" ).click(function() {
            $("#change_affiliate_id").dialog("open");
            return false;
        });
        
        $( "#save_new_affiliate" ).click(function() { 
            $.ajax({
               type: "POST",
               url: "includes/change_affiliate.php",
               data: "new_affiliate_id="+document.getElementById("new_affiliate_id").value,
               success: function(msg){
                 if (msg!="") {
                     document.getElementById("affiliate_feedback").innerHTML = msg;
                 }
               }
             });
             return false;
        });

        </script>
		<a id="open_change_custom_format" href="#"><img width='16' height='16' src='images/icons/date.gif' border='0' align='left'><?php echo "Change Custom Date Format."; ?></a><p>
		<div id="change_custom_format" title="Change Custom Date Format" style='font-size:12px;'>
			<p>Below you can set a default date format for all the reports in every profile.</p>
			<p>You can also set the date fomat individual for each profile in their profile settings bij going to 'edit profile'.</p>
			<?php	
				echo "<form method='POST' action=''>";
					if(!empty($_REQUEST["sumbitDefaultFormat"])){
						$dateFormat = array(
						"format1" => $_REQUEST["format1"]
						,"seperator1" => $_REQUEST["seperator1"]
						,"format2" => $_REQUEST["format2"]
						,"seperator2" => $_REQUEST["seperator2"]
						,"format3" => $_REQUEST["format3"]
						,"seperator3" => $_REQUEST["seperator3"]
						,"format4" => $_REQUEST["format4"]
						);
						SetupCustomDateFormat($dateFormat);
						setGlobalSetting("profileDateFormat",serialize($dateFormat));
					}else{
						$dateFormat = unserialize(getGlobalSetting("profileDateFormat",$dateFormat));
						SetupCustomDateFormat($dateFormat);
					} ?>
					<p class='datepreview'>Date Format Preview: <?php echo date(implode($dateFormat),time()); ?></p>
					<?php
					echo "<br/><input type='submit' value='"._SAVE."' name='sumbitDefaultFormat' />";
				echo "</form>";				
			?>
			<br/>
			<a class="restore-default-date-format-settings" style="cursor:pointer; color:#0000FF; text-decoration:underline;">Restore to Default Format.</a>
		</div>
		 <script language="javascript" type="text/javascript">
		 $(document).ready( function(){
			$( "#change_custom_format" ).dialog({
				autoOpen: false,
				modal: true,
				width:600,
				height:350
			});		
			$( "#open_change_custom_format" ).click(function() {
				$("#change_custom_format").dialog("open");
				return false;
			});
			$(".restore-default-date-format-settings").click( function(){
				$('.format1 option[value=m]').attr('selected', 'selected');
				$('.seperator1 option[value=-]').attr('selected', 'selected');
				$('.format2 option[value=d]').attr('selected', 'selected');
				$('.seperator2 option[value=-]').attr('selected', 'selected');
				$('.format3 option[value=Y]').attr('selected', 'selected');
				$('.seperator3 option').removeAttr('selected');
				$('.format4 option[value=]').attr('selected', 'selected');
				$.ajax({
				  url: 'includes/dateReturner.php',
				  data: {dateString : 'm-d-Y'},
				  success: function(result) {
						$(".datepreview").html("Date Format Preview: "+result);
				  }
				});					
			});
			$("#change_custom_format select").change( function(){
				$.ajax({
				  url: 'includes/dateReturner.php',
				  data: {dateString : $(".format1").val()+$(".seperator1").val()+$(".format2").val()+$(".seperator2").val()+$(".format3").val()+$(".seperator3").val()+$(".format4").val()},
				  success: function(result) {
						$(".datepreview").html("Date Format Preview: "+result);
				  }
				});				
			});	
		});	
        </script>
		
        <?php 
        include "includes/upload_dir.php";
        $upload = new UploadDir();
        $upload->ChangeDir();
        ?>
        <a href="settings.php?uninstall=yes"><img  width=16 height=16 src=images/icons/delete.gif border=0 align=left> <?php echo _UNINSTALL_LOGAHOLIC;?></a><P>
        <?php
        }
        ?>
</div>
<br>
<?php
if (((!$loginSystemExists) || ($userAuthenticationType == USER_AUTHENTICATION_NONE)) ||  (($validUserRequired) && $session->isAdmin())) {
    if (!IS_PHPDOCK) {
        ?>
        <font color=red><b><?php echo _RECOMMENDATIONS;?>:</b></font>
        <P>
        <ol>
		<?php

		echo _DEFAULT_TXT_SETTINGS_PART1."<br /><br />";

        if (file_exists("install.php")===TRUE) {
	        echo _DELETE_INSTALL_FILE;
        }
        echo _DEFAULT_TXT_SETTINGS_PART2;
        ?>
        </ul>
        </ol>
        <?php
        include "checksettings.php"; 
    }
}
?>
</td></tr></table>
</div>
<?php
}
?>

<P> &nbsp;<P>
<div align=center>
<font size=1 color=silver>
<a class=nodec href="credits.php<?php echo "?conf=$conf"; ?>">&copy 2005-<?php echo date('Y');?> Logaholic BV</a><br>
</font></div>
</body>
</html>
