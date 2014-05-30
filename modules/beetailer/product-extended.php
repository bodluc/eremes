<?php

class ProductExtended {
  protected static $producPropertiesCache = array();
  public	  static $_taxCalculationMethod = PS_TAX_EXC;


  public static function getProducts($id_category, $id_product, $id_lang, $p, $n, $orderBy = NULL, $orderWay = NULL, $getTotal = false, $active = true, $random = false, $randomNumberProducts = 1)
  {
    /* global $cookie; */

    if(!preg_match("/^1.(3|4).*/", _PS_VERSION_)){
      $context = Context::getContext();
    }

    if ($p < 1) $p = 1;
    if ($n < 1) $n = 1;

    if (empty($orderBy))
      $orderBy = 'position';
    else
      /* Fix for all modules which are now using lowercase values for 'orderBy' parameter */
    $orderBy = strtolower($orderBy);

    if (empty($orderWay))
      $orderWay = 'ASC';
    if ($orderBy == 'id_product' OR	$orderBy == 'date_add')
      $orderByPrefix = 'p';
    elseif ($orderBy == 'name')
      $orderByPrefix = 'pl';
    elseif ($orderBy == 'manufacturer')
    {
      $orderByPrefix = 'm';
      $orderBy = 'name';
    }
    elseif ($orderBy == 'position')
      $orderByPrefix = 'cp';

    if ($orderBy == 'price')
      $orderBy = 'orderprice';

    if (!Validate::isBool($active) OR !Validate::isOrderBy($orderBy) OR !Validate::isOrderWay($orderWay))
      die (Tools::displayError());

    $id_supplier = (int)(Tools::getValue('id_supplier'));

    /* Return only the number of products */
    if ($getTotal)
    {
      $result = ProductExtended::getDbInstance()->getRow('
        SELECT COUNT(cp.`id_product`) AS total
        FROM `'._DB_PREFIX_.'product` p
        LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON p.`id_product` = cp.`id_product`
        WHERE cp.`id_category` = '.(int)($id_category).($active ? ' AND p.`active` = 1' : '').'
        '.($id_supplier ? 'AND p.id_supplier = '.(int)($id_supplier) : ''));
      return isset($result) ? $result['total'] : 0;
    }

    if (isset($id_category)){
      $where = 'cp.`id_category` = '.(int)($id_category);
    } elseif (isset($id_product)) {
      $where = 'p.`id_product` = '.(int)($id_product);
    }


    if(preg_match("/^1.3.*/", _PS_VERSION_)){
      $sql = '
      SELECT p.*, p.price as original_price, pa.`id_product_attribute`, pl.`description`, pl.`description_short`, pl.`available_now`, pl.`available_later`, pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`, pl.`name`, i.`id_image`, il.`legend`, m.`name` AS manufacturer_name, tl.`name` AS tax_name, t.`rate`, cl.`name` AS category_default, DATEDIFF(p.`date_add`, DATE_SUB(NOW(), INTERVAL '.(Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).' DAY)) > 0 AS new,
        (p.`price` * IF(t.`rate`,((100 + (t.`rate`))/100),1) - IF((DATEDIFF(`reduction_from`, CURDATE()) <= 0 AND DATEDIFF(`reduction_to`, CURDATE()) >=0) OR `reduction_from` = `reduction_to`, IF(`reduction_price` > 0, `reduction_price`, (p.`price` * IF(t.`rate`,((100 + (t.`rate`))/100),1) * `reduction_percent` / 100)),0)) AS price 
      FROM `'._DB_PREFIX_.'category_product` cp
      LEFT JOIN `'._DB_PREFIX_.'product` p ON p.`id_product` = cp.`id_product`
      LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa ON (p.`id_product` = pa.`id_product` AND default_on = 1)
      LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (p.`id_category_default` = cl.`id_category` AND cl.`id_lang` = '.intval($id_lang).')
      LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product` AND pl.`id_lang` = '.intval($id_lang).')
      LEFT JOIN `'._DB_PREFIX_.'image` i ON (i.`id_product` = p.`id_product` AND i.`cover` = 1)
      LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = '.intval($id_lang).')
      LEFT JOIN `'._DB_PREFIX_.'tax` t ON t.`id_tax` = p.`id_tax`
      LEFT JOIN `'._DB_PREFIX_.'tax_lang` tl ON (t.`id_tax` = tl.`id_tax` AND tl.`id_lang` = '.intval($id_lang).')
      LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON m.`id_manufacturer` = p.`id_manufacturer`
      WHERE '. $where .($active ? ' AND p.`active` = 1' : '').'
      '.($id_supplier ? 'AND p.id_supplier = '.$id_supplier : '');
    }else if(preg_match("/^1.4.*/", _PS_VERSION_)){
      $sql = '
        SELECT p.`price` as original_price, p.out_of_stock, p.id_product,p.id_tax_rules_group,p.quantity,p.id_category_default,p.id_manufacturer,p.active,p.id_supplier, pl.`description`, pl.`description_short`, pl.`link_rewrite`, pl.`name`, t.`rate`
        FROM `'._DB_PREFIX_.'category_product` cp
        LEFT JOIN `'._DB_PREFIX_.'product` p ON p.`id_product` = cp.`id_product`
        LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa ON (p.`id_product` = pa.`id_product` AND default_on = 1)
        LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (p.`id_category_default` = cl.`id_category` AND cl.`id_lang` = '.(int)($id_lang).')
        LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product` AND pl.`id_lang` = '.(int)($id_lang).')
        LEFT JOIN `'._DB_PREFIX_.'image` i ON (i.`id_product` = p.`id_product` AND i.`cover` = 1)
        LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)($id_lang).')
      LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (p.`id_tax_rules_group` = tr.`id_tax_rules_group`
        AND tr.`id_country` = '.(int)Country::getDefaultCountryId().'
        AND tr.`id_state` = 0)
        LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
        LEFT JOIN `'._DB_PREFIX_.'tax_lang` tl ON (t.`id_tax` = tl.`id_tax` AND tl.`id_lang` = '.(int)($id_lang).')
        LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON m.`id_manufacturer` = p.`id_manufacturer`
        WHERE '. $where .($active ? ' AND p.`active` = 1' : '').'
        '.($id_supplier ? 'AND p.id_supplier = '.(int)$id_supplier : '');
    }else{

      $sql = '
    SELECT p.`price` as original_price, stock.out_of_stock as out_of_stock_2, p.out_of_stock, p.id_product,p.id_tax_rules_group,IFNULL(stock.quantity, 0) as quantity,p.id_category_default,p.id_manufacturer,p.active,p.id_supplier, pl.`description`, pl.`description_short`, pl.`link_rewrite`, pl.`name`, t.`rate`,pl.available_now, (product_shop.`price` * IF(t.`rate`,((100 + (t.`rate`))/100),1)) AS orderprice
				FROM `'._DB_PREFIX_.'category_product` cp
				LEFT JOIN `'._DB_PREFIX_.'product` p
					ON p.`id_product` = cp.`id_product`
				'.Shop::addSqlAssociation('product', 'p').'
				LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa
				ON (p.`id_product` = pa.`id_product`)
				'.Shop::addSqlAssociation('product_attribute', 'pa', false, 'product_attribute_shop.`default_on` = 1').'
				'.Product::sqlStock('p', 'product_attribute_shop', false, $context->shop).'
				LEFT JOIN `'._DB_PREFIX_.'category_lang` cl
					ON (product_shop.`id_category_default` = cl.`id_category`
					AND cl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('cl').')
				LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
					ON (p.`id_product` = pl.`id_product`
					AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').')
				LEFT JOIN `'._DB_PREFIX_.'image` i
					ON (i.`id_product` = p.`id_product`
					AND i.`cover` = 1)
				LEFT JOIN `'._DB_PREFIX_.'image_lang` il
					ON (i.`id_image` = il.`id_image`
					AND il.`id_lang` = '.(int)$id_lang.')
				LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr
					ON (product_shop.`id_tax_rules_group` = tr.`id_tax_rules_group`
					AND tr.`id_country` = '.(int)$context->country->id.'
					AND tr.`id_state` = 0
					AND tr.`zipcode_from` = 0)
				LEFT JOIN `'._DB_PREFIX_.'tax` t
					ON (t.`id_tax` = tr.`id_tax`)
				LEFT JOIN `'._DB_PREFIX_.'tax_lang` tl
					ON (t.`id_tax` = tl.`id_tax`
					AND tl.`id_lang` = '.(int)$id_lang.')
				LEFT JOIN `'._DB_PREFIX_.'manufacturer` m
					ON m.`id_manufacturer` = p.`id_manufacturer`
				WHERE '. $where . ' AND product_shop.`id_shop` = '.(int)$context->shop->id.'
				AND ((product_attribute_shop.id_product_attribute IS NOT NULL OR pa.id_product_attribute IS NULL) 
					OR (product_attribute_shop.id_product_attribute IS NULL AND pa.default_on=1))'
					.($active ? ' AND product_shop.`active` = 1' : '')
					.($id_supplier ? ' AND p.id_supplier = '.(int)$id_supplier : '');
    }

    if ($random === true)
    {
      $sql .= ' ORDER BY RAND()';
      $sql .= ' LIMIT 0, '.(int)($randomNumberProducts);
    }
    else
    {
      $sql .= ' ORDER BY '.(isset($orderByPrefix) ? $orderByPrefix.'.' : '').'`'.pSQL($orderBy).'` '.pSQL($orderWay).'
        LIMIT '.(((int)($p) - 1) * (int)($n)).','.(int)($n);
    }

    $result = ProductExtended::getDbInstance()->ExecuteS($sql); 
    if ($orderBy == 'orderprice')
      Tools::orderbyPrice($result, $orderWay);

    if (!$result)
      return false;

    return ProductExtended::getProductsProperties($id_lang, $result);
  }

  public static function getProductsProperties($id_lang, $query_result)
  {
    $resultsArray = array();
    foreach ($query_result AS $row)
      if ($row2 = ProductExtended::getProductProperties($id_lang, $row))
      $resultsArray[] = $row2;
    return $resultsArray;
  }

  public static function getProductProperties($id_lang, $row)
  {
    if (!$row['id_product'])
      return false;

    // Product::getDefaultAttribute is only called if id_product_attribute is missing from the SQL query at the origin of it: consider adding it in order to avoid unnecessary queries
    $row['allow_oosp'] = Product::isAvailableWhenOutOfStock($row['out_of_stock']);
    if ((!isset($row['id_product_attribute']) OR !$row['id_product_attribute'])
      AND ((isset($row['cache_default_attribute']) AND ($ipa_default = $row['cache_default_attribute']) !== NULL)
      OR ($ipa_default = Product::getDefaultAttribute($row['id_product'], !$row['allow_oosp'])))
      )
      $row['id_product_attribute'] = $ipa_default;

    if (!isset($row['id_product_attribute']))
      $row['id_product_attribute'] = 0;

    // Tax
    $usetax = Tax::excludeTaxeOption();

    $cacheKey = $row['id_product'].'-'.$row['id_product_attribute'].'-'.$id_lang.'-'.(int)($usetax);
    if (array_key_exists($cacheKey, self::$producPropertiesCache))
      return self::$producPropertiesCache[$cacheKey];

    // Datas
    $row['category'] = Category::getLinkRewrite((int)$row['id_category_default'], (int)($id_lang));

    if(!preg_match("/^1.3.*/", _PS_VERSION_)){ // Not available in Prestashop 1.3.x
      $row['reduction'] = Product::getPriceStatic((int)($row['id_product']), (bool)$usetax, (int)($row['id_product_attribute']), 6, NULL, true, true, 1, true, NULL, NULL, NULL, $specific_prices);
      $row['specific_prices'] = $specific_prices;
    }
    if ($row['id_product_attribute'])
    {
      $row['quantity_all_versions'] = $row['quantity'];
      $row['quantity'] = Product::getQuantity((int)$row['id_product'], $row['id_product_attribute'], isset($row['cache_is_pack']) ? $row['cache_is_pack'] : NULL);
    }
    $row['id_image'] = Product::defineProductImage($row, $id_lang);
    $row['features'] = Product::getFrontFeaturesStatic((int)$id_lang, $row['id_product']);

    // Pack management
    $row['pack'] = (!isset($row['cache_is_pack']) ? Pack::isPack($row['id_product']) : (int)$row['cache_is_pack']);
    $row['packItems'] = $row['pack'] ? Pack::getItemTable($row['id_product'], $id_lang) : array();
    $row['nopackprice'] = $row['pack'] ? Pack::noPackPrice($row['id_product']) : 0;
    if ($row['pack'] AND !Pack::isInStock($row['id_product']))
      $row['quantity'] =  0;

    $sql_combination = '
      SELECT pa.id_product_attribute, pa.price, pa.quantity, pa.id_product
      FROM `'._DB_PREFIX_.'product` p
      LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa ON (p.`id_product` = pa.`id_product`)
      WHERE p.`id_product` =' . $row['id_product'];

    $result_combination = ProductExtended::getDbInstance()->ExecuteS($sql_combination); 
    $row['combinations'] = Array();

    if($result_combination){
      foreach ($result_combination as $combination){
        $combination['attributes'] = Array();

        /* New combinations system Prestashop 1.5.x */
        if(!preg_match("/^1.(3|4).*/", _PS_VERSION_)){
          $combination['quantity'] = StockAvailable::getQuantityAvailableByProduct($row['id_product'], $combination['id_product_attribute']);
        }

        if(isset($combination['id_product_attribute'])){
          $sql_attribute = '
            SELECT pa.id_product_attribute, agl.id_attribute_group, al.name as name_value, agl.name as name_option
            FROM `'._DB_PREFIX_.'product_attribute` pa
            LEFT JOIN `'._DB_PREFIX_.'product_attribute_combination` pac ON (pac.`id_product_attribute` = pa.`id_product_attribute`)
            LEFT JOIN `'._DB_PREFIX_.'attribute` a ON (a.`id_attribute` = pac.`id_attribute`)
            LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al ON (al.`id_attribute` = a.`id_attribute` AND al.`id_lang` = '. $id_lang .')
            LEFT JOIN `'._DB_PREFIX_.'attribute_group` ag ON (ag.`id_attribute_group` = a.`id_attribute_group`)
            LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl ON (agl.`id_attribute_group` = ag.`id_attribute_group` AND agl.`id_lang` = '. $id_lang .')
            WHERE pa.`id_product_attribute` =' . $combination['id_product_attribute'];

          $result_attribute = ProductExtended::getDbInstance()->ExecuteS($sql_attribute); 
          
          if($result_attribute)
            foreach ($result_attribute as $attribute)
              array_push($combination['attributes'], $attribute);

          array_push($row['combinations'], $combination);
        }
      }
    }

    $sql_image = '
      SELECT DISTINCT i.*, pl.link_rewrite
      FROM `'._DB_PREFIX_.'product` p
      LEFT JOIN `'._DB_PREFIX_.'image` i ON (p.`id_product` = i.`id_product`)
      LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product`)
      WHERE p.`id_product` =' . $row['id_product'];

    $result_image = ProductExtended::getDbInstance()->ExecuteS($sql_image); 
    $row['images'] = Array();
    $lang = new Language($id_lang);
    $row['url_locale'] = $lang->iso_code;


    if($result_image){
      $link = preg_match("/^1.(3|4).*/", _PS_VERSION_) ? new Link() : Context::getContext()->link;
      foreach ($result_image as $image){
        if(!preg_match("/^1.3.*/", _PS_VERSION_)){ // Image URL gives relative version using 1.3.x-  
          $image['image_url'] = $link->getImageLink($image['link_rewrite'], $image['id_product'].'-'.$image['id_image']);
        }
        array_push($row['images'], $image);
      }
    }

    self::$producPropertiesCache[$cacheKey] = $row;
    return self::$producPropertiesCache[$cacheKey];
  }

  public static function getDbInstance(){
    if(preg_match("/^1.3.*/", _PS_VERSION_)){
      return Db::getInstance();
    }else{
      return Db::getInstance(_PS_USE_SQL_SLAVE_);
    }
  }
}

