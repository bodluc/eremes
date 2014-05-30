<?php
require (dirname(__FILE__) . '/config/config.inc.php');

class Update
    {
    static public function updateDescription()
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
        }
    static public function updateLinkRewrite() {
        
        global $link;
               
        $ret = db::getInstance()->executeS("SELECT * FROM `ps_product_lang`  WHERE `id_lang`=6 ");
        foreach ($ret as $p) {
            $sql        =sprintf("UPDATE `ps_product_lang` SET `link_rewrite`='%s' WHERE `id_product`=%d",
                                     $link[] = self::createLinkRewrite($p['name']),
                                     $p['id_product']);
           $dd          =Db::getInstance()->executeS($sql);                                                               
        }
        natsort($link);
        
        echo "<pre>";
        print_r($link);
        echo "</pre>";
        
        $ret = db::getInstance()->executeS("SELECT * FROM `ps_category_lang`  WHERE `id_lang`=6");
        
        $link = array();
        foreach ($ret as $c) {
            $sql        =sprintf("UPDATE `ps_category_lang` SET `link_rewrite`='%s' WHERE `id_category`=%d",
                                     $link[] = self::createLinkRewrite($c['link_rewrite']),
                                     $c['id_category']);
           $dd          =Db::getInstance()->executeS($sql);                                                               
        }
        natsort($link);
        
        echo "<pre>";
        print_r($link);
        echo "</pre>";
    }
    
    static  private function createLinkRewrite($str)
    {
        $polishChars = array('ą', 'ć', 'ę', 'ł', 'ń', 'ó', 'ś', 'ż', 'ź',' ','/','(',')');
        $replace     = array('a', 'c', 'e', 'l', 'n', 'o', 's', 'z', 'z','-','','','');
        
        $ret = str_replace($polishChars, $replace, trim($str));
        return $ret;
    }
    
    }

    Update::updateLinkRewrite();
    
echo "END";

//phpinfo();

?>