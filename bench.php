<?php
define("STARTMICROTIME", array_sum( explode(' ', microtime() ) ) );
while($i++ < 999999) 
  $temp_md5 = md5( rand(0,$i) );
$page_gen_time =  round(array_sum( explode(" ", microtime())) - STARTMICROTIME, 2);
echo " \n PG Time: $page_gen_time --- \n\n"
?>