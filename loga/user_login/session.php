<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
include("database.php");
include("mailer.php");
include("form.php");

require_once realpath( dirname(__FILE__) . '/../core_factory.php');

class Session
{
	var $username;     //Username given on sign-up
	var $usersessionid; //Random value generated on current login
	var $isAdminFlag;  // Is this the administrative user?
	var $time;         //Time user was last active (page loaded)
	var $logged_in;    //True if user is logged in, false otherwise
	var $userinfo = array();  //The array holding all user info
	var $cururl;          //The page url current being viewed
	var $referrer;     //Last recorded site page viewed
	var $user_profiles = array();
    var $active; //is the user active
    var $created;
    var $expires;
    var $email;
   
   // options:
   //   none - let everyone in.
	 //   logaholic - Logaholic will prompt for the user / password combination
	 //   webserver - use the Apache or IIS authenticated user (PHP_AUTHUSER, etc).
	 //   other: variablename.  
   
   /**
		* Note: referrer should really only be considered the actual
		* page referrer in process.php, any other time it may be
		* inaccurate.
		*/

	 /* Class constructor */
	 function Session(){
      $this->time = time();
      $this->startSession();
	  //session_write_close();
	 }

   /**
    * startSession - Performs all the actions necessary to 
    * initialize this session object. Tries to determine if the
    * the user has logged in already, and sets the variables 
    * accordingly. Also takes advantage of this page load to
    * update the active visitors tables.
    */
   function startSession(){
      global $database;  //The database connection
			global $debug; // Set the global debug flag based on a session variable here.
			
			//Tell PHP to start the session.  But if it's already been started, then don't raise an error.
            Logaholic_sessionStart();

      // Make sure the user table exists.  If it does, then let's not keep checking it every time, but just
      // when we connect.  This should probably be replaced by a database version number so we can update
      // between versions if necessary.  (we moved this to version check.php !!)
       
            
      /* Determine if user is logged in */
      $this->logged_in = $this->checkLogin();

      /**
       * Set guest value to users not logged in, and update
       * active guests table accordingly.
       */
	   if($this->logged_in == false){
				 $this->username = "";
				 $this->IsAdminFlag = 0;
      }
      /* Update users last active timestamp */
      else{
//         $database->addActiveUser($this->username, $this->time);
      }
      
      /* Remove inactive visitors from database */
//      $database->removeInactiveUsers();
      // $database->removeInactiveGuests();
      
      /* Set referrer page */
	  if(isset($_SESSION['cururl'])){
				 $this->referrer = $_SESSION['cururl'];
      }else{
				 $this->referrer = "/";
      }

      /* Set current url */
	  $this->cururl = $_SESSION['cururl'] = $_SERVER['PHP_SELF'];
	 }

   /**
    * checkLogin - Checks if the user has already previously
    * logged in, and a session with the user has already been
    * established. Also checks to see if user has been remembered.
    * If so, the database is queried to make sure of the user's 
    * authenticity. Returns true if the user has logged in.
    */
   function checkLogin(){
      global $database;  //The database connection
      global $userAuthenticationType;  // This is defined in login.inc.php and defines how we're validating users.
			global $userAuthenticationOther_Var; // If the type is "other", this is the variable we're pulling the from.
			
			$this->logged_in     = false;
			$this->userinfo 	   = "";
			
			logDebugMessage("User authentication mode: " . $userAuthenticationType);
			
			if ($userAuthenticationType  == USER_AUTHENTICATION_WEBSERVER) {
				$this->username = $this->webServerUser();
				if ($this->username) {
					$this->userinfo       = $database->getUserInfo($this->username);
				} else {
					$_SESSION["errormessage_init"] = @$_SESSION["errormessage_init"] . "Logaholic is configured to authenticate using the web server's authenticated user, but the web server isn't providing us that information.";
					$userAuthenticationType = USER_AUTHENTICATION_LOGAHOLIC;
				}
			} elseif ($userAuthenticationType  == USER_AUTHENTICATION_OTHER) {
				// Since PHP's $$ action doesn't work with superglobals, see if we're looking at a superglobal and
				// try to extract the value.
                if (preg_match('/\\$?_?([A-Z]+)\\[\\"(\\w+)\\"\\]/', $userAuthenticationOther_Var, $regs)) {
					switch($regs[1]) {
						case "SERVER": $this->username = $_SERVER[$regs[2]]; break;
						case "ENV": $this->username = $_ENV[$regs[2]]; break;
						case "COOKIE": $this->username = $_COOKIE[$regs[2]]; break;
						case "SESSION": $this->username = $_SESSION[$regs[2]]; break;
						case "REQUEST": $this->username = $_REQUEST[$regs[2]]; break;
					default:
						// Unknown array variable specified.  Tried to evaluate it (just in case it might work on some configs.) 						
                        $this->username = $$userAuthenticationOther_Var;
					}
				} else {
					$this->username = $$userAuthenticationOther_Var;
				}
                
				if ($this->username) {
                    if ($this->userinfo = $database->getUserInfo($this->username)) {
                        if (_LOGAHOLIC_EDITION==4) {
                            //echo md5($_ENV['REMOTE_PASS'])."==".$this->userinfo['password'];
                            $crypted = crypt($this->username . $_ENV['REMOTE_PASSWORD'], $this->userinfo['password']);
                            if ($crypted == $this->userinfo['password']) {
                                // ok
                            } else {
                                $this->userinfo="";
                                $_SESSION["errormessage_init"] = "The password provided to logaholic by the system is invalid.";
                                $userAuthenticationType = USER_AUTHENTICATION_LOGAHOLIC;
                            }   
                        }                        
                    } else {
                        $_SESSION["errormessage_init"] = "The username provided to logaholic by the system is invalid.";
                        $userAuthenticationType = USER_AUTHENTICATION_LOGAHOLIC;
                    }                    
				} else {
					$_SESSION["errormessage_init"] = "$userAuthenticationOther_Var = $this->username<br>";
					$_SESSION["errormessage_init"] = @$_SESSION["errormessage_init"] . "Logaholic is configured to authenticate using the \"".$userAuthenticationOther_Var."\" variable, but it's empty.";
					$userAuthenticationType = USER_AUTHENTICATION_LOGAHOLIC;
				}
			}
			
			if ($userAuthenticationType == USER_AUTHENTICATION_LOGAHOLIC) {
				
				/* Check if user has been remembered */
				if(isset($_COOKIE['logaholic_cookname']) && isset($_COOKIE['logaholic_cookid'])){
					 $this->username = $_SESSION['username'] = $_COOKIE['logaholic_cookname'];
					 $this->usersessionid = $_SESSION['usersessionid']   = $_COOKIE['logaholic_cookid'];
					 logDebugMessage ("Getting user from cookie: " . $this->username);
				}
				
				/* Username and userid have been set and not guest */
				if(isset($_SESSION['username']) && isset($_SESSION['usersessionid'])){
					logDebugMessage ("Username and security ID in session.  Checking user ".$_SESSION['username']);

					 /* Confirm that username and usersessionid are valid */
					 if($database->confirmUserSessionID($_SESSION['username'], $_SESSION['usersessionid']) != 0){
							logDebugMessage ("Session ID doesn't match.");
						 
							/* Variables are incorrect, user not logged in */
							unset($_SESSION['username']);
							unset($_SESSION['usersessionid']);
							return false;
					 }
					 /* User is logged in, set class variables */
					 $this->userinfo       = $database->getUserInfo($_SESSION['username']);
					 logDebugMessage ("Session match, loaded user data: ".$this->userinfo["username"]);
				}
			}
			
			if ($this->userinfo) {
				$this->username       = $this->userinfo['username'];
				$this->usersessionid  = $this->userinfo['usersessionid'];
				$this->isAdminFlag    = $this->userinfo['isAdmin'];
                $this->active   = $this->userinfo['active']; 
                $this->created   = $this->userinfo['created'];
                $this->expires   = $this->userinfo['expires'];
                $this->email   = $this->userinfo['email'];
				$this->user_profiles  = preg_split('/\s*,\s*/', trim($this->userinfo['profiles']));
                $this->logged_in      = true;
            } else {
				$this->logged_in      = false;
            }
	  return $this->logged_in;
   }
   
   
   /**
		* canAccessProfile
		* Does the logged in user have access to the passed in profile
		* name?  Returns true or false.
		* Note: This is a case *insensitive* match, so profiles need to
		* be named accordingly.
		*/
	 function canAccessProfile($profilename) {
		 for ($i = count($this->user_profiles)-1; $i >= 0; $i--) {
			 if (strcasecmp($profilename, $this->user_profiles[$i]) == 0) {
				 return true;
			 }
		 }
		 return false;
	 }

	 /**
		* login - The user has submitted his username and password
		* through the login form, this function checks the authenticity
		* of that information in the database and creates the session.
		* Effectively logging in the user if all goes well.
		*/
	 function login($subuser, $subpass, $subremember){
			global $database, $form, $lgpkey;  //The database and form object

			/* Username error checking */
			//$field = "user";  //Use field name for username
            $field = "login_user";  //Use field name for username
			$form->clearErrors();
			if(!$subuser || strlen($subuser = trim($subuser)) == 0){
                $form->setError($field, "* Username not entered");
            }
			else{
				 /* Check if username is not alphanumeric */
				 if(!eregi("^([@_.0-9a-z-])*$", $subuser)){
            $form->setError($field, "* Username not alphanumeric hier");
            //echo  "Username not alphanumeric";
         }
      }

      /* Return if form errors exist */
			if($form->num_errors > 0){
                //echo "stopped here";
         return false;
      }

      /* Checks that username is in database and password is correct */
	  $subuser = stripslashes($subuser);
      if (@$_REQUEST["lgpkey"]) {
           // if we're doing an lgpkey style login, the passwords is already hashed
            $result = $database->confirmUserPass($subuser, $subpass);
      } else {
            $result = $database->confirmUserPass($subuser, md5($subpass));
      }
      /* Check error codes */
	  if($result == 1){
         $field = "login_user";
         $form->setError($field, "* Username not found");
      }
      else if($result == 2){
         $field = "login_pass";
         if(!$subpass){
           $form->setError($field, "* Password not entered");
         } else {
           $form->setError($field, "* Invalid password");
         }
      }
      
      /* Return if form errors exist */
			if($form->num_errors > 0){
         return false;
			}

      /* Username and password correct, register session variables */
			$this->userinfo      = $database->getUserInfo($subuser);
			$this->username      = $_SESSION['username'] = $this->userinfo['username'];
			$this->usersessionid = $_SESSION['usersessionid'] = $this->generateRandID();
			$this->isAdminFlag   = $this->userinfo['isAdmin'];
            $this->active   = $this->userinfo['active']; 
            $this->created   = $this->userinfo['created'];
            $this->expires   = $this->userinfo['expires'];
            $this->email   = $this->userinfo['email']; 
			$this->user_profiles = preg_split('/\s*,\s*/', trim($this->userinfo['profiles']));
     
      
      /* Insert usersessionid into database and update active users table */
			$database->updateUserField($this->username, "usersessionid", $this->usersessionid);
            $database->updateUserField($this->username, "lastlogin", time());
//      $database->addActiveUser($this->username, $this->time);
      // $database->removeActiveGuest($_SERVER['REMOTE_ADDR']);

      /**
			 * This is the cool part: the user has requested that we remember that
       * he's logged in, so we set two cookies. One to hold his username,
       * and one to hold his random value usersessionid. It expires by the time
       * specified in constants.php. Now, next time he comes to our site, we will
       * log him in automatically, but only if he didn't log out before he left.
			 */
			if($subremember){
				 setcookie("logaholic_cookname", $this->username,      time()+COOKIE_EXPIRE, COOKIE_PATH);
				 setcookie("logaholic_cookid",   $this->usersessionid, time()+COOKIE_EXPIRE, COOKIE_PATH);
			} else {
                 setcookie("logaholic_cookname", $this->username,      0, COOKIE_PATH);
                 setcookie("logaholic_cookid",   $this->usersessionid, 0, COOKIE_PATH);  
            }
			$this->logged_in     = true;

			/* Login completed successfully */
			return true;
	 }
	 
	 // This function is used so we can have alternate authentication systems that
	 // just set an ENV variable and we can load without having to do an actual login.
	 // For example, if we're doing Apache mode authentication, then we probably don't
	 // want to prompt again, but instead just use the Apache verified username.
	 function loginByUserName($username) {
		 $thisUser = getUserInfo($username);
		 if ($thisUser) { // We found a user that matches...
				// login with the username and password that was pulled from the database
				return login($username, $thisUser['password'], false);
		 }
		 return false;
	 }

	 /**
		* logout - Gets called when the user wants to be logged out of the
		* website. It deletes any cookies that were stored on the users
		* computer as a result of him wanting to be remembered, and also
		* unsets session variables and demotes his user level to guest.
		*/
	 function logout(){
			global $database;  //The database connection
			/**
			 * Delete cookies - the time must be in the past,
			 * so just negate what you added when creating the
			 * cookie.
			 */
			if(isset($_COOKIE['logaholic_cookname']) && isset($_COOKIE['logaholic_cookid'])){
				 setcookie("logaholic_cookname", "", time()-COOKIE_EXPIRE, COOKIE_PATH);
				 setcookie("logaholic_cookid",   "", time()-COOKIE_EXPIRE, COOKIE_PATH);
			}

			/* Unset PHP session variables */
			session_start();
			unset($_SESSION['username']);
			unset($_SESSION['usersessionid']);
			session_write_close();
			/* Reflect fact that user has logged out */
			$this->logged_in = false;
			
			/**
			 * Remove from active users table and add to
			 * active guests tables.
			 */
//      $database->removeActiveUser($this->username);
			// $database->addActiveGuest($_SERVER['REMOTE_ADDR'], $this->time);
			
			/* Set user level to guest */
			$this->username  = GUEST_NAME;
			$this->isAdminFlag = 0;
	 }

	 /**
    * editAccount - Attempts to edit the user's account information
    * including the password, which it first makes sure is correct
    * if entered, if so and the new password is in the right
    * format, the change is made. All other fields are changed
    * automatically.
    */
   function editAccount($subcurpass, $subnewpass, $subemail){
      global $database, $form;  //The database and form object
      /* New password entered */
      if($subnewpass){
         /* Current Password error checking */
         $field = "curpass";  //Use field name for current password
         if(!$subcurpass){
            $form->setError($field, "* Current Password not entered");
         }
         else{
            /* Check if password too short or is not alphanumeric */
            $subcurpass = stripslashes($subcurpass);
            if(strlen($subcurpass) < 4 ||
               !eregi("^([@_.0-9a-z-])+$", ($subcurpass = trim($subcurpass)))){
               $form->setError($field, "* Current Password incorrect");
            }
            /* Password entered is incorrect */
            if($database->confirmUserPass($this->username,md5($subcurpass)) != 0){
							 $form->setError($field, "* Current Password incorrect");
            }
         }
         
         /* New Password error checking */
				 $field = "newpass";  //Use field name for new password
         /* Spruce up password and check length*/
         $subpass = stripslashes($subnewpass);
         if(strlen($subnewpass) < 4){
            $form->setError($field, "* New Password too short");
         }
         /* Check if password is not alphanumeric */
         else if(!eregi("^([@_.0-9a-z-])+$", ($subnewpass = trim($subnewpass)))){
            $form->setError($field, "* New Password not alphanumeric");
         }
      }
      /* Change password attempted */
      else if($subcurpass){
         /* New Password error reporting */
         $field = "newpass";  //Use field name for new password
         $form->setError($field, "* New Password not entered");
      }
      
      /* Email error checking */
      $field = "email";  //Use field name for email
      if($subemail && strlen($subemail = trim($subemail)) > 0){
         /* Check if valid email address */
         $regex = "^[_+a-z0-9-]+(\.[_+a-z0-9-]+)*"
								 ."@[a-z0-9-]+(\.[a-z0-9-]{1,})*"
								 ."\.([a-z]{2,}){1}$";
         if(!eregi($regex,$subemail)){
            $form->setError($field, "* Email invalid");
				 }
         $subemail = stripslashes($subemail);
      }
			
      /* Errors exist, have user correct them */
      if($form->num_errors > 0){
         return false;  //Errors with form
      }
      
      /* Update password since there were no errors */
      if($subcurpass && $subnewpass){
         $database->updateUserField($this->username,"password",md5($subnewpass));
      }
      
      /* Change Email */
      if($subemail){
				 $database->updateUserField($this->username,"email",$subemail);
      }
      
      /* Success! */
      return true;
	 }
   
   /**
    * isAdmin - Returns true if currently logged in user is
    * an administrator, false otherwise.
    */
   function isAdmin(){
      return (($this->isAdminFlag == 1) ||
              ($this->username  == ADMIN_NAME));
   }
   
   function webServerUser() {
     // This uses information found here: http://www.php.net/manual/en/features.http-auth.php
     // to find the currently logged in Apache or IIS user.
     if (isset($_SERVER['PHP_AUTH_USER'])) {
       // Apache as a module
       return $_SERVER['PHP_AUTH_USER'];
     } elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
       // IIS
       list($user, $pw) = explode(':', base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));
       return $user;
     } else {
       return; // No username
     }
   }
   
   /**
    * canUpdateLogs
    * Can the logged in user update the log files, or just view?
    */
   function canUpdateLogs() {
     if ((isset($this->userinfo["accessUpdateLogs"])) && ($this->userinfo["accessUpdateLogs"])) {
       return true;
     }
     return false;
   }
   /**
    * canEditProfiles
    * Can the logged in user edit the profile settings, or just view?
    */
   function canEditProfiles() {
     if ((isset($this->userinfo["accessEditProfile"])) && ($this->userinfo["accessEditProfile"])) {
       return true;
     }
     return false;
   }
   
   /**
   * @canAddProfiles
   * Can this user create new Logaholic site profiles?
   */
   function canAddProfiles() {
     if ((isset($this->userinfo["accessAddProfile"])) && ($this->userinfo["accessAddProfile"])) {
			 return true;
     }
     return false;
   }
   
	 
   /**
    * generateRandID - Generates a string made up of randomized
    * letters (lower and upper case) and digits and returns
    * the md5 hash of it to be used as a usersessionid.
    */
   function generateRandID(){
      return md5($this->generateRandStr(16));
   }
   
   /**
    * generateRandStr - Generates a string made up of randomized
    * letters (lower and upper case) and digits, the length
    * is a specified parameter.
    */
   function generateRandStr($length){
      $randstr = "";
      for($i=0; $i<$length; $i++){
         $randnum = mt_rand(0,61);
         if($randnum < 10){
            $randstr .= chr($randnum+48);
         }else if($randnum < 36){
            $randstr .= chr($randnum+55);
         }else{
            $randstr .= chr($randnum+61);
         }
      }
      return $randstr;
   }
};


/**
 * Initialize session object - This must be initialized before
 * the form object because the form uses session variables,
 * which cannot be accessed unless the session has started.
 */
$session = new Session;

/* Initialize form object */
$form = new Form;

?>
