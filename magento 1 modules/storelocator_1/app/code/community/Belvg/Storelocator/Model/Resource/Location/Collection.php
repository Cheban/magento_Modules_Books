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
class Belvg_Storelocator_Model_Resource_Location_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Add filter by store
     *
     * @param int|Mage_Core_Model_Store $store
     * @param bool                      $withAdmin
     *
     * @return Belvg_Storelocator_Model_Resource_Location_Collection
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        if (!$this->getFlag('store_filter_added')) {
            if ($store instanceof Mage_Core_Model_Store) {
                $store = array($store->getId());
            }

            if (!is_array($store)) {
                $store = array($store);
            }

            if ($withAdmin) {
                $store[] = Mage_Core_Model_App::ADMIN_STORE_ID;
            }

            $this->addFilter('store_id', array('in' => $store), 'public');
        }

        return $this;
    }

    /**
     * @param $radius
     *
     * @return Belvg_Storelocator_Model_Resource_Location_Collection
     */
    public function addDistanceFilter($radius)
    {
        return $this->getSelect()->group('distance')->having('distance <= '.$radius);
    }

    /**
     * Calculate the distance in a straight line (off road)
     *
     * @param $latitude
     * @param $longitude
     * @param bool $miles
     *
     * @return Belvg_Storelocator_Model_Resource_Location_Collection
     */
    public function addDistance($latitude = 0.0, $longitude = 0.0, $miles = true)
    {
        if ($miles) {
            $fi = '1';
        } else {
            $fi = '1.609344';
        }

        $this->getSelect()->columns(
            array(
                'distance' => new Zend_Db_Expr(
                    '(((acos(sin(('.$latitude.'*pi()/180)) * sin((`main_table`.`latitude`*pi()/180))+cos(('.$latitude.'*pi()/180)) * cos((`main_table`.`latitude`*pi()/180)) * cos((('.$longitude.'- `main_table`.`longitude`)*pi()/180))))*180/pi())*60*1.1515*'.$fi.')'
                ),
            )
        );

        return $this;
    }

    /**
     * Add filter by all products
     *
     * @return Belvg_Storelocator_Model_Resource_Location_Collection
     */
    public function addLocationFilterWithAllProducts()
    {
        return $this->addFieldToFilter('all_product', array('eq' => 1));
    }

    /**
     * Add filter by active
     *
     * @return Belvg_Storelocator_Model_Resource_Location_Collection
     */
    public function addLocationFilterActive()
    {
        return $this->addFieldToFilter('is_active', array('eq' => 1));
    }

    /**
     * Add product info for collection
     *
     * @return $this
     */
    public function addProductInfo()
    {
        $this->getSelect()->joinLeft(
            array('product_table' => $this->getTable('storelocator/location_product')),
            'main_table.entity_id = product_table.entity_id',
            array()
        );

        return $this;
    }

    /**
     * Add filter by product
     *
     * @param $product
     *
     * @return Belvg_Storelocator_Model_Resource_Location_Collection
     */
    public function addLocationFilterByProduct($product)
    {
        if (is_array($product)) {
            $product = implode(', ', $product);
        }

        $this->getSelect()->where('product_table.product_id in ('.$product.') OR all_product = 1');

        return $this;
    }

    /**
     * Add filter by not found
     *
     * @return $this
     */
    public function notFoundProduct()
    {
        $this->getSelect()->where('product_table.product_id in (0)');

        return $this;
    }

    /**
     * Get SQL for get record count
     *
     * @return Varien_Db_Select
     */
    public function getSelectCountSql()
    {
        $this->_renderFilters();

        $countSelect = clone $this->getSelect();
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);

        $countSelect->reset(Zend_Db_Select::COLUMNS);

        $countSelect->columns('COUNT(*)');

        return $countSelect;
    }

    /**
     * Get all ids
     *
     * @return array
     */
    public function getAllIds()
    {
        $idsSelect = clone $this->getSelect();
        $idsSelect->reset(Zend_Db_Select::ORDER);
        $idsSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $idsSelect->reset(Zend_Db_Select::LIMIT_OFFSET);

        $idsSelect->columns($this->getResource()->getIdFieldName(), 'main_table');

        return $this->getConnection()->fetchCol($idsSelect);
    }

    /**
     * Initialize resource
     *
     */
    protected function _construct()
    {
        $this->_init('storelocator/location');
        $this->_map['fields']['entity_id'] = 'main_table.entity_id';
        $this->_map['fields']['store'] = 'store_table.store_id';
    }

    /**
     * Perform operations after collection load
     *
     * @return Belvg_Storelocator_Model_Resource_Location_Collection
     */
    protected function _afterLoad()
    {
        //Default values
        foreach ($this->getItems() as $item) {
            $item->setData('store_id', array(0));
        }

        $items = $this->getColumnValues('entity_id');
        $connection = $this->getConnection();
        if (count($items)) {
            $select = $connection->select()
                ->from(array('storelocator_store' => $this->getTable('storelocator/location_store')))
                ->where('storelocator_store.entity_id IN (?)', $items);

            if ($result = $connection->fetchAll($select)) {
                foreach ($this as $item) {
                    $stores = $this->_getItemStores($item, $result);

                    if (!$stores) {
                        $storeId = array(0);
                    } else {
                        $storeId = $stores;
                    }

                    $item->setData('store_id', $storeId);
                }
            }

            $select = $connection->select()
                ->from(array('storelocator_product' => $this->getTable('storelocator/location_product')))
                ->where('storelocator_product.entity_id IN (?)', $items);

            if ($result = $connection->fetchPairs($select)) {
                $products = array();

                foreach ($this as $item) {
                    if (!isset($result[$item->getData('entity_id')])) {
                        continue;
                    }

                    if ($result[$item->getData('entity_id')] !== 0) {
                        $products[] = $result[$item->getData('entity_id')];
                    }

                    $item->setData('products', $products);
                }
            }
        }

        return parent::_afterLoad();
    }

    /**
     * Array create with store ids for the current item
     *
     * @param array $results
     * @param $item
     *
     * @return array
     */
    protected function _getItemStores($item, $results = array())
    {
        $stores = array();

        foreach ($results as $result) {
            if ($result['entity_id'] == $item->getData('entity_id')) {
                $stores[] = $result['store_id'];
            }
        }

        return $stores;
    }

    /**
     * Join store relation table if there is store filter
     */
    protected function _renderFiltersBefore()
    {
        if ($this->getFilter('store_id')) {
            $this->getSelect()->join(
                array('store_table' => $this->getTable('storelocator/location_store')),
                'main_table.entity_id = store_table.entity_id',
                array()
            );

            /*
             * Allow analytic functions usage because of one field grouping
             */
            $this->_useAnalyticFunction = true;
        }
    }
}
