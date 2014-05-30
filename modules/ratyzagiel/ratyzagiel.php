<?php
/*
*	Moduł płatności ratalnych Żagiel S.A.
*	
*	Moduł nie jest moją własnością, lecz modyfikacją ogólnodostępnego modułu na licencji Opens-source.
*	Niestety, nie wiem kto jest oryginalnym twórcą, gdyż moduł który znalazłem w internecie był pozbawiony jakichkolwiek komentarzy o autorstwie.
*	Moduł przyłączyłem do mojego małego projektu Barghest ponieważ nie był on aktualizowany od prawie roku, nie ma wersji 1.4+ i posiadał błędy. Więcej na http://pl.seigi.eu/barghest.html
*	
*	@author (Modyfikacji/Updateu) Grzegorz Zawadzki <kontakt@seigi.eu>
*	@link http://pl.seigi.eu/barghest.html Zobacz więcej na temat projektu, aktualizacji jak i innych modułów
*	@license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*	@version 1.0
*
*/

class RatyZagiel extends PaymentModule
{
	private	$_html = '';
	private $_postErrors = array();

	public function __construct()
	{
		$this->name = 'ratyzagiel';
		//$this->tab = 'Payment';
		$this->tab = 'payments_gateways';
		$this->version_rw = '1.4.2';
		$this->version = $this->version_rw.' <a style="color: #9BBD23;" href="http://pl.seigi.eu/barghest.html" target="_blank">Barghest &#10148;</a>';
		$this->author = 'seigi.eu (Barghest)';
		
		parent::__construct();

		$this->page = basename(__FILE__, '.php');
		$this->displayName = $this->l('Zakupy na raty Żagiel');
		$this->description = $this->l('Umożliwia zakupy na raty dzięki systemowi Żagiel S.A');
	}

	public function install()
	{

		

		if (!parent::install() 
		
			OR !Configuration::updateValue('RATYZAGIEL_SHOP_ID', '28019999')
			OR !Configuration::updateValue('RATYZAGIEL_BLOCK_IMAGE', '1')
			OR !Configuration::updateValue('RATYZAGIEL_BLOCK_TITLE', 'Raty Żagiel')	
			OR !Configuration::updateValue('RATYZAGIEL_BLOCK', 'left')		
			OR !Configuration::updateValue('RATYZAGIEL_SYM', 'tak')	
			OR !Configuration::updateValue('RATYZAGIEL_BARGHEST_NOTIFY', 'tak')	
			OR !$this->registerHook('payment') 
			OR !$this->registerHook('paymentReturn') 
			OR !$this->registerHook('rightColumn')
			OR !$this->registerHook('leftColumn')
			OR !$this->registerHook('ShoppingCartExtra')
			OR !$this->registerHook('productActions')
			OR !$this->registerHook('backOfficeHome')
			)
			
			return false;
		return true;
		
	}

	public function uninstall()
	{
		if (
		!Configuration::deleteByName('RATYZAGIEL_SHOP_ID') 
		OR !Configuration::deleteByName('RATYZAGIEL_BLOCK_IMAGE')
		OR !Configuration::deleteByName('RATYZAGIEL_BLOCK')
		OR !Configuration::deleteByName('RATYZAGIEL_BLOCK_TITLE')
		OR !Configuration::deleteByName('RATYZAGIEL_SYM')
		OR !Configuration::deleteByName('RATYZAGIEL_BARGHEST_NOTIFY')
		OR !parent::uninstall())
			return false;
		return true;
	}

	public function getContent()
	{
		$this->_html = '<h2>Zakupy na raty z systemem ratalnym Żagiel S.A.</h2>';
		
		
		if (isset($_POST['submitRatyZagiel']))
		{
			if (empty($_POST['ratyzagiel_shop_id']))
				$this->_postErrors[] = $this->l('Musisz podać id sklepu w dla Żagiel S.A.');
			elseif (!Validate::isUnsignedInt($_POST['ratyzagiel_shop_id']))
				$this->_postErrors[] = $this->l('Numer sklepu musi być liczbą całkowitą.');

			$ratyzagiel_block = $_POST['ratyzagiel_block'];
			$ratyzagiel_block_image = $_POST['ratyzagiel_block_image'];
			$ratyzagiel_block_title = $_POST['ratyzagiel_block_title'];
			$ratyzagiel_sym = $_POST['ratyzagiel_sym'];
			$barghest = $_POST['ratyzagiel_barghest_notify'];

			if (!sizeof($this->_postErrors))
			{
				Configuration::updateValue('RATYZAGIEL_SHOP_ID', intval($_POST['ratyzagiel_shop_id']));
				Configuration::updateValue('RATYZAGIEL_BLOCK', $ratyzagiel_block);
				Configuration::updateValue('RATYZAGIEL_BLOCK_IMAGE', $ratyzagiel_block_image);
				Configuration::updateValue('RATYZAGIEL_BLOCK_TITLE', $ratyzagiel_block_title);
				Configuration::updateValue('RATYZAGIEL_SYM', $ratyzagiel_sym);
				Configuration::updateValue('RATYZAGIEL_BARGHEST_NOTIFY', $barghest);
				$this->displayConf();
			}
			else
				$this->displayErrors();
		}

		$this->displayInformation();
		$this->displayFormSettings();
		return $this->_html;
	}

	public function displayConf()
	{
		$this->_html .= '
		<div class="conf confirm">
			<img src="../img/admin/ok.gif" alt="'.$this->l('Potwierdzenie').'" />
			'.$this->l('Ustawienia zapisane').'
		</div>';
	}

	public function displayErrors()
	{
		$nbErrors = sizeof($this->_postErrors);
		$this->_html .= '
		<div class="alert error">
			<h3>'.($nbErrors > 1 ? $this->l('Wystąpiły') : $this->l('Wystąpił')).' '.$nbErrors.' '.($nbErrors > 1 ? $this->l('błędy') : $this->l('błąd')).'</h3>
			<ol>';
		foreach ($this->_postErrors AS $error)
			$this->_html .= '<li>'.$error.'</li>';
		$this->_html .= '
			</ol>
		</div>';
	}
	
	
	public function displayInformation()
	{
		$this->_html .= '
		<fieldset style="background: #fff; margin: 10px 0;">
		<legend><img src="../img/admin/details.gif" />'.$this->l('Informacje').'</legend>
		<p><img src="../modules/ratyzagiel/images/logo.gif" /></p>
		
		<p><b>'.$this->l('Moduł umożliwia zakupy na raty za pomocą systemu Żagiel S.A.').'</b></p>
		<p>'.$this->l('Przed użyciem modułu skonfiguruj swoje ustawienia dla Żagiel S.A. za pomocą opcji dostępnych poniżej.').'</p>
		<p>'.$this->l('Moduł należy do serii.').'</p>
		</fieldset>';
		
	}

	public function displayFormSettings()
	{
		// PRESTAHOME CORE STYLE
		$this->_html .='
		
		<style type="text/css">
		form.prestahome * {
			text-align: left;
		}
		form.prestahome dl {
			float: left;
			margin: 10px 0;
		}
		form.prestahome dt label {
			width: 500px; float: left; clear: both; margin: 0;
		}
		form.prestahome dd {
			float: left; clear: both; margin: 5px 0;	
		}
		form.prestahome .button {
			float: left; clear: both; cursor: pointer; overflow: hidden;
		}
		form.prestahome input[type=text] {
			min-width: 200px; padding: 5px;
		}
		</style>
		
		';
		// PRESTAHOME CORE STYLE END
		
		$shop_id 					= Configuration::get('RATYZAGIEL_SHOP_ID');
		$block 						= Configuration::get('RATYZAGIEL_BLOCK');
		$block_image				= Configuration::get('RATYZAGIEL_BLOCK_IMAGE');
		$ratyzagiel_block_title 	= Configuration::get('RATYZAGIEL_BLOCK_TITLE');
		
		$left = '';
		$right = '';
		
		$block == 'left' ? $left = 'selected="selected"' : $right = 'selected="selected"';
		
		if($shop_id == '28019999') 
		{
			$this->_html .= '<div class="warning">Uwaga! Używasz domyślnego indentyfikatora sklepu, który służy tylko i wyłącznie do testowania płatności ratalnej!</div>';
		}

		$this->_html .= '
		<form action="'.$_SERVER['REQUEST_URI'].'" method="post" class="prestahome">
		<fieldset>
			<legend><img src="../img/admin/contact.gif" />'.$this->l('Ustawienia').'</legend>
			<dl>
				<dt><label>'.$this->l('Twoj identyfikator sklepu w systemie ratalnym Żagiel S.A.').'</label></dt>
				<dd><input type="text" size="10" name="ratyzagiel_shop_id" value="'.$shop_id.'" /></dd>
			</dl>		
			<dl>
				<dt><label>'.$this->l('W której kolumnie wyświetlić logo Raty Żagiel?').'</label></dt>
				<dd>
					<select name="ratyzagiel_block">
						<option value="right" '.$right.'>Prawej</option>
						<option value="left" '.$left.'>Lewej</option>
					</select>
				</dd>
			</dl>	
			<dl>
				<dt><label>'.$this->l('Czy wyświetlić ikonkę &quot;Symuluj raty&quot; na stronie produktu?').'</label></dt>
				<dd>
					<select name="ratyzagiel_sym">
						<option value="tak" '.(Configuration::get('RATYZAGIEL_SYM') == 'tak' ? 'selected="selected"' : '').'>Tak</option>
						<option value="nie" '.(Configuration::get('RATYZAGIEL_SYM') == 'nie' ? 'selected="selected"' : '').'>Nie</option>
					</select>
				</dd>
			</dl>	
			<dl>
				<dt><label>'.$this->l('Czy notyfikacje o aktualizacjach mają być widoczne?').'</label></dt>
				<dd>
					<select name="ratyzagiel_barghest_notify">
						<option value="tak" '.(Configuration::get('RATYZAGIEL_BARGHEST_NOTIFY') == 'tak' ? 'selected="selected"' : '').'>Tak</option>
						<option value="nie" '.(Configuration::get('RATYZAGIEL_BARGHEST_NOTIFY') == 'nie' ? 'selected="selected"' : '').'>Nie</option>
					</select>
				</dd>
			</dl>	
			
			<dl>
				<dt><label>'.$this->l('Którego loga prezentującego płatność Raty Żagiel użyć?').'</dt>
				<dd>
					<p style="float: left;">
						<label style="text-align: center;"><img src="../modules/ratyzagiel/logos/1.png" />
						<br />
						<input type="radio" name="ratyzagiel_block_image" value="1" 
						'.(Configuration::get('RATYZAGIEL_BLOCK_IMAGE') == '1' ? 'checked="checked"' : '').'
						></label>
					</p>
					<p style="float: left;">
						<label style="text-align: center;"><img src="../modules/ratyzagiel/logos/2.png" />
						<br />
						<input type="radio" name="ratyzagiel_block_image" value="2"
						'.(Configuration::get('RATYZAGIEL_BLOCK_IMAGE') == '2' ? 'checked="checked"' : '').'
						></label>
					</p>
				</dd>
				</label>
			</dl>	
			<dl>
						
</dl>	
			<dl>
				<dt><label>'.$this->l('Nagłówek bloku Raty Żagiel').'</label></dt>
				<dd><input type="text" size="10" name="ratyzagiel_block_title" value="'.$ratyzagiel_block_title.'" /></dd>
			</dl>	
			<input type="submit" name="submitRatyZagiel" value="'.$this->l('Zapisz zmiany').'" class="button" />
		</fieldset>
		</form>
		';
	}

	public function hookPayment($params)
	{
		global $smarty; 		
		$smarty->assign(array(
				'total_order' => $params['cart']->getOrderTotal()
			));
		return $this->display(__FILE__, 'ratyzagiel.tpl');
	}

	public function execPayment($cart)
	{
		global $smarty, $cookie, $cart;
	
		$address = new Address(intval($cart->id_address_invoice));
		$customer = new Customer(intval($cart->id_customer));
	
		$ratyzagiel_shop_id = Configuration::get('RATYZAGIEL_SHOP_ID');
	
		if (!Validate::isUnsignedInt($ratyzagiel_shop_id))
			return $this->l('Błąd płatności: (nieprawidłowy identyfikator sklepu)');

		if (!Validate::isLoadedObject($address) OR !Validate::isLoadedObject($customer))
			return $this->l('Błąd płatności: (nieprawidłowy adres lub klient)');


		$products_inputs = '';
		$produkty = $cart->getProducts(true);
		$razem = sizeof($produkty)+1;
		
		for ($i=0, $nr=1; $i<sizeof($produkty); $i++, $nr++) {
			$products_inputs .= '
	<input name="idTowaru' . $nr . '" readonly="readonly" type="hidden" value="' . $produkty[$i]['id_product'] . '">
	<input name="nazwaTowaru' . $nr . '" readonly="readonly" type="hidden" value="' . $produkty[$i]['name'] .'">
	<input name="wartoscTowaru' . $nr . '" readonly="readonly" type="hidden" value="' . $produkty[$i]['price_wt'] . '">
	<input name="liczbaSztukTowaru' . $nr . '" readonly="readonly" type="hidden" value="' . $produkty[$i]['cart_quantity'] . '">
	<input name="jednostkaTowaru' . $nr . '" readonly="readonly" type="hidden" value="szt">
	';
		} // eof:for
		
			$products_inputs .= '
			
	<!-- Koszty wysyłki jako produkt -->
	<input type="hidden" name="idTowaru'.$razem.'" readonly="readonly" value="KosztPrzesylki" />
	<input type="hidden" name="nazwaTowaru'.$razem.'" readonly="readonly" value="Koszt przesyłki" />
	<input type="hidden" name="wartoscTowaru'.$razem.'" readonly="readonly" value="'.round($cart->getOrderTotal(true, 5),2).'" />
	<input type="hidden" name="liczbaSztukTowaru'.$razem.'" readonly="readonly" value="1" />
	<input name="jednostkaTowaru' . $razem . '" readonly="readonly" type="hidden" value="szt">
';			
		
		$carrier = new carrier($cart->id_carrier, $cart->id_lang);
		$smarty->assign(array(
			'order_id' => intval($cart->id),
			'total_products' => $razem,
			'shop_id' => $ratyzagiel_shop_id,
			'sposobDostarczeniaTowaru'=> $carrier->name,
			'ok_return' => Tools::getShopDomain(true).__PS_BASE_URI__.'modules/'.$this->name.'/ratyzagiel-return.php?status=true&order_id=',
			'error_return' => Tools::getShopDomain(true).__PS_BASE_URI__.'modules/'.$this->name.'/ratyzagiel-return.php?status=false&order_id=',
			'email' => $customer->email,
			'name' => ( ($cookie->isLogged(true) AND !empty($cookie->customer_firstname)) ? $cookie->customer_firstname : false),
			'surname' => ( ($cookie->isLogged(true) AND !empty($cookie->customer_lastname)) ? $cookie->customer_lastname : false),
			'phone' => $address->phone_mobile,
			'street' => $address->address1.' '.$address->address2,
			'city' => $address->city,
			'postal_code' => $address->postcode,
			'products_inputs' => $products_inputs
		));
 				
		return $this->display(__FILE__, 'ratyzagiel-form.tpl');
	}
	
	function paymentReturn()
	{
		global $smarty;
		
		$status = Tools::getValue('status');
		
			$smarty->assign(
				array(
					'status' => $status
				)
			);
		return $this->display(__FILE__, 'ratyzagiel-return.tpl');
	}

	function hookRightColumn()
	{
		global $smarty;
		$smarty->assign(array(
				'raty_logo' => Configuration::get('RATYZAGIEL_BLOCK_IMAGE'),
				'raty_block_title' => Configuration::get('RATYZAGIEL_BLOCK_TITLE')
			));
		if(Configuration::get('RATYZAGIEL_BLOCK') == 'right') return $this->display(__FILE__, 'blockratyzagiel.tpl');
		
	}
	
	function hookProductActions()
	{
		if(Configuration::get('RATYZAGIEL_SYM') !== 'tak')
			return NULL;
			
        global $smarty;
        global $cookie;       
		
		if ($id_product = (int)Tools::getValue('id_product'))
			$product = new Product($id_product, true, $cookie->id_lang);
		
        $smarty->assign(array(
			'id_product' => $id_product,
			'price_for_sym' => $product->getPrice(),
			'shop_id' => Configuration::get('RATYZAGIEL_SHOP_ID')
		));
        
       return $this->display(__FILE__,'productratyzagiel.tpl');
    }
	function hookLeftColumn()
	{
		global $smarty;
		$smarty->assign(array(
				'raty_logo' => Configuration::get('RATYZAGIEL_BLOCK_IMAGE'),
				'raty_block_title' => Configuration::get('RATYZAGIEL_BLOCK_TITLE')
			));
		if(Configuration::get('RATYZAGIEL_BLOCK') == 'left') return $this->display(__FILE__, 'blockratyzagiel.tpl');
	}
	function hookShoppingCartExtra()
	{
		global  $cart;
		return '
		<a rel="nofollow" onclick="window.open(\'https://www.eraty.pl/symulator/oblicz.php?numerSklepu='. Configuration::get('RATYZAGIEL_SHOP_ID') .'&wariantSklepu=1&typProduktu=0&wartoscTowarow='. $cart->getOrderTotal() .'\', \'Policz_rate\',\'width=630,height=500,directories=no,location=no,menubar=no,resizable=yes,scrollbars=yes,status=no,toolbar=no\');">
			<img src="'. _MODULE_DIR_ .'ratyzagiel/images/jakkupic.gif" alt="Jak kupić na raty"  style="cursor: pointer;" border="0">
		</a>
		
		';
		
	}

	// Funkcje sprawdzania wersji Barghest
	function hookbackOfficeHome(){
		
		try {
			$a = version_compare($this->version_rw, $this->getVersionNumber());
			if( $this->getVersionNumber() AND $a <= -1)
				if(Configuration::get('RATYZAGIEL_BARGHEST_NOTIFY') == 'tak')
				  echo '<div class="error" style="position: absolute; top: 0; left: 0; font-size: 10px; padding:0"><img style="width: 12px; height: 12px " src="../img/admin/error.png"> Jeden z modułów Barghest jest nieaktualny. Więcej informacji na dole strony.
				  </div>
				  <div class="error"><img src="../img/admin/error.png">
						Posiadasz nieaktualną wersję modułu '.$this->displayName .' Aktualna wersja to: <b>'.$this->getVersionNumber().'</b> Twoja wersja to: <b>'.$this->version_rw.'</b>
						'. ( $this->getVersionComment() ? '<br />'.$this->getVersionComment() : '' ) .'
						<br/> Zajrzyj <a href="http://pl.seigi.eu/barghest.html">tutaj</a> aby dowiedzieć się więcej.
						</div>';
		} catch (Exception $e) {
			echo $e->getMessage();
		}

	}

	function queryVersion(){
			// adres sklepu tylko dla statystyk ile sklepow aktywnie uzywa modułu :) im więcej tym większa moja chęć do aktualizacji i rozwoju modułu,więc proszę nie usuwać. Nie prowadzi to do żadnych luk w bezpieczeństwie.
			$adres = array();
			$adres[] = "adres=".rawurlencode(_PS_BASE_URL_.__PS_BASE_URI__); 
			$adres[] = "version=".rawurlencode($this->version_rw);
			$adres[] = "modulename=".rawurlencode($this->name);
			$adres = 'http://pl.seigi.eu/barghest.php?'.implode('&', $adres);
			if(function_exists('curl_init')) {
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $adres );
				curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.0; en-GB; rv:1.9.2.9) Gecko/20100824 Firefox/3.6.9 ( .NET CLR 3.5.30729; .NET CLR 4.0.20506)');
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
				curl_setopt($ch, CURLOPT_TIMEOUT, 4);
				return curl_exec($ch);
			} else return file_get_contents($adres);
			
	}
	
	function getVersionNumber(){
		if($a = $this-> getVersionInfo()){
			return htmlentities($a['version']);
		}
		return false;
	}
	
	function getVersionComment(){
		if($a = $this-> getVersionInfo() AND !empty($a['comment']))
			return htmlentities($a['comment']);

		return NULL;
	}
	
	private $checkver_cache = NULL;
	
	function getVersionInfo(){

		if($this-> checkver_cache === NULL) {
		
			$ver_file = dirname(__FILE__ ). '/version.ini';
			
			if( !file_exists($ver_file) OR filemtime($ver_file) < time()- 3600 * 12 ) {
				
				if($a = $this->queryVersion())
					if(!@file_put_contents($ver_file, $a)) {
						$this-> checkver_cache = FALSE;
						throw new Exception('<div class="error"><img src="../img/admin/error.png">Niestety nie można sprawdzić wersji modułu <b> '.$this->displayName .'</b> gdyż katalog z modułem nie ma praw zapisu.</div>');
					
					}
				
			}
			
			$this-> checkver_cache = @parse_ini_file($ver_file);
		}

		return $this-> checkver_cache;
	}
}
