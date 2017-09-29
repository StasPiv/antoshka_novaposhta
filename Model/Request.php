<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 02.07.15
 * Time: 23:15
 */

/**
 * Class OpsWay_NovayaPochta_Model_Request
 *
 * @method OpsWay_NovayaPochta_Model_Request setResponse(string $response)
 * @method OpsWay_NovayaPochta_Model_Request setRequest(string $request)
 * @method string getResponse()
 * @method string getRequest()
 * @method string getUpdatedAt()
 */
class OpsWay_NovayaPochta_Model_Request extends Mage_Core_Model_Abstract
{

    protected function _construct()
    {
        $this->_init('opsway_novayapochta/request');
    }

}