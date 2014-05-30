<?php

class AdminImportController extends AdminImportControllerCore
{
    public static function getMaskedRow($row) {
        $res = parent::getMaskedRow($row);
        $res['description'] = trim(base64_decode($res['description']));
        
        return $res;
    }
}

