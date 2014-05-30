<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
// $conf = "Profiles";  // No profile name here, so we need to set one (otherwise we'll be redirected).
  
include_once("../common.inc.php");
include_once("session.php");
$baseurl = dirname(dirname(currentScriptURL() . "a"))."/"; // strip off the current script name *and* user_login path.


/**
 * User not an administrator, Show an error message.
 */
if ((!$session->isAdmin()) && ($userAuthenticationType != USER_AUTHENTICATION_NONE)) {
  include_once("../top.php");
  echo "<table width=100%><tr><td bgcolor=pink>"._NO_PERMISSION_FOR_ADMIN_AREA.".</td></tr></table>";
  exit();
}

// Do we need to update anything in the database?  If so, do it before including top since that pushes out a header
// we may not want.

if (isset($_POST["authtype"])) {
  setGlobalSetting("UserAuthenticationType", $_POST["authtype"]);
  setGlobalSetting("UserAuthenticationType_Var", $_POST["authtypevar"]);
  
	$_SESSION['lastaction'] = _USER_AUTHENTICATION_METHOD_CHANGED.".";

  header("location: admin.php");
}

if ((((isset($_GET["action"]) && $_GET["action"]=="edituser") && (isset($_GET["username"]))) || 
    (isset($_GET["action"]) && ($_GET["action"]=="newuser"))) && (isset($_POST["useraction"]) && ($_POST["useraction"] == _SAVE))) {
      
  // Make sure the username is appropriate.
  $form->clearErrors();
  $_SESSION['lastaction'] = "";
  
  $edited_user = $_POST["username"];
  if (!($edited_user > "")) {
    $form->setError("username", _USERNAME_MUST_BE_SPECIFIED.".");
  }
  if (strpos($edited_user, " ")) {
    $form->setError("username", _USERNAME_CANNOT_HAVE_A_SPACE.".");
  }
  
  if (!isset($_POST["isAdmin"])) { $_POST["isAdmin"] = 0; }
  if (!isset($_POST["active"])) { $_POST["active"] = 0; }
  if (!isset($_POST["accessUpdateLogs"])) { $_POST["accessUpdateLogs"] = 0; }
  if (!isset($_POST["accessAddProfile"])) { $_POST["accessAddProfile"] = 0; }
  if (!isset($_POST["accessEditProfile"])) { $_POST["accessEditProfile"] = 0; }
  
  if ($form->num_errors == 0){
    $dbdata["username"] = $edited_user;
    $dbdata["name"] = $_POST["name"];
    $dbdata["isAdmin"] = $_POST["isAdmin"];
    $dbdata["email"] = $_POST["email"];
    $dbdata["profiles"] = $_POST["profiles"];
    $dbdata["active"] = intval($_POST["active"]);
    $dbdata["accessUpdateLogs"] = intval($_POST["accessUpdateLogs"]);
    $dbdata["accessAddProfile"] = intval($_POST["accessAddProfile"]);
    $dbdata["accessEditProfile"] = intval($_POST["accessEditProfile"]);
    
    $cdtmp=explode("/", $_POST["created"]);
    $dbdata["created"]=mktime(12,0,0,$cdtmp[0],$cdtmp[1],substr($cdtmp[2],0,4));
    
    if ($_POST["expires"]!="") {
        $cdtmp=explode("/", $_POST["expires"]);
        $dbdata["expires"]=mktime(12,0,0,$cdtmp[0],$cdtmp[1],substr($cdtmp[2],0,4));
    } else {
        $dbdata["expires"]=0;    
    }
		
	if ($_POST["password"] > "") {
	    $dbdata["password"] = md5($_POST["password"]);
	}
    
	if ($_GET["action"] == "newuser") {
	  // Check and see if this user already exists.
	  if (($db->getOne("SELECT count(*) from ".TBL_USERS." where username = \"" . $db->escape($edited_user) . "\"")) != 0) {
        $form->setError("username", _USER_ALREADY_EXISTS_CANT_DUPLICATE.".");
	  } else {
		$db->AutoExecute(TBL_USERS, $dbdata, "INSERT");
	  }
    } else  {
	    $db->AutoExecute(TBL_USERS, $dbdata, "UPDATE", "username = \"" . $db->escape($_POST["origusername"]) . "\"");
	}
  }
  if ($form->num_errors == 0){
    if ($_GET["action"] == "newuser") {
      $_SESSION['lastaction'] = _NEW_USER_ADDED.".";  
    } else {
      $_SESSION['lastaction'] = _USER." " . $_POST["origusername"] . " "._UPDATED.".";
    }
    header("location: admin.php");
    exit();
  }
} elseif (isset($_GET["action"]) && ($_GET["action"]=="deleteuser") && (isset($_GET["username"])) && (isset($_POST["useraction"]))) {
  if ($_POST["useraction"] == _DELETE) {
    logDebugMessage (_DELETING_USER.": " . $_GET["username"]);
    
    // Delete the user!
    $query = "DELETE from " . TBL_USERS . " WHERE username = \"" . $db->escape($_GET["username"]) . "\"";
    if ($db->Execute($query)) {
      $_SESSION['lastaction'] = $db->Affected_Rows() . " "._USERS_DELETED.".";
    } else {
      $form->setError("delete", _ERROR_DELETING_USER.": ".$db->ErrorMsg());
    }
    header("location: admin.php");
    exit();
  } elseif ($_POST["useraction"] == _CANCEL) {
    $_SESSION['lastaction'] = _USER_NOT_DELETED.".";
    header("location: admin.php");
    exit();
  }
}

$headAddition= "<link type=\"text/css\" rel=\"stylesheet\" href=\"components/jquery/css/tablesorter/style.css\" />".PHP_EOL;
include_once "../top.php";
echo "<div class=\"indentbody\">\n";
 

if (isset($_GET["action"]) && ((($_GET["action"]=="edituser") && (isset($_GET["username"]))) || ($_GET["action"]=="newuser"))) {
  // Either create a new user, or update an existing user.
  
  if ($form->num_errors == 0) {
    if ($_GET["action"]=="newuser") {
      $userdata = array();
      $userdata["username"] = "";
      $userdata["password"] = "";
      $userdata["name"] = "";
      $userdata["email"] = "";
      $userdata["isAdmin"] = 0;
      $userdata["profiles"] = "";
      $userdata["foreignkey"] = "";
      $userdata["active"] = 1;
      $userdata["accessUpdateLogs"] = 0;
      $userdata["accessAddProfile"] = 0;
      $userdata["accessEditProfile"] = 0;
      $userdata["expires"] = 0;
    } else {
      $query = "SELECT * from ".TBL_USERS." where username = \"".$db->escape($_GET["username"])."\"";
      $result = $db->Execute($query) or die(_ERROR_FINDING_USER.": " . $db->ErrorMsg());
      $userdata = $result->FetchRow();
    }
  }
  
  if ($form->num_errors > 0) {
    echo "<p><font color=\"red\">";
    $errors = array_values($form->errors);
    for ($i = 0; $i < $form->num_errors; $i++) {
      echo $errors[$i] . "<br>";
    }
    echo "</font></p>";
    $userdata = $_POST;
  }
  ?>
  
  <form method=post name="edituserform">
  <table cellpadding=6 width=600 border=0>
  <tr><td colspan=3 class=toplineblue bgcolor=#BBDDFF><font size=3><b><?php if ($_GET["action"]=="edituser") { echo "<img width=16 height=16 src=images/icons/user_edit.gif border=0 align=left> "._EDIT_USER; } else { echo "<img width=16 height=16 src=images/icons/user_add.gif border=0 align=left> "._CREATE_NEW_USER; } ?></b></font></td></tr>
  <tr>
    <td width=130 valign="top" class="dotline"><?php echo _LOGIN_USERNAME;?></td>
    <td width=200 valign=top class="dotline"><input size=40 type="text" name="username" value="<?php echo $userdata["username"]; ?>"></td>
    <td width=180 bgcolor="#f0f0f0"><?php echo _LOGIN_USERNAME_EXPLAIN;?></tr>
  </tr>
  <tr>
    <td valign="top" class="dotline"><?php echo _NAME;?>.</td>
    <td valign=top class="dotline"><input size=40 type="text" name="name" value="<?php echo $userdata["name"]; ?>"></td>
    <td bgcolor="#f0f0f0"><?php echo _NAME_EXPLAIN;?>.<br><a href="index.php?lgpkey=<?php echo md5($userdata["username"]) . ":".$userdata["password"];?>">Login link</a></tr>
  </tr>
  <tr>
    <td valign="top" class="dotline"><?php echo _PASSWORD;?></td>
    <td valign=top class="dotline"><input size=25 type="password" name="password" value=""></td>
    <td bgcolor="#f0f0f0"><?php echo _PASSWORD_EXPLAIN;?>.</tr>
  </tr>
  <tr>
    <td valign="top" class="dotline"><?php echo _EMAIL_ADDRESS;?></td>
    <td valign=top class="dotline"><input size=25 maxlength=51 type="text" name="email" value="<?php echo $userdata["email"]; ?>"></td>
    <td bgcolor="#f0f0f0"><?php echo _EMAIL_ADDRESS_EXPLAIN;?>.</tr>
  </tr>
  <tr>
    <td valign="top" class="dotline"><?php echo _PROFILES;?></td>
    <td valign=top class="dotline">
		<textarea cols=30 name="profiles" class='user-profiles-area' style="display:none;"><?php echo str_replace(" ", "", $userdata["profiles"]); ?></textarea>
		<div style="height:200px; overflow:auto;border:1px solid silver;">
		<?php	
			$q = $db->Execute("SELECT profilename FROM ". TBL_PROFILES . " ORDER BY profilename");
			$urlx = $_SERVER["PHP_SELF"]."?".$_SERVER["QUERY_STRING"];
			$p = explode(",",str_replace(" ", "", $userdata["profiles"]));
			while($data = $q->fetchRow()){
				$checked = "";
				if(in_array($data["profilename"],$p)){
					$checked = "checked='checked'";
				}
				echo "<input id='{$data["profilename"]}' type='checkbox' $checked value='{$data["profilename"]}' class='user_profile_settings' /><label for='{$data["profilename"]}'>{$data["profilename"]}</label><br/>";
			}		
		?>
		</div>
		<script type='text/javascript'>
			$(".user_profile_settings").live("click", function(){
				userProfiles = new Array();
				i = 0;
				$(".user_profile_settings:checked").each( function(){ 
					userProfiles[i] = $(this).val();	
					i ++;
				});
				$(".user-profiles-area").html(userProfiles.join(","));			
			});
		</script>
	</td>
    <td bgcolor="#f0f0f0"><?php echo _PROFILES_EXPLAIN;?></tr>
  </tr>
  <tr>
    <td valign="top" class="dotline"><?php echo _ENABLED;?></td>
    <td valign=top class="dotline"><input name="active" type="checkbox" value="1" <?php if ($userdata["active"]) { echo "CHECKED"; } ?>></td>
    <td bgcolor="#f0f0f0"><?php echo _ENABLED_EXPLAIN;?>?</tr>
  </tr>
  <tr>
    <td valign="top" class="dotline"><?php echo _IS_ADMINISTRATOR;?>?</td>
    <td valign=top class="dotline"><input name="isAdmin" type="checkbox" value="1" <?php if ($userdata["isAdmin"]) { echo "CHECKED"; } ?>></td>
    <td bgcolor="#f0f0f0"><?php echo _IS_ADMINISTRATOR_EXPLAIN;?>?</tr>
  </tr>
  <tr>
    <td valign="top" class="dotline"><?php echo _CAN_UPDATE_LOGS;?>?</td>
    <td valign=top class="dotline"><input name="accessUpdateLogs" type="checkbox" value="1" <?php if ($userdata["accessUpdateLogs"]) { echo "checked"; } ?>></td>
    <td bgcolor="#f0f0f0"><?php echo _CAN_UPDATE_LOGS_EXPLAIN;?>?</tr>
  </tr>
  <tr>
    <td valign="top" class="dotline"><?php echo _CAN_ADD_PROFILES;?>?</td>
    <td valign=top class="dotline"><input name="accessAddProfile" type="checkbox" value="1" <?php if ($userdata["accessAddProfile"]) { echo "checked"; } ?>></td>
    <td bgcolor="#f0f0f0"><?php echo _CAN_ADD_PROFILES;?>?</tr>
  </tr>
  <tr>
    <td valign="top" class="dotline"><?php echo _CAN_EDIT_PROFILES;?>?</td>
    <td valign=top class="dotline"><input name="accessEditProfile" type="checkbox" value="1" <?php if ($userdata["accessEditProfile"]) { echo "checked"; } ?>></td>
    <td bgcolor="#f0f0f0"><?php echo _CAN_EDIT_PROFILES_EXPLAIN;?>?</tr>
  </tr>
  <tr>
    <td valign="top" class="dotline"><?php echo _ACCOUNT_CREATED_DATE;?></td>
    <?php if (@$userdata["created"]=="") { $userdata["created"]=time();} ?>
    <td valign=top class="dotline"><input size=25 type="text" name="created" value="<?php echo date("m/d/Y",$userdata["created"]); ?>"></td>
    <td bgcolor="#f0f0f0"><?php echo _ACCOUNT_CREATED_DATE_EXPLAIN;?>.</tr>
  </tr>
  <tr>
    <td valign="top" class="dotline"><?php echo _EXPIRE_DATE;?></td>
    <td valign=top class="dotline"><input size=25 type="text" name="expires" value="<?php if ($userdata["expires"]!='0') { echo date("m/d/Y",$userdata["expires"]); } ?>"></td>
    <td bgcolor="#f0f0f0"><?php echo _EXPIRE_DATE_EXPLAIN;?>.</tr>
  </tr>  
  <tr>
   <td>&nbsp;</td>
   <td><INPUT TYPE=Submit name="useraction" value="<?php echo _SAVE;?>"></td>
   <td>&nbsp;
      <input type="hidden" name="origusername" value="<?php echo $userdata["username"];?>">
   </td>
   </tr>
  </table>
  <?php
} elseif (isset($_GET["action"]) && ($_GET["action"]=="deleteuser") && (isset($_GET["username"]))) {
  ?>
  <form method=post name="deleteuserform">
  <table cellpadding=6 width=600 border=0>
  <tr><td class=toplineblue bgcolor=#BBDDFF><font size=3><b><?php echo _DELETE_USER;?></b></font></td></tr>
  <tr><td>
    <p><?php echo _SURE_TO_DELETE_USER;?> <b><?php echo $_GET["username"]; ?>?</p>
    <p></p>
    <p><input type=Submit name="useraction" value="<?php echo _CANCEL;?>">&nbsp<input type=Submit name="useraction" value="<?php echo _DELETE;?>"></p>
    </td>
  </tr></table>
  <?php
} else {
  
  // Show the current user authentication system, and allow it to be changed.
   $userAuthenticationDescription = "";
   
   // Pull the authentication type from the database, since the database type might be different
   // from the current type (during configuration, for example).
   $db_authtype = getGlobalSetting("UserAuthenticationType", USER_AUTHENTICATION_NONE);
   
   switch ($db_authtype) {
    case USER_AUTHENTICATION_NONE: $userAuthenticationDescription = _AUTH_METH_ALL_USERS_ALLOWED_ALL_PROFILES."."; break;
    case USER_AUTHENTICATION_LOGAHOLIC: $userAuthenticationDescription = _AUTH_METH_PROMPT_FOR_LOGIN_DATA."."; break;
    case USER_AUTHENTICATION_WEBSERVER: 
      $userAuthenticationDescription = _AUTH_METH_APACHE_OR_IIS_LOGIN.".";
      if ($session->webServerUser()) {
        $userAuthenticationDescription .= _SERVER_THINKS_LOGGED_IN_AS." ".$session->webServerUser();
      } else {
        $userAuthenticationDescription .= _SERVER_THINKS_NOT_LOGGED_IN.".";
      }
      break;
    case USER_AUTHENTICATION_OTHER: $userAuthenticationDescription = _AUTH_METH_VARIABLE_NAME.": ".$userAuthenticationOther_Var;
   };
   echo "<img src=./images/icons/key.gif width=16 height=16 align=left>" .$userAuthenticationDescription; 
  function selRadButAuth($constant){
    global $db_authtype;
    echo "\"" . $constant . "\"";
    if ($constant == $db_authtype) { echo " checked"; }
  }
  ?>
  <script language="JavaScript">
    function toggleEditor() {
      if (document.getElementById('autheditor').style.display=="block") {
        document.getElementById('autheditor').style.display="none";
      } else {
        document.getElementById('autheditor').style.display="block";
      }
    }
    function toggleServal() {
      if (document.getElementById('serval').style.display=="block") {
        document.getElementById('serval').style.display="none";
      } else {
        document.getElementById('serval').style.display="block";
      }
    }
  </script>
   <a href="javascript: toggleEditor();"><?php echo _CHANGE;?></a>
  <br>
  <form id="autheditor"  style="display:none;" method="post">  
  <p><?php echo _AUTHENTICATION_METHOD;?>:<br>
  <input type=radio name="authtype" value=<?php echo selRadButAuth(USER_AUTHENTICATION_NONE); ?>><?php echo _NO_AUTHENTICATION;?>.<br>
  <input type=radio name="authtype" value=<?php echo selRadButAuth(USER_AUTHENTICATION_LOGAHOLIC); ?>><?php echo _USE_LOGAHOLIC_LOGIN_DIALOG;?>.<br>
  <input type=radio name="authtype" value=<?php echo selRadButAuth(USER_AUTHENTICATION_WEBSERVER); ?>><?php echo _USE_SERVERS_AUTHENTICATED_USER;?>.<br>
  <input onmousedown="toggleServal();" type=radio name="authtype" value=<?php echo selRadButAuth(USER_AUTHENTICATION_OTHER); ?>><?php echo _USE_A_SERVER_VARIABLE;?>.<br>
  <?php
  if ($db_authtype == USER_AUTHENTICATION_OTHER) {
    echo _USE_THIS_SERVER_VARIABLE;?>: <input type=text name="authtypevar" value="<?php echo htmlentities($userAuthenticationOther_Var); ?>"><br>
    <?php
  } else {
    ?><span id="serval" style="display:none;"><?php echo _USE_THIS_SERVER_VARIABLE;?>: <input type=text name="authtypevar" value="<?php echo htmlentities($userAuthenticationOther_Var); ?>"></span>
    <?php  
  }
  ?>
  <br>&nbsp;&nbsp;&nbsp;&nbsp;<input type=submit value="<?php echo _SUBMIT;?>">
  </p></form>
  <?php
  if($form->num_errors > 0){
   echo "<font size=\"2\" color=\"#ff0000\">";
   $errors = array_values($form->errors);
   for ($i = 0; $i < $form->num_errors; $i++) {
     echo $errors[$i] . "<br>";
   }
   echo "</font><br>";
  }
  
  if (@$_SESSION['lastaction'] > "") {
    echo "<p>".$_SESSION['lastaction']."</p>";
  }
  $_SESSION['lastaction'] = "";
  
  if (defined("_LIMIT_USERS") != 1) {
  ?>
  <P>
  <form action='<?php echo $_SERVER['PHP_SELF']."?page_num=1"; ?><?php echo !empty($_GET['num_pp']) ? "&num_pp={$_GET['num_pp']}" : ""; ?>' method=post>
  <?php echo _SEARCH_USERS_MATCHING;?>: <input type="text" name="search" value="<?php if (isset($_REQUEST['search'])){ echo $_REQUEST['search']; } ?>"> 
  <?php echo _IN_FIELD;?>: 
    <select name="sfield">
        <option value="username"><?php echo _USERNAME;?></option>
        <option value="name"><?php echo _NAME;?></option>
        <option value="email"><?php echo _EMAIL_ADDRESS;?></option>
        <option value="profiles"><?php echo _PROFILES;?></option>
    </select>     
    <input type="submit" value="<?php echo _SEARCH;?>">    
  </form>
  <br>
  <?php } else { echo "<P>"; }?>  
  <table align="left" border="0" cellspacing="0" cellpadding="0">
  <tr><td>
  <?php
    
	if (@$_REQUEST["search"]) {
		$searchq = " WHERE {$_REQUEST['sfield']} LIKE '%{$_REQUEST["search"]}%'";
	} else {
		$searchq = "";
	}
	
	if (defined("_LIMIT_USERS") != 0) {
		$ulimit=" limit 0,"._LIMIT_PROFILES;
		$num_of_items = _LIMIT_PROFILES;
	} else {
		# Pagination config starts here
		$num_of_items = 0; # The total amount of items
		$max_links = 5; # The amount of page numbers to show

		$count_query = "SELECT COUNT(*) FROM ".TBL_USERS." {$searchq}";
		$amount_result = $db->Execute($count_query);
		
		$num_of_items = $amount_result->FetchRow();
		$num_of_items = $num_of_items[0];
		
		if(!empty($_GET['num_pp'])) {
			if($_GET['num_pp'] == 'All') {
				$items_per_page = $num_of_items;
			} else {
				$items_per_page = $_GET['num_pp'];
			}
		} else {
			$items_per_page = 20;
		}

		if(!empty($_GET['page_num'])) {
			$page_num = $_GET['page_num'];
		} else {
			$page_num = 1;
		}

		$ulimit = " LIMIT ".floor(($page_num - 1) * $items_per_page).", ".$items_per_page;
	}
	
	$query  = "SELECT username, name, isAdmin, profiles, active, email, FROM_UNIXTIME(created,'%Y-%m-%d'), FROM_UNIXTIME(expires,'%Y-%m-%d'), FROM_UNIXTIME(lastlogin,'%Y-%m-%d') FROM ".TBL_USERS." {$searchq} ORDER BY username $ulimit";
  $db->SetFetchMode(ADODB_FETCH_NUM);
  $data = $db->GetArray($query);
  $db->SetFetchMode(ADODB_FETCH_BOTH);
  $rn=0;
  while ($rn < count($data)) {
    // get rid of 'inserter' account profile list so it doesn't explode the list
    if ($data[$rn][0]=="inserter") {
        $data[$rn][3]="";
    }
    if ($data[$rn][7]=="1970-01-01") {
        $data[$rn][7]="";
    }
    if ($data[$rn][8]=="1970-01-01") {
        $data[$rn][8]="";
    } 
      
    // Stick in the "commands" section.
    $data[$rn][] = "<a href='user_login/admin.php?action=edituser&username=".$data[$rn][0]."'>"._EDIT."</a> <a href='user_login/admin.php?action=deleteuser&username=".$data[$rn][0]."'>"._DELETE."</a>";
    $rn++;
  }

  $showfields=" "._USERNAME." , "._NAME." , "._ADMIN." , "._PROFILES." , "._ACTIVE." , "._EMAIL_ADDRESS." , "._CREATED." , "._EXPIRE_DATE." , "._LAST_LOGIN." , "._COMMANDS." ";
  $mini = 1;  // Don't put in a summary row, or the From / To date range.
  $labels = "<img width=16 height=16 src=images/icons/group_key.gif border=0 align=left> "._USER_ADMINISTRATION;
  ArrayStatsTable(0, 0,$showfields,$labels,$query,"","");
  /*  ArrayStatsTable(0, 0," Username , Name , Admin , Profiles , Active , Created, Email Address , Commands ","<img width=16 height=16 src=images/icons/group_key.gif border=0 align=left> User Administration",$query,"","") ;*/

?>
  </td></tr>
  <tr><td>&nbsp;
  <?php 
  $q = $db->Execute("select count(*) as users FROM ".TBL_USERS);
  $data = $q->FetchRow();
  if (!defined("_LIMIT_USERS") || _LIMIT_USERS > $data['users'] ) { 
  ?>
  <br><img width=16 height=16 src=images/icons/user_add.gif border=0 align=left> <a href="user_login/admin.php?action=newuser"><?php echo _ADD_A_NEW_USER;?></a></p>
  <?php } else { ?>
  <br><img width=16 height=16 src=images/icons/user_add.gif border=0 align=left> You can't add any more users with your current license. Please upgrade your license to add users.</p>
  <?php } ?>
  </td></tr>
	<?php
	if(!isset($items_per_page)) { $items_per_page = 20; }
	if ($num_of_items > $items_per_page) {
		echo "<tr><td cellspacing='0' cellpadding='0' colspan='5'>";			
			if (defined("_LIMIT_PROFILES") == 0) {
				echo "<div class='profile_pagination'>";
					if(ceil($num_of_items/$items_per_page) > 1) {
						echo pagination($num_of_items, $items_per_page, $page_num, $max_links);
					}
					if(!empty($_GET['page_num'])) {
						$url_extension = "?page_num=1&";
					} else {
						$url_extension = "?";
					}
					
					if(!empty($_REQUEST['search'])) {
						if($url_extension == '?') {
							$url_extension .= "search={$_REQUEST['search']}&sfield={$_REQUEST['sfield']}&";
						} else {
							$url_extension .= "search={$_REQUEST['search']}&sfield={$_REQUEST['sfield']}&";
						}
					}
					echo "<script type='text/javascript'>$(document).ready(function() { $(\"#num_pp\").change(function() { window.location = \"{$_SERVER['PHP_SELF']}{$url_extension}num_pp=\" + $(this).val() }); });</script>";
					echo "<div class='select_num_per_page'>";
						echo "<label for='pagination_num_per_page'>Number of users per page: </label>";
						echo "<select id='num_pp' name='num_pp'>";
							echo "<option "; if($items_per_page == 5) { echo "selected "; } echo "value='5'>5</option>";
							echo "<option "; if($items_per_page == 10) { echo "selected "; } echo "value='10'>10</option>";
							echo "<option "; if($items_per_page == 20) { echo "selected "; } echo "value='20'>20</option>";
							echo "<option "; if($items_per_page == 50) { echo "selected "; } echo "value='50'>50</option>";
							echo "<option "; if($items_per_page == 100) { echo "selected "; } echo "value='100'>100</option>";
							echo "<option "; if($items_per_page == $num_of_items) { echo "selected "; } echo "value='All'>All</option>";
						echo "</select>";
					echo "</div>";
				echo "</div>";
			}
		echo "</td></tr>";
	}
	?>
  </table>
  </div>
  </div>
<?php 
} ?>
</body>
</html>
<?php


/* Pagination functions starts here */
function pagination_link($page_number) {
	$pagination_url = $_SERVER['PHP_SELF'].'?page_num='.$page_number;
	if(!empty($_GET['num_pp'])) {
		$pagination_url .= "&num_pp={$_GET['num_pp']}";
	}
	return $pagination_url;
}

function pagination($number_of_items, $items_pp, $page_number, $max_links) {
	$total_pages = ceil($number_of_items/$items_pp);
	if($page_number) {
		if($page_number > 1) { 
			$prev = "<a href='".pagination_link(($page_number -1 ))."'>&lt; Previous</a>"; 
			$first = "<a href='".pagination_link(1)."'>&lt;&lt; First Page</a>"; 
		} else {
			$prev = "<span>&lt; Previous</span>";
			$first = "<span>&lt;&lt; First Page</span>";
		}
	}
	
	if(!empty($_REQUEST['search'])) {
		$url_extension = "&search={$_REQUEST['search']}&sfield={$_REQUEST['sfield']}";
	} else {
		$url_extension = "";
	}
	
	if($page_number < $total_pages) {
		$next = "<a href='".pagination_link(($page_number + 1)).$url_extension."'>Next &gt;</a>"; 
		$last = "<a href='".pagination_link($total_pages).$url_extension."'>Last Page &gt;&gt;</a>";
	} else {
		$next = "<span>Next &gt;</span>";
		$last = "<span>Last Page &gt;&gt;</span>";
	}
	echo $first;
	echo $prev;
	$loop = 0;
	if($page_number >= $max_links) {
		$page_counter = ceil($page_number - ($max_links-1));
	} else {
		$page_counter = 1;
	}
	if($total_pages < $max_links){
		$max_links = $total_pages;
	}
	do{ 
		if($page_counter == $page_number) {
			echo "<strong>{$page_counter}</strong>"; 
		} else {
			echo "<a href='".pagination_link(($page_counter))."'>{$page_counter}</a>";
		} 
		$page_counter++; $current_page=($page_counter+1);
		$loop++;
	} while ($max_links > $loop);
	echo $next;
	echo $last;
}
?>
