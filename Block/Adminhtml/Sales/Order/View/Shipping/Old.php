<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 20.04.15
 * Time: 13:32
 */

class OpsWay_NovayaPochta_Block_Adminhtml_Sales_Order_View_Shipping_Old extends OpsWay_NovayaPochta_Block_Adminhtml_Sales_Order_View_Shipping_New
{
    private $cargoData;

    public function setOrder($order)
    {
        parent::setOrder($order);
        $this->cargoData = json_decode($order->getData('cargo_data'),true);
    }

    public function getCurrentSeatsNumber()
    {
        return isset($this->cargoData['seats']) ? $this->cargoData['seats'] : 1;
    }

    public function getShipmentDate()
    {
        return isset($this->cargoData['shipment_date']) ? $this->cargoData['shipment_date'] : null;
    }

    public function getCostOnSite()
    {
        return isset($this->cargoData['operator_amount']) ? $this->cargoData['operator_amount'] : null;
    }

    public function getSeatWidth($number)
    {
        return isset($this->cargoData['seat_width'][$number]) ? $this->cargoData['seat_width'][$number] : null;
    }

    public function getSeatLength($number)
    {
        return isset($this->cargoData['seat_length'][$number]) ? $this->cargoData['seat_length'][$number] : null;
    }

    public function getSeatHeight($number)
    {
        return isset($this->cargoData['seat_height'][$number]) ? $this->cargoData['seat_height'][$number] : null;
    }

    public function getSeatWeight($number)
    {
        return isset($this->cargoData['seat_weight'][$number]) ? $this->cargoData['seat_weight'][$number] : null;
    }

    public function initSeatVolumetricWeightField($form, $number)
    {
        return false;
    }

    public function getAfterpaymentOnGoodsCost()
    {
        return isset($this->cargoData['afterpayment']) ? $this->cargoData['afterpayment'] : null;
    }

    public function getRecipientAddressRef()
    {
        return isset($this->cargoData['recipient_address']) ? $this->cargoData['recipient_address'] : null;
    }

    public function getSenderAddressRef()
    {
        return isset($this->cargoData['sender_address']) ? $this->cargoData['sender_address'] : null;
    }

    public function getContactSenderRef()
    {
        return isset($this->cargoData['sender_contacts']) ? $this->cargoData['sender_contacts'] : null;
    }

    public function getSenderCityRef()
    {
        return isset($this->cargoData['sender_city']) ? $this->cargoData['sender_city'] : null;
    }

}