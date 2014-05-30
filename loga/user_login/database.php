<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
include("constants.php");
      
class MySQLDB
{
   var $connection;         //The MySQL database connection
   var $num_members;        //Number of signed-up users
   /* Note: call getNumMembers() to access $num_members! */
   
   /* Class constructor */
   function MySQLDB(){
      /* Make connection to database */
//      $this->connection = mysql_old_ mysql_old_connect(DB_SERVER, DB_USER, DB_PASS) or die($db->ErrorMsg());
//      mysql_old_select_db(DB_NAME, $this->connection) or die($db->ErrorMsg());
      
      /**
       * Only query database to find out number of members
       * when getNumMembers() is called for the first time,
       * until then, default value set.
       */
      $this->num_members = -1;
   }

   /**
    * confirmUserPass - Checks whether or not the given
    * username is in the database, if so it checks if the
    * given password is the same password in the database
    * for that user. If the user doesn't exist or if the
    * passwords don't match up, it returns an error code
    * (1 or 2). On success it returns 0.
    */
   function confirmUserPass($username, $password){
   	 
   	 global $db, $debug;
   	 
      /* Add slashes if necessary (for query) */
      if(!get_magic_quotes_gpc()) {
	      $username = addslashes($username);
      }

      /* Verify that user is in database */
      $q = "SELECT password FROM ".TBL_USERS." WHERE username = '$username'";
      $result = $db->Execute($q) or die("Error locating user: " . $db->ErrorMsg());
      if(!$result || ($result->RecordCount() < 1)){
         return 1; //Indicates username failure
      }

      /* Retrieve password from result, strip slashes */
      $dbarray = $result->FetchRow();
      $dbarray['password'] = stripslashes($dbarray['password']);
            
      $password = stripslashes($password);

      //if (@$debug) { echo "<br>PW string entered:$password<br>PW string in base:".$dbarray['password']; } 
      
      /* Validate that password is correct */
      if($password == $dbarray['password']){
         return 0; //Success! Username and password confirmed
      }
      else{
         return 2; //Indicates password failure
      }
   }
   
   
   /**
   * @desc verifyUserDatabase
   * This can be called to make sure there's a valid database connection.
   * (This function was moved to version_check.php)
   */

   /**
    * confirmUserID - Checks whether or not the given
    * username is in the database, if so it checks if the
    * given userid is the same userid in the database
    * for that user. If the user doesn't exist or if the
    * userids don't match up, it returns an error code
    * (1 or 2). On success it returns 0.
    */
   function confirmUserSessionID($username, $usersessionid){
	 		global $db;   	 
      /* Add slashes if necessary (for query) */
      if(!get_magic_quotes_gpc()) {
	      $username = addslashes($username);
      }

      /* Verify that user is in database */
      $q = "SELECT usersessionid FROM ".TBL_USERS." WHERE username = '$username'";
      $result = $db->Execute($q);
      if(!$result || ($result->RecordCount() < 1)){
        logDebugMessage ("No user in table matching $username.");
        return 1; //Indicates username failure
      }
      $result->Close();

      /* Retrieve usersessionid from result, strip slashes */
      $dbarray = $result->FetchRow();
      $dbarray['usersessionid'] = stripslashes($dbarray['usersessionid']);
      $usersessionid = stripslashes($usersessionid);

      /* Validate that usersessionid is correct */
      if($usersessionid == $dbarray['usersessionid']){
         return 0; //Success! Username and usersessionid confirmed
      }
      else{
        logDebugMessage ("Query: " . $q);
        logDebugMessage ("Session ID mismatch, session:".$usersessionid.", database: ".$dbarray['usersessionid']);
        return 2; //Indicates usersessionid invalid
      }
   }
   
   /**
    * usernameTaken - Returns true if the username has
    * been taken by another user, false otherwise.
    */
   function usernameTaken($username){
	 		global $db;   	 
      if(!get_magic_quotes_gpc()){
         $username = addslashes($username);
      }
      $q = "SELECT username FROM ".TBL_USERS." WHERE username = '$username'";
      $result = $db->Execute($q);
      return ($result->RecordCount() > 0);
   }
   
   /**
    * usernameBanned - Returns true if the username has
    * been banned by the administrator.
    */
   function usernameBanned($username){
	 		global $db;   	 
      if(!get_magic_quotes_gpc()){
         $username = addslashes($username);
      }
      $q = "SELECT username FROM ".TBL_BANNED_USERS." WHERE username = '$username'";
      $result = $db->Execute($q);
      return ($result->RecordCount() > 0);
   }
   
   /**
    * addNewUser - Inserts the given (username, password, email)
    * info into the database. Appropriate user level is set.
    * Returns true on success, false otherwise.
    */
   function addNewUser($username, $password, $email){
	 		global $db;   	 
      $time = time();
      /* If admin sign up, give admin user level */
      if(strcasecmp($username, ADMIN_NAME) == 0){
         $ulevel = ADMIN_LEVEL;
      }else{
         $ulevel = USER_LEVEL;
      }
      $q = "INSERT INTO ".TBL_USERS." VALUES ('$username', '$password', '0', $ulevel, '$email', $time)";
      return $db->Execute($q);
   }
   
   /**
    * updateUserField - Updates a field, specified by the field
    * parameter, in the user's row of the database.
    */
   function updateUserField($username, $field, $value){
	 		global $db;   	 
      $q = "UPDATE ".TBL_USERS." SET ".$field." = '$value' WHERE username = '$username'";
      return $db->Execute($q);
   }
   
   /**
    * getUserInfo - Returns the result array from a mysql
    * query asking for all information stored regarding
    * the given username. If query fails, NULL is returned.
    */
   function getUserInfo($username){
	 		global $db;   	 
      $q = "SELECT * FROM ".TBL_USERS." WHERE username = '$username'";
      $result = $db->Execute($q);
      /* Error occurred, return given name by default */
      if(!$result || ($result->RecordCount() < 1)){
         return NULL;
      }
      /* Return result array */
      $dbarray = $result->FetchRow();
      return $dbarray;
   }
   
   /**
    * getNumMembers - Returns the number of signed-up users
    * of the website, banned members not included. The first
    * time the function is called on page load, the database
    * is queried, on subsequent calls, the stored result
    * is returned. This is to improve efficiency, effectively
    * not querying the database when no call is made.
    */
   function getNumMembers(){
	 		global $db;   	 
      if($this->num_members < 0){
         $q = "SELECT * FROM ".TBL_USERS;
         $result = $db->Execute($q);
         $this->num_members = $result->RecordCount();
      }
      return $this->num_members;
   }
   
};

/* Create database connection */
$database = new MySQLDB;

?>
