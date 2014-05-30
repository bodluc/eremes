<?php /* Smarty version Smarty-3.1.8, created on 2013-02-09 14:37:03
         compiled from "/home/bodo/public_html/dev/themes/default/footer.tpl" */ ?>
<?php /*%%SmartyHeaderCode:793322857511650ffb60e74-71519831%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '68e7ebfc536af4448d59177eb50fbca4c06b8170' => 
    array (
      0 => '/home/bodo/public_html/dev/themes/default/footer.tpl',
      1 => 1360012827,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '793322857511650ffb60e74-71519831',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'content_only' => 0,
    'HOOK_SLIDER' => 0,
    'HOOK_FOOTER' => 0,
    'tpl_dir' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_511650ffba3a81_24889891',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_511650ffba3a81_24889891')) {function content_511650ffba3a81_24889891($_smarty_tpl) {?>

		<?php if (!$_smarty_tpl->tpl_vars['content_only']->value){?>
				</div>


			</div>



<!-- Footer -->

		</div>
	<?php }?>
    <?php echo $_smarty_tpl->tpl_vars['HOOK_SLIDER']->value;?>

    <div id="footer" class="grid_9 alpha omega clearfix">
      <div class="center">
                <?php echo $_smarty_tpl->tpl_vars['HOOK_FOOTER']->value;?>


                <br class="clear"/>
      </div>
      <p>© 2012 ///AudioRMS.pl - Wszelkie prawa zastrzeżone. Projekt i realizacja: <a href="http://www.ads-city.pl">ADS-MEDIA</a></p>

</div>
    <?php echo $_smarty_tpl->getSubTemplate (($_smarty_tpl->tpl_vars['tpl_dir']->value)."/rms/footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

	</body>
</html>
<?php }} ?>