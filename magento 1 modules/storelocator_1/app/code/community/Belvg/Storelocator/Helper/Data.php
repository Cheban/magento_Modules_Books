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
class Belvg_Storelocator_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Key module in config.
     */
    const XML_CONFIG_PATH = 'storelocator/settings/';

    /**
     * Key module in session.
     */
    const KEY_LOCATION = 'storelocator';

    /**
     * Type of autotarget.
     */
    const TYPE_LOCATION_IP = 1;
    const TYPE_LOCATION_BROWSER = 2;

    /**
     * @var Belvg_Storelocator_Model_Location.
     */
    private $itemInstance;

    /**
     * Check enabled module.
     *
     * @param string $store
     *
     * @return bool
     */
    public function isEnabled($store = '')
    {
        return (bool) $this->_getConfigValue('enabled', $store);
    }

    /**
     * Config value.
     *
     * @param $key
     * @param $store
     *
     * @return mixed
     */
    protected function _getConfigValue($key, $store = '')
    {
        return Mage::getStoreConfig(self::XML_CONFIG_PATH.$key, $store);
    }

    /**
     * Check test mode.
     *
     * @return bool
     */
    public function isTestMode()
    {
        return (bool) Mage::getStoreConfig('storelocator/dev/enabled');
    }

    /**
     * Return test ip.
     *
     * @return bool
     */
    public function getTestIp()
    {
        return Mage::getStoreConfig('storelocator/dev/override_ip');
    }

    /**
     * Default url module.
     *
     * @return string
     */
    public function getStorelocatorUrl()
    {
        return $this->getUrlAction();
    }

    /**
     * Create custom url.
     *
     * @param $name
     * @param array $params
     *
     * @return string
     */
    public function getUrlAction($name = '', $params = array())
    {
        $base = $this->getConfigValue('route');

        if ($name) {
            $url = Mage::getUrl($base.'/'.$name, $params);
        } else {
            $url = Mage::getUrl($base);
        }

        return $url;
    }

    /**
     * Return value from config.
     *
     * @param $key
     * @param string $store
     *
     * @return mixed
     */
    public function getConfigValue($key, $store = '')
    {
        return $this->_getConfigValue($key, $store);
    }

    /**
     * @return string
     */
    public function getStorelocatorUrlUpdateLocation()
    {
        return $this->getUrlAction('updateLocation');
    }

    /**
     * Format show distance.
     *
     * @param float $data
     *
     * @return string
     */
    public function distanceFormater($data = 0.0)
    {
        if ($this->getConfigValue('units') == 'mi') {
            return round($data, 2).' '.$this->__('Miles');
        } else {
            return round($data, 3).' '.$this->__('Kilometers');
        }
    }

    /**
     * Google param
     *
     * @return string
     */
    public function unitsDistance()
    {
        if ($this->getConfigValue('units') == 'mi') {
            return 'imperial';
        } else {
            return 'metric';
        }
    }

    /**
     * Defaut message for info window.
     *
     * @return mixed
     */
    public function getDefaultMessage()
    {
        return $this->getConfigValue('default_message');
    }

    /**
     * Return distance default
     *
     * @return int
     */
    public function getDistanceDefault()
    {
        $options = $this->getDistanceOptions();

        return (int) array_shift($options);
    }

    /**
     * Values of distance.
     *
     * @return array
     */
    public function getDistanceOptions()
    {
        $_distance = explode(',', $this->getConfigValue('radius'));

        if (is_array($_distance) && count($_distance) > 0) {
            array_unshift($_distance, $this->__('Select...'));

            return $_distance;
        } else {
            return array();
        }
    }

    /**
     * Show map on load module?
     *
     * @return bool
     */
    public function getShowMap()
    {
        if (Mage::app()->getRequest()->getActionName() == 'search') {
            return true;
        } else {
            return $this->_getConfigValue('show_map');
        }
    }

    /**
     * Can autodetect location.
     *
     * @return bool
     */
    public function getCanAutoDetectLocation()
    {
        if (!Mage::registry('storelocator_location') && $this->getConfigValue('detect_location')) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Use language for google map
     *
     * @return bool
     */
    public function getUseLanguage()
    {
        return (bool) $this->getConfigValue('use_language');
    }

    /**
     * Return language code.
     *
     * @return mixed
     */
    public function getLanguageCode()
    {
        return $this->getConfigValue('language_code');
    }

    /**
     * Current location.
     *
     * @return Belvg_Storelocator_Model_Location|mixed
     */
    public function getItemInstance()
    {
        if (!$this->itemInstance) {
            $this->itemInstance = Mage::registry('storelocator_item');

            if (!$this->itemInstance) {
                Mage::throwException($this->__('Item instance does not exist in Registry'));
            }
        }

        return $this->itemInstance;
    }

    /**
     * Return header for import/export
     *
     * @return array
     */
    public function getHeaderFile()
    {
        return array(
            'title' => 'Title',
            'address' => 'Address',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'image' => 'Image Path',
            'all_product' => 'All Products?',
            'description' => 'Description',
            'website' => 'Website Url',
            'phone' => 'Phone',
            'icon' => 'Icon',
            'favorite' => 'Favorite?',
            'is_active' => 'Active',
        );
    }

    /**
     * Prepare json object.
     *
     * @param array $data
     *
     * @return string
     */
    public function arrayToJsObject($data = array())
    {
        $data['empty'] = "";

        return json_encode($data);
        /*$object = '{';

        foreach ($data as $key => $value) {
            $object .= $key.': '.$value.',';
        }

        $object .= 'empty: "" }';

        return $object;*/
    }

    /**
     * @return mixed
     */
    public function getGoogleKey()
    {
        return $this->_getConfigValue('google_key');
    }

}
