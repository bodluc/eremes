<?php
require ('phpQuery/phpQuery.php');

class Add
    {
    public static function loadHttp($http)
        {
        do
            {
            $html=file_get_contents($http);
            } while ($http_response_header[0] != "HTTP/1.1 200 OK" && $i++ < 3);

        return phpQuery::newDocument($html);
        }

    static function phpQuery($url)
        {
        $options=array('http' => array
            (
            'method' => "GET",
            'header' => "Accept-language: en",
            "User-Agent" =>"Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.22 (KHTML, like Gecko) Chrome/25.0.1364.97 Safari/537.22",
            "Accept-Encoding" =>"gzip",
            "Accept"=> "text/html,application/xhtml+xml,application/xml;q=0.9",
            "Accept-Charset"=>"ISO-8859-1,UTF-8;q=0.7,*;q=0.7"
            ));

        $context=stream_context_create($options);
        do
            {
            $html   =file_get_contents($url, false, $context);
            }
            while (!strpos($http_response_header[0],'OK') && $i++ < 3);

        return phpQuery::newDocument($html);

        }
    }
?>