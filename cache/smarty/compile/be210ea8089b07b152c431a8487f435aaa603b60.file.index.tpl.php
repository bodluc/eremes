<?php /* Smarty version Smarty-3.1.8, created on 2013-01-08 04:25:02
         compiled from "/home/bodo/public_html/dev/modules/homeslider/plus/index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:131482700050eb918e742d59-87594271%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'be210ea8089b07b152c431a8487f435aaa603b60' => 
    array (
      0 => '/home/bodo/public_html/dev/modules/homeslider/plus/index.tpl',
      1 => 1355497577,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '131482700050eb918e742d59-87594271',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'p' => 0,
    'slide' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_50eb918e755959_14473636',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50eb918e755959_14473636')) {function content_50eb918e755959_14473636($_smarty_tpl) {?>
 <div id="slider-wrapper" style="margin:0 auto">
			<div id="slider3" >
             <?php  $_smarty_tpl->tpl_vars['slide'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['slide']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['p']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['slide']->key => $_smarty_tpl->tpl_vars['slide']->value){
$_smarty_tpl->tpl_vars['slide']->_loop = true;
?>
				<div data-title="Quote" class="quote" >
					<div style="width:535px;height:369px;margin:0 auto">
                    <a href="<?php echo $_smarty_tpl->tpl_vars['slide']->value['link'];?>
"><img src="<?php echo $_smarty_tpl->tpl_vars['slide']->value['path'];?>
"/></a>
                     </div>
				</div>
         <?php } ?>
    </div>
</div> 
<?php }} ?>