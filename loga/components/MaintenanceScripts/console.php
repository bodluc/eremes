#!/usr/bin/php -q
<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
// PHPLOCKITOPT NOENCODE
/**
* This is a linux console program to help you manage
* updating Logaholic profiles via the command line.
* 
* Setup Instructions:
*  
*  Edit the PHP command reference on the first line of
*  this file to the correct location of the php executable 
*  on your machine.
* 
*  Edit the LGPATH and MYSQL_USERNAME definitions 
*  below to the correct values for your machine
* 
*  Make this file executable, i.e. 
*  > chmod 655 console.php
* 
* Start the program by typing ./console.php in the 
* command line   
*/

# Enter the absolute path to your logaholic directory
define('LGPATH','/var/www/somedomain/');

# Enter your mysql user name 
define('MYSQL_USERNAME','root');

/**
* Do not edit beyond this point
*/

class lgconsole {

    # this will run when the program starts
    function __construct() {
        if ($_SERVER['DOCUMENT_ROOT']) {
            echo "This script should be started from the command line only.<br>Terminating.";
            exit();
        }
        $this->line();
        print("------------- Welcome to the Logaholic Console Program -------------\n");
        $this->Menu();        
    }
    
    # this will make the initialization work with php4
    function lgconsole() {
        return $this->__construct();  
    }
    
    # this reads input from the user and returns it
    function read() {
        $fp=fopen("/dev/stdin", "r");
        $input=fgets($fp, 255);
        fclose($fp);
        return str_replace("\n","",$input);
    }

    # this prints one or more horizontal lines
    function line($n=1) {
        for($i=1;$i<=$n;$i++) {
            print("--------------------------------------------------------------------\n");            
        }    
    }

    /**
    * @desc This function displays the menu options, reads a response 
    * and passes it to MenuAction for processing
    */
    function Menu() {
        global $action;
        $this->line();
        print("What do you want to do?\n ");
        print("1. Update a profile\n ");
        print("2. Check for running updaters\n ");
        print("3. Show a mysql processlist\n ");
        print("4. Show uptime\n ");
        print("5. Reset a profile's update running status\n ");        
        print("6. Exit\n");
        print("Enter number: ");
        $action = $this->read();
        $this->line();
        $this->MenuAction($action);        
    }
    
    /**
    * @desc This function processes the user Menu option response  
    * and executes the correct function. When done it executes the menu 
    * function again
    */
    function MenuAction($action) {
        switch ($action) {
            case 1:
                $this->updateProfile();
                break;
            case 2:
                $this->checkForRunningUpdates();
                break;
            case 3:
                $this->showProcesslist();
                break;
            case 4:
                $this->showUptime(); 
                break;
            case 5:
                $this->resetProfile();
                break;
            case 6:
                $this->endProgram();
                break;
            default:
                print("Invalid option\nYou need to enter the number of your choice... \n");            
        }
        $this->Menu();
    }

    /**
    * @desc This function starts a logaholic update.php process for a profile name
    * the user enters. User can also reset update status if needed.
    */
    function updateProfile() {
        print("Enter profile name: ");
        $pname = $this->read();
        print("Do you want to reset update status?\n ");
        print("0. Leave blank for No\n ");
        print("1. Yes\n");
        print("Enter number: ");
        $reset = $this->read();
        passthru("cd ".LGPATH."; php update.php \"conf=$pname&reset=$reset\"");    
    }

    /**
    * @desc This function shows a process list of any program running on the sytem matching the word "update"
    * Then it executes the next check function
    */
    function checkForRunningUpdates() {
        print("Processes running on the machine matching 'update'\n");
        $this->line(); 
        $c = system("ps ax | grep update | grep -v grep");
        if ($c=="") {
            echo "No running updaters found\n";
        }
        $this->line();
        $this->checkUpdateStatusInDb();            
    }
    
    /**
    * @desc This function shows the last known update status from the logaholic database
    * The update status is "yes" when an update process for a profile has been started 
    * and has not completed (yet)
    */
    function checkUpdateStatusInDb() {
        global $running_from_command_line,$db;
        $running_from_command_line = true;
        include_once(LGPATH."common.inc.php");  
        print("Profiles that are currently updating according to the Logaholic update status\n");
        $this->line(); 
        $q = $db->Execute("SELECT Profile, Value FROM ".TBL_GLOBAL_SETTINGS." where Value='yes' and Name like '%.update_running'");
        if ($data = $q->FetchRow()) {
            do {
                print(" - ".$data['Profile']."\n");        
            } while ($data = $q->FetchRow());
        } else {
            print("No profiles are updating.\n");
        }            
    }
    
    /**
    * @desc This function shows the last known update status from the logaholic database
    * The update status is "yes" when an update process for a profile has been started 
    * and has not completed (yet)
    */
    function resetProfile() {
        global $running_from_command_line,$db;
        $running_from_command_line = true;
        include_once(LGPATH."common.inc.php");
        print("Enter profile name to reset: ");
        $pname = $this->read();
        setProfileData($pname,"$pname.update_running","no");
        print("Update running status for $pname has been set to 'no'\n");              
    }
    
    /**
    * @desc This function shows a process list of queries running in the mysql server
    */
    function showProcesslist() {
        system("mysqladmin -u ".MYSQL_USERNAME." -p processlist");        
    }
    
    /**
    * @desc This function just shows the uptime command with system load etc.
    */
    function showUptime() {
        system("uptime");        
    }
    
    function endProgram() {
        print("Adios!\n");
        $this->line();
        exit();    
    }
    
    # To Do:
    # make option to start update in background and log progress to file
    

}

$run = new lgconsole();


?>

