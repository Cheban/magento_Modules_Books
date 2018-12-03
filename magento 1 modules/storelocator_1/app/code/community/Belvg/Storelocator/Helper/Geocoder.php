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
class Belvg_Storelocator_Helper_Geocoder extends Mage_Core_Helper_Abstract
{
    /**
     * HTTP client
     *
     * @var Zend_Http_Client
     */
    protected $httpClient;
    /**
     * URI
     *
     * @var string
     */
    protected $uri = 'https://maps.googleapis.com/maps/api/geocode/json';

    /**
     * For future ...
     *
     * @param $address
     *
     * @return mixed
     */
    public function prepareAddress($address)
    {
        return $address;
    }

    /**
     * Get coordinates object
     *
     * @param string $address
     *
     * @return Varien_Object
     */
    public function getCoordinates($address)
    {
        $coordinates = new Varien_Data_Collection();
        $data = null;

        if ($address) {
            $httpClient = $this->getHttpClient();
            $httpClient->setUri($this->getUri());
            $httpClient->setParameterGet('key', $this->_getConfigValue('google_key'));
            $httpClient->setParameterGet('address', $address);
            $httpClient->setParameterGet('sensor', 'false');
            try {
                $responce = $httpClient->request('GET');
                if ($responce) {
                    $data = Zend_Json_Decoder::decode($responce->getBody(), Zend_Json::TYPE_OBJECT);
                }
            } catch (Exception $e) {
                $data = null;
            }

            if ($data) {
                if (isset($data->status) && strtoupper(trim($data->status)) == 'OK') {
                    foreach ($data->results as $result) {
                        $geometry = $result->geometry;
                        if (isset($geometry->location) && isset($geometry->location->lat) && isset($geometry->location->lng)) {
                            $item = new Varien_Object();

                            $item->setFullAddress($this->_getFullAddress($result));

                            $location = $geometry->location;
                            $item->setLatitude($location->lat);
                            $item->setLongitude($location->lng);
                            $item->setType(3);
                            $item->setAnswer($data);

                            $coordinates->addItem($item);
                        }
                    }
                }

                if (isset($data->status) && strtoupper(trim($data->status)) == 'OVER_QUERY_LIMIT') {
                    Mage::throwException($data->error_message);
                }
            }
        }

        return $coordinates;
    }

    /**
     * Get HTTP client
     *
     * @return Zend_Http_Client
     */
    protected function getHttpClient()
    {
        if (is_null($this->httpClient)) {
            $this->httpClient = new Zend_Http_Client();
        }

        return $this->httpClient;
    }

    /**
     * Get URI
     *
     * @return string
     */
    protected function getUri()
    {
        return $this->uri;
    }

    /**
     * Create full addresss.
     *
     * @param $data
     *
     * @return string
     */
    protected function _getFullAddress($data)
    {
        $answer = array();

        foreach ($data->address_components as $item) {
            $answer[] = $item->long_name;
        }

        return implode(', ', $answer);
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
        return Mage::getStoreConfig(Belvg_Storelocator_Helper_Data::XML_CONFIG_PATH.$key, $store);
    }
}
