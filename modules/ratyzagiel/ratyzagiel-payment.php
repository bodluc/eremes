<?php

include_once(dirname(__FILE__).'/../../config/config.inc.php');
include_once(dirname(__FILE__).'/../../header.php');
include_once(dirname(__FILE__).'/ratyzagiel.php');

if (!$cookie->isLogged(true))
    Tools::redirect('authentication.php?back=order.php');
	
$RatyZagiel = new RatyZagiel();
echo $RatyZagiel->execPayment($cart);

include_once(dirname(__FILE__).'/../../footer.php');

?>