<?php /* Smarty version Smarty-3.1.8, created on 2012-12-24 14:43:45
         compiled from "/home/bodo/public_html/dev/admin-dev/themes/default/template/controllers/information/helpers/view/view.tpl" */ ?>
<?php /*%%SmartyHeaderCode:201181097250d85c11d71db6-63288814%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '786f19b16629fe2cf871f6904753697511f18b49' => 
    array (
      0 => '/home/bodo/public_html/dev/admin-dev/themes/default/template/controllers/information/helpers/view/view.tpl',
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
  'nocache_hash' => '201181097250d85c11d71db6-63288814',
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
  'unifunc' => 'content_50d85c1241cf28_72275408',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50d85c1241cf28_72275408')) {function content_50d85c1241cf28_72275408($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/home/bodo/public_html/dev/tools/smarty/plugins/modifier.escape.php';
?>

<?php if ($_smarty_tpl->tpl_vars['show_toolbar']->value){?>
	<?php echo $_smarty_tpl->getSubTemplate ("toolbar.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('toolbar_btn'=>$_smarty_tpl->tpl_vars['toolbar_btn']->value,'toolbar_scroll'=>$_smarty_tpl->tpl_vars['toolbar_scroll']->value,'title'=>$_smarty_tpl->tpl_vars['title']->value), 0);?>

<?php }?>

<div class="leadin"></div>



	<script type="text/javascript">
		$(document).ready(function()
		{
			$.ajax({
				type: 'GET',
				url: '<?php echo $_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminInformation');?>
',
				data: {
					'action': 'checkFiles',
					'ajax': 1
				},
				dataType: 'json',
				success: function(json)
				{
					var tab = {
						'missing': '<?php echo smartyTranslate(array('s'=>'Missing files'),$_smarty_tpl);?>
',
						'updated': '<?php echo smartyTranslate(array('s'=>'Updated files'),$_smarty_tpl);?>
'
					};

					if (json.missing.length || json.updated.length)
						$('#changedFiles').html('<div class="warn"><?php echo smartyTranslate(array('s'=>'Changed/missing files have been detected'),$_smarty_tpl);?>
</div>');
					else
						$('#changedFiles').html('<div class="conf"><?php echo smartyTranslate(array('s'=>'No change has been detected in your files'),$_smarty_tpl);?>
</div>');

					$.each(tab, function(key, lang)
					{
						if (json[key].length)
						{
							var html = $('<ul>').attr('id', key+'_files');
							$(json[key]).each(function(key, file)
							{
								html.append($('<li>').html(file))
							});
							$('#changedFiles')
								.append($('<h3>').html(lang+' ('+json[key].length+')'))
								.append(html);
						}
					});
				}
			});
		});
	</script>

	<fieldset>
		<legend><img src="../img/t/AdminInformation.gif" alt="" /><?php echo smartyTranslate(array('s'=>'Configuration Information'),$_smarty_tpl);?>
</legend>
		<p><?php echo smartyTranslate(array('s'=>'This information must be provided when you report an issue on our bug tracker or forum.'),$_smarty_tpl);?>
</p>
	</fieldset>
	<br />
	<fieldset>
		<legend><img src="../img/t/AdminInformation.gif" alt="" /> <?php echo smartyTranslate(array('s'=>'Information about your configuration'),$_smarty_tpl);?>
</legend>
		<h3><?php echo smartyTranslate(array('s'=>'Server information'),$_smarty_tpl);?>
</h3>	
		<?php if (count($_smarty_tpl->tpl_vars['uname']->value)){?>
		<p>
			<b><?php echo smartyTranslate(array('s'=>'Server information'),$_smarty_tpl);?>
:</b> <?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['uname']->value, 'htmlall', 'UTF-8');?>

		</p>
		<?php }?>
		<p>
			<b><?php echo smartyTranslate(array('s'=>'Server software version'),$_smarty_tpl);?>
:</b> <?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['version']->value['server'], 'htmlall', 'UTF-8');?>

		</p>
		<p>
			<b><?php echo smartyTranslate(array('s'=>'PHP version'),$_smarty_tpl);?>
:</b> <?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['version']->value['php'], 'htmlall', 'UTF-8');?>

		</p>
		<p>
			<b><?php echo smartyTranslate(array('s'=>'Memory limit'),$_smarty_tpl);?>
:</b> <?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['version']->value['memory_limit'], 'htmlall', 'UTF-8');?>

		</p>
		<p>
			<b><?php echo smartyTranslate(array('s'=>'Max execution time'),$_smarty_tpl);?>
:</b> <?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['version']->value['max_execution_time'], 'htmlall', 'UTF-8');?>

		</p>
		<?php if ($_smarty_tpl->tpl_vars['apache_instaweb']->value){?>
		<p style="color:red;font-weight:700"><?php echo smartyTranslate(array('s'=>'PageSpeed module for Apache installed (mod_instaweb)'),$_smarty_tpl);?>
</p>
		<?php }?>

		<hr />
		<h3><?php echo smartyTranslate(array('s'=>'Database information'),$_smarty_tpl);?>
</h3>
		<p>
			<b><?php echo smartyTranslate(array('s'=>'MySQL version'),$_smarty_tpl);?>
:</b> <?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['database']->value['version'], 'htmlall', 'UTF-8');?>

		</p>
		<p>
			<b><?php echo smartyTranslate(array('s'=>'MySQL engine'),$_smarty_tpl);?>
:</b> <?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['database']->value['engine'], 'htmlall', 'UTF-8');?>

		</p>
		<p>
			<b><?php echo smartyTranslate(array('s'=>'Tables prefix'),$_smarty_tpl);?>
:</b> <?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['database']->value['prefix'], 'htmlall', 'UTF-8');?>

		</p>
	
		<hr />
		<h3><?php echo smartyTranslate(array('s'=>'Store information'),$_smarty_tpl);?>
</h3>
		<p>
			<b><?php echo smartyTranslate(array('s'=>'Prestashop version'),$_smarty_tpl);?>
:</b> <?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['shop']->value['ps'], 'htmlall', 'UTF-8');?>

		</p>
		<p>
			<b><?php echo smartyTranslate(array('s'=>'Shop URL'),$_smarty_tpl);?>
:</b> <?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['shop']->value['url'], 'htmlall', 'UTF-8');?>

		</p>
		<p>
			<b><?php echo smartyTranslate(array('s'=>'Current theme in use'),$_smarty_tpl);?>
:</b> <?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['shop']->value['theme'], 'htmlall', 'UTF-8');?>

		</p>
		<hr />
		<h3><?php echo smartyTranslate(array('s'=>'Mail configuration'),$_smarty_tpl);?>
</h3>
		<p>
			<b><?php echo smartyTranslate(array('s'=>'Mail method'),$_smarty_tpl);?>
:</b>
	
	<?php if ($_smarty_tpl->tpl_vars['mail']->value){?>
		<?php echo smartyTranslate(array('s'=>'You are using the PHP mail() function.'),$_smarty_tpl);?>
</p>
	<?php }else{ ?>
		<?php echo smartyTranslate(array('s'=>'You are using your own SMTP parameters.'),$_smarty_tpl);?>
</p>
		<p>
			<b><?php echo smartyTranslate(array('s'=>'SMTP server'),$_smarty_tpl);?>
:</b> <?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['smtp']->value['server'], 'htmlall', 'UTF-8');?>

		</p>
		<p>
			<b><?php echo smartyTranslate(array('s'=>'SMTP user'),$_smarty_tpl);?>
:</b>
			<?php if ($_smarty_tpl->tpl_vars['smtp']->value['user']!=''){?>
				<?php echo smartyTranslate(array('s'=>'Defined'),$_smarty_tpl);?>

			<?php }else{ ?>
				<span style="color:red;"><?php echo smartyTranslate(array('s'=>'Not defined'),$_smarty_tpl);?>
</span>
			<?php }?>
		</p>
		<p>
			<b><?php echo smartyTranslate(array('s'=>'SMTP password'),$_smarty_tpl);?>
:</b>
			<?php if ($_smarty_tpl->tpl_vars['smtp']->value['password']!=''){?>
				<?php echo smartyTranslate(array('s'=>'Defined'),$_smarty_tpl);?>

			<?php }else{ ?>
				<span style="color:red;"><?php echo smartyTranslate(array('s'=>'Not defined'),$_smarty_tpl);?>
</span>
			<?php }?>
		</p>
		<p>
			<b><?php echo smartyTranslate(array('s'=>'Encryption'),$_smarty_tpl);?>
:</b> <?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['smtp']->value['encryption'], 'htmlall', 'UTF-8');?>

		</p>
		<p>
			<b><?php echo smartyTranslate(array('s'=>'Port'),$_smarty_tpl);?>
:</b> <?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['smtp']->value['port'], 'htmlall', 'UTF-8');?>

		</p>
	<?php }?>
		<hr />
		<h3><?php echo smartyTranslate(array('s'=>'Your information'),$_smarty_tpl);?>
</h3>
		<p>
			<b><?php echo smartyTranslate(array('s'=>'Your web browser'),$_smarty_tpl);?>
:</b> <?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['user_agent']->value, 'htmlall', 'UTF-8');?>

		</p>
	</fieldset>
	<br />
	<fieldset id="checkConfiguration">
		<legend><img src="../img/t/AdminInformation.gif" alt="" /> <?php echo smartyTranslate(array('s'=>'Check your configuration'),$_smarty_tpl);?>
</legend>
		<p>
			<b><?php echo smartyTranslate(array('s'=>'Required parameters'),$_smarty_tpl);?>
:</b>
			<?php if (!$_smarty_tpl->tpl_vars['failRequired']->value){?>
					<span style="color:green;font-weight:bold;">OK</span>
				</p>
			<?php }else{ ?>
				<span style="color:red"><?php echo smartyTranslate(array('s'=>'Please fix the following error(s)'),$_smarty_tpl);?>
</span>
			</p>
			<ul>
				<?php  $_smarty_tpl->tpl_vars['value'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['value']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['testsRequired']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['value']->key => $_smarty_tpl->tpl_vars['value']->value){
$_smarty_tpl->tpl_vars['value']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['value']->key;
?>
					<?php if ($_smarty_tpl->tpl_vars['value']->value=='fail'){?>
						<li><?php echo $_smarty_tpl->tpl_vars['testsErrors']->value[$_smarty_tpl->tpl_vars['key']->value];?>
</li>
					<?php }?>
				<?php } ?>
			</ul>
			<?php }?>
	
			<p>
				<b><?php echo smartyTranslate(array('s'=>'Optional parameters'),$_smarty_tpl);?>
:</b>
			<?php if (!$_smarty_tpl->tpl_vars['failOptional']->value){?>
				<span style="color:green;font-weight:bold;">OK</span>
			</p>
			<?php }else{ ?>
				<span style="color:red"><?php echo smartyTranslate(array('s'=>'Please fix the following error(s)'),$_smarty_tpl);?>
</span>
			</p>
			<ul>
				<?php  $_smarty_tpl->tpl_vars['value'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['value']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['testsOptional']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['value']->key => $_smarty_tpl->tpl_vars['value']->value){
$_smarty_tpl->tpl_vars['value']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['value']->key;
?>
					<?php if ($_smarty_tpl->tpl_vars['value']->value=='fail'){?>
						<li><?php echo $_smarty_tpl->tpl_vars['key']->value;?>
</li>
					<?php }?>
				<?php } ?>
			</ul>
			<?php }?>
	
	</fieldset>

	<br />
	<fieldset>
		<legend><img src="../img/t/AdminInformation.gif" alt="" /> <?php echo smartyTranslate(array('s'=>'List of changed files'),$_smarty_tpl);?>
</legend>
		<div id="changedFiles"><img src="../img/admin/ajax-loader.gif" /> <?php echo smartyTranslate(array('s'=>'Checking files...'),$_smarty_tpl);?>
</div>
	</fieldset>



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