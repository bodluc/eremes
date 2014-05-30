<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/

class SiteProfile {
    var $profileid;                 // The unique database identifier for this profile.
    
    var $profilename;                // The identifier / name for the profile.
    var $confdomain;                // The domain name for this profile.
    var $equivdomains;            // Equivalent domains that are treated the same as the main domain.
    var $tablename;                    // The database table name.
    var $defaultfile;                // The default file name (index.php / etc).
    var $logfilefullpath;        // Path to the log files.
    var $splitlogs;                    // Are we using split log files.
    var $splitfilter;                // Filename filter if we have multiple file name.s
    var $trackermode;                // Are we using tracker mode?  (vs log files)
    var $skipips;                        // List of IPs to skip
    var $skipfiles;                    // List of files types to skip
    var $urlparamfilter;               // params in the url that we should ...
    var $urlparamfiltermode;           // "Include" or "Exclude"
    var $googleparams;                 // a list of google parameters we care about. If not in the list, it will not be saved
    var $targetfiles;                // "Conversion" files.
    var $usepagecache;            // Should we use page caching?
    var $visitoridentmethod; // What method of user identification are we using?
    var $feedurl;                 // the page where your feeds are
    var $feedburneruri;           // your feedburner account url
	var $lastused;				// Timestamp when the profile is last used
	var $facebooklogin;			// Your facebook login email adress
	
	var $dateFormat;			// set the date format for the reports of that profile!
	
	var $logparsertype;			// your log parser type
    
    var $profileloaded;            // Is the profile loaded / found?
    
    var $dieonsqlerror;            // If there's a sql error, call "die" and end.  Otherwise, just set the contents in $lastsqlerror and go on.
    var $lastsqlerror;            // If there was a sql error, stick it in this variable.
    
    var $tableprefix;
	
    var $tablename_vpd;
    var $tablename_vpm;
    var $tablename_conversions;
    var $tablename_dailyurls;
    var $tablename_urls;
    var $tablename_urlparams;
    var $tablename_referrers;
    var $tablename_refparams;
    var $tablename_keywords;
    var $tablename_trackerlog;    
    var $tablename_useragents;
	
    var $targets_sql;
    var $targets;
    
    var $importantURLParams; // An array of records
    var $_importantURLParamsToDelete; // An array of records to delete
    
    var $animate; // do we want to animate flash graphs ?
    var $timezonecorrection;
    var $timezone; 
    
    var $structure_version; // What structure version is the profile's data?
    
    var $recursive;
    var $ftpserver;
    var $ftpuser;
    var $ftppasswd;
    var $ftpfullpath;
    var $visittimeout;
	var $includebackup;
    
    // Constructor - if you pass a profile name to the constructor    
    function SiteProfile( $ProfileName = null ) {
        global $mysqlprefix ,$session, $validUserRequired;
        // Initialize the variables.
        $this->profilename = "";            
        $this->confdomain = "";                
        $this->equivdomains = "";
        $this->tablename = "";                
        $this->defaultfile = "";            
        $this->logfilefullpath = "";    
        $this->splitlogs = 0;                    
        $this->splitfilter = "";
        $this->splitfilternegative = "";            
        $this->trackermode = 0;                
        $this->skipips = "";                    
        $this->skipfiles = ".gif, .jpg, .jpeg, .js, .css, .txt, .ico, .swf, .xml, .png, .dll, logaholic/";    
        $this->targetfiles= "";                    
        $this->profileloaded = "";            
        $this->dieonsqlerror = true;        
        $this->lastsqlerror = "";                
        $this->usepagecache = true;
        $this->importantURLParams = array();
        $this->_importantURLParamsToDelete = array();
        $this->animate = 0;
        $this->timezonecorrection = 0;
        $this->timezone = (function_exists("date_default_timezone_get") ? date_default_timezone_get() : "Etc/GMT+0");
        $this->structure_version = 0;
        $this->visitoridentmethod = VIDM_IPPLUSAGENT;
        $this->recursive = 0;  
        $this->ftpserver = "";  
        $this->ftpuser = "";  
        $this->ftppasswd = "";  
        $this->ftpfullpath = "";
        $this->visittimeout= "20";
        $this->urlparamfilter= "";
        $this->urlparamfiltermode= "Exclude";
        $this->googleparams="q, start, gclid, as_q, as_epq, as_oq, as _eq, as_sitesearch, as_rq, as_lq";
        $this->feedurl= "";
        $this->feedburneruri= "";		
		$this->facebooklogin = "";
		$this->dateFormat = array();
		$this->logparsertype = "";
		$this->lastused = "";
		$this->includebackup = false;
		
		$this->tableprefix = $mysqlprefix;		
        
        $this->profileloaded = false;
        if ($ProfileName) {
			if (@$_SESSION['profilename'] == $ProfileName && isset($_SESSION['profileobject'])) {
				foreach(unserialize($_SESSION['profileobject']) as $key =>$val) {
					$this->$key = $val;
				}
				$this->loadedfromsession = true;
				$this->profileloaded = true;
			} else {
				$this->Load($ProfileName);
			}
		}
		
		
		if ($validUserRequired && !$session->isAdmin() && !empty($session->username) && !empty($this->profilename) ){
			if(!in_array($this->profilename, $session->user_profiles) ){
				echoWarning("The logged in user cannot use this profile.", "margin:5px;");
				exit();
			}
		}
		
		// Check if this profile is used last.
		$this->updateLastUsed();
		
    }
	function updateLastUsed(){
		global $db;
		$dif = time() - $this->lastused;
		if($dif >= 3600){
			$db->Execute("UPDATE ".TBL_PROFILES." SET lastused=".time()." WHERE profilename='{$this->profilename}'");
		}
	}
    
    function Load( $ProfileName ) {
        global $db, $mysqlprefix,$dateFormat;
    
        $lastsqlerror = "";
        $query = "Select * from ".TBL_PROFILES." where profilename = \"" . $db->escape($ProfileName) . "\"";
        $result = $db->Execute($query) or $this->_sqlError("Couldn't query profile table: " . $db->ErrorMsg());
        
        if ($profile_row = $result->FetchRow()) {
            $this->profileloaded = true;
            $this->profileid = $profile_row["profileid"];
            $this->profilename = $profile_row["profilename"];
            $this->confdomain = $profile_row["confdomain"];
            $this->equivdomains = $profile_row["equivdomains"];
            $this->tablename = $profile_row["tablename"];
            $this->defaultfile = $profile_row["defaultfile"];
            $this->logfilefullpath = $profile_row["logfilefullpath"];
            $this->splitlogs = $profile_row["splitlogs"];
            $this->splitfilter = $profile_row["splitfilter"];
            $this->splitfilternegative = $profile_row["splitfilternegative"]; 
            $this->trackermode = $profile_row["trackermode"];
            $this->skipips = $profile_row["skipips"];
            $this->skipfiles = $profile_row["skipfiles"];
            $this->targetfiles = $profile_row["targetfiles"];
            $this->usepagecache = $profile_row["usepagecache"];
            $this->animate = $profile_row["animate"];
            $this->timezonecorrection = $profile_row["timezonecorrection"];
            $this->timezone = $profile_row["timezone"];
            $this->structure_version = $profile_row["structure_version"];
            $this->visitoridentmethod = $profile_row["visitoridentmethod"];
            $this->recursive = $profile_row["recursive"];
            $this->ftpserver = $profile_row["ftpserver"];
            $this->ftpuser = $profile_row["ftpuser"];
            $this->ftppasswd = $profile_row["ftppasswd"];
            $this->ftpfullpath = $profile_row["ftpfullpath"];
            $this->visittimeout = $profile_row["visittimeout"];
            $this->urlparamfilter = $profile_row["urlparamfilter"];
            $this->urlparamfiltermode = $profile_row["urlparamfiltermode"];
            $this->googleparams = $profile_row["googleparams"];
            $this->feedurl = $profile_row["feedurl"];
            $this->feedburneruri = $profile_row["feedburneruri"];
            $this->lastused = $profile_row["lastused"];
			
            $this->facebooklogin = getProfileData($profile_row["profilename"], $profile_row["profilename"].".facebookLogin", false );
            
			$this->dateFormat = getProfileData($profile_row["profilename"], $profile_row["profilename"].".profileDateFormat", getGlobalSetting("profileDateFormat",$dateFormat));
			$this->dateFormat = unserialize($this->dateFormat);
			
			$this->includebackup = getProfileData($profile_row["profilename"], $profile_row["profilename"].".includebackup", false );      
            
			
            $this->tablename_vpd = $this->tablename . "_vpd";
            $this->tablename_vpm = $this->tablename . "_vpm";
            $this->tablename_dailyurls = $this->tablename . "_dailyurls"; 
            $this->tablename_conversions = $this->tablename . "_conversions";
            $this->tablename_urls = $this->tablename . "_urls";
            $this->tablename_urlparams = $this->tablename . "_urlparams";
            $this->tablename_referrers = $this->tablename . "_referrers";
            $this->tablename_refparams = $this->tablename . "_refparams";
            $this->tablename_keywords = $this->tablename . "_keyword";
            $this->tablename_visitorids = $this->tablename . "_visitorids";
            $this->tablename_sessionids = $this->tablename . "_sessionids";
            $this->tablename_trackerlog = $this->tablename . "_trackerlog";
            $this->tablename_screenres = $this->tablename . "_screenres";
            $this->tablename_colordepth = $this->tablename . "_colordepth";
            $this->tablename_useragents = $this->tablename . "_useragents";
                                      
            // Parse out the target page list so it can be used in a query.
            $this->targets=explode(",",$this->targetfiles);
            $this->targets_sql="";
            foreach ($this->targets as $thistarget) {
                if ($thistarget > "") {
                    $this->targets_sql .= " u.url='".trim($thistarget)."' or";
                }
            }
            if ($this->targets_sql > "") {
                $this->targets_sql="(".substr($this->targets_sql,0,-3).")";
            } else {
                // No targets, so don't return any rows when this is stuck into a query...
                $this->targets_sql = " (0) ";
            }
            $result->Close();
            
            // Load any important URL parameters.
            $this->importantURLParams = array();
            // Order it by the parameter ID to make things consistent
            $query = "Select * from ".TBL_IMPORTANT_URL_PARAMS." where profileid = $this->profileid order by paramid";
            $result = $db->Execute($query);
            
            while ($importantParamsRow = $result->FetchRow()) {
                $this->importantURLParams[] = $importantParamsRow;
            }
            $result->Close();
            
            $this->_importantURLParamsToDelete = array();
            
        } else {
            $this->profilename = $ProfileName;
            $this->profileloaded = false;
            $this->profileid = null;
            $this->importantURLParams = array();
            $this->_importantURLParamsToDelete = array();
        }
		
		// save the profile object in the session for fast access
		foreach($this as $key => $val) {
			$p[$key] = $val;
		}
		if ($ProfileName!="newcnf") {
			$_SESSION['profilename'] = $ProfileName;		
			$_SESSION['profileobject'] = serialize($p);
		}
    }
    
    function _sqlError($errorMessage) {
        $this->lastsqlerror = $errorMessage;
        if ($this->dieonsqlerror) { die($errorMessage); }
    }
    
    // Private method that saves or inserts.
    function _privateSave($newRecord) {
        global $db;
				
        // Build the assignment stuff.
        $record["profilename"] = $this->profilename;
        $record["confdomain"] = $this->confdomain;
        $record["equivdomains"] = $this->equivdomains;
        $record["tablename"] = $this->tablename;
        $record["defaultfile"] = $this->defaultfile;
        $record["logfilefullpath"] = $this->logfilefullpath;
		if (is_dir($this->logfilefullpath)) {
			$this->splitlogs = 1;	
		}
        $record["splitlogs"] = $this->splitlogs;
        $record["splitfilter"] = $this->splitfilter;
        $record["splitfilternegative"] = $this->splitfilternegative;
        $record["trackermode"] = $this->trackermode;
        $record["skipips"] = $this->skipips;
        $record["skipfiles"]= $this->skipfiles;
        $record["targetfiles"] = $this->targetfiles;
        $record["usepagecache"] = $this->usepagecache;
        $record["animate"] = $this->animate;
        $record["timezonecorrection"] = $this->timezonecorrection;
        $record["timezone"] = $this->timezone;
        $record["structure_version"] = $this->structure_version;
        $record["visitoridentmethod"] = $this->visitoridentmethod;
        $record["recursive"] = $this->recursive;
        $record["ftpserver"] = $this->ftpserver;
        $record["ftpuser"] = $this->ftpuser;
        $record["ftppasswd"] = $this->ftppasswd; 
        $record["ftpfullpath"] = $this->ftpfullpath;
        $record["visittimeout"] = $this->visittimeout;
        $record["urlparamfilter"] = $this->urlparamfilter;
        $record["urlparamfiltermode"] = $this->urlparamfiltermode;
        $record["googleparams"] = $this->googleparams;
        $record["feedurl"] = $this->feedurl;
        $record["feedburneruri"] = $this->feedburneruri;  
		
        if ($newRecord) {
            if (!empty($record["tablename"])) {
				$this->tablename = $this->tableprefix.$this->tablename;
				$record["tablename"] = $this->tablename;
			}			
			$db->AutoExecute(TBL_PROFILES, $record, "INSERT");
            $this->profileid = $db->Insert_ID();
            if (empty($record["tablename"])) {
                $this->tablename = $this->tableprefix."p".$this->profileid;
                $this->_privateSave(false);
				$this->setDefaultDashboard();
                return;
            }
			$this->setDefaultDashboard();
        } else {
            if (!$this->profileid) { $this->_sqlError("Can't save profile - no profile id."); }
            $db->AutoExecute(TBL_PROFILES, $record, "UPDATE", "profileid = " . $this->profileid);
            $affected_row_count = $db->Affected_Rows();
            if ($affected_row_count != 1) { 
                // If no rows were affected, then it may be because no changes were made.  Let's just 
                // do a quick and dirty check to make sure that we can actually find our record, though,
                // because maybe the record can't be found.
                $exist_check = $db->Execute("select profileid from ".TBL_PROFILES." where profileid = ".$this->profileid);
                if ($exist_check->RecordCount() != 1) {
                    $this->_sqlError("Error updating profile, profile ID ".$this->profileid." can't be found.");
                }
            }
            // now lets check to see if the profile was RENAMED, if so, we might need to rename the tables as well
            if (isset($_REQUEST['editconf'])) {
                if ($_REQUEST['editconf']!=$this->profilename) {
                    // the name has changed. Is the tablename the same as the profilename ? 
                    // if so, we have to rename the tables to the new name
                    if ($this->profilename == $this->tablename) {
                        $this->renameTables($_REQUEST['editconf'],$this->profilename);                        
                    }
                }
            }  
            
        }
        
        // Don't use foreach to iterate object lists.
        for ($param_loop = 0; $param_loop < $this->getUrlParamCount(); $param_loop ++) {
            
            $this_record = &$this->getUrlParamByIndex($param_loop);
            
            // Build the assignment stuff.
            $record = array();
            $record["profileid"] = $this->profileid;
            $record["filename"] = $this_record["filename"];
            $record["nameisregex"] = ($this_record["nameisregex"] ? "1" : "0");
            $record["importantparams"] = $this_record["importantparams"];
            
            if (!$this_record["paramid"]) {
                if ($this_record["filename"]) {
                    $db->AutoExecute(TBL_IMPORTANT_URL_PARAMS, $record, "INSERT");
                    $this_record["paramid"] = $db->Insert_ID();
                }
            } else {
                // Do a paramid + profileid check, just to make sure that someone can't update a different profile's settings by changing paramids.
                $db->AutoExecute(TBL_IMPORTANT_URL_PARAMS, $record, "UPDATE", "paramid = " . $this_record["paramid"] . " and profileid = " . $this->profileid);
                $affected_row_count = $db->Affected_Rows();
                if ($affected_row_count != 1) { 
                    // If no rows were affected, then it may be because no changes were made.  Let's just 
                    // do a quick and dirty check to make sure that we can actually find our record, though,
                    // because maybe the record can't be found.
                    $exist_check = $db->Execute("select profileid, paramid from ".TBL_IMPORTANT_URL_PARAMS." where paramid = ".$this_record["paramid"]);
                    if ($exist_check->RecordCount() != 1) {
                        $this->_sqlError("Error updating important URL parameters, param ID ".$this_record["paramid"]." can't be found.");
                    }
                }
            }
        }
            
        // Delete all the appropriate ones.
        // Don't use foreach to iterate object lists.
        for ($param_loop = 0; $param_loop < count($this->_importantURLParamsToDelete); $param_loop ++) {
            $this_record = &$this->_importantURLParamsToDelete[$param_loop];
            $query = "DELETE from ".TBL_IMPORTANT_URL_PARAMS." where paramid = " . $this_record["paramid"] . " and profileid = " . $this->profileid;            
            $db->Execute($query);
        }
        
        // insert the default segmentation filters if we haven't already done so.
        $this->insertDefaultSegments();
        
		setProfileData($this->profilename,$this->profilename.".logparsertype",$this->logparsertype);
		setProfileData($this->profilename,$this->profilename.".includebackup",$this->includebackup);
		
		
        if (!$this->lastsqlerror) {
            $this->Load($this->profilename);
        }
    }
    
    function Save() {
        if ($this->profileid) {
            $this->_privateSave(false);
        } else {
            $this->_privateSave(true);
        }
    }
    
    function SaveAsNewProfile() {
        $this->_privateSave(true);
    }
    
    /**
    * create a new parameter / url set in the array.
    */     
    function &getUrlParamNew() {
        $this->importantURLParams[] = array("paramid" => null, "filename" => null, "nameisregex" => null, "importantparams" => null);
        return $this->importantURLParams[$this->getUrlParamCount()-1];
    }
    
    /**
    * Return the count of important URLParams
    */     
    function getUrlParamCount() {
        return count($this->importantURLParams);
    }
    
    
    /**
    * Return a *reference* to the array index passed in.  If you do a byreference variable assignment of this
    * function, then you can edit the values of the returned array and they will automatically be saved.
    * @param integer Index The 0-based index of which record to return.
    */     
    function &getUrlParamByIndex($index) {
        return $this->importantURLParams[$index];
    }
    
    /**
    * Delete a record for the important url parameters list.
    * @param integer Index The 0-based index of which record to delete.
    */     
    function deleteUrlParams($index) {
        $this->_importantURLParamsToDelete[] = $this->getUrlParamByIndex($index);  // not by reference, since we're about to kill it.
        for ($param_loop = $index+1; $param_loop < $this->getUrlParamCount(); $param_loop ++) {
            $this->importantURLParams[$param_loop-1] = $this->importantURLParams[$param_loop]; // not by reference.
        }
        array_pop($this->importantURLParams);
    }
    
    function insertDefaultSegments() {
        global $db;
        $check = "select * from `".TBL_TRAFFIC_SOURCES."` where profileid = '{$this->profileid}' and sourcename='New Visitors'";
        $result = $db->Execute($check);
        if ($data = $result->FetchRow()) {
            // we have something , we'll assume we already did this  
        } else {
            // lets insert the default segmentation filters
            $start  = "INSERT INTO `".TBL_TRAFFIC_SOURCES."` SET profileid = '{$this->profileid}', ";
            $db->Execute($start. "sourcename = 'Google Adwords Ad', sourcecondition = 'params LIKE \'?gclid=%\'', category='Marketing Campaigns'");
            $db->Execute($start. "sourcename = 'Google Search Ad', sourcecondition = 'params LIKE \'?gclid=%\' AND referrer LIKE \'http://www.google.%\'', category='Marketing Campaigns'");
            $db->Execute($start. "sourcename = 'Google Content Ad', sourcecondition = 'referrer LIKE \'[G]%\'', category='Marketing Campaigns'");
            $db->Execute($start. "sourcename = 'Yahoo Search Marketing', sourcecondition = 'params LIKE \'?OVRAW=%\'', category='Marketing Campaigns'");
            
            $db->Execute($start. "sourcename = 'New Visitors', sourcecondition = 'url =\'x\'', category='Visitor Profiling'");
            $db->Execute($start. "sourcename = 'Return Visitors', sourcecondition = 'url =\'x\'', category='Visitor Profiling'");
            $db->Execute($start. "sourcename = 'Mobile Visitors', sourcecondition = 'is_mobile =\'1\'', category='Visitor Profiling'");
            
            $db->Execute($start. "sourcename = 'US Visitors', sourcecondition = 'country =\'US\'', category='Geographic Segments'");
            $db->Execute($start. "sourcename = 'Rest of World', sourcecondition = 'country !=\'US\'', category='Geographic Segments'");
        }
        
    }
    
    /**
    * @desc This function renames the profiles mysql tables when a profile gets renamed
    */
    function renameTables($old,$new) {
        global $db;        
        # first rename the main table
        $q = "RENAME TABLE $old to $new";
        $db->Execute($q);
        //echo "<hr>".$q."<br>"; 
        
        # now get the other tables and rename them
        $tables = $db->Execute("show tables like '{$old}\_%'");
        while ($table=$tables->FetchRow()) {
            # old table name
            $ot = $table[0];
            # new table name
            $nt = str_replace($old,$new,$ot);
            # rename it
            $q = "RENAME TABLE $ot to $nt"; 
            $db->Execute($q);
            //echo $q."<br>";    
        }
        
    }    
    
    /**
    * @desc This function copies a profile
    */
    function copyProfile($target,$copy_data=false) {
        
        # first check if we've provided a new name
        if ($this->profilename==$target) {
            echo "You must provide a different name!";
            exit();                
        }
        
        # get rid of the profileid
        unset($this->profileid);
        
        # change the name        
        $this->profilename=$target;
        
        # remember the old tablename in case we need to copy the data
        $source = $this->tablename;
        
        # change the tablename
        $this->tablename=$target;
        
        # save it
        $this->_privateSave(true);
        
        # copy data or just create tables
        if ($copy_data==true) {
            $this->copyDataTables($source,$target);    
        } else {
            $newprofile = new SiteProfile($target);
            createDataTable($newprofile);
        }          
    }
    
    
    /**
    * @desc This function makes a copy of all of a profiles mysql tables
    */
    function copyDataTables($source,$target) {
        global $db;
        set_time_limit(86400);
        ob_start();
                
        # first copy the main table
        
        $q = "CREATE TABLE $target LIKE $source";
        $db->Execute($q);
        echo "<hr>".$q."<br>"; lgflush();
        $this->copyTable($source,$target);
        
        
        //$q = "ALTER TABLE $target DISABLE KEYS";
        //$db->Execute($q);
        //echo "<hr>".$q."<br>"; lgflush(); 
        
        //$this->copyTable($source,$target);      
        
        //$q = "ALTER TABLE $target ENABLE KEYS";
        //$db->Execute($q);
        //echo "<hr>".$q."<br>"; lgflush();        
        //exit();
        # now get the other tables and copy them
        echo "show tables like '{$source}\_%'";
        $tables = $db->Execute("show tables like '{$source}\_%'");
        while ($table=$tables->FetchRow()) {
            # old table name
            $ot = $table[0];
            # new table name
            $nt = str_replace($source,$target,$ot);
            # copy it
            $q = "CREATE TABLE $nt LIKE $ot";
            $db->Execute($q);
            echo "<hr>".$q."<br>"; lgflush();
            
            $this->copyTable($ot,$nt);
            
            //$q = "ALTER TABLE $nt DISABLE KEYS";
            //$db->Execute($q);
            //echo "<hr>".$q."<br>";lgflush();  
            
            //$q = "INSERT INTO $nt SELECT * FROM $ot";
            //$db->Execute($q);
            //echo "<hr>".$q."<br>";lgflush();
            
            //$q = "ALTER TABLE $nt ENABLE KEYS";
            //$db->Execute($q);
            //echo "<hr>".$q."<br>";lgflush();    
        }
    }
        
    function copyTable($source,$target) {
        global $db;
        $q = "SELECT COUNT(*) FROM $source";
        $result = $db->Execute($q);
        $data = $result->FetchRow();
        $rows = $data[0];           
        $i=0;
        $limit=50000;
        while ($i <= $rows) {
            echo "inserting $i to ".($i+$limit)." in $target...<br>\n";
            $db->Execute("INSERT INTO $target SELECT * FROM $source limit $i,$limit"); lgflush();
            $i=$i+$limit;                            
        }
            
    }
	function setDefaultDashboard() {
		$value = '{"name":"Default","description":"This is an default desktop","icon":"00","startup":1,"reports":[[{"label":"_TODAY_BOX","name":"Today Overview","url":"reports.php?conf='.$this->profilename.'&labels=_TODAY_BOX&gname=months"},{"label":"_VISITORS_PER_DAY","name":"Visitors per Day","url":"reports.php?conf='.$this->profilename.'&labels=_VISITORS_PER_DAY&trafficsource=0"},{"label":"_TOP_KEYWORDS","name":"Top Keywords","url":"reports.php?conf='.$this->profilename.'&labels=_TOP_KEYWORDS&trafficsource=0&searchmode=like&search=&limit=10"}],[{"label":"_OVERALL_PERFORMANCE","name":"Overall Performance","url":"reports.php?conf='.$this->profilename.'&labels=_OVERALL_PERFORMANCE&trafficsource=0"},{"label":"_TOP_COUNTRIES_CITIES","name":"Top Countries \/ Cities","url":"reports.php?conf='.$this->profilename.'&labels=_TOP_COUNTRIES_CITIES&trafficsource=0&limit=10"},{"label":"_TOP_REFERRERS","name":"Top Referrers","url":"reports.php?conf='.$this->profilename.'&labels=_TOP_REFERRERS&trafficsource=0&searchmode=like&search=&limit=10"},{"label":"_TOP_PAGES","name":"Top Pages","url":"reports.php?conf='.$this->profilename.'&labels=_TOP_PAGES&trafficsource=0&searchmode=like&search=&limit=10"}]]}';
		// $value = '{"name":"Default","description":"This is an default desktop","icon":"00","startup":0,"reports":[[{"label":"_TODAY_BOX","name":"Today Overview","classname":"todayoverview","reportOptions":{"gname":"months"}},{"label":"_VISITORS_PER_DAY","name":"Visitors per Day","classname":"visitorsperday","reportOptions":{"trafficsource":"0"}},{"label":"_TOP_KEYWORDS","name":"Top Keywords","classname":"topkeywords","reportOptions":{"trafficsource":"0","searchmode":"like","search":"","limit":"10"}}],[{"label":"_OVERALL_PERFORMANCE","name":"Overall Performance","classname":"overallperformance","reportOptions":{"trafficsource":"0","searchmode":"like","search":""}},{"label":"_TOP_PAGES","name":"Top Pages","classname":"toppages","reportOptions":{"trafficsource":"0","searchmode":"like","search":"","limit":"10"}},{"label":"_TOP_REFERRERS","name":"Top Referrers","classname":"topreferrers","reportOptions":{"trafficsource":"0","searchmode":"like","search":"","limit":"10"}},{"label":"_TOP_COUNTRIES_CITIES","name":"Top Countries \/ Cities","classname":"topcountriescities","reportOptions":{"trafficsource":"0","limit":"10"}}]]}';
		// $value = '{"name":"Default","description":"This is an default desktop","icon":"00","startup":"1","reports":[[{"name":"Today Overview","classname":"todayoverview","reportOptions":{"gname":"months"}},{"name":"Visitors per Day","classname":"visitorsperday","reportOptions":{"trafficsource":"0"}},{"name":"Top Keywords","classname":"topkeywords","reportOptions":{"trafficsource":"0","searchmode":"like","search":"","limit":"10"}}],[{"name":"Overall Performance","classname":"overallperformance","reportOptions":{"trafficsource":"0","searchmode":"like","search":""}},{"name":"Top Pages","classname":"toppages","reportOptions":{"trafficsource":"0","searchmode":"like","search":"","limit":"10"}},{"name":"Top Referrers","classname":"topreferrers","reportOptions":{"trafficsource":"0","searchmode":"like","search":"","limit":"10"}},{"name":"Top Countries / Cities","classname":"topcountriescities","reportOptions":{"trafficsource":"0","limit":"10"}}]]}';
		setProfileData($this->profilename,$this->profilename.".dashboards.Default", $value);
	}
    
}
?>