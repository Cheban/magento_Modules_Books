<?php

/**
 * BelVG LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 *
 * **************************************
 *         MAGENTO EDITION USAGE NOTICE *
 * ***************************************
 * This package designed for Magento COMMUNITY edition
 * BelVG does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * BelVG does not provide extension support in case of
 * incorrect edition usage.
 * **************************************
 *         DISCLAIMER   *
 * ***************************************
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future.
 * ****************************************************
 * @category    Belvg
 * @package     Belvg_Storelocator
 * @copyright   Copyright (c) 2010 - 2015 BelVG LLC. (http://www.belvg.com)
 * @license     http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 */

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$table = $installer->getConnection()
    ->newTable($installer->getTable('storelocator/location'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'unsigned' => true,
        'nullable' => false,
        'primary' => true,
    ), 'Location Id')
    ->addColumn('title', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(), 'Title')
    ->addColumn('description', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(), 'Description')
    ->addColumn('address', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(), 'Street')
    ->addColumn('image', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(), 'Image')
    ->addColumn('all_product', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'default' => '0',
        'nullable' => false,
        'primary' => true,
    ), 'For All Products')
    ->addColumn('latitude', Varien_Db_Ddl_Table::TYPE_FLOAT, null, array(
        'default' => '0',
    ), 'Latitude')
    ->addColumn('longitude', Varien_Db_Ddl_Table::TYPE_FLOAT, null, array(
        'default' => '0',
    ), 'Longitude')
    ->addColumn('phone', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(), 'Phone')
    ->addColumn('website', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(), 'Website Url')
    ->addColumn('is_active', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable' => false,
        'default' => '0',
    ), 'Is Active')
    ->setComment('Store Locator');
$installer->getConnection()->createTable($table);

/**
 * Create table 'belvg_storelocator_store'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('storelocator/location_store'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'nullable' => false,
        'primary' => true,
    ), 'Location ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned' => true,
        'nullable' => false,
        'primary' => true,
    ), 'Store ID')
    ->addIndex(
        $installer->getIdxName(
            'storelocator/location_store',
            array('store_id')
        ),
        array('store_id')
    )
    ->addForeignKey(
        $installer->getFkName(
            'storelocator/location_store',
            'entity_id',
            'storelocator/location',
            'entity_id'
        ),
        'entity_id',
        $installer->getTable('storelocator/location'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName('storelocator/location_store', 'store_id', 'core/store', 'store_id'),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Store Locator To Store Linkage Table');
$installer->getConnection()->createTable($table);

$installer->endSetup();
