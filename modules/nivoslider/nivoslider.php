<?php
class nivoslider extends Module {

	private $_html = '';

	public function __construct()
	{
		$this->name = 'nivoslider';
		$this->tab = 'front_office_features';
		$this->version = '1.2';
		$this->author = 'PrestaShop';
		$this->need_instance = 0;
		$this->secure_key = Tools::encrypt($this->name);

		parent::__construct();

		$this->displayName = $this->l('NivoSlider');
		$this->description = $this->l('NivoSlider');
	}

public function install() {
	/* Adds Module */
		if (parent::install() && $this->registerHook('displayHome') )
		{
			return;
		}
		return false;
}

public function getContent() {

	if(Tools::isSubmit('submit_text')) {

	  Configuration::updateValue(
			  $this->name.'_text_to_show',
			  Tools::getValue('the_text')
			  );

	}

	$this->_generateForm();
	return $this->_html;
}

private function _generateForm() {

	$textToShow=Configuration::get($this->name.'_text_to_show');

	$this->_html .= '<form action="'.$_SERVER['REQUEST_URI'].'" method="post">';
	$this->_html .= '<label>'.$this->l('Enter your text: ').'</label>';
	$this->_html .= '<div class="margin-form">';
	$this->_html .= '<input type="text" name="the_text" value="'.$textToShow.'" >';
	$this->_html .= '<input type="submit" name="submit_text" ';
	$this->_html .= 'value="'.$this->l('Update the text').'" class="button" />';
	$this->_html .= '</div>';
	$this->_html .= '</form>';
}
	public static function getProducts($id_lang, $id_product, $limit, $order_by, $order_way, $id_category = false,
		$only_active = false, Context $context = null)
	{
		if (!$context)
			$context = Context::getContext();

		$front = true;
		if (!in_array($context->controller->controller_type, array('front', 'modulefront')))
			$front = false;

		if (!Validate::isOrderBy($order_by) || !Validate::isOrderWay($order_way))
			die (Tools::displayError());
		if ($order_by == 'id_product' || $order_by == 'price' || $order_by == 'date_add')
			$order_by_prefix = 'p';
		else if ($order_by == 'name')
			$order_by_prefix = 'pl';
		else if ($order_by == 'position')
			$order_by_prefix = 'c';

		if (strpos($order_by, '.') > 0)
		{
			$order_by = explode('.', $order_by);
			$order_by_prefix = $order_by[0];
			$order_by = $order_by[1];
		}
		$sql = 'SELECT p.*, product_shop.*, pl.* , t.`rate` AS tax_rate, m.`name` AS manufacturer_name, s.`name` AS supplier_name
				FROM `'._DB_PREFIX_.'product` p
				'.Shop::addSqlAssociation('product', 'p').'
				LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product` '.Shop::addSqlRestrictionOnLang('pl').')
				LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (product_shop.`id_tax_rules_group` = tr.`id_tax_rules_group`
				   AND tr.`id_country` = '.(int)Context::getContext()->country->id.'
				   AND tr.`id_state` = 0)
				   LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
				LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON (m.`id_manufacturer` = p.`id_manufacturer`)
				LEFT JOIN `'._DB_PREFIX_.'supplier` s ON (s.`id_supplier` = p.`id_supplier`)'.
				($id_category ? 'LEFT JOIN `'._DB_PREFIX_.'category_product` c ON (c.`id_product` = p.`id_product`)' : '').'
				WHERE p.id_product='.$id_product.' AND pl.`id_lang` = '.(int)$id_lang.
					($id_category ? ' AND c.`id_category` = '.(int)$id_category : '').
					($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '').
					($only_active ? ' AND product_shop.`active` = 1' : '').'
				ORDER BY '.(isset($order_by_prefix) ? pSQL($order_by_prefix).'.' : '').'`'.pSQL($order_by).'` '.pSQL($order_way).
				($limit > 0 ? ' LIMIT '.(int)$start.','.(int)$limit : '');
		$rq = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
		if ($order_by == 'price')
			Tools::orderbyPrice($rq, $order_way);
		return ($rq[0]);
	}

public function hookDisplayHome() {

	global $smarty;
	$smarty->assign('our_text',Configuration::get($this->name.'_text_to_show'));

	$this->context->controller->addJqueryPlugin('nivo.slider');
	$this->context->controller->addCSS($this->_path.'nivo.css');
	//$this->context->controller->addCSS($this->_path.'/themes/default/default.css');
	//$this->context->controller->addCSS(($this->_path).'blockcms.css', 'all');

	// $product = ProductSale::getBestSales(3);
		//$product = Product::getNewProducts(6,2,5);

        $product[] = $this->getProducts(6,1271,1,'id_product','asc');
		$product[] = $this->getProducts(6,74,1,'id_product','asc');
		$product[] = $this->getProducts(6,117,1,'id_product','asc');
		$product[] = $this->getProducts(6,491,1,'id_product','asc');

		if ($product == null)
			return;
		if (isset($product[0]) == false)
			$product = array($product);

		  $link = new Link();

		foreach ($product as &$p) {
			//$srcImg = _PS_IMG_DIR_."p/".$p['id_image'].".jpg";


			$product2 = new Product($p['id_product']);
			$images =   $product2->getImages(6) ;

			$image = new Image($images[0]['id_image']);
			$srcImg = _PS_IMG_DIR_.'p/'.$image->getExistingImgPath().'.jpg';
			if (!file_exists($srcImg)) {
				$p = null;
				continue;
			}

			$dstImg = _PS_IMG_DIR_."slides/".$image->id_image.".jpg";
			$p['path']="http://".$_SERVER['HTTP_HOST']._PS_IMG_."slides/".$image->id_image.".jpg";
			$p['link']=$link->getProductLink($p['id_product']);
			//$p['price_tax_exc'] = round($p['price_tax_exc'],2);
			$p['price'] = round($p['price']*1.23,0);
			//$p['path_logo'] =
			if (!file_exists($dstImg))
			{
				ImageManager::resize($srcImg,$dstImg,500,370);
			}
		}

        $m = Manufacturer::getManufacturers();
        $list = array();
        for ($i=0;$i<6;$i++) {
            do {
            $j=rand(0,count($m));
            $path=_PS_IMG_DIR_."m/".$m[$j]['id_manufacturer']."-nivo.jpg";
            }
            while (!(file_exists($path) && !in_array($m[$j]['id_manufacturer'],$list)));
            $M[$i] = $m[$j];
            $list[] = $m[$j]['id_manufacturer'];
        }

        $this->smarty->assign('p', $product);
		$this->smarty->assign('m', $M);

	//return $this->display(__FILE__, 'coda/index.tpl');
	//return $this->display(__FILE__, 'liquid/index.tpl');
	//return $this->display(__FILE__, 'inco/inco.tpl');
	return $this->display(__FILE__, 'nivo/nivo.tpl');
	//return $this->display(__FILE__, 'plus/index.tpl');

}

} // End of: testtwo.php
?>
