<?php

require_once('OpsWay/NovaPoshta/fake_autoload.php');

use OpsWay\NovaPoshta\Cache\ICache;

class OpsWay_NovayaPochta_Helper_Cacher extends Mage_Core_Helper_Abstract implements ICache
{
    public static function saveRequest($request, $response)
    {
        if (self::getResponseByRequest($request)) {
            return;
        }

        $record = Mage::getModel('opsway_novayapochta/request');
        $record->setRequest($request)
               ->setResponse($response)
               ->save();

        return;
    }

    public static function getResponseByRequest($request)
    {
        $savedRecord = Mage::getModel('opsway_novayapochta/request')->load($request, 'request');
        $cacheTime =
            Mage::getSingleton('core/date')->gmtTimestamp() -
            Mage::getSingleton('core/date')->gmtTimestamp($savedRecord->getUpdatedAt());

        if (!$savedRecord->getId() || $cacheTime > self::cacheTimeLifeInSeconds()) {
            return false;
        }

        return $savedRecord->getResponse();
    }

    /**
     * @return int
     */
    private static function cacheTimeLifeInSeconds()
    {
        return 3600 * 24;
    }

}