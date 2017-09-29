<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 02.07.15
 * Time: 23:15
 */ 
class OpsWay_NovayaPochta_Model_Mysql4_Request extends Mage_Core_Model_Resource_Db_Abstract
{

    protected function _construct()
    {
        $this->_init('opsway_novayapochta/requests', 'request_id');
    }

}