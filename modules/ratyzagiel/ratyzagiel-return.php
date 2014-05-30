<?php

include_once(dirname(__FILE__).'/../../config/config.inc.php');
include_once(dirname(__FILE__).'/../../header.php');
include_once(dirname(__FILE__).'/ratyzagiel.php');

$RatyZagiel = new RatyZagiel();


if(Tools::getValue('order_id') != 0 && Tools::getValue('id_wniosku') != '')
{
	$cart = new Cart(Tools::getValue('order_id'));
	
	$errors = '';
	
	if (!$cart->id) $errors .= '<p>Brak koszyka o podanym ID</p>';
	if (Order::getOrderByCartId(Tools::getValue('order_id'))) $errors .= '<p>Zamówienie o podanym ID już istnieje</p>';
	
	if($errors == '')
	{ 
	
		if($RatyZagiel->validateOrder(intval($cart->id), _PS_OS_PAYMENT_, floatval($cart->getOrderTotal()), $RatyZagiel->displayName))
		{
			sleep(2);
			$smarty->assign(array('HOOK_PAYMENT_RETURN' => $RatyZagiel->paymentReturn()));
			$smarty->display(_PS_THEME_DIR_.'order-confirmation.tpl'); 
		
		}
	 
	}
	else
	{
		$smarty->assign(array('HOOK_PAYMENT_RETURN' => $errors));
		$smarty->display(_PS_THEME_DIR_.'order-confirmation.tpl'); 	
	}
		
}
if (Tools::getValue('status') == 'false')
{
	$smarty->assign(array('HOOK_PAYMENT_RETURN' => $RatyZagiel->paymentReturn()));
	$smarty->display(_PS_THEME_DIR_.'order-confirmation.tpl'); 	
}

include_once('../../footer.php');
