<?php
$timer_start = microtime(true);
define('_PS_ADMIN_DIR_', getcwd());

require(dirname(__FILE__).'/../config/config.inc.php');
require(dirname(__FILE__).'/functions.php');

// For retrocompatibility with "tab" parameter
if (!isset($_GET['controller']) && isset($_GET['tab']))
    $_GET['controller'] = strtolower($_GET['tab']);
if (!isset($_POST['controller']) && isset($_POST['tab']))
    $_POST['controller'] = strtolower($_POST['tab']);
if (!isset($_REQUEST['controller']) && isset($_REQUEST['tab']))
    $_REQUEST['controller'] = strtolower($_REQUEST['tab']);

    
    if (!$_GET['id_product'] or !$_GET['reduction']) 
        die(Tools::jsonEncode(array('error','empty get'))); 
        
        $_POST['sp_reduction']=calcReduction();
        $_POST['id_product'] = $_GET['id_product'];
        $_POST['sp_reduction_type']='amount';
        $_POST['submitPriceAddition'] = 1;
        $_POST['sp_id_product_attribute']=false;
        $_POST['sp_id_shop']=0;
        $_POST['sp_id_currency']=0;
        $_POST['sp_id_country']=0;
        $_POST['sp_id_group']=0;
        $_POST['sp_id_customer']=0;
        $_POST['leave_bprice'] = '-1' ;
        $_POST['sp_from_quantity']=1;
        
    
   $admin = new AdminProductsControllerCore();
   
   $specific_price = SpecificPrice::getByProductId($_GET['id_product']);
   while ($specific_price[0]['id_specific_price']) 
  {
   if ($_POST['id_specific_price'] = $specific_price[0]['id_specific_price']) 
       {
            $admin->tabAccess['delete'] = '1';
            $admin->ajaxProcessDeleteSpecificPrice($die='off');
       }
      $specific_price = SpecificPrice::getByProductId($_GET['id_product']); 
   }
   $admin->processPriceAddition();
    
    
   die(Tools::jsonEncode('$json'));        
   
   function calcReduction() {
       $product = new Product($_GET['id_product']);
       $price =  round($product->price*1.23,2);
       
       return $reduction = $price-$_GET['reduction'];
   }
        
?>
