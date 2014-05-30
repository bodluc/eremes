<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
 
include_once("session.php");

class Process
{
   /* Class constructor */
   function Process(){
      global $session,$db;
      /* User submitted login form */
	  if(isset($_REQUEST['sublogin'])){
         $this->procLogin();
      }
      /* User submitted registration form */
      else if(isset($_POST['subjoin'])){
         $this->procRegister();
      }
      /* User submitted forgot password form */
      else if(isset($_POST['subforgot'])){
         $this->procForgotPass();
      }
      /* User submitted edit account form */
      else if(isset($_POST['subedit'])){
         $this->procEditAccount();
      }
      /* User entered with login key from foreign system */
      // This will allow people to log in on the fly from other systems (for SPE versoins)
      // lgpkey should consist of 2 md5 hashes, one for username and one for password, seperated by a colon
      else if (isset($_REQUEST['lgpkey'])) {
         $lgpkey = explode(":",$_REQUEST['lgpkey']);
         $q = "SELECT username FROM ".TBL_USERS." WHERE MD5(username) = '$lgpkey[0]'";
         $result = $db->Execute($q) or die("Error locating user: " . $db->ErrorMsg());
         $data= $result->FetchRow();
         $_REQUEST['login_user'] = $data["username"];
         $_REQUEST['login_pass'] = $lgpkey[1];
         $_REQUEST['login_remember'] = 1;
         $this->procLogin();           
      }
   }

   /**
    * procLogin - Processes the user submitted login form, if errors
    * are found, the user is redirected to correct the information,
    * if not, the user is effectively logged in to the system.
    */
   function procLogin(){
      global $session, $form;
      /* Login attempt */
      $retval = $session->login($_REQUEST['login_user'], $_REQUEST['login_pass'], isset($_REQUEST['login_remember']));
			
      /* Login successful */
      if($retval){
      }
      /* Login failed */
      else{
         $_SESSION['value_array'] = $_POST;
         $_SESSION['error_array'] = $form->getErrorArray();
      }
   }
   
   /**
    * procLogout - Simply attempts to log the user out of the system
    * given that there is no logout form to process.
    */
   function procLogout(){
      global $session;
      $retval = $session->logout();
      $_SESSION['value_array'] = "";
   }
   
   /**
    * procForgotPass - Validates the given username then if
    * everything is fine, a new password is generated and
    * emailed to the address the user gave on sign up.
    */
   function procForgotPass(){
        global $database, $session, $mailer, $form;
        /* Username error checking */
        $subuser = $_REQUEST['login_user'];
        $field = "login_user";  //Use field name for username
        if(!$subuser || strlen($subuser = trim($subuser)) == 0){
            $form->setError($field, "* Username not entered<br>");
        } else {
            /* Make sure username is in database */
            $subuser = stripslashes($subuser);
            if(strlen($subuser) < 5 || strlen($subuser) > 100 || !eregi("^([@-_.0-9a-z])+$", $subuser) || (!$database->usernameTaken($subuser))) {
                $form->setError($field, "* Username does not exist<br>");
            }
            /* Get email of user */
            $usrinf = $database->getUserInfo($subuser);
            $email  = $usrinf['email'];
            if ($email=="") {
                $form->setError($field, "* There is no email address associated with your account<br>");        
            }
        }
        /* Errors exist, have user correct them */
        if($form->num_errors > 0){
            $_SESSION['value_array'] = $_POST;
            $_SESSION['error_array'] = $form->getErrorArray();
        } else {
            /* Generate new password */
            $newpass = $session->generateRandStr(8);

            /* Attempt to send the email with new password */
            if($mailer->sendNewPass($subuser,$email,$newpass)){
                /* Email sent, update database */
                $database->updateUserField($subuser, "password", md5($newpass));
                $_SESSION['forgotpass'] = true;
            } else {
                /* Email failure, do not change password */
                $_SESSION['forgotpass'] = false;
            }
        }
   }
	 
   /**
    * procEditAccount - Attempts to edit the user's account
    * information, including the password, which must be verified
    * before a change is made.
    */
   function procEditAccount(){
      global $session, $form;
      /* Account edit attempt */
      $retval = $session->editAccount($_POST['curpass'], $_POST['newpass'], $_POST['email']);

      /* Account edit successful */
      if($retval){
         $_SESSION['useredit'] = true;
         header("Location: ".$session->referrer);
      }
      /* Error found with form */
      else{
         $_SESSION['value_array'] = $_POST;
         $_SESSION['error_array'] = $form->getErrorArray();
         header("Location: ".$session->referrer);
      }
   }
};

?>
