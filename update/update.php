<?php
require('../config/config.inc.php');
//require('phpQuery/phpQuery.php');

class UpdateRMS
    {

    public function __construct() {
        //require('../config/config.inc.php');
        $host = _DB_SERVER_;
        $link = mysql_connect($host, _DB_USER_, _DB_PASSWD_) OR die(mysql_error());
    }
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
    static function updateDesc($id,$desc) {

              $sql        =sprintf("UPDATE `ps_product_lang` SET `description`='%s' WHERE `id_product`=%d", mysql_real_escape_string($desc),$id);
               return $ret         = Db::getInstance()->executeS($sql);

    }
     static function updateShortDesc($id,$desc) {

              $sql        =sprintf("UPDATE `ps_product_lang` SET `description_short`='%s' WHERE `id_product`=%d", mysql_real_escape_string($desc),$id);
               return $ret         = Db::getInstance()->executeS($sql);

    }
    static function rmDescHtml($desc) {

        $desc = preg_replace('/&nbsp;/','',$desc);
        $desc = preg_replace('/\s+/',' ',$desc);

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

            }

         if ($tr)
            {
                if (!$d) $d=$desc;

                    $pq = phpQuery::newDocument($d);
                foreach (pq('tr') as $li) {
                    $l=pq($li)->text();
                    $l=preg_replace('/\n/','',$l);
                    pq($li)->html(trim($l));
                }
                $d = pq($pq)->html();
            }

         return $d;
    }


    }


?>