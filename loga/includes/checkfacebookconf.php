<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
Class checkFacebookConf {
	
	var $conf;
	var $login;
	
	// To use this class we need login data and the name of your profile.
	function __CONSTRUCT($conf, $login){
		$this->conf = $conf;
		$this->login = $login;
	}
	
	// Save the login data to your profile
	function saveLogin(){
		setProfileData($this->conf, $this->conf.".facebookLogin",$this->login);
		echo true;
	}
	
	// Check if the current profile has login data saved
	function CheckProfileCurrentLogin(){
		$data = unserialize(getProfileData($this->conf, $this->conf."facebookApi".$this->login , false ));
		if($data == false){			
			echo false;
		}else{
			echo true;
		}
	}
	
	// Compares if the ids of both data retrieves matches. this should always return true.
	function compareID(){
		$data1 = unserialize(getProfileData($this->conf, $this->conf."facebookApi".$this->login , false ));
		$data2 = unserialize(getProfileData($this->conf, $this->conf.".FacebookServerApi.".$this->login , false ));
		if($data1["id"] == $data2["id"]){
			return true;
		}else{
			return false;
		}
	}
	
	// compares if the tokens matches this can be different because the user can have different permissions accepted.
	function compareTOKEN(){
		$data1 = unserialize(getProfileData($this->conf, $this->conf."facebookApi".$this->login , false ));
		$data2 = unserialize(getProfileData($this->conf, $this->conf.".FacebookServerApi.".$this->login , false ));
		if($data1["token"] == $data2["token"]){
			return true;
		}else{
			return false;
		}
	}	
	function compareLoginData(){
		// return true or false;
		if($this->compareID()){
			if($this->compareTOKEN()){
				echo true;
			}else{
				echo false;
			}
		}else{
			echo false;
		}
	}
	
	// Saves data to your profile for the first time if this hasnt been done before
	function firstGlobalSettingsSave($data){
		$data = json_decode($data);
		$save["id"] = $data->userid;	
		$save["token"] = $data->token;
		$save = serialize($save);	
		setProfileData($this->conf, $this->conf."facebookApi".$this->login,$save);
		if(getProfileData($this->conf, $this->conf."facebookApi".$this->login , false ) == false){
			echo false;
		}else{
			echo true;
		}
	}
	
	
	function updateLoginData(){
		// update data
		$data1 = getProfileData($this->conf, $this->conf."facebookApi".$this->login , false );
		$data2 = getProfileData($this->conf, $this->conf.".FacebookServerApi.".$this->login , false );
		$data1 = $data2;
		setProfileData($this->conf, $this->conf."facebookApi".$this->login,$data1);
		echo true;
	}
	function deleteFacebookDatabaseData(){
		deleteProfileData($this->conf, $this->conf.".FacebookServerApi.".$this->login);
		echo true;
	}
	function CheckPermissons($perms){
		$perms = json_decode($perms);
		$errors = 0;
		$permarray = array();
		if(!is_array($perms->data)) {
			echo false;
			return;
		}
		
		foreach($perms->data as $key => $val){
			foreach($val as $k => $v){
				if($v != 1){			
					$errors ++;
				}
			}
		}
		if($errors == 0){
			echo true;
		}else{
			echo false;
		}		
	}
}
?>