<?php                                                                              
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/                 
/**         
* @desc This file will enable IP scrambling in order to comply with German Law. 
*/
  
function encodeSPQR($ip) {
      $start=0;
      $gip="";
      do { 
          $l= substr($ip,$start,1);         
          $gip .= chr(ord($l)+22);
          $start++;        
      } while (strlen(substr($ip,$start,1)) > 0);
      return $gip;       
}
  
function decodeSPQR($ip) {
    $start=0;
    $rip="";
    do { 
          $l= substr($ip,$start,1);         
          $rip .= chr(ord($l)-22);
          $start++;        
    } while (strlen(substr($ip,$start,1)) > 0);
    return $rip;  
}

?>
