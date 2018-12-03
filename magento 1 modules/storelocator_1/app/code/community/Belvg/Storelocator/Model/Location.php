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
class Belvg_Storelocator_Model_Location extends Mage_Core_Model_Abstract
{
    /**
     * Return marker options as js object
     */
    public function getMarkerOptions()
    {
        $_options = array();
        $_helper = Mage::helper('storelocator');

        $_options['lat'] = $this->getLatitude();
        $_options['lng'] = $this->getLongitude();
        $_options['html'] = Mage::helper('storelocator/marker')->renderInfoWindow($this);

        if ($this->getIcon()) {
            $_options['icon'] = "'" . Mage::helper('storelocator/icon')->resize($this, 30, 30) . "'";
        }

        return $_helper->arrayToJsObject($_options);
    }

    /**
     * Validation.
     *
     * @param bool $return
     *
     * @return bool
     */
    public function validate($return = false)
    {
        $errors = array();
        $helper = Mage::helper('storelocator');

        if (!Zend_Validate::is($this->getTitle(), 'NotEmpty')) {
            $errors[] = $helper->__('Title can\'t be empty');
            $this->_getSession()->addError($helper->__('Title can\'t be empty'));
        }

        if (!Zend_Validate::is($this->getDescription(), 'NotEmpty')) {
            $errors[] = $helper->__('Description can\'t be empty');
            $this->_getSession()->addError($helper->__('Description can\'t be empty'));
        }

        if (!Zend_Validate::is($this->getAddress(), 'NotEmpty')) {
            $errors[] = $helper->__('Address can\'t be empty');
            $this->_getSession()->addError($helper->__('Address can\'t be empty'));
        }

        if (empty($errors)) {
            return true;
        } else {
            if ($return) {
                return $errors;
            } else {
                return false;
            }
        }
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

    /**
     * @param string $data
     */
    public function parseProducts($data = '')
    {
        $products_ids = array();

        if ($data) {
            parse_str($data, $products_ids);

            foreach ($products_ids as $key => $item) {
                if (!is_integer($key)) {
                    unset($products_ids[$key]);
                }
            }
        }

        $this->setAllProduct(0);
        $this->setProducts($products_ids);
    }

    /**
     * Define resource model
     */
    protected function _construct()
    {
        $this->_init('storelocator/location');
    }
}
