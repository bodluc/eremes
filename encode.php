<?php

return;
    require 'classes/db/Db.php';
    
    

 
 $db = mysql_connect('localhost','root','') or die("Database error"); 
  $charset = mysql_client_encoding($db);
 mysql_set_charset('utf8',$db);  
 mysql_select_db('dev', $db); 
 mysql_query('SET NAMES \'utf8\'', $db) ;
  $charset = mysql_client_encoding($db);
//SOLUTION::  add this comment before your 1st query -- force multiLanuage support 
  
  $sql = "SELECT * FROM `ps_product_lang` WHERE `description_short`!=''";
    $result = mysql_query($sql);
  
 while ($row = mysql_fetch_assoc($result)) {
     $id = $row['id_product'];
    $short = base64_decode($row['description_short']);
    $update = "UPDATE `ps_product_lang` SET `description_short`='".mysql_real_escape_string($short)."' WHERE `id_product`=$id"   ;
    $ret = mysql_query($update);
    if ($ret) echo $id;
}

 $sql = "SELECT * FROM `ps_product_lang` WHERE `description`!=''";
    $result = mysql_query($sql);
  
 while ($row = mysql_fetch_assoc($result)) {
     $id = $row['id_product'];
    $desc =  base64_decode($row['description']);
    $update = "UPDATE `ps_product_lang` SET `description`='".mysql_real_escape_string($desc)."' WHERE `id_product`=$id"   ;
    $ret = mysql_query($update);
    if ($ret) echo $id;
}
  
  
?>
