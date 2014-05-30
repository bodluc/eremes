<?php /* Smarty version Smarty-3.1.8, created on 2012-12-27 01:48:19
         compiled from "/home/bodo/public_html/dev/admin-dev/themes/default/template/controllers/payment/helpers/view/view.tpl" */ ?>
<?php /*%%SmartyHeaderCode:76258281150db9ad36cd775-98085435%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '2522b07d697cd209e7b4207f529e424f1f0c36c3' => 
    array (
      0 => '/home/bodo/public_html/dev/admin-dev/themes/default/template/controllers/payment/helpers/view/view.tpl',
      1 => 1347489974,
      2 => 'file',
    ),
    '52bf8c8ed6d7d94b92322c74a1466b6cf1ce08a0' => 
    array (
      0 => '/home/bodo/public_html/dev/admin-dev/themes/default/template/helpers/view/view.tpl',
      1 => 1347489972,
      2 => 'file',
    ),
    'd15d3ff8c37623d62e3dd2da14003fba2fa62024' => 
    array (
      0 => '/home/bodo/public_html/dev/admin-dev/themes/default/template/controllers/payment/restrictions.tpl',
      1 => 1347489974,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '76258281150db9ad36cd775-98085435',
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
  'unifunc' => 'content_50db9ad3e7baf6_14394206',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50db9ad3e7baf6_14394206')) {function content_50db9ad3e7baf6_14394206($_smarty_tpl) {?>

<?php if ($_smarty_tpl->tpl_vars['show_toolbar']->value){?>
	<?php echo $_smarty_tpl->getSubTemplate ("toolbar.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('toolbar_btn'=>$_smarty_tpl->tpl_vars['toolbar_btn']->value,'toolbar_scroll'=>$_smarty_tpl->tpl_vars['toolbar_scroll']->value,'title'=>$_smarty_tpl->tpl_vars['title']->value), 0);?>

<?php }?>

<div class="leadin"></div>


<?php if (!$_smarty_tpl->tpl_vars['shop_context']->value){?>
	<div class="warn"><?php echo smartyTranslate(array('s'=>'You have more than one shop. You need to select one to configure payment.'),$_smarty_tpl);?>
</div>
<?php }else{ ?>
		<h2 class="space"><?php echo smartyTranslate(array('s'=>'Payment modules list'),$_smarty_tpl);?>
</h2>
		<?php if (isset($_smarty_tpl->tpl_vars['url_modules']->value)){?>
			<input type="button" class="button" onclick="document.location='<?php echo $_smarty_tpl->tpl_vars['url_modules']->value;?>
'" value="<?php echo smartyTranslate(array('s'=>'Click to see the list of payment modules.'),$_smarty_tpl);?>
" /><br>
		<?php }?>
	
		<br />
	
		<?php if ($_smarty_tpl->tpl_vars['display_restrictions']->value){?>
			<br /><h2 class="space"><?php echo smartyTranslate(array('s'=>'Payment module restrictions'),$_smarty_tpl);?>
</h2>
			<?php  $_smarty_tpl->tpl_vars['list'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['list']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['lists']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['list']->key => $_smarty_tpl->tpl_vars['list']->value){
$_smarty_tpl->tpl_vars['list']->_loop = true;
?>
				<?php /*  Call merged included template "controllers/payment/restrictions.tpl" */
$_tpl_stack[] = $_smarty_tpl;
 $_smarty_tpl = $_smarty_tpl->setupInlineSubTemplate('controllers/payment/restrictions.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0, '76258281150db9ad36cd775-98085435');
content_50db9ad3be9a56_58760352($_smarty_tpl);
$_smarty_tpl = array_pop($_tpl_stack); /*  End of included template "controllers/payment/restrictions.tpl" */?>
				<br />
			<?php } ?>
		<?php }else{ ?>
			<br />
			<div class='warn'><?php echo smartyTranslate(array('s'=>'No payment module installed'),$_smarty_tpl);?>
</div>
		<?php }?>
<?php }?>


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
<?php }} ?><?php /* Smarty version Smarty-3.1.8, created on 2012-12-27 01:48:19
         compiled from "/home/bodo/public_html/dev/admin-dev/themes/default/template/controllers/payment/restrictions.tpl" */ ?>
<?php if ($_valid && !is_callable('content_50db9ad3be9a56_58760352')) {function content_50db9ad3be9a56_58760352($_smarty_tpl) {?><?php if (!is_callable('smarty_function_cycle')) include '/home/bodo/public_html/dev/tools/smarty/plugins/function.cycle.php';
?>

<form action="<?php echo $_smarty_tpl->tpl_vars['url_submit']->value;?>
" method="post" id="form_<?php echo $_smarty_tpl->tpl_vars['list']->value['name_id'];?>
">
	<fieldset>
		<legend><img src="../img/admin/<?php echo $_smarty_tpl->tpl_vars['list']->value['icon'];?>
.gif" /><?php echo $_smarty_tpl->tpl_vars['list']->value['title'];?>
</legend>
		<p><?php echo $_smarty_tpl->tpl_vars['list']->value['desc'];?>
<p>
		<table cellpadding="0" cellspacing="0" class="table">
			<tr>
				<th style="width: 200px"><?php echo $_smarty_tpl->tpl_vars['list']->value['title'];?>
</th>
				<?php  $_smarty_tpl->tpl_vars['module'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['module']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['payment_modules']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['module']->key => $_smarty_tpl->tpl_vars['module']->value){
$_smarty_tpl->tpl_vars['module']->_loop = true;
?>
					<?php if ($_smarty_tpl->tpl_vars['module']->value->active){?>
						<th>
							<?php if ($_smarty_tpl->tpl_vars['list']->value['name_id']!='currency'||$_smarty_tpl->tpl_vars['module']->value->currencies_mode=='checkbox'){?>
								<input type="hidden" id="checkedBox_<?php echo $_smarty_tpl->tpl_vars['list']->value['name_id'];?>
_<?php echo $_smarty_tpl->tpl_vars['module']->value->name;?>
" value="checked">
								<a href="javascript:checkPaymentBoxes('<?php echo $_smarty_tpl->tpl_vars['list']->value['name_id'];?>
', '<?php echo $_smarty_tpl->tpl_vars['module']->value->name;?>
')" style="text-decoration:none;">
							<?php }?>
							&nbsp;<img src="<?php echo $_smarty_tpl->tpl_vars['ps_base_uri']->value;?>
modules/<?php echo $_smarty_tpl->tpl_vars['module']->value->name;?>
/logo.gif" alt="<?php echo $_smarty_tpl->tpl_vars['module']->value->name;?>
" title="<?php echo $_smarty_tpl->tpl_vars['module']->value->displayName;?>
" />
							<?php if ($_smarty_tpl->tpl_vars['list']->value['name_id']!='currency'||$_smarty_tpl->tpl_vars['module']->value->currencies_mode=='checkbox'){?>
								</a>
							<?php }?>
						</th>
					<?php }?>
				<?php } ?>
			</tr>
			<?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['item']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['list']->value['items']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value){
$_smarty_tpl->tpl_vars['item']->_loop = true;
?>
				<tr class="<?php echo smarty_function_cycle(array('values'=>",alt_row"),$_smarty_tpl);?>
">
					<td><?php echo $_smarty_tpl->tpl_vars['item']->value['name'];?>
</td>
				<?php  $_smarty_tpl->tpl_vars['module'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['module']->_loop = false;
 $_smarty_tpl->tpl_vars['key_module'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['payment_modules']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['module']->key => $_smarty_tpl->tpl_vars['module']->value){
$_smarty_tpl->tpl_vars['module']->_loop = true;
 $_smarty_tpl->tpl_vars['key_module']->value = $_smarty_tpl->tpl_vars['module']->key;
?>
					<?php if ($_smarty_tpl->tpl_vars['module']->value->active){?>
						<td style="text-align: center">
							<?php $_smarty_tpl->tpl_vars['type'] = new Smarty_variable('null', null, 0);?>
							<?php if (!$_smarty_tpl->tpl_vars['item']->value['check_list'][$_smarty_tpl->tpl_vars['key_module']->value]){?>
								
							<?php }elseif($_smarty_tpl->tpl_vars['list']->value['name_id']==='currency'){?>
								<?php if ($_smarty_tpl->tpl_vars['module']->value->currencies&&$_smarty_tpl->tpl_vars['module']->value->currencies_mode=='checkbox'){?>
									<?php $_smarty_tpl->tpl_vars['type'] = new Smarty_variable('checkbox', null, 0);?>
								<?php }elseif($_smarty_tpl->tpl_vars['module']->value->currencies&&$_smarty_tpl->tpl_vars['module']->value->currencies_mode=='radio'){?>
									<?php $_smarty_tpl->tpl_vars['type'] = new Smarty_variable('radio', null, 0);?>
								<?php }?>
							<?php }else{ ?>
								<?php $_smarty_tpl->tpl_vars['type'] = new Smarty_variable('checkbox', null, 0);?>
							<?php }?>
							<?php if ($_smarty_tpl->tpl_vars['type']->value!='null'){?>
								<input type="checkbox" name="<?php echo $_smarty_tpl->tpl_vars['module']->value->name;?>
_<?php echo $_smarty_tpl->tpl_vars['list']->value['name_id'];?>
[]" value="<?php echo $_smarty_tpl->tpl_vars['item']->value[$_smarty_tpl->tpl_vars['list']->value['identifier']];?>
"
									<?php if ($_smarty_tpl->tpl_vars['item']->value['check_list'][$_smarty_tpl->tpl_vars['key_module']->value]=='checked'){?>checked="checked"<?php }?> 
								/>
							<?php }else{ ?>
								--
							<?php }?>
						</td>
					<?php }?>
				<?php } ?>
				</tr>
			<?php } ?>
			<?php if ($_smarty_tpl->tpl_vars['list']->value['name_id']==='currency'){?>
				<tr class="<?php echo smarty_function_cycle(array('values'=>",alt_row"),$_smarty_tpl);?>
">
					<td><?php echo smartyTranslate(array('s'=>'Customer currency'),$_smarty_tpl);?>
</td>
					<?php  $_smarty_tpl->tpl_vars['module'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['module']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['payment_modules']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['module']->key => $_smarty_tpl->tpl_vars['module']->value){
$_smarty_tpl->tpl_vars['module']->_loop = true;
?>
						<?php if ($_smarty_tpl->tpl_vars['module']->value->active){?>
							<td style="text-align: center"><?php if ($_smarty_tpl->tpl_vars['module']->value->currencies&&$_smarty_tpl->tpl_vars['module']->value->currencies_mode=='radio'){?><input type="radio" name="<?php echo $_smarty_tpl->tpl_vars['module']->value->name;?>
_<?php echo $_smarty_tpl->tpl_vars['list']->value['name_id'];?>
[]" value="-1"<?php if (in_array(-1,$_smarty_tpl->tpl_vars['module']->value->{$_smarty_tpl->tpl_vars['list']->value['name_id']})){?> checked="checked"<?php }?> /><?php }else{ ?>--<?php }?></td>
						<?php }?>
					<?php } ?>
				</tr>
				<tr class="<?php echo smarty_function_cycle(array('values'=>",alt_row"),$_smarty_tpl);?>
">
					<td><?php echo smartyTranslate(array('s'=>'Shop default currency'),$_smarty_tpl);?>
</td>
					<?php  $_smarty_tpl->tpl_vars['module'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['module']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['payment_modules']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['module']->key => $_smarty_tpl->tpl_vars['module']->value){
$_smarty_tpl->tpl_vars['module']->_loop = true;
?>
						<?php if ($_smarty_tpl->tpl_vars['module']->value->active){?>
							<td style="text-align: center"><?php if ($_smarty_tpl->tpl_vars['module']->value->currencies&&$_smarty_tpl->tpl_vars['module']->value->currencies_mode=='radio'){?><input type="radio" name="<?php echo $_smarty_tpl->tpl_vars['module']->value->name;?>
_<?php echo $_smarty_tpl->tpl_vars['list']->value['name_id'];?>
[]" value="-2"<?php if (in_array(-2,$_smarty_tpl->tpl_vars['module']->value->{$_smarty_tpl->tpl_vars['list']->value['name_id']})){?> checked="checked"<?php }?> /><?php }else{ ?>--<?php }?></td>
						<?php }?>
					<?php } ?>
				</tr>
			<?php }?>
		</table>
		<div><input type="submit" class="button space" name="submitModule<?php echo $_smarty_tpl->tpl_vars['list']->value['name_id'];?>
" value="<?php echo smartyTranslate(array('s'=>'Save restrictions'),$_smarty_tpl);?>
" /></div>
	</fieldset>
</form><?php }} ?>