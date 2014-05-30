<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
if (isset($edit) || isset($new)) {
    if ($validUserRequired && @$session->canEditProfiles()===false) {
        echo "<div class=indentbody>";
        echoWarning(_NO_PERMISSION_EDIT_PROFILE);
        echo "<a class=\"extrabuttons ui-state-default ui-corner-all\" href=\"javascript:history.back();\">Go Back</a>";
        echo "</div>";
        
        return;
    }    
	if (isset($new)) {
		// Find our current temp file path.
		$real_path = realpath("index.php");
		$path = dirname($real_path);
		$label=_CREATE_NEW_PROFILE;
		//if (isset($_SERVER['SERVER_ADDR'])) { echo "<img src=\"http://www.logaholic.com/docs/check4updates.php?ip=".$_SERVER['SERVER_ADDR']."&site=".$_SERVER['SERVER_NAME']."\" width=0 height=0>"; }
	} else {
		$label=_EDIT_PROFILE;
	}
	?>
	<div class=indentbody>
	<script language="JavaScript" type="text/javascript">
		function tracktoggle() {
			if (document.getElementById('inst').style.display=="block") {
				document.getElementById('inst').style.display="none";
				document.getElementById('codesp').style.display="block";
			} else {
				document.getElementById('inst').style.display="block";
				document.getElementById('codesp').style.display="none";
			}
		}
		
    function trackerclickon() {
       document.getElementById('trackersettings').style.display="block";
       document.getElementById('restsettings').style.display="block";
       document.getElementById('logfilesettings').style.display="none";
       document.getElementById('filefiltersettings').style.display="none";
       document.getElementById('dataexplain').style.display="none";
       document.getElementById('ftpsettings').style.display="none";
    }
    
    function fcookietracker(trackermode) {
        if (trackermode!=1) {
           if (document.getElementById('profileform').visitoridentmethod.value !='3') {
            document.getElementById('cookietracker').style.display="none";
           } else {
            document.getElementById('cookietracker').style.display="block";
           }     
        }
                 
    }   
    
    
    function ftpclick() {
       document.getElementById('trackersettings').style.display="none";
       document.getElementById('ftpsettings').style.display="block";
       document.getElementById('restsettings').style.display="block";
       document.getElementById('filefiltersettings').style.display="block";
       document.getElementById('logfilesettings').style.display="none";
       document.getElementById('dataexplain').style.display="none";
    }
    
    function trackerclickoff() {
       document.getElementById('trackersettings').style.display="none";
       document.getElementById('logfilesettings').style.display="block";
       document.getElementById('restsettings').style.display="block";
       document.getElementById('filefiltersettings').style.display="block";
       document.getElementById('dataexplain').style.display="none";
       document.getElementById('ftpsettings').style.display="none";
    }
    
    function oldtrackerclick() {
			if (self.document.forms.profileform.logfilefullpath.disabled==true) {
				self.document.forms.profileform.logfilefullpath.disabled=false;
				self.document.forms.profileform.splitlogs.disabled=false;
				self.document.forms.profileform.splitfilter.disabled=false;
				self.document.forms.profileform.codespace.disabled=true;
				
				self.document.forms.profileform.skipfiles.disabled=false;
				self.document.forms.profileform.skipfiles.value='.gif, .jpg, .jpeg, .xml, .css, .txt, .ico, .js, .png, .dll, .txt, logaholic/';
				
			} else {
				//when tracker is selected
				self.document.forms.profileform.logfilefullpath.disabled=true;
				self.document.forms.profileform.splitlogs.disabled=true;
				self.document.forms.profileform.splitfilter.disabled=true;
				self.document.forms.profileform.codespace.disabled=false;
				
				self.document.forms.profileform.skipfiles.value='/logaholic.php';
				self.document.forms.profileform.skipfiles.disabled=true;
				
				
			}
			tracktoggle();
		}
		
		// Don't allow special characters to be entered into form.
		function noSpecialChars(e)
		{
			var keynum
			var iChars = " !@#$%^&*()+=-[]\\\';,./{}|\":<>?"; // Invalid characters.
			
			if	(window.event) { keynum = e.keyCode; } // IE
			else if (e.which) { keynum = e.which; } // Netscape/Firefox/Opera
			
			// if (document.getElementById('mysqltablename').style.display=="none") {
			 // document.getElementById('profileform').tablename.value=document.getElementById('profileform').newconfname.value;
			// }
			
			return (iChars.indexOf(String.fromCharCode(keynum)) == -1);
		}
    
    function Profile(sel) {
      document.getElementById('general_li').className="page_item";
      document.getElementById('data_li').className="page_item";
      document.getElementById('performance_li').className="page_item";
      document.getElementById('automation_li').className="page_item";
      document.getElementById('advanced_li').className="page_item";
      document.getElementById('dynamicpages_li').className="page_item";
      document.getElementById('feeds_li').className="page_item";
      // document.getElementById('socialmedia_li').className="page_item";
      document.getElementById(sel).className="current_page_item";
      self.document.forms.profileform.tab.value=sel;
      
    }
   
    function ProfileC(sel) {
      document.getElementById('general').className="tabcenter";
      document.getElementById('data').className="tabcenter";
      document.getElementById('performance').className="tabcenter";
      document.getElementById('automation').className="tabcenter";
      document.getElementById('advanced').className="tabcenter"; 
      document.getElementById('dynamicpages').className="tabcenter";
      document.getElementById('feeds').className="tabcenter";
      // document.getElementById('socialmedia').className="tabcenter";
      document.getElementById(sel).className="selectedtabcenterbold";
  
    }
    
    function ProfileForm(sel) {
      document.getElementById('generalfp').style.display="none";
      document.getElementById('datafp').style.display="none";
      document.getElementById('performancefp').style.display="none";
      document.getElementById('automationfp').style.display="none";
      document.getElementById('advancedfp').style.display="none";
      document.getElementById('dynamicpagesfp').style.display="none";
      document.getElementById('feedsfp').style.display="none"; 
      // document.getElementById('socialmediafp').style.display="none"; 
      document.getElementById(sel).style.display="block";
    }
    
    function showmysqltablename() {
      if (document.getElementById('mysqltablename').style.display=="none") {
          if (navigator.userAgent.indexOf("MSIE") != -1) {
              document.getElementById('mysqltablename').style.display="block";
          } else {
              document.getElementById('mysqltablename').style.display="table-row";
          }
      } else {
        document.getElementById('mysqltablename').style.display="none";
      }
    }
    
    	
	</script>
	<?php
	
	$real_path = realpath("index.php");
	$rpath = dirname($real_path);
	
	if (isset($saved)) {
		echo $saved;
	}
	if (!@$step) {
        ?>
        <img src="images/icons/edit.gif" alt="Edit" width=16 height=16 align=left border=0 title="<?php echo $editprofile->structure_version;?>"><font size=3><b><?php echo $label; ?>: <font color=red><?php if (!isset($new)) { echo $editprofile->profilename; }?></font></b></font>
        <?php
    }
	?>
	<br><br>
	<div class=menubar style="width:98%;margin:0px;">
    <ul id="menu">
    <li class=current_page_item id="general_li" onclick="Profile('general_li');ProfileC('general');ProfileForm('generalfp');"><a><span id="general" class=selectedtabcenterbold><?php echo _GENERAL_SETTINGS;?></span></a></li>
    
    <li class=page_item id="data_li" onclick="Profile('data_li');ProfileC('data');ProfileForm('datafp');"><a><span id="data" class=tabcenter><?php echo _DATA_COLLECTION;?></span></a></li>
    
    <li class=page_item id="performance_li" onclick="Profile('performance_li');ProfileC('performance');ProfileForm('performancefp');"><a><span id="performance" class=tabcenter><?php echo _KPIS;?></span></a></li>
    
    <li class=page_item id="feeds_li" onclick="Profile('feeds_li');ProfileC('feeds');ProfileForm('feedsfp');"><a><span id="feeds" class=tabcenter>Feeds</span></a></li>  
  
    <li class=page_item id="dynamicpages_li" onclick="Profile('dynamicpages_li');ProfileC('dynamicpages');ProfileForm('dynamicpagesfp');"><a><span id="dynamicpages" class=tabcenter><?php echo _DYNAMIC_PAGES;?></span></a></li>
    <?php
     
    ?><li class=page_item id="automation_li" onclick="Profile('automation_li');ProfileC('automation');ProfileForm('automationfp');"><a><span id="automation" class=tabcenter><?php echo _AUTOMATION;?></span></a></li>
    <?php
    
    ?>
    <li class=page_item id="advanced_li" onclick="Profile('advanced_li');ProfileC('advanced');ProfileForm('advancedfp');"><a><span id="advanced" class=tabcenter><?php echo _ADVANCED;?></span></a></li>
	
	
    
  		</ul>
    </div>
      
	<form method="post" id="profileform" <?php if (isset($install) && ($install == 1)) { echo ' action="profiles.php"'; } ?>>    
    <input type=hidden name=tab value="<?php echo @$tab;?>">
  <div id="generalfp" class=editprofile style="float:left;">
	<br>	
	<table cellpadding=6 cellspacing=5 border=0 width="95%">
	<tr><td colspan=3 class=dotline><?php echo _ENTER_GENERAL_SETUP_INFO;?>:<br>&nbsp;</td></tr>
	<tr><td width=130 valign=top class="dotline"><?php echo _PROFILE_NAME;?>:</td><td valign=top class="dotline">
	<?php if (!$validUserRequired || $session->canAddProfiles()) { ?>
		<input size=40 onkeypress="return noSpecialChars(event)" onblur="return noSpecialChars(event)" type=text name="newconfname" value="<?php echo $editprofile->profilename; ?>">
	<?php } else { 
		echo "<input type=hidden name=newconfname value=\"$editprofile->profilename\">$editprofile->profilename";
	} ?>
	</td>
	<td class=dotlinegraytext>
	<input size=40 type=hidden name=tablename value="<?php echo $editprofile->tablename; ?>">
	<?php echo _NAME_THIS_PROFILE; 
	//if (!$validUserRequired || $session->canAddProfiles()) { echo _MYSQL_SEPERATE_NAME;} 
	?></td></tr>
	
	<?php 
	/*
    if (!$validUserRequired || $session->canAddProfiles()) { 
      if (($editprofile->tablename)==($editprofile->profilename)) {
        $mstyle="display:none;";
      } else {
        //$mstyle="display:block;";
      }
    ?>
	<tr id="mysqltablename" style="<?php echo $mstyle; ?>"><td valign=top width=130 class="dotline">Mysql Table Name:</td><td valign=top class="dotline">
	<input size=40 onkeypress="return noSpecialChars(event)" type=text name=tablename value="<?php echo $editprofile->tablename; ?>">
	</td>
	<td class=dotlinegraytext><?php echo _NAME_OF_MYSQL_TABLE;?></td></tr>
	<?php } 
	*/
	?>
	
	
	<tr><td valign=top class="dotline"><?php echo _DOMAIN_NAME;?>:</td><td valign=top class="dotline"><input size=40 type=text name=confdomain value="<?php  echo $editprofile->confdomain;  ?>"></td>
	<td class=dotlinegraytext><?php echo _DOMAIN_NAME_EXPLAIN;?></td></tr>
	
	<tr><td valign=top class="dotline"><?php echo _EQUIVALENT_SUBDOMAINS;?>:</td><td valign=top class="dotline"><input size=40 type=text name=equivdomains value="<?php  echo $editprofile->equivdomains;  ?>"><br><?php echo _EQUIVALENT_SUBDOMAINS_EXPLAIN;?></tr>

	<tr><td valign=top class="dotline"><?php echo _DEFAULT_FILE;?>:</td><td valign=top class="dotline">
  	<input size=40 type=text name=defaultfile value="<?php echo $editprofile->defaultfile; ?>">
  	</td>
  	<?php echo _DEFAULT_FILE_EXPLAIN;?>	
	<tr><td colspan=3><input type=hidden name=install value="<?php echo $install; ?>"><input type=button value="Next" onclick="Profile('data_li');ProfileC('data');ProfileForm('datafp');">
    <?php
    if (@$new==1) {
        echo "<input name=save type=submit value=\"Save\" disabled>";
    } else {
       echo "<input name=save type=submit value=\"Save\">";    
    }
    ?>
    </td></tr>
  </table>
</div>  


<div id="datafp" class=editprofile style="display:none;float:left;">
  <br>
  <?php if (!$validUserRequired || $session->canAddProfiles()) { ?>
  <table cellpadding=6 border=0 width="95%">
    <tr id="dataexplain"><td valign=top><font color=gray><?php echo _CHOOSE_HOW_TO_COLLECT;?></font></td></tr>
  	<tr><td class="dotline"><?php echo _DATA_COLLECTION_METHOD;?>: &nbsp;&nbsp;&nbsp;
    <?php
    if (!IS_PHPDOCK) {
        ?>
        <input type=radio onclick="trackerclickon();" name="trackermode" id="trackermode" value="1" <?php if ($editprofile->trackermode==1) { echo "checked"; } ?>> <?php echo _JS_BASED_TRACKING.' '._OR;
    }    
    ?>
    <input type=radio onclick="trackerclickoff();" name="trackermode" id="trackermode" value="0" <?php if (!isset($new) && $editprofile->trackermode==0) { echo "checked"; } ?>> <?php echo _LOG_ANALYSIS_LOCAL.' '._OR;?>
    <input type=radio onclick="ftpclick();" name="trackermode" id="trackermode" value="2" <?php if (!isset($new) && $editprofile->trackermode==2) { echo "checked"; } ?>> <?php echo _LOG_ANALYSIS_FTP;?></td></tr></table><br>
    <?php } else { echo "<span id='dataexplain'><input type=hidden name=\"trackermode\" value=\"{$editprofile->trackermode}\"></span>";} ?>
  
  	<div id="logfilesettings" style="display:none;">
  	<table cellpadding=6 border=0 width="95%">
    <?php if (!$validUserRequired || $session->canAddProfiles()) { ?>
  	<tr><td valign=top class="dotline" width="110">
            <?php echo _LOG_FILE_LOCATION;?>:<br />
        </td><td valign=top width="390" class="dotline">
        <input size=40 onkeyup="showHint(this.value)" onfocus="showHint(this.value)" type=text name="logfilefullpath" id="logfilefullpath" value="<?php echo $editprofile->logfilefullpath; ?>">  
        <button id="open_uploadfiles" style="margin-left:20px;"><?php echo "Upload files"; ?></button><p>
        <strong>Hint:</strong><br>
        <div id="txtHint" style='max-height: 250px; overflow: auto;border: 1px solid #e0e0e0; padding:2px;'> <?php echo _FULL_ABSOLUTE_PATH;?>: <font color=green><?php echo logaholic_dir();?></font><br><?php echo _LOGFILE_USUALLY_FEW_DIRS_BACK;?></div>
        </td>
        <td class=dotlinegraytext valign=top><?php echo _FULL_ABSOLUTE_PATH_EXPLAIN;?></td>
  	</tr>  	
	<?php
      	include_once "logparser.inc.php";
	?>
	<tr>
		<td><?php echo _LOG_PARSER_TYPE; ?>:</td>
		<td><select name='log_parser_type'><?php
			$logparsertype = getProfileData($editprofile->profilename,$editprofile->profilename.".logparsertype" ,false);
			
			if($logparsertype != false && $logparsertype != "auto"){
				echo "<option value='$logparsertype'>$logparsertype</option>"; 
			}
			echo "<option value='auto'>". _AUTO_DETECT ."</option>"; 		
			foreach($log_parser_types as $type){
				if($type["ClassName"] != $logparsertype){
					echo "<option value='". $type["ClassName"] ."'>" . $type["ClassName"] . "</option>";
				}
			}	

		?></select></td>
		<td class=dotlinegraytext valign=top><?php echo _LOG_PARSER_TYPE_DESC; ?></td>
	</tr>
	<?php
    } else {
        echo "<tr><td colspan=3 class=dotline><input type=hidden name=\"splitlogs\" value=\"$editprofile->splitlogs\"><input type=hidden name=\"recursive\" value=\"$editprofile->recursive\"></td></tr>";    
    
    } 
	?>
	</table> 
  	</div>    
    
    <div id="trackersettings" style="display:none;float:left">  
    <table cellpadding=6 border=0 width="95%">
        <?php   
        echo "<tr><td colspan=3 class=dotline>"._PLACE_JS_TO_COLLECT."<br><br></td></tr>";
		?>
    <tr>
      <td width="110" valign=top class="dotline"><?php echo _COPY_TRACKING_CODE;?></td>
      <td width="390" class="dotline">
      <?php

        $w_path = $_SERVER['PHP_SELF'];
        $wpath = dirname($w_path);
        
        $codespace="<!-- /* Logaholic Web Analytics Code */ -->
<script type=\"text/javascript\">
var lwa_id = \"$editprofile->tablename\";
if (document.location.protocol==\"https:\") { var ptcl = \"https:\" } else { var ptcl = \"http:\" } 
var lwa_server = ptcl + \"//$_SERVER[HTTP_HOST]$wpath/\";    
document.write(unescape(\"%3Cscript type='text/javascript' src='\" + lwa_server + \"lwa.js'%3E%3C/script%3E\"));
</script>
<script type=\"text/javascript\">
var lwa = trackPage();
</script>";
        $codespace.="<noscript><a href=\"http://$_SERVER[HTTP_HOST]$wpath/logaholictracker.php?conf=$editprofile->tablename\"><img src=\"http://$_SERVER[HTTP_HOST]$wpath/logaholictracker.php?conf=$editprofile->tablename\" alt=\"web stats\" border=\"0\" /></a> <a href=\"http://www.logaholic.com/\">Web Analytics</a> by Logaholic</noscript>";

        
        if ($conf=="newcnf") {
            $codespace=_SAVE_THIS_PROFILE_FIRST;
        }
      ?>
        <textarea wrap=off cols=50 rows=10 name=codespace onClick="javascript:this.form.codespace.focus();this.form.codespace.select();"><?php echo $codespace; ?></textarea><br><font size=1><?php echo _CLICK_TO_SELECT_ALL_CTRLC_TO_COPY;?></font><br><br>
       
      </td>
      <td class=dotlinegraytext valign=top> <?php echo _COPY_CODE_EXPLAIN;?>
        </td>
      </tr>
      </table>
      </div>
    
    
    <div id="ftpsettings" style="display:none;">
    <?php
    include "includes/upload_dir.php";
    $ud = new UploadDir();
    if ($ud->isUploadDirSafe()==false) {
        echo "<div style=\"padding:5px;\">";
        echoWarning($ud->SecurityWarning());
        echo "</div>";        
    }
    ?>
      <table cellpadding=6 border=0 width="95%">
      <?php if (!$validUserRequired || $session->canAddProfiles()) { ?>
      <tr><td valign=top width="110" class="dotline"><?php echo _FTP_SERVER_NAME;?>:</td><td valign=top width="390" class="dotline">
      <input size=40 type=text name="ftpserver" id="ftpserver" value="<?php echo $editprofile->ftpserver; ?>" onblur="showHint('')"><p>
      </td>
      <td class=dotlinegraytext valign=top><?php echo str_replace("%a",$ud->getUploadDir().$editprofile->profilename, _FTP_SERVER_NAME_EXPLAIN);?>
      </td>
      </tr>
      <tr><td valign=top  width="110" class="dotline"><?php echo _FTP_USER_NAME;?>:</td><td valign=top  width="390" class="dotline">
      <input size=40 type=text name="ftpuser" id="ftpuser" value="<?php echo $editprofile->ftpuser; ?>" onblur="showHint('')"><p>
      </td>
      <td class=dotlinegraytext valign=top><?php echo _FTP_USER_NAME_EXPLAIN;?><p>
      </td>
      </tr>
      <tr><td valign=top  width="110" class="dotline"><?php echo _FTP_PASSWORD;?>:</td><td valign=top  width="390" class="dotline">
      <input size=40 type=password name="ftppasswd" id="ftppasswd" value="<?php echo $editprofile->ftppasswd; ?>" onblur="showHint('')"><p>
      </td>
      <td class=dotlinegraytext valign=top><?php echo _FTP_PASSWORD_EXPLAIN;?><p>
      </td>
      </tr>
      <tr><td valign=top  width="110" class="dotline"><?php echo _FTP_LOG_LOCATION;?>:</td><td valign=top  width="390" class="dotline">
      <input size=40 onkeyup="showHint(this.value)" onfocus="showHint(this.value)" type=text name="ftpfullpath" id="ftpfullpath" value="<?php echo $editprofile->ftpfullpath; ?>"><p>
      <div id="ftpHint" style='max-height: 250px; overflow: auto;border: 1px solid #e0e0e0; padding:2px;'></div>      
      </td>
      <td class=dotlinegraytext valign=top><?php echo _FTP_LOG_LOCATION_EXPLAIN;?>
      </td>
      </tr>
      <?php } 
		include_once "logparser.inc.php";
	  ?>
	  <tr>
		<td><?php echo _LOG_PARSER_TYPE; ?>:</td>
		<td><select name='log_parser_type'><?php
			$logparsertype = getProfileData($editprofile->profilename,$editprofile->profilename.".logparsertype" ,false);
			
			if($logparsertype != false && $logparsertype != "auto"){
				echo "<option value='$logparsertype'>$logparsertype</option>"; 
			}
			echo "<option value='auto'>". _AUTO_DETECT ."</option>"; 		
			foreach($log_parser_types as $type){
				if($type["ClassName"] != $logparsertype){
					echo "<option value='". $type["ClassName"] ."'>" . $type["ClassName"] . "</option>";
				}
			}	
		
		?></select></td>
		<td class=dotlinegraytext valign=top><?php echo _LOG_PARSER_TYPE_DESC; ?></td>
	  </tr>
    </table> 
      </div>

      
    <div id="filefiltersettings" style="display:none;float:left">
    <?php if (!$validUserRequired || $session->canAddProfiles()) { ?>  
    <table cellpadding=6 border=0 width="95%">
    <tr><td valign=top width="110" class="dotline"><?php echo _MULTIPLE_LOGFILES; ?>:</td><td valign=top width="390" class="dotline"><!--<input id="splitlogsCheckbox" type=checkbox name="splitlogs" value="1" <?php //if ($editprofile->splitlogs==1) { //echo "checked"; } ?>> <?php //echo _LOGS_ARE_SPLIT;?><p>-->
    <input type=checkbox name="recursive" value="1" <?php if ($editprofile->recursive==1) { echo "checked"; } ?>> <?php echo _RECURSIVELY_SEARCH_DIRS;?><br>
      <P><?php echo _FILTER_OPTIONAL_ONLY_ANALYZE_MATCH;?>:<br><input size=20 type=text name="splitfilter" id="splitfilter" value="<?php echo $editprofile->splitfilter; ?>" onkeyup="showHint('')"> <?php echo _BUT_NOT;?><br>
      <input size=20 type=text name="splitfilternegative" id="splitfilternegative" value="<?php echo $editprofile->splitfilternegative; ?>" onkeyup="showHint('')"><br>
      <font size=1><br>&nbsp;</font></td>
      <td class=dotlinegraytext valign=top><?php echo _ONLY_ANALYZE_MATCH_CAUTION;?> </td>
    </tr></table>
    <?php } ?>
    </div>
    
  	<div id="restsettings" style="display:none;float:left;">
  	<table cellpadding=6 border=0 width="95%">
    <tr><td valign=top width="110" class="dotline"><?php echo _SKIP_IPNUMBERS;?>:</td><td valign=top width="390" class="dotline"><textarea rows=5 cols=50 name=skipips><?php echo $editprofile->skipips; ?></textarea></td>
    <td class=dotlinegraytext><?php echo _SKIP_IP_NUMBERS_EXPLAIN;?> <a href="profiles.php?editconf=<?php echo $editconf; ?>&amp;del=9&amp;fldname=ipnumber"><?php echo _CLICK_HERE;?></a></td></tr>
    
    <tr><td valign=top width="110" class="dotline"><?php echo _SKIP_FILES;?>:</td><td valign=top width="390" class="dotline"><textarea rows=5 cols=50 name=skipfiles><?php echo $editprofile->skipfiles; ?></textarea><p></td>
    <td class=dotlinegraytext><?php echo _SKIP_FILES_EXPLAIN;?> <a href="profiles.php?editconf=<?php echo $editconf; ?>&amp;del=9&amp;fldname=page"><?php echo _CLICK_HERE;?></a></td></tr>
        
    <tr><td valign=top width="110" class="dotline"><?php echo _STRIP_URL_PARAMS;?>:</td><td valign=top width="390" class="dotline"><textarea rows=5 cols=50 name=urlparamfilter><?php echo $editprofile->urlparamfilter; ?></textarea><br>
    <input name="urlparamfiltermode" type=radio value="Exclude" <?php if ($editprofile->urlparamfiltermode=="Exclude") {echo "checked";}?>> <?php echo _EXCLUDE_RECOMMENDED;?>
    <br><input name="urlparamfiltermode" type=radio value="Include" <?php if ($editprofile->urlparamfiltermode=="Include") {echo "checked";}?>> <?php echo _INCLUDE_DANGEROUS;?> 
    </td>
    <td class=dotlinegraytext><?php echo _STRIP_URL_PARAMS_EXPLAIN;?></td></tr>
 
    <tr><td valign=top width="110" class="dotline"><?php echo _GOOGLE_PARAMETERS;?>:</td><td valign=top width="390" class="dotline"><textarea rows=2 cols=50 name="googleparams"><?php echo $editprofile->googleparams; ?></textarea><p></td>
    <td class=dotlinegraytext><?php echo _GOOGLE_PARAMETERS_EXPLAIN;?> </td></tr>

	<tr><td colspan=3><input type=button value="Next" onclick="Profile('performance_li');ProfileC('performance');ProfileForm('performancefp');">
    <input name=save type=submit value="Save">
    </td></tr>
    </table>
    </div>
    
    
  	
    <script language="javascript" type="text/javascript">
  	<?php 
    if (!isset($new)) {
      if ($editprofile->trackermode!=1) { 
        echo "trackerclickoff();";
        if ($editprofile->trackermode==2) { 
            echo "ftpclick();";
        } 
      } else {
        echo "trackerclickon();";
      }
    }
    ?>
  	</script>

</div>


  <div id="performancefp" class=editprofile style="display:none;float:left;">
    <br>
    <table cellpadding=6 border=0 width="95%">
    <tr><td colspan=3 class=dotline><?php echo _KPI_COMPLETE_INFO;?>:<br>&nbsp;</td></tr>
    <tr><td valign=top class="dotline"><?php echo _TARGET_PAGES;?>:</td><td valign=top class="dotline"><textarea cols=40 rows=10 name=targetfiles><?php echo $editprofile->targetfiles; ?></textarea></td>
  	<td class=dotlinegraytext valign=top><?php echo _TARGET_PAGES_EXPLAIN;?> <a href="http://www.logaholic.com/help/85" target=_blank><?php echo _READ_THIS;?></a>.</td></tr>
    <tr><td colspan=3><input type=button value="Next" onclick="Profile('feeds_li');ProfileC('feeds');ProfileForm('feedsfp');">
    <input name=save type=submit value="Save"></td></tr>    
    </table>
  </div>
  
  
  <div id="automationfp" class=editprofile style="display:none;float:left;">
  <br>
  <table cellpadding=6 border=0 width="95%">
  <tr><td colspan=3  class="dotline">
  <b><?php echo _AUTOMATE_UPDATING_OF_STATS;?></b><br><br>
  
  <?php
  if (!$validUserRequired || $session->isAdmin()) {
  ?>
  <?php echo _AUTOMATE_UPDATING_OF_STATS_1;?> <a href="http://www.logaholic.com/manual/LogaholicManual/AutomatingUpdates" target=_blank><b><?php echo _IN_THE_MANUAL;?></b></a>.
  <?php echo _EXAMPLE_OF_CRON_JOB; ?> 
 
  <p style="padding:8px;"><span style="margin-left:30px;border:1px solid silver;padding:8px;"><?php 
  $w_path = $_SERVER['PHP_SELF'];
  $wpath = dirname($w_path);
  echo "<b>*/60 * * * * wget -q -O /dev/null \"http://$_SERVER[HTTP_HOST]$wpath/update.php?conf=$editprofile->profilename\"</b>";
  ?>
  </span></p>
  <P><?php echo _MULTIPLE_WAYS_TO_AUTOMATE_1;?> <a href="http://www.logaholic.com/manual/LogaholicManual/AutomatingUpdates" target=_blank><?php echo _IN_THE_MANUAL;?></a> <?php echo _MULTIPLE_WAYS_TO_AUTOMATE_2;?>.</p>
  <?php
  } else { echo "Automated updating is handled by the system admin."; }  
  ?>
  </td></tr><tr><td colspan=3  class="dotline">
  <p><strong><?php echo _EMAIL_ALERTS; ?></strong></p>
  <?php echo _EMAIL_ALERTS_EXPLAIN; ?>  
  
  <button id="open_emailalerts" style="margin-left:30px;"><?php echo _MANAGE_EMAIL_ALERTS; ?></button>
  
  <P><?php echo _EMAIL_ALERTS_EXPLAIN_2; ?></p>  
  
  <div id="emailalerts" title="<?php echo _MANAGE_EMAIL_ALERTS; ?>" style="margin:0px;padding:0px;"><iframe id="emailalerts_iframe" width="100%" height="100%" scrolling="auto" marginWidth="0" marginHeight="0" frameBorder="0"></iframe></div>

  </td></tr>
  <tr><td colspan=3><input type=button value="Next" onclick="Profile('advanced_li');ProfileC('advanced');ProfileForm('advancedfp');">
  <input name=save type=submit value="Save"></td></tr>
  </table>
  </div>
  
  
  <div id="advancedfp" class=editprofile style="display:none;float:left;">	
    <br><table cellpadding=6 border=0 width="95%">
    <tr><td colspan=3 class=dotline><?php echo _OPTIONAL_ADV_SETTINGS;?><br>&nbsp;</td></tr>
    
    <?php
	if (_LOGAHOLIC_EDITION == 4 || LOGAHOLIC_BASE_EDITION == "cPanel Edition") {
		?>
		<tr><td valign=top class="dotline"><?php echo "Backup Logaholic Data";?>:</td><td valign=top class="dotline">
		<input size=40 type=radio name=includebackup value="1" <?php if ($editprofile->includebackup) { echo "checked"; } ?>> <?php echo _RC_ON;?> <input size=40 type=radio name=includebackup value="0" <?php if (!$editprofile->includebackup) { echo "checked"; } ?>> <?php echo _RC_OFF;?>  
		</td>
		<td class=dotlinegraytext width="35%"><?php echo "Select 'On' if you want to include the data from this profile in your cPanel Backup.";?></td></tr>
  		<?php
	}
	
	?> 	
	
	<tr><td valign=top class="dotline"><?php echo _REPORT_CACHE;?>:</td><td valign=top class="dotline">
  	<input size=40 type=radio name=caching value="1" <?php if ($editprofile->usepagecache) { echo "checked"; } ?>> <?php echo _RC_ON;?> <input size=40 type=radio name=caching value="0" <?php if (!$editprofile->usepagecache) { echo "checked"; } ?>> <?php echo _RC_OFF;?>  
  	</td>
  	<td class=dotlinegraytext width="35%"><?php echo _REPORT_CACHE_EXPLAIN;?></td></tr>
  	
  	<tr><td valign=top class="dotline"><?php echo _ANIMATE_FLASH_GRAPHS;?>:</td><td valign=top class="dotline">
  	<input size=40 type=radio name=animate value="0" <?php if (!$editprofile->animate) { echo "checked"; } ?>> <?php echo _RC_ON;?> <input size=40 type=radio name=animate value="1" <?php if ($editprofile->animate) { echo "checked"; } ?>> <?php echo _RC_OFF;?> 
  	</td>
  	<td class=dotlinegraytext width="35%"><?php echo _ANIMATE_FLASH_GRAPHS_EXPLAIN;?></td></tr>
  	
  
    <?php    
    if (function_exists("date_default_timezone_get")) {
        ?>
        <tr><td valign=top class="dotline"><?php echo _REPORTING_TIMEZONE; ?>:</td><td valign=top class="dotline">
        <select name="timezone">
        <?php
        if (!$editprofile->timezone) { $editprofile->timezone = date_default_timezone_get(); }        
        $zones = file('includes/timezones.txt');
        foreach ($zones as $zone) {
            $zone=trim($zone);
            echo "<option value=\"$zone\" ".($editprofile->timezone == $zone ? 'SELECTED' : '').">$zone</option>\n";            
        }
        ?>
        </select>
        </td>
        <td class=dotlinegraytext width="35%"><?php echo _THE_DEAULT_TIMEZONE_ON_THIS_SYSTEM_IS." ". $system_timezone .". ". _TIMEZONE_CHANGE_EXPLAIN; ?></td></tr>
        
        <?php
    }
    ?>
    <tr><td valign=top class="dotline"><?php echo _TIMEZONE_CORRECTION;?>:</td><td valign=top class="dotline">
    <input size=5 type=text name=timezonecorrection value="<?php echo $editprofile->timezonecorrection; ?>">
    </td>
    <td class=dotlinegraytext width="35%"><?php echo _TIMEZONE_CORRECTION_EXPLAIN;?></td></tr>    
    <tr>
		<td>Time format display:</td>
		<td class="date-format-settings">
			<?php SetupCustomDateFormat($editprofile->dateFormat); ?>
		</td>
		<td class=dotlinegraytext width="35%">Choose the format of how you want to view the dates in the reports.<br/> In the preview below the selectors you can see the format that is set. You can also restore the settings to default.</td>
  	</tr>
	<tr><td>Date Format Preview:</td><td><p class="date-format-settings-preview"><?php echo date(implode($editprofile->dateFormat),time()); ?></p>
	<a class="restore-default-date-format-settings" style="cursor:pointer; color:#0000FF; text-decoration:underline;">Restore to Default Format.</a></td><td></td>
    <tr><td valign=top class="dotline"><?php echo _VISIT_TIMEOUT;?>:</td><td valign=top class="dotline">
      <input size=5 type=text name=visittimeout value="<?php echo $editprofile->visittimeout; ?>">
      </td>
      <td class=dotlinegraytext width="35%"><?php echo _VISIT_TIMEOUT_EXPLAIN;?></td></tr>
	
	<?php if (_LOGAHOLIC_EDITION != 4 && LOGAHOLIC_BASE_EDITION != "cPanel Edition") { 
		$hidevid = "";
	} else {
		$hidevid = "display:none;";
	}
	?>	
    <tr style="<?php echo $hidevid; ?>"><td valign=top class="dotline"><?php echo _UNIQUE_VISITOR_ID_METHOD;?>:</td><td valign=top class="dotline"><select onchange="fcookietracker('<?php echo $editprofile->trackermode;?>');" name="visitoridentmethod"><?php
        foreach ($vidmethods as $key => $value) {
            echo "<option value=\"$key\" ".($key == $editprofile->visitoridentmethod ? "selected" : "").">$value</option>";
            }
      echo "</select>";  
      ?>
      
      
      </td>
    <td class=dotlinegraytext><?php echo _HOW_TO_IDENTIFY_UNIQUE_VISITOR;?> <a target =_new href="http://www.logaholic.com/manual/LogaholicManual/Advanced"><?php echo _THIS_HELP_ARTICLE;?></a>.
    </td></tr>
    <tr>
    <td colspan=3>
      <div id="cookietracker" class="dotline" style="padding-left:50px;display:<?php if ($editprofile->visitoridentmethod!=3 || $editprofile->trackermode==1) { echo "none"; } else { echo "block"; }?>;">
      <?php echo _IMPORTANT_COOKIE_BASED;?><br>
          
            
	   <?php
	   $codespace = str_replace("var lwa_id", "var lwa_trackermode = $editprofile->trackermode;\nvar lwa_id", $codespace);
	   ?>
	   <textarea wrap=off cols=50 rows=10 name=codespace2 onClick="javascript:this.form.codespace2.focus();this.form.codespace2.select();"><?php echo $codespace; ?></textarea><br><font size=1><?php echo _CLICK_TO_SELECT_ALL_CTRLC_TO_COPY;?></font><br><br> 
	   <?php echo _COPY_CODE_EXPLAIN;?>
	   <P>
       <?php echoWarning("Finally, read <a target =_new href=\"http://www.logaholic.com/manual/LogaholicManual/LogFileAnalysisHybridMode\">". _THIS_HELP_ARTICLE."</a> "._MORE_INFO_ON_COOKIE_TRACKING.""); ?>
       
       </div>
    
    </td></tr>
    <?php
     if (!$validUserRequired || $session->isAdmin()) { 
        if (!IS_PHPDOCK) {
        ?>
        <tr>
			<td valign="top" class="dotline"><?php echo _EXIT_CLICK_TRACKING;?></td>
			<td valign="top" class="dotline"><textarea id='exitclicktracker' style='width: 450px;' onClick="javascript:this.form.exitclicktracker.focus();this.form.exitclicktracker.select();">
<?php 
$w_path = $_SERVER['PHP_SELF'];
$wpath = dirname($w_path);
$trackmode = $editprofile->trackermode == 1 ? 'js' : 'log';

echo "<script type='text/javascript'>LWA_tracking = '{$trackmode}';</script><script type='text/javascript'>if (document.location.protocol==\"https:\") { var ptcl = \"https:\" } else { var ptcl = \"http:\" } 
var lwa_server = ptcl + \"//{$_SERVER['HTTP_HOST']}{$wpath}/\";
document.write(unescape(\"%3Cscript type='text/javascript' src='\" + lwa_server + \"exitclicks.js'%3E%3C/script%3E\"));</script>";?></textarea></td>
			<td class="dotlinegraytext"><?php echo _EXIT_CLICK_TRACKING_EXPLAIN;?>.</td>
		</tr>
        <?php
        }
     }
    ?>
  
  <tr><td colspan=3><input name=save type=submit value="Save"></td></tr>
  </table>  	
 
  	
  </div>

  <div id="dynamicpagesfp" class=editprofile style="display:none;">
  <br>
  <table cellpadding=6 border=0 width="95%">
  <?php if (defined("_ENABLE_IMPORTANT_PARAMETER_EDITOR_")) { ?>
           <tr><td valign=top class="dotline"><?php echo _DYNAMIC_PAGES;?>:</td><td valign=top class="dotline">
      
      
          <input type=hidden name="importantparampagecount" value=<?php echo count($editprofile->importantURLParams)+1 ?>>
    <table><tr><th width="30%" align="left"><?php echo _PAGE_NAME_WITH_PATH;?></th><th align="left" width="70%"><?php echo _IMPORTANT_PARAMS_COMMA_DELIMITED;?></th><th align="left"><?php echo _DELETE;?>?</th></tr>
    <?php
        // Use a for loop here so we can grab an empty one at the end.
        for ($param_loop = 0; $param_loop < $editprofile->getUrlParamCount()+1 ; $param_loop ++) {
            if ($param_loop < $editprofile->getUrlParamCount()) {
                $this_param =& $editprofile->getUrlParamByIndex($param_loop);
            } else {
                $this_param = array("paramid" => null, "filename" => null, "nameisregex" => null, "importantparams" => null);
            }
            echo "<tr><td><input type=hidden name=\"paramid_{$param_loop}\" value=\"".$this_param["paramid"]."\"><input name=\"paramurl_{$param_loop}\" value=\"".addslashes($this_param["filename"])."\" size=40></td><td><input name=\"paramnames_{$param_loop}\" value=\"".addslashes($this_param["importantparams"])."\" size=50></td><td align=\"center\">";
            if ($this_param["paramid"]) {
                echo "<input type=CHECKBOX name=\"DeleteParam_{$param_loop}\">";
            } else {
                echo '<img src="images/new_record.gif" border="0" width="35" height="20" alt="new_record.gif">';
            }
            echo "</td></tr>\n";
        }
    ?>
    </table>
  <P><b><?php echo _DYNAMIC_PAGES_CONF;?>:</b></p>
  <font color="gray">  
         <?php echo _DYNAMIC_PAGES_CONF_EXPLAIN;?>
    </font>
    </td></tr>

    
    <?php } ?>
  
  <tr><td colspan=3><input type=button value="Next" onclick="Profile('automation_li');ProfileC('automation');ProfileForm('automationfp');">
  <input name=save type=submit value="Save"></td></tr>
  </table>
  </div>
  
  
  

  <div id="feedsfp" class=editprofile style="display:none;">
  <br>
  <table cellpadding=6 border=0 width="95%">
    <tr><td valign=top class="dotline" colspan="3"><?php echo _FEED_SETTINGS; ?><br>&nbsp;</td></tr>
    <tr><td valign=top class="dotline" width="110"><?php echo _FEEDS_DIRECTORY; ?>:</td><td valign=top class="dotline">
        <input type=text size="40" name="feedurl" value="<?php echo $editprofile->feedurl; ?>">
    </td>
    <td class=dotlinegraytext><?php echo _FEEDS_DIRECTORY_EXPLAIN; ?> 
    </td></tr>
    <tr><td valign=top class="dotline"><?php echo _FEEDBURNER_URI; ?>:</td><td valign=top class="dotline">
        <input type=text size="40" name="feedburneruri" value="<?php echo $editprofile->feedburneruri; ?>">
    </td>
    <td class=dotlinegraytext><?php echo _FEEDBURNER_URI_EXPLAIN; ?> 
    </td></tr>
  
  <tr><td colspan=3><input type=button value="Next" onclick="Profile('dynamicpages_li');ProfileC('dynamicpages');ProfileForm('dynamicpagesfp');">
  <input name=save type=submit value="Save"></td></tr>
  </table>
  </div>  
</form> 
</div>






<script language="javascript" type="text/javascript">
<?php
/* if we need to open a tab, do it now */
if (@$tab!="") {
    echo "Profile(".$tab."_li);ProfileC(".$tab.");ProfileForm(".$tab."fp);";  
}
if ((!$validUserRequired || $session->canAddProfiles())) {
    ?>
    // give showHint a kick
    $(document).ready(function(){
        showHint("");
    });
    <?php
} 
?>
$(function() {
    $( "#uploadfiles" ).dialog({
        autoOpen: false,
        modal: true,
        width:640,
        height:480
    });

    $( "#open_uploadfiles" ).click(function() {
        <?php 
			ob_start();
            $updir = $ud->getUploadDir() . $editprofile->profilename ."/";
			ob_end_clean();
            echo "document.getElementById(\"logfilefullpath\").value = \"$updir\";";     
        ?>
        showHint(document.getElementById("logfilefullpath").value); 
        url = "upload.php?conf=<?php echo $editprofile->profilename; ?>";
        $("#uploadfiles").dialog("open");
        $("#uploadfiles_iframe").attr('src',url);
        return false;
    });
});
$(function() {
    $( "#emailalerts" ).dialog({
        autoOpen: false,
        modal: true,
        width:600,
        height:500
    });

    $( "#open_emailalerts" ).click(function() {
        url = "emailalerts.php?conf=<?php echo $editprofile->profilename; ?>";
        $("#emailalerts").dialog("open");
        $("#emailalerts_iframe").attr('src',url);
        return false;
    });
	
	 // $( ".facebookconf" ).dialog({
        // autoOpen: false,
        // modal: true,
        // width:600,
        // height:500
    // });

    // $( "#open_facebookconf" ).click(function() {
        // url = "facebook_conf_iframe.php?conf=<?php echo $editprofile->profilename . "&email=" . $editprofile->facebooklogin; ?>";
        // $(".facebookconf").dialog("open");
        // $(".facebookconf_iframe").attr('src',url);
        // return false;
    // });
});
</script>
<div id="uploadfiles" title="<?php echo _UPLOAD_LOG_FILES_IFRAME_HEADER; ?>" style="margin:0px;padding:10px;"><iframe id="uploadfiles_iframe" width="100%" height="100%" scrolling="auto" marginWidth="0" marginHeight="0" frameBorder="0"></iframe></div>

<P style="clear:both;height:50px;">&nbsp;</P>
<?php
}
?>
