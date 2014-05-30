<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2012 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 

class Import {
	
	function __construct() {
		global $profile;
		
		$this->print = 1;
		
		if(empty($profile->logfilefullpath)) {
			$logdir = getGlobalSetting("upload_dir", logaholic_dir()."files/");
			$logdir .= "{$profile->profilename}/";
		} else {
			$logdir = $profile->logfilefullpath;
		}
		$this->logdir = $logdir;
		$this->totaltime=0;
		$this->manage_keys=false;
	}
	
	function AvailableCheck() {
		# In this function we check if everything we need to do a perl update is available.
		
		global $mysqltmp;
		
		$return = array(
			'hasError' => false
		);
		
		# We don't support Windows machines.
		if(strpos(strtolower($_SERVER['SERVER_SOFTWARE']), 'win32') !== false || strpos(strtolower($_SERVER['SERVER_SOFTWARE']), 'win64') !== false) {
			$return[] = _PERL_UPDATE_NOT_AVAILABLE_FOR_WINDOWS;
			$return['hasError'] = true;
			return $return;
		}
		
		# Let's check if Perl exists.
		$perl_script = logaholic_dir()."components/import/perlneeds.plx";
		
		# The command to run
		$cmd = "perl {$perl_script}";
		
		#Run the command
		exec($cmd, $output, $status);
		
		if(!empty($output)) {
			// $return[] = $output[0];
			unset($output);
		} else {
			$return[] = _PERL_NOT_INSTALLED_INSTALL_ACTIVEPERL;
			$return['hasError'] = true;
		}
		
		# Let's check if we have the Time::Local module.
		exec("perl -MTime::Local -e 1", $output, $status);
		
		if($status != 0) {
			$return[] = _TIME_LOCAL_PERL_MODULE_NOT_INSTALLED;
			$return['hasError'] = true;
		} else {
			// $return[] = _TIME_LOCAL_PERL_MODULE_INSTALLED;
		}
		
		# Let's check if we have the Digest::MD5 module.
		exec("perl -MDigest::MD5 -e 1", $output, $status);
		
		if($status != 0) {
			$return[] = _DIGEST_MD5_PERL_MODULE_NOT_INSTALLED;
			$return['hasError'] = true;
		} else {
			// $return[] = _DIGEST_MD5_PERL_MODULE_INSTALLED;
		}
		
		# Let's check if we have the PerlIO::gzip module.
		exec("perl -MPerlIO::gzip -e 1", $output, $status);
		
		if($status != 0) {
			$return[] = _PERLIO_GZIP_PERL_MODULE_NOT_INSTALLED;
			$return['hasError'] = true;
		} else {
			// $return[] = _PERLIO_GZIP_PERL_MODULE_INSTALLED;
		}
		
		# Let's check if we have the Geo::IP module.
		exec("perl -MGeo::IP -e 1", $output, $status);
		if($status != 0) {
			$return[] = _GEOIP_PERL_MODULE_NOT_INSTALLED;
			$return['hasError'] = true;
		} else {
			// $return[] = _GEOIP_PERL_MODULE_INSTALLED;
		}
		
		# Let's check if the temporary files directory exists...
		if(is_dir($mysqltmp)) {
			# ... and if it's writable
			if(is_writable($mysqltmp)) {
				// $return[] = str_replace("%s", $mysqltmp, _MYSQLTMP_DIR_WRITABLE);
			} else {
				$return[] = str_replace("%s", $mysqltmp, _MYSQLTMP_DIR_NOT_WRITABLE);
				$return['hasError'] = true;
			}
		} else {
			$return[] = str_replace("%s", $mysqltmp, _MYSQLTMP_DIR_NONEXISTING);
			$return['hasError'] = true;
		}
		
		return $return;
	}
	
	function WriteConfigFile($config_file = '', $log_file = '', $new_log_file = '') {
		global $profile, $force;
		
		# The config file will be written in the /files/ directory, as {$profilename}_config
		
		# Important params should strip important param from the urlparams and refparams
		# Then, the removed part should be pasted to the corresponding url
		$important_params = '';
		$c = 0;
		foreach($profile->importantURLParams as $param) {
			if($c > 0) { $important_params .= ','; }
			$important_params .= $param['filename'].'::'.$param['importantparams'];
			$c++;
		}
		
		# Set time where to start parsing
		$skiptime = $this->getStartTimeForProfile();
		
		$fh = fopen($config_file, 'w+');
		
		# Set the input file (log file) and output file (preparsed log)
		fwrite($fh, "inputfile=".$log_file."\n");
		fwrite($fh, "outputfile=".$new_log_file."\n");
		
		# get the last log position
		$lastpos = getProfileData($profile->profilename, "lastlogpos.".md5($log_file), 0);
		
		# Set force, if needed
		if(isset($force) && $force == true) {
			fwrite($fh, "force=1\n");
		}
		
		$format = formatOfLogFile($log_file);
		fwrite($fh, "logformat=".$format['ClassName']."\n");
		
		# Set the visitorid method
		if ($profile->visitoridentmethod == VIDM_IPADDRESS) {
			$visitorid_method = 'VIDM_IPADDRESS';
		} else if ($profile->visitoridentmethod == VIDM_IPPLUSAGENT) {
			$visitorid_method = 'VIDM_IPPLUSAGENT';
		} else if ($profile->visitoridentmethod == VIDM_COOKIE) {
			$visitorid_method = 'VIDM_COOKIE';
		}
		
		# Add equivalent domains to the config file
		if(substr($profile->equivdomains, 0, 1) == '(') {
			$equivdomains = $profile->equivdomains;
		} else {
			$equivdomains = str_replace(" ", "", str_replace(",", "|", $profile->equivdomains));
			$equivdomains = explode("|", $equivdomains);
			foreach($equivdomains as $key => $val) {
				$equivdomains[$key] = '://'.$val;
			}
			$equivdomains = implode("|", $equivdomains);
		}
		
		# Write the settings to the config file
		fwrite($fh, "confdomain={$profile->confdomain}\n");
		fwrite($fh, "equivdomains=".$equivdomains."\n");
		fwrite($fh, "vidm={$visitorid_method}\n");
		fwrite($fh, "lastpos={$lastpos}\n");
		fwrite($fh, "skiptime={$skiptime}\n");
		fwrite($fh, "googleparams=".str_replace(" ", "", str_replace(",", "|", $profile->googleparams))."\n");
		fwrite($fh, "skipfiles=".str_replace(" ", "", str_replace(",", "|", $profile->skipfiles))."\n");
		fwrite($fh, "skipips=".str_replace(" ", "", str_replace(",", "|", $profile->skipips))."\n");
		fwrite($fh, "importantparams={$important_params}\n");
		fwrite($fh, "urlparamfiltermode={$profile->urlparamfiltermode}\n");
		fwrite($fh, "urlparamfilter={$profile->urlparamfilter}\n");
		
		fclose($fh);
	}
	
	# This function fetches the maximum timestamp from the data existing in the profile's main table and returns it.
	function getStartTimeForProfile() {
		global $profile, $db, $skiptime;
		if (isset($skiptime)) {
			return $skiptime;
		}
		
		$q = $db->Execute("SELECT MAX(`timestamp`) FROM {$profile->tablename}");
		$skiptime = $q->FetchRow();
		if(!empty($skiptime)) {
			$skiptime = $skiptime[0];
		} else {
			$skiptime = 0;
		}
		
		return $skiptime;
	}
	
	# This function returns an array containing all full paths to each log file we need to parse.
	function GetLogFilesArray() {
		global $profile, $skiptime;
		
		$logfiles = array();
		
		# If we handle more than one log file
		if($profile->splitlogs == 1) {
			# We open the directory
			$handle = opendir($this->logdir);
			$ls = array();
			
			# While we have files in the directory...
			while ($file = readdir($handle)) {
				# ... we paste the directory and filename together ...
				if(substr($profile->logfilefullpath, -1) != "/") {
					$filename = $profile->logfilefullpath."/".$file;
				} else {
					$filename = $profile->logfilefullpath.$file;
				}
				
				# ... and get the filename and modification date for each file.
				if ($file[0] != '.') {
					$rfn = "{$filename}";
					$mt = filemtime($rfn);
					# now lets see if we've already analyzed this file
					$lastmodtime = getProfileData($profile->profilename, "lastmodtime.".md5($filename), 0);
					if ($lastmodtime==$mt) {
						# this file has probably been done, skip it
						continue;
					}
					$ls[$file] = $mt;					
				}			
				
			}
			
			if (!isset($ls)) {
				closedir($handle);
				return;
			}
			
			# Sort by filename
			asort($ls);
			
			$skiptime = $this->getStartTimeForProfile();
			
			foreach($ls as $file => $modtime) {
				# If the modification time is lower than the start time of data, we are looking at an old log file; skip it.
				if($modtime <= $skiptime) {
					continue;
				}
				
				# We did this already in the above code, but apparently, it's needed to do so again.
				if(substr($profile->logfilefullpath, -1) != "/") {
					$filename = $profile->logfilefullpath."/".$file;
				} else {
					$filename = $profile->logfilefullpath.$file;
				}
				
				if ($profile->splitfilternegative) {
					if (strpos($file, $profile->splitfilternegative) !== FALSE) {
						continue;
					}
				}
				
				if ($profile->splitfilter) {
					if (strpos($profile->splitfilter, '(') !== FALSE) {
						# we have a regex splitfilter
						if (preg_match("/".$profile->splitfilter."/i", $file)) {
							# it matched, so we want to analyze it
						} else {
							# It doesn't match; skip it.
							continue;
						}
					} else if (strpos($file, $profile->splitfilter) === FALSE) {
						continue;
					}
				}
								
				# In Perl, we can read both zipped files as non-zipped files.
				# However, the code below might be needed sometime again, so it's commented.
				
				if (substr($filename, -3, 3) == ".gz") {
					// // exec("gzip --stdout -d {$filename} > /tmp/lwa_{$file}.log;");
					// // $logfiles[] = "/tmp/lwa_{$file}.log";
					$logfiles[] = $filename;
				} else {
					$logfiles[] = $filename;
				}
			}
		} else {
			# We only have one log file, so we return an array with 1 entry.
			$logfiles[] = $profile->logfilefullpath;
		}
		
		return $logfiles;
	}
	
	# This is where we load the preparsed log file into a temprary table.
	function ImportLog($file) {
		global $db, $profile, $log_parser_types, $skiptime;
		$start=time();
		
		$this->fsize = round(((filesize($file)/1024)/1024),2);
		if ($this->manage_keys === true) {
			$this->LogProcess("starting import with manage_keys ON");
		} else {
			$this->LogProcess("starting import with manage_keys off");
		}
		
		//$this->LogProcess(_IMPORTING_LOG_FILE_INTO_DATABASE." ($this->fsize mb) ...");
		
		# This function creates a mysql function for URL decoding.
		// mysql_urldecode();
		
		# If the log table already exists, delete it; we want a clean one.
		$db->Execute("DROP TABLE IF EXISTS {$profile->tablename}log");
		
		# Create the log table.
		$db->Execute("CREATE TABLE `{$profile->tablename}log` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `host` varchar(255) DEFAULT NULL,
			  `logname` varchar(45) DEFAULT NULL,
			  `user` varchar(45) DEFAULT NULL,
			  `timestamp` int(11) DEFAULT NULL,
			  `timezone` varchar(7) DEFAULT NULL,
			  `request` text,
			  `status` varchar(4) DEFAULT NULL,
			  `bytes` varchar(10) DEFAULT NULL,
			  `referrer` text,
			  `useragent` text,
			  `useragent_hash` varchar(32) DEFAULT NULL,
			  `cookie` text,
			  `urlparams` text,
			  `refparams` text,
			  `keywords` text,
			  `visitorid` varchar(32) DEFAULT NULL,
			  `country` varchar(100) DEFAULT NULL,
			  `crawl` int(1) DEFAULT '0',
			  `sessionid` int(11) DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  KEY `visitortime` (`visitorid`,`timestamp`)
			) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8
		");
		
		# We are running Perl Update, so we force it to use LogaholicLogformatParser
		$log_format["ClassName"] = "LogaholicLogformatParser";
		$log_parser = new $log_format["ClassName"];
		$log_parser->Initialize($file);
	
		# Load the parser query...
		$import_query = $log_parser->ImportQuery($file);
		
		# ... and run it!
		$db->Execute($import_query);
		$this->num_import_lines = $db->Affected_Rows();
		# Do some Log parser clean up, if needed
		$log_parser->CleanUp();
		
		$took = $this->took($start);
			
		$this->LogProcess("Imported {$this->num_import_lines} records from the log file ($this->fsize mb) in ".$this->sec2time($took));
		
		$this->StopOrContinue();

	}
	
	# This inserts elements like urls and keywords into the subtables
	function insertIDs() {
		global $db, $profile;
		
		if ($this->num_import_lines < 1) {
			return;
		}
		
		$tables = $this->getTables();
		
		foreach ($tables as $t) {
			$start = time();
						
			# Skip visitorIDs
			if($t['table'] == $profile->tablename_visitorids) { continue; }
			
			//$rows = $this->NumRows($t['table']);
			
			# disable the keys to speed up the insert
			//$this->EnableKeys($t['table'], false);
			
			//6$this->LogProcess(_INSERTING_IDS_INTO_DATABASE."(".$t['table'].")...");
						
			# insert missing items
			//$db->Execute("INSERT IGNORE INTO {$t['table']} ({$t['field']}, `hash`) (SELECT DISTINCT {$t['sfield']}, MD5({$t['sfield']}) FROM `{$profile->tablename}log` AS s LEFT JOIN {$t['table']} AS u ON MD5({$t['sfield']}) = u.hash WHERE u.id IS NULL)");
			//Just do insert ignore, it's faster
			$select = "SELECT DISTINCT {$t['sfield']}, MD5({$t['sfield']}) FROM `{$profile->tablename}log` AS s";
			$db->Execute("INSERT IGNORE INTO {$t['table']} ({$t['field']}, `hash`) ($select)");
			$this->LogProcess($select);
			//$this->EnableKeys($t['table'], true);
			
			# calculate how many we inserted
			//$rows = $this->NumRows($t['table']) - $rows;
			//$this->LogProcess("inserted $rows");
			$rows = $db->Affected_Rows();
			$took = $this->took($start);
			$this->LogProcess("Inserting $rows records in {$t['table']} took ".$this->sec2time($took));			
			$this->StopOrContinue();
		}		
	}
	
	# This inserts the visitorids into the visitors table
	function insertVisitorIDs() {
		global $db, $profile;
		$start = time();
		
		if ($this->num_import_lines < 1) {
			return;
		}
		
		//$this->LogProcess(_INSERTING_VISITORIDS."...");
		
		# Detect the visitorid identification method
		switch($profile->visitoridentmethod) {
			case VIDM_IPADDRESS:
				$selection_method = " MD5(s.host) ";
				
				break;
			case VIDM_IPPLUSAGENT:
				$selection_method = " MD5(CONCAT(s.host,':',s.useragent)) ";
				
				break;
			case VIDM_COOKIE:
				$selection_method = "IF(LOCATE('NewLogaholic_VID=', `cookie`), MD5(SUBSTRING_INDEX(SUBSTRING_INDEX(`cookie`, 'NewLogaholic_VID=', -1), ';', 1)), MD5(CONCAT(s.host,':',s.useragent)))";
				
				break;
		}
		
		# Insert the visitorid into the visitor ID's table
		//$sql = "INSERT IGNORE INTO {$profile->tablename_visitorids} (`visitorid`, `hash`, `ipnumber`, `created`) (SELECT DISTINCT {$selection_method} AS vid, MD5({$selection_method}), s.host, MIN(s.`timestamp`) FROM `{$profile->tablename}log` AS s LEFT JOIN {$profile->tablename_visitorids} AS u ON MD5({$selection_method}) = u.hash WHERE u.id IS NULL GROUP BY vid)";
		//$this->EnableKeys($profile->tablename_visitorids, false);
		$sql = "INSERT IGNORE INTO {$profile->tablename_visitorids} (`visitorid`, `hash`, `ipnumber`, `created`) (SELECT DISTINCT {$selection_method} AS vid, MD5({$selection_method}), s.host, MIN(s.`timestamp`) FROM `{$profile->tablename}log` AS s GROUP BY vid)";
		// dump($sql);
		// exit();
		$db->Execute($sql);
		//$this->EnableKeys($profile->tablename_visitorids, true);
		# calculate how many we inserted
		$rows = $db->Affected_Rows();
		$took = $this->took($start);
		$this->LogProcess("Inserting $rows records in $profile->tablename_visitorids took ".$this->sec2time($took));			
		$this->StopOrContinue();
	}
	
	function AlterTables() {
		# this should never happen
		// global $db, $profile;
		
		// $tables = $this->getTables();
		
		// # Update all the profile's tables, so we have hash columns in each table.
		// foreach ($tables as $t) {
			// $db->Execute("ALTER TABLE {$t['table']} ADD COLUMN `hash` CHAR(32)");
			// $db->Execute("UPDATE {$t['table']} SET `hash` = MD5({$t['field']})");
			// $db->Execute("ALTER TABLE {$t['table']} ADD INDEX (hash)");
		// }
		// $db->Execute("ALTER TABLE {$profile->tablename_visitorids} CHANGE COLUMN `ipnumber` `ipnumber` VARCHAR(255)");
	}
	function AlterTablesAgain() {
		
		global $db, $profile;
		
		$tables = $this->getTables();
		
		# Update all the profile's tables, so we have hash columns in each table.
		foreach ($tables as $t) {
			
			$db->Execute("ALTER TABLE {$t['table']} DROP INDEX {$t['table']}_{$t['field']}");
			$db->Execute("ALTER TABLE {$t['table']} DROP INDEX hash");
			$db->Execute("create unique index hash on {$t['table']} (hash)");
			$this->LogProcess("changed index on  {$t['table']}");
			
		}
		# now do the main table indexes
		$db->Execute("ALTER TABLE $profile->tablename DROP INDEX visitorid");
		$db->Execute("ALTER TABLE $profile->tablename DROP INDEX url");
		$db->Execute("ALTER TABLE $profile->tablename DROP INDEX referrer");
		$db->Execute("ALTER TABLE $profile->tablename DROP INDEX keywords");
	}
	
	function getTables() {
		global $profile, $took, $totaltime;
		
		# Get an array containing all needed tables for the current profile.
		$tables = array();
		$tables[] = array('table' => $profile->tablename_urls , 'field' => 'url', 'sfield' => 's.request');
		$tables[] = array('table' => $profile->tablename_urlparams , 'field' => 'params', 'sfield' => 's.urlparams');
		$tables[] = array('table' => $profile->tablename_referrers , 'field' => 'referrer', 'sfield' => 's.referrer');
		$tables[] = array('table' => $profile->tablename_refparams , 'field' => 'params', 'sfield' => 's.refparams');
		$tables[] = array('table' => $profile->tablename_keywords , 'field' => 'keywords', 'sfield' => 's.keywords');
		$tables[] = array('table' => $profile->tablename_useragents , 'field' => 'useragent', 'sfield' => 's.useragent');
		$tables[] = array('table' => $profile->tablename_visitorids , 'field' => 'visitorid', 'sfield' => "md5(CONCAT(s.host,':',s.useragent))");
		
		return $tables;
	}
	
	function insertNormalized() {
		global $profile, $db, $mysqltmp, $skiptime;
		
		if ($this->num_import_lines < 1) {
			return;
		}
		
		$start = time();
		
		//$this->LogProcess(_NORMALIZING_DATA."...");
		
		
		# Insert all ID's into the profile's main table, from all other tables.
		$q  = "INSERT INTO {$profile->tablename} (timestamp, visitorid, url, params, status, bytes, country, crawl, sessionid, referrer, refparams, useragentid, keywords) ";
		$q .= "SELECT `timestamp`, v.id, u.id, up.id, status, bytes, country, crawl, sessionid, r.id, rp.id, ua.id, k.id FROM `{$profile->tablename}log` AS log, "; 
		$q .= "{$profile->tablename_visitorids} as v,";
		$q .= "{$profile->tablename_urls} as u,";
		$q .= "{$profile->tablename_urlparams} as up,";
		$q .= "{$profile->tablename_referrers} as r,";
		$q .= "{$profile->tablename_refparams} as rp,";
		$q .= "{$profile->tablename_keywords} as k,";
		$q .= "{$profile->tablename_useragents} as ua ";
		$q .= "WHERE MD5(MD5(concat(log.host,':',log.useragent))) = v.hash AND "; 
		$q .= "MD5(log.request) = u.hash AND MD5(log.urlparams) = up.hash AND ";
		$q .= "MD5(log.referrer) = r.hash AND MD5(log.refparams) = rp.hash AND ";
		$q .= "MD5(log.keywords) = k.hash AND MD5(log.useragent) = ua.hash ";
		//$q .= "INTO OUTFILE '{$mysqltmp}/{$profile->tablename}.normalized'";
		$db->Execute($q);		
		//$db->Execute("LOAD DATA INFILE '{$mysqltmp}/{$profile->tablename}.normalized' INTO TABLE {$profile->tablename}");
		
		$rows = $db->Affected_Rows();
		$took = $this->took($start);
		$this->LogProcess("Inserting $rows records in $profile->tablename took ".$this->sec2time($took));					
		
		$this->StopOrContinue();
		$q = $db->Execute("SELECT MAX(`timestamp`) FROM {$profile->tablename}");
		$skiptime = $q->FetchRow();
	}
	
	# This will turns all non-unique mysql indexes off for the table. It makes inserting a lot of rows faster.
	function EnableKeys($table, $enable) {
		global $db;
		if ($this->manage_keys===false) {
			return;
		}
		if (!isset($enable)) {
			return;
		}		
		if ($enable==true) {
			$start = time();
			# enable keys when we are done inserting
			$db->Execute("ALTER TABLE {$table} ENABLE KEYS");
			$took = time() - $start;
			$this->LogProcess("Enabling index on $table took {$took}");
		} else {
			# disable the keys to speed up the insert
			$db->Execute("ALTER TABLE {$table} DISABLE KEYS");
		}	
	}
	
	function thisTook($start, $update_total = true) {		
		
		$took = $this->took($start);
		
		$tot="";
		if ($update_total == true) {
			$this->totaltime = $this->totaltime + $took;
			$tot_dur = $this->sec2time($this->totaltime);
			$tot = ". Total processing time is now {$tot_dur}";
		}		
		$duration = $this->sec2time($took);
				
		$this->LogProcess("This took $duration $tot", false);
		
		return time();
	}
	
	# this calculates how many seconds have passed since the start time
	function took($start) {				
		return time() - $start;		
	}
	
	# This converts a number of seconds to a pretty hours, minutes, seconds string
	function sec2time($secs) {
		$hours = str_pad(floor($secs / (60 * 60)),2,'0',STR_PAD_LEFT);
		$divisor_for_minutes = $secs % (60 * 60);
		$minutes = str_pad(floor($divisor_for_minutes / 60),2,'0',STR_PAD_LEFT);
		$divisor_for_seconds = $divisor_for_minutes % 60;
		$seconds = str_pad(ceil($divisor_for_seconds),2,'0',STR_PAD_LEFT);
		return "{$hours}:{$minutes}:{$seconds} ($secs secs)";
	}
	
	# This function prints stuff to the update progress log.
	function LogProcess($message, $include_took = false) {
		global $profile, $updatelog, $took, $totaltime, $human_readable_file;
		
		# If the message that will be returned starts with [Finished], we want to prepend this to the message, and remove it from the message.
		# This is so we then can detect it in the ajax request that reads the update progress log.
		if(substr($message, 0, 10) == '[Finished]') {
			$message = "[Finished]".$human_readable_file.": ".str_replace("[Finished]", "", $message);
		} else {
			$message = $human_readable_file.": ".$message;
		}
		
		# Write the line to the update progress log.
		fwrite($updatelog, $message."\n");
		// echo $message."<br/>";
		lgflush();			
		
	}
	
	# This function prints stuff to the log we use to analyze the spped of this fucker.
	function LogDuration($label, $speed, $secs) {
		global $profile, $human_readable_file;
		if (!isset($this->durationlog)) {
			$this->durationlog = fopen(logaholic_dir()."files/{$profile->profilename}_update_duration.lwa.log", "a+");
		}
		$message = $human_readable_file.", ".@$this->fsize.", ".@$this->num_import_lines.", ".$label.", ".$speed.", ".$secs;
		# Write the line to the update duration log.
		fwrite($this->durationlog, $message."\n");	
		
	}	
	
	# This function will stop the update process if a user has remotely turned on the stop flag (via UI)
	function StopOrContinue() {
		global $profile;
		if (getProfileData($profile->profilename, "{$profile->profilename}.stop_update", false) == 1) {
			$this->LogProcess("Error: user stopped process", false);
			# reset the stop flag
			setProfileData($profile->profilename, "{$profile->profilename}.stop_update", false);
			# We are done with Perl update; unflag it.
			setProfileData($profile->profilename, "{$profile->profilename}.perlupdate_running", 'no');			
			die();
		} else {
			return true;
		}
	}
	
	# This function sets the corrected profile table name for this update job.
	# we need this when we are using merge tables
	function correctProfileTablename() {
		global $db, $profile;
		
		if (isset($this->real_profile_tablename)) {
			$profile->tablename = $this->real_profile_tablename;
			return;
		}
				
		$query  = "SHOW CREATE TABLE $profile->tablename";
		$q= $db->Execute($query);
		if ($data = $q->FetchRow()) {
			$createtable = $data['Create Table'];
			if (strpos($createtable,"UNION")!==false) {
				$this->real_profile_tablename = $profile->tablename;
				$profile->tablename = $profile->tablename."_current";
			}
		} else {
			echoDebug("error for query: $query");
			return false;  
		}
			
	}

}
?>