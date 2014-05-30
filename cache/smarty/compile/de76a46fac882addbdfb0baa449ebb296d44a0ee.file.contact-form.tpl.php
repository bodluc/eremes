<?php /* Smarty version Smarty-3.1.8, created on 2013-02-09 00:00:24
         compiled from "/home/bodo/public_html/dev/themes/default/contact-form.tpl" */ ?>
<?php /*%%SmartyHeaderCode:212108166551158388dee069-85691069%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'de76a46fac882addbdfb0baa449ebb296d44a0ee' => 
    array (
      0 => '/home/bodo/public_html/dev/themes/default/contact-form.tpl',
      1 => 1359997018,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '212108166551158388dee069-85691069',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'confirmation' => 0,
    'base_dir' => 0,
    'img_dir' => 0,
    'alreadySent' => 0,
    'customerThread' => 0,
    'request_uri' => 0,
    'contacts' => 0,
    'contact' => 0,
    'email' => 0,
    'message' => 0,
    'fileupload' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_51158388f3eed2_92082835',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51158388f3eed2_92082835')) {function content_51158388f3eed2_92082835($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/home/bodo/public_html/dev/tools/smarty/plugins/modifier.escape.php';
?>

<?php $_smarty_tpl->_capture_stack[0][] = array('path', null, null); ob_start(); ?><?php echo smartyTranslate(array('s'=>'Contact'),$_smarty_tpl);?>
<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>
<?php echo $_smarty_tpl->getSubTemplate (($_smarty_tpl->tpl_vars['tpl_dir']->value)."./breadcrumb.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


<div class="contact clearfix">
  <div class="left">
     <P><b>///AudioRMS.pl - Sklep Internetowy</b></p>
     <P>Fax : +48 (59) 727 34 66</p>
     <p>Kom.: +48 501 759 039</p>
     <p><b>WWW: </b>www.AudioRMS.pl</p>
     <p><b>E-mail: </b><a href="sklep@audiorms.pl">sklep@audiorms.pl</a></p>
  </div>
  <div class="right">
     <p><b>Dane firmy:</b></p>
     <p>AdsCity Media Łukasz Bodnar</p>
     <p>Wołcza Mała 4</p>
     <p>77-200 Miastko</p>
     <p><b>Raiffeisen Bank:</b></p>
     <p>PL05 1750 0012 0000 0000 2073 1338</p>
   </div>

   </div>
   <br />
<h4><b>Interaktywna Mapa siedziby firmy.</b></h4>
<iframe width="532" height="250" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.pl/maps?f=q&amp;source=s_q&amp;hl=pl&amp;geocode=&amp;q=Miastko&amp;aq=&amp;sll=51.953751,19.134379&amp;sspn=10.772927,19.753418&amp;ie=UTF8&amp;hq=&amp;hnear=Miastko,+bytowski,+Pomorskie&amp;ll=54.0026,16.98265&amp;spn=5.271618,9.876709&amp;t=m&amp;z=7&amp;output=embed"></iframe>
 <br />
 <br />


<?php if (isset($_smarty_tpl->tpl_vars['confirmation']->value)){?>
	<h4 color="red"><?php echo smartyTranslate(array('s'=>'Your message has been successfully sent to our team.'),$_smarty_tpl);?>
</h4>
	<ul class="footer_links">
		<li><a href="<?php echo $_smarty_tpl->tpl_vars['base_dir']->value;?>
"><img class="icon" alt="" src="<?php echo $_smarty_tpl->tpl_vars['img_dir']->value;?>
icon/home.gif"/></a><a href="<?php echo $_smarty_tpl->tpl_vars['base_dir']->value;?>
"><?php echo smartyTranslate(array('s'=>'Home'),$_smarty_tpl);?>
</a></li>
	</ul>
<?php }elseif(isset($_smarty_tpl->tpl_vars['alreadySent']->value)){?>
	<h4><?php echo smartyTranslate(array('s'=>'Your message has already been sent.'),$_smarty_tpl);?>
</h4>
	<ul class="footer_links">
		<li><a href="<?php echo $_smarty_tpl->tpl_vars['base_dir']->value;?>
"><img class="icon" alt="" src="<?php echo $_smarty_tpl->tpl_vars['img_dir']->value;?>
icon/home.gif"/></a><a href="<?php echo $_smarty_tpl->tpl_vars['base_dir']->value;?>
"><?php echo smartyTranslate(array('s'=>'Home'),$_smarty_tpl);?>
</a></li>
	</ul>
<?php }else{ ?>
    <h4><?php echo smartyTranslate(array('s'=>'Customer Service'),$_smarty_tpl);?>
 - <?php if (isset($_smarty_tpl->tpl_vars['customerThread']->value)&&$_smarty_tpl->tpl_vars['customerThread']->value){?><?php echo smartyTranslate(array('s'=>'Your reply'),$_smarty_tpl);?>
<?php }else{ ?><?php echo smartyTranslate(array('s'=>'Contact us'),$_smarty_tpl);?>
<?php }?></h4>
	<p class="bold"><?php echo smartyTranslate(array('s'=>'For questions about an order or for more information about our products'),$_smarty_tpl);?>
.</p>
	<?php echo $_smarty_tpl->getSubTemplate (($_smarty_tpl->tpl_vars['tpl_dir']->value)."./errors.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

	<form action="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['request_uri']->value, 'htmlall', 'UTF-8');?>
" method="post" class="std" enctype="multipart/form-data">
		<fieldset>
			<h3><?php echo smartyTranslate(array('s'=>'Send a message'),$_smarty_tpl);?>
</h3>
			<p class="select" style="display:none">
				<label for="id_contact"><?php echo smartyTranslate(array('s'=>'Subject Heading'),$_smarty_tpl);?>
</label>
			<?php if (isset($_smarty_tpl->tpl_vars['customerThread']->value['id_contact'])){?>
				<?php  $_smarty_tpl->tpl_vars['contact'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['contact']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['contacts']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['contact']->key => $_smarty_tpl->tpl_vars['contact']->value){
$_smarty_tpl->tpl_vars['contact']->_loop = true;
?>
					<?php if ($_smarty_tpl->tpl_vars['contact']->value['id_contact']==$_smarty_tpl->tpl_vars['customerThread']->value['id_contact']){?>
						<input type="text" id="contact_name" name="contact_name" value="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['contact']->value['name'], 'htmlall', 'UTF-8');?>
" readonly="readonly" />
						<input type="hidden" name="id_contact" value="<?php echo $_smarty_tpl->tpl_vars['contact']->value['id_contact'];?>
" />
					<?php }?>
				<?php } ?>
			</p>
			<?php }else{ ?>
				<select id="id_contact" name="id_contact" onchange="showElemFromSelect('id_contact', 'desc_contact')">
				<?php  $_smarty_tpl->tpl_vars['contact'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['contact']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['contacts']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['contact']->key => $_smarty_tpl->tpl_vars['contact']->value){
$_smarty_tpl->tpl_vars['contact']->_loop = true;
?>
					<option value="<?php echo intval($_smarty_tpl->tpl_vars['contact']->value['id_contact']);?>
" <?php if (isset($_POST['id_contact'])&&$_POST['id_contact']==$_smarty_tpl->tpl_vars['contact']->value['id_contact']){?>selected="selected"<?php }?>><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['contact']->value['name'], 'htmlall', 'UTF-8');?>
</option>
				<?php } ?>
				</select>
			</p>

				<?php  $_smarty_tpl->tpl_vars['contact'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['contact']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['contacts']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['contact']->key => $_smarty_tpl->tpl_vars['contact']->value){
$_smarty_tpl->tpl_vars['contact']->_loop = true;
?>
					<p id="desc_contact<?php echo intval($_smarty_tpl->tpl_vars['contact']->value['id_contact']);?>
" class="desc_contact" style="display:none;">
						<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['contact']->value['description'], 'htmlall', 'UTF-8');?>

					</p>
				<?php } ?>
			<?php }?>
			<p class="text">
				<label for="email"><?php echo smartyTranslate(array('s'=>'E-mail address'),$_smarty_tpl);?>
</label>
				<?php if (isset($_smarty_tpl->tpl_vars['customerThread']->value['email'])){?>
					<input type="text" id="email" name="from" value="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['customerThread']->value['email'], 'htmlall', 'UTF-8');?>
" readonly="readonly" />
				<?php }else{ ?>
					<input type="text" id="email" name="from" value="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['email']->value, 'htmlall', 'UTF-8');?>
" />
				<?php }?>
			</p>


		<p class="textarea">
			<label for="message"><?php echo smartyTranslate(array('s'=>'Message'),$_smarty_tpl);?>
</label>
			 <textarea id="message" name="message" rows="15" cols="10"><?php if (isset($_smarty_tpl->tpl_vars['message']->value)){?><?php echo stripslashes(smarty_modifier_escape($_smarty_tpl->tpl_vars['message']->value, 'htmlall', 'UTF-8'));?>
<?php }?></textarea>
		</p>
                <?php if ($_smarty_tpl->tpl_vars['fileupload']->value==1){?>
            <p class="text">
            <label for="fileUpload"><?php echo smartyTranslate(array('s'=>'Attach File'),$_smarty_tpl);?>
</label>
                <input type="hidden" name="MAX_FILE_SIZE" value="2000000" />
                <input type="file" name="fileUpload" id="fileUpload" />
            </p>
        <?php }?>
		<p class="submit">
			<input type="submit" name="submitMessage" id="submitMessage" value="<?php echo smartyTranslate(array('s'=>'Send'),$_smarty_tpl);?>
" class="button_large" onclick="$(this).hide();" />
		</p>
	</fieldset>
</form>
<?php }?>
<?php }} ?>