<?php

require_once('OpsWay/NovaPoshta/fake_autoload.php');

use OpsWay\NovaPoshta\Client;
use OpsWay\NovaPoshta\Address;
use OpsWay\NovaPoshta\Counterparty;
use OpsWay\NovaPoshta\InternetDocument;
use OpsWay\NovaPoshta\InternetDocument\Document;
use OpsWay\NovaPoshta\InternetDocument\Filter as InternetDocumentFilter;

class OpsWay_NovayaPochta_Helper_Api extends Mage_Core_Helper_Abstract
{
    private $keyForAntoshka = '949e86dcdf5d535de09df874c388b434';

    /**
     * @return Address
     */
    public function getAddress()
    {
        return $this->initClientForAntoshkaAndMagento(new Address());
    }

    /**
     * @return Counterparty
     */
    public function getCounterparty()
    {
        return $this->initClientForAntoshkaAndMagento(new Counterparty());
    }

    /**
     * @return InternetDocument
     */
    public function getInternetDocument()
    {
        return $this->initClientForAntoshkaAndMagento(new InternetDocument());
    }

    /**
     * @return Document
     */
    public function getNullDocumentForAntoshka()
    {
        $document = new Document;
        $document->PayerType = 'Sender';
        $document->PaymentMethod = 'NonCash';
        $document->CargoType = 'Cargo';
        $document->ServiceType = 'DoorsWarehouse';
        $document->Description = 'Детские товары';
        $document->AdditionalInformation = 'Хрупко';
        return $document;
    }

    private function initClientForAntoshkaAndMagento(Client $client)
    {
        $client->setKey($this->keyForAntoshka);
        $client->setLogger(array($this,'log'));
        $client->setCache(Mage::helper('opsway_novayapochta/cacher'));
        return $client;
    }

    public function getInternetDocumentByNumber($number)
    {
        $filter = new InternetDocumentFilter();
        $filter->IntDocNumber = $number;
        $responseData = $this->getInternetDocument()->getDocumentList($filter);

        if (isset($responseData['data'][0])) {
            $documentByRef = $this->getInternetDocument()->getDocumentByRef($responseData['data'][0]['Ref']);
            return $documentByRef['data'][0];
        } else {
            throw new \OpsWay\NovaPoshta\Exception('EN is not found');
        }
    }

    /**
     * @return array
     */
    public function getAvailableSenderCities()
    {
        $availableSenders = Mage::helper('opsway_novayapochta/api')->getCounterparty()->getSenders();

        $availableSenderCities = array();
        foreach ($availableSenders['data'] as $sender) {
            $availableSenderCities[$sender['City']] = $sender['City'];
        }

        return $availableSenderCities;
    }

    public function log($message)
    {
        Mage::log($message, null, 'novaposhta.log');
    }
}