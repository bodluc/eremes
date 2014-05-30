<style>
#upgrade-content{ float:left; margin:0 30px; width: 100%;}
</style>
<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
//Runs local
include_once 'top.php';
if(!$session->isAdmin() && $validUserRequired){
	echoWarning("You have no permission to upgrade Logaholic.","margin:10px;");
	exit();
}
include_once 'includes/Upgrade_logaholic.php';
$ttp = new Upgrade_logaholic();

$host = $_SERVER['HTTP_HOST'];
$path = str_replace("upgrade.php","",$_SERVER['REQUEST_URI']);
//$path = getcwd();
echo "<div id='upgrade-content'>";
echo "<h3>Logaholic Backup</h3>";

# Create a backup of your installation
$backupDir = logaholic_dir() . "files/backup/";
if(!file_exists( $backupDir.LOGAHOLIC_VERSION_NUMBER .".tar.gz") && !file_exists( $backupDir.LOGAHOLIC_VERSION_NUMBER .".zip") ) {

	if(!is_dir($backupDir . LOGAHOLIC_VERSION_NUMBER)) {
		$ttp->Recursive_walk('', '', LOGAHOLIC_VERSION_NUMBER);
	}

	if(strpos(strtolower($_SERVER['SERVER_SOFTWARE']), 'win32') !== false || strpos(strtolower($_SERVER['SERVER_SOFTWARE']), 'win64') !== false) {
		$ttp->zipBackup($backupDir,LOGAHOLIC_VERSION_NUMBER);
	} else {
		exec("cd $backupDir; tar -czf ". LOGAHOLIC_VERSION_NUMBER .".tar.gz ".LOGAHOLIC_VERSION_NUMBER); 
		exec("rm -rf $backupDir". LOGAHOLIC_VERSION_NUMBER); 
	}
}



echo "Logaholic created an backup of your installation in case something goes wrong. 
<br/> Your backup is placed in: <b>".str_replace("upgrade.php", "",$_SERVER["SCRIPT_FILENAME"])."/files/backup/".LOGAHOLIC_VERSION_NUMBER."/</b>";
echo '<h3>Upgrade Logaholic</h3>
<iframe frameBorder="0" src="http://updates.logaholic.com/index1.php?host='.$host.'&Product='._LOGAHOLIC_PRODUCTNAME.'&version='.LOGAHOLIC_VERSION_NUMBER.'&Path='.$path.'" width="100%" height="1600px">
</iframe>';
echo "</div>";
?>