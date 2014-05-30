<?php /* Smarty version Smarty-3.1.8, created on 2012-11-26 12:50:40
         compiled from "/home/bodo/public_html/dev/admin-dev/themes/default/template/controllers/carts/helpers/view/view.tpl" */ ?>
<?php /*%%SmartyHeaderCode:176432260950b3579081b368-43312919%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '615bc3b4483f30d8be8aec6c66037b232f1251d4' => 
    array (
      0 => '/home/bodo/public_html/dev/admin-dev/themes/default/template/controllers/carts/helpers/view/view.tpl',
      1 => 1347489974,
      2 => 'file',
    ),
    '52bf8c8ed6d7d94b92322c74a1466b6cf1ce08a0' => 
    array (
      0 => '/home/bodo/public_html/dev/admin-dev/themes/default/template/helpers/view/view.tpl',
      1 => 1347489972,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '176432260950b3579081b368-43312919',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'show_toolbar' => 0,
    'toolbar_btn' => 0,
    'toolbar_scroll' => 0,
    'title' => 0,
    'name_controller' => 0,
    'hookName' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_50b35791165c27_40147638',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50b35791165c27_40147638')) {function content_50b35791165c27_40147638($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/home/bodo/public_html/dev/tools/smarty/plugins/modifier.escape.php';
if (!is_callable('smarty_function_math')) include '/home/bodo/public_html/dev/tools/smarty/plugins/function.math.php';
?>

<?php if ($_smarty_tpl->tpl_vars['show_toolbar']->value){?>
	<?php echo $_smarty_tpl->getSubTemplate ("toolbar.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('toolbar_btn'=>$_smarty_tpl->tpl_vars['toolbar_btn']->value,'toolbar_scroll'=>$_smarty_tpl->tpl_vars['toolbar_scroll']->value,'title'=>$_smarty_tpl->tpl_vars['title']->value), 0);?>

<?php }?>

<div class="leadin"></div>


	
	<div id="container-customer">
		<h2><img src="../img/admin/basket.png" /><span><?php if ($_smarty_tpl->tpl_vars['customer']->value->id){?><?php echo $_smarty_tpl->tpl_vars['customer']->value->firstname;?>
 <?php echo $_smarty_tpl->tpl_vars['customer']->value->lastname;?>
<?php }else{ ?><?php echo smartyTranslate(array('s'=>'Guest'),$_smarty_tpl);?>
<?php }?> - <?php echo smartyTranslate(array('s'=>'Cart #'),$_smarty_tpl);?>
<?php echo sprintf("%06d",$_smarty_tpl->tpl_vars['cart']->value->id);?>
</span><span style="color:#585A69;padding-left:10px;"><?php echo smartyTranslate(array('s'=>'on'),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['cart']->value->date_upd;?>
</span></h2>
				<div class="separation"></div>
	
	<div class="info-customer-left">
		<h2><img src="../img/admin/vcard.png" /><?php echo smartyTranslate(array('s'=>'Customer information'),$_smarty_tpl);?>
</h2>
		<span style="font-size: 14px;">
		<?php if ($_smarty_tpl->tpl_vars['customer']->value->id){?>
			<a href="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminCustomers'), 'htmlall', 'UTF-8');?>
&id_customer=<?php echo $_smarty_tpl->tpl_vars['customer']->value->id;?>
&viewcustomer"><?php echo $_smarty_tpl->tpl_vars['customer']->value->firstname;?>
 <?php echo $_smarty_tpl->tpl_vars['customer']->value->lastname;?>
</a></span>
			<?php echo smartyTranslate(array('s'=>'#'),$_smarty_tpl);?>
<?php echo $_smarty_tpl->tpl_vars['customer']->value->id;?>
<br />
			<a style="text-decoration: underline; color:#268CCD;" href="mailto:<?php echo $_smarty_tpl->tpl_vars['customer']->value->email;?>
"><?php echo $_smarty_tpl->tpl_vars['customer']->value->email;?>
</a>	<br /><br />
			<?php echo smartyTranslate(array('s'=>'Account registration date:'),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['dateFormat'][0][0]->dateFormat(array('date'=>$_smarty_tpl->tpl_vars['customer']->value->date_add),$_smarty_tpl);?>
<br />
			<?php echo smartyTranslate(array('s'=>'Valid orders placed:'),$_smarty_tpl);?>
 <b><?php echo $_smarty_tpl->tpl_vars['customer_stats']->value['nb_orders'];?>
</b><br />
			<?php echo smartyTranslate(array('s'=>'Total spent since registration:'),$_smarty_tpl);?>
 <b><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayWtPriceWithCurrency'][0][0]->displayWtPriceWithCurrency(array('price'=>$_smarty_tpl->tpl_vars['customer_stats']->value['total_orders'],'currency'=>$_smarty_tpl->tpl_vars['currency']->value),$_smarty_tpl);?>
</b><br />
		<?php }else{ ?>
			<?php echo smartyTranslate(array('s'=>'Guest not registered'),$_smarty_tpl);?>

		<?php }?>
		</span>
	</div>
	<div class="info-customer-right">
		<h2><img src="../img/admin/cart.gif" /> <?php echo smartyTranslate(array('s'=>'Order information'),$_smarty_tpl);?>
</h2>
		<span>
		<?php if ($_smarty_tpl->tpl_vars['order']->value->id){?>
			<a href="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminOrders'), 'htmlall', 'UTF-8');?>
&id_order=<?php echo $_smarty_tpl->tpl_vars['order']->value->id;?>
&vieworder"> <?php echo smartyTranslate(array('s'=>'Order #%d','sprintf'=>sprintf("%06d",$_smarty_tpl->tpl_vars['order']->value->id)),$_smarty_tpl);?>
</a></span>
			<br /><br />
			<?php echo smartyTranslate(array('s'=>'Made on:'),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['dateFormat'][0][0]->dateFormat(array('date'=>$_smarty_tpl->tpl_vars['order']->value->date_add),$_smarty_tpl);?>
<br /><br /><br /><br />
		<?php }else{ ?>
			<?php echo smartyTranslate(array('s'=>'No order created from this cart'),$_smarty_tpl);?>
</span>
			<?php if ($_smarty_tpl->tpl_vars['customer']->value->id){?><p><a class="button" href="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminOrders'), 'htmlall', 'UTF-8');?>
&id_cart=<?php echo $_smarty_tpl->tpl_vars['cart']->value->id;?>
&addorder"><?php echo smartyTranslate(array('s'=>'Create an order with from cart'),$_smarty_tpl);?>
</a></p><?php }?>
		<?php }?>
	</div>
	<br style="clear:both;" />
				<div class="separation"></div>
				

	<h2><img src="../img/admin/cart.gif" alt="<?php echo smartyTranslate(array('s'=>'Products'),$_smarty_tpl);?>
" /><?php echo smartyTranslate(array('s'=>'Cart summary'),$_smarty_tpl);?>
</h2>
	<br/>
		<table style="width:100%;" cellspacing="0" cellpadding="0" class="table" id="orderProducts">
		<thead>
			<tr>
			<th height="39" align="center" style="width: 60px">&nbsp;</th>
			<th><?php echo smartyTranslate(array('s'=>'Product'),$_smarty_tpl);?>
</th>
			<th style="width: 80px; text-align: center"><?php echo smartyTranslate(array('s'=>'Unit price'),$_smarty_tpl);?>
</th>
			<th style="width: 20px; text-align: center"><?php echo smartyTranslate(array('s'=>'Quantity'),$_smarty_tpl);?>
</th>
			<th style="width: 30px; text-align: center"><?php echo smartyTranslate(array('s'=>'Stock'),$_smarty_tpl);?>
</th>
			<th style="width: 90px; text-align: right; font-weight:bold;"><?php echo smartyTranslate(array('s'=>'Total'),$_smarty_tpl);?>
</th>
		</tr>
		<tbody>
		<?php  $_smarty_tpl->tpl_vars['product'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['product']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['products']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['product']->key => $_smarty_tpl->tpl_vars['product']->value){
$_smarty_tpl->tpl_vars['product']->_loop = true;
?>
			<?php if (isset($_smarty_tpl->tpl_vars['customized_datas']->value[$_smarty_tpl->tpl_vars['product']->value['id_product']][$_smarty_tpl->tpl_vars['product']->value['id_product_attribute']][$_smarty_tpl->tpl_vars['product']->value['id_address_delivery']])){?>
				<tr>
					<td align="center"><?php echo $_smarty_tpl->tpl_vars['product']->value['image'];?>
</td>
					<td><a href="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminProducts'), 'htmlall', 'UTF-8');?>
&id_product=<?php echo $_smarty_tpl->tpl_vars['product']->value['id_product'];?>
&updateproduct">
								<span class="productName"><?php echo $_smarty_tpl->tpl_vars['product']->value['name'];?>
</span><?php if (isset($_smarty_tpl->tpl_vars['product']->value['attributes'])){?><br /><?php echo $_smarty_tpl->tpl_vars['product']->value['attributes'];?>
<?php }?><br />
							<?php if ($_smarty_tpl->tpl_vars['product']->value['reference']){?><?php echo smartyTranslate(array('s'=>'Ref:'),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['product']->value['reference'];?>
<?php }?>
							<?php if ($_smarty_tpl->tpl_vars['product']->value['reference']&&$_smarty_tpl->tpl_vars['product']->value['supplier_reference']){?> / <?php echo $_smarty_tpl->tpl_vars['product']->value['supplier_reference'];?>
<?php }?>
						</a>
					</td>
					<td align="center"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayWtPriceWithCurrency'][0][0]->displayWtPriceWithCurrency(array('price'=>$_smarty_tpl->tpl_vars['product']->value['price_wt'],'currency'=>$_smarty_tpl->tpl_vars['currency']->value),$_smarty_tpl);?>
</td>
					<td align="center" class="productQuantity"><?php echo $_smarty_tpl->tpl_vars['product']->value['customization_quantity'];?>
</td>
					<td align="center" class="productQuantity"><?php echo $_smarty_tpl->tpl_vars['product']->value['qty_in_stock'];?>
</td>
					<td align="right"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayWtPriceWithCurrency'][0][0]->displayWtPriceWithCurrency(array('price'=>$_smarty_tpl->tpl_vars['product']->value['total_customization_wt'],'currency'=>$_smarty_tpl->tpl_vars['currency']->value),$_smarty_tpl);?>
</td>
				</tr>
				<?php  $_smarty_tpl->tpl_vars['customization'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['customization']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['customized_datas']->value[$_smarty_tpl->tpl_vars['product']->value['id_product']][$_smarty_tpl->tpl_vars['product']->value['id_product_attribute']][$_smarty_tpl->tpl_vars['product']->value['id_address_delivery']]; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['customization']->key => $_smarty_tpl->tpl_vars['customization']->value){
$_smarty_tpl->tpl_vars['customization']->_loop = true;
?>
				<tr>
					<td colspan="2">
					<?php  $_smarty_tpl->tpl_vars['datas'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['datas']->_loop = false;
 $_smarty_tpl->tpl_vars['type'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['customization']->value['datas']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['datas']->key => $_smarty_tpl->tpl_vars['datas']->value){
$_smarty_tpl->tpl_vars['datas']->_loop = true;
 $_smarty_tpl->tpl_vars['type']->value = $_smarty_tpl->tpl_vars['datas']->key;
?>
						<?php if ($_smarty_tpl->tpl_vars['type']->value==constant('Product::CUSTOMIZE_FILE')){?>
							<ul style="margin: 0; padding: 0; list-style-type: none;">
							<?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['data']->_loop = false;
 $_smarty_tpl->tpl_vars['index'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['datas']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value){
$_smarty_tpl->tpl_vars['data']->_loop = true;
 $_smarty_tpl->tpl_vars['index']->value = $_smarty_tpl->tpl_vars['data']->key;
?>
									<li style="display: inline; margin: 2px;">
										<a href="displayImage.php?img=<?php echo $_smarty_tpl->tpl_vars['data']->value['value'];?>
&name=<?php echo $_smarty_tpl->tpl_vars['order']->value->id;?>
-file<?php echo $_smarty_tpl->getVariable('smarty')->value['foreach']['count']['index'];?>
" target="_blank">
										<img src="<?php echo $_smarty_tpl->tpl_vars['pic_dir']->value;?>
<?php echo $_smarty_tpl->tpl_vars['data']->value['value'];?>
_small" alt="" /></a>
									</li>
							<?php } ?>
							</ul>
						<?php }elseif($_smarty_tpl->tpl_vars['type']->value==constant('Product::CUSTOMIZE_TEXTFIELD')){?>
							<ul style="margin-bottom: 4px; padding: 0; list-style-type: none;">
							<?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['data']->_loop = false;
 $_smarty_tpl->tpl_vars['index'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['datas']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value){
$_smarty_tpl->tpl_vars['data']->_loop = true;
 $_smarty_tpl->tpl_vars['index']->value = $_smarty_tpl->tpl_vars['data']->key;
?>
								<li><?php if ($_smarty_tpl->tpl_vars['data']->value['name']){?><?php echo $_smarty_tpl->tpl_vars['data']->value['name'];?>
<?php }else{ ?><?php echo smartyTranslate(array('s'=>'Text #'),$_smarty_tpl);?>
<?php echo $_smarty_tpl->getVariable('smarty')->value['foreach']['count']['index'];?>
<?php }?><?php echo smartyTranslate(array('s'=>':'),$_smarty_tpl);?>
<b><?php echo $_smarty_tpl->tpl_vars['data']->value['value'];?>
</b></li>
							<?php } ?>
							</ul>
						<?php }?>
					<?php } ?>
					</td>
					<td align="center"></td>
					<td align="center" class="productQuantity"><?php echo $_smarty_tpl->tpl_vars['customization']->value['quantity'];?>
</td>
					<td align="center" class="productQuantity"></td>
					<td align="center"></td>
				</tr>
				<?php } ?>
			<?php }?>
			
			<?php if ($_smarty_tpl->tpl_vars['product']->value['cart_quantity']>$_smarty_tpl->tpl_vars['product']->value['customization_quantity']){?>
				<tr>
					<td align="center"><?php echo $_smarty_tpl->tpl_vars['product']->value['image'];?>
</td>
					<td>
						<a href="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminProducts'), 'htmlall', 'UTF-8');?>
&id_product=<?php echo $_smarty_tpl->tpl_vars['product']->value['id_product'];?>
&updateproduct">
						<span class="productName"><?php echo $_smarty_tpl->tpl_vars['product']->value['name'];?>
</span><?php if (isset($_smarty_tpl->tpl_vars['product']->value['attributes'])){?><br /><?php echo $_smarty_tpl->tpl_vars['product']->value['attributes'];?>
<?php }?><br />
						<?php if ($_smarty_tpl->tpl_vars['product']->value['reference']){?><?php echo smartyTranslate(array('s'=>'Ref:'),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['product']->value['reference'];?>
<?php }?>
						<?php if ($_smarty_tpl->tpl_vars['product']->value['reference']&&$_smarty_tpl->tpl_vars['product']->value['supplier_reference']){?> / <?php echo $_smarty_tpl->tpl_vars['product']->value['supplier_reference'];?>
<?php }?>
						</a>
					</td>
					<td align="center"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayWtPriceWithCurrency'][0][0]->displayWtPriceWithCurrency(array('price'=>$_smarty_tpl->tpl_vars['product']->value['product_price'],'currency'=>$_smarty_tpl->tpl_vars['currency']->value),$_smarty_tpl);?>
</td>
					<td align="center" class="productQuantity"><?php echo smarty_function_math(array('equation'=>'x - y','x'=>$_smarty_tpl->tpl_vars['product']->value['cart_quantity'],'y'=>intval($_smarty_tpl->tpl_vars['product']->value['customization_quantity'])),$_smarty_tpl);?>
</td>
					<td align="center" class="productQuantity"><?php echo $_smarty_tpl->tpl_vars['product']->value['qty_in_stock'];?>
</td>
					<td align="right"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayWtPriceWithCurrency'][0][0]->displayWtPriceWithCurrency(array('price'=>$_smarty_tpl->tpl_vars['product']->value['product_total'],'currency'=>$_smarty_tpl->tpl_vars['currency']->value),$_smarty_tpl);?>
</td>
				</tr>
			<?php }?>
		<?php } ?>
		<tr class="cart_total_product">
			<td colspan="5"><?php echo smartyTranslate(array('s'=>'Total products:'),$_smarty_tpl);?>
</td>
			<td class="price bold right"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayWtPriceWithCurrency'][0][0]->displayWtPriceWithCurrency(array('price'=>$_smarty_tpl->tpl_vars['total_products']->value,'currency'=>$_smarty_tpl->tpl_vars['currency']->value),$_smarty_tpl);?>
</td>
		</tr>
	
		<?php if ($_smarty_tpl->tpl_vars['total_discounts']->value!=0){?>
			<tr class="cart_total_voucher">
				<td colspan="5"><?php echo smartyTranslate(array('s'=>'Total vouchers:'),$_smarty_tpl);?>
</td>
				<td class="price-discount bold right"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayWtPriceWithCurrency'][0][0]->displayWtPriceWithCurrency(array('price'=>$_smarty_tpl->tpl_vars['total_discounts']->value,'currency'=>$_smarty_tpl->tpl_vars['currency']->value),$_smarty_tpl);?>
</td>
			</tr>
		<?php }?>
		<?php if ($_smarty_tpl->tpl_vars['total_wrapping']->value>0){?>
			<tr class="cart_total_voucher">
				<td colspan="5"><?php echo smartyTranslate(array('s'=>'Total gift-wrapping:'),$_smarty_tpl);?>
</td>
				<td class="price-discount bold right"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayWtPriceWithCurrency'][0][0]->displayWtPriceWithCurrency(array('price'=>$_smarty_tpl->tpl_vars['total_wrapping']->value,'currency'=>$_smarty_tpl->tpl_vars['currency']->value),$_smarty_tpl);?>
</td>
			</tr>
		<?php }?>
		<?php if ($_smarty_tpl->tpl_vars['cart']->value->getOrderTotal(true,Cart::ONLY_SHIPPING)>0){?>
			<tr class="cart_total_delivery">
				<td colspan="5"><?php echo smartyTranslate(array('s'=>'Total shipping:'),$_smarty_tpl);?>
</td>
				<td class="price bold right"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayWtPriceWithCurrency'][0][0]->displayWtPriceWithCurrency(array('price'=>$_smarty_tpl->tpl_vars['total_shipping']->value,'currency'=>$_smarty_tpl->tpl_vars['currency']->value),$_smarty_tpl);?>
</td>
			</tr>
		<?php }?>
		<tr class="cart_total_price">
			<td colspan="5" class="bold"><?php echo smartyTranslate(array('s'=>'Total:'),$_smarty_tpl);?>
</td>
			<td class="price bold right"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayWtPriceWithCurrency'][0][0]->displayWtPriceWithCurrency(array('price'=>$_smarty_tpl->tpl_vars['total_price']->value,'currency'=>$_smarty_tpl->tpl_vars['currency']->value),$_smarty_tpl);?>
</td>
		</tr>
	</table>
	
	<?php if ($_smarty_tpl->tpl_vars['discounts']->value){?>
		<table cellspacing="0" cellpadding="0" class="table" style="width:280px; margin:15px 0px 0px 420px;">
		<tr>
			<th><img src="../img/admin/coupon.gif" alt="<?php echo smartyTranslate(array('s'=>'Discounts'),$_smarty_tpl);?>
" /><?php echo smartyTranslate(array('s'=>'Discount name'),$_smarty_tpl);?>
</th>
			<th align="center" style="width: 100px"><?php echo smartyTranslate(array('s'=>'Value'),$_smarty_tpl);?>
</th>
		</tr>
		<?php  $_smarty_tpl->tpl_vars['discount'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['discount']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['discounts']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['discount']->key => $_smarty_tpl->tpl_vars['discount']->value){
$_smarty_tpl->tpl_vars['discount']->_loop = true;
?>
			<tr>
				<td><a href="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminDiscounts'), 'htmlall', 'UTF-8');?>
&id_discount=<?php echo $_smarty_tpl->tpl_vars['discount']->value['id_discount'];?>
&updatediscount"><?php echo $_smarty_tpl->tpl_vars['discount']->value['name'];?>
</a></td>
				<td align="center">- <?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayWtPriceWithCurrency'][0][0]->displayWtPriceWithCurrency(array('price'=>$_smarty_tpl->tpl_vars['discount']->value['value_real'],'currency'=>$_smarty_tpl->tpl_vars['currency']->value),$_smarty_tpl);?>
</td>
			</tr>
		<?php } ?>
	</table>
	<?php }?>
	<div style="float:left; margin-top:15px;">
	<?php echo smartyTranslate(array('s'=>'For this customer\'s group, prices are displayed as:'),$_smarty_tpl);?>
 <b><?php if ($_smarty_tpl->tpl_vars['order']->value->getTaxCalculationMethod()==@PS_TAX_EXC){?><?php echo smartyTranslate(array('s'=>'tax excluded'),$_smarty_tpl);?>
<?php }else{ ?><?php echo smartyTranslate(array('s'=>'tax included'),$_smarty_tpl);?>
<?php }?></b>
</div>
	
	<div class="clear" style="height:20px;">&nbsp;</div>


<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0][0]->smartyHook(array('h'=>'displayAdminView'),$_smarty_tpl);?>

<?php if (isset($_smarty_tpl->tpl_vars['name_controller']->value)){?>
	<?php $_smarty_tpl->_capture_stack[0][] = array('hookName', 'hookName', null); ob_start(); ?>display<?php echo ucfirst($_smarty_tpl->tpl_vars['name_controller']->value);?>
View<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>
	<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0][0]->smartyHook(array('h'=>$_smarty_tpl->tpl_vars['hookName']->value),$_smarty_tpl);?>

<?php }elseif(isset($_GET['controller'])){?>
	<?php $_smarty_tpl->_capture_stack[0][] = array('hookName', 'hookName', null); ob_start(); ?>display<?php echo htmlentities(ucfirst($_GET['controller']));?>
View<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>
	<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0][0]->smartyHook(array('h'=>$_smarty_tpl->tpl_vars['hookName']->value),$_smarty_tpl);?>

<?php }?>
<?php }} ?>