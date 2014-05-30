<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
include_once "common.inc.php";
$template->HTMLheadTag();
$template->BodyStart();

$saved = getProfileData($_REQUEST["conf"], $_REQUEST["conf"].".facebookLogin", false );
if($saved){
	$login = $saved;
}else{
	$login = "";
}
if(isset($_GET["login"])){
	$login = $_GET["login"];
}
?>
<div id="wrap">
	<p>Please insert your Facebook email adress or username and run the check to see if your Login data is correct and working.</p>
	<form onSubmit="return false;">
		<input type="text" name="fb_login" value="<?php echo $login; ?>"/>
		<input type="button" value="Check" OnClick="startCheck(this.form);"/>
	</form>
	<div class='InjectTracker'></div>
</div>
<style>
#wrap{ margin:5px; }
#wrap form { padding:0 0 10px 0; }
.fb-check-true{ color:green; }
.fb-check-false{ color:red; }		
</style>
<script type="text/javascript">
	var login;
	var newLoginData;
	var permList;
	var currentUrl = window.parent.location.href;
	
	function startCheck(submit){		
		$(".InjectTracker").empty();
		login = submit.fb_login.value;
		if(checkInput(login)){		
			saveLoginInput();
		}else{
			$(".InjectTracker").append("<p>Please insert a email adress or username to connect with Facebook.</p>");
		}
	}
	
	function checkInput(input){
		if(input ==""){
			return false;
		}else{
			return true;
		}
	}
	
	function saveLoginInput(){
		$.ajax({
			url: 'includes/facebookhandler.js.php',
			data: {
			conf : '<?php echo $_REQUEST["conf"]; ?>',
			email : login,
			action : 'save_login_input'
			},
			success: function(result) {
				getDataInject();
			}
		});
	}
	
	function getDataInject(){
		var injecturl = "<script type=\"text/javascript\" src=http://facebook.logaholic.com/facebook_get_data.php?login="+login+"></ script>";
		 $("head").append(injecturl);
	}
	
	function FacebookCollect(result,perms) {
			newLoginData = result;
			insertServerData(result);
			permList = perms;
	};
	
	function insertServerData(result){
		$(".InjectTracker").append("PRE STEP: Lets first try to get your Login data from our facebook database on facebook.logaholic.com..");
		$.ajax({
			url: 'includes/facebookhandler.js.php',
			data: {
			conf : '<?php echo $_REQUEST["conf"]; ?>',
			email : login,
			serverdata : result,
			action : 'save_fb_login_data'
			},
			success: function(result) {
				if(result == true) {
					$(".InjectTracker").append("<p class='fb-check-true'><img src='images/icons/accept.png' />We have found and colleted your latest facebook login data.</p>");
					step1(0);
				} else {					
					$(".InjectTracker").append("<p class='fb-check-false'><img src='images/icons/16x16/window-close-2.png' />This profile does not have Facebook data saved for the given email or username. <a href='http://apps.facebook.com/logaholicauth/' target='_blank'>Go to our application</a> to authorize us to your facebook data</p>");
					checkCompleted();
				}
			}
		});
	};
	
	function step1(tekst){
		if(tekst == 0){
			$(".InjectTracker").append("STEP 1: Lets see if you got Login data saved on your Logaholic Profile.");
		}
		$.ajax({
			url: "includes/facebookhandler.js.php",
			data: {
				conf : '<?php echo $_REQUEST["conf"]; ?>',
				email : login,
				action : 'checkloginexists'
			},
			success: function(result) {
				if(result == true) {
					$(".InjectTracker").append("<p class='fb-check-true'><img src='images/icons/accept.png' />We have found login data.</p>");
					step2();
				} else {					
					setFirstGlobalFacebookData();
				}
			}
		});
	};
	
	function setFirstGlobalFacebookData(){
		$.ajax({
			url: 'includes/facebookhandler.js.php',
			data: {
			conf : '<?php echo $_REQUEST["conf"]; ?>',
			email : login,
			serverdata : newLoginData,
			action : 'firstGlobalSettingsSave'
			},
			success: function(result) {
				if(result == true) {
					$(".InjectTracker").append("<p class='fb-check-true'><img src='images/icons/accept.png' />Saved your data to the global settings.</p>");
					step1(1);
				}else{
					checkCompleted();
				}
			}
		});	
	}		
	function step2(){
		$(".InjectTracker").append("STEP 2: Check if the data from the facebook data is the same as those on your profile.");
		$.ajax({
			url: "includes/facebookhandler.js.php",
			data: {
				conf : '<?php echo $_REQUEST["conf"]; ?>',
				email : login,
				action : 'compareData'
			},
			success: function(result) {
				if(result == true) {
					$(".InjectTracker").append("<p class='fb-check-true'><img src='images/icons/accept.png' />The ID and Token given to your login data matches the data given from Facebook.</p>");
					insertPermissionList(permList);
				} else {					
					$(".InjectTracker").append("<p class='fb-check-false'><img src='images/icons/16x16/window-close-2.png' />The ID or Token from your login data does not match those from Facebook. Please wait while we update your login data.</p>");
					updateLogin();
				}
			}
		});
	}
	function insertPermissionList(perms){
		$(".InjectTracker").append("STEP 3: Lets see if your login data permits Logaholic to look into the needed data of your Facebook account.");
		$.ajax({
			url: 'includes/facebookhandler.js.php',
			data: {
			conf : '<?php echo $_REQUEST["conf"]; ?>',
			email : login,
			permissions : perms,
			logdata: newLoginData,
			action : 'check_perms_data'
			},
			success: function(result) {
				// $(".InjectTracker").append(result);
				if(result == true){
					$(".InjectTracker").append("<p class='fb-check-true'><img src='images/icons/accept.png' />Your login data has all the permissions it needs.</p>");
					checkCompleted();
				}else{
					$(".InjectTracker").append("<p class='fb-check-false'><img src='images/icons/16x16/window-close-2.png' />Your login data is missing some permissions.</p>");
					GoToApp();
					checkCompleted();
				}
			}
		});
	}
	function updateLogin(){
		$(".InjectTracker").append("");
		$.ajax({
			url: "includes/facebookhandler.js.php",
			data: {
				conf : '<?php echo $_REQUEST["conf"]; ?>',
				email : login,
				action : 'updateData'
			},
			success: function(result) {
				if(result == true) {
					$(".InjectTracker").append("<p class='fb-check-true'><img src='images/icons/accept.png' />Your login data has been updated.</p>");
					insertPermissionList(permList);
				} else {					
					$(".InjectTracker").append("<p class='fb-check-false'><img src='images/icons/16x16/window-close-2.png' />Something went wrong while updating your login data. Please close this frame and restart this check.</p>");
					checkCompleted();
				}		
			}
		});	
	}
	function deleteFacebookServerData(){
		$.ajax({
			url: "includes/facebookhandler.js.php",
			data: {
				conf : '<?php echo $_REQUEST["conf"]; ?>',
				email : login,
				action : 'deleteFacebookDatabaseData'
			}
		});		
	}
	function checkCompleted(){
		$(".InjectTracker").append("CHECK COMPLETE: Facebook configuration check completed. Please close this dialog.</p>");
		deleteFacebookServerData();
	}
	function GoToApp(){
		autosaveDashboard(function(){}, true);
		currentUrl = php_urlencode(currentUrl);
		login = php_urlencode(login);
		$(".InjectTracker").append("<p>Please go to our <a href='http://apps.facebook.com/logaholicauth/?log="+ login +"&reurl="+ currentUrl +"' target='_parent'>authorization application</a> and authorize us to access your Facebook data.</p>");
	}
</script>