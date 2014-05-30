<?php /* Smarty version 2.6.26, created on 2013-02-06 22:11:34
         compiled from /home/bodo/public_html/dev/piwik/plugins/UsersManager/templates/UsersManager.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'loadJavascriptTranslations', '/home/bodo/public_html/dev/piwik/plugins/UsersManager/templates/UsersManager.tpl', 2, false),array('function', 'ajaxErrorDiv', '/home/bodo/public_html/dev/piwik/plugins/UsersManager/templates/UsersManager.tpl', 52, false),array('function', 'ajaxLoadingDiv', '/home/bodo/public_html/dev/piwik/plugins/UsersManager/templates/UsersManager.tpl', 53, false),array('modifier', 'translate', '/home/bodo/public_html/dev/piwik/plugins/UsersManager/templates/UsersManager.tpl', 31, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "CoreAdminHome/templates/header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php echo smarty_function_loadJavascriptTranslations(array('plugins' => 'UsersManager'), $this);?>


<?php echo '
<style type="text/css">
.dialog {
	display: none;
	padding:20px 10px;
	color:#7A0101;
	cursor:wait;
	font-size:1.2em;
	font-weight:bold;
	text-align:center;
}
.editable:hover, .addrow:hover, .updateAccess:hover, .accessGranted:hover, .adduser:hover, .edituser:hover, .deleteuser:hover, .updateuser:hover, .cancel:hover{
	cursor: pointer;
}
.addrow {
	padding:1em;
	font-weight:bold;
}
.addrow a {
	text-decoration: none;
}
.addrow img {
	vertical-align: middle;
}
</style>
'; ?>


<h2><?php echo ((is_array($_tmp='UsersManager_ManageAccess')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</h2>
<div id="sites">
	<section class="sites_selector_container">
		<p><?php echo ((is_array($_tmp='UsersManager_MainDescription')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</p>
		<div style="display:inline-block;margin-top:5px;"><?php echo ((is_array($_tmp='UsersManager_Sites')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
: </div>
	
		<?php ob_start(); ?>
			<strong><?php echo ((is_array($_tmp='UsersManager_ApplyToAllWebsites')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</strong>
		<?php $this->_smarty_vars['capture']['applyAllSitesText'] = ob_get_contents();  $this->assign('applyAllSitesText', ob_get_contents());ob_end_clean(); ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "CoreHome/templates/sites_selection.tpl", 'smarty_include_vars' => array('siteName' => $this->_tpl_vars['defaultReportSiteName'],'idSite' => $this->_tpl_vars['idSiteSelected'],'allSitesItemText' => $this->_tpl_vars['applyAllSitesText'],'allWebsitesLinkLocation' => 'top')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	</section>
	<?php echo '<script type="text/javascript">
		window.autocompleteOnNewSiteSelect = function(siteId, siteName)
		{
			switchSite(siteId, siteName, false /* do not show main ajax loading animation */);
		};
	</script>'; ?>

</div>

<?php echo smarty_function_ajaxErrorDiv(array(), $this);?>

<?php echo smarty_function_ajaxLoadingDiv(array(), $this);?>


<div class="entityContainer" style='width:600px'>
	<table class="entityTable dataTable" id="access" style="display:inline-table;width:500px;">
		<thead>
		<tr>
			<th class='first'><?php echo ((is_array($_tmp='UsersManager_User')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</th>
			<th><?php echo ((is_array($_tmp='UsersManager_Alias')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</th>
			<th><?php echo ((is_array($_tmp='UsersManager_PrivNone')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</th>
			<th><?php echo ((is_array($_tmp='UsersManager_PrivView')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</th>
			<th><?php echo ((is_array($_tmp='UsersManager_PrivAdmin')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</th>
		</tr>
		</thead>
		
		<tbody>
		<?php $this->assign('accesValid', "<img src='plugins/UsersManager/images/ok.png' class='accessGranted' />"); ?>
		<?php $this->assign('accesInvalid', "<img src='plugins/UsersManager/images/no-access.png' class='updateAccess' />"); ?>
		<?php $_from = $this->_tpl_vars['usersAccessByWebsite']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['login'] => $this->_tpl_vars['access']):
?>
		<tr>
			<td id='login'><?php echo $this->_tpl_vars['login']; ?>
</td>
			<td><?php echo $this->_tpl_vars['usersAliasByLogin'][$this->_tpl_vars['login']]; ?>
</td>
			<td id='noaccess'><?php if ($this->_tpl_vars['access'] == 'noaccess' && $this->_tpl_vars['idSiteSelected'] != 'all'): ?><?php echo $this->_tpl_vars['accesValid']; ?>
<?php else: ?><?php echo $this->_tpl_vars['accesInvalid']; ?>
<?php endif; ?>&nbsp;</td>
			<td id='view'><?php if ($this->_tpl_vars['access'] == 'view' && $this->_tpl_vars['idSiteSelected'] != 'all'): ?><?php echo $this->_tpl_vars['accesValid']; ?>
<?php else: ?><?php echo $this->_tpl_vars['accesInvalid']; ?>
<?php endif; ?>&nbsp;</td>
			<td id='admin'>
				<?php if ($this->_tpl_vars['login'] == 'anonymous'): ?>
					N/A
				<?php else: ?>
					<?php if ($this->_tpl_vars['access'] == 'admin' && $this->_tpl_vars['idSiteSelected'] != 'all'): ?><?php echo $this->_tpl_vars['accesValid']; ?>
<?php else: ?><?php echo $this->_tpl_vars['accesInvalid']; ?>
<?php endif; ?>&nbsp;
				<?php endif; ?>
			</td>
		</tr>
		<?php endforeach; endif; unset($_from); ?>
		</tbody>
	</table>
	<div id="accessUpdated" class="ajaxSuccess" style="display:none;vertical-align:top;"><?php echo ((is_array($_tmp='General_Done')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
!</div>
</div>

<div class="ui-confirm" id="confirm">
	<h2><?php echo ((is_array($_tmp='UsersManager_ChangeAllConfirm')) ? $this->_run_mod_handler('translate', true, $_tmp, "<span id='login'></span>") : smarty_modifier_translate($_tmp, "<span id='login'></span>")); ?>
</h2>
    <input role="yes" type="button" value="<?php echo ((is_array($_tmp='General_Yes')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
" />
    <input role="no" type="button" value="<?php echo ((is_array($_tmp='General_No')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
" />
</div> 

<?php if ($this->_tpl_vars['userIsSuperUser']): ?>
    <div class="ui-confirm" id="confirmUserRemove">
        <h2></h2>
        <input role="yes" type="button" value="<?php echo ((is_array($_tmp='General_Yes')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
" />
        <input role="no" type="button" value="<?php echo ((is_array($_tmp='General_No')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
" />
    </div> 
    <div class="ui-confirm" id="confirmPasswordChange">
        <h2><?php echo ((is_array($_tmp='UsersManager_ChangePasswordConfirm')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</h2>
        <input role="yes" type="button" value="<?php echo ((is_array($_tmp='General_Yes')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
" />
        <input role="no" type="button" value="<?php echo ((is_array($_tmp='General_No')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
" />
    </div> 

	<br />
	<h2><?php echo ((is_array($_tmp='UsersManager_UsersManagement')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</h2>
	<p><?php echo ((is_array($_tmp='UsersManager_UsersManagementMainDescription')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
 
	<?php echo ((is_array($_tmp='UsersManager_ThereAreCurrentlyNRegisteredUsers')) ? $this->_run_mod_handler('translate', true, $_tmp, "<b>".($this->_tpl_vars['usersCount'])."</b>") : smarty_modifier_translate($_tmp, "<b>".($this->_tpl_vars['usersCount'])."</b>")); ?>
</p>

	<?php echo smarty_function_ajaxErrorDiv(array('id' => 'ajaxErrorUsersManagement'), $this);?>

	<?php echo smarty_function_ajaxLoadingDiv(array('id' => 'ajaxLoadingUsersManagement'), $this);?>


	<div class="entityContainer" style='margin-bottom:50px'>
	<table class="entityTable dataTable" id="users">
		<thead>
			<tr>
				<th><?php echo ((is_array($_tmp='General_Username')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</th>
				<th><?php echo ((is_array($_tmp='UsersManager_Password')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</th>
				<th><?php echo ((is_array($_tmp='UsersManager_Email')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</th>
				<th><?php echo ((is_array($_tmp='UsersManager_Alias')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</th>
				<th>token_auth</th>
				<th><?php echo ((is_array($_tmp='General_Edit')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</th>
				<th><?php echo ((is_array($_tmp='General_Delete')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</th>
			</tr>
		</thead>
		
		<tbody>
			<?php $_from = $this->_tpl_vars['users']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['i'] => $this->_tpl_vars['user']):
?>
				<?php if ($this->_tpl_vars['user']['login'] != 'anonymous'): ?>
				<tr class="editable" id="row<?php echo $this->_tpl_vars['i']; ?>
">
					<td id="userLogin" class="editable"><?php echo $this->_tpl_vars['user']['login']; ?>
</td>
					<td id="password" class="editable">-</td>
					<td id="email" class="editable"><?php echo $this->_tpl_vars['user']['email']; ?>
</td>
					<td id="alias" class="editable"><?php echo $this->_tpl_vars['user']['alias']; ?>
</td>
					<td id="token_auth"><?php echo $this->_tpl_vars['user']['token_auth']; ?>
</td>
					<td><span class="edituser link_but" id="row<?php echo $this->_tpl_vars['i']; ?>
"><img title="<?php echo ((is_array($_tmp='General_Edit')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
" src='themes/default/images/ico_edit.png' /> <?php echo ((is_array($_tmp='General_Edit')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
 </span></td>
					<td><span class="deleteuser link_but" id="row<?php echo $this->_tpl_vars['i']; ?>
"><img title="<?php echo ((is_array($_tmp='General_Delete')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
" src='themes/default/images/ico_delete.png' /> <?php echo ((is_array($_tmp='General_Delete')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
 </span></td>
				</tr>
				<?php endif; ?>
			<?php endforeach; endif; unset($_from); ?>
		</tbody>
	</table>
	<div class="addrow"><img src='plugins/UsersManager/images/add.png' /> <?php echo ((is_array($_tmp='UsersManager_AddUser')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</div>
	</div>	
<?php endif; ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "CoreAdminHome/templates/footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>