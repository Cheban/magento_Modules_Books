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
class Belvg_Storelocator_StorelocatorController extends Belvg_Storelocator_Controller_Front_Action
{
    /**
     * Index page
     */
    public function indexAction()
    {
        $this->loadLayout();

        /* @var $_helper Belvg_Storelocator_Helper_Data */
        $_helper = Mage::helper('storelocator');

        $this->getLayout()->getBlock('head')->setTitle($this->__($_helper->getConfigValue('title')));

        $this->renderLayout();
    }

    /**
     * Search function
     */
    public function searchAction()
    {
        $this->loadLayout();

        /* @var $_helper Belvg_Storelocator_Helper_Data */
        $_helper = Mage::helper('storelocator');

        $this->getLayout()->getBlock('head')->setTitle($this->__($_helper->getConfigValue('title')));

        /* @var $query Mage_CatalogSearch_Model_Query */
        $query = Mage::helper('storelocator/search')->getQuery();
        /* @var $location Belvg_Storelocator_Model_Resource_Location_Collection */
        $location = Mage::getModel('storelocator/location')->getCollection();

        /* Init product info */
        $location->addProductInfo();

        if (!is_null($query) && $query->getQueryText()) {
            $query->setStoreId(Mage::app()->getStore()->getId());

            $query->prepare();

            $collection = $query->getResultCollection()->load();

            /* @var $location Belvg_Storelocator_Model_Resource_Location_Collection */
            if ($collection->getSize()) {
                $location->addLocationFilterByProduct($collection->getAllIds());
            } else {
                $location->notFoundProduct();
            }
        }

        Mage::register('storelocator_result', $location);

        $near = $this->getRequest()->getParam('near', '');

        if ($near) {
            /* @var $address Belvg_Storelocator_Model_Address */
            $address = Mage::getSingleton('storelocator/address');

            $address->setNear($near);

            /* @var $_currentAnswer Varien_Data_Collection */
            $_currentAnswer = $address->getCoordinates();

            if ($_currentAnswer && $_currentAnswer->getSize()) {
                if ($_currentAnswer->getSize() > 1) {
                    Mage::register('storelocator_multyanswer', $_currentAnswer);
                }

                if ($_currentAnswer->setPageSize(1)->getFirstItem()) {
                    Mage::register('storelocator_location', $_currentAnswer->setPageSize(1)->getFirstItem());
                } else {
                    Mage::register('storelocator_location', new Varien_Object());
                    $location->notFoundProduct();
                }
            } else {
                $location->notFoundProduct();
            }
        }

        $this->renderLayout();
    }

    /**
     * Update location.
     */
    public function updateLocationAction()
    {
        if ($this->getRequest()->isAjax() && $this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('updateLocation', '');

            if (is_array($data)) {
                if ($data['formkey'] == Mage::getSingleton('core/session')->getFormKey()) {
                    Mage::getSingleton('storelocator/customer')
                        ->setLocation((float) $data['lat'], (float) $data['lng']);
                }
            }
        }
    }

    /**
     * Retrieve catalog session
     *
     * @return Mage_Catalog_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('catalog/session');
    }
}
