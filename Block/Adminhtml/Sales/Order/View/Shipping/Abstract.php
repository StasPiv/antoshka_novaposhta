<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 15.04.15
 * Time: 14:47
 */

abstract class OpsWay_NovayaPochta_Block_Adminhtml_Sales_Order_View_Shipping_Abstract
{
    /** @var  Mage_Sales_Model_Order */
    private $order;

    /**
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param Mage_Sales_Model_Order $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }

    public function getSeatWidth($number)
    {
        return '';
    }

    public function getSeatLength($number)
    {
        return '';
    }

    public function getSeatHeight($number)
    {
        return '';
    }

    public function getSeatWeight($number)
    {
        return '';
    }

    abstract public function getCurrentSeatsNumber();
    abstract public function getShipmentDate();
    abstract public function getCostOnSite();
    abstract public function initSeatVolumetricWeightField($form, $number);
    abstract public function getAfterpaymentOnGoodsCost();
    abstract public function getRecipientAddressRef();
    abstract public function getSenderAddressRef();
    abstract public function getContactSenderRef();
    abstract public function getRecipientCity();
    abstract public function getSenderCityRef();
}