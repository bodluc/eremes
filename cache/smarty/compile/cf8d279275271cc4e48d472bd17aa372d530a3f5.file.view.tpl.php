<?php /* Smarty version Smarty-3.1.8, created on 2012-12-22 12:09:50
         compiled from "/home/bodo/public_html/dev/admin-dev/themes/default/template/controllers/manufacturers/helpers/view/view.tpl" */ ?>
<?php /*%%SmartyHeaderCode:152702138550d594fe0ae4e3-98317716%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'cf8d279275271cc4e48d472bd17aa372d530a3f5' => 
    array (
      0 => '/home/bodo/public_html/dev/admin-dev/themes/default/template/controllers/manufacturers/helpers/view/view.tpl',
      1 => 1347489972,
      2 => 'file',
    ),
    '52bf8c8ed6d7d94b92322c74a1466b6cf1ce08a0' => 
    array (
      0 => '/home/bodo/public_html/dev/admin-dev/themes/default/template/helpers/view/view.tpl',
      1 => 1347489972,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '152702138550d594fe0ae4e3-98317716',
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
  'unifunc' => 'content_50d594fe7c0d86_45737620',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50d594fe7c0d86_45737620')) {function content_50d594fe7c0d86_45737620($_smarty_tpl) {?>

<?php if ($_smarty_tpl->tpl_vars['show_toolbar']->value){?>
	<?php echo $_smarty_tpl->getSubTemplate ("toolbar.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('toolbar_btn'=>$_smarty_tpl->tpl_vars['toolbar_btn']->value,'toolbar_scroll'=>$_smarty_tpl->tpl_vars['toolbar_scroll']->value,'title'=>$_smarty_tpl->tpl_vars['title']->value), 0);?>

<?php }?>

<div class="leadin"></div>



	<h2><?php echo $_smarty_tpl->tpl_vars['manufacturer']->value->name;?>
</h2>

	<h3><?php echo smartyTranslate(array('s'=>'Total addresses:'),$_smarty_tpl);?>
 <?php echo count($_smarty_tpl->tpl_vars['addresses']->value);?>
</h3>
	<hr />

	<?php if (!count($_smarty_tpl->tpl_vars['addresses']->value)){?>
		<?php echo smartyTranslate(array('s'=>'No address for this manufacturer'),$_smarty_tpl);?>

	<?php }else{ ?>
		<?php  $_smarty_tpl->tpl_vars['addresse'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['addresse']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['addresses']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['addresse']->key => $_smarty_tpl->tpl_vars['addresse']->value){
$_smarty_tpl->tpl_vars['addresse']->_loop = true;
?>
			<table border="0" cellpadding="0" cellspacing="0" class="table" style="width: 600px;">
				<tr>
					<th><strong><?php echo $_smarty_tpl->tpl_vars['addresse']->value['firstname'];?>
 <?php echo $_smarty_tpl->tpl_vars['addresse']->value['lastname'];?>
</strong></th>
				</tr>
				<tr>
					<td>
						<div style="padding:5px; float:left; width:350px;">
							<?php echo $_smarty_tpl->tpl_vars['addresse']->value['address1'];?>
<br />
							<?php if ($_smarty_tpl->tpl_vars['addresse']->value['address2']){?><?php echo $_smarty_tpl->tpl_vars['addresse']->value['address2'];?>
<br /><?php }?>
							<?php echo $_smarty_tpl->tpl_vars['addresse']->value['postcode'];?>
 <?php echo $_smarty_tpl->tpl_vars['addresse']->value['city'];?>
<br />
							<?php if ($_smarty_tpl->tpl_vars['addresse']->value['state']){?><?php echo $_smarty_tpl->tpl_vars['addresse']->value['state'];?>
<br /><?php }?>
							<b><?php echo $_smarty_tpl->tpl_vars['addresse']->value['country'];?>
</b><br />
						</div>
						<div style="padding:5px; float:left;">
							<?php if ($_smarty_tpl->tpl_vars['addresse']->value['phone']){?><?php echo $_smarty_tpl->tpl_vars['addresse']->value['phone'];?>
<br /><?php }?>
							<?php if ($_smarty_tpl->tpl_vars['addresse']->value['phone_mobile']){?><?php echo $_smarty_tpl->tpl_vars['addresse']->value['phone_mobile'];?>
<br /><?php }?>
						</div>
						<?php if ($_smarty_tpl->tpl_vars['addresse']->value['other']){?><div style="padding:5px; clear:both;"><br /><i><?php echo $_smarty_tpl->tpl_vars['addresse']->value['other'];?>
</i></div><?php }?>
					</td>
				</tr>
			</table><br />
		<?php } ?>
	<?php }?>

	<h3><?php echo smartyTranslate(array('s'=>'Total products:'),$_smarty_tpl);?>
 <?php echo count($_smarty_tpl->tpl_vars['products']->value);?>
</h3>
	<?php  $_smarty_tpl->tpl_vars['product'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['product']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['products']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['product']->key => $_smarty_tpl->tpl_vars['product']->value){
$_smarty_tpl->tpl_vars['product']->_loop = true;
?>
		<hr />
		<?php if (!$_smarty_tpl->tpl_vars['product']->value->hasAttributes()){?>
			<div style="float:right;">
				<a href="?tab=AdminProducts&id_product=<?php echo $_smarty_tpl->tpl_vars['product']->value->id;?>
&updateproduct&token=<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminProducts'),$_smarty_tpl);?>
" class="button"><?php echo smartyTranslate(array('s'=>'Edit'),$_smarty_tpl);?>
</a>
				<a href="?tab=AdminProducts&id_product=<?php echo $_smarty_tpl->tpl_vars['product']->value->id;?>
&deleteproduct&token=<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminProducts'),$_smarty_tpl);?>
" class="button" onclick="return confirm('<?php echo smartyTranslate(array('s'=>'Delete item #'),$_smarty_tpl);?>
<?php echo $_smarty_tpl->tpl_vars['product']->value->id;?>
 ?');"><?php echo smartyTranslate(array('s'=>'Delete'),$_smarty_tpl);?>
</a>
			</div>
			<br/><br/>
			<table border="0" cellpadding="0" cellspacing="0" class="table" style="width:100%;">
				<tr>
					<th height="39"><?php echo $_smarty_tpl->tpl_vars['product']->value->name;?>
</th>
					<?php if (!empty($_smarty_tpl->tpl_vars['product']->value->reference)){?><th width="150"><?php echo smartyTranslate(array('s'=>'Ref:'),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['product']->value->reference;?>
</th><?php }?>
					<?php if (!empty($_smarty_tpl->tpl_vars['product']->value->ean13)){?><th width="120"><?php echo smartyTranslate(array('s'=>'EAN13:'),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['product']->value->ean13;?>
</th><?php }?>
					<?php if (!empty($_smarty_tpl->tpl_vars['product']->value->upc)){?><th width="120"><?php echo smartyTranslate(array('s'=>'UPC:'),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['product']->value->upc;?>
</th><?php }?>
					<?php if ($_smarty_tpl->tpl_vars['stock_management']->value){?><th class="right" width="50"><?php echo smartyTranslate(array('s'=>'Qty:'),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['product']->value->quantity;?>
</th><?php }?>
				</tr>
			</table>
		<?php }else{ ?>
			<div style="float:right;">
				<a href="?tab=AdminProducts&id_product=<?php echo $_smarty_tpl->tpl_vars['product']->value->id;?>
&updateproduct&token=<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminProducts'),$_smarty_tpl);?>
" class="button"><?php echo smartyTranslate(array('s'=>'Edit'),$_smarty_tpl);?>
</a>
				<a href="?tab=AdminProducts&id_product=<?php echo $_smarty_tpl->tpl_vars['product']->value->id;?>
&deleteproduct&token=<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminProducts'),$_smarty_tpl);?>
" class="button" onclick="return confirm('<?php echo smartyTranslate(array('s'=>'Delete item #'),$_smarty_tpl);?>
<?php echo $_smarty_tpl->tpl_vars['product']->value->id;?>
 ?');"><?php echo smartyTranslate(array('s'=>'Delete'),$_smarty_tpl);?>
</a>
			</div>
			<h3><a href="?tab=AdminProducts&id_product=<?php echo $_smarty_tpl->tpl_vars['product']->value->id;?>
&updateproduct&token=<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminProducts'),$_smarty_tpl);?>
"><?php echo $_smarty_tpl->tpl_vars['product']->value->name;?>
</a></h3>
			<table border="0" cellpadding="0" cellspacing="0" class="table" style="width:100%;">
				<tr>
					<th height="39"><?php echo smartyTranslate(array('s'=>'Attribute name'),$_smarty_tpl);?>
</th>
					<th width="80"><?php echo smartyTranslate(array('s'=>'Reference'),$_smarty_tpl);?>
</th>
					<th width="80"><?php echo smartyTranslate(array('s'=>'EAN13'),$_smarty_tpl);?>
</th>
					<th width="80"><?php echo smartyTranslate(array('s'=>'UPC'),$_smarty_tpl);?>
</th>
					<?php if ($_smarty_tpl->tpl_vars['stock_management']->value&&$_smarty_tpl->tpl_vars['shopContext']->value!=Shop::CONTEXT_ALL){?><th class="right" width="150"><?php echo smartyTranslate(array('s'=>'Available Quantity'),$_smarty_tpl);?>
</th><?php }?>
				</tr>
				<?php  $_smarty_tpl->tpl_vars['product_attribute'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['product_attribute']->_loop = false;
 $_smarty_tpl->tpl_vars['id_product_attribute'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['product']->value->combination; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['product_attribute']->key => $_smarty_tpl->tpl_vars['product_attribute']->value){
$_smarty_tpl->tpl_vars['product_attribute']->_loop = true;
 $_smarty_tpl->tpl_vars['id_product_attribute']->value = $_smarty_tpl->tpl_vars['product_attribute']->key;
?>
					<tr <?php if ($_smarty_tpl->tpl_vars['id_product_attribute']->value%2){?>class="alt_row"<?php }?> >
						<td><?php echo $_smarty_tpl->tpl_vars['product_attribute']->value['attributes'];?>
</td>
						<td><?php echo $_smarty_tpl->tpl_vars['product_attribute']->value['reference'];?>
</td>
						<td><?php echo $_smarty_tpl->tpl_vars['product_attribute']->value['ean13'];?>
</td>
						<td><?php echo $_smarty_tpl->tpl_vars['product_attribute']->value['upc'];?>
</td>
						<?php if ($_smarty_tpl->tpl_vars['stock_management']->value&&$_smarty_tpl->tpl_vars['shopContext']->value!=Shop::CONTEXT_ALL){?><td class="right"><?php echo $_smarty_tpl->tpl_vars['product_attribute']->value['quantity'];?>
</td><?php }?>
					</tr>
				<?php } ?>
			</table>
		<?php }?>
	<?php } ?>


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