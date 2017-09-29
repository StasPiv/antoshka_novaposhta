<?php

require_once('OpsWay/NovaPoshta/fake_autoload.php');

use OpsWay\NovaPoshta\Counterparty\Filter\Sender as SenderFilter;
use OpsWay\NovaPoshta\Counterparty\Agent;
use OpsWay\NovaPoshta\InternetDocument\Document;
use OpsWay\NovaPoshta\InternetDocument\Document\Seat;
use OpsWay\NovaPoshta\InternetDocument\Filter;

class OpsWay_NovayaPochta_RequestController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $this->_sendRequest();
    }

    public function getAddressesAction()
    {
        $this->getResponse()->setBody(
            Mage::helper('core')->jsonEncode(
                Mage::helper('opsway_novayapochta/api')->getCounterparty()->getCounterpartyAddresses(
                    $this->getRequest()->getParam('ref')
                )
            )
        );
    }

    public function getContactsAction()
    {
        $this->getResponse()->setBody(
            Mage::helper('core')->jsonEncode(
                Mage::helper('opsway_novayapochta/api')->getCounterparty()->getCounterpartyContactPersons(
                    $this->getRequest()->getParam('ref')
                )
            )
        );
    }

    public function getSendersAction()
    {
        $filter = new SenderFilter();
        $filter->CityRef = $this->getRequest()->getParam('ref');

        $this->getResponse()->setBody(
            Mage::helper('core')->jsonEncode(
                Mage::helper('opsway_novayapochta/api')->getCounterparty()->getCounterparties($filter)
            )
        );
    }

    private function _sendRequest()
    {
        try {
            $documentForAdd = $this->initDocument(
                Mage::helper('opsway_novayapochta/api')->getCounterparty()->save($this->initRecipient())
            );
        } catch (Exception $e) {
            $this->getResponse()->setBody(
                Mage::helper('core')->jsonEncode(
                    $e->getMessage()
                )
            );
            return;
        }

        try {
            $this->removeDocumentsWithThisMagentoOrder($documentForAdd);

            $this->getResponse()->setBody(
                Mage::helper('core')->jsonEncode(
                    Mage::helper('opsway_novayapochta/api')->getInternetDocument()->save($documentForAdd)
                )
            );
        } catch (Exception $e) {
            $this->getResponse()->setBody(
                Mage::helper('core')->jsonEncode($e->getMessage())
            );
        }
    }

    /**
     * @param $recipientFromNP
     * @return Document
     */
    private function initDocument($recipientFromNP)
    {
        $documentForAdd = Mage::helper('opsway_novayapochta/api')->getNullDocumentForAntoshka();

        $documentForAdd->AfterpaymentOnGoodsCost = $this->getRequest()->getParam('afterpayment');

        $documentForAdd->Recipient = $recipientFromNP['data'][0]['Ref'];
        $documentForAdd->RecipientAddress = $this->getRequest()->getParam('recipient_address');
        $documentForAdd->RecipientsPhone = $this->getRequest()->getParam('customer_telephone');
        $documentForAdd->CityRecipient = $this->getRequest()->getParam('city_ref');
        $documentForAdd->ContactRecipient = $recipientFromNP['data'][0]['ContactPerson']['data'][0]['Ref'];
        $documentForAdd->Sender = $this->getRequest()->getParam('sender');
        $documentForAdd->SenderAddress = $this->getRequest()->getParam('sender_address');

        $senderContactPerson = Mage::helper('opsway_novayapochta/api')->getCounterparty()
            ->getCounterpartyContactPerson($documentForAdd->Sender, $this->getRequest()->getParam('sender_contacts'));

        $documentForAdd->SendersPhone = $senderContactPerson['Phones'];
        $documentForAdd->ContactSender = $senderContactPerson['Ref'];
        $documentForAdd->CitySender = $this->getRequest()->getParam('sender_city');

        $documentForAdd->DateTime = date('d.m.Y', time() + 3600 * 2);
        $documentForAdd->Cost = $this->getRequest()->getParam('cost_order');

        $documentForAdd->SeatsAmount = $this->getRequest()->getParam('seats');

        $seatWidths = $this->getRequest()->getParam('seat_width');
        $seatHeights = $this->getRequest()->getParam('seat_height');
        $seatLengths = $this->getRequest()->getParam('seat_length');
        $seatWeights = $this->getRequest()->getParam('seat_weight');
        foreach ($seatWidths as $key => $seatWidth) {
            $newSeat = new Seat;
            $newSeat->volumetricHeight = $seatHeights[$key];
            $newSeat->volumetricWidth = $seatWidth;
            $newSeat->volumetricLength = $seatLengths[$key];
            $newSeat->weight = $seatWeights[$key];

            $documentForAdd->Weight += $newSeat->weight;

            $documentForAdd->addSeat($newSeat);
        }

        $documentForAdd->InfoRegClientBarcodes = $this->getRequest()->getParam('inner_order_id');
        return $documentForAdd;
    }

    /**
     * @return Agent
     */
    private function initRecipient()
    {
        $recipientForAdd = new Agent;
        $recipientForAdd->CityRef = $this->getRequest()->getParam('city_ref');
        $recipientForAdd->FirstName = $this->getRequest()->getParam('customer_firstname');
        $recipientForAdd->MiddleName = $this->getRequest()->getParam('customer_middlename') . ' ' . $this->getRequest(
            )->getParam('inner_order_id');

        $recipientForAdd->LastName = $this->getRequest()->getParam('customer_lastname') == ''
                                     ? 'Фамилия'
                                     : $this->getRequest()->getParam('customer_lastname');

        $recipientForAdd->Phone = preg_replace('/\D/','',$this->getRequest()->getParam('customer_telephone'));
        return $recipientForAdd;
    }

    /**
     * @param Document $documentForAdd
     */
    private function removeDocumentsWithThisMagentoOrder(Document $documentForAdd)
    {
        $filter = new Filter;
        $filter->InfoRegClientBarcodes = $documentForAdd->InfoRegClientBarcodes;

        $oldDocuments = Mage::helper('opsway_novayapochta/api')->getInternetDocument()->getDocumentList($filter);
        if (isset($oldDocuments['data'])) {
            foreach ($oldDocuments['data'] as $document) {
                Mage::helper('opsway_novayapochta/api')->getInternetDocument()->delete($document['Ref']);
            }
        }
    }
}