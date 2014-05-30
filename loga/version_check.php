<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/
set_time_limit(1800);
 
	include_once "common.inc.php";

	/* ---------------------------------------------------------------------------------------------------------------------------------------------------------------- */
	/* ---------------------------------------------------------------------------------------------------------------------------------------------------------------- */
	/* --- profile table updates (profiloe structure_version) ------------------------------------------------------------------------------------------------------------------------------------------------------------- */
	/* ---------------------------------------------------------------------------------------------------------------------------------------------------------------- */
	/* ---------------------------------------------------------------------------------------------------------------------------------------------------------------- */
	

	/**
	* If a data table doesn't have the right fields (or the wrong types), then let's "fix it", by moving
	* columns or whatever is necessary.
	* 
	* This function assumes that the "TBL_USER_AGENTS" table exists already.
	* @param SiteProfile The profile that needs to be updated.
	**/
	function updateDataTableForProfile($profiletoupdate) {
		global $db, $profile, $databasedriver;
		
		// Make sure there's something to update.
		if ($profiletoupdate->structure_version >=  CURRENT_PROFILE_STRUCTURE_VERSION) {
		  return;
		}
		
		echoWarning("Updating Profile ".$profiletoupdate->profilename." from structure version $profiletoupdate->structure_version to version ".CURRENT_PROFILE_STRUCTURE_VERSION,"clear:both;");
		flush();
		
		// Create the index prefix once for easy later reference.
		if ($databasedriver == "mysql") {
			$index_name_pre = "";
            
		} else {
			$index_name_pre = $profiletoupdate->tablename . "_";
             
		}
		
		$columns = $db->MetaColumns($profiletoupdate->tablename);
		if ($profiletoupdate->structure_version < 2.00) {
			// Add a new column for the agent id.
			echo "<p>&nbsp;&nbsp;&nbsp;Normalizing user agent data..</p>";
			flush();
		
		  if (!isset($columns["USERAGENTID"])) {
				$db->Execute("alter table `".$profiletoupdate->tablename."` add column useragentid integer");
			}
			// Insert records for each agent in use that doesn't already exist in the database
			$db->Execute("INSERT ".($databasedriver == "sqlite" ? "OR " : "")." IGNORE INTO `".TBL_USER_AGENTS."` (name) SELECT distinct useragent from ".$profiletoupdate->tablename);
			
			// Update the table with the proper user agent id.
			$db->Execute("UPDATE `".$profiletoupdate->tablename."` P, `".TBL_USER_AGENTS."` A set P.useragentid = A.id where P.useragent = A.name and P.useragent is not null");
			$db->Execute("alter table `".$profiletoupdate->tablename."` drop column useragent");
			$profiletoupdate->structure_version = 2.00;
		  $profiletoupdate->Save();  
		}
		
		if ($profiletoupdate->structure_version < 2.01) {
			// Add new column for visitor id.
			echo "<p>&nbsp;&nbsp;&nbsp;Adding visitor id column (and creating the visitor ID based on ip address)..</p>";
			flush();
			
		  if (!isset($columns["VISITORID"])) {
				$db->Execute("alter table `".$profiletoupdate->tablename."` add column visitorid char(32)");
			}
			$db->Execute("update ".$profiletoupdate->tablename." set visitorid = md5(ipnumber)");
			if (!@$db->Execute("CREATE INDEX ".$index_name_pre."visitorid ON ".$profiletoupdate->tablename."(visitorid)")) {
				echo "<p>".$db->ErrorMsg()."</p>";
			};

			// Summarry tables need to be flushed and the conversion table needs to be changed from IP to VisitorID
			$meta_tables = $db->MetaTables();
			if (in_array_insensitive($profiletoupdate->tablename_conversions, $meta_tables)) {
				$db->Execute("drop table ".$profiletoupdate->tablename_conversions);
				
				$db->Execute("CREATE TABLE $profiletoupdate->tablename_conversions (
					timestamp int(11) NOT NULL default '0',
					visitorid varchar(32) NOT NULL default '0',
					url varchar(255) NOT NULL default '0'
				) ENGINE=MyISAM CHARSET=utf8");
				$db->Execute("CREATE INDEX {$profiletoupdate->tablename_conversions}_timestamp on {$profile->tablename_conversions}(timestamp)");
				
				$db->Execute("delete from  ".$profiletoupdate->tablename_vpd);
				$db->Execute("delete from  ".$profiletoupdate->tablename_vpm);
			}
			
			$profiletoupdate->structure_version = 2.01;
		  $profiletoupdate->Save();  
		}
			
		if ($profiletoupdate->structure_version < 2.02) {
			// Add new column for authuser
		  if (!isset($columns["AUTHUSER"])) {
				$db->Execute("alter table `".$profiletoupdate->tablename."` add column authuser varchar(80)");
			}
			$profiletoupdate->structure_version = 2.02;
		  $profiletoupdate->Save();  
		}
        if ($profiletoupdate->structure_version < 2.03) {
            // Add new column for sessionid
          if (!isset($columns["SESSIONID"])) {
                $db->Execute("alter table `".$profiletoupdate->tablename."` add column sessionid char(32)");
          }
          $profiletoupdate->structure_version = 2.03;
          $profiletoupdate->Save();  
        }
        
        if ($profiletoupdate->structure_version < 2.04) {
            // warn people for now
          //echo "<P><b><font color=red>Warning:</font> This profile contains a data tables in the old format (Prior to this beta version). You will need to <a href=profiles.php?editconf=$profiletoupdate->profilename&del=1>delete the old mysql data</a> table and reimport your log file. If you don't have your original log files, we recommend to reinstall this Logaholic beta version in a new databse, and keep your old install untill the final version is released (which will convert your datatabes automatically).</b><P>";
          echo "<P><b><font color=red>Warning, do not close screen. Your action required now:</font> This profile contains a data tables in the old format (Prior to this beta version). You will need upgrade the database tables and reimport your log data.</b><P>
          <ul> <LI>If you still have the original log files, you can simply delete the existing database tables and click Update now to reimport them in the new database stucture:<br><a href=\"profiles.php?editconf=$profiletoupdate->profilename&del=4&upgrade=true\">Yes, I still have the log files</a>.<P><li>If you DON'T have the original log files, please choose the link below to backup you current  data to a log file first.<P>If you have a lot of data, it might take quite a while to export it, so please be patient. A temporary file will be created in the logaholic/files directory, make sure you have sufficient disk space.<br><a href=\"profiles.php?editconf=$profiletoupdate->profilename&backup=2&upgrade=true\">Begin Backup/Upgrade procedure</a></ul>";
          $profiletoupdate->structure_version = 2.04;
          $profiletoupdate->Save();
          exit();
            
        }
        
        if ($profiletoupdate->structure_version < 2.05) {
          @$db->Execute("rename table ".$profiletoupdate->tablename."_keywords to $profiletoupdate->tablename_keywords");
          $profiletoupdate->structure_version = 2.05;
          $profiletoupdate->Save();
            
        }
        
        if ($profiletoupdate->structure_version < 2.06) {
          echo "If you have a lot of data, this can take a while, please wait.";
          @flush();
          @ob_flush();
          $db->Execute("alter table ".$profiletoupdate->tablename_urls." drop index ".$profiletoupdate->tablename_urls."_url");
          $db->Execute("ALTER IGNORE TABLE ".$profiletoupdate->tablename_urls." ADD UNIQUE INDEX ".$profiletoupdate->tablename_urls."_url (url)");
          
          @$db->Execute("alter table ".$profiletoupdate->tablename_urlparams." drop index ".$profiletoupdate->tablename_urlparams."_params");
          //$db->Execute("create unique index ".$profiletoupdate->tablename_urlparams."_params on ".$profiletoupdate->tablename_urlparams." (params)");
          $db->Execute("ALTER IGNORE TABLE ".$profiletoupdate->tablename_urlparams." ADD UNIQUE INDEX ".$profiletoupdate->tablename_urlparams."_params (params)");  
          
          @$db->Execute("alter table ".$profiletoupdate->tablename_referrers." drop index ".$profiletoupdate->tablename_referrers."_referrer");
          //$db->Execute("create unique index ".$profiletoupdate->tablename_referrers."_referrer on ".$profiletoupdate->tablename_referrers." (referrer)");
          $db->Execute("ALTER IGNORE TABLE ".$profiletoupdate->tablename_referrers." ADD UNIQUE INDEX ".$profiletoupdate->tablename_referrers."_referrer (referrer)");
          
          @$db->Execute("alter table ".$profiletoupdate->tablename_refparams." drop index ".$profiletoupdate->tablename_refparams."_params");
          //$db->Execute("create unique index ".$profiletoupdate->tablename_refparams."_params on ".$profiletoupdate->tablename_refparams." (params)");
          $db->Execute("ALTER IGNORE TABLE ".$profiletoupdate->tablename_refparams." ADD UNIQUE INDEX ".$profiletoupdate->tablename_refparams."_params (params)"); 
          
          @$db->Execute("alter table ".$profiletoupdate->tablename_keywords." drop index ".$profiletoupdate->tablename_keywords."_keywords");
          //$db->Execute("create unique index ".$profiletoupdate->tablename_keywords."_keywords on ".$profiletoupdate->tablename_keywords." (keywords)");
          $db->Execute("ALTER IGNORE TABLE ".$profiletoupdate->tablename_keywords." ADD UNIQUE INDEX ".$profiletoupdate->tablename_keywords."_keywords (keywords)");
          
          $profiletoupdate->structure_version = 2.06;
          $profiletoupdate->Save();
            
        }
        
        if ($profiletoupdate->structure_version < 2.08) {
          echo "<P style=\"clear:both;\">Changing structure of Summary table, Updating Summary table (please be patient, this can take a while) ... ";
          @flush();
          @ob_flush();
          $start=time();  
          @$db->Execute("alter table $profiletoupdate->tablename_vpd add column visits bigint(100) NOT NULL default '0'");
          @$db->Execute("alter table $profiletoupdate->tablename_vpd drop column ppu");
          @$db->Execute("alter table $profiletoupdate->tablename_vpm add column visits bigint(100) NOT NULL default '0'");
          @$db->Execute("alter table $profiletoupdate->tablename_vpm drop column ppu");
          
          // now insert the new visit numbers
          $db->Execute("update $profile->tablename_vpd, (select FROM_UNIXTIME(timestamp,'%d-%b-%Y %a') AS days, count(distinct sessionid) as visits from $profile->tablename where crawl=0 group by days) as ut set $profile->tablename_vpd.visits=ut.visits where $profile->tablename_vpd.days = ut.days");
          
          $db->Execute("update $profile->tablename_vpm, (select FROM_UNIXTIME(timestamp,'%M %Y') AS month, count(distinct sessionid) as visits from $profile->tablename where crawl=0 group by month) as ut set $profile->tablename_vpm.visits=ut.visits where $profile->tablename_vpm.month = ut.month");
          $took=time()-$start;
          echo "Done! (Upgrade took $took seconds)</p><br><br><br>";
          @flush();
          @ob_flush();
          
          $profiletoupdate->structure_version = 2.08;
          $profiletoupdate->Save();
            
        }
        
        if ($profiletoupdate->structure_version < 2.11) {
          echo "<P style=\"clear:both;\">Changing structure of Visitor ID table, Updating table (please be patient, this can take a while) ... ";
          @flush();
          @ob_flush();
          $start=time();  
          @$db->Execute("alter table $profiletoupdate->tablename_visitorids add column created int(11) NOT NULL default '0'");
          @$db->Execute("alter table $profiletoupdate->tablename_visitorids add column customlabel varchar(80) ");
          
          // now insert the new visit numbers
          $db->Execute("update $profile->tablename_visitorids, (select min(timestamp) AS created, visitorid from $profile->tablename group by visitorid) as ut set $profile->tablename_visitorids.created=ut.created where $profile->tablename_visitorids.id = ut.visitorid");
          
          $took=time()-$start;
          echo "Done! (Upgrade took $took seconds)</p><br><br><br>";
          @flush();
          @ob_flush();
          
          $profiletoupdate->structure_version = 2.11;
          $profiletoupdate->Save();
            
        }
        if ($profiletoupdate->structure_version < 2.12) {
          echo "<P style=\"clear:both;\">Adding index (please be patient, this can take a while) ... ";
          @flush();
          @ob_flush();
          $start=time();  
          $db->Execute("alter table $profiletoupdate->tablename_visitorids add index (created)") or die("creating index on table '$profiletoupdate->tablename_visitorids' failed, please try again");         
          $took=time()-$start;
          echo "Done! (Upgrade took $took seconds)</p><br><br><br>";
          @flush();
          @ob_flush();
          
          $profiletoupdate->structure_version = 2.12;
          $profiletoupdate->Save();
            
        }
        if ($profiletoupdate->structure_version < 2.13) {
          # this part must be done because we made a huge mistake a while back, when we did the update to version 2.06 above, we forgot to actually add unique indexes to new profiles! Doh!  
          echoNotice("Checking tables for use of archives and unique indexes (so we can update faster) ...","font-weight:bold;");
          @flush();
          @ob_flush();
          $start=time();
          
          # check these tables and columns
          $checktables[$profiletoupdate->tablename_urls]="url";
          $checktables[$profiletoupdate->tablename_urlparams]="params"; 
          $checktables[$profiletoupdate->tablename_referrers]="referrer"; 
          $checktables[$profiletoupdate->tablename_refparams]="params"; 
          $checktables[$profiletoupdate->tablename_keywords]="keywords"; 
          $checktables[$profiletoupdate->tablename_visitorids]="visitorid"; 
          
          $nogood=0;
          foreach ($checktables as $table => $column) {
            $q = $db->Execute("show keys from $table where Column_name='$column'");
            if ($data = $q->FetchRow()) {
                if ($data['Non_unique']==1) {
                    $w = "Index in table '$table' on column '$column' is not unique. ";
                    $sq = $db->Execute("show table status where name='$table'");
                    if ($sqdata = $sq->FetchRow()) {
                        if ($sqdata['Engine']=="MyISAM") {
                            $w .= "We will now add a unique index...";
                            echoWarning($w);
                            @flush();
                            @ob_flush();
                            # make the index
                            $db->Execute("alter table $table drop index ".$data['Key_name']) or die (Warning("Failed to drop index from table $table, please try again."));
                            $db->Execute("alter ignore table $table add unique index ".$data['Key_name']." ($column)") or die (Warning("Failed to create index in table $table, please try again."));
                            echoNotice("Created unique index in table $table","margin-left:50px;");
                        } else if ($sqdata['Engine']=="MRG_MyISAM") {
                            $w .= "This is a MERGE table, which means we cannot add a unique index. Updating method for this profile will therefore remain unchanged.";
                            echoWarning($w);
                            $nogood=1;
                        } else {
                            $w .= "The storage engine for this table is '{$sqdata['Engine']}', which is not officially supported by Logaholic. Updating method for this profile will therefore remain unchanged.";
                            echoWarning($w);
                            $nogood=1; 
                        }                    
                    }    
                } else if ($data['Non_unique']==0) {
                    echoNotice("OK. Index in table '$table' on column '$column' is unique.");        
                } else {
                    echoWarning("Can't determine if the index is unique.. don't know what to do");
                    $nogood=1;   
                }
            } else {
                echoWarning("No index for $column was found on table $table, adding one now...");
                @flush();
                @ob_flush();
                $db->Execute("alter ignore table $table add unique index ".$table."_$column ($column)") or die (Warning("Failed to create index in table $table, please try again."));
                echoNotice("Created unique index in table $table","margin-left:50px;");
            }    
          }
          if ($nogood==1) {
            setProfileData($profiletoupdate->profilename,$profiletoupdate->profilename."_update_method","selectinsert");
          }        
          $took=time()-$start;
          echoNotice("Done! (Upgrade took $took seconds)","margin-bottom:50px;font-weight:bold;");
          @flush();
          @ob_flush();
          $profiletoupdate->structure_version = 2.13;
          $profiletoupdate->Save();
            
        }
        
        if ($profiletoupdate->structure_version < 2.14) {
           
          $db->Execute("alter table $profiletoupdate->tablename_urls add column title varchar(255) default ''") or die("Could not add column to '$profiletoupdate->tablename_urls', please try again");         
          echoNotice("Upgraded table $profiletoupdate->tablename_urls; added title field.","margin-bottom:50px");
          
          @flush();
          @ob_flush();
          
          $profiletoupdate->structure_version = 2.14;
          $profiletoupdate->Save();
            
        }       
		
		if ($profiletoupdate->structure_version < 2.15) {
			$q = $db->Execute("SELECT * FROM {$profiletoupdate->tablename_keywords}");
			while($row = $q->FetchRow()) {
				$db->Execute("UPDATE IGNORE {$profiletoupdate->tablename_keywords} SET keywords = ".$db->quote(urldecode($row['keywords']))." WHERE id = {$row['id']}");
			}
			
			echoNotice("Done! (Updated keywords)","margin-bottom:50px;font-weight:bold;");
		  
			$profiletoupdate->structure_version = 2.15;
			$profiletoupdate->Save();
		}
		if ($profiletoupdate->structure_version < 2.16) {
			$profiletoupdate->setDefaultDashboard();
			
			$profiletoupdate->structure_version = 2.16;
			$profiletoupdate->Save();
		}
		
		if ($profiletoupdate->structure_version < 2.17) {
			$db->Execute("ALTER TABLE {$profiletoupdate->tablename_visitorids} MODIFY `ipnumber` VARCHAR(39)");
			
			$profiletoupdate->structure_version = 2.17;
			$profiletoupdate->Save();
		}
		
		if($profiletoupdate->structure_version < 2.18) {
			createDataTable($profiletoupdate);
			
			$profiletoupdate->structure_version = 2.18;
			$profiletoupdate->Save();
		}
		
		if($profiletoupdate->structure_version < 2.19) {
			include_once("components/useragents/techpattern.php");
			
			ob_start();
			echoNotice("Updating user agent table, this might take a while...");
			lgflush();
			
			# the hash name can be different
			$q = $db->Execute("show create table {$profiletoupdate->tablename_useragents}");
			$txt = $q->FetchRow();
			if (strpos($txt, "useragent_hash")!==false) {
				$hash = "useragent_hash";
			} else {
				$hash = "hash";
			}
			
			$q = $db->Execute("SELECT DISTINCT(useragentid), crawl FROM {$profiletoupdate->tablename}");
			while($row = $q->FetchRow()) {
				if ($row['useragentid']=='') {
					continue;
				}
				$q2 = $db->Execute("SELECT * FROM ".TBL_USER_AGENTS." WHERE id = {$row['useragentid']}");
				while($row2 = $q2->FetchRow()) {
					$id = $row2['id'];
					$agent_array = explode(" on ", $row2['name']);
					
					if(empty($row['crawl'])) {
						$row['crawl'] = 0;
					}
					
					if(count($agent_array) > 1) {
						$tmp_agent = explode(" ", str_replace("(text based browser)","",str_replace("(unknown version)","",$agent_array[0])));
						if($tmp_agent[0] == "Internet") {
							$browsername = $tmp_agent[0]." ".$tmp_agent[1];
							unset($tmp_agent[0]);
							unset($tmp_agent[1]);
						} else {
							$browsername = $tmp_agent[0];
							unset($tmp_agent[0]);
						}
						$browserversion = "";
						foreach($tmp_agent as $part) {
							$browserversion .= "{$part} ";
						}
						
						$tmp_os = explode(" ", str_replace("(unknown version)","",$agent_array[1]));
						$osname = $tmp_os[0];
						
						$osversion = "";
						if(count($tmp_os) > 1) {
							for($i = 1; $i < count($tmp_os); $i++) {
								$osversion .= "{$tmp_os[$i]} ";
							}
						}
						
						$uq = "INSERT INTO {$profiletoupdate->tablename_useragents} (`id`,`name`,`version`,`os`,`os_version`,`useragent`,`$hash`,`is_bot`) VALUES (".$db->Quote($id).", ".$db->Quote($browsername).", ".$db->Quote($browserversion).", ".$db->Quote($osname).", ".$db->Quote($osversion).", ".$db->Quote($row2['name']).", ".$db->Quote(md5($row2['name'])).",{$row['crawl']}) ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id)";
					} else {
						$detected = get_useragent($row2['name']);
						
						$uq = "INSERT INTO {$profiletoupdate->tablename_useragents} (`id`, `name`, `version`, `os`, `os_version`, `engine`, `useragent`, `$hash`, `is_bot`, `is_mobile`, `device`) VALUES (".$db->Quote($id).", ".$db->Quote($detected['agent_name']).", ".$db->Quote($detected['agent_version']).", ".$db->Quote($detected['agent_os']).", ".$db->Quote($detected['agent_os_version']).", ".$db->Quote($detected['agent_engine']).", ".$db->Quote($detected['agent_string']).", ".$db->Quote(md5($detected['agent_string'])).", ".$db->Quote($detected['agent_is_bot']).", ".$db->Quote($detected['agent_is_mobile']).", ".$db->Quote($detected['agent_device']).") ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id)";
					}
					//echo $uq;
					$db->Execute($uq) or die('Error creating table');
				}
			}
			
			$profiletoupdate->structure_version = 2.19;
			$profiletoupdate->Save();
		}
		
		if($profiletoupdate->structure_version < 2.20) {
			$db->Execute("UPDATE {$profiletoupdate->tablename_visitorids} SET customlabel='' WHERE customlabel is NULL") or die('Error updating visitorid table');
			$db->Execute("ALTER table {$profiletoupdate->tablename_visitorids} change column customlabel customlabel varchar(80) DEFAULT ''") or die('Error updating visitorid table');
			
			$profiletoupdate->structure_version = 2.20;
			$profiletoupdate->Save();
		}
		
		if($profiletoupdate->structure_version < 2.21) {
			$query = "UPDATE ".TBL_TRAFFIC_SOURCES." set sourcecondition = ".$db->Quote("is_mobile ='1'")." where profileid='$profiletoupdate->profileid' and sourcename='Mobile Visitors'";
			if ($result = $db->Execute($query)) {
					echoNotice("Updated Mobile Visitors Segment");
			}
			$profiletoupdate->structure_version = 2.21;
			$profiletoupdate->Save();
		}
		
		if ($profiletoupdate->structure_version < 2.22) {
			$q = $db->Execute("SELECT * FROM `".TBL_GLOBAL_SETTINGS."` WHERE `Name` LIKE '{$profiletoupdate->profilename}.dashboards.%' AND `Profile` = '{$profiletoupdate->profilename}'");
			while($row = $q->FetchRow()) {
				$dashboarddata = json_decode($row['Value'], true);
				foreach($dashboarddata['reports'] as $grid_key => $grid) {
					foreach($grid as $report_key => $report) {
						if(!empty($report['url'])) { continue 3; }
						$report_url = "reports.php?conf={$profiletoupdate->profilename}&labels={$report['label']}";
						
						foreach($report['reportOptions'] as $option_key => $report_option) {
							$report_url .= "&{$option_key}={$report_option}";
						}
						
						$dashboarddata['reports'][$grid_key][$report_key]['url'] = $report_url;
						
						unset($dashboarddata['reports'][$grid_key][$report_key]['classname']);
						unset($dashboarddata['reports'][$grid_key][$report_key]['reportOptions']);
					}
				}
				$dashboarddata = json_encode($dashboarddata);
				$db->Execute("UPDATE `".TBL_GLOBAL_SETTINGS."` SET `Value` = '{$dashboarddata}' WHERE `Name` = '{$row['Name']}' AND `Profile` = '{$profiletoupdate->profilename}'");
			}
			
			$profiletoupdate->structure_version = 2.22;
			$profiletoupdate->Save();
		}
		
		if ($profiletoupdate->structure_version < 2.23) {
			$q = $db->Execute("SELECT * FROM `".TBL_GLOBAL_SETTINGS."` WHERE (`Name` = '{$profiletoupdate->profilename}.facebook_age' OR `Name` = '{$profiletoupdate->profilename}.facebook_education') AND `Profile` = '{$profiletoupdate->profilename}'");
			while($row = $q->FetchRow()) {
				$new_name = explode(".", $row['Name']);
				
				// We want to add .report. between the profile name and the report name
				$new_name = $profiletoupdate->profilename.".report.".$new_name[count($new_name) - 1];
				$db->Execute("INSERT INTO `".TBL_GLOBAL_SETTINGS."` (`Name`, `Profile`, `Value`) VALUES ('{$new_name}', '{$row['Profile']}', '{$row['Value']}')");
				$db->Execute("DELETE FROM `".TBL_GLOBAL_SETTINGS."` WHERE `Name` = '{$row['Name']}' AND `Profile` = '{$row['Profile']}'");
			}
			
			$profiletoupdate->structure_version = 2.23;
			$profiletoupdate->Save();
		}
		
		if ($profiletoupdate->structure_version < 2.24) {
			
			$q = $db->Execute("ALTER table {$profiletoupdate->tablename_visitorids} change column customlabel customlabel varchar(80) NOT NULL DEFAULT ''");
			
			$profiletoupdate->structure_version = 2.24;
			$profiletoupdate->Save();
		}
		
		if ($profiletoupdate->structure_version < 2.25) {
			$old_bandwidth = getProfileData($profile->profilename, $profile->profilename.".bandwidthData", false);
			if(!empty($old_bandwidth)) {
				$old_bandwidth = unserialize($old_bandwidth);
				$new_bandwidth = array();
				
				foreach($old_bandwidth as $day => $bw) {
					$day = substr($day, 0, 8);
					for($c = 0; $c < 24; $c++) {
						$hour = strlen($c) < 2 ? "0{$c}" : $c;
						$new_bandwidth[$day.$hour] = round($bw / 24);
					}
				}
				
				$new_bandwidth = serialize($new_bandwidth);
				
				setProfileData($profile->profilename, $profile->profilename.".bandwidthData", $new_bandwidth);
			}
			
			$profiletoupdate->structure_version = 2.25;
			$profiletoupdate->Save();
		}
		
		if ($profiletoupdate->structure_version < 2.26) {
			$sql = "SELECT * FROM `".TBL_GLOBAL_SETTINGS."` WHERE `Name` LIKE '{$profiletoupdate->profilename}.dashboards.%' AND `Profile` = '{$profiletoupdate->profilename}'";

			$q = $db->Execute($sql);
			
			while($dashboard = $q->FetchRow()) {
				$dashboardEntryName = $dashboard['Name'];
				$dashboard = stdToAssoc(json_decode($dashboard['Value']));
				foreach($dashboard['reports'] as $gridkey => $grid) {
					foreach($grid as $reportkey => $report) {
						if($report['label'] == '_TOP_KEYWORDS_TRENDS') {
							$report['name'] = _KEYWORD_TRENDS;
							$report['icon'] = str_replace("_TOP_KEYWORDS_TRENDS", "_KEYWORD_TRENDS", $report['icon']);
							$report['label'] = str_replace("_TOP_KEYWORDS_TRENDS", "_KEYWORD_TRENDS", $report['label']);
							
							$grid[$reportkey] = $report;
						}
					}
					$dashboard['reports'][$gridkey] = $grid;
				}
				
				$dashboardData = json_encode($dashboard);
				
				setProfileData($profiletoupdate->profilename, $dashboardEntryName, $dashboardData);
			}
			
			$profiletoupdate->structure_version = 2.26;
			$profiletoupdate->Save();
		}
		
		if($profiletoupdate->structure_version < 2.28) {
			setProfileData($profiletoupdate->profilename, "{$profiletoupdate->profilename}.updatePreference", 'regular');
			
			$profiletoupdate->structure_version = 2.28;
			$profiletoupdate->Save();
		}
		
		if($profiletoupdate->structure_version < 2.29) {
			# the hash name can be different
			$q = $db->Execute("show create table {$profiletoupdate->tablename_useragents}");
			$txt = $q->FetchRow();
			if (strpos($txt[1], "useragent_hash")!==false) {
				ob_start();
				echoNotice("Changing user agent table key ..");
				lgflush();
				@$db->Execute("ALTER TABLE {$profiletoupdate->tablename_useragents} DROP COLUMN `hash`");
				$db->Execute("ALTER TABLE {$profiletoupdate->tablename_useragents} CHANGE COLUMN `useragent_hash` `hash` CHAR(32)");
			} 
			$profiletoupdate->structure_version = 2.29;
			$profiletoupdate->Save();
		}

		# this is not needed yet, include it when we release perl
		/*
		if($profiletoupdate->structure_version < 2.27) {
			$tables = array();
			$tables[] = array('table' => $profiletoupdate->tablename_urls , 'field' => 'url', 'sfield' => 's.request');
			$tables[] = array('table' => $profiletoupdate->tablename_urlparams , 'field' => 'params', 'sfield' => 's.urlparams');
			$tables[] = array('table' => $profiletoupdate->tablename_referrers , 'field' => 'referrer', 'sfield' => 's.referrer');
			$tables[] = array('table' => $profiletoupdate->tablename_refparams , 'field' => 'params', 'sfield' => 's.refparams');
			$tables[] = array('table' => $profiletoupdate->tablename_keywords , 'field' => 'keywords', 'sfield' => 's.keywords');
			//$tables[] = array('table' => $profiletoupdate->tablename_useragents , 'field' => 'useragent', 'sfield' => 's.useragent');
			$tables[] = array('table' => $profiletoupdate->tablename_visitorids , 'field' => 'visitorid', 'sfield' => "md5(CONCAT(s.host,':',s.useragent))");
			
			foreach ($tables as $t) {
				@$db->Execute("ALTER TABLE {$t['table']} ADD COLUMN `hash` CHAR(32)");
				@$db->Execute("UPDATE {$t['table']} SET `hash` = MD5({$t['field']})");
				@$db->Execute("ALTER TABLE {$t['table']} ADD INDEX (hash)");
			}
			@$db->Execute("ALTER TABLE {$profiletoupdate->tablename_visitorids} CHANGE COLUMN `ipnumber` `ipnumber` VARCHAR(255)");
			
			$profiletoupdate->structure_version = 2.27;
			$profiletoupdate->Save();
		}
		*/		
		
		//echo "<p>Profile data update completed.</p>";
		flush();
	}
	
	
	/* ---------------------------------------------------------------------------------------------------------------------------------------------------------------- */
	/* ---------------------------------------------------------------------------------------------------------------------------------------------------------------- */
	/* --- Shared table updates (cur_dbver) ------------------------------------------------------------------------------------------------------------------------------------------------------------- */
	/* ---------------------------------------------------------------------------------------------------------------------------------------------------------------- */
	/* ---------------------------------------------------------------------------------------------------------------------------------------------------------------- */
	
        
	$cur_dbver = getGlobalSetting("DB_MetadataVersion", 0.00);
	//echo "the curdb version is $cur_dbver";
	if ($cur_dbver == 0.00) {
		verifySettingsTable();
	}	
	if ($cur_dbver < 1.9) {
			 //$db->Execute("CREATE TABLE geoip (  start_ip varchar(15) NOT NULL default '',  end_ip varchar(15) NOT NULL default '',  start int(10) unsigned NOT NULL default '0',  end int(10) unsigned NOT NULL default '0',  cc char(2) NOT NULL default '',  cn varchar(50) NOT NULL default '',  KEY start_ip (start_ip),  KEY end_ip (end_ip),  KEY start (start),  KEY end (end)) TYPE=MyISAM ENGINE=MyISAM CHARSET=utf8");
		if (!in_array_insensitive(TBL_LGSTATUS, $db->MetaTables())) {
			$db->Execute("CREATE TABLE ".TBL_LGSTATUS." (id ". ($databasedriver == "sqlite"? "INTEGER PRIMARY KEY ": "int(10) NOT NULL auto_increment ") .
							                                        ",  code int(5) NOT NULL default '0',  descr varchar(100) NOT NULL default '0' ".($databasedriver == "sqlite" ? "" : ", PRIMARY KEY  (id)") . ") ENGINE=MyISAM CHARSET=utf8");
			@$db->Execute("CREATE unique index code on ".TBL_LGSTATUS." (code) ");
			
			$db->Execute("DELETE FROM ".TBL_LGSTATUS); // Delete everything that's currently here so we can re-insert.
            // list from http://www.askapache.com/htaccess/apache-status-code-headers-errordocument.html
			$db->Execute("insert into ".TBL_LGSTATUS." (code, descr) values ('100', 'Continue')");
			$db->Execute("insert into ".TBL_LGSTATUS." (code, descr) values ('101', 'Switching Protocols')");
			$db->Execute("insert into ".TBL_LGSTATUS." (code, descr) values ('102', 'Processing')");
			$db->Execute("insert into ".TBL_LGSTATUS." (code, descr) values ('200', 'OK')");
			$db->Execute("insert into ".TBL_LGSTATUS." (code, descr) values ('201', 'Created')");
			$db->Execute("insert into ".TBL_LGSTATUS." (code, descr) values ('202', 'Accepted')");
			$db->Execute("insert into ".TBL_LGSTATUS." (code, descr) values ('203', 'Non-Authoritative Information')");
			$db->Execute("insert into ".TBL_LGSTATUS." (code, descr) values ('204', 'No Content')");
			$db->Execute("insert into ".TBL_LGSTATUS." (code, descr) values ('205', 'Reset Content')");
			$db->Execute("insert into ".TBL_LGSTATUS." (code, descr) values ('206', 'Partial Content')");
			$db->Execute("insert into ".TBL_LGSTATUS." (code, descr) values ('207', 'Multi-Status')");
			$db->Execute("insert into ".TBL_LGSTATUS." (code, descr) values ('300', 'Multiple Choices')");
			$db->Execute("insert into ".TBL_LGSTATUS." (code, descr) values ('301', 'Moved Permanently (redirect)')");
			$db->Execute("insert into ".TBL_LGSTATUS." (code, descr) values ('302', 'Moved Temporarily (redirect)')");
			$db->Execute("insert into ".TBL_LGSTATUS." (code, descr) values ('303', 'See Other')");
			$db->Execute("insert into ".TBL_LGSTATUS." (code, descr) values ('304', 'Not Modified')");
			$db->Execute("insert into ".TBL_LGSTATUS." (code, descr) values ('305', 'Use Proxy')");
			$db->Execute("insert into ".TBL_LGSTATUS." (code, descr) values ('307', 'Temporary Redirect')");
			$db->Execute("insert into ".TBL_LGSTATUS." (code, descr) values ('400', 'Bad Request')");
			$db->Execute("insert into ".TBL_LGSTATUS." (code, descr) values ('401', 'Authorization Required')");
			$db->Execute("insert into ".TBL_LGSTATUS." (code, descr) values ('402', 'Payment Required')");
			$db->Execute("insert into ".TBL_LGSTATUS." (code, descr) values ('403', 'Forbidden')");
			$db->Execute("insert into ".TBL_LGSTATUS." (code, descr) values ('404', 'Not Found')");
			$db->Execute("insert into ".TBL_LGSTATUS." (code, descr) values ('405', 'Method Not Allowed')");
			$db->Execute("insert into ".TBL_LGSTATUS." (code, descr) values ('406', 'Not Acceptable')");
			$db->Execute("insert into ".TBL_LGSTATUS." (code, descr) values ('407', 'Proxy Authentication Required')");
			$db->Execute("insert into ".TBL_LGSTATUS." (code, descr) values ('408', 'Request Time-out')");
			$db->Execute("insert into ".TBL_LGSTATUS." (code, descr) values ('409', 'Conflict')");
			$db->Execute("insert into ".TBL_LGSTATUS." (code, descr) values ('410', 'Gone')");
			$db->Execute("insert into ".TBL_LGSTATUS." (code, descr) values ('411', 'Length Required')");
			$db->Execute("insert into ".TBL_LGSTATUS." (code, descr) values ('412', 'Precondition Failed')");
			$db->Execute("insert into ".TBL_LGSTATUS." (code, descr) values ('413', 'Request Entity Too Large')");
			$db->Execute("insert into ".TBL_LGSTATUS." (code, descr) values ('414', 'Request-URI Too Large')");
			$db->Execute("insert into ".TBL_LGSTATUS." (code, descr) values ('415', 'Unsupported Media Type')");
			$db->Execute("insert into ".TBL_LGSTATUS." (code, descr) values ('416', 'Requested Range Not Satisfiable')");
			$db->Execute("insert into ".TBL_LGSTATUS." (code, descr) values ('417', 'Expectation Failed')");
			$db->Execute("insert into ".TBL_LGSTATUS." (code, descr) values ('422', 'Unprocessable Entity')");
			$db->Execute("insert into ".TBL_LGSTATUS." (code, descr) values ('423', 'Locked')");
			$db->Execute("insert into ".TBL_LGSTATUS." (code, descr) values ('424', 'Failed Dependency')");
			$db->Execute("insert into ".TBL_LGSTATUS." (code, descr) values ('425', 'No code')");
			$db->Execute("insert into ".TBL_LGSTATUS." (code, descr) values ('426', 'Upgrade Required')");
			$db->Execute("insert into ".TBL_LGSTATUS." (code, descr) values ('500', 'Internal Server Error')");
			$db->Execute("insert into ".TBL_LGSTATUS." (code, descr) values ('501', 'Method Not Implemented')");
			$db->Execute("insert into ".TBL_LGSTATUS." (code, descr) values ('502', 'Bad Gateway')");
			$db->Execute("insert into ".TBL_LGSTATUS." (code, descr) values ('503', 'Service Temporarily Unavailable')");
			$db->Execute("insert into ".TBL_LGSTATUS." (code, descr) values ('504', 'Gateway Time-out')");
			$db->Execute("insert into ".TBL_LGSTATUS." (code, descr) values ('505', 'HTTP Version Not Supported')");
			$db->Execute("insert into ".TBL_LGSTATUS." (code, descr) values ('506', 'Variant Also Negotiates')");
			$db->Execute("insert into ".TBL_LGSTATUS." (code, descr) values ('507', 'Insufficient Storage')");
			$db->Execute("insert into ".TBL_LGSTATUS." (code, descr) values ('510', 'Not Extended')");        
        
        }

		if (!in_array_insensitive(TBL_NOTES, $db->MetaTables())) {
			$db->Execute("CREATE TABLE ".TBL_NOTES." ( id ". ($databasedriver == "sqlite"? "INTEGER PRIMARY KEY ": "int(10) NOT NULL auto_increment ") .
													",  profile varchar(75) NOT NULL default '0',  timestamp int(11) NOT NULL default '0',  note varchar(255) NOT NULL default '0'" .
													($databasedriver == "sqlite" ? "" : ", PRIMARY KEY  (id)") . ") ENGINE=MyISAM CHARSET=utf8");
			$db->Execute("CREATE index timestamp on ".TBL_NOTES." (timestamp) ");
			$db->Execute("CREATE index profile on ".TBL_NOTES." (profile) ");
		}
		
		// No profiles - we need to create the profiles table and then migrate everything.
		setGlobalSetting("DB_MetadataVersion", 1.90);
	}
	
	
	// We introduced the metadataversion number in version 2.00.  Anything before that has some migration to do.
	// *NOTE* the database version number does *not* match the program version number.  We just need to increment the numbers
	// each time we have a metadata version number.
	if ($cur_dbver < 2.0) {
		
		 // Make sure the table exists.
		 if (!in_array_insensitive(TBL_PROFILES, $db->MetaTables())) {
			 // We need to create the user database.
			$query = "CREATE TABLE ".TBL_PROFILES." ( ".
					"profileid ". (($databasedriver != "sqlite") ? "int(11) NOT NULL auto_increment" : "INTEGER PRIMARY KEY") .", ".
					"profilename varchar(100) NOT NULL, ".
					"confdomain varchar(100) default NULL, ".
					"equivdomains text default NULL, ".
					"tablename varchar(32) NOT NULL, ".
					"defaultfile varchar(128) default NULL, ".
					"logfilefullpath text default NULL, ".
					"splitlogs tinyint default 0, ".
					"splitfilter varchar(100) default NULL, ".
					"trackermode tinyint default 0, ".
					"visitoridentmethod tinyint, " .
					"skipips text default NULL, ".
					"skipfiles text default NULL, ".
					"targetfiles mediumtext default NULL, ".
					"othersettings text default NULL, ".
					"usepagecache tinyint default 1, ".
					"structure_version float " . // What metadataversion is the profile's data stored in?  Used so we know if we need to update
					(($databasedriver != "sqlite") ? ", PRIMARY KEY (profileid)" : "").") ENGINE=MyISAM CHARSET=utf8";
			 $db->Execute($query);
			 $db->Execute("CREATE unique index ".TBL_PROFILES."_profilename on ".TBL_PROFILES." (profilename) ");
			 $db->Execute("CREATE unique index ".TBL_PROFILES."_tablename on ".TBL_PROFILES." (tablename) ");
		 }
		 
		 // Find all the profiles in the files directory, read them, and insert the values into the database
		// this is old, we can skip this - it causing problems with the new files in this directory
        $real_path = realpath("index.php");
		$rpath = dirname($real_path);
		$confpath=$rpath . "/files/";
		/*
		$handle = opendir($confpath);
		while ($file = readdir($handle)) {
			if ($file[0] != '.' && $file!="global.php") {
				// We have a file name - let's see if we've already imported this profile or not.
				$confname = $db->escape(substr($file,0,-4));
				
				$result = $db->Execute("select * from  ".TBL_PROFILES." where profilename = \"".$confname."\"") or die("Error converting profile, " . $db->ErrorMsg());
				
				// It doesn't already exist in the database, so let's import it.
				if ($result->RecordCount() == 0) {
				
					// Variables are set from the profile file.
					$confname = "--- NONAME ---";
					require $confpath . $file;
					
					if ($confname != "--- NONAME ---") {
						
						$record = array();
						$record["profilename"] = $confname;
						$record["confdomain"] = $confdomain;
						$record["tablename"] = $tablename;
						$record["defaultfile"] = $defaultfile;
						$record["logfilefullpath"] = $logfilefullpath;
						$record["splitlogs"] = $splitlogs;
						$record["splitfilter"] = $splitfilter;
						$record["trackermode"] = $trackermode;
						$record["skipips"] = $skipips;
						$record["skipfiles"] = $skipfiles;
						$record["targetfiles"] = $targetfiles;
						
						if (!$db->AutoExecute(TBL_PROFILES, $record, "INSERT")) {
							die("Error inserting record, ".$db->ErrorMsg());
						}
					}
				}
				// It's all inserted (or maybe already exists in the database), so delete the profile.
				unlink($confpath . $file) or die("Couldn't delete profile file " . $confpath . $file . " - aborting.");
			}
		}
		*/
		// No profiles - we need to create the profiles table and then migrate everything.
		setGlobalSetting("DB_MetadataVersion", 2.00);
	}
	
	// Create the association table for important parameters.
	if ($cur_dbver < 2.01) {
		 // Make sure the table exists.
		if (!in_array_insensitive(TBL_IMPORTANT_URL_PARAMS, $db->MetaTables())) {
			 // We need to create the user database.
			$query = "CREATE TABLE ".TBL_IMPORTANT_URL_PARAMS." ( ".
					"paramid ". (($databasedriver != "sqlite") ? "int(11) NOT NULL auto_increment" : "INTEGER PRIMARY KEY") .", ".
					"profileid int(11) NOT NULL, ".
					"filename varchar(128) NOT NULL, ".
					"nameisregex tinyint(1) default 0, ".
					"importantparams text default NULL" .
					(($databasedriver != "sqlite") ? ", PRIMARY KEY (paramid)" : "").
					") ENGINE=MyISAM CHARSET=utf8";
			 $db->Execute($query);
			 $db->Execute("CREATE index profileid on ".TBL_IMPORTANT_URL_PARAMS." (profileid)");
		 }
		setGlobalSetting("DB_MetadataVersion", 2.01);
	}
	

	// Added a "use page cache" field to the profile table.
	if ($cur_dbver < 2.03) {
	  $columns = $db->MetaColumns(TBL_PROFILES);
	  if (!isset($columns["USEPAGECACHE"])) {
			$query = "ALTER TABLE ".TBL_PROFILES." ADD COLUMN usepagecache tinyint default 1";
			$db->Execute($query);
		}
		setGlobalSetting("DB_MetadataVersion", 2.03);
	}
	
	// Create the traffic sources tables.  TBL_TRAFFIC_SOURCES & TBL_TRAFFIC_SOURCE_CONDITIONS
	if ($cur_dbver < 2.04) {
		// Make sure the table exists.
		if (!in_array_insensitive(TBL_TRAFFIC_SOURCES, $db->MetaTables())) {
			// We need to create the user database.
			$query = "CREATE TABLE `".TBL_TRAFFIC_SOURCES."` ( ".
					" id ". (($databasedriver != "sqlite") ? "int(11) NOT NULL auto_increment" : "INTEGER PRIMARY KEY") .", ".
					"`profileid` int(11) NOT NULL, ".
                    "`sourcename` varchar(128) NOT NULL, ".
					"`sourcecondition` text default NULL, ".
					"`category` varchar(255) default NULL".
					(($databasedriver != "sqlite") ? ", PRIMARY KEY (id)" : "").
					") ENGINE=MyISAM CHARSET=utf8";
			$db->Execute($query);
			$db->Execute("CREATE index ".TBL_TRAFFIC_SOURCES."_sourcename  on ".TBL_TRAFFIC_SOURCES." (sourcename)");
			$db->Execute("CREATE index ".TBL_TRAFFIC_SOURCES."_profileid on ".TBL_TRAFFIC_SOURCES." (profileid) ");
		}
		setGlobalSetting("DB_MetadataVersion", 2.04);
	}
	
	// The "text" type of the global settings table needs to be mediumtext, not text.
	if ($cur_dbver < 2.05) {
		// Make sure the table exists.
		if ($databasedriver == "mysql") {
			$db->Execute("ALTER TABLE ".TBL_GLOBAL_SETTINGS." CHANGE Value Value mediumtext");
		}
		setGlobalSetting("DB_MetadataVersion", 2.05);
	}
	
	if ($cur_dbver < 2.06) {
		// add the animate vaiable to the profile
        //echo "the curdb version is $cur_dbver";  
		$db->Execute("ALTER TABLE ".TBL_PROFILES." add column animate int(1) default '1'");
		setGlobalSetting("DB_MetadataVersion", 2.06);
	}
	if ($cur_dbver < 2.07) {
		// add the time zone correction vaiable to the profile         
		$db->Execute("ALTER TABLE ".TBL_PROFILES." add column timezonecorrection varchar(5) default '0'");
		setGlobalSetting("DB_MetadataVersion", 2.07);
	}	
	// If we're upgrading from 2.05+, add the profile id column to the traffic sources table.
	if (($cur_dbver < 2.08) && ($cur_dbver >= 2.04)) {
		// the profileid to the filters table
		$db->Execute("ALTER TABLE ".TBL_TRAFFIC_SOURCES." add column profileid int(11) NOT NULL");
		$db->Execute("CREATE INDEX ".TBL_TRAFFIC_SOURCES."_profileid on ".TBL_TRAFFIC_SOURCES." (profileid) ");
		setGlobalSetting("DB_MetadataVersion", 2.08);
	}
  if (($cur_dbver < 2.10)  && ($cur_dbver > 2.04)) {
		// the category to the filters table
		$db->Execute("ALTER TABLE ".TBL_TRAFFIC_SOURCES." add column category varchar(255) DEFAULT NULL");
		setGlobalSetting("DB_MetadataVersion", 2.10);
	}	
	if ($cur_dbver < 2.11) {
		// 2.11 normalizes the user agent field.		
		
		if ($cur_dbver >= 2.00) {  // If the profile table was just created, we don't need to do this.
			$db->Execute("ALTER TABLE `".TBL_PROFILES."` add column structure_version float");
		}
		
		// What metadataversion is the profile's data stored in?  Used so we know if we need to update
		$db->Execute("CREATE TABLE `".TBL_USER_AGENTS."` (".
					" `id` ". (($databasedriver != "sqlite") ? "int(11) NOT NULL auto_increment" : "INTEGER PRIMARY KEY") .", ".
          "`name` varchar(128) NOT NULL ".
					(($databasedriver != "sqlite") ? ", PRIMARY KEY (id)" : "").
					") ENGINE=MyISAM CHARSET=utf8");
		$db->Execute("CREATE unique index ".TBL_USER_AGENTS."_name on ".TBL_USER_AGENTS." (name)");
		// The data will be updated the first time a profile is accessed.  This means profiles don't have to 
		// be updated all at once, if there are multiple profiles.
		setGlobalSetting("DB_MetadataVersion", 2.11);
	}
	
	
	if (($cur_dbver < 2.12) && ($cur_dbver >= 2.0)) {
		// Add support for different kinds of user agents, and also the "equivalent domains" capability
		$columns = $db->MetaColumns(TBL_PROFILES);
		if (!isset($columns["EQUIVDOMAINS"])) {
			$db->Execute("ALTER TABLE ".TBL_PROFILES." add column equivdomains text default NULL ".($databasedriver != "sqlite" ? "after confdomain" : ""));
		}
		if (!isset($columns["VISITORIDENTMETHOD"])) {
			$db->Execute("ALTER TABLE ".TBL_PROFILES." add column visitoridentmethod tinyint ".($databasedriver != "sqlite" ? "after trackermode" : ""));
		} 
		$db->Execute("UPDATE ".TBL_PROFILES." set visitoridentmethod = ".VIDM_IPADDRESS); // Default to just use the IP Address, since we want to migrate data the same way it used to be.
		setGlobalSetting("DB_MetadataVersion", 2.12);
	}
    if (($cur_dbver < 2.13) && ($cur_dbver >= 2.0)) {
        // add the ftp fields and recursive switch to the profile
        $db->Execute("ALTER TABLE ".TBL_PROFILES." add column recursive int(1) default '0'");
        $db->Execute("ALTER TABLE ".TBL_PROFILES." add column ftpserver varchar(150) default NULL");
        $db->Execute("ALTER TABLE ".TBL_PROFILES." add column ftpuser varchar(50) default NULL");
        $db->Execute("ALTER TABLE ".TBL_PROFILES." add column ftppasswd varchar(50) default NULL");
        setGlobalSetting("DB_MetadataVersion", 2.13);
    }
	if (($cur_dbver < 2.14) && ($cur_dbver >= 2.0)) {
        // add one more ftp field
        $db->Execute("ALTER TABLE ".TBL_PROFILES." add column ftpfullpath text default NULL");
        setGlobalSetting("DB_MetadataVersion", 2.14);
    }
    if (($cur_dbver < 2.15) && ($cur_dbver >= 2.0)) {
        // add one more ftp field
        $db->Execute("ALTER TABLE ".TBL_PROFILES." add column visittimeout int(2) default '20'");
        setGlobalSetting("DB_MetadataVersion", 2.15);
    }
    if (($cur_dbver < 2.16) && ($cur_dbver >= 2.0)) {
        // we need more room
        if ($databasedriver == "mysql") {
            $db->Execute("ALTER TABLE ".TBL_GLOBAL_SETTINGS." CHANGE column Name Name varchar(255) NOT NULL");
        }
        setGlobalSetting("DB_MetadataVersion", 2.16);
    }
    if (($cur_dbver < 2.17) && ($cur_dbver >= 2.0)) {
        $db->Execute("ALTER TABLE ".TBL_PROFILES." add column urlparamfilter varchar(255) default ''");
        $db->Execute("ALTER TABLE ".TBL_PROFILES." add column urlparamfiltermode varchar(20) default 'Exclude'");
        setGlobalSetting("DB_MetadataVersion", 2.17);
    }
    if (($cur_dbver < 2.18) && ($cur_dbver >= 2.0)) {
        $db->Execute("ALTER TABLE ".TBL_PROFILES." add column splitfilternegative varchar(100) default NULL");
        setGlobalSetting("DB_MetadataVersion", 2.18);
    }
    
    if (($cur_dbver < 2.19)  && ($cur_dbver >= 2.0) && ($loginSystemExists==true)) {
        // add a last time logged in field to the user table
        @$db->Execute("ALTER TABLE ".TBL_USERS." add column lastlogin int(11) NOT NULL default'0'");      
        setGlobalSetting("DB_MetadataVersion", 2.19);
    }
    
    if (($cur_dbver < 2.20)  && ($cur_dbver >= 2.0)) {
        // add feed columns
        $db->Execute("ALTER TABLE ".TBL_PROFILES." add column feedurl varchar(100) default ''");
        $db->Execute("ALTER TABLE ".TBL_PROFILES." add column feedburneruri varchar(100) default ''");      
        setGlobalSetting("DB_MetadataVersion", 2.20);
    }
    
    if (($cur_dbver < 2.22)  && ($cur_dbver >= 2.0)) {
        // change useragent table
        //echo "Don't close this screen, changing useragent table .... please wait....";
        $db->Execute("ALTER TABLE ".TBL_USER_AGENTS." change column name name varchar(255) NOT NULL");
        @$db->Execute("ALTER TABLE ".TBL_USER_AGENTS." add column ismobile tinyint(1) UNSIGNED");     
        setGlobalSetting("DB_MetadataVersion", 2.22);
    }
    
    if (($cur_dbver < 2.23)  && ($cur_dbver >= 2.0)) {
        $db->Execute("ALTER TABLE ".TBL_PROFILES." add column timezone varchar(50) default ''");
        setGlobalSetting("DB_MetadataVersion", 2.23);
    }
    if (($cur_dbver < 2.24)  && ($cur_dbver >= 2.0)) {
        # we are now using cookies for the language selection, so remove all the entries in the DB that set the lanbguage the old way
        $db->Execute("DELETE FROM ".TBL_GLOBAL_SETTINGS." where name like '%-lang'"); 
        setGlobalSetting("DB_MetadataVersion", 2.24);
    }
    if (($cur_dbver < 2.25)  && ($cur_dbver >= 2.0)) {
        $db->Execute("ALTER TABLE ".TBL_PROFILES." add column googleparams varchar(255) default 'q, start, gclid, as_q, as_epq, as_oq, as _eq, as_sitesearch, as_rq, as_lq'");
        setGlobalSetting("DB_MetadataVersion", 2.25);
    }
    
    if (($cur_dbver < 2.26)  && ($cur_dbver >= 2.0)) {
        // we need to increase the size of some fields to avoid truncation taking place on very long domain names
        $db->Execute("ALTER TABLE ".TBL_PROFILES." modify tablename varchar(64)");
        $db->Execute("ALTER TABLE ".TBL_PROFILES." modify profilename varchar(255)");
        $db->Execute("ALTER TABLE ".TBL_PROFILES." modify confdomain varchar(255)");
        setGlobalSetting("DB_MetadataVersion", 2.26);
    }
	
	if (($cur_dbver < 2.27)  && ($cur_dbver >= 2.0)) {
        // we need to add fields for twitter and facebook   // OVERRULED! NO MORE NEEDED!   
        setGlobalSetting("DB_MetadataVersion", 2.27);
    }
		
	if(($cur_dbver < 2.28)  && ($cur_dbver >= 2.0)) {
		if (!in_array_insensitive(TBL_GOALS, $db->MetaTables())) {
			$db->Execute("CREATE TABLE `".TBL_GOALS."` (
				`goalID` int(11) NOT NULL AUTO_INCREMENT,
				`goalName` varchar(100) DEFAULT NULL,
				`timeunit` varchar(100) DEFAULT NULL,
				`targetValue` varchar(11) DEFAULT NULL,
				`metric` varchar(50) DEFAULT NULL,
				`conditions` text,
				`graphType` varchar(50) DEFAULT 'speed',
				`profileID` int(11) DEFAULT NULL,
				`inverse` int(1) DEFAULT NULL,
				`kpi` varchar(100) DEFAULT NULL,
				PRIMARY KEY (`goalID`)
			) ENGINE=MyISAM CHARSET=utf8");
		}
        setGlobalSetting("DB_MetadataVersion", 2.28);
	}
	if(($cur_dbver < 2.29) && ($cur_dbver >= 2.0)){
		$db->Execute("ALTER TABLE ".TBL_PROFILES." add column lastused int(11) default '0'");
		setGlobalSetting("DB_MetadataVersion", 2.29);
	}


	/* ---------------------------------------------------------------------------------------------------------------------------------------------------------------- */
	/* ---------------------------------------------------------------------------------------------------------------------------------------------------------------- */
	/* --- User table updates ------------------------------------------------------------------------------------------------------------------------------ */
	/* ---------------------------------------------------------------------------------------------------------------------------------------------------------------- */
	/* ---------------------------------------------------------------------------------------------------------------------------------------------------------------- */
	
	function verifyUserTable() {
        global $db, $databasedriver,$user_dbver; 
        
         if (!in_array_insensitive(TBL_USERS, $db->MetaTables())) {
            // We need to create the user database.
            $query = "CREATE TABLE ".TBL_USERS." ( ".
                        "userid ". (($databasedriver != "sqlite") ? "int(11) NOT NULL auto_increment" : "INTEGER PRIMARY KEY") .", ".
              "username varchar(100) NOT NULL, ".
              "name varchar(100) default NULL, ".
              "password varchar(32) default NULL, ".
              "email varchar(100) default NULL, ".
              "profiles text default NULL, ".
              "foreignkey varchar(100) default NULL, ".
              "created int(11) NOT NULL, ".
              "isAdmin int(4) NOT NULL default '0', ".
              "active tinyint(1) default '1', ".
              "accessUpdateLogs tinyint(4) NOT NULL default '1', ".
              "accessAddProfile tinyint(4) NOT NULL default '1', ".
              "usersessionid varchar(32) default NULL, ".
              "lastlogin int(11) NOT NULL default '0'".
                        (($databasedriver != "sqlite") ? ", PRIMARY KEY (userid)" : "").
              ") ENGINE=MyISAM CHARSET=utf8";
              
            $db->Execute($query);
            $db->Execute("CREATE UNIQUE INDEX ".TBL_USERS."_username on ".TBL_USERS."(username)");
            // Insert the default record.
         }
         
         // Do we have an administrator?  If not, makes sure we do.
         $result = $db->Execute("SELECT * from ".TBL_USERS." where isAdmin = 1 limit 1") or die("Couldn't query admin count from user table. ". $db->ErrorMsg());
         if ($result->RecordCount() == 0) {
           $result = $db->Execute("SELECT * from ".TBL_USERS." where username = \"" . ADMIN_NAME ."\" limit 1") or die("Couldn't query admin user from user table. ". $db->ErrorMsg());
           if ($result->RecordCount() == 0) {
             $query = "INSERT INTO ".TBL_USERS." (username, password, isAdmin, created, active) ".
                        "VALUES (\"".ADMIN_NAME."\", \"".md5("logaholic")."\", 1, UNIX_TIMESTAMP(NOW()), 1)";
                     $db->Execute($query) or die("Error inserting admin record, ". $db->ErrorMsg());
           } else {
             $query = "UPDATE ".TBL_USERS." set isAdmin = 1 where username = \"".ADMIN_NAME."\"";
             $db->Execute($query) or die("Error updating admin record to administrative rights ".ADMIN_LEVEL.", ". $db->ErrorMsg());
           }
         }
         setGlobalSetting("DB_UserDataVersion", 1.00);
         $user_dbver = "1.00";
   }
	
	// Check to see the version number of the user database schema.    
    $user_dbver = getGlobalSetting("DB_UserDataVersion", 0.00);
	// var_dump($user_dbver);var_dump($loginSystemExists);
    if (($loginSystemExists == true) && ($user_dbver < USER_DB_VERSION)) { // var_dump("HOEREN NEUKEN");exit;
        if ($user_dbver < 1.0) {
            verifyUserTable();
        }
        if ($user_dbver < 2.0) {        
            $cols = $db->MetaColumns(TBL_USERS);       
            if (!isset($cols["ACCESSEDITPROFILE"])) {
                $db->Execute("alter table `".TBL_USERS."` add column accessEditProfile tinyint(4) NOT NULL default '1'");                            
            }
            setGlobalSetting("DB_UserDataVersion", 2.00);
            $user_dbver = "2.00";             
        }
        if ($user_dbver < 2.10) {        
            $cols = $db->MetaColumns(TBL_USERS);       
            if (!isset($cols["EXPIRES"])) {
                $db->Execute("alter table `".TBL_USERS."` add column expires int(11) NOT NULL default '0'");                            
            }
            setGlobalSetting("DB_UserDataVersion", 2.10);
            $user_dbver = "2.10";             
        }
        if ($user_dbver < 2.20) {        
            $cols = $db->MetaColumns(TBL_USERS);       
            if (!isset($cols["METADATA"])) {
                $db->Execute("alter table `".TBL_USERS."` add column metadata text");                            
            }
            setGlobalSetting("DB_UserDataVersion", 2.20);
            $user_dbver = "2.20";             
        }
                
    }	
	
      
	// If a "update_all_profiles" command is passed in, then iterate all the profiles and make sure they're
	// of a valid version.  If not, then force it to be updated now.
	if (isset($_REQUEST["update_all_profiles"]) && ($_REQUEST["update_all_profiles"] == 1)) {
		
		if (!isset($profile)) {
			$profile = new SiteProfile("");  // Load up an empty profile so that top doesn't try and load one.
			include "top.php";
		}
		
		if (($validUserRequired) && (!$session->isAdmin())) {
			die("Current user doesn't have access rights to update all profiles (admin rights required).");
		}
		$query = "Select profileid, profilename from ".TBL_PROFILES." where structure_version < " . 
								CURRENT_PROFILE_STRUCTURE_VERSION . " or structure_version is null";
		$result = $db->Execute($query);
		while ($profile_row = $result->FetchRow()) {
			$confname = $profile_row["profilename"];
			$profile = new SiteProfile($confname);
			updateLogTableForProfile($profile);
		}
	}
	
?>
