<?php
require('config/config.inc.php');
require('phpQuery/phpQuery.php');

class update
    {
    function updateDesc64()
        {
            
        $file=file(_PS_ROOT_DIR_ . "/admin-dev/import/p12.csv");

        foreach ($file as $line)
            {
            $arg     = split(';', $line);
            $name    =trim($arg[2], '"');
            $ref     =$arg[1];
            $products=Db::getInstance()->executeS("SELECT * FROM `ps_product_lang` WHERE `name`='$name'");

            if (!$products)
                continue;

            foreach ($products as $p)
                {
                $desc_short = base64_decode($arg[3]);
                $desc       =base64_decode($arg[4]);
                $id         =$p['id_product'];
                $sql        =sprintf("UPDATE `ps_product_lang` SET `description_short`='%s' WHERE `id_product`=%d",
                                     mysql_real_escape_string($desc_short),
                                     $id);
                $dd         =Db::getInstance()->executeS($sql);
                $sql        =sprintf("UPDATE `ps_product_lang` SET `description`='%s' WHERE `id_product`=%d",
                                     mysql_real_escape_string($desc),
                                     $id);
                $dd         =Db::getInstance()->executeS($sql);
                }

            echo $name . "<br>$desc_short<br>";
            }

        echo "END";
        }
    function updateDesc($id,$desc) {
           
              $sql        =sprintf("UPDATE `ps_product_lang` SET `description`='%s' WHERE `id_product`=%d", mysql_real_escape_string($desc),$id);
               return $ret         = Db::getInstance()->executeS($sql);
               
    }
    function rmDescHtml($desc) {
         
        $m = preg_match('/<li>/',$desc,$out);
        $tr = preg_match('/<tr>/',$desc,$out);        
            if (!$m && !$tr) return $desc;
            if ($m) {
                    $pq = phpQuery::newDocument($desc);
                foreach (pq('li') as $li) {
                    $l=pq($li)->text();
                    $l=preg_replace('/\n/','',$l);
                    pq($li)->html(trim($l));
                }
                $d = pq($pq)->html();
                return $d;
            }
        
         if ($tr) 
            {
                    $pq = phpQuery::newDocument($desc);
                foreach (pq('tr') as $li) {
                    $l=pq($li)->text();
                    $l=preg_replace('/\n/','',$l);
                    pq($li)->html(trim($l));
                }
                $d = pq($pq)->html();
                return $d;
            }
            
    }
    function searchFeauture() {
        
        $ret=Db::getInstance()->executeS("SELECT * FROM `ps_product_lang` ");
       // print_r($ret);
       //mysql_real_escape_string('fdsafdsaf');
        
        $p = new Product();
        $products = Product::getProducts(6,0,999,'id_product','asc',29);
        
        foreach ($products as $p) {
            $desc = $p['description'];

            $desc = $this->rmDescHtml($desc);
            //$rd=$this->updateDesc($p['id_product'],$desc);
            
            $f['rms']=$this->_rms($desc);
            //$f['max']=$this->_max($desc);
            //$f['ohm']=$this->_impedance($desc);
            
            $fval = $f['rms'][1];
            $features = sprintf("Moc RMS:%s",$fval);
            $is = $this->getFeature($p['id_product'],$features);            
            
            if ($is && $fval) 
            {  
                echo $p['id_product']." <b style='color:green'>" ;
                print_r($f);
                echo "</b><br>";
            }
            if (!$is && $fval)  
            {
                echo "<b style='color:blue'>" ;
                print_r($f);
                echo "</b><br>";
              //  $this->addFeature($p['id_product'],$features);
            }
            if ($is && !$fval)
            {
                echo "<b style='color:red'>" ;
                print_r($f);
                echo "</b><br>";
            }
            if (!$is && !$fval)
             {
                echo "<br>".$p['id_product']." ".$p['name'];
                print_r($f);
                
                echo $desc;
                
            }
            
        
        }
        
        //print_r($ret);
    }
    function _rms($desc) {
        preg_match("|Max power handling capacity([ 0-9]+)W|",$desc,$out);
        
        if (!$out[1])     preg_match("|moc RMS[: ]*([ 0-9]+)W|i",$desc,$out);
        if (!$out[1])     preg_match("|moc\s*(\d+)\s*W|i",$desc,$out);
        if (!$out[1])     preg_match("|moc\s*(\d+)|i",$desc,$out);
        
         if (!$out[1]) {
            preg_match("|Moc sinusoidalna:([ 0-9]+)W|",$desc,$out);
        }
          if (!$out[1]) {
            preg_match("|Moc RMS/Max:([ 0-9]+)/|",$desc,$out);
        }
        if (!$out[1]) {
            //preg_match("|moc RMS:(\d+)W|i",$desc,$out);
            //preg_match("|RMS:.*(\d{3})|i",$desc,$out);
             preg_match("|RMS:\s*.*(\d{3})|i",$desc,$out);
        }
        if (!$out[1])  preg_match("|([0-9]+)W RMS|i",$desc,$out);
        if (!$out[1])   preg_match("|RMS:.*\s*.*(\d{3})\s*W|i",$desc,$out);
        if (!$out[1]) {  
            preg_match("|RMS:.*(\d</span>\d+)|i",$desc,$out);
            
        }
        
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
           if (!$out[1]) {
            preg_match("|Max: *([0-9]+) W|i",$desc,$out);
        }
           if (!$out[1]) {
            preg_match("|Moc maksymalna:([ 0-9]+)W|",$desc,$out);
        }
        return $out;
  } 
  function _impedance($desc) {
       preg_match("|Impedance ([0-9]+) ohm|",$desc,$out);
       
          if (!$out[1])  preg_match("|Impedancja:* ([x\,\+0-9]+) [ohmy]|i",$desc,$out);
          if (!$out[1])  preg_match("|([0-9]+x[0-9]+).*ohm|i",$desc,$out);
       
       $out[1]=preg_replace('/,/','.',$out[1]);
       return $out;
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
            return true;
        else 
            return false;
    }
  
    }

$info = new update();
$info->searchFeauture();

$info->addFeature(3,'Moc RMS:500,Moc Maxymalna:250');

?>