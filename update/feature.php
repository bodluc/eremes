<?php
require('update.php');

        // Radia - 11

        // Głośniki - 20
        // Subwoofery - 29
        // Sub w obudowach - 41

        // Wzmacniacze - 8

        // Marine - 37
        // Akumlatory - 38
        // Akcesoria - 17

define ('CATEGORY',0);
define('WRITE',0);

class FeatureRMS {

    private $fValue = array();
    private $category = CATEGORY;
    private $suffix;

    function addShortDesc() {
        $update = new UpdateRMS();

        echo CATEGORY." <br>";
        $products = Product::getProducts(6,0,9999,'id_product','asc',$this->category);

        foreach ($products as &$p) {
            $id = $p['id_product'];

            $html = "";
            $feature = Product::getFeaturesStatic($id);
            foreach ($feature as $f) {
              $f_name = $this->getFeatureName($f['id_feature']);
              $f_name = $f_name['name'];
              $f_value = FeatureValue::getFeatureValueLang($f['id_feature_value']);
              $f_value = $f_value[0]['value'];
              $suffix = $this->suffix($f_name);
              $html .= "<span>$f_name:</span><b>$f_value $suffix</b></br>";
            }

            $update->updateShortDesc($id,$html);

        }
    }
    function searchFeauture() {

        $ret=Db::getInstance()->executeS("SELECT * FROM `ps_product_lang` ");

        $this->category = CATEGORY;
        echo "$this->category <br>";
        $p = new Product();
        $products = Product::getProducts(6,0,999,'id_product','asc',$this->category);


        foreach ($products as $p) {
            $desc = $p['description'];

            $id = $p['id_product'];
            $title = $p['name'];
            $desc = Update::rmDescHtml($desc);

            $featureName = 'Impedancja';  $this->suffix = 'Ohm'; $f[$featureName]=$this->_impedance($desc);
            //$featureName = 'Moc RMS';  $this->suffix = 'Watt'; $f[$featureName]=$this->_rms($desc);
            //$featureName = 'Moc Max';  $this->suffix = 'Watt'; $f[$featureName]=$this->_max($desc);

            //if (!$f[$featureName]) $f[$featureName]=$this->_rms($p['description_short']);

            $fval = $f[$featureName][1];
            $features = sprintf("$featureName:%s",$fval);
            $is = $this->getFeature($p['id_product'],$features);

            if ($is && $fval)
            {
                echo $p['id_product']." <b style='color:green'>" ;
                print_r($f);
                echo "</b><br>";
            }
            if (!$is && $fval)
            {
                echo "$id $title <b style='color:blue'>" ;
                print_r($f);
                echo "</b><br>";

                if (WRITE) {
                  $this->addFeature($p['id_product'],$features);
                  $rd=Update::updateDesc($p['id_product'],$desc);
                }
            }
            if ($is && !$fval)
            {
                $value = FeatureValue::getFeatureValueLang($is);
                echo "<b style='color:red'>" ;
                echo "$id $title ";
                print_r($value[0]['value']);
                echo "</b><br>";
            }
            if (!$is && !$fval)
             {
                echo "<br> ".$p['id_product']." ".$p['name']." ";
                print_r($f);

                echo $desc;

            }
        }
        echo "<pre>";
        sort($this->fValue);
        print_r(($this->fValue));
        //print_r($ret);
    }
    static function suffix($feature_name) {
        $suffix = array('Moc RMS'=>'W',
                        'Moc Max'=>'W',
                        'Impedancja'=>'Ohm'
                    );
        return $suffix[$feature_name];
    }
    static function getFeatureName($id_feature)
    {
        $ret = Db::getInstance()->executeS('
            SELECT *
            FROM `'._DB_PREFIX_.'feature_lang`
            WHERE `id_feature` = '.(int)$id_feature.' AND `id_lang`=6');

        return $ret[0];
    }

    function _rms($desc) {
        preg_match("|power handling capacity([ 0-9]+)W|i",$desc,$out);

        if (!$out[1])     preg_match("|moc RMS[: ]*([ 0-9]+)W|i",$desc,$out);
        if (!$out[1])     preg_match("|moc\s*(\d+)\s*W|i",$desc,$out);
        if (!$out[1])     preg_match("|moc\s*(\d+)|i",$desc,$out);

         if (!$out[1]) preg_match("|Moc sinusoidalna:([ 0-9]+)W|",$desc,$out);
         if (!$out[1]) preg_match("|moc nominalna:([ 0-9]+)W|i",$desc,$out);

          if (!$out[1]) preg_match("|Moc RMS/Max:([ 0-9]+)/|",$desc,$out);
          if (!$out[1]) preg_match("|moc RMS\s*/\s*Max.:\s*(\d+)/|i",$desc,$out);

        if (!$out[1]) {
            //preg_match("|moc RMS:(\d+)W|i",$desc,$out);
            //preg_match("|RMS:.*(\d{3})|i",$desc,$out);
             preg_match("|RMS:\D*(\d+) W|i",$desc,$out);
        }
        if (!$out[1])  preg_match("|([0-9]+)W RMS|i",$desc,$out);
        if (!$out[1])  preg_match("|RMS:\s*(\d+)|i",$desc,$out);
        if (!$out[1])   preg_match("|RMS:.*\s*(\d{3})\s*W|i",$desc,$out);
        if (!$out[1]) {
            preg_match("|RMS:.*(\d</span>\d+)|i",$desc,$out);

        }
        $out[1]=trim($out[1]);
        $this->listFeatureValue($out[1]);
        return $out;
    }
  function _max($desc) {
        preg_match("|Peak power([ 0-9]+)W|",$desc,$out);

        if (!$out[1]) {
            preg_match("|Moc muzyczna:([ 0-9]+)W|",$desc,$out);
        }
        if (!$out[1]) {
            preg_match("|Moc RMS/Max:[ 0-9]+/([0-9]+)|",$desc,$out);
        }
           if (!$out[1]) {
            //preg_match("|moc Max[.: ]+( [0-9]+)W|i",$desc,$out);
        }
           if (!$out[1]) { preg_match("|(\d+) W max|i",$desc,$out);     }
           if (!$out[1]) { preg_match("|Max: *([0-9]+) W|i",$desc,$out);     }
           if (!$out[1]) { preg_match("|Max ([0-9]+) W|i",$desc,$out);     }
           if (!$out[1]) { preg_match("|max.?:\s*(\d+)\s*W|i",$desc,$out);     }
           if (!$out[1]) {
            preg_match("|Moc maksymalna:?([ 0-9]+)W|i",$desc,$out);
        }

        $out[1]=trim($out[1]);
        $this->listFeatureValue($out[1]);
        return $out;
  }

  function _impedance($desc) {
       preg_match("|Impedance ([0-9]+) ohm|",$desc,$out);

          if (!$out[1])  preg_match("|Impedancja:* ([x\,\+0-9]+) [ohmy]|i",$desc,$out);
          if (!$out[1])  preg_match("|impedancji\s*([x\,\+0-9]+) [ohmy]|i",$desc,$out);
          if (!$out[1])  preg_match("|Impedancja:?\s*(\d+)|i",$desc,$out);
          if (!$out[1])  preg_match("|([0-9]+x[0-9]+).*ohm|i",$desc,$out);

       $out[1]=preg_replace('/,/','.',$out[1]);
       $out[1]=preg_replace('/\+/','x',$out[1]);

       if ($this->category == 29 || $this->category == 41) {
            if (strlen($out[1]) == 1)
                $out[1]="1x".$out[1];
       }

       $out[1]=trim($out[1]);
       $this->listFeatureValue($out[1]);
       return $out;
  }
  function listFeatureValue($val) {

      $key = array_search($val,$this->fValue);
      if ($key === false)  {
        $this->fValue[] = "$val";
      }
      sort($this->fValue);
  }

  function addFeature($id_product,$featuresVal) {

      $seperator = ',';
      $product = new Product($id_product);
      $product->features = $featuresVal;

      if (isset($product->id_category))
                    $product->updateCategories(array_map('intval', $product->id_category));

                // Features import
                $features = get_object_vars($product);

                if (isset($features['features']) && !empty($features['features']))
                    foreach (explode($seperator, $features['features']) as $single_feature)
                    {
                        $tab_feature = explode(':', $single_feature);
                        $feature_name = trim($tab_feature[0]);
                        $feature_value = trim($tab_feature[1]);
                        $position = isset($tab_feature[2]) ? $tab_feature[1]: false;
                        $id_feature = Feature::addFeatureImport($feature_name, $position);
                        //$id_feature_value = FeatureValue::getFeatureValueLang()
                        $id_feature_value = FeatureValue::addFeatureValueImport($id_feature, $feature_value);
                        Product::addFeatureProductImport($product->id, $id_feature, $id_feature_value);
                    }
                // clean feature positions to avoid conflict
                Feature::cleanPositions();

  }
  function getFeature($id_product, $feature)
    {
        $name = explode(':',$feature);
        $name = $name[0];
        $fvalue = (explode(':',$feature));
        $fvalue = trim($fvalue[1]);

        $rq = Db::getInstance()->getRow('
            SELECT `id_feature`
            FROM '._DB_PREFIX_.'feature_lang
            WHERE `name` = \''.pSQL($name).'\'
            GROUP BY `id_feature`
        ');
        if (!empty($rq))
            $id_feature = (int)$rq['id_feature'];

       $ret =  Db::getInstance()->getRow('
            SELECT * FROM `ps_feature_product` WHERE `id_product`='.(int)$id_product.' and `id_feature`='.(int)$id_feature
        );

       $rval = Db::getInstance()->executeS('
            SELECT fv.`id_feature_value`
            FROM '._DB_PREFIX_.'feature_value fv
            LEFT JOIN '._DB_PREFIX_.'feature_value_lang fvl
                ON (fvl.`id_feature_value` = fv.`id_feature_value`)
            WHERE `value` = \''.pSQL($fvalue).'\'
                AND fv.`id_feature` = '.(int)$id_feature.'
                AND fv.`custom` = 0
            GROUP BY fv.`id_feature_value` LIMIT 1
        ');


        if ($ret['id_feature_value'])
            return $ret['id_feature_value'];
        else
            return false;
    }
}

$f=new FeatureRMS();

//$f->searchFeauture();
$f->addShortDesc();

?>
