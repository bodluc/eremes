<?php /* Smarty version Smarty-3.1.8, created on 2012-11-15 12:58:18
         compiled from "/home/bodo/public_html/dev/modules/homeslider/homeslider.tpl" */ ?>
<?php /*%%SmartyHeaderCode:102478914150a4d8dad8e6e6-67248144%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '2a1079d0241fcf6a8047dafcdd2e1a71bbaf2982' => 
    array (
      0 => '/home/bodo/public_html/dev/modules/homeslider/homeslider.tpl',
      1 => 1352592369,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '102478914150a4d8dad8e6e6-67248144',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'p' => 0,
    'homeslider_slides' => 0,
    'homeslider' => 0,
    'slide' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_50a4d8dadef4c4_94168496',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50a4d8dadef4c4_94168496')) {function content_50a4d8dadef4c4_94168496($_smarty_tpl) {?>

<!-- Module HomeSlider -->
<?php if (isset($_smarty_tpl->tpl_vars['p']->value)){?>
<script type="text/javascript">
<?php if (isset($_smarty_tpl->tpl_vars['homeslider_slides']->value)&&count($_smarty_tpl->tpl_vars['homeslider_slides']->value)>1){?>
	<?php if ($_smarty_tpl->tpl_vars['homeslider']->value['loop']==1){?>
		var homeslider_loop = true;
	<?php }else{ ?>
		var homeslider_loop = false;
	<?php }?>
<?php }else{ ?>
	var homeslider_loop = false;
<?php }?>
var homeslider_speed = <?php echo $_smarty_tpl->tpl_vars['homeslider']->value['speed'];?>
;
var homeslider_pause = <?php echo $_smarty_tpl->tpl_vars['homeslider']->value['pause'];?>
;
</script>
<?php }?>
<?php if (isset($_smarty_tpl->tpl_vars['homeslider_slides']->value)){?>
<ul id="homeslider">
<?php  $_smarty_tpl->tpl_vars['slide'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['slide']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['p']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['slide']->key => $_smarty_tpl->tpl_vars['slide']->value){
$_smarty_tpl->tpl_vars['slide']->_loop = true;
?>
	<?php if ($_smarty_tpl->tpl_vars['slide']->value['active']){?>
		<li class="homeslider_li">
        <div class="homeslider">
          <h2><?php echo $_smarty_tpl->tpl_vars['slide']->value['name'];?>
</h2>
        </div>
        <a href="<?php echo $_smarty_tpl->tpl_vars['slide']->value['link'];?>
" title="<?php echo $_smarty_tpl->tpl_vars['slide']->value['name'];?>
"><img src="<?php echo $_smarty_tpl->tpl_vars['slide']->value['path'];?>
" alt="<?php echo $_smarty_tpl->tpl_vars['slide']->value['name'];?>
" title="<?php echo $_smarty_tpl->tpl_vars['slide']->value['name'];?>
" height="<?php echo $_smarty_tpl->tpl_vars['homeslider']->value['height'];?>
"  /></a>
        
        </li>
	<?php }?>
<?php } ?>
</ul>
<?php }?>
<!-- /Module HomeSlider -->
<?php }} ?>