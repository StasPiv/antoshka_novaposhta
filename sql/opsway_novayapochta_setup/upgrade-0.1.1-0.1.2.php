<?php

/** @var Mage_Sales_Model_Mysql4_Setup $installer */
$installer = $this;
$installer->startSetup();

if ($this->getConnection()->isTableExists($this->getTable('opsway_novayapochta/requests'))) {
    return true;
}

$table = $this->getConnection()
    ->newTable($this->getTable('opsway_novayapochta/requests'))
    ->addColumn(
        'request_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
        ),
        'Request Id'
    )
    ->addColumn(
        'request',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        null,
        array(
            'nullable' => false,
        ),
        'Request'
    )
    ->addColumn(
        'response',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        null,
        array(
            'nullable' => false,
        ),
        'Response'
    )
    ->addColumn(
        'updated_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        array(
            'nullable' => false
        ),
        'Updated at'
    );

$this->getConnection()->createTable($table);

$installer->endSetup();