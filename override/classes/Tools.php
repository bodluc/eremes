<?php
/**
 * This file from exfeatures module. Here used tab mapper for calculate token
 * 
 * @category modules
 * @package 'exfeatures'
 */ 
class Tools extends ToolsCore
{
	/**
     * @see parent::getAdminTokenLite()
     */ 
    public static function getAdminTokenLite($tab)
	{
		global $cookie;
		
        
        $ret=ToolsCore::getAdminToken(Tab::exfeaturesMap($tab).(int)(Tab::getIdFromClassName($tab)).(int)($cookie->id_employee));
        
        return $ret;
	}
}