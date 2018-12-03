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
class Belvg_Storelocator_Block_Customer extends Belvg_Storelocator_Block_Abstract
{
    /**
     * @var Belvg_Storelocator_Model_Customer
     */
    protected $location = null;

    /**
     * Can show position
     *
     * @return bool
     */
    public function canShowPosition()
    {
        if ($this->getLocation()->getCity() || $this->getLocation()->getCountryName()) {
            return true;
        }

        return false;
    }

    /**
     * Return current customer location
     *
     * @return Belvg_Storelocator_Model_Customer|null
     */
    public function getLocation()
    {
        if (!$this->location) {
            $this->location = Mage::getModel('storelocator/customer')->getCustomerLocation();
        }

        return $this->location;
    }
}
