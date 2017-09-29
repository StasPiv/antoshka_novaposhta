<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 12/14/14
 * Time: 3:43 PM
 */

use OpsWay\NovaPoshta\Counterparty\Filter\Sender as SenderFilter;

/**
 * Class OpsWay_NovayaPochta_Block_Sales_Order_View_Shipping
 * @method setOrder(Varien_Object $order)
 * @method Smile_Sales_Model_Order getOrder()
 * @method string getWareHouseForRecipient()
 * @method setWareHouseForRecipient(string $code)
 * @method Mage_Sales_Model_Order_Address getAddress()
 * @method setAddress(Mage_Sales_Model_Order_Address $address)
 * @method array getCity()
 * @method setCity(array $city)
 */
class OpsWay_NovayaPochta_Block_Adminhtml_Sales_Order_View_Shipping extends Mage_Adminhtml_Block_Widget_Form
{
    private $maxSeats = 10;
    private $novaPoshtaError = false;
    private $internetDocument = false;
    /** @var  OpsWay_NovayaPochta_Block_Adminhtml_Sales_Order_View_Shipping_Abstract */
    private $shippingStrategy;

    /**
     * @return OpsWay_NovayaPochta_Block_Adminhtml_Sales_Order_View_Shipping_Abstract
     */
    public function getShippingStrategy()
    {
        return $this->shippingStrategy;
    }

    /**
     * @param OpsWay_NovayaPochta_Block_Adminhtml_Sales_Order_View_Shipping_Abstract $shippingStrategy
     */
    public function setShippingStrategy($shippingStrategy)
    {
        $shippingStrategy->setOrder($this->getOrder());
        $this->shippingStrategy = $shippingStrategy;
    }

    /**
     * @param array $args
     */
    public function __construct($args)
    {
        parent::__construct();
        try {
            $this->prepare($args);
            $this->initForm();
        } catch (Exception $e) {
            $this->novaPoshtaError = 'Nova poshta: ' . $e->getMessage();
        }
    }

    protected function _toHtml()
    {
        if ($this->novaPoshtaError !== false) {
            return '<strong>' . $this->novaPoshtaError . '</strong>';
        }

        return parent::_toHtml();
    }


    public function getTemplate()
    {
        return 'sales/order/view/novayapochta.phtml';
    }

    public function getCurrentSeatsNumber()
    {
        return $this->getShippingStrategy()->getCurrentSeatsNumber();
    }

    public function getShipmentDate()
    {
        return $this->getShippingStrategy()->getShipmentDate();
    }

    public function getCostOnSite()
    {
        return $this->getShippingStrategy()->getCostOnSite();
    }

    private function initForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);

        $this->initSeatsField();
        $this->initVolumeGeneralField();
        $this->initVolumeWeightField();
        foreach (range(1, $this->getMaxSeats()) as $i) {
            $this->initSeatPlaceField($i);
        }
        $this->initRecipientAddressField();
        $this->initSenderCityField();
        $this->initSenderField();
        $this->initSenderAddressField();
        $this->initSenderContactsField();
        $this->initWeightField();
        $this->initAfterpaymentOnGoodsCostField();
        $this->initRequestButton();
    }

    public function helper($name = 'opsway_novayapochta')
    {
        return parent::helper($name);
    }

    protected function initSeatsField()
    {
        $values = array();

        foreach (range(1, $this->getMaxSeats()) as $i) {
            $values[] = array(
                'value' => $i,
                'label' => $i
            );
        }

        $this->getForm()->addField(
            'seats',
            'select',
            array(
                'name' => 'seats',
                'label' => $this->helper()->__('Seats'),
                'values' => $values,
                'value' => array($this->getCurrentSeatsNumber())
            )
        );
    }

    protected function initVolumeGeneralField()
    {
        $this->getForm()->addField(
            "volume_general",
            'text',
            array(
                'name' => "volume_general",
                'label' => $this->helper()->__('VolumeGeneral')
            )
        );
    }

    protected function initVolumeWeightField()
    {
        $this->getForm()->addField(
            "volume_weight",
            'text',
            array(
                'name' => "volume_weight",
                'label' => $this->helper()->__('VolumeWeight')
            )
        );
    }

    /**
     * @return int
     */
    public function getMaxSeats()
    {
        return $this->maxSeats;
    }

    private function initSeatPlaceField($number)
    {
        $this->initSeatWidthField($number);
        $this->initSeatHeightField($number);
        $this->initSeatLengthField($number);
        $this->initSeatWeightField($number);
        $this->initSeatVolumetricWeightField($number);
    }

    /**
     * @param $number
     */
    private function initSeatWidthField($number)
    {
        $this->getForm()->addField(
            "seat_width$number",
            'text',
            array(
                'name' => "seat_width[$number]",
                'label' => $this->helper()->__('SeatWidth'),
                'value' => $this->getShippingStrategy()->getSeatWidth($number)
            )
        );
    }

    /**
     * @param $number
     */
    private function initSeatHeightField($number)
    {
        $this->getForm()->addField(
            "seat_height$number",
            'text',
            array(
                'name' => "seat_height[$number]",
                'label' => $this->helper()->__('SeatHeight'),
                'value' => $this->getShippingStrategy()->getSeatHeight($number)
            )
        );
    }

    /**
     * @param $number
     */
    private function initSeatLengthField($number)
    {
        $this->getForm()->addField(
            "seat_length$number",
            'text',
            array(
                'name' => "seat_length[$number]",
                'label' => $this->helper()->__('SeatLength'),
                'value' => $this->getShippingStrategy()->getSeatLength($number)
            )
        );
    }

    /**
     * @param $number
     */
    private function initSeatWeightField($number)
    {
        $this->getForm()->addField(
            "seat_weight$number",
            'text',
            array(
                'name' => "seat_weight[$number]",
                'label' => $this->helper()->__('SeatWeight'),
                'value' => $this->getShippingStrategy()->getSeatWeight($number)
            )
        );
    }

    /**
     * @param $number
     */
    private function initSeatVolumetricWeightField($number)
    {
        $this->getShippingStrategy()->initSeatVolumetricWeightField($this->getForm(), $number);
    }

    protected function initWeightField()
    {
        $this->getForm()->addField(
            "weight",
            'text',
            array(
                'name' => "weight",
                'label' => $this->helper()->__('Weight')
            )
        );
    }

    private function initAfterpaymentOnGoodsCostField()
    {
        $this->getForm()->addField(
            "afterpayment",
            'text',
            array(
                'name' => "afterpayment",
                'label' => $this->helper()->__('AfterpaymentOnGoodsCost'),
                'value' => $this->getShippingStrategy()->getAfterpaymentOnGoodsCost()
            )
        );
    }

    private function initRecipientAddressField()
    {
        $recipientCity = $this->getShippingStrategy()->getRecipientCity();
        $this->getForm()->addField(
            'recipient',
            'select',
            array(
                'name' => 'recipient_address',
                'label' => $this->helper()->__('RecipientAddress'),
                'values' => array_map(
                    function ($wareHouse) {
                        return array(
                            'label' => $wareHouse['DescriptionRu'],
                            'value' => $wareHouse['Ref']
                        );
                    },
                    Mage::helper('opsway_novayapochta/api')->getAddress()->getWarehouses($recipientCity['Description'])
                ),
                'value' => array($this->getShippingStrategy()->getRecipientAddressRef())
            )
        );
    }

    private function initSenderCityField()
    {
        $citiesFromNpWhereAntoshkaExists = array();
        $allCitiesFromNp = Mage::helper('opsway_novayapochta/api')->getAddress()->getCities();
        $availableSenderCities = Mage::helper('opsway_novayapochta/api')->getAvailableSenderCities();

        foreach ($allCitiesFromNp['data'] as $cityFromNp) {
            if (!isset($availableSenderCities[$cityFromNp['Ref']])) {
                continue;
            }
            $citiesFromNpWhereAntoshkaExists[] = array(
                'label' => $cityFromNp['DescriptionRu'],
                'value' => $cityFromNp['Ref']
            );
        }

        $this->getForm()->addField(
            'sender_city',
            'select',
            array(
                'name' => 'sender_city',
                'label' => $this->helper()->__('SenderCity'),
                'values' => $citiesFromNpWhereAntoshkaExists,
                'value' => array($this->getShippingStrategy()->getSenderCityRef())
            )
        );
    }

    private function initSenderField()
    {
        $availableSendersForSelect = array();
        $senderFilter = new SenderFilter();
        $availableSenders = Mage::helper('opsway_novayapochta/api')->getCounterparty()->getCounterparties($senderFilter);

        $senderFilter->CityRef = $this->getShippingStrategy()->getSenderCityRef();
        $sendersForCity = Mage::helper('opsway_novayapochta/api')->getCounterparty()->getCounterparties($senderFilter);

        foreach ($availableSenders['data'] as $sender) {
            $availableSendersForSelect[] = array(
                'label' => $sender['Description'],
                'value' => $sender['Ref']
            );
        }

        $sendersForCityForSelect = array();
        foreach ($sendersForCity['data'] as $sender) {
            $sendersForCityForSelect[] = $sender['Ref'];
        }

        $this->getForm()->addField(
            'sender',
            'select',
            array(
                'name' => 'sender',
                'label' => $this->helper()->__('Sender'),
                'values' => $availableSendersForSelect,
                'value' => $sendersForCityForSelect
            )
        );
    }

    private function initSenderAddressField()
    {
        $availableSenderAddressesForSelect = array();

        $availableSenderAddresses = Mage::helper('opsway_novayapochta/api')->getCounterparty()->getSenderAddresses($this->getShippingStrategy()->getSenderCityRef());

        foreach ($availableSenderAddresses as $senderAddress) {
            $availableSenderAddressesForSelect[] = array(
                'label' => $senderAddress['Description'],
                'value' => $senderAddress['Ref']
            );
        }

        $this->getForm()->addField(
            'sender_address',
            'select',
            array(
                'name' => 'sender_address',
                'label' => $this->helper()->__('SenderAddress'),
                'values' => $availableSenderAddressesForSelect,
                'value' => array($this->getShippingStrategy()->getSenderAddressRef())
            )
        );
    }

    private function initSenderContactsField()
    {
        $values = array();

        $senderContacts = Mage::helper('opsway_novayapochta/api')->getCounterparty()->getSenderContacts($this->getShippingStrategy()->getSenderCityRef());

        foreach ($senderContacts as $contact) {
            $values[] = array(
                'label' => $contact['Description'],
                'value' => $contact['Ref']
            );
        }

        $this->getForm()->addField(
            'sender_contacts',
            'select',
            array(
                'name' => 'sender_contacts',
                'label' => $this->helper()->__('SenderContacts'),
                'values' => $values,
                'value' => array($this->getShippingStrategy()->getContactSenderRef())
            )
        );
    }

    private function initRequestButton()
    {
        $this->getForm()->addField(
            'request_to_np',
            'button',
            array(
                'label' => $this->helper()->__('RequestToNP'),
                'value' => $this->helper()->__('RequestToNP'),
                'type' => 'button',
                'class' => 'form-button'
            )
        );
    }

    /**
     * @param $args
     */
    private function prepare($args)
    {
        if (!isset($args['order']) || !$args['order'] instanceof Varien_Object) {
            return false;
        }
        $this->setOrder($args['order']);

        if (!$this->setAddressFromOrder()) {
            return false;
        }

        $this->initInternetDocumentIfExists();

        $this->setCity($this->getShippingStrategy()->getRecipientCity());
        $this->setWareHouseForRecipient($this->getShippingStrategy()->getRecipientAddressRef());


    }

    public function getCityRef()
    {
        $city = $this->getCity();
        if (!empty($city['Ref'])) {
            return $city['Ref'];
        } else {
            return '';
        }
    }

    protected function setAddressFromOrder()
    {
        $shippingAddressId = $this->getOrder()->getData('shipping_address_id');

        if (!$shippingAddressId) {
            return false;
        }

        $address = Mage::getModel('sales/order_address')->load($shippingAddressId);

        if (!$address->getId()) {
            $this->setAddress(Mage::getModel('sales/order_address'));

            return false;
        }

        $this->setAddress($address);

        return true;
    }

    private function initInternetDocumentIfExists()
    {
        if ($this->getOrder()->getShippingOperatorDocumentNumber() == '') {
            $this->setShippingStrategy(new OpsWay_NovayaPochta_Block_Adminhtml_Sales_Order_View_Shipping_New());
        } elseif (!in_array($this->getOrder()->getData('cargo_data'), array('','-'))) {
            $this->setShippingStrategy(new OpsWay_NovayaPochta_Block_Adminhtml_Sales_Order_View_Shipping_Old());
        } else {
            try {
                $this->setShippingStrategy(new OpsWay_NovayaPochta_Block_Adminhtml_Sales_Order_View_Shipping_NP());
                $this->setInternetDocument(Mage::helper('opsway_novayapochta/api')->getInternetDocumentByNumber(
                    $this->getOrder()->getShippingOperatorDocumentNumber()
                ));
            } catch (Exception $e) {
                $this->setShippingStrategy(new OpsWay_NovayaPochta_Block_Adminhtml_Sales_Order_View_Shipping_New());
            }
        }
    }

    /**
     * @param mixed $internetDocument
     */
    public function setInternetDocument($internetDocument)
    {
        $this->internetDocument = $internetDocument;
    }

    /**
     * @return mixed
     */
    public function getInternetDocument()
    {
        if (!isset($this->internetDocument['CitySenderRef'])) {
            return false;
        }

        return $this->internetDocument;
    }
}