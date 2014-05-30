<?php /* Smarty version Smarty-3.1.8, created on 2012-11-19 08:58:31
         compiled from "/home/bodo/public_html/dev/admin-dev/themes/default/template/controllers/customers/helpers/view/view.tpl" */ ?>
<?php /*%%SmartyHeaderCode:55500444750a9e6a7c15946-96663389%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'efed98070d66c249e68a7e2b5e1d7eb1940c35fa' => 
    array (
      0 => '/home/bodo/public_html/dev/admin-dev/themes/default/template/controllers/customers/helpers/view/view.tpl',
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
  'nocache_hash' => '55500444750a9e6a7c15946-96663389',
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
  'unifunc' => 'content_50a9e6a8770d11_75055300',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50a9e6a8770d11_75055300')) {function content_50a9e6a8770d11_75055300($_smarty_tpl) {?>

<?php if ($_smarty_tpl->tpl_vars['show_toolbar']->value){?>
	<?php echo $_smarty_tpl->getSubTemplate ("toolbar.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('toolbar_btn'=>$_smarty_tpl->tpl_vars['toolbar_btn']->value,'toolbar_scroll'=>$_smarty_tpl->tpl_vars['toolbar_scroll']->value,'title'=>$_smarty_tpl->tpl_vars['title']->value), 0);?>

<?php }?>

<div class="leadin"></div>


	<script type="text/javascript">
		function saveCustomerNote()
		{
			$('#note_feedback').html('<img src="../img/loader.gif" alt="" />').show();
			var noteContent = $('#noteContent').val();
	
			$.ajax({
				type: "POST",
				url: "index.php",
				data: "token=<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminCustomers'),$_smarty_tpl);?>
&tab=AdminCustomers&ajax=1&action=updateCustomerNote&id_customer=<?php echo $_smarty_tpl->tpl_vars['customer']->value->id;?>
&note="+noteContent,
				async : true,
				success: function(r) {
					$('#note_feedback').html('').hide();
					if (r == 'ok')
					{
						$('#note_feedback').html("<b style='color:green'><?php echo smartyTranslate(array('s'=>'Your note has been saved'),$_smarty_tpl);?>
</b>").fadeIn(400);
						$('#submitCustomerNote').attr('disabled', true);
					}
					else if (r == 'error:validation')
						$('#note_feedback').html("<b style='color:red'>(<?php echo smartyTranslate(array('s'=>'Error: your note is not valid'),$_smarty_tpl);?>
</b>").fadeIn(400);
					else if (r == 'error:update')
						$('#note_feedback').html("<b style='color:red'><?php echo smartyTranslate(array('s'=>'Error: cannot save your note'),$_smarty_tpl);?>
</b>").fadeIn(400);
					$('#note_feedback').fadeOut(3000);
				}
			});
		}
	</script>

<div id="container-customer">

	<div class="info-customer-left">
			<div style="float: right">
			<a href="<?php echo $_smarty_tpl->tpl_vars['current']->value;?>
&updatecustomer&id_customer=<?php echo $_smarty_tpl->tpl_vars['customer']->value->id;?>
&token=<?php echo $_smarty_tpl->tpl_vars['token']->value;?>
">
				<img src="../img/admin/edit.gif" />
			</a>
		</div>
		<span style="font-size: 14px;">
			<?php echo $_smarty_tpl->tpl_vars['customer']->value->firstname;?>
 <?php echo $_smarty_tpl->tpl_vars['customer']->value->lastname;?>

		</span>
		<img src="<?php echo $_smarty_tpl->tpl_vars['gender_image']->value;?>
" style="margin-bottom: 5px" /><br />
		<a href="mailto:<?php echo $_smarty_tpl->tpl_vars['customer']->value->email;?>
" style="text-decoration: underline; color:#268CCD;"><?php echo $_smarty_tpl->tpl_vars['customer']->value->email;?>
</a>
		<br /><br />
		<?php echo smartyTranslate(array('s'=>'ID:'),$_smarty_tpl);?>
 <?php echo sprintf("%06d",$_smarty_tpl->tpl_vars['customer']->value->id);?>
<br />
		<?php echo smartyTranslate(array('s'=>'Registration date:'),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['registration_date']->value;?>
<br />
		<?php echo smartyTranslate(array('s'=>'Last visit:'),$_smarty_tpl);?>
 <?php if ($_smarty_tpl->tpl_vars['customer_stats']->value['last_visit']){?><?php echo $_smarty_tpl->tpl_vars['last_visit']->value;?>
<?php }else{ ?><?php echo smartyTranslate(array('s'=>'never'),$_smarty_tpl);?>
<?php }?><br />
		<?php if ($_smarty_tpl->tpl_vars['count_better_customers']->value!='-'){?><?php echo smartyTranslate(array('s'=>'Rank: #'),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['count_better_customers']->value;?>
<br /><?php }?>
		<?php if ($_smarty_tpl->tpl_vars['shop_is_feature_active']->value){?><?php echo smartyTranslate(array('s'=>'Shop:'),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['name_shop']->value;?>
<br /><?php }?>
	</div>
	
	<div class="info-customer-right">
		<div style="float: right">
			<a href="<?php echo $_smarty_tpl->tpl_vars['current']->value;?>
&addcustomer&id_customer=<?php echo $_smarty_tpl->tpl_vars['customer']->value->id;?>
&token=<?php echo $_smarty_tpl->tpl_vars['token']->value;?>
">
				<img src="../img/admin/edit.gif" />
			</a>
		</div>
		<?php echo smartyTranslate(array('s'=>'Newsletter:'),$_smarty_tpl);?>
 <?php if ($_smarty_tpl->tpl_vars['customer']->value->newsletter){?><img src="../img/admin/enabled.gif" /><?php }else{ ?><img src="../img/admin/disabled.gif" /><?php }?><br />
		<?php echo smartyTranslate(array('s'=>'Opt-in:'),$_smarty_tpl);?>
 <?php if ($_smarty_tpl->tpl_vars['customer']->value->optin){?><img src="../img/admin/enabled.gif" /><?php }else{ ?><img src="../img/admin/disabled.gif" /><?php }?><br />
		<?php echo smartyTranslate(array('s'=>'Age:'),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['customer_stats']->value['age'];?>
 <?php if (isset($_smarty_tpl->tpl_vars['customer']->value->birthday['age'])){?>(<?php echo $_smarty_tpl->tpl_vars['customer_birthday']->value;?>
)<?php }else{ ?><?php echo smartyTranslate(array('s'=>'unknown'),$_smarty_tpl);?>
<?php }?><br /><br />
		<?php echo smartyTranslate(array('s'=>'Last update:'),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['last_update']->value;?>
<br />
		<?php echo smartyTranslate(array('s'=>'Status:'),$_smarty_tpl);?>
 <?php if ($_smarty_tpl->tpl_vars['customer']->value->active){?><img src="../img/admin/enabled.gif" /><?php }else{ ?><img src="../img/admin/disabled.gif" /><?php }?>
	
		<?php if ($_smarty_tpl->tpl_vars['customer']->value->isGuest()){?>
			<div>
				<?php echo smartyTranslate(array('s'=>'This customer is registered as'),$_smarty_tpl);?>
 <b><?php echo smartyTranslate(array('s'=>'guest'),$_smarty_tpl);?>
</b>
				<?php if (!$_smarty_tpl->tpl_vars['customer_exists']->value){?>
					<form method="post" action="index.php?tab=AdminCustomers&id_customer=<?php echo $_smarty_tpl->tpl_vars['customer']->value->id;?>
&token=<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminCustomers'),$_smarty_tpl);?>
">
						<input type="hidden" name="id_lang" value="<?php echo $_smarty_tpl->tpl_vars['id_lang']->value;?>
" />
						<p class="center"><input class="button" type="submit" name="submitGuestToCustomer" value="<?php echo smartyTranslate(array('s'=>'Transform to customer account'),$_smarty_tpl);?>
" /></p>
						<?php echo smartyTranslate(array('s'=>'This feature generates a random password and sends an e-mail to the customer'),$_smarty_tpl);?>

					</form>
				<?php }else{ ?>
					</div><div><b style="color:red;"><?php echo smartyTranslate(array('s'=>'A registered customer account already exists with this e-mail address'),$_smarty_tpl);?>
</b>
				<?php }?>
			</div>
		<?php }?>

</div>
<div class="clear"></div>
	<div class="separation"></div>
	
	<div>
		<h2>
			<img src="../img/admin/cms.gif" /> <?php echo smartyTranslate(array('s'=>'Add a private note'),$_smarty_tpl);?>

		</h2>
		<p><?php echo smartyTranslate(array('s'=>'This note will be displayed to all employees but not to the customer.'),$_smarty_tpl);?>
</p>
		<form action="ajax.php" method="post" onsubmit="saveCustomerNote();return false;" id="customer_note">
			<textarea name="note" id="noteContent" style="width:600px;height:100px" onkeydown="$('#submitCustomerNote').removeAttr('disabled');"><?php echo $_smarty_tpl->tpl_vars['customer_note']->value;?>
</textarea><br />
			<input type="submit" id="submitCustomerNote" class="button" value="<?php echo smartyTranslate(array('s'=>'   Save   '),$_smarty_tpl);?>
" style="float:left;margin-top:5px" disabled="disabled" />
			<span id="note_feedback" style="position:relative; top:10px; left:10px;"></span>
		</form>
	</div>
	<div class="clear"></div>
	<div class="separation"></div>
	
	
	<h2><?php echo smartyTranslate(array('s'=>'Messages'),$_smarty_tpl);?>
 (<?php echo count($_smarty_tpl->tpl_vars['messages']->value);?>
)</h2>
	<?php if (count($_smarty_tpl->tpl_vars['messages']->value)){?>
		<table cellspacing="0" cellpadding="0" class="table" style="width:100%;">
			<tr>
				<th class="center"><?php echo smartyTranslate(array('s'=>'Status'),$_smarty_tpl);?>
</th>
				<th class="center"><?php echo smartyTranslate(array('s'=>'Message'),$_smarty_tpl);?>
</th>
				<th class="center"><?php echo smartyTranslate(array('s'=>'Sent on'),$_smarty_tpl);?>
</th>
			</tr>
			<?php  $_smarty_tpl->tpl_vars['message'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['message']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['messages']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['message']->key => $_smarty_tpl->tpl_vars['message']->value){
$_smarty_tpl->tpl_vars['message']->_loop = true;
?>
				<tr>
					<td><?php echo $_smarty_tpl->tpl_vars['message']->value['status'];?>
</td>
					<td>
						<a href="index.php?tab=AdminCustomerThreads&id_customer_thread=<?php echo $_smarty_tpl->tpl_vars['message']->value['id_customer_thread'];?>
&viewcustomer_thread&token=<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminCustomerThreads'),$_smarty_tpl);?>
">
							<?php echo $_smarty_tpl->tpl_vars['message']->value['message'];?>
...
						</a>
					</td>
					<td><?php echo $_smarty_tpl->tpl_vars['message']->value['date_add'];?>
</td>
				</tr>
			<?php } ?>
		</table>
		<div class="clear">&nbsp;</div>
	<?php }else{ ?>
		<?php echo smartyTranslate(array('s'=>'%1$s %2$s has never contacted you','sprintf'=>array($_smarty_tpl->tpl_vars['customer']->value->firstname,$_smarty_tpl->tpl_vars['customer']->value->lastname)),$_smarty_tpl);?>

	<?php }?>
	
	
	<div><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0][0]->smartyHook(array('h'=>"displayAdminCustomers",'id_customer'=>$_smarty_tpl->tpl_vars['customer']->value->id),$_smarty_tpl);?>
</div>
	
	<div class="clear">&nbsp;</div>
	
	<h2>
		<?php echo smartyTranslate(array('s'=>'Groups'),$_smarty_tpl);?>
 (<?php echo count($_smarty_tpl->tpl_vars['groups']->value);?>
)
		<a href="<?php echo $_smarty_tpl->tpl_vars['current']->value;?>
&addcustomer&id_customer=<?php echo $_smarty_tpl->tpl_vars['customer']->value->id;?>
&token=<?php echo $_smarty_tpl->tpl_vars['token']->value;?>
">
			<img src="../img/admin/edit.gif" />
		</a>
	</h2>
	<?php if ($_smarty_tpl->tpl_vars['groups']->value&&count($_smarty_tpl->tpl_vars['groups']->value)){?>
		<table cellspacing="0" cellpadding="0" class="table" style="width:100%;">
			<colgroup>
				<col width="10px">
				<col width="">
				<col width="70px">
			</colgroup>
			<tr>
				<th height="39px" class="right"><?php echo smartyTranslate(array('s'=>'ID'),$_smarty_tpl);?>
</th>
				<th class="center"><?php echo smartyTranslate(array('s'=>'Name'),$_smarty_tpl);?>
</th>
				<th class="center"><?php echo smartyTranslate(array('s'=>'Actions'),$_smarty_tpl);?>
</th>
			</tr>
		<?php  $_smarty_tpl->tpl_vars['group'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['group']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['groups']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['group']->key => $_smarty_tpl->tpl_vars['group']->value){
$_smarty_tpl->tpl_vars['group']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['group']->key;
?>
			<tr <?php if ($_smarty_tpl->tpl_vars['key']->value%2){?>class="alt_row"<?php }?> style="cursor: pointer" onclick="document.location = '?tab=AdminGroups&id_group=<?php echo $_smarty_tpl->tpl_vars['group']->value['id_group'];?>
&viewgroup&token=<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminGroups'),$_smarty_tpl);?>
'">
				<td class="center"><?php echo $_smarty_tpl->tpl_vars['group']->value['id_group'];?>
</td>
				<td><?php echo $_smarty_tpl->tpl_vars['group']->value['name'];?>
</td>
				<td align="center"><a href="?tab=AdminGroups&id_group=<?php echo $_smarty_tpl->tpl_vars['group']->value['id_group'];?>
&viewgroup&token=<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminGroups'),$_smarty_tpl);?>
"><img src="../img/admin/details.gif" /></a></td>
			</tr>
		<?php } ?>
		</table>
	<?php }?>
	<div class="clear">&nbsp;</div>
	
	
	<h2><?php echo smartyTranslate(array('s'=>'Orders'),$_smarty_tpl);?>
 (<?php echo count($_smarty_tpl->tpl_vars['orders']->value);?>
)</h2>
	<?php if ($_smarty_tpl->tpl_vars['orders']->value&&count($_smarty_tpl->tpl_vars['orders']->value)){?>
		<?php $_smarty_tpl->tpl_vars['count_ok'] = new Smarty_variable(count($_smarty_tpl->tpl_vars['orders_ok']->value), null, 0);?>
		<?php if ($_smarty_tpl->tpl_vars['count_ok']->value){?>
			<div>
				<h3 style="color:green;font-weight:700">
					<?php echo smartyTranslate(array('s'=>'Valid orders:'),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['count_ok']->value;?>
 <?php echo smartyTranslate(array('s'=>'for'),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['total_ok']->value;?>

				</h3>
				<table cellspacing="0" cellpadding="0" class="table" style="width:100%; text-align:left;">
					<colgroup>
						<col width="10px">
						<col width="100px">
						<col width="100px">
						<col width="">
						<col width="50px">
						<col width="80px">
						<col width="70px">
					</colgroup>
					<tr>
						<th height="39px" class="center"><?php echo smartyTranslate(array('s'=>'ID'),$_smarty_tpl);?>
</th>
						<th class="left"><?php echo smartyTranslate(array('s'=>'Date'),$_smarty_tpl);?>
</th>
						<th class="left"><?php echo smartyTranslate(array('s'=>'Payment'),$_smarty_tpl);?>
</th>
						<th class="left"><?php echo smartyTranslate(array('s'=>'State'),$_smarty_tpl);?>
</th>
						<th class="left"><?php echo smartyTranslate(array('s'=>'Products'),$_smarty_tpl);?>
</th>
						<th class="left"><?php echo smartyTranslate(array('s'=>'Total spent'),$_smarty_tpl);?>
</th>
						<th class="center"><?php echo smartyTranslate(array('s'=>'Actions'),$_smarty_tpl);?>
</th>
					</tr>
					<?php  $_smarty_tpl->tpl_vars['order'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['order']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['orders_ok']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['order']->key => $_smarty_tpl->tpl_vars['order']->value){
$_smarty_tpl->tpl_vars['order']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['order']->key;
?>
						<tr <?php if ($_smarty_tpl->tpl_vars['key']->value%2){?>class="alt_row"<?php }?> style="cursor: pointer" onclick="document.location = '?tab=AdminOrders&id_order=<?php echo $_smarty_tpl->tpl_vars['order']->value['id_order'];?>
&vieworder&token=<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminOrders'),$_smarty_tpl);?>
'">
							<td class="center"><?php echo $_smarty_tpl->tpl_vars['order']->value['id_order'];?>
</td>
							<td><?php echo $_smarty_tpl->tpl_vars['order']->value['date_add'];?>
</td>
							<td><?php echo $_smarty_tpl->tpl_vars['order']->value['payment'];?>
</td>
							<td><?php echo $_smarty_tpl->tpl_vars['order']->value['order_state'];?>
</td>
							<td align="right"><?php echo $_smarty_tpl->tpl_vars['order']->value['nb_products'];?>
</td>
							<td align="right"><?php echo $_smarty_tpl->tpl_vars['order']->value['total_paid_real'];?>
</td>
							<td align="center"><a href="?tab=AdminOrders&id_order=<?php echo $_smarty_tpl->tpl_vars['order']->value['id_order'];?>
&vieworder&token=<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminOrders'),$_smarty_tpl);?>
"><img src="../img/admin/details.gif" /></a></td>
						</tr>
					<?php } ?>
				</table>
			</div>
		<?php }?>
		<?php $_smarty_tpl->tpl_vars['count_ko'] = new Smarty_variable(count($_smarty_tpl->tpl_vars['orders_ko']->value), null, 0);?>
		<?php if ($_smarty_tpl->tpl_vars['count_ko']->value){?>
			<div>
				<h3 style="color:red;font-weight:normal;"><?php echo smartyTranslate(array('s'=>'Invalid orders:'),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['count_ko']->value;?>
</h3>
				<table cellspacing="0" cellpadding="0" class="table" style="width:100%;">
					<colgroup>
						<col width="10px">
						<col width="100px">
						<col width="">
						<col width="">
						<col width="100px">
						<col width="100px">
						<col width="52px">
					</colgroup>
					<tr>
						<th height="39px" class="center"><?php echo smartyTranslate(array('s'=>'ID'),$_smarty_tpl);?>
</th>
						<th class="center"><?php echo smartyTranslate(array('s'=>'Date'),$_smarty_tpl);?>
</th>
						<th class="center"><?php echo smartyTranslate(array('s'=>'Payment'),$_smarty_tpl);?>
</th>
						<th class="center"><?php echo smartyTranslate(array('s'=>'State'),$_smarty_tpl);?>
</th>
						<th class="center"><?php echo smartyTranslate(array('s'=>'Products'),$_smarty_tpl);?>
</th>
						<th class="center"><?php echo smartyTranslate(array('s'=>'Total spent'),$_smarty_tpl);?>
</th>
						<th class="center"><?php echo smartyTranslate(array('s'=>'Actions'),$_smarty_tpl);?>
</th>
					</tr>
					<?php  $_smarty_tpl->tpl_vars['order'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['order']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['orders_ko']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['order']->key => $_smarty_tpl->tpl_vars['order']->value){
$_smarty_tpl->tpl_vars['order']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['order']->key;
?>
						<tr <?php if ($_smarty_tpl->tpl_vars['key']->value%2){?>class="alt_row"<?php }?> style="cursor: pointer" onclick="document.location = '?tab=AdminOrders&id_order=<?php echo $_smarty_tpl->tpl_vars['order']->value['id_order'];?>
&vieworder&token=<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminOrders'),$_smarty_tpl);?>
'">
							<td class="center"><?php echo $_smarty_tpl->tpl_vars['order']->value['id_order'];?>
</td>
							<td><?php echo $_smarty_tpl->tpl_vars['order']->value['date_add'];?>
</td>
							<td><?php echo $_smarty_tpl->tpl_vars['order']->value['payment'];?>
</td>
							<td><?php echo $_smarty_tpl->tpl_vars['order']->value['order_state'];?>
</td>
														<td align="right"><?php echo $_smarty_tpl->tpl_vars['order']->value['nb_products'];?>
</td>
							<td align="right"><?php echo $_smarty_tpl->tpl_vars['order']->value['total_paid_real'];?>
</td>
							<td align="center"><a href="?tab=AdminOrders&id_order=<?php echo $_smarty_tpl->tpl_vars['order']->value['id_order'];?>
&vieworder&token=<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminOrders'),$_smarty_tpl);?>
"><img src="../img/admin/details.gif" /></a></td>
						</tr>
					<?php } ?>
				</table>
			</div>
			<div class="clear">&nbsp;</div>
		<?php }?>
	<?php }else{ ?>
		<?php echo smartyTranslate(array('s'=>'%1$s %2$s has not placed any orders yet','sprintf'=>array($_smarty_tpl->tpl_vars['customer']->value->firstname,$_smarty_tpl->tpl_vars['customer']->value->lastname)),$_smarty_tpl);?>

	<?php }?>
	
	<?php if ($_smarty_tpl->tpl_vars['products']->value&&count($_smarty_tpl->tpl_vars['products']->value)){?>
	<div class="clear">&nbsp;</div>
		<h2><?php echo smartyTranslate(array('s'=>'Products'),$_smarty_tpl);?>
 (<?php echo count($_smarty_tpl->tpl_vars['products']->value);?>
)</h2>
		<table cellspacing="0" cellpadding="0" class="table" style="width:100%;">
					<colgroup>
						<col width="50px">
						<col width="">
						<col width="60px">
						<col width="70px">
					</colgroup>
			<tr>
				<th height="39px" class="center"><?php echo smartyTranslate(array('s'=>'Date'),$_smarty_tpl);?>
</th>
				<th class="center"><?php echo smartyTranslate(array('s'=>'Name'),$_smarty_tpl);?>
</th>
				<th class="center"><?php echo smartyTranslate(array('s'=>'Quantity'),$_smarty_tpl);?>
</th>
				<th class="center"><?php echo smartyTranslate(array('s'=>'Actions'),$_smarty_tpl);?>
</th>
			</tr>
			<?php  $_smarty_tpl->tpl_vars['product'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['product']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['products']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['product']->key => $_smarty_tpl->tpl_vars['product']->value){
$_smarty_tpl->tpl_vars['product']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['product']->key;
?>
				<tr <?php if ($_smarty_tpl->tpl_vars['key']->value%2){?>class="alt_row"<?php }?> style="cursor: pointer" onclick="document.location = '?tab=AdminOrders&id_order=<?php echo $_smarty_tpl->tpl_vars['product']->value['id_order'];?>
&vieworder&token=<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminOrders'),$_smarty_tpl);?>
'">
					<td><?php echo $_smarty_tpl->tpl_vars['product']->value['date_add'];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['product']->value['product_name'];?>
</td>
					<td align="right"><?php echo $_smarty_tpl->tpl_vars['product']->value['product_quantity'];?>
</td>
					<td align="center"><a href="?tab=AdminOrders&id_order=<?php echo $_smarty_tpl->tpl_vars['product']->value['id_order'];?>
&vieworder&token=<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminOrders'),$_smarty_tpl);?>
"><img src="../img/admin/details.gif" /></a></td>
				</tr>
			<?php } ?>
		</table>
	<?php }?>
	<div class="clear">&nbsp;</div>
	
	<h2><?php echo smartyTranslate(array('s'=>'Addresses'),$_smarty_tpl);?>
 (<?php echo count($_smarty_tpl->tpl_vars['addresses']->value);?>
)</h2>
	<?php if (count($_smarty_tpl->tpl_vars['addresses']->value)){?>
		<table cellspacing="0" cellpadding="0" class="table" style="width:100%;">
					<colgroup>
						<col width="120px">
						<col width="120px">
						<col width="">
						<col width="100px">
						<col width="170px">
						<col width="70px">
					</colgroup>
			<tr>
				<th height="39px"><?php echo smartyTranslate(array('s'=>'Company'),$_smarty_tpl);?>
</th>
				<th><?php echo smartyTranslate(array('s'=>'Name'),$_smarty_tpl);?>
</th>
				<th><?php echo smartyTranslate(array('s'=>'Address'),$_smarty_tpl);?>
</th>
				<th><?php echo smartyTranslate(array('s'=>'Country'),$_smarty_tpl);?>
</th>
				<th><?php echo smartyTranslate(array('s'=>'Phone number(s)'),$_smarty_tpl);?>
</th>
				<th><?php echo smartyTranslate(array('s'=>'Actions'),$_smarty_tpl);?>
</th>
			</tr>
			<?php  $_smarty_tpl->tpl_vars['address'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['address']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['addresses']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['address']->key => $_smarty_tpl->tpl_vars['address']->value){
$_smarty_tpl->tpl_vars['address']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['address']->key;
?>
				<tr <?php if ($_smarty_tpl->tpl_vars['key']->value%2){?>class="alt_row"<?php }?>>
					<td><?php if ($_smarty_tpl->tpl_vars['address']->value['company']){?><?php echo $_smarty_tpl->tpl_vars['address']->value['company'];?>
<?php }else{ ?>--<?php }?></td>
					<td><?php echo $_smarty_tpl->tpl_vars['address']->value['firstname'];?>
 <?php echo $_smarty_tpl->tpl_vars['address']->value['lastname'];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['address']->value['address1'];?>
 <?php if ($_smarty_tpl->tpl_vars['address']->value['address2']){?><?php echo $_smarty_tpl->tpl_vars['address']->value['address2'];?>
<?php }?> <?php echo $_smarty_tpl->tpl_vars['address']->value['postcode'];?>
 <?php echo $_smarty_tpl->tpl_vars['address']->value['city'];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['address']->value['country'];?>
</td>
					<td class="right">
						<?php if ($_smarty_tpl->tpl_vars['address']->value['phone']){?>
							<?php echo $_smarty_tpl->tpl_vars['address']->value['phone'];?>

							<?php if ($_smarty_tpl->tpl_vars['address']->value['phone_mobile']){?><br /><?php echo $_smarty_tpl->tpl_vars['address']->value['phone_mobile'];?>
<?php }?>
						<?php }else{ ?>
							<?php if ($_smarty_tpl->tpl_vars['address']->value['phone_mobile']){?><br /><?php echo $_smarty_tpl->tpl_vars['address']->value['phone_mobile'];?>
<?php }else{ ?>--<?php }?>
						<?php }?>
					</td>
					<td align="center">
						<a href="?tab=AdminAddresses&id_address=<?php echo $_smarty_tpl->tpl_vars['address']->value['id_address'];?>
&addaddress&token=<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminAddresses'),$_smarty_tpl);?>
"><img src="../img/admin/edit.gif" /></a>
						<a href="?tab=AdminAddresses&id_address=<?php echo $_smarty_tpl->tpl_vars['address']->value['id_address'];?>
&deleteaddress&token=<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminAddresses'),$_smarty_tpl);?>
"><img src="../img/admin/delete.gif" /></a>
					</td>
				</tr>
			<?php } ?>
		</table>
	<?php }else{ ?>
		<?php echo smartyTranslate(array('s'=>'%1$s %2$s has not registered any addresses yet','sprintf'=>array($_smarty_tpl->tpl_vars['customer']->value->firstname,$_smarty_tpl->tpl_vars['customer']->value->lastname)),$_smarty_tpl);?>

	<?php }?>
	
	<div class="clear">&nbsp;</div>
	<h2><?php echo smartyTranslate(array('s'=>'Vouchers'),$_smarty_tpl);?>
 (<?php echo count($_smarty_tpl->tpl_vars['discounts']->value);?>
)</h2>
	<?php if (count($_smarty_tpl->tpl_vars['discounts']->value)){?>
		<table cellspacing="0" cellpadding="0" class="table">
			<tr>
				<th><?php echo smartyTranslate(array('s'=>'ID'),$_smarty_tpl);?>
</th>
				<th><?php echo smartyTranslate(array('s'=>'Code'),$_smarty_tpl);?>
</th>
				<th><?php echo smartyTranslate(array('s'=>'Name'),$_smarty_tpl);?>
</th>
				<th><?php echo smartyTranslate(array('s'=>'Status'),$_smarty_tpl);?>
</th>
				<th><?php echo smartyTranslate(array('s'=>'Actions'),$_smarty_tpl);?>
</th>
			</tr>
		<?php  $_smarty_tpl->tpl_vars['discount'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['discount']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['discounts']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['discount']->key => $_smarty_tpl->tpl_vars['discount']->value){
$_smarty_tpl->tpl_vars['discount']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['discount']->key;
?>
			<tr <?php if ($_smarty_tpl->tpl_vars['key']->value%2){?>class="alt_row"<?php }?>>
				<td align="center"><?php echo $_smarty_tpl->tpl_vars['discount']->value['id_cart_rule'];?>
</td>
				<td><?php echo $_smarty_tpl->tpl_vars['discount']->value['code'];?>
</td>
				<td><?php echo $_smarty_tpl->tpl_vars['discount']->value['name'];?>
</td>
				<td align="center"><img src="../img/admin/<?php if ($_smarty_tpl->tpl_vars['discount']->value['active']){?>enabled.gif<?php }else{ ?>disabled.gif<?php }?>" alt="<?php echo smartyTranslate(array('s'=>'Status'),$_smarty_tpl);?>
" title="<?php echo smartyTranslate(array('s'=>'Status'),$_smarty_tpl);?>
" /></td>
				<td align="center">
					<a href="?tab=AdminCartRules&id_cart_rule=<?php echo $_smarty_tpl->tpl_vars['discount']->value['id_cart_rule'];?>
&addcart_rule&token=<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminCartRules'),$_smarty_tpl);?>
"><img src="../img/admin/edit.gif" /></a>
					<a href="?tab=AdminCartRules&id_cart_rule=<?php echo $_smarty_tpl->tpl_vars['discount']->value['id_cart_rule'];?>
&deletecart_rule&token=<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminCartRules'),$_smarty_tpl);?>
"><img src="../img/admin/delete.gif" /></a>
				</td>
			</tr>
		<?php } ?>
		</table>
	<?php }else{ ?>
		<?php echo smartyTranslate(array('s'=>'%1$s %2$s has no discount vouchers','sprintf'=>array($_smarty_tpl->tpl_vars['customer']->value->firstname,$_smarty_tpl->tpl_vars['customer']->value->lastname)),$_smarty_tpl);?>
.
	<?php }?>
	<div class="clear">&nbsp;</div>
	
	<div>
		<h2><?php echo smartyTranslate(array('s'=>'Carts'),$_smarty_tpl);?>
 (<?php echo count($_smarty_tpl->tpl_vars['carts']->value);?>
)</h2>
		<?php if ($_smarty_tpl->tpl_vars['carts']->value&&count($_smarty_tpl->tpl_vars['carts']->value)){?>
			<table cellspacing="0" cellpadding="0" class="table" style="width:100%">
				<colgroup>
					<col width="50px">
					<col width="150px">
					<col width="">
					<col width="70px">
					<col width="50px">
				</colgroup>
				<tr>
					<th height="39px" class="center"><?php echo smartyTranslate(array('s'=>'ID'),$_smarty_tpl);?>
</th>
					<th class="center"><?php echo smartyTranslate(array('s'=>'Date'),$_smarty_tpl);?>
</th>
					<th class="center"><?php echo smartyTranslate(array('s'=>'Carrier'),$_smarty_tpl);?>
</th>
					<th class="center"><?php echo smartyTranslate(array('s'=>'Total'),$_smarty_tpl);?>
</th>
					<th class="center"><?php echo smartyTranslate(array('s'=>'Actions'),$_smarty_tpl);?>
</th>
				</tr>
				<?php  $_smarty_tpl->tpl_vars['cart'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['cart']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['carts']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['cart']->key => $_smarty_tpl->tpl_vars['cart']->value){
$_smarty_tpl->tpl_vars['cart']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['cart']->key;
?>
					<tr <?php if ($_smarty_tpl->tpl_vars['key']->value%2){?>class="alt_row"<?php }?> style="cursor: pointer" onclick="document.location = '?tab=AdminCarts&id_cart=<?php echo $_smarty_tpl->tpl_vars['cart']->value['id_cart'];?>
&viewcart&token=<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminCarts'),$_smarty_tpl);?>
'">
						<td class="center"><?php echo $_smarty_tpl->tpl_vars['cart']->value['id_cart'];?>
</td>
						<td><?php echo $_smarty_tpl->tpl_vars['cart']->value['date_add'];?>
</td>
						<td><?php echo $_smarty_tpl->tpl_vars['cart']->value['name'];?>
</td>
						<td align="right"><?php echo $_smarty_tpl->tpl_vars['cart']->value['total_price'];?>
</td>
						<td align="center"><a href="index.php?tab=AdminCarts&id_cart=<?php echo $_smarty_tpl->tpl_vars['cart']->value['id_cart'];?>
&viewcart&token=<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminCarts'),$_smarty_tpl);?>
"><img src="../img/admin/details.gif" /></a></td>
					</tr>
				<?php } ?>
			</table>
		<?php }else{ ?>
			<?php echo smartyTranslate(array('s'=>'No cart available'),$_smarty_tpl);?>
.
		<?php }?>
	</div>
	
	<?php if (count($_smarty_tpl->tpl_vars['interested']->value)){?>
		<div>
		<h2><?php echo smartyTranslate(array('s'=>'Products'),$_smarty_tpl);?>
 (<?php echo count($_smarty_tpl->tpl_vars['interested']->value);?>
)</h2>
			<table cellspacing="0" cellpadding="0" class="table" style="width:100%;">
				<colgroup>
					<col width="10px">
					<col width="">
					<col width="50px">
				</colgroup>
				<?php  $_smarty_tpl->tpl_vars['p'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['p']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['interested']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['p']->key => $_smarty_tpl->tpl_vars['p']->value){
$_smarty_tpl->tpl_vars['p']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['p']->key;
?>
					<tr <?php if ($_smarty_tpl->tpl_vars['key']->value%2){?>class="alt_row"<?php }?> style="cursor: pointer" onclick="document.location = '<?php echo $_smarty_tpl->tpl_vars['p']->value['url'];?>
'">
						<td><?php echo $_smarty_tpl->tpl_vars['p']->value['id'];?>
</td>
						<td><?php echo $_smarty_tpl->tpl_vars['p']->value['name'];?>
</td>
						<td align="center"><a href="<?php echo $_smarty_tpl->tpl_vars['p']->value['url'];?>
"><img src="../img/admin/details.gif" /></a></td>
					</tr>
				<?php } ?>
			</table>
		</div>
	<?php }?>
				
	<div class="clear">&nbsp;</div>
	
	
	<?php if (count($_smarty_tpl->tpl_vars['connections']->value)){?>
		<h2><?php echo smartyTranslate(array('s'=>'Last connections'),$_smarty_tpl);?>
</h2>
		<table cellspacing="0" cellpadding="0" class="table" style="width:100%;">
				<colgroup>
					<col width="150px">
					<col width="100px">
					<col width="100px">
					<col width="">
					<col width="150px">
				</colgroup>
			<tr>
				<th height="39px;"><?php echo smartyTranslate(array('s'=>'Date'),$_smarty_tpl);?>
</th>
				<th><?php echo smartyTranslate(array('s'=>'Pages viewed'),$_smarty_tpl);?>
</th>
				<th><?php echo smartyTranslate(array('s'=>'Total time'),$_smarty_tpl);?>
</th>
				<th><?php echo smartyTranslate(array('s'=>'Origin'),$_smarty_tpl);?>
</th>
				<th><?php echo smartyTranslate(array('s'=>'IP Address'),$_smarty_tpl);?>
</th>
			</tr>
			<?php  $_smarty_tpl->tpl_vars['connection'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['connection']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['connections']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['connection']->key => $_smarty_tpl->tpl_vars['connection']->value){
$_smarty_tpl->tpl_vars['connection']->_loop = true;
?>
				<tr>
					<td><?php echo $_smarty_tpl->tpl_vars['connection']->value['date_add'];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['connection']->value['pages'];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['connection']->value['time'];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['connection']->value['http_referer'];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['connection']->value['ipaddress'];?>
</td>
				</tr>
			<?php } ?>
		</table>
		<div class="clear">&nbsp;</div>
	<?php }?>
	
	<?php if (count($_smarty_tpl->tpl_vars['referrers']->value)){?>
		<h2><?php echo smartyTranslate(array('s'=>'Referrers'),$_smarty_tpl);?>
</h2>
		<table cellspacing="0" cellpadding="0" class="table">
			<tr>
				<th style="width: 200px"><?php echo smartyTranslate(array('s'=>'Date'),$_smarty_tpl);?>
</th>
				<th style="width: 200px"><?php echo smartyTranslate(array('s'=>'Name'),$_smarty_tpl);?>
</th>
				<?php if ($_smarty_tpl->tpl_vars['shop_is_feature_active']->value){?><th style="width: 200px"><?php echo smartyTranslate(array('s'=>'Shop'),$_smarty_tpl);?>
</th><?php }?>
			</tr>
			<?php  $_smarty_tpl->tpl_vars['referrer'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['referrer']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['referrers']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['referrer']->key => $_smarty_tpl->tpl_vars['referrer']->value){
$_smarty_tpl->tpl_vars['referrer']->_loop = true;
?>
				<tr>
					<td><?php echo $_smarty_tpl->tpl_vars['referrer']->value['date_add'];?>
</td>
					<td><?php echo $_smarty_tpl->tpl_vars['referrer']->value['name'];?>
</td>
					<?php if ($_smarty_tpl->tpl_vars['shop_is_feature_active']->value){?><td><?php echo $_smarty_tpl->tpl_vars['referrer']->value['shop_name'];?>
</td><?php }?>
				</tr>
			<?php } ?>
		</table>
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
<?php }} ?>