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
class Belvg_Storelocator_Block_Script extends Belvg_Storelocator_Block_Abstract
{
    /**
     * Return json object for init Gmap.
     *
     * @return mixed
     */
    public function getGmapParams()
    {
        $options = array();

        /* @var $_helper Belvg_Storelocator_Helper_Data */
        $_helper = $this->helper('storelocator');

        $options['baseUrl'] = $this->getUrl('storelocator/storelocator');

        if ($_helper->getCanAutoDetectLocation()) {
            $options['searchLocation'] = (bool) $_helper->getConfigValue('detect_location');
        }

        $options['defaultLat'] = (float) $this->getCustomerLocation()->getLatitude();
        $options['defaultLng'] = (float) $this->getCustomerLocation()->getLongitude();
        $options['defaultMessage'] = base64_encode($_helper->getDefaultMessage());
        $options['showItemLoadPage'] = (int) $this->canShowResult();
        $options['zoom'] = (int) $_helper->getConfigValue('google_zoom');
        $options['directions'] = (bool) $_helper->getConfigValue('directions');
        $options['zoom_marker'] = (int) $_helper->getConfigValue('google_zoom_marker');
        $options['units'] = (string) $_helper->unitsDistance();
        $options['formkey'] = $this->getFormKey();

        Mage::dispatchEvent('gmap_options', array(
            'object' => $this,
            'options' => $options,
        ));

        return $this->helper('core')->jsonEncode($options);
    }

    /**
     * Return customer location.
     *
     * @return mixed
     */
    public function getCustomerLocation()
    {
        return Mage::getSingleton('storelocator/customer')->getLocation();
    }

    /**
     * Key for form.
     *
     * @return mixed
     */
    public function getFormKey()
    {
        return Mage::getSingleton('core/session')->getFormKey();
    }

    /**
     * Path for update location.
     *
     * @return mixed
     */
    public function getUrlUpdateLocation()
    {
        return $this->helper('storelocator')->getStorelocatorUrlUpdateLocation();
    }
}
