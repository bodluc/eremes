<?php /* Smarty version Smarty-3.1.8, created on 2013-02-09 14:49:18
         compiled from "/home/bodo/public_html/dev/modules/nivoslider/nivo/nivo.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1305017693511650ff32b9d4-98001634%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1ab24fc49da862e2454ab341418b797b2f426f22' => 
    array (
      0 => '/home/bodo/public_html/dev/modules/nivoslider/nivo/nivo.tpl',
      1 => 1360417748,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1305017693511650ff32b9d4-98001634',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_511650ff36df64_93315076',
  'variables' => 
  array (
    'p' => 0,
    'slide' => 0,
    'm' => 0,
    'logo' => 0,
    'base_dir_ssl' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_511650ff36df64_93315076')) {function content_511650ff36df64_93315076($_smarty_tpl) {?><!-- MODULE NivoSlider Products -->

<div id="tmnivoslider">
    <div id="slider" style="background:<?php echo $_smarty_tpl->tpl_vars['p']->value[0]['link'];?>
">
        <?php  $_smarty_tpl->tpl_vars['slide'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['slide']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['p']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['slide']->key => $_smarty_tpl->tpl_vars['slide']->value){
$_smarty_tpl->tpl_vars['slide']->_loop = true;
?>
            <a href="<?php echo $_smarty_tpl->tpl_vars['slide']->value['link'];?>
"><img src="<?php echo $_smarty_tpl->tpl_vars['slide']->value['path'];?>
" rel="<?php echo $_smarty_tpl->tpl_vars['slide']->value['path'];?>
" alt="" title="#htmlcaption<?php echo $_smarty_tpl->tpl_vars['slide']->value['id_product'];?>
"></a>
        <?php } ?>
        </div>
        <div id="htmlcaption1271" class="nivo-html-caption" >
        <a href="car-audio/car-audio/radia-samochodowe/p/Alpine-INA-W910R">
            <h2>ALPINE</h2>
            <h3>INA-W910R</h3>
            <h4>INA-W910R ACE Navi jest wielofunkcyjnym urządzeniem oferującym najlepszej jakości rozrywkę i nawigację dostępną w samochodzie. ACE Navi ma wszystko czego trzeba, aby w maksymalnym stopniu ułatwić prowadzenie pojazdu i czerpać z tego największą przyjemność.</h4>
            <h5>Już od 4999 PLN</h5>
            </div>
        </a>
         <div id="htmlcaption74" class="nivo-html-caption" >
        <a href="car-audio/glosniki-samochodowe/p/Focal-PC-130">
            <h2>FOCAL</h2>
            <h3>PC 130</h3>
            <h4>głośnik coaxialny 13cm</h4><h4>regulowane nachylenie tweetera</h4><h4>moc maksymalna: 120W</h4><h4>moc nominalna: 60W</h4>
            <h5>Już od 599 PLN</h5>
            </div>
        </a>
        <div id="htmlcaption117" class="nivo-html-caption">
        <a href="/car-audio/wzmacniacze/2-kanalowe/p/DLS-A3">
            <h2>DLS</h2>
            <h3>A3</h3>
            <h4>     Moc przy 4 ohm 2 x 150 W RMS</h4><h4>            Moc przy 2 ohm 2 x 270 W RMS</h4><h4>            Moc przy 1 ohm 2 x 425 W RMS</h4><h4>            Moc w mostku 4 ohm 550 W RMS</h4><h4>            Moc w mostku 2 ohm 870 W RMS</h4><h4>            Moc w mostku 1.33 ohm 1000 W RMS</h4><h4>
            Damping factor > 200</h4>
            <h5>Już od 3299 PLN</h5>
        </a>
        </div>
        <div id="htmlcaption491" class="nivo-html-caption">
        <a href="/car-audio/subwoofery/p/RE-Audio-SE-12-D2">
            <h2>RE AUDIO</h2>
            <h3>SE-12 D2</h3>
            <h4>Średnica 12"/30CM</h4><h4>Moc głośnika 600/1800W</h4><h4>Impedancja 2X2OHM</h4>
            <h5>Już od 1099 PLN</h5>
        </a>
        </div>

    </div>
<div id="tmmanufacturer">
    <div class="block_content">
    <ul>
    <?php  $_smarty_tpl->tpl_vars['logo'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['logo']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['m']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['logo']->key => $_smarty_tpl->tpl_vars['logo']->value){
$_smarty_tpl->tpl_vars['logo']->_loop = true;
?>
        <li><a href="/car-audio/marka/<?php echo $_smarty_tpl->tpl_vars['logo']->value['link_rewrite'];?>
" title="<?php echo $_smarty_tpl->tpl_vars['logo']->value['description'];?>
"><img src="<?php echo $_smarty_tpl->tpl_vars['base_dir_ssl']->value;?>
img/m/<?php echo $_smarty_tpl->tpl_vars['logo']->value['id_manufacturer'];?>
-nivo.jpg" alt="<?php echo $_smarty_tpl->tpl_vars['logo']->value['name'];?>
"></a></li>
    <?php } ?>
                </ul>
    </div>
</div>
<?php }} ?>