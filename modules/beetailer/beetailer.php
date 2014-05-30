<?php
$module_home = preg_match("/classes/", dirname(__FILE__)) ? str_replace('classes', 'modules/beetailer', dirname(__FILE__)) : dirname(__FILE__);
include_once($module_home.'/../../config/config.inc.php');
include_once($module_home.'/product-extended.php');
if(!preg_match("/^1.3.*/", _PS_VERSION_)){ include_once($module_home.'/category-extended.php');}

class Beetailer extends Module{

  public function __construct(){
		$this->name = 'beetailer';
		$this->tab = 'social_networks';
		$this->version = '2.6';
		$this->author = 'Beeshopy Inc.';
		$this->module_key = '7b6112bf8eb74634d3e07607859d50c8';
		parent::__construct();
		
		$this->displayName = $this->l('Beetailer');
		$this->description = $this->l('Beetailer is a service that allows you to integrate your existing online store with Facebook, allowing you to display your products so Facebook users can discover, comment, share, and purchase them.');
		$this->confirmUninstall = $this->l('Are you sure you want to delete the module?');

  }

	public function install(){
		if (!parent::install() OR !$this->registerHook('orderConfirmation') OR !$this->registerHook('footer'))
			return false;

    Configuration::updateValue('CONNECTION_TOKEN', substr(str_shuffle(str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789',5)),0,30));
    return true;
  }   
  
	public function uninstall(){
    if(!parent::uninstall() && !$this->unregisterHook('orderConfirmation') && !$this->unregisterHook('footer'))
      return false;

    return true;
	}

  public function hookFooter($params){
    return "<script src='//www.beetailer.com/javascripts/beetailer.js' type='text/javascript'></script>";
  }

  public function hookOrderConfirmation($params){
    $beetailer_ref = isset($_COOKIE['beetailer_ref']) ? $_COOKIE['beetailer_ref'] : NULL;
    $beetailer_ref_date = isset($_COOKIE['beetailer_ref_date']) ? $_COOKIE['beetailer_ref_date'] : NULL;

    $order = $params["objOrder"];
    $customer = new Customer($order->id_customer);

    $res = '<script type="text/javascript" src=\'//www.beetailer.com/s.js'.
           '?p[order_number]='.$order->id.
           '&p[amount]='.urlencode(sprintf("%.2f", $order->total_paid)).
           '&p[order_date]='.urlencode($order->date_add).
           '&p[email]='.urlencode($customer->email).
           '&p[beetailer_ref]='.urlencode($beetailer_ref).
           '&p[beetailer_ref_date]='.urlencode($beetailer_ref_date).
           '&p[shop_domain]='.urlencode(Configuration::get('PS_SHOP_DOMAIN')).
           '\'></script>';

    setcookie("beetailer_ref", "", time()-3600); 
		return $res;
  }

  /* Security */
  public function isKeyActive($auth_key){
    return WebserviceKeyCore::isKeyActive($auth_key);
  }

  /* Categories */
  public function getCategoryTree(){
    $this->loadEnv();
    /* Extended in order to get all the categories, NOTE, only works on 1.4+ */
    if(preg_match("/^1.3.*/", _PS_VERSION_)){
      return (Category::getRootCategory($_GET["id_lang"])->recurseLiteCategTree(3, 0, $_GET['id_lang']));
    }else{
      $root = new CategoryExtended(Category::getRootCategory()->id,  $_GET["id_lang"]);
      return $root->recurseLiteCategTree(3, 0, $_GET['id_lang']);
    }
  }
  
  /* Products */
  public function getProducts(){
    $this->loadEnv();
    return (ProductExtended::getProducts($_GET['id_category'], NULL, $_GET['id_lang'], $_GET['page'], $_GET['limit']));
  }

  public function getProduct(){
    $this->loadEnv();
    return (ProductExtended::getProducts(NULL, $_GET['id_product'], $_GET['id_lang'], NULL, NULL));
  }

  public function getTotal(){
    return (ProductExtended::getProducts($_GET['id_category'], NULL, $_GET['id_lang'], NULL, NULL, NULL, NULL, true));
  }

  /* Attributes */
  public function getAttributes(){
    return (AttributeCore::getAttributes($_GET['id_lang']));
  }
  
  /* Images */
  public function getImageDir() {
    return (str_replace(_PS_ROOT_DIR_, '', _PS_PROD_IMG_DIR_));
  }  
  
  public function getImageTypes(){
    return (ImageType::getImagesTypes('products'));
  }
   
  /* Currencies */
  public function getDefaultCurrency() {
    if(preg_match("/^1.3.*/", _PS_VERSION_)){
      $res = Currency::getCurrency(intval(Configuration::get('PS_CURRENCY_DEFAULT')));
      $res["id"] = $res["id_currency"]; // Mapping id_currency as id for Beetailer internal issues
    }else{
      $res = Currency::getDefaultCurrency();
    }
    return $res;
  }

  public function getCurrencies() {
    return (Currency::getCurrencies());
  }

  public function getSign() {
    return Currency::getDefaultCurrency()->getSign("right");
  }
  
  /* Languages */
  public function getLanguages() {
    if(preg_match("/^1.3.*/", _PS_VERSION_)){
      return (Language::getLanguages());
    }else{
      return (LanguageCore::getLanguages());
    }
  }
  
  public function getDefaultLanguage() {
    return (new Language(Configuration::get('PS_LANG_DEFAULT')));
  }

  public function storeSettings() {
    return array('taxes_included' =>  Configuration::get('PS_TAX'));
  }

  public function getVersion(){
    return _PS_VERSION_;
  }

  public function getModuleVersion(){
    return $this->version;
  }
  
  public function toJson($results) {
    echo json_encode($results);
  }

  /* User content form */
	public function getContent()
	{
		$this->_displayForm();
		return $this->_html;
	}

  private function _displayForm()
	{
		$this->_html .=
		  '<div style="width: 500px; margin:0 auto 30px auto;"><h2>'. $this->l("Congratulations, you have installed the Beetailer module!") . '</h2>
      <p>Now you need to</p>
      <p>
        <h3>1 - Signup in Beetailer.com following this <a style="text-decoration:underline;" href="https://www.beetailer.com/signup" target="_blank">link</a>.</h3>
        <h3>2 - Once you registered click on "Add new Facebook Store" button and follow the installation wizard.</h3>';

    if(preg_match("/^1.3.*/", _PS_VERSION_))
      $this->_html .= '
        <p>During the installation you will be asked for a Webservice Key, here you have yours, copy it and paste it in the form.</p>
        </p>
        <br/><p style="text-align:center; padding:15px; width:400px; margin:auto; margin-bottom: 15px; border:1px solid #268CCD; font-size: 20px;">'. Configuration::get('CONNECTION_TOKEN'). '</p>';

    $this->_html .= '
      <h3>3 - Then just select the categories you want to publish on Facebook.</h3>
      <h3>4 - (optional) Check out <a style="text-decoration:underline;" href="http://addons.prestashop.com/en/social-networks/2928-beetailer-widget-facebook-plugins-and-twitter-integration.html" target="_blank">Beetailer social widget</a>, another cool extension from the Beetailer Team :-)</h3>
      <h3>5 - Enjoy and support us!</h3>
      <div style="float:left; margin-right: 5px;"><a href="https://twitter.com/share" class="twitter-share-button" data-url="https://www.beetailer.com" data-text="I have just installed my Facebook store using Beetailer :-)" data-count="none" data-via="beeshopy" data-related="beeshopy">Tweet</a><script type="text/javascript" src="//platform.twitter.com/widgets.js"></script></div>
      <iframe src="https://www.facebook.com/plugins/like.php?href=https://www.facebook.com/BeeShopyMagento&amp;show_faces=false&amp;layout=button_count&amp;width=650&amp;action=like&amp;font&amp;colorscheme=light&amp;height=21" scrolling="no" frameborder="0" allowTransparency="true" style="float:left;"></iframe>
      </div>';
	}

  /* Calling init.php on-demand fix the payment tab problem */
  private function loadEnv()
	{
    $module_home = preg_match("/classes/", dirname(__FILE__)) ? str_replace('classes', 'modules/beetailer', dirname(__FILE__)) : dirname(__FILE__);
    if(!preg_match("/^1.3.*/", _PS_VERSION_))
      include_once($module_home.'/../../init.php'); // Only added if Prestashop >~ 1.4.x
  }
}

/* Execution */
$beetailer = new Beetailer();

/* JSON headers */
if(isset($_GET['format']) && $_GET['format'] == 'json'){
  ini_set('display_errors', 0);
  header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
  header("Cache-Control: no-cache, must-revalidate" );
  header("Pragma: no-cache" );
  header("Content-type: application/json");
  /* header("Content-Disposition:attachment;filename=response.json"); */
}

if(isset($_GET['method'])){
  /* Authentication, prestashop 1.3.x does not include a webservice key */
  if(preg_match("/^1.3.*/", _PS_VERSION_)){
    if(Configuration::get('CONNECTION_TOKEN') != $_GET['auth_key'])
      return;
  }else{
    if(!$beetailer->isKeyActive($_GET['auth_key'])){
      echo "Authentication error";
      return;
    }
  }
  $method = $_GET['method'];
  eval("\$results = \$beetailer->\$method();");
  $beetailer->toJson($results);
} 
?>
