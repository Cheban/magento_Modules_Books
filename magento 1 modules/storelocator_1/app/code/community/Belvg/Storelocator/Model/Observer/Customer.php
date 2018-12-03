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
class Belvg_Storelocator_Model_Observer_Customer
{
    /**
     * Key for session.
     */
    const KEY_LOCATION = 'storelocator';

    /**
     * Save current location in session.
     *
     * @param Varien_Event_Observer $observer
     *
     * @return $this
     */
    public function customerSessionInit(Varien_Event_Observer $observer)
    {
        $session = $observer->getEvent()->getCustomerSession();

        if (Mage::helper('storelocator')->isEnabled()) {
            $session->setData(self::KEY_LOCATION, $this->getIpLocation());
        }

        return $this;
    }

    /**
     * Addres by ip.
     *
     * @return mixed
     */
    public function getIpLocation()
    {
        return Mage::helper('storelocator/geoip')->getAddressByIp($this->getCustomerIp());
    }

    /**
     * Return current ip of customer.
     *
     * @return string
     */
    public function getCustomerIp()
    {
        if (Mage::helper('storelocator')->isTestMode()) {
            if (Mage::helper('storelocator')->getTestIp()) {
                return Mage::helper('storelocator')->getTestIp();
            }
        }

        return Mage::helper('core/http')->getRemoteAddr();
    }
}
