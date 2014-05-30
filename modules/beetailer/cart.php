<?php 

include(dirname(__FILE__).'/beetailer.php');
include_once($module_home.'/../../init.php'); 

if(version_compare(_PS_VERSION_, "1.5", "<")){
  /* Fix needed for Prestashop v 1.4.4.0+ */
  if (!isset($cart->id) OR !$cart->id){
      $cart = new Cart();
      $cart->id_lang = $cookie->id_lang;
      $cart->id_currency = $cookie->id_currency;
      $cart->add();
      $cookie->id_cart = (int)($cart->id);
  }
}else{
  /* New 1.5.x context cart */
  $context =  Context::getContext();
  if(!$context->cart->id)
  {
    $context->cart->add();
    if ($context->cart->id)
      $context->cookie->id_cart = (int)$context->cart->id;
  }
  $cart = $context->cart;
} 

foreach($_GET['products'] AS $product){
  $cart->updateQty($product["qty"], (int)$product["id_product"], (int)$product["id_product_attribute"], NULL, 'up');
}
$cart->update();

/* Once step checkout support */
$url = Configuration::get('PS_ORDER_PROCESS_TYPE') == 1 ? "order-opc.php" : "order.php";
Tools::redirect($url.'?fb_ref='. $_GET['fb_ref']);
?>

