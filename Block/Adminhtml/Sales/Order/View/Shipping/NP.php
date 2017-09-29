<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 15.04.15
 * Time: 14:48
 */

class OpsWay_NovayaPochta_Block_Adminhtml_Sales_Order_View_Shipping_NP extends OpsWay_NovayaPochta_Block_Adminhtml_Sales_Order_View_Shipping_Abstract
{
    private $internetDocumet = array();

    /**
     * @return array
     */
    public function getInternetDocumet()
    {
        return $this->internetDocumet;
    }

    /**
     * @param array $internetDocumet
     */
    public function setInternetDocumet($internetDocumet)
    {
        $this->internetDocumet = $internetDocumet;
    }

    public function setOrder($order)
    {
        parent::setOrder($order);
        $this->setInternetDocumet(Mage::helper('opsway_novayapochta/api')->getInternetDocumentByNumber(
            $order->getShippingOperatorDocumentNumber()
        ));
    }

    public function getCurrentSeatsNumber()
    {
        return $this->internetDocumet['SeatsAmount'];
    }

    public function getShipmentDate()
    {
        return date('Y-m-d', strtotime($this->internetDocumet['DateTime']));
    }

    public function getCostOnSite()
    {
        return $this->internetDocumet['CostOnSite'];
    }

    /**
     * @param Varien_Data_Form $form
     * @param $number
     */
    public function initSeatVolumetricWeightField($form, $number)
    {
        $form->addField(
            "seat_vol_weight$number",
            'text',
            array(
                'name' => "seat_vol_weight[$number]",
                'label' => Mage::helper('opsway_novayapochta')->__('SeatVolumetricWeight'),
                'value' => $this->internetDocumet['OptionsSeat'][$number - 1]['volumetricWeight']
            )
        );
    }

    public function getAfterpaymentOnGoodsCost()
    {
        return $this->internetDocumet['AfterpaymentOnGoodsCost'];
    }

    public function getRecipientAddressRef()
    {
        return $this->internetDocumet['RecipientAddressRef'];
    }

    public function getSenderAddressRef()
    {
        return $this->internetDocumet['SenderAddressRef'];
    }

    public function getContactSenderRef()
    {
        return $this->internetDocumet['ContactSenderRef'];
    }

    public function getRecipientCity()
    {
        return $this->internetDocumet['CityRecipient'];
    }

    public function getSenderCityRef()
    {
        return $this->internetDocumet['CitySenderRef'];
    }
}