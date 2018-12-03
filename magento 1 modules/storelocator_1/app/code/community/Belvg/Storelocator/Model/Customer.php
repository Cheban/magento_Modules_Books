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
class Belvg_Storelocator_Model_Customer extends Mage_Customer_Model_Session
{
    const KEY_LOCATION = 'storelocator';

    const TYPE_LOCATION_IP = 1;
    const TYPE_LOCATION_BROWSER = 2;

    /**
     * Current location.
     *
     * @return mixed
     */
    public function getLocation()
    {
        if (Mage::registry('storelocator_location')) {
            return Mage::registry('storelocator_location');
        } else {
            return $this->_getLocation();
        }
    }

    /**
     * Return location by ip.
     *
     * @return mixed
     */
    protected function _getLocation()
    {
        if (!$this->hasData(self::KEY_LOCATION)) {
            $this->setData(
                self::KEY_LOCATION,
                Mage::helper('storelocator/geoip')->getAddressByIp($this->_getCustomerIp())
            );
        }

        return $this->getData(self::KEY_LOCATION);
    }

    /**
     * Customer ip.
     *
     * @return mixed
     */
    protected function _getCustomerIp()
    {
        if (Mage::helper('storelocator')->isTestMode()) {
            if (Mage::helper('storelocator')->getTestIp()) {
                return Mage::helper('storelocator')->getTestIp();
            }
        }

        return Mage::helper('core/http')->getRemoteAddr();
    }

    /**
     * Return current location customer
     *
     * @return mixed
     */
    public function getCustomerLocation()
    {
        return $this->_getLocation();
    }

    /**
     * Set current location.
     *
     * @param $latitude
     * @param $longitude
     */
    public function setLocation($latitude, $longitude)
    {
        if ($this->hasData(self::KEY_LOCATION)) {
            $currentLocation = $this->getData(self::KEY_LOCATION);

            if ($currentLocation->getData('type') != self::TYPE_LOCATION_BROWSER) {
                $currentLocation->setData('latitude', $latitude);
                $currentLocation->setData('longitude', $longitude);
                $currentLocation->setData('type', self::TYPE_LOCATION_BROWSER);
            }
        }
    }

    /**
     * Return marker options as js object
     */
    public function getMarkerOptions()
    {
        $_options = array();
        $_helper = Mage::helper('storelocator');

        $_options['lat'] = $this->getLatitude();
        $_options['lng'] = $this->getLongitude();
        $_options['html'] = $_helper->__('You are here!');

        return $_helper->arrayToJsObject($_options);
    }
}
