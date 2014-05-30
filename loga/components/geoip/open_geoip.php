<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
if (file_exists("components/geoip/GeoIPCity.dat")) {
	include_once("components/geoip/geoipcity.inc");
	$gi = geoip_open("components/geoip/GeoIPCity.dat",GEOIP_STANDARD);
} else if (file_exists("components/geoip/GeoLiteCity.dat")) {
	include_once("components/geoip/geoipcity.inc");
	$gi = geoip_open("components/geoip/GeoLiteCity.dat",GEOIP_STANDARD);
}

?>