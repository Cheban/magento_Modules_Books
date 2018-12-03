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
class Belvg_Storelocator_Block_Abstract extends Mage_Core_Block_Template
{
    /**
     * Products collection
     *
     * @var Belvg_Storelocator_Model_Resource_Location_Collection
     */
    protected $collection;

    /**
     * Return current data of customer.
     *
     * @return Mage_Core_Model_Abstract
     */
    public function getCustomer()
    {
        $customer = Mage::getModel('storelocator/customer');

        return $customer;
    }

    /**
     * Return if you specify multiple answers from Google.
     *
     * @return mixed
     */
    public function getMultyAnswer()
    {
        return Mage::registry('storelocator_multyanswer');
    }

    /**
     * Can show items.
     *
     * @return bool
     */
    public function canShowResult()
    {
        return (bool) ($this->helper('storelocator')->getShowMap() && $this->getCollection()->getSize());
    }

    /**
     * Return products collection instance
     *
     * @return Belvg_Storelocator_Model_Resource_Location_Collection|null
     */
    public function getCollection()
    {
        if (!$this->collection) {
            $this->collection = Mage::getModel('storelocator/location')->getCollection();

            $_filter = $this->_filterProductCollection();

            // current location of customer
            $_customerLocation = Mage::getSingleton('storelocator/customer')->getLocation();

            $_filter->addFieldToSelect(array('latitude', 'longitude'))
                ->addDistance($_customerLocation->getLatitude(), $_customerLocation->getLongitude());

            if ($this->getRadius()) {
                $_filter->addDistanceFilter($this->getRadius());
            }

            $this->collection
                ->addFieldToFilter('entity_id', array('in' => $_filter->getAllIds()))
                ->addStoreFilter(Mage::app()->getStore()->getId())
                ->addLocationFilterActive()
                ->setPageSize($this->getLimit())
                ->addDistance($_customerLocation->getLatitude(), $_customerLocation->getLongitude());

            $this->collection->getSelect()->order(array('favorite DESC', 'distance ASC'));
        }

        return $this->collection;
    }

    /**
     * Set collection.
     *
     * @param Belvg_Storelocator_Model_Resource_Location_Collection $collection
     *
     * @return Belvg_Storelocator_Model_Resource_Location_Collection
     */
    public function setCollection(Belvg_Storelocator_Model_Resource_Location_Collection $collection)
    {
        $this->collection = $collection;

        return $this->collection;
    }

    /**
     * Prepare filter collection by product
     * @return mixed|object
     */
    protected function _filterProductCollection()
    {
        if (Mage::registry('storelocator_result')) {
            return Mage::registry('storelocator_result');
        } else {
            return Mage::getModel('storelocator/location')->getCollection();
        }
    }

    /**
     * Return radius render
     *
     * @return int
     */
    public function getRadius()
    {
        $options = $this->helper('storelocator')->getDistanceOptions();
        $defualt = (int) array_shift($options);

        return (int) $this->getRequest()->getParam('distance', $defualt);
    }

    /**
     * Get specified locations limit display per page
     *
     * @return string
     */
    public function getLimit()
    {
        return (int) $this->helper('storelocator')->getConfigValue('location_count');
    }

    /**
     * Show map on load module?
     *
     * @return bool
     */
    public function getShowMap()
    {
        return (bool) $this->helper('storelocator')->getShowMap();
    }
}
