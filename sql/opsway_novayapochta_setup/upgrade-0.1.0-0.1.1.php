<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 20.04.15
 * Time: 13:08
 */

/** @var Mage_Sales_Model_Mysql4_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->addAttribute(
    'order',
    'cargo_data',
    array(
        'type' => 'text',
        'visible' => true,
        'required' => false,
        'is_user_defined' => false,
        'note' => 'Cargo data'
    )
);

$installer->endSetup();