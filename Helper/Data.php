<?php

/**
 * Created by PhpStorm.
 * User: stas
 * Date: 12/14/14
 * Time: 3:39 PM
 */
class OpsWay_NovayaPochta_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getWarehouseCodeFromString($city, $string)
    {
        $wareHouses = Mage::helper('opsway_novayapochta/api')->getAddress()->getWarehouses(
            $city,
            $this->_extractStreet($string)
        );

        if (empty($wareHouses)) {
            return false;
        }

        foreach ($wareHouses as $wareHouse) {
            return $wareHouse;
        }
    }

    private function _extractStreet($string)
    {
        preg_match('/(.+) \(/', $string, $matches);
        return isset($matches[1]) ? $matches[1] : null;
    }

    private function getTtnFromSalesOrders()
    {
        $collection = Mage::getModel('sales/order')->getCollection()
                        ->addFieldToFilter('shipping_operator_document_number', array('neq' => ''));

        $ttns = array_map(
            function($row)
            {
                $shipping_operator_document_number = $row['shipping_operator_document_number'];
                if ($row['cargo_data'] != '-' && !preg_match('\D', $shipping_operator_document_number) && strlen($shipping_operator_document_number) == 14) {
                    return $shipping_operator_document_number;
                }
            },
            $collection->getItems()
        );

        return array_filter(
            $ttns,
            function($row)
            {
                return $row != '';
            }
        );
    }

    public function fillCargoDataIntoOrdersFromNPLogs()
    {
        $ttnFromSalesOrders = $this->getTtnFromSalesOrders();
        $countAllTtns = count($ttnFromSalesOrders);
        $i = 0;
        foreach ($ttnFromSalesOrders as $ttn) {
            echo ++$i . ' / ' . $countAllTtns . "\n";
            $order = Mage::getModel('sales/order')->load($ttn, 'shipping_operator_document_number');
            if ($order->getData('cargo_data') != '') {
                continue;
            }
            echo 'search ' . $ttn . "...\n";
            $infoFromLogs = $this->getInfoFromTtn($ttn);
            if (isset($infoFromLogs[1])) {
                $sendData = json_decode(preg_replace('/.+Send: /', '', $infoFromLogs[1]), true);
                $order->setData('cargo_data', $this->prepareCargoData($sendData['methodProperties']))
                      ->save();
                echo 'found and save ' . $ttn . "...\n";
            } else {
                $order->setData('cargo_data', '-')->save();
                echo 'not found and save ' . $ttn . "...\n";
            }
        }
    }

    /**
     * @param $ttnFromNp
     * @return mixed
     */
    private function getInfoFromTtn($ttnFromNp)
    {
        exec('tac /var/www/current/var/log/novaposhta.log | grep "' . $ttnFromNp . '\",\"TypeDocument" -A1 -m1', $ouptput);
        return $ouptput;
    }
    
    private function prepareCargoData($sendData)
    {
        return json_encode(
            array (
                'form_key' => 'CGvyUSL8e1AzR3IK',
                'token' => '',
                'operator' => 'Новая Почта',
                'seats' => $sendData['SeatsAmount'],
                'seat_width' => $this->getSeatParam($sendData, 'volumetricWidth'),
                'seat_height' => $this->getSeatParam($sendData, 'volumetricHeight'),
                'seat_length' => $this->getSeatParam($sendData, 'volumetricLength'),
                'seat_weight' => $this->getSeatParam($sendData, 'volumetricWeight'),
                'sender_city' => $sendData['CitySender'],
                'sender' => $sendData['Sender'],
                'sender_address' => $sendData['SenderAddress'],
                'sender_contacts' => $sendData['ContactSender'],
                'recipient_address' => $sendData['RecipientAddress'],
                'afterpayment' => $sendData['AfterpaymentOnGoodsCost'],
                'customer_firstname' => '',
                'customer_middlename' => '',
                'customer_lastname' => '',
                'customer_telephone' => '',
                'cost_order' => $sendData['Cost'],
                'city_ref' => '',
                'inner_order_id' => $sendData['InfoRegClientBarcodes'],
                'operator_amount' => '',
                'customer_paid' => '',
                'operator_doc_num' => '',
                'shipment_date' => ''
            )
        );
    }

    /**
     * @param $sendData
     * @return array
     */
    private function getSeatParam($sendData, $param)
    {
        $paramWithNullBegin = array_map(
            function ($seat) use ($param) {
                return $seat[$param];
            },
            $sendData['OptionsSeat']
        );

        $paramWithOneBegin = array();

        $i = 1;
        foreach ($paramWithNullBegin as $value) {
            $paramWithOneBegin[$i++] = $value;
        }

        return $paramWithOneBegin;
    }
}