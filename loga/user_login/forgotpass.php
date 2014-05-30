<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
include_once "../common.inc.php";
include_once "login.inc.php";

$baseurl = dirname(dirname(currentScriptURL() . "a"))."/"; // strip off the current script name *and* user_login path.
//$conf = "Profiles";  // No profile name here, so we need to set one (otherwise we'll be redirected).
$skiploginform = true;
include_once "../top.php";

echo "<div class=\"indentbody\">\n";
/**
 * Forgot Password form has been submitted and no errors
 * were found with the form (the username is in the database)
 */
if(isset($_SESSION['forgotpass'])){
   /**
    * New password was generated for user and sent to user's
    * email address.
    */
   if($_SESSION['forgotpass']){
      echo "<h1>New Password Generated</h1>";
      echo "<p>Your new password has been generated "
          ."and sent to the email <br>associated with your account. ";
   }
   /**
    * Email could not be sent, therefore password was not
    * edited in the database.
    */
   else{
      echo "<h1>New Password Failure</h1>";
      echo "<p>There was an error sending you the "
          ."email with the new password,<br> so your password has not been changed. ";
   }
       
   unset($_SESSION['forgotpass']);
}
else{

/**
 * Forgot password form is displayed, if error found
 * it is displayed.
 */
?>

<?php if ($validUserRequired) {    
?>
<h1>Forgot Password</h1>
A new password will be generated for you and sent to the email address<br>
associated with your account, all you have to do is enter your
username.<br><br>
<?php echo $form->error("login_user"); ?>
<form action="" method="POST">
<b>Username:</b> <input type="text" name="login_user" maxlength="100" value="<?php echo $form->value("login_user"); ?>">
<input type="hidden" name="subforgot" value="1">
<input type="submit" value="Get New Password">
</form>
<?php } else { ?>
  <p>The user authentication system is disabled.  Can't email passwords...</p>
<?php } ?>

<?php
}
?>
</div>
</body>
</html>
