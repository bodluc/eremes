<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
// FILL THESE IN WITH YOUR SERVER'S DETAILS
set_time_limit(86400);
error_reporting(E_ALL ^ E_NOTICE);
function getmicrotime() {
    list($usec, $sec) = explode(" ", microtime()); 
    return ((float)$usec + (float)$sec); 
}

$start = getmicrotime();
  

$mysqlhost = $_REQUEST['mysqlhost'];
 
if ($mysqlhost!="") {
$mysqlusr = $_POST['mysqluser'];
$mysqlpass = $_POST['mysqlpass'];

if ($mysqlusr) {
mysql_connect($mysqlhost,$mysqlusr,$mysqlpass);
}

?>
<html>
<head><title>MySQL Command Line</title></head>
<body>
<?php
if (isset($_POST['query'])) {
   
        if (get_magic_quotes_gpc()) $_POST['query'] = stripslashes($_POST['query']);
        echo('<p><b>Query:</b><br />'.nl2br($_POST['query']).'</p>');
    mysql_select_db($_POST['db']);
        $result = mysql_query($_POST['query']);
        if ($result) {
                if (@mysql_num_rows($result)) {
                        ?>
                        <p><b>Result Set:</b></p>
                        <table border="1">
                        <thead>
                        <tr>
                        <?php
                        for ($i=0;$i<mysql_num_fields($result);$i++) {
                                echo('<th>'.mysql_field_name($result,$i).'</th>');
                        }
                        ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        while ($row = mysql_fetch_row($result)) {
                                echo('<tr>');
                                for ($i=0;$i<mysql_num_fields($result);$i++) {
                                        echo('<td>'.$row[$i].'</td>');
                                }
                                echo('</tr>');
                        }
                        ?>
                        </tbody>
                        </table>
                        <?php
                } else {
                        echo('<p><b>Query OK:</b> '.mysql_affected_rows().' rows affected.</p>');
                }
        } else {
                echo('<p><b>Query Failed:</b> '.mysql_error().'</p>');
        }
        echo('<hr />');
        echo "Query took ".(getmicrotime()-$start)." seconds";
}
?>
<form action="<?php echo $_SERVER['PHP_SELF']?>" method="POST">
<p>Target Database:
<select name="db">
<?php
$dbs = mysql_list_dbs();
for ($i=0;$i<mysql_num_rows($dbs);$i++) {
        $dbname = mysql_db_name($dbs,$i);
        if ($dbname == $_POST['db'])
                echo("<option selected>$dbname</option>");
        else
                echo("<option>$dbname</option>");
}
?>
</select>
</p>
<p>SQL Query:<br />
<input type="text" name="query" size=300 value="<?php echo htmlspecialchars($_POST['query'])?>">
<!--<textarea onFocus="this.select()" cols="60" rows="5" name="query">
<?php htmlspecialchars($_POST['query'])?>
</textarea>-->
</p>
<p><!--<input type="submit" name="submitquery" value="Submit Query (Alt-S)" accesskey="S" />-->
<input type="submit" name="submitquery" value="Submit Query" /></p>

Mysql user:<input type=text name="mysqluser" value="<?php echo $_POST['mysqluser']?>">, Mysql pass
<input type=password name="mysqlpass" value="<?php echo $_POST['mysqlpass']?>">, mysql host <input type=text name="mysqlhost" value="<?php echo $_POST['mysqlhost']?>">   


</form>
</body>
</html>
<?php
} else {
?>
    
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
  <title></title>
  <style>
  TD {
    line-height:14px;
  }
  </style>
  </head>
  <body>
<?php
//===========================================================================
//* --       Logaholic Web Analytics and Web Site Statistics             -- *
//===========================================================================
//           URL:   http://www.logaholic.com/    EMAIL: michael@logaholic.com
//  Copyright (C) 2005-2009 Logaholic BV, Erkelens International BV (http://www.logaholic.com)
// --------------------------------------------------------------------------
// NOTICE: Do NOT remove the copyright and/or license information from any files. 
//         doing so will automatically terminate your rights to use program.
//         Any changes you make for your personal use are unsupported by 
//                 Logaholic.
// --------------------------------------------------------------------------
// LICENSE:
//          This is commercial software protected by international copyright laws. 
//         You are specifically prohibited from copying or otherwise distibuting
//         Logaholic without prior consent of Erkelens International BV (creator
//         of Logaholic). 
//         This program is distributed in the hope that it will be useful,
//         but WITHOUT ANY WARRANTY; without even the implied warranty of
//         MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
//
//         You should have received a copy of the full License Agreement
//         along with this program in a file named LICENSE.txt .
//===========================================================================

if (isset($_REQUEST)) {
    while(list($varname, $varvalue) = each($_REQUEST)) { $$varname = $varvalue; }
}
if (isset($_SERVER)) { 
    while (list($varname, $varvalue) = each($_ENV)) { $$varname = $varvalue; }
    while (list($varname, $varvalue) = each($_SERVER)) { $$varname = $varvalue; }
}
function CheckLogFormat($logline) {
  $Jan=1;
  $Feb=2;
  $Mar=3;
  $Apr=4;
  $May=5;
  $Jun=6;
  $Jul=7;
  $Aug=8;
  $Sep=9;
  $Oct=10;
  $Nov=11;
  $Dec=12;

    $lparts=explode(" ",$logline);
    $ip  = $lparts[0];
    $long = ip2long($ip);
    
    if (substr($logline, 0, 7)=="#Fields") {
    echo '<font color=green>Valid log file format (W3C Extended)</font>';
  } else {
      if ($long == -1 || $long === FALSE) {
          echo '<font color=red>Invalid file format</font>';
      } else {
          //valid
          $logtime=substr(trim($lparts[3]),1);
          $timeparts=explode(":", $logtime);
          $dateline=explode("/",$timeparts[0]);
          $month = $$dateline[1];
          $logtimestamp=mktime($timeparts[1],$timeparts[2],$timeparts[3],$month,$dateline[0],$dateline[2]);
          if (date("Y",$logtimestamp) > 1989) {
              echo '<font color=green>Valid log file format (NCSA Common/Combined)</font>';
          } else {
            echo '<font color=red>Invalid file format</font>';
          }
       }
  }
}


//check log file
$q=urldecode($q);
$q=stripslashes($q);
if (!$q) {
    //echo "<P>Usage: logviewer.php?q=[your log file location]</P><P> Example: logviewer.php?q=/home/mydomain/logs/access_log</P>";
    //echo "<P>Optional:<ul><li>&offset=0 - add an offset in bytes to skip through the file</Li><li>&lines=10 - number of lines to show on one screen</li><li>&php=1 to show PHP server info</ul></P>";
    exit();
}
if (file_exists($q)) {
    $what = filetype($q);
    if ($what=="dir") {
        echo "<font color=green>Directory Path: </font>$q<p>";
        if (substr($q,-1,1)!="/") {
            $q=$q."/";
        }
        if ($handle = @opendir($q)) {
            
            echo "<strong>Contents of this directory:</strong><ul>";
            while ($file = readdir($handle)) {
                if ($file[0] != '.') {
                    $fullfile="$q$file";
                    if (@filetype($fullfile)=="dir") {
                        echo "<li> <font color=#0A3057>/$file</font><br>";
                    } else {
                        echo "<li> $file<br>";
                    }
                }
            }
            closedir($handle);
            echo "</ul>";
        } else {
            echo "Can't scan directory";
        }
        
        
    
    } else if ($what=="file") {
        echo "<font color=green>The File Exists: </font>$q<P>";
        $lfown=fileowner($q);
        $lfperm=substr(sprintf('%o', fileperms($q)), -4);     
        $lfmb=number_format(((filesize($q)/1024)/1024),2);

        echo "<b><font color=gray>File Info:</font></b><br>Log file location: <font color=green>$q</font><br>";
        echo "Size: $lfmb MB (".filesize($q).")<br>";

        if ($_ENV['OS']!="Windows_NT") {
            if (extension_loaded('posix')) {
                        $list=posix_getpwuid($lfown);
                        $lfown=$list['name'];    
            }
          echo "Owner: $lfown<br>";
          echo "Permissions: $lfperm<br>";
    }
        
        echo "Access Check: ";
        if (is_readable($q)) {
            echo 'The file is readable<br>';
            if (substr($q,-3,3)==".gz") {
                
                $zmode="gz";
            } else {
             $zmode="";    
            }
            
            
                $logfile = gzopen($q, "r");
                
                $logline=gzgets($logfile);
                $logline=gzgets($logfile);
                $logline=gzgets($logfile);
                $logline=gzgets($logfile);        
                
                echo "Check log file format: ";
                //echo $logline;
                CheckLogFormat($logline);
                echo "<p>";
                if (!$offset) {
          $offset=0;
        }
        if (!$lines) {
          $lines=10;
        }
                fseek($logfile,$offset);
                $i=0;
                echo "<table width=1000 border=1><tr><td><font face=\"ms sans serif\" size=1>";
                while ($i < $lines) {
          $logline=gzgets($logfile);
          echo "<nobr>$logline</nobr><br>\n";
          $i++;
        } 
                echo "</font></td></tr><table>";
                gzclose($logfile);
                    
        
        } else {
            echo 'The file is not readable<br>';
            
        }
        echo "";
        
    }
} else {
    echo "<font color=red>File Not Found: </font>$q<P>";
}
if ($php==1) {
  echo phpinfo();
}

?>
</body>
</html>

<?php
}
?>    

