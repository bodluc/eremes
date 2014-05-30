<?php
require(dirname(__FILE__).'/config/config.inc.php');


$file=file(_PS_ROOT_DIR_."/admin-dev/import/p12.csv");

foreach ($file as $line) {
    $arg = split(';',$line);
    $name = trim($arg[2],'"');
    $ref = $arg[1];
    $products = Db::getInstance()->executeS("SELECT * FROM `ps_product_lang` WHERE `name`='$name'");
    if (!$products)
        continue;
    foreach ($products as $p) {
        $desc_short=  base64_decode($arg[3]);
        $desc=  base64_decode($arg[4]);
        $id=$p['id_product'];
        $sql=sprintf("UPDATE `ps_product_lang` SET `description_short`='%s' WHERE `id_product`=%d",  mysql_real_escape_string($desc_short),$id);
        $dd = Db::getInstance()->executeS($sql);
        $sql=sprintf("UPDATE `ps_product_lang` SET `description`='%s' WHERE `id_product`=%d",  mysql_real_escape_string($desc),$id);
        $dd = Db::getInstance()->executeS($sql);
    }
    echo $name."<br>$desc_short<br>";
    
}

echo "END";
 
//phpinfo();

 
?>
