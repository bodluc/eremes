<?php

class Dispatcher extends DispatcherCore
{
    public function removePlChar($str)
    {
    $polishChars = array('ą', 'ć', 'ę', 'ł', 'ń', 'ó', 'ś', 'ż', 'ź');
    $replace     = array('a', 'c', 'e', 'l', 'n', 'o', 's', 'z', 'z');
    return str_replace($polishChars, $replace, $str);
    }
    
    public function getProductId($rewrite,$lang) {
        $ret = db::getInstance()->executeS("SELECT `id_product`,`name` FROM `ps_product_lang` WHERE `id_lang`=$lang AND `link_rewrite` LIKE '$rewrite'");
        return $ret[0]['id_product'];
    }
    public function getCategoryId($category ,$lang) {
        
        $category = trim($category,'/');
        $category=array_reverse(explode('/',$category)); 
        
        $ret = db::getInstance()->executeS("SELECT * FROM `ps_category_lang` WHERE `id_lang`=$lang AND `link_rewrite` like '$category[0]'");
        
        return $ret[0]['id_category'];
    }
     public function getManufactureId($rewrite,$lang) {
         $rewrite = str_replace(array('-',' '),'.',$rewrite);
        $ret = db::getInstance()->executeS("SELECT `id_manufacturer` FROM `ps_manufacturer` WHERE name regexp '$rewrite'");
        return $ret[0]['id_manufacturer'];
    } 
}

