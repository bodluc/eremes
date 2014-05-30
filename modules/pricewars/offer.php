<?php
	error_reporting(0);
	require_once('../../config/config.inc.php');
	ini_set("memory_limit", "100M");
	include('./pricewars.php');
	
	header("Content-type: text/xml");
	//header("content-type: text/plain");
	
	set_time_limit(240);

	$pricewars = new Pricewars();
	$pricewars->getXML();
?>