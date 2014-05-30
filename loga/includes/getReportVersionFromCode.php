<?php
include_once("../common.inc.php");

$reportcode = $_POST['code'];

$r = base64_decode($reportcode);
$r = substr($r, strpos($r,"\$reports["));
$r = substr($r,0,(strpos($r,");")+2));

$r = eval($r);

foreach($reports as $report) {
	echo $report['Filename']."\n";
	echo $report['ReportVersion'];
}
?>