<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
/**
* @desc This class handles setup and distrubution of reports per email
*/
include_once "common.inc.php";
include_once "queries.php";

class EmailAlerts {
    
    var $profilename;
    var $sender;
    
    function __construct() {
        global $conf;
        if (!isset($conf)) { die("No profile selected"); }
        $this->profilename=$conf;
        if (isset($_SERVER['HTTP_HOST'])) {
            $edomain = $_SERVER['HTTP_HOST'];    
        } else {
            $edomain = @php_uname("n");     
        }
        $this->sender="noreply@".$edomain;       
        
        /*
        $admin=$this->AdminEmail();
        if ($admin['email']=="") {
            echoWarning("The email address for user '".$admin['username']."' has not been set. This email will be used as the 'From' address in any messages sent. Please go to the 'User Administration' tab and edit the '".$admin['username']."' user to enter the email address you want to use.");
            $this->adminemail="";
        } else {
            $this->adminemail=$admin['email'];                
        }
        */
    }
    function EmailAlerts() {
        __construct();
    }
    
    /**
    * @desc This function will allow a user to create an email alert
    */
    function CreateAlert() {
        $this->ValidateForm();
        echo "<h3>"._CREATE_ALERT."</h3>\n";
        echo "<form id=\"emailalerts\" method=post action=\"emailalerts.php\" onsubmit=\"javascript:return validate('emailalerts','email');\">\n";
        echo "<div><label>"._EMAIL_ADDRESS.":</label><input type=text name=\"email\"></div>\n";
        echo "<div><label>"._SELECT_INTERVAL.":</label>".$this->SelectInterval()."</div>\n";
        echo "<div><label>"._SELECT_REPORTS.":</label>".$this->SelectReports()."</div>\n";
        echo "<input type=hidden name=\"conf\" value=\"$this->profilename\">\n";
        echo "<input type=submit id=\"submit\" value=\""._SAVE_EMAIL_ALERTS."\">\n";            
    }
    
    /**
    * @desc This function will allow a user to select from a list of reports
    */
    function SelectReports() {
        global $l_constant,$t_constant,$reports;
        $o = "<select name=\"reports[]\" MULTIPLE size=10>";
        $i=0;
		$noTableReports = array(
			// "_ADWORDS_NETWORKS",
			// "_ADWORDS_AD_GROUPS",
			// "_ADWORDS_ADS",
			// "_ADWORDS_CAMPAIGNS",
			// "_ADWORDS_KEWORDS",
			// "_ALL_TRAFFIC_BY_MINUTE",
			// "_AUTHENTICATED_VISITORS",
			// "_BROWSER_BREAKDOWN",
			// "_BROWSER_TRENDS",
			// "_CLICK_TRAILS",
			// "_COLOR_PALETTE",
			// "_CONTENT_BASED_SPLIT_TEST",
			// "_CONVERSION_RATE",
			// "_CONVERSION_TRENDS",
			// "_COUNTRY_CONVERSION",
			// "_COUNTRY_TRENDS",
			// "_CRAWLER_TRENDS",
			// "_DETAILED_CRAWLER_REPORT",
			// "_ERROR_TRENDS",
			// "_FACEBOOK_ACTIVE_COUNTRIES",
			// "_FACEBOOK_AGE",
			// "_FACEBOOK_PAGE_VIEWS",
			// "_FACEBOOK_API_PERFORMANCE",
			// "_FACEBOOK_APP_SHARING",
			// "_FACEBOOK_APP_ACTIVTY",
			// "_FACEBOOK_EDUCATION",
			// "_FACEBOOK_FANS",
			// "_FACEBOOK_LANGUAGE",
			// "_FACEBOOK_LOCATIONS",
			// "_FACEBOOK_OVERVIEW",
			// "_FACEBOOK_RELIGION",
			// "_FACEBOOK_RELATIONSHIPS",
			// "_FACEBOOK_WORK",
			// "_FUNNEL_ANALYSIS",
			// "_GOALS",
			// "_KEYWORD_CONVERSION",
			// "_KEYWORD_TRENDS",
			// "_MOUSE_TRACKER",
			// "_OPENCART_SALES",
			// "_OPENCART_SALES_PER_COUNTRY",
			// "_OPENCART_TAXES",
			// "_OPENCART_TOP_CUSTOMERS",
			// "_OPENCART_TOP_PRODUCTS",
			// "_PAGE_ANALYSIS",
			// "_PAGE_TRENDS",
			// "_PERFORMANCE_TRENDS",
			// "_REFERRER_TRENDS",
			// "_ROAD_TO_SALES",
			// "_SCREEN_RESOLUTION",
			// "_SEARCH_ENGINE_TRENDS",
			// "_SURVEYS",
			// "_TIMED_SPLIT_TEST",
			// "_TREND_ANALYSIS",
			// "_TOP_CITIES",
			// "_TWEETS_PER_DAY",
			// "_TWITTER_FEED",
			// "_TWITTER_FOLLOWERS",
			// "_TWITTER_FOLLOWING",
			// "_TWITTER_TOTAL_TWEETS",
			// "_URL_BASED_SPLIT_TEST",
			// "_YOUTUBE_CHANNEL",
			// "_ZENCART_SALES",
			// "_ZENCART_SALES_PER_COUNTRY",
			// "_ZENCART_TAXES",
			// "_ZENCART_TOP_CUSTOMERS",
			// "_ZENCART_TOP_PRODUCTS"
		);
		foreach($reports as $k => $v) {
			if((isset($v['EmailAlerts']) && $v['EmailAlerts'] == false) || (isset($v['hidden']) && $v['hidden'] == true)) {
				$noTableReports[] = $k;
				continue;
			}
			if(in_array($k,$noTableReports) == FALSE){
				$o.= "<option value=\"$k\">".constant($k)."</option>\n";
			}
		}
        // foreach ($t_constant as $report) {
            // if ($i==0) { $i++; continue; }
            // $o.= "<option value=\"$report\">".constant($report)."</option>\n";
            // $i++;    
        // }
        // $i=0;
        // foreach ($l_constant as $report) {
            // if ($i==0) { $i++; continue; } 
            // $o.= "<option value=\"$report\">".constant($report)."</option>\n";
            // $i++;    
        // }
        $o.= "</select>";
        return $o;
    }
    
    /**
    * @desc This funtion will allow a user to select an interval at which the email is to be sent
    */
    function SelectInterval() {
        $o ="<select name=\"emailinterval\">\n";
        $o.="<option value=\"Daily\">"._DAILY."</option>\n";
        $o.="<option value=\"Weekly\">"._WEEKLY."</option>\n";
        $o.="<option value=\"Monthly\">"._MONTHLY."</option>\n";
        $o.="</select>";
        return $o;
    }
    
    /**
    * @desc This function validates the create form
    */
    function ValidateForm() {
        ?>
        <script type="text/javascript">
        function validate(form_id,email) {
           var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
           var address = document.forms[form_id].elements[email].value;
           if(reg.test(address) == false) {
              alert('<?php echo _INVALID_EMAIL;?>');
              return false;
           }
           if (document.forms[form_id].elements['reports[]'].value=="") {
                alert('<?php echo _SELECT_AT_LEAST_ONE_REPORT;?>');
                return false;    
           }           
        }
        </script>
        <?php
    }
    
    /**
    * @desc This function will store the email alert in the database     
    */
    function StoreAlert($alertData,$lastsent="0") {        
        # first give it a unique name
        $name = "emailalerts.".md5(serialize($alertData));        
        # now add a 'last sent' timestamp to the array
        $alertData["lastsent"] = $lastsent;
        # now serialize the array and store it
        $value = serialize($alertData);
        setProfileData($this->profilename,$name,$value);                
    }
    
    /**
    * @desc This function will send the email alerts for a given profile. If $test=true, email will be sent regardless of interval and last sent timestamp
    * @returns An array with feedback from the function, i.e. emails sent or any problems
    */
    function SendAlerts($test=false) {
        global $profile;
        $feedback = array();
        $alerts = $this->GetAlerts();
        if (count($alerts) == 0) {
            $feedback[] = Warning(_NOTHING_TO_SEND);
            return;    
        }
        foreach($alerts as $alert) {
            $a = unserialize($alert[2]);
            # check if we are good to go, skip we we are manually testing
            if ($this->ShouldWeSend($a)==false && $test==false) {
                continue;    
            }
            # set up the message
            $subject=_LOGAHOLIC_EMAIL_ALERT." [";
            $mail_contents = "<html><head>";
            $mail_contents.= $this->ApplyReportStyles();
            $mail_contents.= "</head><body>";
            $mail_contents.= _BEGIN_EMAIL." <strong>$profile->confdomain</strong>:<br />\n";
            # create all the selected reports
            foreach ($a['reports'] as $report) {
                $subject .= constant($report).", ";
                $mail_contents.= $this->BuildReport($report);
            }
            $mail_contents.= "<br /><br />Powered by <a href=\"http://www.logaholic.com/\">Logaholic Web Analytics</a><br /><br /></body></html>";
            $subject = substr($subject,0,-2)."]";
            # now send it
            $this->HtmlEmail($a['email'],$this->sender,$subject,$mail_contents);
            //echo $mail_contents;
            $notice = str_replace("%a",$a['email'],_SENT_EMAIL);
            $notice = str_replace("%b",$subject,$notice);
            $feedback[] = Notice($notice);
            #now update the last sent timestamp
            $lastsent = time();
            unset($a['lastsent']);
            $this->StoreAlert($a,$lastsent);                            
        }
        return $feedback;
    }
    
    /**
    * @desc This function will build a report based on the label that has been passed in
    */
    function BuildReport($report, $from="",$to="") {
        global $query, $data, $showfields, $mini,$reports, $labels;
		$labels= $report;
        if (empty($from)) {
			$from = mktime(0,0,0,date("m"),01,date("Y"));
			$to = mktime(23,59,59,date("m"),date("d"),date("Y"));
		}
        ob_start();
		if (isset($labels) && isset($reports[$labels])) {
			# there is a class file for this report, let's use it
			$r = new $reports[$labels]["ClassName"]();
			$r->labels = $labels;
			$r->DisplaySimpleTable();
		}
		$contents = ob_get_clean();
        return $contents;    
    }
    
    /**
    * @desc This function will send an html formatted email
    */
    function HtmlEmail($to,$from,$subject,$contents) {
        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
        $neatmail=trim($to);       
        mail($neatmail, "$subject","$contents","From: $from <$from> \n" . $headers) or die (Warning(_FAILED_TO_SEND));
        return true;        
    }
    
    /**
    * @desc This function will list the email alerts that have been created for the selected profile
    */
    function ShowAlerts() {
        $alerts = $this->GetAlerts();
        if (count($alerts) == 0) {
            # no alerts have been defined yet
            return;    
        } 
        echo "<h3>"._ACTIVE_EMAIL_ALERTS.":</h3>";
        echo "<table id=\"alerttable\" cellspacing=\"0\"><tr><th>&nbsp;</th><th>"._EMAIL."</th><th>"._INTERVAL."</th><th>"._REPORTS."</th><th>"._LAST_SENT."</th></tr>";
        foreach($alerts as $alert) {
            $a = unserialize($alert[2]);
            echo "<tr><td>".Button("emailalerts.php?conf=$this->profilename&del={$alert[0]}",_DELETE)."</td><td>{$a['email']}</td><td>{$a['emailinterval']}</td><td>";
            $i=0;
            foreach ($a['reports'] as $report) {
                if ($i > 5) { echo ", ..."; break; }
                if ($i > 0) { echo ", "; }
                echo constant($report);
                $i++;    
            }            
            echo "</td><td>";
            if ($a['lastsent']==0) { echo _NEVER; } else { echo date("Y-m-d H:i:s",$a['lastsent']); }
            echo "</td></tr>\n";   
        }
        echo "</table>";            
    }
    
    /**
    * @desc This function gets an array of email alerts from the database    
    */
    function GetAlerts() {
        global $db;
        $db->SetFetchMode(ADODB_FETCH_NUM);
        $result = $db->Execute("select * from ".TBL_GLOBAL_SETTINGS." where profile = '$this->profilename' and name like 'emailalerts.%'");
        $alerts = $result->GetArray();
        $db->SetFetchMode(ADODB_FETCH_BOTH);
        return $alerts;    
    }
    
    /**
    * @desc This function deletes an alert
    */
    function DeleteAlert($alert) {
        global $db;
        $db->Execute("delete from ".TBL_GLOBAL_SETTINGS." where profile = '$this->profilename' and name='$alert'");                
    }
    
    /**
    * @desc This function gets the email address of the first admin user, which is used as the sender of any emails
    */
    function AdminEmail() {
        global $db;
        $q = $db->Execute("select email,username from ".TBL_USERS." where isadmin = '1' order by userid limit 1");
        return $q->FetchRow();           
    }
    
    /**
    * @desc This function tells the user from which address emails will be sent and allows the user to test/send alerts
    */
    function FootNote() {
        $message = Button("emailalerts.php?conf=$this->profilename&testalerts=1",_SEND_EMAIL_ALERTS_NOW,"float:right;margin-top:15px;");
        $message.= str_replace("%a",$this->sender,_FOOTNOTE);
        $altemail = "noreply@".@php_uname("n");
        $message = str_replace("%b",$altemail,$message);
        echoNotice($message,"margin-top:20px;padding:10px;");    
    }
    
    /**
    * @desc This function checks to see if an alert should be sent or not, based on the interval and the last sent timestamp
    */    
    function ShouldWeSend($alert) {
        if ($alert['lastsent']==0) {
            return true;
        }
        if ($alert['emailinterval']=="Daily" && (time() - $alert['lastsent']) >= 86400) {
            return true;
        }
        if ($alert['emailinterval']=="Weekly" && (time() - $alert['lastsent']) >= (86400 *7)) {
            return true;
        }
        if ($alert['emailinterval']=="Monthly" && date("m",time())!= date("m",$alert['lastsent'])) {
            return true;
        }
        return false;        
    }
    
    /**
    * @desc This function will apply email freindly styles to the report tables
    */
    function ApplyReportStyles() {
        $style="<style type=\"text/css\">
        BODY { font-family:Arial; font-size:12px; }
        .report_title { font-size: 16px; font-weight: bold; margin-top:25px; padding-left:2px; }
        .date_line { font-size:11px; padding-left:3px; }
        .report_table TABLE { border: 1px solid silver; border-collapse: collapse; width:100%; font-size:12px; }
        .report_table TH { background-color: #E0E0E0; border: 1px solid silver; text-align: left; padding:4px 4px 4px 6px; }
        .tabletotalcolor { background-color: #E0E0E0; border: 1px solid silver; text-align: left; padding:4px 4px 4px 6px; }
        .report_table TD { border: 1px solid silver; padding:6px; vertical-align: top; }        
        </style>";
        return $style;        
    }
}
?>
