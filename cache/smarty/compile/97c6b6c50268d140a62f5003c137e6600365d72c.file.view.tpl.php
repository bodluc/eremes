<?php /* Smarty version Smarty-3.1.8, created on 2012-11-14 11:47:44
         compiled from "/home/bodo/public_html/dev/admin-dev/themes/default/template/controllers/orders/helpers/view/view.tpl" */ ?>
<?php /*%%SmartyHeaderCode:72717159050a376d0ad6e96-47041202%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '97c6b6c50268d140a62f5003c137e6600365d72c' => 
    array (
      0 => '/home/bodo/public_html/dev/admin-dev/themes/default/template/controllers/orders/helpers/view/view.tpl',
      1 => 1347489974,
      2 => 'file',
    ),
    '52bf8c8ed6d7d94b92322c74a1466b6cf1ce08a0' => 
    array (
      0 => '/home/bodo/public_html/dev/admin-dev/themes/default/template/helpers/view/view.tpl',
      1 => 1347489972,
      2 => 'file',
    ),
    '96c6059a3ff51b1ab8d25a4da833fb712eb43c39' => 
    array (
      0 => '/home/bodo/public_html/dev/admin-dev/themes/default/template/controllers/orders/_documents.tpl',
      1 => 1347489974,
      2 => 'file',
    ),
    '2951751efd3af09d13195193c4ded6f143ae9d50' => 
    array (
      0 => '/home/bodo/public_html/dev/admin-dev/themes/default/template/controllers/orders/_shipping.tpl',
      1 => 1347489974,
      2 => 'file',
    ),
    '74ff8c9ac3df9358532b6548a44f227d768440af' => 
    array (
      0 => '/home/bodo/public_html/dev/admin-dev/themes/default/template/controllers/orders/_customized_data.tpl',
      1 => 1347489974,
      2 => 'file',
    ),
    '1e5ddadd001e235fb61b494716066b11681c5fff' => 
    array (
      0 => '/home/bodo/public_html/dev/admin-dev/themes/default/template/controllers/orders/_product_line.tpl',
      1 => 1347489974,
      2 => 'file',
    ),
    '2154672698751eb49dc76c28f6dadc35b7f5110b' => 
    array (
      0 => '/home/bodo/public_html/dev/admin-dev/themes/default/template/controllers/orders/_new_product.tpl',
      1 => 1347489974,
      2 => 'file',
    ),
    '1e6636fb3fb79ea35a1c505cecafa956bb187405' => 
    array (
      0 => '/home/bodo/public_html/dev/admin-dev/themes/default/template/controllers/orders/_discount_form.tpl',
      1 => 1347489974,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '72717159050a376d0ad6e96-47041202',
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
  'unifunc' => 'content_50a376d2ae3c49_25928088',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50a376d2ae3c49_25928088')) {function content_50a376d2ae3c49_25928088($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/home/bodo/public_html/dev/tools/smarty/plugins/modifier.escape.php';
if (!is_callable('smarty_modifier_regex_replace')) include '/home/bodo/public_html/dev/tools/smarty/plugins/modifier.regex_replace.php';
if (!is_callable('smarty_modifier_replace')) include '/home/bodo/public_html/dev/tools/smarty/plugins/modifier.replace.php';
?>

<?php if ($_smarty_tpl->tpl_vars['show_toolbar']->value){?>
	<?php echo $_smarty_tpl->getSubTemplate ("toolbar.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('toolbar_btn'=>$_smarty_tpl->tpl_vars['toolbar_btn']->value,'toolbar_scroll'=>$_smarty_tpl->tpl_vars['toolbar_scroll']->value,'title'=>$_smarty_tpl->tpl_vars['title']->value), 0);?>

<?php }?>

<div class="leadin"></div>


	<script type="text/javascript">
	var admin_order_tab_link = "<?php echo $_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminOrders');?>
";
	var id_order = <?php echo $_smarty_tpl->tpl_vars['order']->value->id;?>
;
	var id_lang = <?php echo $_smarty_tpl->tpl_vars['current_id_lang']->value;?>
;
	var id_currency = <?php echo $_smarty_tpl->tpl_vars['order']->value->id_currency;?>
;
	<?php $_smarty_tpl->tpl_vars['PS_TAX_ADDRESS_TYPE'] = new Smarty_variable(Configuration::get('PS_TAX_ADDRESS_TYPE'), null, 0);?>
	var id_address = <?php echo $_smarty_tpl->tpl_vars['order']->value->{$_smarty_tpl->tpl_vars['PS_TAX_ADDRESS_TYPE']->value};?>
;
	var currency_sign = "<?php echo $_smarty_tpl->tpl_vars['currency']->value->sign;?>
";
	var currency_format = "<?php echo $_smarty_tpl->tpl_vars['currency']->value->format;?>
";
	var currency_blank = "<?php echo $_smarty_tpl->tpl_vars['currency']->value->blank;?>
";
	var priceDisplayPrecision = 2;
	var use_taxes = <?php if ($_smarty_tpl->tpl_vars['order']->value->getTaxCalculationMethod()==@PS_TAX_INC){?>true<?php }else{ ?>false<?php }?>;
	var token = "<?php echo smarty_modifier_escape($_GET['token'], 'htmlall', 'UTF-8');?>
";

	var txt_add_product_stock_issue = "<?php echo smartyTranslate(array('s'=>'You want to add more product than are available in stock, are you sure you want to add this quantity?','js'=>1),$_smarty_tpl);?>
";
	var txt_add_product_new_invoice = "<?php echo smartyTranslate(array('s'=>'Are you sure you want to create a new invoice?','js'=>1),$_smarty_tpl);?>
";
	var txt_add_product_no_product = "<?php echo smartyTranslate(array('s'=>'Error: No product has been selected','js'=>1),$_smarty_tpl);?>
";
	var txt_add_product_no_product_quantity = "<?php echo smartyTranslate(array('s'=>'Error: Quantity of product must be set','js'=>1),$_smarty_tpl);?>
";
	var txt_add_product_no_product_price = "<?php echo smartyTranslate(array('s'=>'Error: Price of product must be set','js'=>1),$_smarty_tpl);?>
";
	var txt_confirm = "<?php echo smartyTranslate(array('s'=>'Are you sure?','js'=>1),$_smarty_tpl);?>
";

	var statesShipped = new Array();
	<?php  $_smarty_tpl->tpl_vars['state'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['state']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['states']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['state']->key => $_smarty_tpl->tpl_vars['state']->value){
$_smarty_tpl->tpl_vars['state']->_loop = true;
?>
		<?php if ((!$_smarty_tpl->tpl_vars['currentState']->value->shipped&&$_smarty_tpl->tpl_vars['state']->value['shipped'])){?>
			statesShipped.push(<?php echo $_smarty_tpl->tpl_vars['state']->value['id_order_state'];?>
);
		<?php }?>
	<?php } ?>
	</script>

	<?php ob_start();?><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0][0]->smartyHook(array('h'=>"displayInvoice",'id_order'=>$_smarty_tpl->tpl_vars['order']->value->id),$_smarty_tpl);?>
<?php $_tmp1=ob_get_clean();?><?php $_smarty_tpl->tpl_vars["hook_invoice"] = new Smarty_variable($_tmp1, null, 0);?>
	<?php if (($_smarty_tpl->tpl_vars['hook_invoice']->value)){?>
	<div style="float: right; margin: -40px 40px 10px 0;"><?php echo $_smarty_tpl->tpl_vars['hook_invoice']->value;?>
</div><br class="clear" />';
	<?php }?>

<div class="bloc-command">
	<div class="button-command">
			<?php if ((count($_smarty_tpl->tpl_vars['invoices_collection']->value))){?>
				<a class="button" href="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminPdf'), 'htmlall', 'UTF-8');?>
&submitAction=generateInvoicePDF&id_order=<?php echo $_smarty_tpl->tpl_vars['order']->value->id;?>
" target="_blank">
					<img src="../img/admin/charged_ok.gif" alt="<?php echo smartyTranslate(array('s'=>'View invoice'),$_smarty_tpl);?>
" /> <?php echo smartyTranslate(array('s'=>'View invoice'),$_smarty_tpl);?>

				</a>
			<?php }else{ ?>
				<img src="../img/admin/charged_ko.gif" alt="<?php echo smartyTranslate(array('s'=>'No invoice'),$_smarty_tpl);?>
" /> <?php echo smartyTranslate(array('s'=>'No invoice'),$_smarty_tpl);?>

			<?php }?>
			 |
			<?php if ((($_smarty_tpl->tpl_vars['currentState']->value&&$_smarty_tpl->tpl_vars['currentState']->value->delivery)||$_smarty_tpl->tpl_vars['order']->value->delivery_number)){?>
				<a class="button"  href="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminPdf'), 'htmlall', 'UTF-8');?>
&submitAction=generateDeliverySlipPDF&id_order=<?php echo $_smarty_tpl->tpl_vars['order']->value->id;?>
" target="_blank">
					<img src="../img/admin/delivery.gif" alt="<?php echo smartyTranslate(array('s'=>'View delivery slip'),$_smarty_tpl);?>
" /> <?php echo smartyTranslate(array('s'=>'View delivery slip'),$_smarty_tpl);?>

				</a>
			<?php }else{ ?>
				<img src="../img/admin/delivery_ko.gif" alt="<?php echo smartyTranslate(array('s'=>'No delivery slip'),$_smarty_tpl);?>
" /> <?php echo smartyTranslate(array('s'=>'No delivery slip'),$_smarty_tpl);?>

			<?php }?>
			 |
			<a class="button" href="javascript:window.print()"><img src="../img/admin/printer.gif" alt="<?php echo smartyTranslate(array('s'=>'Print order'),$_smarty_tpl);?>
" title="<?php echo smartyTranslate(array('s'=>'Print order'),$_smarty_tpl);?>
" /> <?php echo smartyTranslate(array('s'=>'Print order'),$_smarty_tpl);?>
</a>
		</div>
		<div class="metadata-command">
			<dl>
				<dt><?php echo smartyTranslate(array('s'=>'Date:'),$_smarty_tpl);?>
 </dt>
				<dd><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['dateFormat'][0][0]->dateFormat(array('date'=>$_smarty_tpl->tpl_vars['order']->value->date_add,'full'=>true),$_smarty_tpl);?>
</dd>
			|</dl>
			<dl>
				<dt><?php echo smartyTranslate(array('s'=>'Messages:'),$_smarty_tpl);?>
</dt>
				<dd><?php echo sizeof($_smarty_tpl->tpl_vars['messages']->value);?>
</dd>
			|</dl>
			<dl>
				<dt><a href="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminCustomerThreads'), 'htmlall', 'UTF-8');?>
"><?php echo smartyTranslate(array('s'=>'New Customer Messages:'),$_smarty_tpl);?>
</a></dt>
				<dd><a href="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminCustomerThreads'), 'htmlall', 'UTF-8');?>
"><?php echo sizeof($_smarty_tpl->tpl_vars['customer_thread_message']->value);?>
</a></dd>
			|</dl>
			<dl>
				<dt><?php echo smartyTranslate(array('s'=>'Products:'),$_smarty_tpl);?>
</dt>
				<dd id="product_number"><?php echo sizeof($_smarty_tpl->tpl_vars['products']->value);?>
</dd>
			|</dl>
			<dl>
				<dt><?php echo smartyTranslate(array('s'=>'Total:'),$_smarty_tpl);?>
</dt>
				<dd class="total_paid"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['order']->value->total_paid_tax_incl,'currency'=>$_smarty_tpl->tpl_vars['currency']->value->id),$_smarty_tpl);?>
</dd>
			</dl>
		<div class="clear"></div>
	</div>
</div>
	<div class="container-command">
		<!-- Left column -->
		<div style="width: 49%; float:left;">
			<!-- Change status form -->
			<form action="<?php echo $_smarty_tpl->tpl_vars['currentIndex']->value;?>
&viewOrder&token=<?php echo $_GET['token'];?>
" method="post">
				<select id="id_order_state" name="id_order_state">
				<?php  $_smarty_tpl->tpl_vars['state'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['state']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['states']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['state']->key => $_smarty_tpl->tpl_vars['state']->value){
$_smarty_tpl->tpl_vars['state']->_loop = true;
?>
					<?php if ($_smarty_tpl->tpl_vars['state']->value['id_order_state']!=$_smarty_tpl->tpl_vars['currentState']->value->id){?>
					<option value="<?php echo $_smarty_tpl->tpl_vars['state']->value['id_order_state'];?>
"><?php echo stripslashes($_smarty_tpl->tpl_vars['state']->value['name']);?>
</option>
					<?php }?>
				<?php } ?>
				</select>
				<input type="hidden" name="id_order" value="<?php echo $_smarty_tpl->tpl_vars['order']->value->id;?>
" />
				<input type="submit" name="submitState" value="<?php echo smartyTranslate(array('s'=>'Add'),$_smarty_tpl);?>
" class="button" />
			</form>
			<br />

			<!-- History of status -->
			<table cellspacing="0" cellpadding="0" class="table history-status" style="width: 100%;">
				<colgroup>
					<col width="1%">
					<col width="">
					<col width="20%">
					<col width="20%">
				</colgroup>
			<?php  $_smarty_tpl->tpl_vars['row'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['row']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['history']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['row']->key => $_smarty_tpl->tpl_vars['row']->value){
$_smarty_tpl->tpl_vars['row']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['row']->key;
?>
				<?php if (($_smarty_tpl->tpl_vars['key']->value==0)){?>
				<tr>
					<th><img src="../img/os/<?php echo $_smarty_tpl->tpl_vars['row']->value['id_order_state'];?>
.gif" /></th>
					<th><?php echo stripslashes($_smarty_tpl->tpl_vars['row']->value['ostate_name']);?>
</th>
					<th><?php if ($_smarty_tpl->tpl_vars['row']->value['employee_lastname']){?><?php echo stripslashes($_smarty_tpl->tpl_vars['row']->value['employee_firstname']);?>
 <?php echo stripslashes($_smarty_tpl->tpl_vars['row']->value['employee_lastname']);?>
<?php }?></th>
					<th><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['dateFormat'][0][0]->dateFormat(array('date'=>$_smarty_tpl->tpl_vars['row']->value['date_add'],'full'=>true),$_smarty_tpl);?>
</th>
				</tr>
				<?php }else{ ?>
				<tr class="<?php if (($_smarty_tpl->tpl_vars['key']->value%2)){?>alt_row<?php }?>">
					<td><img src="../img/os/<?php echo $_smarty_tpl->tpl_vars['row']->value['id_order_state'];?>
.gif" /></td>
					<td><?php echo stripslashes($_smarty_tpl->tpl_vars['row']->value['ostate_name']);?>
</td>
					<td><?php if ($_smarty_tpl->tpl_vars['row']->value['employee_lastname']){?><?php echo stripslashes($_smarty_tpl->tpl_vars['row']->value['employee_firstname']);?>
 <?php echo stripslashes($_smarty_tpl->tpl_vars['row']->value['employee_lastname']);?>
<?php }else{ ?>&nbsp;<?php }?></td>
					<td><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['dateFormat'][0][0]->dateFormat(array('date'=>$_smarty_tpl->tpl_vars['row']->value['date_add'],'full'=>true),$_smarty_tpl);?>
</td>
				</tr>
				<?php }?>
			<?php } ?>
			</table>

			<?php if ($_smarty_tpl->tpl_vars['customer']->value->id){?>
			<!-- Customer informations -->
			<br />
			<fieldset>
				<legend><img src="../img/admin/tab-customers.gif" /> <?php echo smartyTranslate(array('s'=>'Customer information'),$_smarty_tpl);?>
</legend>
				<span style="font-weight: bold; font-size: 14px;"><a href="?tab=AdminCustomers&id_customer=<?php echo $_smarty_tpl->tpl_vars['customer']->value->id;?>
&viewcustomer&token=<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminCustomers'),$_smarty_tpl);?>
"> <?php echo $_smarty_tpl->tpl_vars['customer']->value->firstname;?>
 <?php echo $_smarty_tpl->tpl_vars['customer']->value->lastname;?>
</a></span> (<?php echo smartyTranslate(array('s'=>'#'),$_smarty_tpl);?>
<?php echo $_smarty_tpl->tpl_vars['customer']->value->id;?>
)<br />
				(<a href="mailto:<?php echo $_smarty_tpl->tpl_vars['customer']->value->email;?>
"><?php echo $_smarty_tpl->tpl_vars['customer']->value->email;?>
</a>)<br /><br />
				<?php if (($_smarty_tpl->tpl_vars['customer']->value->isGuest())){?>
					<?php echo smartyTranslate(array('s'=>'This order has been placed by a guest.'),$_smarty_tpl);?>

					<?php if ((!Customer::customerExists($_smarty_tpl->tpl_vars['customer']->value->email))){?>
					<form method="post" action="index.php?tab=AdminCustomers&id_customer=<?php echo $_smarty_tpl->tpl_vars['customer']->value->id;?>
&token=<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminCustomers'),$_smarty_tpl);?>
">
						<input type="hidden" name="id_lang" value="<?php echo $_smarty_tpl->tpl_vars['order']->value->id_lang;?>
" />
						<p class="center"><input class="button" type="submit" name="submitGuestToCustomer" value="<?php echo smartyTranslate(array('s'=>'Transform guest into customer'),$_smarty_tpl);?>
" /></p>
						<?php echo smartyTranslate(array('s'=>'This feature will generate a random password and send an e-mail to the customer'),$_smarty_tpl);?>

					</form>
					<?php }else{ ?>
						<div><b style="color:red;"><?php echo smartyTranslate(array('s'=>'A registered customer account already exists with this e-mail address'),$_smarty_tpl);?>
</b></div>
					<?php }?>
				<?php }else{ ?>
					<?php echo smartyTranslate(array('s'=>'Account registered:'),$_smarty_tpl);?>
 <b><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['dateFormat'][0][0]->dateFormat(array('date'=>$_smarty_tpl->tpl_vars['customer']->value->date_add,'full'=>true),$_smarty_tpl);?>
</b><br />
					<?php echo smartyTranslate(array('s'=>'Valid orders placed:'),$_smarty_tpl);?>
 <b><?php echo $_smarty_tpl->tpl_vars['customerStats']->value['nb_orders'];?>
</b><br />
					<?php echo smartyTranslate(array('s'=>'Total spent since registration:'),$_smarty_tpl);?>
 <b><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>Tools::ps_round(Tools::convertPrice($_smarty_tpl->tpl_vars['customerStats']->value['total_orders'],$_smarty_tpl->tpl_vars['currency']->value),2),'currency'=>$_smarty_tpl->tpl_vars['currency']->value->id),$_smarty_tpl);?>
</b><br />
			</fieldset>
				<?php }?>
			<?php }?>

			<!-- Sources block -->
			<?php if ((sizeof($_smarty_tpl->tpl_vars['sources']->value))){?>
			<br />
			<fieldset>
				<legend><img src="../img/admin/tab-stats.gif" /> <?php echo smartyTranslate(array('s'=>'Sources'),$_smarty_tpl);?>
</legend>
				<ul <?php if (sizeof($_smarty_tpl->tpl_vars['sources']->value)>3){?>style="height: 200px; overflow-y: scroll;"<?php }?>>
				<?php  $_smarty_tpl->tpl_vars['source'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['source']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['sources']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['source']->key => $_smarty_tpl->tpl_vars['source']->value){
$_smarty_tpl->tpl_vars['source']->_loop = true;
?>
					<li>
						<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['dateFormat'][0][0]->dateFormat(array('date'=>$_smarty_tpl->tpl_vars['source']->value['date_add'],'full'=>true),$_smarty_tpl);?>
<br />
						<b><?php echo smartyTranslate(array('s'=>'From:'),$_smarty_tpl);?>
</b> <a href="<?php echo $_smarty_tpl->tpl_vars['source']->value['http_referer'];?>
"><?php echo smarty_modifier_regex_replace(parse_url($_smarty_tpl->tpl_vars['source']->value['http_referer'],@PHP_URL_HOST),'/^www./','');?>
</a><br />
						<b><?php echo smartyTranslate(array('s'=>'To:'),$_smarty_tpl);?>
</b> <?php echo $_smarty_tpl->tpl_vars['source']->value['request_uri'];?>
<br />
						<?php if ($_smarty_tpl->tpl_vars['source']->value['keywords']){?><b><?php echo smartyTranslate(array('s'=>'Keywords:'),$_smarty_tpl);?>
</b> <?php echo $_smarty_tpl->tpl_vars['source']->value['keywords'];?>
<br /><?php }?><br />
					</li>
				<?php } ?>
				</ul>
			</fieldset>
			<?php }?>

			<!-- Admin order hook -->
			<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0][0]->smartyHook(array('h'=>"displayAdminOrder",'id_order'=>$_smarty_tpl->tpl_vars['order']->value->id),$_smarty_tpl);?>

		</div>
		<!-- END Left column -->

		<!-- Right column -->
		<div style="width: 49%; float:right;">
			<div class="button-command-prev-next">
				<b><?php echo smartyTranslate(array('s'=>'Orders'),$_smarty_tpl);?>
</b> :
				<?php if ($_smarty_tpl->tpl_vars['previousOrder']->value){?><a class="button" href="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminOrders'), 'htmlall', 'UTF-8');?>
&vieworder&id_order=<?php echo $_smarty_tpl->tpl_vars['previousOrder']->value;?>
"><?php echo smartyTranslate(array('s'=>'< Prev'),$_smarty_tpl);?>
</a><?php }?>
				<?php if ($_smarty_tpl->tpl_vars['nextOrder']->value){?><a class="button" href="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminOrders'), 'htmlall', 'UTF-8');?>
&vieworder&id_order=<?php echo $_smarty_tpl->tpl_vars['nextOrder']->value;?>
"><?php echo smartyTranslate(array('s'=>'Next >'),$_smarty_tpl);?>
</a><?php }?>
			</div>
			<div class="clear"></div>
			
			<!-- linked orders block -->
			<?php if (count($_smarty_tpl->tpl_vars['order']->value->getBrother())>0){?>
				<fieldset>
					<legend><img src="../img/admin/tab-orders.gif" /> <?php echo smartyTranslate(array('s'=>'Linked orders'),$_smarty_tpl);?>
</legend>
					<table class="table" width="100%;" cellspacing="0" cellpadding="0">
						<thead>
							<tr>
								<th width="10%">
									<?php echo smartyTranslate(array('s'=>'Order no.'),$_smarty_tpl);?>

								</th>
								<th>
									<?php echo smartyTranslate(array('s'=>'Status'),$_smarty_tpl);?>

								</th>
								<th width="10%">
									<?php echo smartyTranslate(array('s'=>'Amount'),$_smarty_tpl);?>

								</th>
								<th width="5%">
								</th>
							</tr>
						</thead>
						<tbody>
							<?php  $_smarty_tpl->tpl_vars['brother_order'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['brother_order']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['order']->value->getBrother(); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['brother_order']->index=-1;
foreach ($_from as $_smarty_tpl->tpl_vars['brother_order']->key => $_smarty_tpl->tpl_vars['brother_order']->value){
$_smarty_tpl->tpl_vars['brother_order']->_loop = true;
 $_smarty_tpl->tpl_vars['brother_order']->index++;
 $_smarty_tpl->tpl_vars['brother_order']->first = $_smarty_tpl->tpl_vars['brother_order']->index === 0;
?>
								<tr>
									<td>
										<a href="<?php echo $_smarty_tpl->tpl_vars['current_index']->value;?>
&vieworder&id_order=<?php echo $_smarty_tpl->tpl_vars['brother_order']->value->id;?>
&token=<?php echo smarty_modifier_escape($_GET['token'], 'htmlall', 'UTF-8');?>
">#<?php echo sprintf('%06d',$_smarty_tpl->tpl_vars['brother_order']->value->id);?>
</a>
									</td>
									<td>
										<?php echo $_smarty_tpl->tpl_vars['brother_order']->value->getCurrentOrderState()->name[$_smarty_tpl->tpl_vars['current_id_lang']->value];?>

									</td>
									<td>
										<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['brother_order']->value->total_paid_tax_incl,'currency'=>$_smarty_tpl->tpl_vars['currency']->value->id),$_smarty_tpl);?>

									</td>
									<td>
										<a href="<?php echo $_smarty_tpl->tpl_vars['current_index']->value;?>
&vieworder&id_order=<?php echo $_smarty_tpl->tpl_vars['brother_order']->value->id;?>
&token=<?php echo smarty_modifier_escape($_GET['token'], 'htmlall', 'UTF-8');?>
"><img alt="<?php echo smartyTranslate(array('s'=>'See the order'),$_smarty_tpl);?>
" src="../img/admin/details.gif"></a>
									</td>
								</tr>
							<?php } ?>
						</tbody>
					</table>
				</fieldset>
				<br />
			<?php }?>
			
			<!-- Documents block -->
			<fieldset>
				<legend><img src="../img/admin/details.gif" /> <?php echo smartyTranslate(array('s'=>'Documents'),$_smarty_tpl);?>
</legend>

				
				<?php /*  Call merged included template "controllers/orders/_documents.tpl" */
$_tpl_stack[] = $_smarty_tpl;
 $_smarty_tpl = $_smarty_tpl->setupInlineSubTemplate('controllers/orders/_documents.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0, '72717159050a376d0ad6e96-47041202');
content_50a376d18de032_89841595($_smarty_tpl);
$_smarty_tpl = array_pop($_tpl_stack); /*  End of included template "controllers/orders/_documents.tpl" */?>
			</fieldset>
			<br />

			<!-- Payments block -->
			<fieldset>
				<legend><img src="../img/admin/money.gif" /> <?php echo smartyTranslate(array('s'=>'Payment'),$_smarty_tpl);?>
</legend>

				<?php if ((!$_smarty_tpl->tpl_vars['order']->value->valid&&sizeof($_smarty_tpl->tpl_vars['currencies']->value)>1)){?>
				<form method="post" action="<?php echo $_smarty_tpl->tpl_vars['currentIndex']->value;?>
&viewOrder&id_order=<?php echo $_smarty_tpl->tpl_vars['order']->value->id;?>
&token=<?php echo smarty_modifier_escape($_GET['token'], 'htmlall', 'UTF-8');?>
">
					<p class="warn"><?php echo smartyTranslate(array('s'=>'Don\'t forget to update your conversion rate before make this change.'),$_smarty_tpl);?>
</p>
					<label><?php echo smartyTranslate(array('s'=>'Don\'t forget to update your conversion rate before make this change.'),$_smarty_tpl);?>
</label>
					<select name="new_currency">
						<?php  $_smarty_tpl->tpl_vars['currency_change'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['currency_change']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['currencies']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['currency_change']->key => $_smarty_tpl->tpl_vars['currency_change']->value){
$_smarty_tpl->tpl_vars['currency_change']->_loop = true;
?>
							<?php if ($_smarty_tpl->tpl_vars['currency_change']->value['id_currency']!=$_smarty_tpl->tpl_vars['order']->value->id_currency){?>
							<option value="<?php echo $_smarty_tpl->tpl_vars['currency_change']->value['id_currency'];?>
"><?php echo $_smarty_tpl->tpl_vars['currency_change']->value['name'];?>
 - <?php echo $_smarty_tpl->tpl_vars['currency_change']->value['sign'];?>
</option>
							<?php }?>
						<?php } ?>
					</select>
					<input type="submit" class="button" name="submitChangeCurrency" value="<?php echo smartyTranslate(array('s'=>'Change'),$_smarty_tpl);?>
" />
				</form>
				<hr class="clear"/>
				<?php }?>
				
				<?php if (count($_smarty_tpl->tpl_vars['order']->value->getOrderPayments())>0){?>
				<p class="error" style="<?php if (round($_smarty_tpl->tpl_vars['orders_total_paid_tax_incl']->value,2)==round($_smarty_tpl->tpl_vars['total_paid']->value,2)||$_smarty_tpl->tpl_vars['currentState']->value->id==6){?>display: none;<?php }?>">
					<?php echo smartyTranslate(array('s'=>'Warning:'),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['total_paid']->value,'currency'=>$_smarty_tpl->tpl_vars['currency']->value->id),$_smarty_tpl);?>

					<?php echo smartyTranslate(array('s'=>'paid instead of'),$_smarty_tpl);?>
 <span class="total_paid"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['orders_total_paid_tax_incl']->value,'currency'=>$_smarty_tpl->tpl_vars['currency']->value->id),$_smarty_tpl);?>
</span>
					
					<?php  $_smarty_tpl->tpl_vars['brother_order'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['brother_order']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['order']->value->getBrother(); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['brother_order']->index=-1;
foreach ($_from as $_smarty_tpl->tpl_vars['brother_order']->key => $_smarty_tpl->tpl_vars['brother_order']->value){
$_smarty_tpl->tpl_vars['brother_order']->_loop = true;
 $_smarty_tpl->tpl_vars['brother_order']->index++;
 $_smarty_tpl->tpl_vars['brother_order']->first = $_smarty_tpl->tpl_vars['brother_order']->index === 0;
?>
						<?php if ($_smarty_tpl->tpl_vars['brother_order']->first){?>
							<?php if (count($_smarty_tpl->tpl_vars['order']->value->getBrother())==1){?>
								<br /><?php echo smartyTranslate(array('s'=>'This warning also concerns the order '),$_smarty_tpl);?>

							<?php }else{ ?>
								<br /><?php echo smartyTranslate(array('s'=>'This warning also concerns the next orders:'),$_smarty_tpl);?>

							<?php }?>
						<?php }?>
						<a href="<?php echo $_smarty_tpl->tpl_vars['current_index']->value;?>
&vieworder&id_order=<?php echo $_smarty_tpl->tpl_vars['brother_order']->value->id;?>
&token=<?php echo smarty_modifier_escape($_GET['token'], 'htmlall', 'UTF-8');?>
">#<?php echo sprintf('%06d',$_smarty_tpl->tpl_vars['brother_order']->value->id);?>
</a>
					<?php } ?>
				</p>
				<?php }?>

				<form id="formAddPayment" method="post" action="<?php echo $_smarty_tpl->tpl_vars['current_index']->value;?>
&vieworder&id_order=<?php echo $_smarty_tpl->tpl_vars['order']->value->id;?>
&token=<?php echo smarty_modifier_escape($_GET['token'], 'htmlall', 'UTF-8');?>
">
					<table class="table" width="100%" cellspacing="0" cellpadding="0">
						<colgroup>
							<col width="15%">
							<col width="">
							<col width="20%">
							<col width="10%">
							<col width="10%">
							<col width="1%">
						</colgroup>
						<thead>
							<tr>
								<th><?php echo smartyTranslate(array('s'=>'Date'),$_smarty_tpl);?>
</th>
								<th><?php echo smartyTranslate(array('s'=>'Payment method'),$_smarty_tpl);?>
</th>
								<th><?php echo smartyTranslate(array('s'=>'Transaction ID'),$_smarty_tpl);?>
</th>
								<th><?php echo smartyTranslate(array('s'=>'Amount'),$_smarty_tpl);?>
</th>
								<th><?php echo smartyTranslate(array('s'=>'Invoice'),$_smarty_tpl);?>
</th>
								<th>&nbsp;</th>
							</tr>
						</thead>
						<tbody>
							<?php  $_smarty_tpl->tpl_vars['payment'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['payment']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['order']->value->getOrderPaymentCollection(); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['payment']->key => $_smarty_tpl->tpl_vars['payment']->value){
$_smarty_tpl->tpl_vars['payment']->_loop = true;
?>
							<tr>
								<td><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['dateFormat'][0][0]->dateFormat(array('date'=>$_smarty_tpl->tpl_vars['payment']->value->date_add,'full'=>true),$_smarty_tpl);?>
</td>
								<td><?php echo $_smarty_tpl->tpl_vars['payment']->value->payment_method;?>
</td>
								<td><?php echo $_smarty_tpl->tpl_vars['payment']->value->transaction_id;?>
</td>
								<td><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['payment']->value->amount,'currency'=>$_smarty_tpl->tpl_vars['payment']->value->id_currency),$_smarty_tpl);?>
</td>
								<td>
								<?php if (!isset($_smarty_tpl->tpl_vars['invoice'])) $_smarty_tpl->tpl_vars['invoice'] = new Smarty_Variable(null);if ($_smarty_tpl->tpl_vars['invoice']->value = $_smarty_tpl->tpl_vars['payment']->value->getOrderInvoice($_smarty_tpl->tpl_vars['order']->value->id)){?>
									<?php echo $_smarty_tpl->tpl_vars['invoice']->value->getInvoiceNumberFormatted($_smarty_tpl->tpl_vars['current_id_lang']->value);?>

								<?php }else{ ?>
									<?php echo smartyTranslate(array('s'=>'No invoice'),$_smarty_tpl);?>

								<?php }?>
								</td>
								<td class="right">
									<a href="#" class="open_payment_information"><img src="../img/admin/details.gif" title="<?php echo smartyTranslate(array('s'=>'See payment informations'),$_smarty_tpl);?>
" alt="<?php echo smartyTranslate(array('s'=>'See payment informations'),$_smarty_tpl);?>
" /></a>
								</td>
							</tr>
							<tr class="payment_information" style="display: none;">
								<td colspan="6">
									<p>
										<b><?php echo smartyTranslate(array('s'=>'Card Number:'),$_smarty_tpl);?>
</b>&nbsp;
										<?php if ($_smarty_tpl->tpl_vars['payment']->value->card_number){?>
											<?php echo $_smarty_tpl->tpl_vars['payment']->value->card_number;?>

										<?php }else{ ?>
											<i><?php echo smartyTranslate(array('s'=>'Not defined'),$_smarty_tpl);?>
</i>
										<?php }?>
									</p>

									<p>
										<b><?php echo smartyTranslate(array('s'=>'Card Brand:'),$_smarty_tpl);?>
</b>&nbsp;
										<?php if ($_smarty_tpl->tpl_vars['payment']->value->card_brand){?>
											<?php echo $_smarty_tpl->tpl_vars['payment']->value->card_brand;?>

										<?php }else{ ?>
											<i><?php echo smartyTranslate(array('s'=>'Not defined'),$_smarty_tpl);?>
</i>
										<?php }?>
									</p>

									<p>
										<b><?php echo smartyTranslate(array('s'=>'Card Expiration:'),$_smarty_tpl);?>
</b>&nbsp;
										<?php if ($_smarty_tpl->tpl_vars['payment']->value->card_expiration){?>
											<?php echo $_smarty_tpl->tpl_vars['payment']->value->card_expiration;?>

										<?php }else{ ?>
											<i><?php echo smartyTranslate(array('s'=>'Not defined'),$_smarty_tpl);?>
</i>
										<?php }?>
									</p>

									<p>
										<b><?php echo smartyTranslate(array('s'=>'Card Holder:'),$_smarty_tpl);?>
</b>&nbsp;
										<?php if ($_smarty_tpl->tpl_vars['payment']->value->card_holder){?>
											<?php echo $_smarty_tpl->tpl_vars['payment']->value->card_holder;?>

										<?php }else{ ?>
											<i><?php echo smartyTranslate(array('s'=>'Not defined'),$_smarty_tpl);?>
</i>
										<?php }?>
									</p>
								</td>
							</tr>
							<?php }
if (!$_smarty_tpl->tpl_vars['payment']->_loop) {
?>
							<tr>
								<td colspan="6" class="center">
									<h3><?php echo smartyTranslate(array('s'=>'No payment available'),$_smarty_tpl);?>
</h3>
								</td>
							</tr>
							<?php } ?>
							<tr class="current-edit">
								<td><input type="text" name="payment_date" class="datepicker" size="17" value="<?php echo date('Y-m-d H:i:s');?>
" /></td>
								<td>
									<select name="payment_method">
									<?php  $_smarty_tpl->tpl_vars['payment_method'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['payment_method']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['payment_methods']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['payment_method']->key => $_smarty_tpl->tpl_vars['payment_method']->value){
$_smarty_tpl->tpl_vars['payment_method']->_loop = true;
?>
										<option value="<?php echo $_smarty_tpl->tpl_vars['payment_method']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['payment_method']->value;?>
</option>
									<?php } ?>
									</select>
								</td>
								<td>
									<input type="text" name="payment_transaction_id" value="" />
								</td>
								<td>
									<input type="text" name="payment_amount" size="5" value="" />
									<select name="payment_currency">
									<?php  $_smarty_tpl->tpl_vars['current_currency'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['current_currency']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['currencies']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['current_currency']->key => $_smarty_tpl->tpl_vars['current_currency']->value){
$_smarty_tpl->tpl_vars['current_currency']->_loop = true;
?>
										<option value="<?php echo $_smarty_tpl->tpl_vars['current_currency']->value['id_currency'];?>
"<?php if ($_smarty_tpl->tpl_vars['current_currency']->value['id_currency']==$_smarty_tpl->tpl_vars['currency']->value->id){?> selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['current_currency']->value['sign'];?>
</option>
									<?php } ?>
									</select>
								</td>
								<?php if (count($_smarty_tpl->tpl_vars['invoices_collection']->value)>0){?>
								<td>
									<select name="payment_invoice" id="payment_invoice">
									<?php  $_smarty_tpl->tpl_vars['invoice'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['invoice']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['invoices_collection']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['invoice']->key => $_smarty_tpl->tpl_vars['invoice']->value){
$_smarty_tpl->tpl_vars['invoice']->_loop = true;
?>
										<option value="<?php echo $_smarty_tpl->tpl_vars['invoice']->value->id;?>
" selected="selected"><?php echo $_smarty_tpl->tpl_vars['invoice']->value->getInvoiceNumberFormatted($_smarty_tpl->tpl_vars['current_id_lang']->value);?>
</option>
									<?php } ?>
									</select>
								</td>
								<?php }?>
								<td><input class="button" type="submit" name="submitAddPayment" value="Add" /></td>
							</tr>
						</tbody>
					</table>
				</form>
			</fieldset>
			<br />

			<!-- Shipping block -->
			<?php if (!$_smarty_tpl->tpl_vars['order']->value->isVirtual()){?>
				<fieldset>
					<legend><img src="../img/admin/delivery.gif" /> <?php echo smartyTranslate(array('s'=>'Shipping'),$_smarty_tpl);?>
</legend>

					<div class="clear" style="float: left; margin-right: 10px;">
						<span><?php echo smartyTranslate(array('s'=>'Recycled packaging:'),$_smarty_tpl);?>
</span>
						<?php if ($_smarty_tpl->tpl_vars['order']->value->recyclable){?>
						<img src="../img/admin/enabled.gif" />
						<?php }else{ ?>
						<img src="../img/admin/disabled.gif" />
						<?php }?>
					</div>
					<div style="float: left;">
						<span><?php echo smartyTranslate(array('s'=>'Gift-wrapping:'),$_smarty_tpl);?>
</span>
						<?php if ($_smarty_tpl->tpl_vars['order']->value->gift){?>
						<img src="../img/admin/enabled.gif" />
						</div>
						<div style="clear: left; margin: 0px 42px 0px 42px; padding-top: 2px;">
							<?php if ($_smarty_tpl->tpl_vars['order']->value->gift_message){?>
							<div style="border: 1px dashed #999; padding: 5px; margin-top: 8px;"><b><?php echo smartyTranslate(array('s'=>'Message:'),$_smarty_tpl);?>
</b><br /><?php echo nl2br($_smarty_tpl->tpl_vars['order']->value->gift_message);?>
</div>
							<?php }?>
						<?php }else{ ?>
						<img src="../img/admin/disabled.gif" />
						<?php }?>
					</div>
					<div class="clear" style="margin-bottom: 10px;"></div>

					<?php /*  Call merged included template "controllers/orders/_shipping.tpl" */
$_tpl_stack[] = $_smarty_tpl;
 $_smarty_tpl = $_smarty_tpl->setupInlineSubTemplate('controllers/orders/_shipping.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0, '72717159050a376d0ad6e96-47041202');
content_50a376d1dcfb65_13316946($_smarty_tpl);
$_smarty_tpl = array_pop($_tpl_stack); /*  End of included template "controllers/orders/_shipping.tpl" */?>

					<?php if ($_smarty_tpl->tpl_vars['carrierModuleCall']->value){?>
						<?php echo $_smarty_tpl->tpl_vars['carrierModuleCall']->value;?>

					<?php }?>
				</fieldset>
				<br />

				<!-- Return block -->
				<fieldset>
					<legend><img src="../img/admin/delivery.gif" /> <?php echo smartyTranslate(array('s'=>'Merchandise returns'),$_smarty_tpl);?>
</legend>

					<?php if (count($_smarty_tpl->tpl_vars['order']->value->getReturn())>0){?>
					<table class="table" width="100%" cellspacing="0" cellpadding="0">
						<thead>
							<tr>
								<th style="width:30%">Date</th>
								<th>Type</th>
								<th style="width:20%">Carrier</th>
								<th style="width:30%">Tracking number</th>
							</tr>
						</thead>
						<tbody>
							<?php  $_smarty_tpl->tpl_vars['line'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['line']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['order']->value->getReturn(); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['line']->key => $_smarty_tpl->tpl_vars['line']->value){
$_smarty_tpl->tpl_vars['line']->_loop = true;
?>
							<tr>
								<td><?php echo $_smarty_tpl->tpl_vars['line']->value['date_add'];?>
</td>
								<td><?php echo $_smarty_tpl->tpl_vars['line']->value['type'];?>
</td>
								<td><?php echo $_smarty_tpl->tpl_vars['line']->value['state_name'];?>
</td>
								<td>
									<span id="shipping_number_show"><?php if (isset($_smarty_tpl->tpl_vars['line']->value['url'])&&isset($_smarty_tpl->tpl_vars['line']->value['tracking_number'])){?><a href="<?php echo smarty_modifier_replace($_smarty_tpl->tpl_vars['line']->value['url'],'@',$_smarty_tpl->tpl_vars['line']->value['tracking_number']);?>
"><?php echo $_smarty_tpl->tpl_vars['line']->value['tracking_number'];?>
</a><?php }elseif(isset($_smarty_tpl->tpl_vars['line']->value['tracking_number'])){?><?php echo $_smarty_tpl->tpl_vars['line']->value['tracking_number'];?>
<?php }?></span>
									<?php if ($_smarty_tpl->tpl_vars['line']->value['can_edit']){?>
									<form style="display: inline;" method="post" action="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminOrders'), 'htmlall', 'UTF-8');?>
&vieworder&id_order=<?php echo $_smarty_tpl->tpl_vars['order']->value->id;?>
&id_order_invoice=<?php if ($_smarty_tpl->tpl_vars['line']->value['id_order_invoice']){?><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['line']->value['id_order_invoice'], 'htmlall', 'UTF-8');?>
<?php }else{ ?>0<?php }?>&id_carrier=<?php if ($_smarty_tpl->tpl_vars['line']->value['id_carrier']){?><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['line']->value['id_carrier'], 'htmlall', 'UTF-8');?>
<?php }else{ ?>0<?php }?>">
										<span class="shipping_number_edit" style="display:none;">
											<input type="text" name="tracking_number" value="<?php echo htmlentities($_smarty_tpl->tpl_vars['line']->value['tracking_number']);?>
" />
											<input type="submit" class="button" name="submitShippingNumber" value="<?php echo smartyTranslate(array('s'=>'Update'),$_smarty_tpl);?>
" />
										</span>
										<a href="#" class="edit_shipping_number_link"><img src="../img/admin/edit.gif" alt="<?php echo smartyTranslate(array('s'=>'Edit'),$_smarty_tpl);?>
" /></a>
										<a href="#" class="cancel_shipping_number_link" style="display: none;"><img src="../img/admin/disabled.gif" alt="<?php echo smartyTranslate(array('s'=>'Cancel'),$_smarty_tpl);?>
" /></a>
									</form>
									<?php }?>
								</td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
					<?php }else{ ?>
					<?php echo smartyTranslate(array('s'=>'No merchandise returns yet.'),$_smarty_tpl);?>

					<?php }?>

					<?php if ($_smarty_tpl->tpl_vars['carrierModuleCall']->value){?>
						<?php echo $_smarty_tpl->tpl_vars['carrierModuleCall']->value;?>

					<?php }?>
				</fieldset>
			<?php }?>
		</div>
		<!-- END Right column -->
		<div class="clear" style="margin-bottom: 10px;"></div>
	</div>

	<div class="container-command container-command-top-spacing">
		<!-- Addresses -->
		<?php if (!$_smarty_tpl->tpl_vars['order']->value->isVirtual()){?>
			<div style="width: 49%; float:left;">
				<!-- Shipping address -->
				<fieldset>
					<legend><img src="../img/admin/delivery.gif" alt="<?php echo smartyTranslate(array('s'=>'Shipping address'),$_smarty_tpl);?>
" /><?php echo smartyTranslate(array('s'=>'Shipping address'),$_smarty_tpl);?>
</legend>

					<?php if ($_smarty_tpl->tpl_vars['can_edit']->value){?>
					<form method="post" action="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminOrders'), 'htmlall', 'UTF-8');?>
&vieworder&id_order=<?php echo $_smarty_tpl->tpl_vars['order']->value->id;?>
">
						<div style="margin-bottom:5px;">
							<p>
								<select name="id_address">
									<?php  $_smarty_tpl->tpl_vars['address'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['address']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['customer_addresses']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['address']->key => $_smarty_tpl->tpl_vars['address']->value){
$_smarty_tpl->tpl_vars['address']->_loop = true;
?>
									<option value="<?php echo $_smarty_tpl->tpl_vars['address']->value['id_address'];?>
"<?php if ($_smarty_tpl->tpl_vars['address']->value['id_address']==$_smarty_tpl->tpl_vars['order']->value->id_address_delivery){?> selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['address']->value['alias'];?>
 - <?php echo $_smarty_tpl->tpl_vars['address']->value['address1'];?>
 <?php echo $_smarty_tpl->tpl_vars['address']->value['postcode'];?>
 <?php echo $_smarty_tpl->tpl_vars['address']->value['city'];?>
<?php if (!empty($_smarty_tpl->tpl_vars['address']->value['state'])){?> <?php echo $_smarty_tpl->tpl_vars['address']->value['state'];?>
<?php }?>, <?php echo $_smarty_tpl->tpl_vars['address']->value['country'];?>
</option>
									<?php } ?>
								</select>
								<input class="button" type="submit" name="submitAddressShipping" value="<?php echo smartyTranslate(array('s'=>'Change'),$_smarty_tpl);?>
" />
							</p>
						</div>
					</form>
					<?php }?>

					<div style="float: right">
						<a href="?tab=AdminAddresses&id_address=<?php echo $_smarty_tpl->tpl_vars['addresses']->value['delivery']->id;?>
&addaddress&realedit=1&id_order=<?php echo $_smarty_tpl->tpl_vars['order']->value->id;?>
<?php if (($_smarty_tpl->tpl_vars['addresses']->value['delivery']->id==$_smarty_tpl->tpl_vars['addresses']->value['invoice']->id)){?>&address_type=1<?php }?>&token=<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminAddresses'),$_smarty_tpl);?>
&back=<?php echo urlencode($_SERVER['REQUEST_URI']);?>
"><img src="../img/admin/edit.gif" /></a>
						<a href="http://maps.google.com/maps?f=q&hl=<?php echo $_smarty_tpl->tpl_vars['iso_code_lang']->value;?>
&geocode=&q=<?php echo $_smarty_tpl->tpl_vars['addresses']->value['delivery']->address1;?>
 <?php echo $_smarty_tpl->tpl_vars['addresses']->value['delivery']->postcode;?>
 <?php echo $_smarty_tpl->tpl_vars['addresses']->value['delivery']->city;?>
 <?php if (($_smarty_tpl->tpl_vars['addresses']->value['delivery']->id_state)){?> <?php echo $_smarty_tpl->tpl_vars['addresses']->value['deliveryState']->name;?>
<?php }?>" target="_blank"><img src="../img/admin/google.gif" alt="" class="middle" /></a>
					</div>

					<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayAddressDetail'][0][0]->generateAddressSmarty(array('address'=>$_smarty_tpl->tpl_vars['addresses']->value['delivery'],'newLine'=>'<br />'),$_smarty_tpl);?>

					<?php if ($_smarty_tpl->tpl_vars['addresses']->value['delivery']->other){?><hr /><?php echo $_smarty_tpl->tpl_vars['addresses']->value['delivery']->other;?>
<br /><?php }?>
				</fieldset>
			</div>
		<?php }?>
		<div style="width: 49%; float:right;">
			<!-- Invoice address -->
			<fieldset>
				<legend><img src="../img/admin/invoice.gif" alt="<?php echo smartyTranslate(array('s'=>'Invoice address'),$_smarty_tpl);?>
" /><?php echo smartyTranslate(array('s'=>'Invoice address'),$_smarty_tpl);?>
</legend>

				<?php if ($_smarty_tpl->tpl_vars['can_edit']->value){?>
				<form method="post" action="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminOrders'), 'htmlall', 'UTF-8');?>
&vieworder&id_order=<?php echo $_smarty_tpl->tpl_vars['order']->value->id;?>
">
					<div style="margin-bottom:5px;">
						<p>
							<select name="id_address">
								<?php  $_smarty_tpl->tpl_vars['address'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['address']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['customer_addresses']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['address']->key => $_smarty_tpl->tpl_vars['address']->value){
$_smarty_tpl->tpl_vars['address']->_loop = true;
?>
								<option value="<?php echo $_smarty_tpl->tpl_vars['address']->value['id_address'];?>
"<?php if ($_smarty_tpl->tpl_vars['address']->value['id_address']==$_smarty_tpl->tpl_vars['order']->value->id_address_invoice){?> selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['address']->value['alias'];?>
 - <?php echo $_smarty_tpl->tpl_vars['address']->value['address1'];?>
 <?php echo $_smarty_tpl->tpl_vars['address']->value['postcode'];?>
 <?php echo $_smarty_tpl->tpl_vars['address']->value['city'];?>
<?php if (!empty($_smarty_tpl->tpl_vars['address']->value['state'])){?> <?php echo $_smarty_tpl->tpl_vars['address']->value['state'];?>
<?php }?>, <?php echo $_smarty_tpl->tpl_vars['address']->value['country'];?>
</option>
								<?php } ?>
							</select>
							<input class="button" type="submit" name="submitAddressInvoice" value="<?php echo smartyTranslate(array('s'=>'Change'),$_smarty_tpl);?>
" />
						</p>
					</div>
				</form>
				<?php }?>

				<div style="float: right">
					<a href="?tab=AdminAddresses&id_address=<?php echo $_smarty_tpl->tpl_vars['addresses']->value['invoice']->id;?>
&addaddress&realedit=1&id_order=<?php echo $_smarty_tpl->tpl_vars['order']->value->id;?>
<?php if (($_smarty_tpl->tpl_vars['addresses']->value['delivery']->id==$_smarty_tpl->tpl_vars['addresses']->value['invoice']->id)){?>&address_type=2<?php }?>&back=<?php echo urlencode($_SERVER['REQUEST_URI']);?>
&token=<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminAddresses'),$_smarty_tpl);?>
"><img src="../img/admin/edit.gif" /></a>
				</div>

				<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayAddressDetail'][0][0]->generateAddressSmarty(array('address'=>$_smarty_tpl->tpl_vars['addresses']->value['invoice'],'newLine'=>'<br />'),$_smarty_tpl);?>

				<?php if ($_smarty_tpl->tpl_vars['addresses']->value['invoice']->other){?><hr /><?php echo $_smarty_tpl->tpl_vars['addresses']->value['invoice']->other;?>
<br /><?php }?>
			</fieldset>
		</div>
		<div class="clear" style="margin-bottom: 10px;"></div>
	</div>

	<form style="width: 98%" class="container-command-top-spacing" action="<?php echo $_smarty_tpl->tpl_vars['current_index']->value;?>
&vieworder&token=<?php echo $_GET['token'];?>
&id_order=<?php echo $_smarty_tpl->tpl_vars['order']->value->id;?>
" method="post" onsubmit="return orderDeleteProduct('<?php echo smartyTranslate(array('s'=>'Cannot return this product'),$_smarty_tpl);?>
', '<?php echo smartyTranslate(array('s'=>'Quantity to cancel is greater than quantity available'),$_smarty_tpl);?>
');">
		<input type="hidden" name="id_order" value="<?php echo $_smarty_tpl->tpl_vars['order']->value->id;?>
" />
		<fieldset style="width: 100%; ">
			<div style="display: none">
				<input type="hidden" value="<?php echo implode($_smarty_tpl->tpl_vars['order']->value->getWarehouseList());?>
" id="warehouse_list" />
			</div>
			<legend><img src="../img/admin/cart.gif" alt="<?php echo smartyTranslate(array('s'=>'Products'),$_smarty_tpl);?>
" /><?php echo smartyTranslate(array('s'=>'Products'),$_smarty_tpl);?>
</legend>
			<div style="float:left;width: 100%;">
				<?php if ($_smarty_tpl->tpl_vars['can_edit']->value){?>
				<?php if (!$_smarty_tpl->tpl_vars['order']->value->hasBeenDelivered()){?><div style="float: left;"><a href="#" class="add_product button"><img src="../img/admin/add.gif" alt="<?php echo smartyTranslate(array('s'=>'Add a product'),$_smarty_tpl);?>
" /> <?php echo smartyTranslate(array('s'=>'Add a product'),$_smarty_tpl);?>
</a></div><?php }?>
				<div style="float: right; margin-right: 10px" id="refundForm">
				<!--
					<a href="#" class="standard_refund"><img src="../img/admin/add.gif" alt="<?php echo smartyTranslate(array('s'=>'Process a standard refund'),$_smarty_tpl);?>
" /> <?php echo smartyTranslate(array('s'=>'Process a standard refund'),$_smarty_tpl);?>
</a>
					<a href="#" class="partial_refund"><img src="../img/admin/add.gif" alt="<?php echo smartyTranslate(array('s'=>'Process a partial refund'),$_smarty_tpl);?>
" /> <?php echo smartyTranslate(array('s'=>'Process a partial refund'),$_smarty_tpl);?>
</a>
				-->
				</div>
				<br clear="left" /><br />
				<?php }?>
				<table style="width: 100%;" cellspacing="0" cellpadding="0" class="table" id="orderProducts">
					<tr>
						<th height="39" align="center" style="width: 7%">&nbsp;</th>
						<th><?php echo smartyTranslate(array('s'=>'Product'),$_smarty_tpl);?>
</th>
						<th style="width: 15%; text-align: center"><?php echo smartyTranslate(array('s'=>'Unit Price'),$_smarty_tpl);?>
 <sup>*</sup></th>
						<th style="width: 4%; text-align: center"><?php echo smartyTranslate(array('s'=>'Qty'),$_smarty_tpl);?>
</th>
						<?php if (($_smarty_tpl->tpl_vars['order']->value->hasBeenPaid())){?><th style="width: 3%; text-align: center"><?php echo smartyTranslate(array('s'=>'Refunded'),$_smarty_tpl);?>
</th><?php }?>
						<?php if (($_smarty_tpl->tpl_vars['order']->value->hasBeenDelivered()||$_smarty_tpl->tpl_vars['order']->value->hasProductReturned())){?><th style="width: 3%; text-align: center"><?php echo smartyTranslate(array('s'=>'Returned'),$_smarty_tpl);?>
</th><?php }?>
						<th style="width: 10%; text-align: center"><?php echo smartyTranslate(array('s'=>'Available quantity'),$_smarty_tpl);?>
</th>
						<th style="width: 10%; text-align: center"><?php echo smartyTranslate(array('s'=>'Total'),$_smarty_tpl);?>
 <sup>*</sup></th>
						<th colspan="2" style="display: none;" class="add_product_fields">&nbsp;</th>
						<th colspan="2" style="display: none;" class="edit_product_fields">&nbsp;</th>
						<th colspan="2" style="display: none;" class="standard_refund_fields"><img src="../img/admin/delete.gif" alt="<?php echo smartyTranslate(array('s'=>'Products'),$_smarty_tpl);?>
" />
							<?php if (($_smarty_tpl->tpl_vars['order']->value->hasBeenDelivered()||$_smarty_tpl->tpl_vars['order']->value->hasBeenShipped())){?>
								<?php echo smartyTranslate(array('s'=>'Return'),$_smarty_tpl);?>

							<?php }elseif(($_smarty_tpl->tpl_vars['order']->value->hasBeenPaid())){?>
								<?php echo smartyTranslate(array('s'=>'Refund'),$_smarty_tpl);?>

							<?php }else{ ?>
								<?php echo smartyTranslate(array('s'=>'Cancel'),$_smarty_tpl);?>

							<?php }?>
						</th>
						<th style="width: 12%;text-align:right;display:none" class="partial_refund_fields">
							<?php echo smartyTranslate(array('s'=>'Partial refund'),$_smarty_tpl);?>

						</th>
						<?php if (!$_smarty_tpl->tpl_vars['order']->value->hasBeenDelivered()){?>
						<th style="width: 8%;text-align:center;">
							<?php echo smartyTranslate(array('s'=>'Action'),$_smarty_tpl);?>

						</th>
						<?php }?>
					</tr>

					<?php  $_smarty_tpl->tpl_vars['product'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['product']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['products']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['product']->key => $_smarty_tpl->tpl_vars['product']->value){
$_smarty_tpl->tpl_vars['product']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['product']->key;
?>
						
						<?php /*  Call merged included template "controllers/orders/_customized_data.tpl" */
$_tpl_stack[] = $_smarty_tpl;
 $_smarty_tpl = $_smarty_tpl->setupInlineSubTemplate('controllers/orders/_customized_data.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0, '72717159050a376d0ad6e96-47041202');
content_50a376d21d4385_88666617($_smarty_tpl);
$_smarty_tpl = array_pop($_tpl_stack); /*  End of included template "controllers/orders/_customized_data.tpl" */?>

						
						<?php /*  Call merged included template "controllers/orders/_product_line.tpl" */
$_tpl_stack[] = $_smarty_tpl;
 $_smarty_tpl = $_smarty_tpl->setupInlineSubTemplate('controllers/orders/_product_line.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0, '72717159050a376d0ad6e96-47041202');
content_50a376d23e8743_15481212($_smarty_tpl);
$_smarty_tpl = array_pop($_tpl_stack); /*  End of included template "controllers/orders/_product_line.tpl" */?>
					<?php } ?>
					<?php if ($_smarty_tpl->tpl_vars['can_edit']->value){?>
						<?php /*  Call merged included template "controllers/orders/_new_product.tpl" */
$_tpl_stack[] = $_smarty_tpl;
 $_smarty_tpl = $_smarty_tpl->setupInlineSubTemplate('controllers/orders/_new_product.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0, '72717159050a376d0ad6e96-47041202');
content_50a376d26c5019_76292654($_smarty_tpl);
$_smarty_tpl = array_pop($_tpl_stack); /*  End of included template "controllers/orders/_new_product.tpl" */?>
					<?php }?>
				</table>

				<div style="float:left; width:280px; margin-top:15px;">
					<sup>*</sup> <?php echo smartyTranslate(array('s'=>'For this customer group, prices are displayed as:'),$_smarty_tpl);?>

					<?php if (($_smarty_tpl->tpl_vars['order']->value->getTaxCalculationMethod()==@PS_TAX_EXC)){?>
						<?php echo smartyTranslate(array('s'=>'tax excluded.'),$_smarty_tpl);?>

					<?php }else{ ?>
						<?php echo smartyTranslate(array('s'=>'tax included.'),$_smarty_tpl);?>

					<?php }?>

					<?php if (!Configuration::get('PS_ORDER_RETURN')){?>
						<br /><br /><?php echo smartyTranslate(array('s'=>'Merchandise returns are disabled'),$_smarty_tpl);?>

					<?php }?>
				</div>
				<div style="float:right; margin-top: 20px;">
					<table class="table" width="450px;" style="border-radius:0px;"cellspacing="0" cellpadding="0">
						<tr id="total_products">
							<td width="150px;"><b><?php echo smartyTranslate(array('s'=>'Products'),$_smarty_tpl);?>
</b></td>
							<td class="amount" align="right"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['order']->value->total_products_wt,'currency'=>$_smarty_tpl->tpl_vars['currency']->value->id),$_smarty_tpl);?>
</td>
							<td class="partial_refund_fields current-edit" style="display:none;">&nbsp;</td>
						</tr>
						<tr id="total_discounts" <?php if ($_smarty_tpl->tpl_vars['order']->value->total_discounts_tax_incl==0){?>style="display: none;"<?php }?>>
							<td><b><?php echo smartyTranslate(array('s'=>'Discounts'),$_smarty_tpl);?>
</b></td>
							<td class="amount" align="right">-<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['order']->value->total_discounts_tax_incl,'currency'=>$_smarty_tpl->tpl_vars['currency']->value->id),$_smarty_tpl);?>
</td>
							<td class="partial_refund_fields current-edit" style="display:none;">&nbsp;</td>
						</tr>
						<tr id="total_wrapping" <?php if ($_smarty_tpl->tpl_vars['order']->value->total_wrapping_tax_incl==0){?>style="display: none;"<?php }?>>
							<td><b><?php echo smartyTranslate(array('s'=>'Wrapping'),$_smarty_tpl);?>
</b></td>
							<td class="amount" align="right"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['order']->value->total_wrapping_tax_incl,'currency'=>$_smarty_tpl->tpl_vars['currency']->value->id),$_smarty_tpl);?>
</td>
							<td class="partial_refund_fields current-edit" style="display:none;">&nbsp;</td>
						</tr>
						<tr id="total_shipping">
							<td><b><?php echo smartyTranslate(array('s'=>'Shipping'),$_smarty_tpl);?>
</b></td>
							<td class="amount" align="right"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['order']->value->total_shipping_tax_incl,'currency'=>$_smarty_tpl->tpl_vars['currency']->value->id),$_smarty_tpl);?>
</td>
							<td class="partial_refund_fields current-edit" style="display:none;"><input type="text" size="3" name="partialRefundShippingCost" value="0" /> &euro;</td>
						</tr>
						<tr style="font-size: 20px" id="total_order">
							<td style="font-size: 20px"><?php echo smartyTranslate(array('s'=>'Total'),$_smarty_tpl);?>
</td>
							<td class="amount" style="font-size: 20px" align="right">
								<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['order']->value->total_paid_tax_incl,'currency'=>$_smarty_tpl->tpl_vars['currency']->value->id),$_smarty_tpl);?>

							</td>
							<td class="partial_refund_fields current-edit" style="display:none;">&nbsp;</td>
						</tr>
					</table>
				</div>
				<div class="clear"></div>

				<?php if ((sizeof($_smarty_tpl->tpl_vars['discounts']->value)||$_smarty_tpl->tpl_vars['can_edit']->value)){?>
				<div style="float:right; width:450px; margin-top:15px;">
					<table cellspacing="0" cellpadding="0" class="table" style="width:100%;">
						<tr>
							<th><img src="../img/admin/coupon.gif" alt="<?php echo smartyTranslate(array('s'=>'Discounts'),$_smarty_tpl);?>
" /><?php echo smartyTranslate(array('s'=>'Discount name'),$_smarty_tpl);?>
</th>
							<th align="center" style="width: 100px"><?php echo smartyTranslate(array('s'=>'Value'),$_smarty_tpl);?>
</th>
							<?php if ($_smarty_tpl->tpl_vars['can_edit']->value){?><th align="center" style="width: 30px"><?php echo smartyTranslate(array('s'=>'Action'),$_smarty_tpl);?>
</th><?php }?>
						</tr>
						<?php  $_smarty_tpl->tpl_vars['discount'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['discount']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['discounts']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['discount']->key => $_smarty_tpl->tpl_vars['discount']->value){
$_smarty_tpl->tpl_vars['discount']->_loop = true;
?>
						<tr>
							<td><?php echo $_smarty_tpl->tpl_vars['discount']->value['name'];?>
</td>
							<td align="center">
							<?php if ($_smarty_tpl->tpl_vars['discount']->value['value']!=0.00){?>
								-
							<?php }?>
							<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['discount']->value['value'],'currency'=>$_smarty_tpl->tpl_vars['currency']->value->id),$_smarty_tpl);?>

							</td>
							<?php if ($_smarty_tpl->tpl_vars['can_edit']->value){?>
							<td class="center">
								<a href="<?php echo $_smarty_tpl->tpl_vars['current_index']->value;?>
&submitDeleteVoucher&id_order_cart_rule=<?php echo $_smarty_tpl->tpl_vars['discount']->value['id_order_cart_rule'];?>
&id_order=<?php echo $_smarty_tpl->tpl_vars['order']->value->id;?>
&token=<?php echo smarty_modifier_escape($_GET['token'], 'htmlall', 'UTF-8');?>
"><img src="../img/admin/delete.gif" alt="<?php echo smartyTranslate(array('s'=>'Delete voucher'),$_smarty_tpl);?>
" /></a>
							</td>
							<?php }?>
						</tr>
						<?php } ?>
					<?php if ($_smarty_tpl->tpl_vars['can_edit']->value){?>
						<tr>
							<td colspan="3" class="center">
								<a class="button" href="#" id="add_voucher"><img src="../img/admin/add.gif" alt="<?php echo smartyTranslate(array('s'=>'Add'),$_smarty_tpl);?>
" /> <?php echo smartyTranslate(array('s'=>'Add a new discount'),$_smarty_tpl);?>
</a>
							</td>
						</tr>
						<tr style="display: none" >
							<td colspan="3" class="current-edit" id="voucher_form">
								<?php /*  Call merged included template "controllers/orders/_discount_form.tpl" */
$_tpl_stack[] = $_smarty_tpl;
 $_smarty_tpl = $_smarty_tpl->setupInlineSubTemplate('controllers/orders/_discount_form.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0, '72717159050a376d0ad6e96-47041202');
content_50a376d2889392_53206881($_smarty_tpl);
$_smarty_tpl = array_pop($_tpl_stack); /*  End of included template "controllers/orders/_discount_form.tpl" */?>
							</td>
						</tr>
					<?php }?>
					</table>
				</div>
				<?php }?>
			</div>

			<div style="clear:both; height:15px;">&nbsp;</div>
			<div style="float: right; width: 160px; display: none;" class="standard_refund_fields">
				<?php if (($_smarty_tpl->tpl_vars['order']->value->hasBeenDelivered()&&Configuration::get('PS_ORDER_RETURN'))){?>
					<input type="checkbox" id="reinjectQuantities" name="reinjectQuantities" class="button" />&nbsp;<label for="reinjectQuantities" style="float:none; font-weight:normal;"><?php echo smartyTranslate(array('s'=>'Re-stock products'),$_smarty_tpl);?>
</label><br />
				<?php }?>
				<?php if (((!$_smarty_tpl->tpl_vars['order']->value->hasBeenDelivered()&&$_smarty_tpl->tpl_vars['order']->value->hasBeenPaid())||($_smarty_tpl->tpl_vars['order']->value->hasBeenDelivered()&&Configuration::get('PS_ORDER_RETURN')))){?>
					<input type="checkbox" id="generateCreditSlip" name="generateCreditSlip" class="button" onclick="toggleShippingCost(this)" />&nbsp;<label for="generateCreditSlip" style="float:none; font-weight:normal;"><?php echo smartyTranslate(array('s'=>'Generate a credit slip'),$_smarty_tpl);?>
</label><br />
					<input type="checkbox" id="generateDiscount" name="generateDiscount" class="button" onclick="toggleShippingCost(this)" />&nbsp;<label for="generateDiscount" style="float:none; font-weight:normal;"><?php echo smartyTranslate(array('s'=>'Generate a voucher'),$_smarty_tpl);?>
</label><br />
					<span id="spanShippingBack" style="display:none;"><input type="checkbox" id="shippingBack" name="shippingBack" class="button" />&nbsp;<label for="shippingBack" style="float:none; font-weight:normal;"><?php echo smartyTranslate(array('s'=>'Repay shipping costs'),$_smarty_tpl);?>
</label><br /></span>
				<?php }?>
				<?php if ((!$_smarty_tpl->tpl_vars['order']->value->hasBeenDelivered()||($_smarty_tpl->tpl_vars['order']->value->hasBeenDelivered()&&Configuration::get('PS_ORDER_RETURN')))){?>
					<div style="text-align:center; margin-top:5px;">
						<input type="submit" name="cancelProduct" value="<?php if ($_smarty_tpl->tpl_vars['order']->value->hasBeenDelivered()){?><?php echo smartyTranslate(array('s'=>'Return products'),$_smarty_tpl);?>
<?php }elseif($_smarty_tpl->tpl_vars['order']->value->hasBeenPaid()){?><?php echo smartyTranslate(array('s'=>'Refund products'),$_smarty_tpl);?>
<?php }else{ ?><?php echo smartyTranslate(array('s'=>'Cancel products'),$_smarty_tpl);?>
<?php }?>" class="button" style="margin-top:8px;" />
					</div>
				<?php }?>
			</div>
			<div style="float: right; width: 160px; display:none;" class="partial_refund_fields">
				<div style="text-align:center; margin-top:5px;">
					<input type="checkbox" id="generateDiscountRefund" name="generateDiscountRefund" class="button" onclick="toggleShippingCost(this)" />&nbsp;<label for="generateDiscount" style="float:none; font-weight:normal;"><?php echo smartyTranslate(array('s'=>'Generate a voucher'),$_smarty_tpl);?>
</label><br />
					<input type="submit" name="partialRefund" value="<?php echo smartyTranslate(array('s'=>'Partial refund'),$_smarty_tpl);?>
" class="button" style="margin-top:8px;" />
				</div>
			</div>
		</fieldset>
	</form>
	<div class="clear" style="height:20px;">&nbsp;</div>

	<div style="float: left">
		<form action="<?php echo $_SERVER['REQUEST_URI'];?>
&token=<?php echo $_GET['token'];?>
" method="post" onsubmit="if (getE('visibility').checked == true) return confirm('<?php echo smartyTranslate(array('s'=>'Do you want to send this message to the customer?'),$_smarty_tpl);?>
');">
		<fieldset style="width: 400px;">
			<legend style="cursor: pointer;" onclick="$('#message').slideToggle();$('#message_m').slideToggle();return false"><img src="../img/admin/email_edit.gif" /> <?php echo smartyTranslate(array('s'=>'New message'),$_smarty_tpl);?>
</legend>
			<div id="message_m" style="display: <?php if (Tools::getValue('message')){?>none<?php }else{ ?>block<?php }?>; overflow: auto; width: 400px;">
				<a href="#" onclick="$('#message').slideToggle();$('#message_m').slideToggle();return false"><b><?php echo smartyTranslate(array('s'=>'Click here'),$_smarty_tpl);?>
</b> <?php echo smartyTranslate(array('s'=>'to add a comment or send a message to the customer'),$_smarty_tpl);?>
</a>
			</div>
			<a href="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminCustomerThreads'), 'htmlall', 'UTF-8');?>
"><b><?php echo smartyTranslate(array('s'=>'Click here'),$_smarty_tpl);?>
</b> <?php echo smartyTranslate(array('s'=>'to see all messages'),$_smarty_tpl);?>
</a><br>
			<div id="message" style="display: <?php if (Tools::getValue('message')){?>block<?php }else{ ?>none<?php }?>">
						<select name="order_message" id="order_message" onchange="orderOverwriteMessage(this, '<?php echo smartyTranslate(array('s'=>'Do you want to overwrite your existing message?'),$_smarty_tpl);?>
')">
							<option value="0" selected="selected">-- <?php echo smartyTranslate(array('s'=>'Choose a standard message'),$_smarty_tpl);?>
 --</option>
			<?php  $_smarty_tpl->tpl_vars['orderMessage'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['orderMessage']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['orderMessages']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['orderMessage']->key => $_smarty_tpl->tpl_vars['orderMessage']->value){
$_smarty_tpl->tpl_vars['orderMessage']->_loop = true;
?>
				<option value="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['orderMessage']->value['message'], 'htmlall', 'UTF-8');?>
"><?php echo $_smarty_tpl->tpl_vars['orderMessage']->value['name'];?>
</option>
			<?php } ?>
						</select><br /><br />
						<b><?php echo smartyTranslate(array('s'=>'Display to customer?'),$_smarty_tpl);?>
</b>
						<input type="radio" name="visibility" id="visibility" value="0" /> <?php echo smartyTranslate(array('s'=>'Yes'),$_smarty_tpl);?>

						<input type="radio" name="visibility" value="1" checked="checked" /> <?php echo smartyTranslate(array('s'=>'No'),$_smarty_tpl);?>

						<p id="nbchars" style="display:inline;font-size:10px;color:#666;"></p><br /><br />
				<textarea id="txt_msg" name="message" cols="50" rows="8" onKeyUp="var length = document.getElementById('txt_msg').value.length; if (length > 600) length = '600+'; document.getElementById('nbchars').innerHTML = '<?php echo smartyTranslate(array('s'=>'600 chars max'),$_smarty_tpl);?>
 (' + length + ')';"><?php echo smarty_modifier_escape(Tools::getValue('message'), 'htmlall', 'UTF-8');?>
</textarea><br /><br />
				<input type="hidden" name="id_order" value="<?php echo $_smarty_tpl->tpl_vars['order']->value->id;?>
" />
				<input type="hidden" name="id_customer" value="<?php echo $_smarty_tpl->tpl_vars['order']->value->id_customer;?>
" />
				<input type="submit" class="button" name="submitMessage" value="<?php echo smartyTranslate(array('s'=>'Send'),$_smarty_tpl);?>
" />
			</div>
		</fieldset>
		</form>

	<?php if ((sizeof($_smarty_tpl->tpl_vars['messages']->value))){?>
		<br />
		<fieldset style="width: 400px;">
		<legend><img src="../img/admin/email.gif" /> <?php echo smartyTranslate(array('s'=>'Messages'),$_smarty_tpl);?>
</legend>
		<?php  $_smarty_tpl->tpl_vars['message'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['message']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['messages']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['message']->key => $_smarty_tpl->tpl_vars['message']->value){
$_smarty_tpl->tpl_vars['message']->_loop = true;
?>
			<div style="overflow:auto; width:400px;" <?php if ($_smarty_tpl->tpl_vars['message']->value['is_new_for_me']){?>class="new_message"<?php }?>>
			<?php if (($_smarty_tpl->tpl_vars['message']->value['is_new_for_me'])){?>
				<a class="new_message" title="<?php echo smartyTranslate(array('s'=>'Mark this message as \'viewed\''),$_smarty_tpl);?>
" href="<?php echo $_SERVER['REQUEST_URI'];?>
&token=<?php echo $_GET['token'];?>
&messageReaded=<?php echo $_smarty_tpl->tpl_vars['message']->value['id_message'];?>
"><img src="../img/admin/enabled.gif" alt="" /></a>
			<?php }?>
			<?php echo smartyTranslate(array('s'=>'At'),$_smarty_tpl);?>
 <i><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['dateFormat'][0][0]->dateFormat(array('date'=>$_smarty_tpl->tpl_vars['message']->value['date_add']),$_smarty_tpl);?>

			</i> <?php echo smartyTranslate(array('s'=>'from'),$_smarty_tpl);?>
 <b><?php if (($_smarty_tpl->tpl_vars['message']->value['elastname'])){?><?php echo $_smarty_tpl->tpl_vars['message']->value['efirstname'];?>
 <?php echo $_smarty_tpl->tpl_vars['message']->value['elastname'];?>
<?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['message']->value['cfirstname'];?>
 <?php echo $_smarty_tpl->tpl_vars['message']->value['clastname'];?>
<?php }?></b>
			<?php if (($_smarty_tpl->tpl_vars['message']->value['private']==1)){?><span style="color:red; font-weight:bold;"><?php echo smartyTranslate(array('s'=>'Private:'),$_smarty_tpl);?>
</span><?php }?>
			<p><?php echo nl2br($_smarty_tpl->tpl_vars['message']->value['message']);?>
</p>
			</div>
			<br />
		<?php } ?>
		</fieldset>
	<?php }?>
	</div>


	<div class="clear">&nbsp;</div>
	<br /><br /><a href="<?php echo $_smarty_tpl->tpl_vars['current_index']->value;?>
&token=<?php echo $_GET['token'];?>
"><img src="../img/admin/arrow2.gif" /> <?php echo smartyTranslate(array('s'=>'Back to list'),$_smarty_tpl);?>
</a><br />


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
<?php }} ?><?php /* Smarty version Smarty-3.1.8, created on 2012-11-14 11:47:45
         compiled from "/home/bodo/public_html/dev/admin-dev/themes/default/template/controllers/orders/_documents.tpl" */ ?>
<?php if ($_valid && !is_callable('content_50a376d18de032_89841595')) {function content_50a376d18de032_89841595($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/home/bodo/public_html/dev/tools/smarty/plugins/modifier.escape.php';
?>
<table class="table" width="100%;" cellspacing="0" cellpadding="0" id="documents_table">
	<thead>
	<tr>
		<th style="width:10%"><?php echo smartyTranslate(array('s'=>'Date'),$_smarty_tpl);?>
</th>
		<th style=""><?php echo smartyTranslate(array('s'=>'Document'),$_smarty_tpl);?>
</th>
		<th style="width:20%"><?php echo smartyTranslate(array('s'=>'Number'),$_smarty_tpl);?>
</th>
		<th style="width:10%"><?php echo smartyTranslate(array('s'=>'Amount'),$_smarty_tpl);?>
</th>
		<th style="width:1%"></th>
	</tr>
	</thead>
	<tbody>
	<?php  $_smarty_tpl->tpl_vars['document'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['document']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['order']->value->getDocuments(); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['document']->key => $_smarty_tpl->tpl_vars['document']->value){
$_smarty_tpl->tpl_vars['document']->_loop = true;
?>

	<?php if (get_class($_smarty_tpl->tpl_vars['document']->value)=='OrderInvoice'){?>
		<?php if (isset($_smarty_tpl->tpl_vars['document']->value->is_delivery)){?>
		<tr class="invoice_line" id="delivery_<?php echo $_smarty_tpl->tpl_vars['document']->value->id;?>
">
		<?php }else{ ?>
		<tr class="invoice_line" id="invoice_<?php echo $_smarty_tpl->tpl_vars['document']->value->id;?>
">
		<?php }?>
	<?php }elseif(get_class($_smarty_tpl->tpl_vars['document']->value)=='OrderSlip'){?>
		<tr class="invoice_line" id="orderslip_<?php echo $_smarty_tpl->tpl_vars['document']->value->id;?>
">
	<?php }?>

		<td class="document_date"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['dateFormat'][0][0]->dateFormat(array('date'=>$_smarty_tpl->tpl_vars['document']->value->date_add),$_smarty_tpl);?>
</td>
		<td class="document_type">
			<?php if (get_class($_smarty_tpl->tpl_vars['document']->value)=='OrderInvoice'){?>
				<?php if (isset($_smarty_tpl->tpl_vars['document']->value->is_delivery)){?>
					<?php echo smartyTranslate(array('s'=>'Delivery slip'),$_smarty_tpl);?>

				<?php }else{ ?>
					<?php echo smartyTranslate(array('s'=>'Invoice'),$_smarty_tpl);?>

				<?php }?>
			<?php }elseif(get_class($_smarty_tpl->tpl_vars['document']->value)=='OrderSlip'){?>
				<?php echo smartyTranslate(array('s'=>'Credit Slip'),$_smarty_tpl);?>

			<?php }?></td>
		<td class="document_number">
			<?php if (get_class($_smarty_tpl->tpl_vars['document']->value)=='OrderInvoice'){?>
				<?php if (isset($_smarty_tpl->tpl_vars['document']->value->is_delivery)){?>
					<a target="_blank" href="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminPdf'), 'htmlall', 'UTF-8');?>
&submitAction=generateDeliverySlipPDF&id_order_invoice=<?php echo $_smarty_tpl->tpl_vars['document']->value->id;?>
">
			   	<?php }else{ ?>
					<a target="_blank" href="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminPdf'), 'htmlall', 'UTF-8');?>
&submitAction=generateInvoicePDF&id_order_invoice=<?php echo $_smarty_tpl->tpl_vars['document']->value->id;?>
">
			   <?php }?>
			<?php }elseif(get_class($_smarty_tpl->tpl_vars['document']->value)=='OrderSlip'){?>
				<a target="_blank" href="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminPdf'), 'htmlall', 'UTF-8');?>
&submitAction=generateOrderSlipPDF&id_order_slip=<?php echo $_smarty_tpl->tpl_vars['document']->value->id;?>
">
			<?php }?>
			<?php if (get_class($_smarty_tpl->tpl_vars['document']->value)=='OrderInvoice'){?>
				<?php if (isset($_smarty_tpl->tpl_vars['document']->value->is_delivery)){?>
					#<?php echo Configuration::get('PS_DELIVERY_PREFIX',$_smarty_tpl->tpl_vars['current_id_lang']->value);?>
<?php echo sprintf('%06d',$_smarty_tpl->tpl_vars['document']->value->delivery_number);?>

				<?php }else{ ?>
					<?php echo $_smarty_tpl->tpl_vars['document']->value->getInvoiceNumberFormatted($_smarty_tpl->tpl_vars['current_id_lang']->value);?>

				<?php }?>
			<?php }elseif(get_class($_smarty_tpl->tpl_vars['document']->value)=='OrderSlip'){?>
				<?php echo smartyTranslate(array('s'=>'#%d','sprintf'=>$_smarty_tpl->tpl_vars['document']->value->id),$_smarty_tpl);?>

			<?php }?> <img src="../img/admin/details.gif" alt="<?php echo smartyTranslate(array('s'=>'See the document'),$_smarty_tpl);?>
" /></a></td>
		<td class="document_amount">
		<?php if (get_class($_smarty_tpl->tpl_vars['document']->value)=='OrderInvoice'){?>
			<?php if (isset($_smarty_tpl->tpl_vars['document']->value->is_delivery)){?>
				--
			<?php }else{ ?>
				<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['document']->value->total_paid_tax_incl,'currency'=>$_smarty_tpl->tpl_vars['currency']->value->id),$_smarty_tpl);?>
&nbsp;
				<?php if ($_smarty_tpl->tpl_vars['document']->value->getTotalPaid()){?>
					<span style="color:red;font-weight:bold;">
					<?php if ($_smarty_tpl->tpl_vars['document']->value->getRestPaid()>0){?>
						(<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['document']->value->getRestPaid(),'currency'=>$_smarty_tpl->tpl_vars['currency']->value->id),$_smarty_tpl);?>
 <?php echo smartyTranslate(array('s'=>'not paid'),$_smarty_tpl);?>
)
					<?php }elseif($_smarty_tpl->tpl_vars['document']->value->getRestPaid()<0){?>
						(<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>-$_smarty_tpl->tpl_vars['document']->value->getRestPaid(),'currency'=>$_smarty_tpl->tpl_vars['currency']->value->id),$_smarty_tpl);?>
 <?php echo smartyTranslate(array('s'=>'overpaid'),$_smarty_tpl);?>
)
					<?php }?>
					</span>
				<?php }?>
			<?php }?>
		<?php }elseif(get_class($_smarty_tpl->tpl_vars['document']->value)=='OrderSlip'){?>
			<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['document']->value->amount,'currency'=>$_smarty_tpl->tpl_vars['currency']->value->id),$_smarty_tpl);?>

		<?php }?>
		</td>
		<td class="right document_action">
		<?php if (get_class($_smarty_tpl->tpl_vars['document']->value)=='OrderInvoice'){?>
			<?php if (!isset($_smarty_tpl->tpl_vars['document']->value->is_delivery)){?>
				<?php if ($_smarty_tpl->tpl_vars['document']->value->getRestPaid()){?>
					<a href="#" class="js-set-payment" data-amount="<?php echo $_smarty_tpl->tpl_vars['document']->value->getRestPaid();?>
" data-id-invoice="<?php echo $_smarty_tpl->tpl_vars['document']->value->id;?>
" title="<?php echo smartyTranslate(array('s'=>'Set payment form'),$_smarty_tpl);?>
"><img src="../img/admin/money_add.png" alt="<?php echo smartyTranslate(array('s'=>'Set payment form'),$_smarty_tpl);?>
" /></a>
				<?php }?>
				<a href="#" onclick="$('#invoiceNote<?php echo $_smarty_tpl->tpl_vars['document']->value->id;?>
').show(); return false;" title="<?php if ($_smarty_tpl->tpl_vars['document']->value->note==''){?><?php echo smartyTranslate(array('s'=>'Add note'),$_smarty_tpl);?>
<?php }else{ ?><?php echo smartyTranslate(array('s'=>'Edit note'),$_smarty_tpl);?>
<?php }?>"><img src="../img/admin/note.png" alt="<?php if ($_smarty_tpl->tpl_vars['document']->value->note==''){?><?php echo smartyTranslate(array('s'=>'Add note'),$_smarty_tpl);?>
<?php }else{ ?><?php echo smartyTranslate(array('s'=>'Edit note'),$_smarty_tpl);?>
<?php }?>"<?php if ($_smarty_tpl->tpl_vars['document']->value->note==''){?> class="js-disabled-action"<?php }?> /></a>
			<?php }?>
		<?php }?>
		</td>
	</tr>
	<?php if (get_class($_smarty_tpl->tpl_vars['document']->value)=='OrderInvoice'){?>
		<?php if (!isset($_smarty_tpl->tpl_vars['document']->value->is_delivery)){?>
	<tr id="invoiceNote<?php echo $_smarty_tpl->tpl_vars['document']->value->id;?>
" style="display:none" class="current-edit">
		<td colspan="5">
			<form action="<?php echo $_smarty_tpl->tpl_vars['current_index']->value;?>
&viewOrder&id_order=<?php echo $_smarty_tpl->tpl_vars['order']->value->id;?>
&token=<?php echo smarty_modifier_escape($_GET['token'], 'htmlall', 'UTF-8');?>
" method="post">
				<p>
					<label for="editNote<?php echo $_smarty_tpl->tpl_vars['document']->value->id;?>
" class="t"><?php echo smartyTranslate(array('s'=>'Note'),$_smarty_tpl);?>
</label>
					<input type="hidden" name="id_order_invoice" value="<?php echo $_smarty_tpl->tpl_vars['document']->value->id;?>
" />
					<textarea name="note" rows="10" cols="10" id="editNote<?php echo $_smarty_tpl->tpl_vars['document']->value->id;?>
" class="edit-note"><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['document']->value->note, 'htmlall', 'UTF-8');?>
</textarea>
				</p>
				<p class="right">
					<input type="submit" name="submitEditNote" value="<?php echo smartyTranslate(array('s'=>'Save'),$_smarty_tpl);?>
" class="button" />&nbsp;
					<a href="#" id="cancelNote" onclick="$('#invoiceNote<?php echo $_smarty_tpl->tpl_vars['document']->value->id;?>
').hide();return false;"><?php echo smartyTranslate(array('s'=>'Cancel'),$_smarty_tpl);?>
</a>
				</p>
			</form>
		</td>
	</tr>
		<?php }?>
	<?php }?>
	<?php }
if (!$_smarty_tpl->tpl_vars['document']->_loop) {
?>
	<tr>
		<td colspan="5" class="center">
			<h3><?php echo smartyTranslate(array('s'=>'No document is available'),$_smarty_tpl);?>
</h3>
			<?php if (isset($_smarty_tpl->tpl_vars['invoice_management_active']->value)&&$_smarty_tpl->tpl_vars['invoice_management_active']->value){?>
			<p><a class="button" href="<?php echo $_smarty_tpl->tpl_vars['current_index']->value;?>
&viewOrder&submitGenerateInvoice&id_order=<?php echo $_smarty_tpl->tpl_vars['order']->value->id;?>
&token=<?php echo smarty_modifier_escape($_GET['token'], 'htmlall', 'UTF-8');?>
"><?php echo smartyTranslate(array('s'=>'Generate invoice'),$_smarty_tpl);?>
</a></p>
			<?php }?>
		</td>
	</tr>
	<?php } ?>
	</tbody>
</table>
<?php }} ?><?php /* Smarty version Smarty-3.1.8, created on 2012-11-14 11:47:45
         compiled from "/home/bodo/public_html/dev/admin-dev/themes/default/template/controllers/orders/_shipping.tpl" */ ?>
<?php if ($_valid && !is_callable('content_50a376d1dcfb65_13316946')) {function content_50a376d1dcfb65_13316946($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_replace')) include '/home/bodo/public_html/dev/tools/smarty/plugins/modifier.replace.php';
if (!is_callable('smarty_modifier_escape')) include '/home/bodo/public_html/dev/tools/smarty/plugins/modifier.escape.php';
?>
<table class="table" width="100%" cellspacing="0" cellpadding="0" id="shipping_table">
<colgroup>
	<col width="15%">
	<col width="15%">
	<col width="">
	<col width="10%">
	<col width="20%">
</colgroup>
	<thead>
	<tr>
		<th><?php echo smartyTranslate(array('s'=>'Date:'),$_smarty_tpl);?>
</th>
		<th><?php echo smartyTranslate(array('s'=>'Type'),$_smarty_tpl);?>
</th>
		<th><?php echo smartyTranslate(array('s'=>'Carrier'),$_smarty_tpl);?>
</th>
		<th><?php echo smartyTranslate(array('s'=>'Weight'),$_smarty_tpl);?>
</th>
		<th><?php echo smartyTranslate(array('s'=>'Shipping cost'),$_smarty_tpl);?>
</th>
		<th><?php echo smartyTranslate(array('s'=>'Tracking number'),$_smarty_tpl);?>
</th>
	</tr>
	</thead>
	<tbody>
	<?php  $_smarty_tpl->tpl_vars['line'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['line']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['order']->value->getShipping(); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['line']->key => $_smarty_tpl->tpl_vars['line']->value){
$_smarty_tpl->tpl_vars['line']->_loop = true;
?>
	<tr>
		<td><?php echo $_smarty_tpl->tpl_vars['line']->value['date_add'];?>
</td>
		<td><?php echo $_smarty_tpl->tpl_vars['line']->value['type'];?>
</td>
		<td><?php echo $_smarty_tpl->tpl_vars['line']->value['state_name'];?>
</td>
		<td><?php echo sprintf("%.3f",$_smarty_tpl->tpl_vars['line']->value['weight']);?>
 <?php echo Configuration::get('PS_WEIGHT_UNIT');?>
</td>
		<td>
			<?php if ($_smarty_tpl->tpl_vars['order']->value->getTaxCalculationMethod()==@PS_TAX_INC){?>
				<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['line']->value['shipping_cost_tax_incl'],'currency'=>$_smarty_tpl->tpl_vars['currency']->value->id),$_smarty_tpl);?>

			<?php }else{ ?>
				<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['line']->value['shipping_cost_tax_excl'],'currency'=>$_smarty_tpl->tpl_vars['currency']->value->id),$_smarty_tpl);?>

			<?php }?>
		</td>
		<td>
			<span id="shipping_number_show"><?php if ($_smarty_tpl->tpl_vars['line']->value['url']&&$_smarty_tpl->tpl_vars['line']->value['tracking_number']){?><a href="<?php echo smarty_modifier_replace($_smarty_tpl->tpl_vars['line']->value['url'],'@',$_smarty_tpl->tpl_vars['line']->value['tracking_number']);?>
"><?php echo $_smarty_tpl->tpl_vars['line']->value['tracking_number'];?>
</a><?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['line']->value['tracking_number'];?>
<?php }?></span>
			<?php if ($_smarty_tpl->tpl_vars['line']->value['can_edit']){?>
				<form style="display: inline;" method="post" action="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminOrders'), 'htmlall', 'UTF-8');?>
&vieworder&id_order=<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['order']->value->id, 'htmlall', 'UTF-8');?>
">
					<span class="shipping_number_edit" style="display:none;">
						<input type="hidden" name="id_order_carrier" value="<?php echo htmlentities($_smarty_tpl->tpl_vars['line']->value['id_order_carrier']);?>
" />
						<input type="text" name="tracking_number" value="<?php echo htmlentities($_smarty_tpl->tpl_vars['line']->value['tracking_number']);?>
" />
						<input type="submit" class="button" name="submitShippingNumber" value="<?php echo smartyTranslate(array('s'=>'Update'),$_smarty_tpl);?>
" />
					</span>
					<a href="#" class="edit_shipping_number_link"><img src="../img/admin/edit.gif" alt="<?php echo smartyTranslate(array('s'=>'Edit'),$_smarty_tpl);?>
" /></a>
					<a href="#" class="cancel_shipping_number_link" style="display: none;"><img src="../img/admin/disabled.gif" alt="<?php echo smartyTranslate(array('s'=>'Cancel'),$_smarty_tpl);?>
" /></a>
				</form>
			<?php }?>
		</td>
	</tr>
	<?php } ?>
	</tbody>
</table>
<?php }} ?><?php /* Smarty version Smarty-3.1.8, created on 2012-11-14 11:47:46
         compiled from "/home/bodo/public_html/dev/admin-dev/themes/default/template/controllers/orders/_customized_data.tpl" */ ?>
<?php if ($_valid && !is_callable('content_50a376d21d4385_88666617')) {function content_50a376d21d4385_88666617($_smarty_tpl) {?>
<?php if ($_smarty_tpl->tpl_vars['product']->value['customizedDatas']){?>
	<tr class="customized customized-<?php echo $_smarty_tpl->tpl_vars['product']->value['id_order_detail'];?>
">
		<input type="hidden" class="edit_product_id_order_detail" value="<?php echo $_smarty_tpl->tpl_vars['product']->value['id_order_detail'];?>
" />
		<td align="center"><?php if (isset($_smarty_tpl->tpl_vars['product']->value['image'])&&$_smarty_tpl->tpl_vars['product']->value['image']->id){?><?php echo $_smarty_tpl->tpl_vars['product']->value['image_tag'];?>
<?php }else{ ?>--<?php }?></td>
		<td>
			<a href="index.php?controller=adminproducts&id_product=<?php echo $_smarty_tpl->tpl_vars['product']->value['product_id'];?>
&updateproduct&token=<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminProducts'),$_smarty_tpl);?>
">
			<span class="productName"><?php echo $_smarty_tpl->tpl_vars['product']->value['product_name'];?>
 - <?php echo smartyTranslate(array('s'=>'customized'),$_smarty_tpl);?>
</span><br />
			<?php if (($_smarty_tpl->tpl_vars['product']->value['product_reference'])){?><?php echo smartyTranslate(array('s'=>'Ref:'),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['product']->value['product_reference'];?>
<br /><?php }?>
			<?php if (($_smarty_tpl->tpl_vars['product']->value['product_supplier_reference'])){?><?php echo smartyTranslate(array('s'=>'Ref Supplier:'),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['product']->value['product_supplier_reference'];?>
<?php }?>
			</a>
		</td>
		<td align="center">
			<span class="product_price_show"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['product']->value['product_price_wt'],'currency'=>$_smarty_tpl->tpl_vars['currency']->value->id),$_smarty_tpl);?>
</span>
			<?php if ($_smarty_tpl->tpl_vars['can_edit']->value){?>
			<span class="product_price_edit" style="display:none;">
				<input type="hidden" name="product_id_order_detail" class="edit_product_id_order_detail" value="<?php echo $_smarty_tpl->tpl_vars['product']->value['id_order_detail'];?>
" />
				<?php if ($_smarty_tpl->tpl_vars['currency']->value->sign%2){?><?php echo $_smarty_tpl->tpl_vars['currency']->value->sign;?>
<?php }?><input type="text" name="product_price_tax_excl" class="edit_product_price_tax_excl edit_product_price" value="<?php echo Tools::ps_round($_smarty_tpl->tpl_vars['product']->value['unit_price_tax_excl'],2);?>
" size="5" /> <?php if (!($_smarty_tpl->tpl_vars['currency']->value->sign%2)){?><?php echo $_smarty_tpl->tpl_vars['currency']->value->sign;?>
<?php }?> <?php echo smartyTranslate(array('s'=>'tax excl.'),$_smarty_tpl);?>
<br />
				<?php if ($_smarty_tpl->tpl_vars['currency']->value->sign%2){?><?php echo $_smarty_tpl->tpl_vars['currency']->value->sign;?>
<?php }?><input type="text" name="product_price_tax_incl" class="edit_product_price_tax_incl edit_product_price" value="<?php echo Tools::ps_round($_smarty_tpl->tpl_vars['product']->value['unit_price_tax_incl'],2);?>
" size="5" /> <?php if (!($_smarty_tpl->tpl_vars['currency']->value->sign%2)){?><?php echo $_smarty_tpl->tpl_vars['currency']->value->sign;?>
<?php }?> <?php echo smartyTranslate(array('s'=>'tax incl.'),$_smarty_tpl);?>

			</span>
			<?php }?>
		</td>
		<td align="center" class="productQuantity"><?php echo $_smarty_tpl->tpl_vars['product']->value['customizationQuantityTotal'];?>
</td>
		<?php if (($_smarty_tpl->tpl_vars['order']->value->hasBeenPaid())){?><td align="center" class="productQuantity"><?php echo $_smarty_tpl->tpl_vars['product']->value['customizationQuantityRefunded'];?>
</td><?php }?>
		<?php if (($_smarty_tpl->tpl_vars['order']->value->hasBeenDelivered())){?><td align="center" class="productQuantity"><?php echo $_smarty_tpl->tpl_vars['product']->value['customizationQuantityReturned'];?>
</td><?php }?>
		<td align="center" class=""> - </td>
		<td align="center" class="total_product">
		<?php if (($_smarty_tpl->tpl_vars['order']->value->getTaxCalculationMethod()==@PS_TAX_EXC)){?>
			<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>Tools::ps_round($_smarty_tpl->tpl_vars['product']->value['product_price']*$_smarty_tpl->tpl_vars['product']->value['customizationQuantityTotal'],2),'currency'=>$_smarty_tpl->tpl_vars['currency']->value->id),$_smarty_tpl);?>

		<?php }else{ ?>
			<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>Tools::ps_round($_smarty_tpl->tpl_vars['product']->value['product_price_wt']*$_smarty_tpl->tpl_vars['product']->value['customizationQuantityTotal'],2),'currency'=>$_smarty_tpl->tpl_vars['currency']->value->id),$_smarty_tpl);?>

		<?php }?>
		</td>
		<td class="edit_product_fields" colspan="2" style="display:none">&nbsp;</td>
		<td class="partial_refund_fields current-edit" style="text-align:left;display:none"></td>
		<td class="product_action" style="text-align:right">
			<a href="#" class="edit_product_change_link"><img src="../img/admin/edit.gif" alt="<?php echo smartyTranslate(array('s'=>'Edit'),$_smarty_tpl);?>
" /></a>
			<input type="submit" class="button" name="submitProductChange" value="<?php echo smartyTranslate(array('s'=>'Update'),$_smarty_tpl);?>
"  style="display: none;" />
			<a href="#" class="cancel_product_change_link" style="display: none;"><img src="../img/admin/disabled.gif" alt="<?php echo smartyTranslate(array('s'=>'Cancel'),$_smarty_tpl);?>
" /></a>
			<a href="#" class="delete_product_line"><img src="../img/admin/delete.gif" alt="<?php echo smartyTranslate(array('s'=>'Delete'),$_smarty_tpl);?>
" /></a>
		</td>
	</tr>
	<?php  $_smarty_tpl->tpl_vars['customizationPerAddress'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['customizationPerAddress']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['product']->value['customizedDatas']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['customizationPerAddress']->key => $_smarty_tpl->tpl_vars['customizationPerAddress']->value){
$_smarty_tpl->tpl_vars['customizationPerAddress']->_loop = true;
?>
		<?php  $_smarty_tpl->tpl_vars['customization'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['customization']->_loop = false;
 $_smarty_tpl->tpl_vars['customizationId'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['customizationPerAddress']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['customization']->key => $_smarty_tpl->tpl_vars['customization']->value){
$_smarty_tpl->tpl_vars['customization']->_loop = true;
 $_smarty_tpl->tpl_vars['customizationId']->value = $_smarty_tpl->tpl_vars['customization']->key;
?>
			<tr class="customized customized-<?php echo $_smarty_tpl->tpl_vars['product']->value['id_order_detail'];?>
">
				<input type="hidden" class="edit_product_id_order_detail" value="<?php echo $_smarty_tpl->tpl_vars['product']->value['id_order_detail'];?>
" />
				<td colspan="2">
			<?php  $_smarty_tpl->tpl_vars['datas'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['datas']->_loop = false;
 $_smarty_tpl->tpl_vars['type'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['customization']->value['datas']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['datas']->key => $_smarty_tpl->tpl_vars['datas']->value){
$_smarty_tpl->tpl_vars['datas']->_loop = true;
 $_smarty_tpl->tpl_vars['type']->value = $_smarty_tpl->tpl_vars['datas']->key;
?>
				<?php if (($_smarty_tpl->tpl_vars['type']->value==Product::CUSTOMIZE_FILE)){?>
					<ul style="margin: 4px 0px 4px 0px; padding: 0px; list-style-type: none;">
					<?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['data']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['datas']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['data']->iteration=0;
foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value){
$_smarty_tpl->tpl_vars['data']->_loop = true;
 $_smarty_tpl->tpl_vars['data']->iteration++;
?>
						<li style="margin: 2px;">
							<span><?php if ($_smarty_tpl->tpl_vars['data']->value['name']){?><?php echo $_smarty_tpl->tpl_vars['data']->value['name'];?>
<?php }else{ ?><?php echo smartyTranslate(array('s'=>'Picture #'),$_smarty_tpl);?>
<?php echo $_smarty_tpl->tpl_vars['data']->iteration;?>
<?php }?><?php echo smartyTranslate(array('s'=>':'),$_smarty_tpl);?>
</span>&nbsp;
								<a href="displayImage.php?img=<?php echo $_smarty_tpl->tpl_vars['data']->value['value'];?>
&name=<?php echo $_smarty_tpl->tpl_vars['order']->value->id;?>
-file<?php echo $_smarty_tpl->tpl_vars['data']->iteration;?>
" target="_blank"><img src="<?php echo @_THEME_PROD_PIC_DIR_;?>
<?php echo $_smarty_tpl->tpl_vars['data']->value['value'];?>
_small" alt="" /></a>
						</li>
					<?php } ?>
					</ul>
				<?php }elseif(($_smarty_tpl->tpl_vars['type']->value==Product::CUSTOMIZE_TEXTFIELD)){?>
					<ul style="margin: 0px 0px 4px 0px; padding: 0px 0px 0px 6px; list-style-type: none;">
					<?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['data']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['datas']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['data']->iteration=0;
foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value){
$_smarty_tpl->tpl_vars['data']->_loop = true;
 $_smarty_tpl->tpl_vars['data']->iteration++;
?>
						<li><?php if ($_smarty_tpl->tpl_vars['data']->value['name']){?><?php echo smartyTranslate(array('s'=>'%s:','sprintf'=>$_smarty_tpl->tpl_vars['data']->value['name']),$_smarty_tpl);?>
<?php }else{ ?><?php echo smartyTranslate(array('s'=>'Text #%s:','sprintf'=>$_smarty_tpl->tpl_vars['data']->iteration),$_smarty_tpl);?>
<?php }?> <?php echo $_smarty_tpl->tpl_vars['data']->value['value'];?>
</li>
					<?php } ?>
					</ul>
				<?php }?>
			<?php } ?>
				</td>
				<td align="center">-
				</td>
				<td align="center" class="productQuantity">
					<span class="product_quantity_show"><?php echo $_smarty_tpl->tpl_vars['customization']->value['quantity'];?>
</span>
					<?php if ($_smarty_tpl->tpl_vars['can_edit']->value){?>
					<span class="product_quantity_edit" style="display:none;">
						<input type="text" name="product_quantity[<?php echo $_smarty_tpl->tpl_vars['customizationId']->value;?>
]" class="edit_product_quantity" value="<?php echo htmlentities($_smarty_tpl->tpl_vars['customization']->value['quantity']);?>
" size="2" />
					</span>
					<?php }?>
				</td>
				<?php if (($_smarty_tpl->tpl_vars['order']->value->hasBeenPaid())){?><td align="center"><?php echo $_smarty_tpl->tpl_vars['customization']->value['quantity_refunded'];?>
</td><?php }?>
				<?php if (($_smarty_tpl->tpl_vars['order']->value->hasBeenDelivered())){?><td align="center"><?php echo $_smarty_tpl->tpl_vars['customization']->value['quantity_returned'];?>
</td><?php }?>
				<td align="center">
				-
				</td>
				<td align="center" class="total_product">
					<?php if (($_smarty_tpl->tpl_vars['order']->value->getTaxCalculationMethod()==@PS_TAX_EXC)){?>
						<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>Tools::ps_round($_smarty_tpl->tpl_vars['product']->value['product_price']*$_smarty_tpl->tpl_vars['customization']->value['quantity'],2),'currency'=>$_smarty_tpl->tpl_vars['currency']->value->id),$_smarty_tpl);?>

					<?php }else{ ?>
						<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>Tools::ps_round($_smarty_tpl->tpl_vars['product']->value['product_price_wt']*$_smarty_tpl->tpl_vars['customization']->value['quantity'],2),'currency'=>$_smarty_tpl->tpl_vars['currency']->value->id),$_smarty_tpl);?>

					<?php }?>
				</td>
				<td class="partial_refund_fields current-edit" style="text-align:left;display:none">
					<div style="width:40%;margin-top:5px;float:left"><?php echo smartyTranslate(array('s'=>'Quantity:'),$_smarty_tpl);?>
</div> <div style="width:60%;margin-top:2px;float:left"><input type="text" size="3" name="partialRefundProductQuantity[<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
]" value="0" /> 0/<?php echo $_smarty_tpl->tpl_vars['customization']->value['quantity']-$_smarty_tpl->tpl_vars['product']->value['product_quantity_refunded'];?>
</div>
					<div style="width:40%;margin-top:5px;float:left"><?php echo smartyTranslate(array('s'=>'Amount:'),$_smarty_tpl);?>
</div> <div style="width:60%;margin-top:2px;float:left"><input type="text" size="3" name="partialRefundProduct[<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
]" /> &euro;</div>
				</td>
				<td class="edit_product_fields" colspan="2" style="display:none"></td>
				<td class="product_action" style="text-align:right">
				</td>
			</tr>
		<?php } ?>
	<?php } ?>
<?php }?>
<?php }} ?><?php /* Smarty version Smarty-3.1.8, created on 2012-11-14 11:47:46
         compiled from "/home/bodo/public_html/dev/admin-dev/themes/default/template/controllers/orders/_product_line.tpl" */ ?>
<?php if ($_valid && !is_callable('content_50a376d23e8743_15481212')) {function content_50a376d23e8743_15481212($_smarty_tpl) {?>


<?php if (($_smarty_tpl->tpl_vars['order']->value->getTaxCalculationMethod()==@PS_TAX_EXC)){?>
	<?php $_smarty_tpl->tpl_vars['product_price'] = new Smarty_variable(($_smarty_tpl->tpl_vars['product']->value['unit_price_tax_excl']+$_smarty_tpl->tpl_vars['product']->value['ecotax']), null, 0);?>
<?php }else{ ?>
	<?php $_smarty_tpl->tpl_vars['product_price'] = new Smarty_variable($_smarty_tpl->tpl_vars['product']->value['unit_price_tax_incl'], null, 0);?>
<?php }?>

<?php if (($_smarty_tpl->tpl_vars['product']->value['product_quantity']>$_smarty_tpl->tpl_vars['product']->value['customizationQuantityTotal'])){?>
<tr<?php if (isset($_smarty_tpl->tpl_vars['product']->value['image'])&&$_smarty_tpl->tpl_vars['product']->value['image']->id&&isset($_smarty_tpl->tpl_vars['product']->value['image_size'])){?> height="<?php echo $_smarty_tpl->tpl_vars['product']->value['image_size'][1]+7;?>
"<?php }?>>
	<td align="center"><?php if (isset($_smarty_tpl->tpl_vars['product']->value['image'])&&$_smarty_tpl->tpl_vars['product']->value['image']->id){?><?php echo $_smarty_tpl->tpl_vars['product']->value['image_tag'];?>
<?php }?></td>
	<td><a href="index.php?controller=adminproducts&id_product=<?php echo $_smarty_tpl->tpl_vars['product']->value['product_id'];?>
&updateproduct&token=<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminProducts'),$_smarty_tpl);?>
">
		<span class="productName"><?php echo $_smarty_tpl->tpl_vars['product']->value['product_name'];?>
</span><br />
		<?php if ($_smarty_tpl->tpl_vars['product']->value['product_reference']){?><?php echo smartyTranslate(array('s'=>'Ref:'),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['product']->value['product_reference'];?>
<br /><?php }?>
		<?php if ($_smarty_tpl->tpl_vars['product']->value['product_supplier_reference']){?><?php echo smartyTranslate(array('s'=>'Ref Supplier:'),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['product']->value['product_supplier_reference'];?>
<?php }?>
	</a></td>
	<td align="center">
		<span class="product_price_show"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['product_price']->value,'currency'=>$_smarty_tpl->tpl_vars['currency']->value->id),$_smarty_tpl);?>
</span>
		<?php if ($_smarty_tpl->tpl_vars['can_edit']->value){?>
		<span class="product_price_edit" style="display:none;">
			<input type="hidden" name="product_id_order_detail" class="edit_product_id_order_detail" value="<?php echo $_smarty_tpl->tpl_vars['product']->value['id_order_detail'];?>
" />
			<?php if ($_smarty_tpl->tpl_vars['currency']->value->sign%2){?><?php echo $_smarty_tpl->tpl_vars['currency']->value->sign;?>
<?php }?><input type="text" name="product_price_tax_excl" class="edit_product_price_tax_excl edit_product_price" value="<?php echo Tools::ps_round($_smarty_tpl->tpl_vars['product']->value['unit_price_tax_excl'],2);?>
" size="5" /> <?php if (!($_smarty_tpl->tpl_vars['currency']->value->sign%2)){?><?php echo $_smarty_tpl->tpl_vars['currency']->value->sign;?>
<?php }?> <?php echo smartyTranslate(array('s'=>'tax excl.'),$_smarty_tpl);?>
<br />
			<?php if ($_smarty_tpl->tpl_vars['currency']->value->sign%2){?><?php echo $_smarty_tpl->tpl_vars['currency']->value->sign;?>
<?php }?><input type="text" name="product_price_tax_incl" class="edit_product_price_tax_incl edit_product_price" value="<?php echo Tools::ps_round($_smarty_tpl->tpl_vars['product']->value['unit_price_tax_incl'],2);?>
" size="5" /> <?php if (!($_smarty_tpl->tpl_vars['currency']->value->sign%2)){?><?php echo $_smarty_tpl->tpl_vars['currency']->value->sign;?>
<?php }?> <?php echo smartyTranslate(array('s'=>'tax incl.'),$_smarty_tpl);?>

		</span>
		<?php }?>
	</td>
	<td align="center" class="productQuantity">
		<span class="product_quantity_show"><?php echo $_smarty_tpl->tpl_vars['product']->value['product_quantity'];?>
</span>
		<?php if ($_smarty_tpl->tpl_vars['can_edit']->value){?>
		<span class="product_quantity_edit" style="display:none;">
			<input type="text" name="product_quantity" class="edit_product_quantity" value="<?php echo htmlentities($_smarty_tpl->tpl_vars['product']->value['product_quantity']);?>
" size="2" />
		</span>
		<?php }?>
	</td>
	<?php if (($_smarty_tpl->tpl_vars['order']->value->hasBeenPaid())){?>
		<td align="center" class="productQuantity">
			<?php echo $_smarty_tpl->tpl_vars['product']->value['product_quantity_refunded'];?>

			<?php if (count($_smarty_tpl->tpl_vars['product']->value['refund_history'])){?>
				<span class="tooltip">
					<span class="tooltip_label tooltip_button">+</span>
					<div class="tooltip_content">
					<span class="title"><?php echo smartyTranslate(array('s'=>'Refund history'),$_smarty_tpl);?>
</span>
					<?php  $_smarty_tpl->tpl_vars['refund'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['refund']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['product']->value['refund_history']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['refund']->key => $_smarty_tpl->tpl_vars['refund']->value){
$_smarty_tpl->tpl_vars['refund']->_loop = true;
?>
						<?php ob_start();?><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['dateFormat'][0][0]->dateFormat(array('date'=>$_smarty_tpl->tpl_vars['refund']->value['date_add']),$_smarty_tpl);?>
<?php $_tmp1=ob_get_clean();?><?php ob_start();?><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['refund']->value['amount_tax_incl']),$_smarty_tpl);?>
<?php $_tmp2=ob_get_clean();?><?php echo smartyTranslate(array('s'=>'%1s - %2s','sprintf'=>array($_tmp1,$_tmp2)),$_smarty_tpl);?>
<br />
					<?php } ?>
					</div>
				</span>
			<?php }?>
		</td>
	<?php }?>
	<?php if ($_smarty_tpl->tpl_vars['order']->value->hasBeenDelivered()||$_smarty_tpl->tpl_vars['order']->value->hasProductReturned()){?>
		<td align="center" class="productQuantity">
			<?php echo $_smarty_tpl->tpl_vars['product']->value['product_quantity_return'];?>

			<?php if (count($_smarty_tpl->tpl_vars['product']->value['return_history'])){?>
				<span class="tooltip">
					<span class="tooltip_label tooltip_button">+</span>
					<div class="tooltip_content">
					<span class="title"><?php echo smartyTranslate(array('s'=>'Return history'),$_smarty_tpl);?>
</span>
					<?php  $_smarty_tpl->tpl_vars['return'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['return']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['product']->value['return_history']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['return']->key => $_smarty_tpl->tpl_vars['return']->value){
$_smarty_tpl->tpl_vars['return']->_loop = true;
?>
						<?php ob_start();?><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['dateFormat'][0][0]->dateFormat(array('date'=>$_smarty_tpl->tpl_vars['return']->value['date_add']),$_smarty_tpl);?>
<?php $_tmp3=ob_get_clean();?><?php echo smartyTranslate(array('s'=>'%1s - %2s - %3s','sprintf'=>array($_tmp3,$_smarty_tpl->tpl_vars['return']->value['product_quantity'],$_smarty_tpl->tpl_vars['return']->value['state'])),$_smarty_tpl);?>
<br />
					<?php } ?>
					</div>
				</span>
			<?php }?>
		</td>
	<?php }?>
	<td align="center" class="productQuantity product_stock"><?php echo $_smarty_tpl->tpl_vars['product']->value['current_stock'];?>
</td>
	<td align="center" class="total_product">
		<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>(Tools::ps_round($_smarty_tpl->tpl_vars['product_price']->value,2)*($_smarty_tpl->tpl_vars['product']->value['product_quantity']-$_smarty_tpl->tpl_vars['product']->value['customizationQuantityTotal'])),'currency'=>$_smarty_tpl->tpl_vars['currency']->value->id),$_smarty_tpl);?>

	</td>
	<td colspan="2" style="display: none;" class="add_product_fields">&nbsp;</td>
	<td align="center" class="cancelCheck standard_refund_fields current-edit" style="display:none">
		<input type="hidden" name="totalQtyReturn" id="totalQtyReturn" value="<?php echo $_smarty_tpl->tpl_vars['product']->value['product_quantity_return'];?>
" />
		<input type="hidden" name="totalQty" id="totalQty" value="<?php echo $_smarty_tpl->tpl_vars['product']->value['product_quantity'];?>
" />
		<input type="hidden" name="productName" id="productName" value="<?php echo $_smarty_tpl->tpl_vars['product']->value['product_name'];?>
" />
	<?php if (((!$_smarty_tpl->tpl_vars['order']->value->hasBeenDelivered()||Configuration::get('PS_ORDER_RETURN'))&&(int)($_smarty_tpl->tpl_vars['product']->value['product_quantity_return'])<(int)($_smarty_tpl->tpl_vars['product']->value['product_quantity']))){?>
		<input type="checkbox" name="id_order_detail[<?php echo $_smarty_tpl->tpl_vars['product']->value['id_order_detail'];?>
]" id="id_order_detail[<?php echo $_smarty_tpl->tpl_vars['product']->value['id_order_detail'];?>
]" value="<?php echo $_smarty_tpl->tpl_vars['product']->value['id_order_detail'];?>
" onchange="setCancelQuantity(this, <?php echo $_smarty_tpl->tpl_vars['product']->value['id_order_detail'];?>
, <?php echo $_smarty_tpl->tpl_vars['product']->value['product_quantity_in_stock']-$_smarty_tpl->tpl_vars['product']->value['customizationQuantityTotal']-$_smarty_tpl->tpl_vars['product']->value['product_quantity_reinjected'];?>
)" <?php if (($_smarty_tpl->tpl_vars['product']->value['product_quantity_return']+$_smarty_tpl->tpl_vars['product']->value['product_quantity_refunded']>=$_smarty_tpl->tpl_vars['product']->value['product_quantity'])){?>disabled="disabled" <?php }?>/>
	<?php }else{ ?>
		--
	<?php }?>
	</td>
	<td class="cancelQuantity standard_refund_fields current-edit" style="display:none">
	<?php if (($_smarty_tpl->tpl_vars['product']->value['product_quantity_return']+$_smarty_tpl->tpl_vars['product']->value['product_quantity_refunded']>=$_smarty_tpl->tpl_vars['product']->value['product_quantity'])){?>
		<input type="hidden" name="cancelQuantity[<?php echo $_smarty_tpl->tpl_vars['product']->value['id_order_detail'];?>
]" value="0" />
	<?php }elseif((!$_smarty_tpl->tpl_vars['order']->value->hasBeenDelivered()||Configuration::get('PS_ORDER_RETURN'))){?>
		<input type="text" id="cancelQuantity_<?php echo $_smarty_tpl->tpl_vars['product']->value['id_order_detail'];?>
" name="cancelQuantity[<?php echo $_smarty_tpl->tpl_vars['product']->value['id_order_detail'];?>
]" size="2" onclick="selectCheckbox(this);" value="" />
	<?php }?>

	<?php if ($_smarty_tpl->tpl_vars['product']->value['customizationQuantityTotal']){?>
		<?php $_smarty_tpl->tpl_vars['productQuantity'] = new Smarty_variable(($_smarty_tpl->tpl_vars['product']->value['product_quantity']-$_smarty_tpl->tpl_vars['product']->value['customizationQuantityTotal']), null, 0);?>
	<?php }else{ ?>
		<?php $_smarty_tpl->tpl_vars['productQuantity'] = new Smarty_variable($_smarty_tpl->tpl_vars['product']->value['product_quantity'], null, 0);?>
	<?php }?>

	<?php if (($_smarty_tpl->tpl_vars['order']->value->hasBeenDelivered())){?>
		<?php echo $_smarty_tpl->tpl_vars['product']->value['product_quantity_refunded'];?>
/<?php echo $_smarty_tpl->tpl_vars['productQuantity']->value-$_smarty_tpl->tpl_vars['product']->value['product_quantity_refunded'];?>

	<?php }elseif(($_smarty_tpl->tpl_vars['order']->value->hasBeenPaid())){?>
		<?php echo $_smarty_tpl->tpl_vars['product']->value['product_quantity_return'];?>
/<?php echo $_smarty_tpl->tpl_vars['productQuantity']->value;?>

	<?php }else{ ?>
		0/<?php echo $_smarty_tpl->tpl_vars['productQuantity']->value;?>

	<?php }?>
	</td>
	<td class="partial_refund_fields current-edit" style="text-align:left;display:none">
		<div style="width:40%;margin-top:5px;float:left"><?php echo smartyTranslate(array('s'=>'Quantity:'),$_smarty_tpl);?>
</div> <div style="width:60%;margin-top:2px;float:left"><input onchange="checkPartialRefundProductQuantity(this)" type="text" size="3" name="partialRefundProductQuantity[<?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['product']->value['id_order_detail'];?>
<?php $_tmp4=ob_get_clean();?><?php echo $_tmp4;?>
]" value="0" /> 0/<?php echo $_smarty_tpl->tpl_vars['productQuantity']->value-$_smarty_tpl->tpl_vars['product']->value['product_quantity_refunded'];?>
</div>
		<div style="width:40%;margin-top:5px;float:left"><?php echo smartyTranslate(array('s'=>'Amount:'),$_smarty_tpl);?>
</div> <div style="width:60%;margin-top:2px;float:left"><input onchange="checkPartialRefundProductAmount(this)" type="text" size="3" name="partialRefundProduct[<?php echo $_smarty_tpl->tpl_vars['product']->value['id_order_detail'];?>
]" /> &euro;</div> <?php if (!empty($_smarty_tpl->tpl_vars['product']->value['amount_refund'])&&$_smarty_tpl->tpl_vars['product']->value['amount_refund']>0){?>(<?php echo smartyTranslate(array('s'=>'%s refund','sprintf'=>$_smarty_tpl->tpl_vars['product']->value['amount_refund']),$_smarty_tpl);?>
)<?php }?>
		<input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['product']->value['quantity_refundable'];?>
" class="partialRefundProductQuantity" />
		<input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['product']->value['amount_refundable'];?>
" class="partialRefundProductAmount" />
	</td>
	<?php if (($_smarty_tpl->tpl_vars['can_edit']->value&&!$_smarty_tpl->tpl_vars['order']->value->hasBeenDelivered())){?>
	<td class="product_invoice" colspan="2" style="display: none;text-align:center;">
		<?php if (sizeof($_smarty_tpl->tpl_vars['invoices_collection']->value)){?>
		<select name="product_invoice" class="edit_product_invoice">
			<?php  $_smarty_tpl->tpl_vars['invoice'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['invoice']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['invoices_collection']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['invoice']->key => $_smarty_tpl->tpl_vars['invoice']->value){
$_smarty_tpl->tpl_vars['invoice']->_loop = true;
?>
			<option value="<?php echo $_smarty_tpl->tpl_vars['invoice']->value->id;?>
" <?php if ($_smarty_tpl->tpl_vars['invoice']->value->id==$_smarty_tpl->tpl_vars['product']->value['id_order_invoice']){?>selected="selected"<?php }?>>#<?php echo Configuration::get('PS_INVOICE_PREFIX',$_smarty_tpl->tpl_vars['current_id_lang']->value);?>
<?php echo sprintf('%06d',$_smarty_tpl->tpl_vars['invoice']->value->number);?>
</option>
			<?php } ?>
		</select>
		<?php }else{ ?>
		&nbsp;
		<?php }?>
	</td>
	<td class="product_action" style="text-align:right">
		<a href="#" class="edit_product_change_link"><img src="../img/admin/edit.gif" alt="<?php echo smartyTranslate(array('s'=>'Edit'),$_smarty_tpl);?>
" /></a>
		<input type="submit" class="button" name="submitProductChange" value="<?php echo smartyTranslate(array('s'=>'Update'),$_smarty_tpl);?>
"  style="display: none;" />
		<a href="#" class="cancel_product_change_link" style="display: none;"><img src="../img/admin/disabled.gif" alt="<?php echo smartyTranslate(array('s'=>'Cancel'),$_smarty_tpl);?>
" /></a>
		<a href="#" class="delete_product_line"><img src="../img/admin/delete.gif" alt="<?php echo smartyTranslate(array('s'=>'Delete'),$_smarty_tpl);?>
" /></a>
	</td>
	<?php }?>
</tr>
<?php }?>
<?php }} ?><?php /* Smarty version Smarty-3.1.8, created on 2012-11-14 11:47:46
         compiled from "/home/bodo/public_html/dev/admin-dev/themes/default/template/controllers/orders/_new_product.tpl" */ ?>
<?php if ($_valid && !is_callable('content_50a376d26c5019_76292654')) {function content_50a376d26c5019_76292654($_smarty_tpl) {?>
<tr id="new_product" height="52" style="display:none;background-color:#e9f1f6">
	<td style="display:none;" colspan="2">
		<input type="hidden" id="add_product_product_id" name="add_product[product_id]" value="0" />
		<?php echo smartyTranslate(array('s'=>'Product:'),$_smarty_tpl);?>
 <input type="text" id="add_product_product_name" value="" size="42" />
		<div id="add_product_product_attribute_area" style="margin-top: 5px;display: none;">
			<?php echo smartyTranslate(array('s'=>'Combinations:'),$_smarty_tpl);?>
 <select name="add_product[product_attribute_id]" id="add_product_product_attribute_id"></select>
		</div>
		<div id="add_product_product_warehouse_area" style="margin-top: 5px; display: none;">
			<?php echo smartyTranslate(array('s'=>'Warehouse:'),$_smarty_tpl);?>
 <select  id="add_product_warehouse" name="add_product_warehouse">
			</select>
		</div>
	</td>
	<td style="display:none;">
		<?php if ($_smarty_tpl->tpl_vars['currency']->value->sign%2){?><?php echo $_smarty_tpl->tpl_vars['currency']->value->sign;?>
<?php }?><input type="text" name="add_product[product_price_tax_excl]" id="add_product_product_price_tax_excl" value="" size="4" disabled="disabled" /> <?php if (!($_smarty_tpl->tpl_vars['currency']->value->sign%2)){?><?php echo $_smarty_tpl->tpl_vars['currency']->value->sign;?>
<?php }?> <?php echo smartyTranslate(array('s'=>'tax excl.'),$_smarty_tpl);?>
<br />
		<?php if ($_smarty_tpl->tpl_vars['currency']->value->sign%2){?><?php echo $_smarty_tpl->tpl_vars['currency']->value->sign;?>
<?php }?><input type="text" name="add_product[product_price_tax_incl]" id="add_product_product_price_tax_incl" value="" size="4" disabled="disabled" /> <?php if (!($_smarty_tpl->tpl_vars['currency']->value->sign%2)){?><?php echo $_smarty_tpl->tpl_vars['currency']->value->sign;?>
<?php }?> <?php echo smartyTranslate(array('s'=>'tax incl.'),$_smarty_tpl);?>
<br />
	</td>
	<td style="display:none;" align="center" class="productQuantity"><input type="text" name="add_product[product_quantity]" id="add_product_product_quantity" value="1" size="3" disabled="disabled" /></td>
	<?php if (($_smarty_tpl->tpl_vars['order']->value->hasBeenPaid())){?><td style="display:none;" align="center" class="productQuantity">&nbsp;</td><?php }?>
	<?php if (($_smarty_tpl->tpl_vars['order']->value->hasBeenDelivered())){?><td style="display:none;" align="center" class="productQuantity">&nbsp;</td><?php }?>
	<td style="display:none;" align="center" class="productQuantity" id="add_product_product_stock">0</td>
	<td style="display:none;" align="center" id="add_product_product_total"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>0,'currency'=>$_smarty_tpl->tpl_vars['currency']->value->id),$_smarty_tpl);?>
</td>
	<td style="display:none;" align="center" colspan="2">
		<?php if (sizeof($_smarty_tpl->tpl_vars['invoices_collection']->value)){?>
		<select name="add_product[invoice]" id="add_product_product_invoice" disabled="disabled">
			<optgroup class="existing" label="<?php echo smartyTranslate(array('s'=>'Existing'),$_smarty_tpl);?>
">
				<?php  $_smarty_tpl->tpl_vars['invoice'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['invoice']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['invoices_collection']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['invoice']->key => $_smarty_tpl->tpl_vars['invoice']->value){
$_smarty_tpl->tpl_vars['invoice']->_loop = true;
?>
				<option value="<?php echo $_smarty_tpl->tpl_vars['invoice']->value->id;?>
"><?php echo $_smarty_tpl->tpl_vars['invoice']->value->getInvoiceNumberFormatted($_smarty_tpl->tpl_vars['current_id_lang']->value);?>
</option>
				<?php } ?>
			</optgroup>
			<optgroup label="<?php echo smartyTranslate(array('s'=>'New'),$_smarty_tpl);?>
">
				<option value="0"><?php echo smartyTranslate(array('s'=>'Create a new invoice'),$_smarty_tpl);?>
</option>
			</optgroup>
		</select>
		<?php }?>
	</td>
	<td style="display:none;">
		<input type="button" class="button" id="submitAddProduct" value="<?php echo smartyTranslate(array('s'=>'Add product'),$_smarty_tpl);?>
" disabled="disabled" />
	</td>
</tr>
<tr id="new_invoice" style="display:none;background-color:#e9f1f6;">
	<td colspan="10">
		<h3><?php echo smartyTranslate(array('s'=>'New invoice information'),$_smarty_tpl);?>
</h3>
		<label><?php echo smartyTranslate(array('s'=>'Carrier:'),$_smarty_tpl);?>
</label>
		<div class="margin-form">
			<?php echo $_smarty_tpl->tpl_vars['carrier']->value->name;?>

		</div>
		<div class="margin-form">
			<input type="checkbox" name="add_invoice[free_shipping]" value="1" />
			<label class="t"><?php echo smartyTranslate(array('s'=>'Free shipping'),$_smarty_tpl);?>
</label>
			<p><?php echo smartyTranslate(array('s'=>'If you don\'t select "Free shipping," the normal shipping cost will be applied'),$_smarty_tpl);?>
</p>
		</div>
	</td>
</tr>
<?php }} ?><?php /* Smarty version Smarty-3.1.8, created on 2012-11-14 11:47:46
         compiled from "/home/bodo/public_html/dev/admin-dev/themes/default/template/controllers/orders/_discount_form.tpl" */ ?>
<?php if ($_valid && !is_callable('content_50a376d2889392_53206881')) {function content_50a376d2889392_53206881($_smarty_tpl) {?>

	<label><?php echo smartyTranslate(array('s'=>'Name'),$_smarty_tpl);?>
</label>
	<div class="margin-form">
		<input type="text" name="discount_name" value="" />
	</div>

	<label><?php echo smartyTranslate(array('s'=>'Type'),$_smarty_tpl);?>
</label>
	<div class="margin-form">
		<select name="discount_type" id="discount_type">
			<option value="1"><?php echo smartyTranslate(array('s'=>'Percent'),$_smarty_tpl);?>
</option>
			<option value="2"><?php echo smartyTranslate(array('s'=>'Amount'),$_smarty_tpl);?>
</option>
			<option value="3"><?php echo smartyTranslate(array('s'=>'Free shipping'),$_smarty_tpl);?>
</option>
		</select>
	</div>

	<div id="discount_value_field">
		<label><?php echo smartyTranslate(array('s'=>'Value'),$_smarty_tpl);?>
</label>
		<div class="margin-form">
			<?php if (($_smarty_tpl->tpl_vars['currency']->value->format%2)){?>
				<span id="discount_currency_sign" style="display: none;"><?php echo $_smarty_tpl->tpl_vars['currency']->value->sign;?>
</span>
			<?php }?>
			<input type="text" name="discount_value" size="3" />
			<?php if (!($_smarty_tpl->tpl_vars['currency']->value->format%2)){?>
				<span id="discount_currency_sign" style="display: none;"><?php echo $_smarty_tpl->tpl_vars['currency']->value->sign;?>
</span>
			<?php }?>
			<span id="discount_percent_symbol">%</span>
			<p class="preference_description" id="discount_value_help" style="width: 95%;display: none;">
				<?php echo smartyTranslate(array('s'=>'This value must be taxes included.'),$_smarty_tpl);?>

			</p>
		</div>
	</div>

	<?php if ($_smarty_tpl->tpl_vars['order']->value->hasInvoice()){?>
	<label><?php echo smartyTranslate(array('s'=>'Invoice'),$_smarty_tpl);?>
</label>
	<div class="margin-form">
		<select name="discount_invoice">
			<?php  $_smarty_tpl->tpl_vars['invoice'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['invoice']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['invoices_collection']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['invoice']->key => $_smarty_tpl->tpl_vars['invoice']->value){
$_smarty_tpl->tpl_vars['invoice']->_loop = true;
?>
				<option value="<?php echo $_smarty_tpl->tpl_vars['invoice']->value->id;?>
" selected="selected"><?php echo $_smarty_tpl->tpl_vars['invoice']->value->getInvoiceNumberFormatted($_smarty_tpl->tpl_vars['current_id_lang']->value);?>
 - <?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['invoice']->value->total_paid_tax_incl,'currency'=>$_smarty_tpl->tpl_vars['order']->value->id_currency),$_smarty_tpl);?>
</option>
			<?php } ?>
		</select><br />
		<input type="checkbox" name="discount_all_invoices" id="discount_all_invoices" value="1" /> <label class="t" for="discount_all_invoices"><?php echo smartyTranslate(array('s'=>'Apply on all invoices'),$_smarty_tpl);?>
</label>
		<p class="preference_description" style="width: 95%">
			<?php echo smartyTranslate(array('s'=>'If you select to create this discount for all invoices, one discount will be created per order invoice.'),$_smarty_tpl);?>

		</p>
	</div>
	<?php }?>

	<p class="center">
		<input class="button" type="submit" name="submitNewVoucher" value="<?php echo smartyTranslate(array('s'=>'Add'),$_smarty_tpl);?>
" />&nbsp;
		<a href="#" id="cancel_add_voucher"><?php echo smartyTranslate(array('s'=>'Cancel'),$_smarty_tpl);?>
</a>
	</p>

<?php }} ?>