<?php

$module_home = preg_match("/classes/", dirname(__FILE__)) ? str_replace('classes', 'modules/beetailerwidget', dirname(__FILE__)) : dirname(__FILE__);
include_once($module_home.'/../../config/config.inc.php');
/* include_once($module_home.'/../../init.php'); */

class BeetailerWidget extends Module{

  public function __construct(){
		$this->name = 'beetailerwidget';
    $this->tab = 'social_networks';
		$this->version = '1.7';
		$this->author = 'Beeshopy Inc.';
		$this->module_key = '6ff6f3f881e0da65ec38e05706c434bb';

		parent::__construct();
		
		$this->displayName = $this->l('Beetailer Social Widget');
		$this->description = $this->l('Beetailer Widget allows to your users comment and share your products in Facebook, Twitter, and Google+');
		$this->confirmUninstall = $this->l('Are you sure you want to delete the module?');
  }

	public function install(){
		if (!parent::install() OR !$this->registerHook('productfooter') OR !$this->registerHook('header'))
			return false;
			
		Configuration::updateValue('BW_SCHEME', 'light');
		Configuration::updateValue('BW_STYLE', 'padding-top: 20px; clear: both');
		Configuration::updateValue('BW_COMMENTS_WIDTH', '525');
    Configuration::updateValue('BW_COMMENTS_NUMBER', '10');		
    Configuration::updateValue('BW_TWITTER_TEXT', 'Check it out!');
    Configuration::updateValue('BW_OG_TAGS', false);
		
    return true;
  }   
  
	public function uninstall(){
    if(!parent::uninstall() && !$this->unregisterHook('productfooter') && !$this->unregisterHook('header'))
      return false;

    return true;
	}

  public function hookHeader($params){
    /* Adding Open Graph Meta tags */
		global $smarty;
		$id_product = (int)Tools::getValue('id_product');

    /* Load Product */
    if($id_product && Configuration::get('BW_OG_TAGS')){
      list($product, $image_url, $domain, $url) = $this->loadProductInfo($params, $id_product);

      $smarty->assign(array('product' => $product, 'image_link' => $image_url, 'description' => strip_tags($product->description_short), 'url' => $url));
      return $this->display(__FILE__, 'meta_tags.tpl');
    }
  }


  public function hookProductfooter($params){
		$id_product = (int)Tools::getValue('id_product');

    if($id_product){
      list($product, $image_url, $domain, $url) = $this->loadProductInfo($params, $id_product);

      $html = "<div id='beesocial' data-domain='" . urlencode($domain) ."' data-product-id='". $params['product']->id . "' data-url='". urlencode($url) ."' data-comment-width='". Configuration::get('BW_COMMENTS_WIDTH') ."' data-fb-comment-num-post='". Configuration::get('BW_COMMENTS_NUMBER')."' data-twitter-text='". Configuration::get('BW_TWITTER_TEXT') ."' data_shop='prestashop' data-css-style='". Configuration::get('BW_STYLE') ."' data-disable-twitter='".Configuration::get('BW_DISABLE_TWITTER')."' data-disable-like='".Configuration::get('BW_DISABLE_LIKE')."' data-product-image='".urlencode($image_url)."' data-disable-comment='".Configuration::get('BW_DISABLE_COMMENTS')."' platform='prestashop' data-disable-pinterest='".Configuration::get('BW_DISABLE_PINTEREST')."'></div>";
      $html .= "<script src='//www.beetailer.com/javascripts/beetailer.js' type='text/javascript'></script>";
      return $html;
    }
  }

  public function loadProductInfo($params, $id_product){
    $id_lang = (int)$params['cookie']->id_lang;
    $http   = (!empty($_SERVER['HTTPS'])) ? "https://" : "http://";
    $domain = $http   . $_SERVER['SERVER_NAME'];
    $url    = $domain . $_SERVER['REQUEST_URI'];
    $product = new Product($id_product, true, $id_lang);
    $link = new Link();

    /* Main Image */
    $image = Db::getInstance()->getRow(' SELECT `id_image` FROM `'._DB_PREFIX_.'image` WHERE `cover` = 1 AND id_product = '.(int)$id_product);
    $image_url = $link->getImageLink($product->link_rewrite, $id_product.'-'.$image['id_image'], 'medium');
    $image_url = !preg_match("/^http*/", $image_url) ? $domain.$image_url : $image_url; /* Prestashop 1.3 does not include the hostname in the image src */
    return array($product, $image_url, $domain, $url);
  }
  
  private function _postProcess()
	{
		Configuration::updateValue('BW_SCHEME', Tools::getValue('BW_SCHEME'));
		Configuration::updateValue('BW_STYLE', Tools::getValue('BW_STYLE'));
		Configuration::updateValue('BW_DISABLE_LIKE', (int)Tools::getValue('BW_DISABLE_LIKE'));
		Configuration::updateValue('BW_DISABLE_TWITTER', (int)Tools::getValue('BW_DISABLE_TWITTER'));
		Configuration::updateValue('BW_DISABLE_COMMENTS', (int)Tools::getValue('BW_DISABLE_COMMENTS'));
		Configuration::updateValue('BW_COMMENTS_WIDTH', (int)Tools::getValue('BW_COMMENTS_WIDTH'));								
		Configuration::updateValue('BW_COMMENTS_NUMBER', (int)Tools::getValue('BW_COMMENTS_NUMBER'));										
		Configuration::updateValue('BW_TWITTER_TEXT', Tools::getValue('BW_TWITTER_TEXT'));									
		Configuration::updateValue('BW_DISABLE_PINTEREST', Tools::getValue('BW_DISABLE_PINTEREST'));									
		Configuration::updateValue('BW_OG_TAGS', Tools::getValue('BW_OG_TAGS'));									
  }
  
  private function _displayForm()
	{
		$this->_html .=
		'<form style="margin-left:20px" action="'.Tools::htmlentitiesUTF8($_SERVER['REQUEST_URI']).'" method="post">
		  <fieldset>		  <div style="margin:0 0 20px 20px;">
      <legend>'. $this->l('Basic configuration') . '</legend>
      <br/>
		    <label class="t" for="BW_SCHEME">'.$this->l('Color scheme').'</label>
		    <br/>
		    <input type="radio" name="BW_SCHEME" value="light" '.(Configuration::get('BW_SCHEME') == 'light' ? 'checked' : '').'> Light
        <input type="radio" name="BW_SCHEME" value="black" '.(Configuration::get('BW_SCHEME') == 'black' ? 'checked' : '').'> Black
		  
			</div>
			<div style="margin:0 0 20px 20px;">
		    <label class="t" for="BW_STYLE">'.$this->l('Basic style').'</label>
		    <br/>
				<input type="text" name="BW_STYLE" id="BW_STYLE" style="width:250px;vertical-align: middle;" value="'. Configuration::get('BW_STYLE') .'"/>  
			</div>
			
			<div style="margin:0 0 20px 20px;">
				<input type="checkbox" name="BW_DISABLE_LIKE" id="BW_DISABLE_LIKE" style="vertical-align: middle;" value="1" '.(Configuration::get('BW_DISABLE_LIKE') ? 'checked="checked"' : '').' /> <label class="t" for="BW_DISABLE_LIKE">'.$this->l('Disable Facebook like widget').'</label>
			</div>
			<div style="margin:0 0 20px 20px;">
				<input type="checkbox" name="BW_DISABLE_TWITTER" id="BW_DISABLE_TWITTER" style="vertical-align: middle;" value="1" '.(Configuration::get('BW_DISABLE_TWITTER') ? 'checked="checked"' : '').' /> <label class="t" for="BW_DISABLE_TWITTER">'.$this->l('Disable Twitter widget').'</label>
			</div>
 
			<div style="margin:0 0 20px 20px;">
				<input type="checkbox" name="BW_DISABLE_PINTEREST" id="BW_DISABLE_PINTEREST" style="vertical-align: middle;" value="1" '.(Configuration::get('BW_DISABLE_PINTEREST') ? 'checked="checked"' : '').' /> <label class="t" for="BW_DISABLE_PINTEREST">'.$this->l('Disable Pinterest widget').'</label>
			</div>

			<div style="margin:0 0 20px 20px;">
				<input type="checkbox" name="BW_DISABLE_COMMENTS" id="BW_DISABLE_COMMENTS" style="vertical-align: middle;" value="1" '.(Configuration::get('BW_DISABLE_COMMENTS') ? 'checked="checked"' : '').' /> <label class="t" for="BW_DISABLE_TWITTER">'.$this->l('Disable Facebook comment widget').'</label>
			</div></fieldset>
		<fieldset>	
		  <legend>'. $this->l('Facebook comments') . '</legend>
		  
		  <div style="margin:0 0 20px 20px;">
		    <label class="t" for="BW_COMMENTS_WIDTH">'.$this->l('Width').'</label>
		    <br/>
				<input type="text" name="BW_COMMENTS_WIDTH" id="BW_COMMENTS_WIDTH" style="vertical-align: middle;" value="'. Configuration::get('BW_COMMENTS_WIDTH') .'" size="3"/> px 
			</div>
			
			<div style="margin:0 0 20px 20px;">
		    <label class="t" for="BW_COMMENTS_NUMBER">'.$this->l('Number of comments').'</label>
		    <br/>
				<input type="text" name="BW_COMMENTS_NUMBER" id="BW_COMMENTS_NUMBER" style="vertical-align: middle;" value="'. Configuration::get('BW_COMMENTS_NUMBER') .'" size="2"/>  
			</div>
			<div style="margin:0 0 20px 20px;">
				<input type="checkbox" name="BW_OG_TAGS" id="BW_OG_TAGS" style="vertical-align: middle;" value="1" '.(Configuration::get('BW_OG_TAGS') ? 'checked="checked"' : '').' /> <label class="t" for="BW_OG_TAGS">'.$this->l('Add Open Graph tags to your products (<a target=\'_blank\' href=\'http://support.beetailer.com/kb/general/social-widget-how-to-fix-the-wrong-image-shown-in-the-facebook-streamline-when-click-on-the-like-button\'>Read more</a>).').'</label>
			</div></fieldset><fieldset>
		  <legend>'. $this->l('Twitter') . '</legend>
		  
		  <div style="margin:0 0 20px 20px;">
		    <label class="t" for="BW_TWITTER_TEXT">'.$this->l('Text in the tweet').'</label>
		    <br/>
				<textarea name="BW_TWITTER_TEXT" id="BW_TWITTER_TEXT" style="vertical-align: middle;">'.Configuration::get('BW_TWITTER_TEXT') .'</textarea>
			</div>
			</fieldset>
		  <fieldset>	
      <legend>Enjoy and support us!</legend>
      <p>Beetailer Widget is compatible with the <a target="_blank" style="text-decoration:underline;" href="http://addons.prestashop.com/en/social-networks/2931-beetailer-your-online-store-on-facebook.html">Beetailer extension</a> for Facebook integration, so when you install it, you will share all your comments and likes between you storefront on Facebook and your regular store.
      <a target="_blank" style="text-decoration:underline;" href="http://www.beetailer.com?utm_source=beesocial_prestashop&utm_medium=widget_admin&utm_campaign=support_us">Check it out!</a></p>
      <div style="float:left; margin-right: 5px;"><a href="https://twitter.com/share" class="twitter-share-button" data-url="https://www.beetailer.com" data-text="I am using the Beetailer Social widget for Prestashop :-)" data-count="none" data-via="beeshopy" data-related="beeshopy">Tweet</a><script type="text/javascript" src="//platform.twitter.com/widgets.js"></script></div>
      <iframe src="https://www.facebook.com/plugins/like.php?href=https://www.facebook.com/BeeShopyMagento&amp;show_faces=false&amp;layout=button_count&amp;width=650&amp;action=like&amp;font&amp;colorscheme=light&amp;height=21" scrolling="no" frameborder="0" allowTransparency="true" style="float:left;height:21px;"></iframe>
      </fieldset>
     <br/> 
			<input name="btnSubmit" class="button" type="submit"
			value="'. $this->l('Save configuration') .'" />
		</form>';
	}
	
	public function getContent()
	{
	  $this->_html .= '<div style="margin:auto; width:600px;">';
		$this->_html .= '<h2>'.$this->l('Beetailer Social Widget configuration').'</h2>';

		if (Tools::isSubmit('btnSubmit'))
			$this->_postProcess();

		$this->_displayForm();
	  $this->_html .= '</div>';

		return $this->_html;
	}
}
?>
