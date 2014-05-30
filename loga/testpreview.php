<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
include "common.inc.php";
$profile = new SiteProfile($conf);
$sptablename=$profile->tablename."_splittests";
$testid = $_REQUEST["testid"];
$page=strtoupper($_REQUEST["page"]);
if ($page=="A") {
  $otherpage="B";
  $q = $db->Execute("select testurl,pagea from $sptablename where id=$testid");
} else {
  $q = $db->Execute("select testurl,pageb from $sptablename where id=$testid");
  $otherpage="A";
}
$data=$q->FetchRow();
$url= "http://".$_SERVER["HTTP_HOST"]. $_SERVER["PHP_SELF"];
echo "<div style='width:100%;background-color:#f0f0f0;border-style:ridge;border-width:1px;'>"._LOGAHOLIC_SPLIT_TEST_PREVIEW." <b>"._PAGE." $page</b> - "._SEE." <a href={$url}?labels=_TEST_CENTER&conf=$conf&testid=$testid&page=$otherpage>"._PAGE." $otherpage</a></div>";
echo "<base href=".$data[0].">";
echo stripslashes($data[1]);
?>
