<?php
        //$timer_start = microtime(true);
        
if ($_GET['service']) { 
        define('_PS_ADMIN_DIR_', getcwd());
        require(dirname(__FILE__).'/../../config/config.inc.php');
//require(dirname(__FILE__).'/../../functions.php');
}

class Pricewars extends Module
{
    private $_html = '';
    private $_postErrors = array();

	public $CeneoCategories = array(); // Id => słowne 
	public $mappings = array(); // mapowanie produktow
	
	
    function __construct()
    {
		if(Configuration::get(strtoupper($this->name).'_UPDATE_NOTIFY') === FALSE)
			Configuration::updateValue(strtoupper($this->name).'_UPDATE_NOTIFY', '1');
		
		// fix dla klasy link
		global $protocol_content;
		if (empty($protocol_content))
			$protocol_content = 'http://';
		
        $this->name = 'pricewars';
		
		if($this-> isNewPS())
			$this->tab = 'advertising_marketing';
		else
			$this->tab = 'Advertisement';
		
		$this->author = 'seigi.eu (Barghest)';
		
		
		$this->version_rw = '1.7';
		$this->version = $this->version_rw.' <a style="color: #9BBD23;" href="http://pl.seigi.eu/barghest.html" target="_blank">Barghest &#10148;</a>';
		
		
        parent::__construct();
		
		
		$mappings = glob(_PS_MODULE_DIR_.$this->name.'/mappings/*.php');
		
		foreach ($mappings as $mapping) {
			require($mapping);
			if(isset($array_map) AND is_array($array_map))
				foreach ($array_map as $array_map_key => $array_map_value)
					$this->mappings[$array_map_key] = $array_map_value;
		} 
		
		if(!defined("_PS_BASE_URL_"))
			define('_PS_BASE_URL_', $this->getShopHost(false));
        /* The parent construct is required for translations */
		$this->page = basename(__FILE__, '.php');
        $this->displayName = $this->l('Price wars!');
        $this->description = $this->l('Generuje xml z ofertą sklepu dla Ceneo, Nokaut, Skąpiec, Radar, okazje.info i zakupy.onet.pl');
		require(_PS_MODULE_DIR_.$this->name.'/ceneocategories.php');
		
		$this->postProcess();
		
		
    }
	private function getLangId() {
		return (Language::getIdByIso("pl") ? Language::getIdByIso("pl") : Configuration::get('PS_LANG_DEFAULT'));
	}
	
	public static $cache_getShopHost = NULL;
	
	private function getShopHost($with_dir = true) {
	
		if (self::$cache_getShopHost === NULL) {
			if(strpos(_PS_VERSION_, '1.2') === 0) {
				self::$cache_getShopHost = 'http://'.$_SERVER['HTTP_HOST'];
				
			} elseif (strpos(_PS_VERSION_, '1.3') === 0) {
				self::$cache_getShopHost = Tools::getHttpHost(true);
				
			} else {
				self::$cache_getShopHost = Tools::getShopDomain(true);
			}
		}
		return self::$cache_getShopHost . ($with_dir ? __PS_BASE_URI__ : NULL);
	}
	
	
	// Sprawdza, czy PS w wersji 1.4+
	public function isNewPS() {
		return version_compare(_PS_VERSION_, '1.4') >= 0 ? true : false;
	}
	
	
	public function install()
	{
		if (
			!parent::install()
			//OR ! Configuration::updateValue('PRICEWARS_SKAPIEC_SHOP_ID', '')
			OR ! Configuration::updateValue('PRICEWARS_EXP_WITH_ZERO', '0')
			OR ! Configuration::updateValue('PRICEWARS_REDUCE_AVAIL', '0')
			OR ! Configuration::updateValue(strtoupper($this->name).'_UPDATE_NOTIFY', '1')
			OR ! Configuration::updateValue(strtoupper($this->name).'_XML_IMAGE_TYPE', 'medium')
			OR ! $this->registerHook('backOfficeHome')
			)
			return false;

		if(
			$this->isNewPS()
			AND (
					!$this->registerHook('categoryUpdate')
				OR 	!$this->registerHook('categoryDeletion')
				)
			)
			return false;
		
		$this->generateCeneoCategoriesArrayFile(); // call to regenerate CENEO Category Strucure :)
		return true;
	}

	public function uninstall()
	{
		if (
			!Configuration::deleteByName('PRICEWARS_SKAPIEC_SHOP_ID') 
			OR !Configuration::deleteByName('PRICEWARS_EXP_WITH_ZERO') 
			OR !Configuration::deleteByName('PRICEWARS_REDUCE_AVAIL') 
			OR !Configuration::deleteByName(strtoupper($this->name).'_UPDATE_NOTIFY') 
			OR !Configuration::deleteByName(strtoupper($this->name).'_XML_IMAGE_TYPE') 
			OR !parent::uninstall())
			return false;
		return true;
	}
	
	public function postProcess(){
		if(isset($_POST['reassignCatSubmit']) AND (int) $_POST['reassignCatIdSubmit']) {
			$this->generateCeneoMappingFile();
		}
		
		if(isset($_POST['regenerateCeneoCatArray']))
			$this->generateCeneoCategoriesArrayFile(); // call to regenerate CENEO Category Strucure :)
	}
	function getContent()
    {	

		
		$this->_postValidateProcess();

       echo '<h2>Price wars!</h2>';
       echo '
	   		<style type="text/css">
			ul.pw_links_list {
				padding: 0 5px; 
				list-style: none;
			}
			ul.pw_links_list li {
				padding: 5px;
				margin-bottom: 10px;
				border: 1px dotted grey;
			}
			ul.pw_links_list li input {
				display: block;
				padding: 4px;
				width: 710px;
				margin: 4px 0;
			}
			span.greenIt {
				color: green;
			}
			span.redIt {
				color: red;
			}
		</style>';
		if($this-> isNewPS()){
			echo '<fieldset class="width4">
				<legend><img src="../img/admin/warning.gif" />Mapowanie kategorii</legend>
				<h3>Wybierz Kategorię do mapowania</h3>';


			$homeCat = Category::getHomeCategories($this->getLangId());
			foreach ($homeCat as $cat) {
				echo '<a href="index.php?tab=AdminModules&configure='.$this->name.'&token='.$_GET['token'].'&tab_module=administration&module_name='.$this->name.'&reassignCatId='.$cat["id_category"].'">'.$cat["name"].'</a> '
				.( file_exists(_PS_MODULE_DIR_.$this->name.'/mappings/'.$cat["id_category"].'.php') ? '<span class="greenIt">Zmapowane</span>' : '<span class="redIt">Nie zmapowane</span>' ).' <br />';
			}
			if(isset($_GET['reassignCatId'])) {

				echo '<h2>Opcje mapowania kategorii</h2>';


				echo '<form method="post" action=""><table>
					<input type="hidden" value="'.$_GET['reassignCatId'].'" name="reassignCatIdSubmit" />';
				$this->drawCategoryTree(Category::getCategories($this->getLangId()), 1, 0, $_GET['reassignCatId']);
					
				echo '<tr><td></td><td><input type="submit" class="button" name="reassignCatSubmit" value="Przypisz"></td></tr>
				</table></form>';
			}
			
			echo '</fieldset><br/>';

			echo '<fieldset class="width4">
				<legend><img src="../img/admin/warning.gif" />Odświeżanie XML\'a Kategorii ceneo</legend>
					Jeśli dopiero zainstalowałeś moduł, kliknij aby mieć najnowszy XML kategorii ceneo w sklepie<br>
					<form method="post" action="">
					<input type="submit" class="button" name="regenerateCeneoCatArray" value="Pobierz Świeży XML Kategorii z Ceneo">
				</form>';
			echo '</fieldset><br/>';
		}
		
		$this->_displaySettingsForm();
		
		echo '<br/><fieldset class="width4">
			<legend><img src="../img/admin/warning.gif" />Informacje</legend>
			
			Przy nawiązywaniu współpracy z porównywarkami cen podaj następujące linki jako lokoalizacje plików xml zawierających ofertę sklepu:<br /><br />
			<ul class="pw_links_list">';
			
			if($this->isNewPS())
			echo '
			<li><b>Ceneo (Nowy XML):</b> '.' 
				<input readonly="readonly" value="'.$this->getShopHost().'modules/'.$this->name.'/offer.php?service=ceneoNew"/>
			
			<div>
				<span style="color: red;">
					Narazie skrypt dość wolno się wczytuje przy mapowaniu, postaram się to poprawić w następnej wersji.<br /><br />
					Nieprzypisane kategorie, nie będą uwzględnione w nowym XML, gdyż nowy XML Musi mieć kategorie. Jeśli nie chcesz tworzyć mapowania to użyj starego XML\'a.<br /><br />
					Przypisywanie kategorii co Ceneo odbywa się na zasadzie edycji kategorii głównych. Jeśli usuniemy lub przemieścimy kategorię główną niżej (np pod inną podkategorię) jej mapowanie zostanie usunięte.<br />
					<b>Dlatego też przed rozpoczęciem mapowania zaleca się najpierw ostatecznie ustalić strukturę kategorii.</b>
				</span>
				<br />
				<br />
			</div>
			</li>
			'; else echo '<li>Niestety, nowy XML Ceneo jest obsługiwany od wersji 1.4</li>';
			
			
			echo '<li><b>Ceneo:</b>
				<input readonly="readonly" value="'.$this->getShopHost().'modules/'.$this->name.'/offer.php?service=ceneo"/>
				XML Ceneo akceptują także takie porównywarki jak: <br />
				
				<b>najtaniej24.pl</b>, <b>smartbay.pl</b>, <b>cenuj.pl</b>, <b>kupujemy.pl</b>, <b>bazarcen.pl</b>, <b>harpagon.pl</b>
			</li>
			<li><b>skapiec.pl:</b>
			<input readonly="readonly" value="'.$this->getShopHost().'modules/'.$this->name.'/offer.php?service=skapiec"/></li>
			<li><b>nokaut.pl:</b>
				<input readonly="readonly" value="'.$this->getShopHost().'modules/'.$this->name.'/offer.php?service=nokaut"/>
			
			</li>
			<li><b>radar.pl:</b>
				<input readonly="readonly" value="'.$this->getShopHost().'modules/'.$this->name.'/offer.php?service=radar"/></li>
			<li><b>okazje.info.pl:</b>
				<input readonly="readonly" value="'.$this->getShopHost().'modules/'.$this->name.'/offer.php?service=okazje"/></li>
			<li><b>zakupy.onet.pl:</b>
				<input readonly="readonly" value="'.$this->getShopHost().'modules/'.$this->name.'/offer.php?service=zakupy_onet"/></li>
			<li><b>sklepy24.pl:</b>
				<input readonly="readonly" value="'.$this->getShopHost().'modules/'.$this->name.'/offer.php?service=sklepy24"/></li>
			
			<li><b>tanio.pl:</b>
				<code>Brak obsługi</code>
			</li>
			<li>
				<b>oferciak.pl:</b> <code>Brak obsługi</code>
			</li>
			<li>
				<b>alejka.pl:</b> <code>Brak obsługi</code>
			</li>
			<li>
				<b>szoker.pl:</b> <code>Brak obsługi</code>
			</li>
			<li>
				<b>ceneria.pl: </b>   <code>Brak obsługi</code><br />
				Zakres: "Skupiamy się wyłącznie na szeroko rozumianym sprzęcie do wszelkich form aktywności"
			</li>
			
			<li><b>kreocen.pl:</b> <code>Brak obsługi</code><br />
				KreoCEN to unikalna platforma promocyjna dla sklepów internetowych z kategorii "Dom i ogród"
			
			</li>
			

			</ul>
			</fieldset>
				

			
			';
		return $this->_html;
    }
		
	private function drawCategoryTree($categories, $parent = 0, $level = 0, $genForCat = FALSE) {
		
		foreach ($categories[$parent] as $key => $category){
			$id = (int) $category['infos']['id_category'];
			//var_dump($genForCat);
			
			
			if ( $genForCat > 0 AND $parent === 2 AND $id != $genForCat) continue;
			
			//echo '<a style="display: block; padding-left: '.($level*40).'px" href="index.php?tab=AdminModules&configure='.$this->name.'&tab_module=administration&module_name='.$this->name.'&catId='.$id.'">'.$category['infos']['name'].' ('.$cat->getProducts($this->lang_id, 0, 0, NULL, NULL, true).')</a>';
			if($id !== 1) {
				echo '<tr><td style="padding-left: '.($level*40).'px">'.$category['infos']['name'].'</td><td>
				<select style="width: 400px; font-size: 10px;" name="categoryToMap['.$id.']" />';
				echo "<option style='font-size: 10px; color: red;' value='0'> Nie przypisane...</option>"; 
					 foreach ($this->CeneoCategories as $ceneoId => $ceneoName)
						
						echo "<option ".(@$this->mappings[$id] == $ceneoId ? ' selected="selected" ' : '')." style='font-size: 10px;' value='{$ceneoId}'>{$ceneoName}</option>"; 
				echo '</select>
                				</td></tr>';
			}
			if (isset($categories[$id]))
				$this-> drawCategoryTree($categories, $id, $level+1 ,$genForCat );
			//break;
		}
	}
	
	public function generateCeneoMappingFile () {
		$file = '<?php $array_map = ';
		$file .= var_export($_POST['categoryToMap'], true);
		$file .= ';';
		file_put_contents(_PS_MODULE_DIR_.$this->name.'/mappings/'.$_POST['reassignCatIdSubmit'].'.php', $file );
		
	}
	public function generateCeneoCategoriesArrayFile () {
		
		$xml = simplexml_load_file('http://api.ceneo.pl/Kategorie/dane.xml');
		$this->ceneoXmlToArray ($xml);
		$categories = '<?php
		$this->CeneoCategories = ';
		$categories .= var_export($this->CeneoCategories, true);
		file_put_contents(_PS_MODULE_DIR_.$this->name.'/ceneocategories.php',$categories.';');
	}
	
	// reurencyjna funkcja uzywana do zapełniania drzewa kategorii z ceneo.
	public function ceneoXmlToArray ($categoryArray, $prefix = NULL) {
		
		
		foreach ( $categoryArray as $xml ) {

			$this->CeneoCategories[(string)$xml->Id] = (string) $prefix . $xml->Name;
			if(isset($xml->Subcategories))
				$this->ceneoXmlToArray($xml->Subcategories->Category, $prefix.$xml->Name.'/');

		}
	}
	
	private function _displaySettingsForm()
	{

		$exportwithzero = intval(Configuration::get('PRICEWARS_EXP_WITH_ZERO'));
		$reduceavailibilty = intval(Configuration::get('PRICEWARS_REDUCE_AVAIL'));
		$updatenotify = intval(Configuration::get(strtoupper($this->name).'_UPDATE_NOTIFY'));
		$xmlImageType = Configuration::get(strtoupper($this->name).'_XML_IMAGE_TYPE');
				
		//$skapiecShopId = Configuration::get('PRICEWARS_SKAPIEC_SHOP_ID');
		if (sizeof($this->_postErrors))
			$this->_displayErrors();
		echo '
		<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
		<fieldset class="width4">
			<legend><img src="../img/admin/contact.gif" />'.$this->l('Ustawienia').'</legend>';
			// Skapiec Zrezygnowal z Identyfikatorow sklepu, ale dla pewnosci zostawię to na razie :)
			/* echo '<label>'.$this->l('Identyfikator Twojego sklepu w skapiec.pl').'</label>
				<div class="margin-form"><input type="text" size="10" name="skapiecShopId" value="'.$skapiecShopId.'" /></div>
				<br>'; */
				
			echo '<label>'.$this->l('Eksportuj z zerowym stanem magazynowym').'</label>
			<div class="margin-form">
				Eksportuje produkty, które posiadają stan magazynowy 0. Gdy nie prowadzisz stanów magazynowych.<br>
				<input type="radio" '.($exportwithzero === 0 ? ' checked="checked" ' : '').' name="exportwithzero" value="0" /><b>(domyślne)</b> Nie eksporuj<br>
				<input type="radio" '.($exportwithzero === 1 ? ' checked="checked" ' : '').' name="exportwithzero" value="1" /> Eksportuj (jako dostępne; stan wymuszony na 1) (Tip: Zaznacz, gdy masz na sklepie atrybuty (np. kolory), a główny produkt ma stan magazynowy zero )<br>
				<input type="radio" '.($exportwithzero === 2 ? ' checked="checked" ' : '').' name="exportwithzero" value="2" /> Eksportuj (jako niedostępne; stan wymuszony na 0)<br>
				
			</div>
			<br>
			<label>'.$this->l('Redukuj dostępność').'</label>
			<div class="margin-form">
				<input type="radio" '.($reduceavailibilty === 0 ? ' checked="checked" ' : '').' name="reduceavailibilty" value="0" /><b>(domyślne)</b> Eksportuj produkty z prawdziwym stanem magazynowym; Produkty wymuszone przez poprzenią opcję będą eksportowane ze stanem magazynowym ustalonym wyżej. <br>
				<input type="radio" '.($reduceavailibilty === 1 ? ' checked="checked" ' : '').' name="reduceavailibilty" value="1" /> Redukuj stany większe niż 1 na 1; zero pozostaje zerem.
			</div>
			
			<br>
			<label>'.$this->l('Informuj o dostępności aktualizacji').'</label>
			<div class="margin-form">
				<input type="radio" '.($updatenotify === 0 ? ' checked="checked" ' : '').' name="updatenotify" value="0" />Nie<br>
				<input type="radio" '.($updatenotify === 1 ? ' checked="checked" ' : '').' name="updatenotify" value="1" />Tak (Zalecane)
			</div>
			
			<br>
			<label>'.$this->l('Wybierz jaki typ obrazka ma być udostępniany w xml').'</label>
			<div class="margin-form">
			<select name="xmlimagetype">
			';

			$images = db::getInstance()->ExecuteS("SELECT name, width, height FROM `ps_image_type` WHERE `products` = 1");
			
			foreach ($images as $image){
				
				echo '<option '.($xmlImageType === $image['name'] ? "selected=\"selected\"" : NULL ).' value="'.$image['name'].'">'.$image['name'].' ('.$image['width'].'px x '.$image['height'].'px)</option>';
			}
			
		
			
			
		echo '</select></div>
		<br /><center><input type="submit" name="submitPricewars" value="'.$this->l('Zapisz zmiany').'" class="button" /></center>
		</fieldset>

		</form>';
	}


	private function _postValidateProcess(){
		if (isset($_POST['submitPricewars'])){
			
			/* if (empty($_POST['skapiecShopId']))
				$this->_postErrors[] = $this->l('Musisz podać id sklepu w skapiec.pl, o ile chcesz z niego korzystać.');
			else
				Configuration::updateValue('PRICEWARS_SKAPIEC_SHOP_ID', intval($_POST['skapiecShopId'])); */
			
			Configuration::updateValue('PRICEWARS_EXP_WITH_ZERO', intval($_POST['exportwithzero']));
			Configuration::updateValue('PRICEWARS_REDUCE_AVAIL', intval($_POST['reduceavailibilty']));
			Configuration::updateValue(strtoupper($this->name).'_UPDATE_NOTIFY', intval($_POST['updatenotify']));
			Configuration::updateValue(strtoupper($this->name).'_XML_IMAGE_TYPE', $_POST['xmlimagetype']);
			
		}
	}
	

	private function _displayErrors()
	{
		$nbErrors = sizeof($this->_postErrors);
		$this->_html .= '
		<div class="alert error">
			<h3>'.($nbErrors > 1 ? $this->l('Wystapily') : $this->l('Wystapil')).' '.$nbErrors.' '.($nbErrors > 1 ? $this->l('bledy') : $this->l('blad')).'</h3>
			<ol>';
		foreach ($this->_postErrors AS $error)
			$this->_html .= '<li>'.$error.'</li>';
		$this->_html .= '
			</ol>
		</div>';
	}

	private function _getCategoryTree(){

		$categories = Category::getCategories($this->getLangId(),false,false);
		$categoryArray = array ();

		foreach($categories as $category) {
			$categoryArray[$category['id_category']] = $category;
		}

		foreach ($categoryArray as $category) {
			$categoryParent = $categoryArray[$category['id_category']]['id_parent'];
			$categoryPath = $categoryArray[$category['id_category']]['name'];
			$counter = 0;
			while ($categoryParent) {
				$counter++;
				if ($counter > 100)
					break;
				if ($categoryParent != $categoryArray[$categoryParent]['id_parent']) {
					if ($categoryArray[$categoryParent]['id_category']!=1)
						$categoryPath = $categoryArray[$categoryParent]['name'] . "/" . $categoryPath;
					$categoryParent = $categoryArray[$categoryParent]['id_parent'];
				} else {
					$categoryParent = 0;
				}
			}
			$categoryArray[$category['id_category']]['path'] = $categoryPath;
		}
		return $categoryArray;
	}

	public function getXML(){		
		if (isset($_GET['service'])){
			switch($_GET['service']){
			case "ceneoNew":				
				$xml = $this->_doCeneoNew();
				break;
			case "ceneo":				
				$xml = $this->_doCeneo();
				break;
			case "nokaut":  
				$xml = $this->_doNokaut();
				break;
			case "skapiec":  
				$xml = $this->_doSkapiec();
				break;
			case "radar":  
				$xml = $this->_doRadar();
				break;
			case "okazje":  
				$xml = $this->_doOkazje();
				break;
			case "sklepy24":
				$xml = $this->_doSklepy24();
				break;
			case "zakupy_onet":  
				$xml = $this->_doZakupyOnet();
				break;
			default:
				echo "Nothing to do.";
				break;
			}

			echo $xml->saveXML();
		}else
			echo "Nothing to do.";
	}
	private function getXmlImageType() {
		return Configuration::get(strtoupper($this->name).'_XML_IMAGE_TYPE');
	}
	/*
	*
	*
	*	@param bool $active 			true aby pobrać tylko produkty aktywne
	*	@param bool $instock 			true aby pobrać tylko produkty które są na magazynie.
	*	@param bool $reducequantity		true aby ustawić stan magazynowy produktu na 1 - w celu zamaskowania w xmlu prawdziwej ilosci na magazynie
	*	
	*
	*/
	private function getProducts($active = true, $instock = true) { 
		Global $cookie;
		$cookie = new Cookie('ps');
		
		//public static function getProducts($id_lang, $start, $limit, $orderBy, $orderWay, $id_category = false, $only_active = false)
		$Products = Product::getProducts($this->getLangId(), 0, NULL, 'name', 'ASC', false, (bool) $active);
		
		$exportwithzero = intval(Configuration::get('PRICEWARS_EXP_WITH_ZERO'));
		$reduceavailibilty = intval(Configuration::get('PRICEWARS_REDUCE_AVAIL'));
		
		//products attributes
		
		//$productFromAttributes = array();
/* 		foreach ($Products as $product) {
			
				$p = new Product(1, true);
				$a = $p->getAttributesGroups(1);
				$c = array();
				foreach($a as $b){
					if(isset($c[$b["id_attribute_group"]])) {
						$c[$b["id_attribute_group"]][] = $b;
					} else {
						$c[$b["id_attribute_group"]][0] = $b;
					}
				}
				
		
		
		}
 */
		    $i=0;
		foreach ($Products as $key => &$product) {
			
                           
            
			if ( $exportwithzero === 0 AND $product['quantity'] <= 0) { // to usun
				unset($Products[$key]);
				continue;
				
			} elseif ( $exportwithzero === 1 AND $product['quantity'] <= 0 ) {
				$product['quantity'] = 1;
				
			} elseif  ( $exportwithzero === 2 AND $product['quantity'] <= 0 ) {
				$product['quantity'] = 0;
				
			}
			
			if( $reduceavailibilty === 1 AND $product['quantity'] > 1 )
				$product['quantity'] = 1;
			
		}
		return $Products;

	}
	
	
	public static function htmlentities( $string )
	{
		return str_replace('&', '&amp;', $string);
		return htmlentities( $string , ENT_COMPAT, 'UTF-8');
	}
	
	private function getTaxedPrice ($Product) {
	

            $price = ProductCore::getPriceStatic($Product['id_product']);
            
            return round($price,0);
            
            
            //$product_price = $this->product->getPrice(Product::$_taxCalculationMethod == PS_TAX_INC, false);
		
/* 		if($Product["tax_rate"] === NULL)
			$Product["tax_rate"] = 0;
	
		if(!isset($Product["tax_rate"]) OR !isset($Product["price"])) {
			throw new Exception('Przekazana wartość musi być tablicą produktu.');
		}
		if($Product["tax_rate"] > 0)
			$tax = $Product["price"] * ( $Product["tax_rate"] / 100);
		else 
			$tax = 0;
		
		return round ($Product["price"] + $tax, 2); */
		
	}
	
	function hookcategoryUpdate(){
		if( intval($_POST["id_parent"]) !== 1 AND file_exists(_PS_MODULE_DIR_.$this->name.'/mappings/'.$_POST['id_category'].'.php')) {
			unlink(_PS_MODULE_DIR_.$this->name.'/mappings/'.$_POST['id_category'].'.php');
		}
	}
	function hookcategoryDeletion(){
		@unlink(_PS_MODULE_DIR_.$this->name.'/mappings/'.$_POST['id_category'].'.php');
	}
	
	// Funkcje sprawdzania wersji Barghest
	function hookbackOfficeHome(){
		
		try {
			$a = version_compare($this->version_rw, $this->getVersionNumber());
			if( $this->getVersionNumber() AND $a <= -1)
			if(intval(Configuration::get(strtoupper($this->name).'_UPDATE_NOTIFY')))
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
			$adres[] = "adres=".rawurlencode($this->getShopHost()); 
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
		if($a = $this-> getVersionInfo())
			return htmlentities($a['version']);

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

	
	private function _doCeneo(){
		$link = new Link();
        $xml = new DOMDocument();

		//$Products = Product::getProducts(Language::getIdByIso("pl"), 0, NULL, 'name', 'ASC');
		$Products = $this->getProducts();
		$Categories = $this->_getCategoryTree();				
		
		if($Products&&$Categories){
			
			$xml->loadXML('<?xml version="1.0" encoding="UTF-8"?>
						   <!DOCTYPE pasaz:Envelope SYSTEM "loadOffers.dtd">
							<pasaz:Envelope xmlns:pasaz="http://schemas.xmlsoap.org/soap/envelope/">
							 <pasaz:Body>
							  <loadOffers xmlns="urn:ExportB2B">
							   <offers/>
							  </loadOffers>
							 </pasaz:Body>	
							</pasaz:Envelope>');

			$offers = $xml->getElementsByTagName('offers');
			$offers = $offers->item(0);

			foreach ($Products AS $Product){
				
				$offer = $xml->createElement('offer');
				$offers->appendChild($offer);

				$id = $xml->createElement('id',$Product['id_product']);
				$offer->appendChild($id);
				
				//to avoid entity warning
				//$productName = str_replace("&", "&amp;", $Product['name']);
				//again. one below is better ;)
				$name = $xml->createElement('name', self::htmlentities($Product['name'])); 
				$offer->appendChild($name);

				$price = $xml->createElement('price',$this-> getTaxedPrice($Product)); 
				$offer->appendChild($price);

				$url = $xml->createElement('url',htmlspecialchars($link->getProductLink($Product['id_product'], $Product['link_rewrite'],null,null,null,null,null,1)));
				$offer->appendChild($url);

				$categoryId = $xml->createElement('categoryId',$Categories[$Product['id_category_default']]['path']);
				$offer->appendChild($categoryId);

				$description_cdata = $xml->createCDATASection(strip_tags(html_entity_decode($Product['description_short'],ENT_COMPAT,"UTF-8"),"<br><li><p><ul><tr>"));
				$description = $xml->createElement('description');
				$description->appendChild($description_cdata);
				$offer->appendChild($description);

				$image = Image::getCover($Product['id_product']);					
				if ($image['id_image'])
					//$img_url = $this->getShopHost().'img/p/'.$image['id_product'].'-'.$image['id_image'].'-large.jpg';
					$img_url = $link->getImageLink($Product['link_rewrite'], $image['id_product'].'-'.$image['id_image'],$this->getXmlImageType());
				else
					$img_url = "";
					
				$image = $xml->createElement('image',$img_url);
				$offer->appendChild($image);
				
				$attributes = $xml->createElement('attributes');
				$offer->appendChild($attributes);

				$attribute = $xml->createElement('attribute');
				$attributes->appendChild($attribute);

				$name = $xml->createElement('name','Producent');
				$attribute->appendChild($name);

				$value = $xml->createElement('value',$Product['manufacturer_name']);
				$attribute->appendChild($value);
				
				/// ean13

				$attribute = $xml->createElement('attribute');
				$attributes->appendChild($attribute);

				$name = $xml->createElement('name', 'EAN');
				$attribute->appendChild($name);

				$value = $xml->createElement('value', $Product["ean13"]);
				$attribute->appendChild($value);
				/// eof ean13;
				
				/// ean13
				$attribute = $xml->createElement('attribute');
				$attributes->appendChild($attribute);

				$name = $xml->createElement('name', 'Kod producenta');
				$attribute->appendChild($name);

				$value = $xml->createElement('value', $Product["reference"]);
				$attribute->appendChild($value);
				/// eof ean13;
			

				/// hurtownie ACTION S.A. - Dla sklepów zintegrowanych z programem Cennik-Offline
				if(isset($Product["id_cennik_offline"])) {
					$attribute = $xml->createElement('attribute');
					$attributes->appendChild($attribute);

					$name = $xml->createElement('name','Kod hurtowni');
					$attribute->appendChild($name);

					$value = $xml->createElement('value',$Product["id_cennik_offline"]);
					$attribute->appendChild($value);
				}
				/// eof hurtownie;
				
				$availability = $xml->createElement('availability', $Product['quantity']);
				$offer->appendChild($availability);

			}
		}
		return $xml;
	}
	private function _doCeneoNew(){
        
		
		$link = new Link();
        $xml  = new DOMDocument();

		$Products = $this->getProducts();
		$Categories = $this->_getCategoryTree();				
		
		
		/* 		
		foreach ($Categories as $c) {
			echo $c["id_category"] . ' => NULL, //'.$c["name"];
		
			echo "\r\n";
		}
		die(); */
		// Generate cool Manufacturers array :)
		
		$Manufacturers_temp = Manufacturer::getManufacturers();
		$Manufacturers = array();
		
		foreach ($Manufacturers_temp as $m)
			$Manufacturers[$m['id_manufacturer']] = $m['name'];
		
		// OMg... heeeeeavy, time to free up some space... not that Domdocument is havy as it is.... Smarty would be better...
		
		unset($Manufacturers_temp); ///itwontdoashitanyway...justlooksnice.GivemeGarbargecollectors!givephp5.3everywhere<3!!
		
		if($Products && $Categories && $Manufacturers){
			
			$xml->loadXML('<?xml version="1.0" encoding="utf-8"?>
							<offers xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" version="1">
								<group name="other">
								</group>
							</offers>
							');

			$group = $xml->getElementsByTagName('group');
			$group = $group->item(0);

			foreach ($Products AS $Product){
				//if(!isset($this->mappings[$Product['id_category_default']]) OR !isset($this->CeneoCategories[$this->mappings[$Product['id_category_default']]])) continue;
					
			    $availability = $Product['quantity'] > 0 ? 1 : 0;

				$o = $xml->createElement('o');
				
				$id = $xml->createAttribute ('id');
				$id->value = $Product['id_product'];
				$o->appendChild($id);
				
				$url = $xml->createAttribute ('url');
				$url->value = htmlspecialchars($link->getProductLink($Product['id_product'], $Product['link_rewrite'],null,null,null,null,null,1));
				$o->appendChild($url);
				
				$price = $xml->createAttribute ('price');
				$price->value = $this-> getTaxedPrice($Product);
				$o->appendChild($price);

				$avail = $xml->createAttribute ('avail');
				$avail->value = 3;
				$o->appendChild($avail);
				
				$set = $xml->createAttribute ('set');
				$set->value = '0';
				$o->appendChild($set);
				
				//*
				if( (float) $Product['weight'] > 0 ) {
					$weight = $xml->createAttribute ('weight');
					$weight->value = (float) $Product['weight'];
					$o->appendChild($weight);
				}
				//*/
				
				$stock = $xml->createAttribute ('stock');
				$stock->value = 'medium';
				$o->appendChild($stock);
				

				$name = $xml->createElement('name');
				$o->appendChild($name);
				
				$cdata_name = $xml->createCDATASection(self::htmlentities($Product['name']));
				$name->appendChild($cdata_name);
				
				
				// description
				$desc = $xml->createElement('desc');
				$o->appendChild($desc);
				
				$cdata_desc = $xml->createCDATASection(self::htmlentities($Product['description_short']));
				$desc->appendChild($cdata_desc);

				// category
				$cat = $xml->createElement('cat');
				$o->appendChild($cat);
				
				$cdata_cat = $xml->createCDATASection($this->CeneoCategories[$this->mappings[$Product['id_category_default']]]);
				$cat->appendChild($cdata_cat);
				
				
				/// Start atrybutów
				$attrs = $xml->createElement('attrs');
				$o->appendChild($attrs);
				
				// Atrybuty New
				$a = $xml->createElement('a');
				$attrs->appendChild($a);
				
				$cdata_a = $xml->createCDATASection(  $Manufacturers[$Product['id_manufacturer']] );
				$a->appendChild($cdata_a);
				$name = $xml->createAttribute ('name');
				$a->appendChild($name);
				$name->value = 'Producent';
				
				// Atrybuty New
				$a = $xml->createElement('a');
				$attrs->appendChild($a);
				
				$cdata_a = $xml->createCDATASection($Product["reference"]);
				$a->appendChild($cdata_a);
				$name = $xml->createAttribute ('name');
				$a->appendChild($name);
				$name->value = 'Kod_producenta';
				
				
				// Atrybuty New  :: Dla hurtowni ACTION S.A. W sklepach zintegrowanych z prgramem Cennik-offline.
				if(isset($Product["id_cennik_offline"])) {
					$a = $xml->createElement('a');
					$attrs->appendChild($a);

					$cdata_a = $xml->createCDATASection($Product["id_cennik_offline"]);
					$a->appendChild($cdata_a);
					
					$name = $xml->createAttribute ('name');
					$a->appendChild($name);
					$name->value = 'Kod hurtowni';
				}
				
									
				// images
				//$image = Image::getCover($Product['id_product']);	
                
                $tag = 'main';
                $images = Image::getImages(6,$Product['id_product']);
                 if ($images) {
                    $imgs = $xml->createElement('imgs');
                    $o->appendChild($imgs);
                
                
                    foreach ($images as $image)				
				    if ($image['id_image']) {
					    //$img_url = ''.$this->getShopHost().'img/p/'.$image['id_product'].'-'.$image['id_image'].'-large.jpg';
					    $img_url = 'http://'.$link->getImageLink($Product['link_rewrite'], $image['id_product'].'-'.$image['id_image']);
					    $main = $xml->createElement($tag);
					    $imgs->appendChild($main);
                        $tag = 'i';
					    
					    $url = $xml->createAttribute ('url');
					    $main->appendChild($url);
					    
					    $url->value = $img_url;
				    }
				// eof::images
                 }
				
				$group->appendChild($o);
				//echo $xml->saveXML(); die();

                //break;
			}
		}
		return $xml;
	}
	private function _doNokaut(){
		$link = new Link();
        $xml = new DOMDocument();
		
		//$Products = Product::getProductsFor(Language::getIdByIso("pl"), 0, NULL, 'name', 'ASC');
		$Products = $this->getProducts();
		$Categories = $this->_getCategoryTree();

		if($Products&&$Categories){
			$xml->loadXML('<?xml version="1.0" encoding="UTF-8"?>
						   <!DOCTYPE nokaut SYSTEM "http://www.nokaut.pl/integracja/nokaut.dtd">
							<nokaut>
							   <offers/>
							</nokaut>');

			$offers = $xml->getElementsByTagName('offers');
			$offers = $offers->item(0);

			foreach ($Products AS $Product){
			
				$offer = $xml->createElement('offer');
				$offers->appendChild($offer);

				$id = $xml->createElement('id',$Product['id_product']);
				$offer->appendChild($id);
				
				$name = $xml->createElement('name', self::htmlentities($Product['name'])); 
				$offer->appendChild($name);

				$description_cdata = $xml->createCDATASection(strip_tags(html_entity_decode($Product['description_short'],ENT_COMPAT,"UTF-8"),"<br><li><p><ul><tr>"));
				$description = $xml->createElement('description');
				$description->appendChild($description_cdata);
				$offer->appendChild($description);

				$url = $xml->createElement('url',htmlspecialchars($link->getProductLink($Product['id_product'], $Product['link_rewrite'],null,null,null,null,null,1)));
				$offer->appendChild($url);

				$image = Image::getCover($Product['id_product']);
				//var_dump($image);
				
				if ($image['id_image'])
					//$img_url = ''.$this->getShopHost().'img/p/'.$image['id_product'].'-'.$image['id_image'].'-large.jpg';
					$img_url = "http://".$link->getImageLink($Product['link_rewrite'], $image['id_product'].'-'.$image['id_image'],$this->getXmlImageType());
				else
					$img_url = "";
				$image = $xml->createElement('image',$img_url);
				$offer->appendChild($image);

				$price = $xml->createElement('price',$this-> getTaxedPrice($Product)); 
				$offer->appendChild($price);

				$category = $xml->createElement('category',$Categories[$Product['id_category_default']]['path']);
				$offer->appendChild($category);

				$producer = $xml->createElement('producer',$Product['manufacturer_name']);
				$offer->appendChild($producer);
			}
		}
		return $xml;
	}
	private function _doSkapiec(){
		$link = new Link();
        $xml = new DOMDocument();
		
		//$Products = Product::getProductsForSkapiec(Language::getIdByIso("pl"), 0, NULL, 'name', 'ASC');
		$Products = $this->getProducts();
		$Categories = $this->_getCategoryTree();

		if($Products&&$Categories){
			$xml->loadXML('<?xml version="1.0" encoding="UTF-8"?>
							<xmldata>
							  <version>12</version>
							  <header/>		
							  <category/>
							  <data/> 
							</xmldata>');

			$header = $xml->getElementsByTagName('header');
			$header = $header->item(0);

			$name = $xml->createElement('name',Configuration::get('PS_SHOP_NAME'));
			$header->appendChild($name);
			
			/* 
			// Wersja 12 już tego nie używa, przynajmniej nie było tego w dokumentacji dostarczonej.
			$shopid = $xml->createElement('shopid',Configuration::get('PRICEWARS_SKAPIEC_SHOP_ID') ? Configuration::get('PRICEWARS_SKAPIEC_SHOP_ID') : "");
			$header->appendChild($shopid);
			*/

			$www = $xml->createElement('www',$this->getShopHost());
			$header->appendChild($www);

			$time = $xml->createElement('time',date('Y-m-d'));
			$header->appendChild($time);

			$category = $xml->getElementsByTagName('category');
			$category = $category->item(0);

			foreach($Categories as $catitem){
				$cat = $xml->createElement('catitem');
				$category->appendChild($cat);

				$catid = $xml->createElement('catid',$catitem['id_category']);
				$cat->appendChild($catid);

				$catname = $xml->createElement('catname',$Categories[$catitem['id_category']]['path']);   
				$cat->appendChild($catname);
			}

			$data = $xml->getElementsByTagName('data');
			$data = $data->item(0);
            
            foreach ($Products AS $Product){
				
				$item = $xml->createElement('item');
				$data->appendChild($item);

				$compid = $xml->createElement('compid',$Product['id_product']);
				$item->appendChild($compid);

				$vendor = $xml->createElement('vendor',$Product['manufacturer_name']);
				$item->appendChild($vendor);

				$name = $xml->createElement('name', self::htmlentities($Product['name'])); 
				$item->appendChild($name);

				$price = $xml->createElement('price',$this-> getTaxedPrice($Product)); 
				$item->appendChild($price);
				/*  
					<partnr>APED:2030-53PLVFY</partnr> 
				- <!--  tzw PartNumber czyli unikalny kod nadany przez producenta danemu towarowi, pole nie jest obowiazkowe; wartoĹÄ moĹźe byc umieszczona takze w znaczniku NAME
				  --> 
				*/
				$catid = $xml->createElement('catid',$Product['id_category_default']); 
				$item->appendChild($catid);
				
				
				// description
				$desc = $xml->createElement('desclong');
				$item->appendChild($desc);
				
				$cdata_desc = $xml->createCDATASection(self::htmlentities($Product['description_short']));
				$desc->appendChild($cdata_desc);
				/* 
				<desclong>pelny opis towaru w sklepie</desclong> 
				pole nie jest obowiazkowe 
				
				<availability>2</availability> 
				ilosc dni od zlozenia zamowienia do czasu wyslania towaru, (-1  towar na zamowienie), pole nie jest obowiazkowe 
				*/

				$ean = $xml->createElement('ean',$Product['ean13']); 
				$item->appendChild($ean);
				
				/*
				<ean>1234567890123</ean> 
				kod EAN/ISBN produktu (pole obowiazkowe dla ksiazek oraz plyt z muzyka) 

			    <url>http://www.sklep.pl/0123abc.php</url> 
				URL do widoku produktu, pole nie jest obowiazkowe, obecnie url generowany jest dynamicznie na podstawie <compid> 
				  */
				
				$url = $xml->createElement('url',htmlspecialchars($link->getProductLink($Product['id_product'], $Product['link_rewrite'],null,null,null,null,null,1)));
				$item->appendChild($url);

				
				$actionid = isset($Product['id_cennik_offline']) ? $xml->createElement('action',$Product['id_cennik_offline']) : $xml->createElement('action', NULL);
				$item->appendChild( $actionid );
                
                $image = Image::getCover($Product['id_product']);                    
                if ($image['id_image'])
                    //$img_url = ''.$this->getShopHost().'img/p/'.$image['id_product'].'-'.$image['id_image'].'-large.jpg';
                    $img_url = 'http://'.$link->getImageLink($Product['link_rewrite'], $image['id_product'].'-'.$image['id_image'],$this->getXmlImageType());
                else
                    $img_url = "";
                $image = $xml->createElement('image',$img_url);
                $item->appendChild($image);

			}
		}
		return $xml;
	}






	private function _doSklepy24(){
		$link = new Link();
        $xml = new DOMDocument();

		//$Products = Product::getProducts(Language::getIdByIso("pl"), 0, NULL, 'name', 'ASC');
		$Products = $this->getProducts();
		$Categories = $this->_getCategoryTree();

		if($Products&&$Categories){
			$xml->loadXML('<?xml version="1.0" encoding="UTF-8"?>
							<products
								xmlns="http://www.sklepy24.pl"
								xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
								xsi:schemaLocation="http://www.sklepy24.pl http://www.sklepy24.pl/formats/products.xsd"
								date="' . date('Y-m-d') . '">
							</products>');

			$offers = $xml->getElementsByTagName('products');
			$offers = $offers->item(0);

			foreach ($Products AS $Product){

					$offer = $xml->createElement('product');
					$offers->appendChild($offer);

					$id = $xml->createAttribute('id');
					$offer->appendChild($id);

					$id_text = $xml->createTextNode($Product['id_product']);
					$id->appendChild($id_text);

					$name = $xml->createElement('name')-> appendChild($xml->createCDATASection($Product['name']));
					$offer->appendChild($name);
					

					$url = $xml->createElement('url',htmlspecialchars($link->getProductLink($Product['id_product'], $Product['link_rewrite'],null,null,null,null,null,1)));
					$offer->appendChild($url);

					if ($Product['manufacturer_name']==NULL)
						$brand = $xml->createElement('brand'," ");
					else
						$brand = $xml->createElement('brand',$Product['manufacturer_name']);
					
					$offer->appendChild($brand);


					$categories = $xml->createElement('categories');
					$offer->appendChild($categories);

					$category = $xml->createElement('category',$Categories[$Product['id_category_default']]['path']);
					$categories->appendChild($category);

					$image = Image::getCover($Product['id_product']);
					if ($image['id_image'])
						$img_url = $link->getImageLink($Product['link_rewrite'], $image['id_product'].'-'.$image['id_image'],$this->getXmlImageType());
					else
						$img_url = "";
					$photo = $xml->createElement('photo',$img_url);
					$offer->appendChild($photo);

					$description_cdata = $xml->createCDATASection(strip_tags(html_entity_decode($Product['description_short'],ENT_COMPAT,"UTF-8"),"<br><li><p><ul><tr>"));
					$description = $xml->createElement('description');
					$description->appendChild($description_cdata);
					$offer->appendChild($description);

					$price = $xml->createElement('price', $this->getTaxedPrice($Product));
					$offer->appendChild($price);
			}
		}
		return $xml;
	}
	private function _doRadar(){
		$link = new Link();
        $xml = new DOMDocument();
		
		//$Products = Product::getProducts(Language::getIdByIso("pl"), 0, NULL, 'name', 'ASC');
		$Products = $this->getProducts();
		$Categories = $this->_getCategoryTree();

		if($Products&&$Categories){

			$xml->loadXML('<?xml version="1.0" encoding="UTF-8"?>
						   <radar wersja="1.0">
							 <oferta/>
							</radar>');

			$oferta = $xml->getElementsByTagName('oferta');
			$oferta = $oferta->item(0);


			foreach ($Products AS $Product){


				
				$produkt = $xml->createElement('produkt');
				$oferta->appendChild($produkt);

				$grupa1 = $xml->createElement('grupa1');
				$produkt->appendChild($grupa1);

				$nazwa = $xml->createElement('nazwa',self::htmlentities($Product['name'])); 
				$grupa1->appendChild($nazwa);

				$producent = $xml->createElement('producent',$Product['manufacturer_name']);
				$grupa1->appendChild($producent);

				$description_cdata = $xml->createCDATASection(strip_tags(html_entity_decode($Product['description_short'],ENT_COMPAT,"UTF-8")));
				$opis = $xml->createElement('opis');
				$opis->appendChild($description_cdata);
				$grupa1->appendChild($opis);

				$id = $xml->createElement('id',$Product['id_product']);
				$grupa1->appendChild($id);
				
				$url = $xml->createElement('url',htmlspecialchars($link->getProductLink($Product['id_product'], $Product['link_rewrite'],null,null,null,null,null,1)));
				$grupa1->appendChild($url);

				$image = Image::getCover($Product['id_product']);					
				if ($image['id_image'])
					//$img_url = ''.$this->getShopHost().'img/p/'.$image['id_product'].'-'.$image['id_image'].'-large.jpg';
					$img_url = $link->getImageLink($Product['link_rewrite'], $image['id_product'].'-'.$image['id_image'],$this->getXmlImageType());
				else
					$img_url = "";
				$foto = $xml->createElement('foto',$img_url);
				$grupa1->appendChild($foto);

				$kategoria = $xml->createElement('kategoria',$Categories[$Product['id_category_default']]['path']);
				$grupa1->appendChild($kategoria);

				$cena = $xml->createElement('cena',$this-> getTaxedPrice($Product)); 
				$grupa1->appendChild($cena);
			}
		}
		return $xml;
	}
	private function _doOkazje(){
		$link = new Link();
        $xml = new DOMDocument();
        
		
		//$Products = Product::getProducts(Language::getIdByIso("pl"), 0, NULL, 'name', 'ASC');
		$Products = $this->getProducts();
		$Categories = $this->_getCategoryTree();

		if($Products&&$Categories){
			$xml->loadXML('<?xml version="1.0" encoding="UTF-8" ?>
							<okazje>
								<offers/>
							</okazje>');

			$offers = $xml->getElementsByTagName('offers');
			$offers = $offers->item(0);

			foreach ($Products AS $Product){

				$offer = $xml->createElement('offer');
				$offers->appendChild($offer);

				$id = $xml->createElement('id',$Product['id_product']);
				$offer->appendChild($id);

				$name = $xml->createElement('name',self::htmlentities($Product['name'])); 
				$offer->appendChild($name);

				$description_cdata = $xml->createCDATASection(strip_tags(html_entity_decode($Product['description_short'],ENT_COMPAT,"UTF-8"),"<br><li><p><ul><tr>"));
				$description = $xml->createElement('description');
				$description->appendChild($description_cdata);
				$offer->appendChild($description);

				$category = $xml->createElement('category',$Categories[$Product['id_category_default']]['path']);
				$offer->appendChild($category);

				//$price = $xml->createElement('price',$this-> getTaxedPrice($Product)); 
				//$offer->appendChild($price);
				
				if (Product::isDiscounted($Product['id_product']))
                {
                    $price = $xml->createElement('price',$this-> getTaxedPrice($Product));
                    $offer->appendChild($price);
                   
                    $price = $xml->createElement('old_price',Product::getPriceStatic($Product['id_product'], true, NULL, 2, NULL, false, false));
                    $offer->appendChild($price);
                   /// oh watever... niech juz ttaj bedzie ten product::getPriceStatic :)
                }
                else {
                    //$p=new Product($Product['id_product']);
                    $price = $xml->createElement('price',$this-> getTaxedPrice($Product));
                    $offer->appendChild($price);
                }
				
				

				$url = $xml->createElement('url', htmlspecialchars($link->getProductLink($Product['id_product'], $Product['link_rewrite'],null,null,null,null,null,1)));
				$offer->appendChild($url);

				$producer = $xml->createElement('producer',$Product['manufacturer_name']);
				$offer->appendChild($producer);

				$image = Image::getCover($Product['id_product']);					
				if ($image['id_image'])
					//$img_url = ''.$this->getShopHost().'img/p/'.$image['id_product'].'-'.$image['id_image'].'-large.jpg';
					$img_url = 'http://'.$link->getImageLink($Product['link_rewrite'], $image['id_product'].'-'.$image['id_image'],$this->getXmlImageType());
				else
					$img_url = "";
				$image = $xml->createElement('image',$img_url);
				$offer->appendChild($image);
			}
		}
		return $xml;
	}
	private function _doZakupyOnet(){
		$link = new Link();
        $xml = new DOMDocument();
		
		//$Products = Product::getProducts(Language::getIdByIso("pl"), 0, NULL, 'name', 'ASC');
		$Products = $this->getProducts();
		$Categories = $this->_getCategoryTree();

		if($Products&&$Categories){
			$xml->loadXML('<?xml version="1.0" encoding="UTF-8" ?>
							<oferty aktualizacja="N" xmlns="http://www.zakupy.onet.pl/walidacja/oferty-partnerzy.xsd">

							</oferty>');

			$offers = $xml->getElementsByTagName('oferty');
			$offers = $offers->item(0);

			foreach ($Products AS $Product){

					$offer = $xml->createElement('oferta');
					$offers->appendChild($offer);

					$id = $xml->createElement('identyfikator',$Product['id_product']);
					$offer->appendChild($id);
					
					$name = $xml->createElement('nazwa',self::htmlentities($Product['name'])); 
					$offer->appendChild($name);

					$description_cdata = $xml->createCDATASection(strip_tags(html_entity_decode($Product['description_short'],ENT_COMPAT,"UTF-8"),"<br><li><p><ul><tr>"));
					$description = $xml->createElement('opis');
					$description->appendChild($description_cdata);
					$offer->appendChild($description);

					$category = $xml->createElement('sciezka_kategorii',$Categories[$Product['id_category_default']]['path']);
					$offer->appendChild($category);

					$category = $xml->createElement('id_kategorii_sklepu',$Product['id_category_default']);
					$offer->appendChild($category);

					$price = $xml->createElement('cena',$this-> getTaxedPrice($Product)); 
					$offer->appendChild($price);

					$url = $xml->createElement('url', htmlspecialchars($link->getProductLink($Product['id_product'], $Product['link_rewrite'])));
					$offer->appendChild($url);

					$producer = $xml->createElement('marka_producent',$Product['manufacturer_name']);
					$offer->appendChild($producer);

					$image = Image::getCover($Product['id_product']);					
					if ($image['id_image'])
						//$img_url = $this->getShopHost().'img/p/'.$image['id_product'].'-'.$image['id_image'].'-large.jpg';
						$img_url = $link->getImageLink($Product['link_rewrite'], $image['id_product'].'-'.$image['id_image'], $this->getXmlImageType());
					else
						$img_url = "";
					$image = $xml->createElement('zdjecie',$img_url);
					$offer->appendChild($image);
			}
		}		
		return $xml;
	}

}

