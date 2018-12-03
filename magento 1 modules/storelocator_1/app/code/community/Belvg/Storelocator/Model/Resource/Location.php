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
class Belvg_Storelocator_Model_Resource_Location extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Store model
     *
     * @var null|Mage_Core_Model_Store
     */
    protected $store = null;

    /**
     * Retrieve store model
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        return Mage::app()->getStore($this->store);
    }

    /**
     * Set store model
     *
     * @param Mage_Core_Model_Store $store
     *
     * @return Belvg_Storelocator_Model_Resource_Location
     */
    public function setStore($store)
    {
        $this->store = $store;

        return $this;
    }

    /**
     * Model initialization
     */
    protected function _construct()
    {
        $this->_init('storelocator/location', 'entity_id');
    }

    /**
     * Process page data before saving
     *
     * @param Mage_Core_Model_Abstract $object
     *
     * @return Belvg_Storelocator_Model_Resource_Location
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if ($object->isObjectNew() && !$object->getLongitude() && !$object->getLatitude()) {
            $result = Mage::helper('storelocator/geocoder')->getCoordinates($object->getData('address'))->setPageSize(1)->getFirstItem();
            if ($result instanceof Varien_Object && $result->hasData('longitude') && $result->hasData('latitude')) {
                $object->setLongitude($result->getLongitude());
                $object->setLatitude($result->getLatitude());
            }
        }

        if ($object->getUpdatelatlng()) {
            $result = Mage::helper('storelocator/geocoder')->getCoordinates($object->getData('address'))->setPageSize(1)->getFirstItem();

            if ($result instanceof Varien_Object && $result->hasData('longitude') && $result->hasData('latitude')) {
                $object->setLongitude($result->getLongitude());
                $object->setLatitude($result->getLatitude());
            }
        }

        // modify create / update dates
        if ($object->isObjectNew() && !$object->hasCreationTime()) {
            $object->setCreationTime(Mage::getSingleton('core/date')->gmtDate());
        }

        $object->setUpdateTime(Mage::getSingleton('core/date')->gmtDate());

        return parent::_beforeSave($object);
    }

    /**
     * Assign location to store views
     *
     * @param Mage_Core_Model_Abstract $object
     *
     * @return Belvg_Storelocator_Model_Resource_Location
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $oldStores = $this->lookupStoreIds($object->getId());
        $newStores = (array) $object->getStores();

        if (empty($newStores)) {
            $newStores = (array) $object->getStoreId();
        }

        $table = $this->getTable('storelocator/location_store');
        $insert = array_diff($newStores, $oldStores);
        $delete = array_diff($oldStores, $newStores);

        if ($delete) {
            $where = array(
                'entity_id = ?' => (int) $object->getId(),
                'store_id IN (?)' => $delete,
            );

            $this->_getWriteAdapter()->delete($table, $where);
        }

        if ($insert) {
            $data = array();

            foreach ($insert as $storeId) {
                $data[] = array(
                    'entity_id' => (int) $object->getId(),
                    'store_id' => (int) $storeId,
                );
            }

            $this->_getWriteAdapter()->insertMultiple($table, $data);
        }

        if (!$object->getAllProduct()) {
            $oldProducts = $this->lookupProductIds($object->getId());
            $newProducts = (array) $object->getProducts();

            $table = $this->getTable('storelocator/location_product');
            $insert = array_diff($newProducts, $oldProducts);
            $delete = array_diff($oldProducts, $newProducts);

            if (!empty($newProducts)) {
                if ($delete) {
                    $where = array(
                        'entity_id = ?' => (int) $object->getId(),
                        'product_id IN (?)' => $delete,
                    );

                    $this->_getWriteAdapter()->delete($table, $where);
                }

                if ($insert) {
                    $data = array();

                    foreach ($insert as $productId) {
                        $data[] = array(
                            'entity_id' => (int) $object->getId(),
                            'product_id' => (int) $productId,
                        );
                    }

                    $this->_getWriteAdapter()->insertMultiple($table, $data);
                }
            }
        }

        if ($object->getProductsClear()) {
            $oldProducts = $this->lookupProductIds($object->getId());
            $newProducts = (array) $object->getProducts();

            $table = $this->getTable('storelocator/location_product');
            $delete = array_diff($oldProducts, $newProducts);

            if ($delete) {
                $where = array(
                    'entity_id = ?' => (int) $object->getId(),
                    'product_id IN (?)' => $delete,
                );

                $this->_getWriteAdapter()->delete($table, $where);
            }
        }

        return parent::_afterSave($object);
    }

    /**
     * Get store ids to which specified item is assigned.
     *
     * @param $entityId
     *
     * @return array
     */
    public function lookupStoreIds($entityId)
    {
        $adapter = $this->_getReadAdapter();

        $select = $adapter->select()
            ->from($this->getTable('storelocator/location_store'), 'store_id')
            ->where('entity_id = ?', (int) $entityId);

        return $adapter->fetchCol($select);
    }

    /**
     * Get product ids to which item is assigned.
     *
     * @param $entityId
     *
     * @return array
     */
    public function lookupProductIds($entityId)
    {
        $adapter = $this->_getReadAdapter();

        $select = $adapter->select()
            ->from($this->getTable('storelocator/location_product'), 'product_id')
            ->where('entity_id = ?', (int) $entityId);

        return $adapter->fetchCol($select);
    }

    /**
     * Perform operations after object load
     *
     * @param Mage_Core_Model_Abstract $object
     *
     * @return Belvg_Storelocator_Model_Resource_Location
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        if ($object->getId()) {
            $stores = $this->lookupStoreIds($object->getId());

            $object->setData('store_id', $stores);

            if (!$object->getAllProduct()) {
                $save_products = $this->lookupProductIds($object->getId());
                $products = array();

                foreach ($save_products as $id) {
                    $products[$id] = $id;
                }

                $object->setData('products', $products);
            }
        }

        return parent::_afterLoad($object);
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string                                     $field
     * @param mixed                                      $value
     * @param Mage_Core_Model_Abstract $object
     *
     * @return Zend_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);

        if ($object->getStoreId()) {
            $storeIds = array(Mage_Core_Model_App::ADMIN_STORE_ID, (int) $object->getStoreId());
            $select->join(
                array('storelocator_store' => $this->getTable('storelocator/location_store')),
                $this->getMainTable().'.entity_id = storelocator_store.entity_id',
                array()
            )->where('is_active = ?', 1)
                ->where('storelocator_store.store_id IN (?)', $storeIds)
                ->order('storelocator_store.store_id DESC')
                ->limit(1);
        }

        return $select;
    }

    /**
     * Process page data before deleting
     *
     * @param Mage_Core_Model_Abstract $object
     *
     * @return Belvg_Storelocator_Model_Resource_Location
     */
    protected function _beforeDelete(Mage_Core_Model_Abstract $object)
    {
        $condition = array(
            'entity_id = ?' => (int) $object->getId(),
        );

        $this->_getWriteAdapter()->delete($this->getTable('storelocator/location_store'), $condition);
        $this->_getWriteAdapter()->delete($this->getTable('storelocator/location_product'), $condition);

        return parent::_beforeDelete($object);
    }
}
