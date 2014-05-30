<?php
/*
* 2007-2012 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 7048 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

class HomeFeatured extends Module
{
	private $_html = '';
	private $_postErrors = array();

	function __construct()
	{
		$this->name = 'homefeatured';
		$this->tab = 'front_office_features';
		$this->version = '0.9';
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('Featured Products on the homepage');
		$this->description = $this->l('Displays Featured Products in the middle of your homepage.');
	}

	function install()
	{
		if (!Configuration::updateValue('HOME_FEATURED_NBR', 8) || !parent::install() || !$this->registerHook('displayHome') || !$this->registerHook('displayHeader'))
			return false;
		return true;
	}

	public function getContent()
	{
		$output = '<h2>'.$this->displayName.'</h2>';
		if (Tools::isSubmit('submitHomeFeatured'))
		{
			$nbr = (int)(Tools::getValue('nbr'));
			if (!$nbr OR $nbr <= 0 OR !Validate::isInt($nbr))
				$errors[] = $this->l('Invalid number of products');
			else
				Configuration::updateValue('HOME_FEATURED_NBR', (int)($nbr));
			if (isset($errors) AND sizeof($errors))
				$output .= $this->displayError(implode('<br />', $errors));
			else
				$output .= $this->displayConfirmation($this->l('Settings updated'));
		}
		return $output.$this->displayForm();
	}

	public function displayForm()
	{
		$output = '
		<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="post">
			<fieldset><legend><img src="'.$this->_path.'logo.gif" alt="" title="" />'.$this->l('Settings').'</legend>
				<p>'.$this->l('In order to add products to your homepage, just add them to the "home" category.').'</p><br />
				<label>'.$this->l('Number of products displayed').'</label>
				<div class="margin-form">
					<input type="text" size="5" name="nbr" value="'.Tools::safeOutput(Tools::getValue('nbr', (int)(Configuration::get('HOME_FEATURED_NBR')))).'" />
					<p class="clear">'.$this->l('The number of products displayed on homepage (default: 10).').'</p>

				</div>
				<center><input type="submit" name="submitHomeFeatured" value="'.$this->l('Save').'" class="button" /></center>
			</fieldset>
		</form>';
		return $output;
	}

	public function hookDisplayHeader($params)
	{
		$this->context->controller->addCss($this->_path.'homefeatured.css');
	}


     //random products
     public function hookDisplayHome($params)
    {
        /* random product

        //$category = new Category(8, (int)Context::getContext()->language->id);
        $products = array();
        $nb = (int)(Configuration::get('HOME_FEATURED_NBR'));
                $category[0] = new Category(11, Configuration::get('PS_LANG_DEFAULT'));
                $category[1] = new Category(20, Configuration::get('PS_LANG_DEFAULT'));
                $category[2] = new Category(29, Configuration::get('PS_LANG_DEFAULT'));
                $category[3] = new Category(41, Configuration::get('PS_LANG_DEFAULT'));
                $category[4] = new Category(8, Configuration::get('PS_LANG_DEFAULT'));
                $category[5] = new Category(37, Configuration::get('PS_LANG_DEFAULT'));
                $category[6] = new Category(38, Configuration::get('PS_LANG_DEFAULT'));
                $category[7] = new Category(12, Configuration::get('PS_LANG_DEFAULT'));

                for ($i=0;$i<8;$i++) {
                    $list = $category[$i]->getProducts((int)($params['cookie']->id_lang), 1, 6,null,null,false,true,true,1);
                   array_push($products,$list[0]);
                }
         */

        $product[] = $this->getProduct(6,356,1,'id_product','asc');
        $product[] = $this->getProduct(6,591,1,'id_product','asc');
        $product[] = $this->getProduct(6,1205,1,'id_product','asc');
        $product[] = $this->getProduct(6,589,1,'id_product','asc');
        $product[] = $this->getProduct(6,731,1,'id_product','asc');
        $product[] = $this->getProduct(6,107,1,'id_product','asc');
        $product[] = $this->getProduct(6,197,1,'id_product','asc');
        $product[] = $this->getProduct(6,534,1,'id_product','asc');

        $this->setImg($product);



        $this->smarty->assign(array(
            'products' => $product,
            'add_prod_display' => Configuration::get('PS_ATTRIBUTE_CATEGORY_DISPLAY'),
            'homeSize' => Image::getSize('homefeature'),
        ));

        return $this->display(__FILE__, 'homefeatured.tpl');
    }

    public function setImg(&$products) {

        foreach ($products as &$p) {
            $tp = new Product($p['id_product']);
            $img  = $tp->getImages(6);
            $p['id_image'] = $img[0]['id_image'];
            $p =  Product::getProductProperties(6,$p);

        }
    }

    public static function getProduct($id_lang, $id_product, $limit, $order_by, $order_way, $id_category = false,
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
}
