<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 15.04.15
 * Time: 14:47
 */

class OpsWay_NovayaPochta_Block_Adminhtml_Sales_Order_View_Shipping_New extends OpsWay_NovayaPochta_Block_Adminhtml_Sales_Order_View_Shipping_Abstract
{
    private $senderCityRef;
    /** @var  Mage_Sales_Model_Order_Address */
    private $recipientAddress;

    /**
     * @return Mage_Sales_Model_Order_Address
     */
    public function getRecipientAddress()
    {
        return $this->recipientAddress;
    }

    /**
     * @param Mage_Sales_Model_Order_Address $recipientAddress
     */
    public function setRecipientAddress($recipientAddress)
    {
        $this->recipientAddress = $recipientAddress;
    }

    public function getCurrentSeatsNumber()
    {
        return 1;
    }

    public function getShipmentDate()
    {
        return '';
    }

    public function getCostOnSite()
    {
        return '';
    }

    public function initSeatVolumetricWeightField($form, $number)
    {
        return false;
    }

    public function getAfterpaymentOnGoodsCost()
    {
        return $this->getOrder()->getData('base_grand_total');
    }

    public function getRecipientAddressRef()
    {
        if (!isset($this->recipientAddress)) {
            $this->initRecipientAddress();
        }

        $streets = $this->getRecipientAddress()->getStreet();
        $street = $streets[0];

        $warehouseCodeFromString = Mage::helper('opsway_novayapochta')->getWarehouseCodeFromString($this->getRecipientAddress()->getCity(),
            $street);

        return $warehouseCodeFromString['Ref'];
    }

    private function initRecipientAddress()
    {
        $shippingAddressId = $this->getOrder()->getData('shipping_address_id');

        if (!$shippingAddressId) {
            return false;
        }

        $address = Mage::getModel('sales/order_address')->load($shippingAddressId);

        if (!$address->getId()) {
            $address = Mage::getModel('sales/order_address');
        }

        $this->setRecipientAddress($address);
    }

    public function getSenderAddressRef()
    {
        return $this->getRecipientAddressRef();
    }

    public function getContactSenderRef()
    {
        return '';
    }

    public function getRecipientCity()
    {
        if (!isset($this->recipientAddress)) {
            $this->initRecipientAddress();
        }

        return Mage::helper('opsway_novayapochta/api')->getAddress()->getFirstCity(
            $this->getRecipientAddress()->getCity()
        );
    }

    public function getSenderCityRef()
    {
        if (isset($this->senderCityRef)) {
            return $this->senderCityRef;
        }

        $city = Mage::helper('opsway_novayapochta/api')->getAddress()->getFirstCity($this->getOrder()->getShippingAddress()->getCity());
        $availableSenderCities = Mage::helper('opsway_novayapochta/api')->getAvailableSenderCities();

        if (isset($availableSenderCities[$city['Ref']])) {
            return $this->senderCityRef = $city['Ref'];
        } else {
            $city = Mage::helper('opsway_novayapochta/api')->getAddress()->getFirstCity('Киев');
            return $this->senderCityRef = $city['Ref'];
        }
    }
}