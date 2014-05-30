<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
/**
* @desc This report displays a visitors per day table 
*/

class TwitterCore extends Report {
    	
    function getAcounts(){
		$accounts = getProfileData($this->profile->profilename, $this->profile->profilename.".twitterAccounts", false);
		if($accounts != false){ $accounts = unserialize($accounts); }
		return $accounts;
	}
	function UpdateStats(){
		$accs = $this->getAcounts();
		if($accs){
			foreach($accs as $k => $v){
				$this->account = $v;
				if($this->UpdateTwitterData()){
					//echoNotice("Updated: $v");
				}
			}
		}
	}	
	function UpdateTwitterData(){
		$url = "http://api.twitter.com/1/users/show.json?screen_name={$this->account}";
			
		@$json = file_get_contents($url);
		$json = json_decode($json);
		
		if(empty($json)){
			// echoWarning(_INVALID_TWITTER_LOGIN);
		}
		
		$twitter_data = getProfileData($this->profile->profilename, $this->profile->profilename."twitterFollowers".$this->account, false);
		if($twitter_data !== false) {
			$twitter_data = unserialize($twitter_data);
		}
		
		if($twitter_data == false){
			$yesterday = mktime(0,0,0,date("m",time()),date("d",time())- 1,date("Y",time()) );
			$size = 0;
			$twitter_data[0]["stamp"] = $yesterday;
			$twitter_data[0]["date"] = date("d-m-Y", $yesterday);
			$twitter_data[0]["followers"] = $json->followers_count;
			$twitter_data[0]["following"] = $json->friends_count;
			$twitter_data[0]["statuses"] = $json->statuses_count;			
			setProfileData($this->profile->profilename, $this->profile->profilename."twitterFollowers".$this->account, serialize($twitter_data));
		}else{
			$date = date("d-m-Y", time());
			$size = sizeof($twitter_data);
			$prev = $size - 1;
			if($size != 1 && $twitter_data[$prev]["date"] == $date){ $size = $prev;  }
			$twitter_data[$size]["stamp"] = time();
			$twitter_data[$size]["date"] = date("d-m-Y", time());
			$twitter_data[$size]["followers"] = $json->followers_count;
			$twitter_data[$size]["following"] = $json->friends_count;
			$twitter_data[$size]["statuses"] = $json->statuses_count;			
		}
		
		$last_entry = $size - 2;
		if($last_entry < 0){ $last_entry = 0; }
		if($twitter_data[$last_entry]["followers"] == $json->followers_count && $twitter_data[$last_entry]["following"] == $json->friends_count && $twitter_data[$last_entry]["statuses"] == $json->statuses_count) {
			//echoNotice("not saving");
		} else {
			setProfileData($this->profile->profilename, $this->profile->profilename."twitterFollowers".$this->account, serialize($twitter_data));
		}
		return $twitter_data;
	}
	function DefineReport(){
		echo "<h1>". _ADD_REMOVE_TWITTER ."</h1>";		
		$this->addForm();
		$this->remForm();
	}
	
	function addForm(){
		if(isset($_REQUEST["accountName"])){
			$val = $_REQUEST["accountName"];
		}else{ $val = ""; }
		
		echo "<form action='' method=post>";
			echo "<input type='text' name='accountName' value='$val' />";
			echo "<input type='submit' name='submitBtn' value='Add new account' />";
		echo "</form>";

		if(isset($_REQUEST["accountName"])){
			$this->addAccount($_REQUEST["accountName"]);
		}
	}
	function addAccount($account){
		if(!$this->checkDoubleAccounts($account)){
			$accs = $this->getAcounts();
			if($accs == false){
				$accs[] = $account;
			}else{
				array_push($accs,$account);
			}
			setProfileData($this->conf, $this->conf.".twitterAccounts",serialize($accs));
		}else{
			echo _ACCOUNT_STORED;
		}
	}
	function checkDoubleAccounts($account){
		$accs = $this->getAcounts();
		if(!empty($accs)) {
			foreach($accs as $k => $v){
				if($v == $account){
					return true;
				}
			}
		}
		return false;
	}
	function remForm(){
		if(isset($_REQUEST["rem"])){
			$this->remAccount($_REQUEST["rem"]);
		}
		$accs = $this->getAcounts();
		if(!$accs){
			
		}else{
			echo "<ul class='twitter-remove-list'>";
			foreach($accs as $k => $v){
				echo "<li id='$k'><a href='?conf={$this->conf}&labels={$this->labels}&action=remform&rem=$k'></a> $v</li>";
			}
			echo "</ul>";
		}
	}
	function remAccount($num){
		$accs = $this->getAcounts();
		unset($accs[$num]);
		setProfileData($this->conf, $this->conf.".twitterAccounts",serialize($accs));
	}
	
	# Select/Add/Remove twitter account
	function DisplayCustomForm(){
		$accounts = $this->getAcounts();
		if(!$accounts){
			echo _NO_TWITTER_FOUND . "<br><br>";
		}else{
			echo "<select id='account' class='report_option_field'>";
			foreach($accounts as $k => $v){
				echo "<option value='$v'>$v</option>";
			}
			echo "</select>";
		}
		echo "<a href='definereport.php?conf={$this->conf}&labels={$this->labels}&action=start' target='_blank' class='graylink open_iframe_window'>Add/Remove a Twitter account.</a>";
	}
	function reDate($date) {
		$date = explode(" ",$date);
		$d[0] = $date[2];		
		$d[1] = $this->getMonth($date[1]);
		$d[2] = $date[5];
		return $d;
	}
	function getMonth($date) {
		if($date == "Jan"){ return "01"; }
		if($date == "Feb"){ return "02"; }
		if($date == "Mar"){ return "03"; }
		if($date == "Apr"){ return "04"; }
		if($date == "May"){ return "05"; }
		if($date == "Jun"){ return "06"; }
		if($date == "Jul"){ return "07"; }
		if($date == "Aug"){ return "08"; }
		if($date == "Sep"){ return "09"; }
		if($date == "Oct"){ return "10"; }
		if($date == "Nov"){ return "11"; }
		if($date == "Dec"){ return "12"; }
	}
}
?>
