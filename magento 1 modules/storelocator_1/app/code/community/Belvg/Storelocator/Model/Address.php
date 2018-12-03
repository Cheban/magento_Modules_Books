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
class Belvg_Storelocator_Model_Address extends Mage_Core_Model_Abstract
{
    /**
     * @var $mixed .
     */
    protected $address;

    /**
     * Set query.
     *
     * @param $near
     */
    public function setNear($near)
    {
        $this->address = $this->prepareQuery($near);
    }

    /**
     * Tool for query.
     *
     * @param $query
     *
     * @return string
     */
    protected function prepareQuery($query)
    {
        return Mage::helper('core')->escapeUrl($query);
    }

    /**
     * Return lat/lng for address.
     *
     * @return mixed
     */
    public function getCoordinates()
    {
        if ($this->address) {
            $this->prepare();

            return Mage::helper('storelocator/geocoder')->getCoordinates($this->getAddress());
        }

        return false;
    }

    /**
     * Prepare query.
     */
    protected function prepare()
    {
        $this->address = Mage::helper('storelocator/geocoder')->prepareAddress($this->getAddress());
    }

    /**
     * Return address(s).
     *
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }
}
