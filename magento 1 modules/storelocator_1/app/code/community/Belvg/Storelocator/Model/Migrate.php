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
class Belvg_Storelocator_Model_Migrate extends Mage_Core_Model_Abstract
{
    /**
     * Run action.
     *
     * @return array
     */
    public function megrateRows()
    {
        /**
         * @var $collectionMigrate Belvg_Storelocator_Model_Resource_Migrate_Collection
         */
        $collectionMigrate = Mage::getModel('storelocator/migrate')->getCollection();
        /**
         * @var $collectionCreate Belvg_Storelocator_Model_Resource_Location_Collection
         */
        $collectionCreate = Mage::getModel('storelocator/location')->getCollection();

        $errors = array();

        foreach ($collectionMigrate->getItems() as $item) {
            $location = Mage::getModel('storelocator/location');
            $this->prepareFieldsForSave($item, $location);
            $collectionCreate->addItem($location);
        }

        try {
            $collectionCreate->walk('save');
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError($e->getMessage());
            $errors[] = Mage::helper('storelocator')->__('Recording is not possible to migrate.');

            return false;
        }

        return $errors;
    }

    /**
     * Prepare data.
     *
     * @param Belvg_Storelocator_Model_Migrate  $source
     * @param Belvg_Storelocator_Model_Location $target
     */
    public function prepareFieldsForSave(
        Belvg_Storelocator_Model_Migrate $source,
        Belvg_Storelocator_Model_Location $target
    ) {
        $target->setTitle($source->getTitle());
        $target->setDescription($source->getTitle());

        $address = '';

        if ($source->getAddress()) {
            $address .= $source->getAddress();
        }

        if ($source->getCity()) {
            $address .= ', '.$source->getCity();
        }

        if ($source->getState()) {
            $address .= ', '.$source->getState();
        }

        $target->setAddress($address);

        $target->setLatitude($source->getLng());
        $target->setLongitude($source->getLat());
        $target->setPhone($source->getPhone());
        $target->setFavorite(0);
        $target->setStatus(1);
        $target->setStores(array(0));
        $target->setAllProduct();
    }

    /**
     * Define resource model
     */
    protected function _construct()
    {
        $this->_init('storelocator/migrate');
    }

    /**
     * Retrieve adminhtml session model object
     *
     * @return Mage_Adminhtml_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('adminhtml/session');
    }
}
