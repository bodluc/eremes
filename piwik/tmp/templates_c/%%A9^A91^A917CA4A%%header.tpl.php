<?php /* Smarty version 2.6.26, created on 2013-01-19 03:51:18
         compiled from Login/templates/header.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'translate', 'Login/templates/header.tpl', 5, false),array('modifier', 'escape', 'Login/templates/header.tpl', 9, false),)), $this); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr">
<head>
	<title><?php if (! $this->_tpl_vars['isCustomLogo']): ?>Piwik &rsaquo; <?php endif; ?><?php echo ((is_array($_tmp='Login_LogIn')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="shortcut icon" href="plugins/CoreHome/templates/images/favicon.ico" />
	<link rel="stylesheet" type="text/css" href="plugins/Login/templates/login.css" />
	<meta name="description" content="<?php echo ((is_array($_tmp=((is_array($_tmp='General_OpenSourceWebAnalytics')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" />
	


<?php if (isset ( $this->_tpl_vars['forceSslLogin'] ) && $this->_tpl_vars['forceSslLogin']): ?>
<?php echo '
	<script type="text/javascript">
		if(window.location.protocol !== \'https:\') {
			var url = window.location.toString();
			url = url.replace(/^http:/, \'https:\');
			window.location.replace(url);
		}
	</script>
'; ?>

<?php endif; ?>
<?php echo '
	<script type="text/javascript">
		function focusit() {
			var formLogin = document.getElementById(\'form_login\');
			if(formLogin)
			{
				formLogin.focus();
			}
		}
		window.onload = focusit;
	</script>
'; ?>

	<script type="text/javascript" src="libs/jquery/jquery.js"></script>
	<script type="text/javascript" src="plugins/Login/templates/login.js"></script>
<?php if (((is_array($_tmp='General_LayoutDirection')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)) == 'rtl'): ?>
<link rel="stylesheet" type="text/css" href="themes/default/rtl.css" />
<?php endif; ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "CoreHome/templates/iframe_buster_header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</head>
<body class="login">
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "CoreHome/templates/iframe_buster_body.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "default/ie6.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<div id="logo">
	<?php if (! $this->_tpl_vars['isCustomLogo']): ?><a href="http://piwik.org" alt="Piwik" title="<?php echo $this->_tpl_vars['linkTitle']; ?>
"><?php endif; ?>
		<img src='<?php echo $this->_tpl_vars['logoLarge']; ?>
' title="<?php echo $this->_tpl_vars['linkTitle']; ?>
" width='200' style='margin-right:20px' />
		<?php if (! $this->_tpl_vars['isCustomLogo']): ?><div class="description"># <?php echo $this->_tpl_vars['linkTitle']; ?>
</div>
		<?php else: ?><?php ob_start(); ?>
				<i><a href="http://piwik.org/" target="_blank"><?php echo $this->_tpl_vars['linkTitle']; ?>
</a></i>
				<?php $this->_smarty_vars['capture']['poweredByPiwik'] = ob_get_contents(); ob_end_clean(); ?>
		<?php endif; ?>
	<?php if (! $this->_tpl_vars['isCustomLogo']): ?></a><?php endif; ?>
	</div>