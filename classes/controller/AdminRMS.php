<?php
  class RMS
  {
      public static function getPriceHurtNetto(&$list) {

      if (isset($list[0]['id_product']))
          foreach ($list as &$p) {
              $p['hurt_brutto']=round($p['wholesale_price']*1.23);
              @$procent = round(1-($p['hurt_brutto']/$p['price_final']),2)*100;
              $marza =  round($p['price_final']-$p['hurt_brutto'],2);
              $p['price_marza'] = "($procent%) $marza zł" ;
              $p['price_wars'] = "-";

          }
      }
  }
?>
