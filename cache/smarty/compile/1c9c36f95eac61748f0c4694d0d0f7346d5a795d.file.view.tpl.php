<?php /* Smarty version Smarty-3.1.8, created on 2013-02-04 22:04:18
         compiled from "/home/bodo/public_html/dev/admin-dev/themes/default/template/controllers/customer_threads/helpers/view/view.tpl" */ ?>
<?php /*%%SmartyHeaderCode:214698027251102252adac18-13618605%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1c9c36f95eac61748f0c4694d0d0f7346d5a795d' => 
    array (
      0 => '/home/bodo/public_html/dev/admin-dev/themes/default/template/controllers/customer_threads/helpers/view/view.tpl',
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
  'nocache_hash' => '214698027251102252adac18-13618605',
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
  'unifunc' => 'content_51102252f23ab0_33622836',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51102252f23ab0_33622836')) {function content_51102252f23ab0_33622836($_smarty_tpl) {?>

<?php if ($_smarty_tpl->tpl_vars['show_toolbar']->value){?>
	<?php echo $_smarty_tpl->getSubTemplate ("toolbar.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('toolbar_btn'=>$_smarty_tpl->tpl_vars['toolbar_btn']->value,'toolbar_scroll'=>$_smarty_tpl->tpl_vars['toolbar_scroll']->value,'title'=>$_smarty_tpl->tpl_vars['title']->value), 0);?>

<?php }?>

<div class="leadin"></div>


	<form action="<?php echo $_smarty_tpl->tpl_vars['current']->value;?>
&token=<?php echo $_smarty_tpl->tpl_vars['token']->value;?>
&viewcustomer_thread&id_customer_thread=<?php echo $_smarty_tpl->tpl_vars['id_customer_thread']->value;?>
" method="post" enctype="multipart/form-data">
		<fieldset>

			<div id="ChangeStatus">
				<select onchange="quickSelect(this);">
					<option value="0"><?php echo smartyTranslate(array('s'=>'Change status of message:'),$_smarty_tpl);?>
</option>
					<?php  $_smarty_tpl->tpl_vars['action'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['action']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['actions']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['action']->key => $_smarty_tpl->tpl_vars['action']->value){
$_smarty_tpl->tpl_vars['action']->_loop = true;
?>
						<option value="<?php echo $_smarty_tpl->tpl_vars['action']->value['href'];?>
">&gt; <?php echo $_smarty_tpl->tpl_vars['action']->value['name'];?>
</option>
					<?php } ?>
				</select>
			</div>

			<p>
				<img src="../img/admin/email_go.png" alt="" style="vertical-align: middle;" /> 
				<?php echo smartyTranslate(array('s'=>'Forward this discussion to an employee:'),$_smarty_tpl);?>

				<select name="id_employee_forward" style="vertical-align: middle;">
					<option value="-1"><?php echo smartyTranslate(array('s'=>'-- Choose --'),$_smarty_tpl);?>
</option>
					<?php  $_smarty_tpl->tpl_vars['employee'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['employee']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['employees']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['employee']->key => $_smarty_tpl->tpl_vars['employee']->value){
$_smarty_tpl->tpl_vars['employee']->_loop = true;
?>
						<option value="<?php echo $_smarty_tpl->tpl_vars['employee']->value['id_employee'];?>
"> <?php echo Tools::substr($_smarty_tpl->tpl_vars['employee']->value['firstname'],0,1);?>
. <?php echo $_smarty_tpl->tpl_vars['employee']->value['lastname'];?>
</option>
					<?php } ?>
					<option value="0"><?php echo smartyTranslate(array('s'=>'Someone else'),$_smarty_tpl);?>
</option>
				</select>
			</p>

			<div id="message_forward_email" style="display:none">
				<b><?php echo smartyTranslate(array('s'=>'E-mail'),$_smarty_tpl);?>
</b> <input type="text" name="email" />
			</div>

			<div id="message_forward" style="display:none;margin-bottom:10px">
				<textarea name="message_forward" style="width:500px;height:80px;margin-top:15px;"><?php echo smartyTranslate(array('s'=>'You can add a comment here.'),$_smarty_tpl);?>
</textarea><br />
				<input type="Submit" name="submitForward" class="button" value="<?php echo smartyTranslate(array('s'=>'Forward this discussion'),$_smarty_tpl);?>
" style="margin-top: 10px;" />
			</div>

		</fieldset>
	</form>
	<div class="clear">&nbsp;</div>

	<?php if ($_smarty_tpl->tpl_vars['thread']->value->id_customer){?>

		<div style="float:right;margin-left:20px;">
		<?php if ($_smarty_tpl->tpl_vars['orders']->value&&count($_smarty_tpl->tpl_vars['orders']->value)){?>
			<?php if ($_smarty_tpl->tpl_vars['count_ok']->value){?>
				<div>
					<h2><?php echo smartyTranslate(array('s'=>'Orders'),$_smarty_tpl);?>
</h2>
					<table cellspacing="0" cellpadding="0" class="table float">
						<tr>
							<th class="center"><?php echo smartyTranslate(array('s'=>'ID'),$_smarty_tpl);?>
</th>
							<th class="center"><?php echo smartyTranslate(array('s'=>'Date'),$_smarty_tpl);?>
</th>
							<th class="center"><?php echo smartyTranslate(array('s'=>'Products'),$_smarty_tpl);?>
</th>
							<th class="center"><?php echo smartyTranslate(array('s'=>'Total paid'),$_smarty_tpl);?>
</th>
							<th class="center"><?php echo smartyTranslate(array('s'=>'Payment'),$_smarty_tpl);?>
</th>
							<th class="center"><?php echo smartyTranslate(array('s'=>'State'),$_smarty_tpl);?>
</th>
							<th class="center"><?php echo smartyTranslate(array('s'=>'Actions'),$_smarty_tpl);?>
</th>
						</tr>
						<?php $_smarty_tpl->tpl_vars['irow'] = new Smarty_variable(0, null, 0);?>
						<?php  $_smarty_tpl->tpl_vars['order'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['order']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['orders_ok']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['order']->key => $_smarty_tpl->tpl_vars['order']->value){
$_smarty_tpl->tpl_vars['order']->_loop = true;
?>
							<tr <?php if ($_smarty_tpl->tpl_vars['irow']->value++%2){?>class="alt_row"<?php }?> style="cursor: pointer" 
											onclick="document.location='?tab=AdminOrders&id_order=<?php echo $_smarty_tpl->tpl_vars['order']->value['id_order'];?>
&vieworder&token=<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminOrders'),$_smarty_tpl);?>
">
								<td class="center"><?php echo $_smarty_tpl->tpl_vars['order']->value['id_order'];?>
</td>
								<td><?php echo $_smarty_tpl->tpl_vars['order']->value['date_add'];?>
</td>
								<td align="right"><?php echo $_smarty_tpl->tpl_vars['order']->value['nb_products'];?>
</td>
								<td align="right"><?php echo $_smarty_tpl->tpl_vars['order']->value['total_paid_real'];?>
</td>
								<td><?php echo $_smarty_tpl->tpl_vars['order']->value['payment'];?>
</td>
								<td><?php echo $_smarty_tpl->tpl_vars['order']->value['order_state'];?>
</td>
								<td align="center">
									<a href="?tab=AdminOrders&id_order=<?php echo $_smarty_tpl->tpl_vars['order']->value['id_order'];?>
&vieworder&token=<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminOrders'),$_smarty_tpl);?>
">
										<img src="../img/admin/details.gif" />
									</a>
								</td>
							</tr>
						<?php } ?>
					</table>
					<h3 style="color:green;font-weight:700;margin-top:10px">
						<?php echo smartyTranslate(array('s'=>'Validated Orders:'),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['count_ok']->value;?>
 <?php echo smartyTranslate(array('s'=>'for'),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['total_ok']->value;?>

					</h3>
				</div>
			<?php }?>
		<?php }?>
		<?php if ($_smarty_tpl->tpl_vars['products']->value&&count($_smarty_tpl->tpl_vars['products']->value)){?>
			<div>
				<h2><?php echo smartyTranslate(array('s'=>'Products'),$_smarty_tpl);?>
</h2>
				<table cellspacing="0" cellpadding="0" class="table">
					<tr>
						<th class="center"><?php echo smartyTranslate(array('s'=>'Date'),$_smarty_tpl);?>
</th>
						<th class="center"><?php echo smartyTranslate(array('s'=>'ID'),$_smarty_tpl);?>
</th>
						<th class="center"><?php echo smartyTranslate(array('s'=>'Name'),$_smarty_tpl);?>
</th>
						<th class="center"><?php echo smartyTranslate(array('s'=>'Quantity'),$_smarty_tpl);?>
</th>
						<th class="center"><?php echo smartyTranslate(array('s'=>'Actions'),$_smarty_tpl);?>
</th>
					</tr>
					<?php $_smarty_tpl->tpl_vars['irow'] = new Smarty_variable(0, null, 0);?>
					<?php  $_smarty_tpl->tpl_vars['product'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['product']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['products']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['product']->key => $_smarty_tpl->tpl_vars['product']->value){
$_smarty_tpl->tpl_vars['product']->_loop = true;
?>
						<tr <?php if ($_smarty_tpl->tpl_vars['irow']->value++%2){?>class="alt_row"<?php }?> style="cursor: pointer" 
							onclick="document.location = '?tab=AdminOrders&id_order=<?php echo $_smarty_tpl->tpl_vars['product']->value['id_order'];?>
&vieworder&token=<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminOrders'),$_smarty_tpl);?>
'">
							<td><?php echo $_smarty_tpl->tpl_vars['product']->value['date_add'];?>
</td>
							<td><?php echo $_smarty_tpl->tpl_vars['product']->value['product_id'];?>
</td>
							<td><?php echo $_smarty_tpl->tpl_vars['product']->value['product_name'];?>
</td>
							<td align="right"><?php echo $_smarty_tpl->tpl_vars['product']->value['product_quantity'];?>
</td>
							<td align="center">
								<a href="?tab=AdminOrders&id_order=<?php echo $_smarty_tpl->tpl_vars['product']->value['id_order'];?>
&vieworder&token=<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminOrders'),$_smarty_tpl);?>
">
									<img src="../img/admin/details.gif" />
								</a>
							</td>
						</tr>
					<?php } ?>
				</table>
			</div>
		<?php }?>
		</div>
	<?php }?>

	<div style="margin-top:10px">
		<?php  $_smarty_tpl->tpl_vars['message'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['message']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['messages']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['message']->key => $_smarty_tpl->tpl_vars['message']->value){
$_smarty_tpl->tpl_vars['message']->_loop = true;
?>
			<?php echo $_smarty_tpl->tpl_vars['message']->value;?>

		<?php } ?>
	</div>



	
	<script type="text/javascript">
		var timer;
		$(document).ready(function(){
			$('select[name=id_employee_forward]').change(function(){
				if ($(this).val() >= 0)
					$('#message_forward').show(400);
				else
					$('#message_forward').hide(200);
				if ($(this).val() == 0)
					$('#message_forward_email').show(200);
				else
					$('#message_forward_email').hide(200);
			});
			$('teaxtrea[name=message_forward]').click(function(){
				if($(this).val() == '<?php echo smartyTranslate(array('s'=>'You can add a comment here.'),$_smarty_tpl);?>
')
				{
					$(this).val('');
				}
			});
			timer = setInterval("markAsRead()", 3000);
		});
		
		
		function markAsRead()
		{
			$.ajax({
				type: 'POST',
				url: 'ajax-tab.php',
				async: true,
				dataType: 'json',
				data: {
					controller: 'AdminCustomerThreads',
					action: 'markAsRead',
					token : '<?php echo $_smarty_tpl->tpl_vars['token']->value;?>
',
					id_thread: <?php echo $_smarty_tpl->tpl_vars['id_customer_thread']->value;?>

				}
			});
			clearInterval(timer);
			timer = null;
   		}
	</script>



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