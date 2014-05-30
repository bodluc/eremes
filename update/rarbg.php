<?php
require('add.php');

  class Rarbg extends Add {

      public $link_rarbg  = "http://rarbg.com/torrents.php?category=14;15;16;17;21;22;42";
      public $movies;
      public $rank;

      function bestIMDB() {

          for ($i=1;$i<5;$i++) {
            self::_getMovies($this->link_rarbg."&page=$i");
            sleep(10);
          }
      }
      private function _getMovies($url) {

          self::phpQuery($url);
          self::loadHttp($url);

          foreach (pq('.lista2') as $movie) {
                $imdb = pq('td:eq(1) span[style]',$movie)->text();
                preg_match("/:(.*)\//",$imdb,$match);
                echo $i['title'] = trim(pq('td:eq(1) a',$movie)->text());
                $i['imdb'] = floatval($match[1]);
                $i['href'] = "http://rarbg.com".pq('td:eq(1) a',$movie)->attr('href');

                $this->rank[] = $imdb;
                $this->movies[] = $i;
          }

          return;
      }
  }

$m = new Rarbg();
$m->bestIMDB();

?>
