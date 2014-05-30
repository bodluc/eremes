<?php
$search=$_GET['name'];

$search =   preg_replace('/ +/','+',$search);
$json['skapiec']=file("http://notanio.pl/search?productName=$search&minPrice=&serviceClient=skapiec");
$json['nokaut']=file("http://notanio.pl/search?productName=$search&minPrice=&serviceClient=nokaut");
$json['ceneo']=file("http://notanio.pl/search?productName=$search&minPrice=&serviceClient=ceneo");
$json['okazje']=file("http://notanio.pl/search?productName=$search&minPrice=&serviceClient=okazje");
//$json['bazarcen']=file("http://notanio.pl/search?productName=$search&minPrice=&serviceClient=bazarcen");
$json['allegro']=file("http://notanio.pl/search?productName=$search&minPrice=&serviceClient=allegro");

foreach ($json as $key=>$val)    {
    $tab[$key] =json_decode($val[0], true);
    $cena[$key]=$tab[$key]['products'][0]['prices'][0]['amount'] ? $tab[$key]['products'][0]['prices'][0]['amount'].' PLN' : '';
    $name[$key]=$tab[$key]['products'][0]['name'] ? $tab[$key]['products'][0]['name'] : '' ;
}


echo "{";
echo '"name":', json_encode($name), ",\n";
echo '"cena":', json_encode($cena), "\n";
echo "}";


echo null;
?>