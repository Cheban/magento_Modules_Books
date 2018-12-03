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
class Belvg_Storelocator_Helper_Geoip extends Mage_Core_Helper_Abstract
{
    /**
     * Path lib.
     */
    const GEOIP_DIR = 'GeoIp';
    protected $geoip;
    protected $regionsNames;
    protected $address;

    /**
     * Constructor.
     */
    public function __construct()
    {
        if (!class_exists('GeoIP', false)) {
            include_once $this->_getPath('geoip.inc');
            include_once $this->_getPath('geoipregionvars.php');
            include_once $this->_getPath('geoipcity.inc');

            $this->regionsNames = $GEOIP_REGION_NAME;
        }
    }

    /**
     * Generate path for GeoIP.
     *
     * @param $dir
     *
     * @return string
     */
    protected function _getPath($dir)
    {
        return Mage::getBaseDir('lib').'/'.self::GEOIP_DIR.'/'.$dir;
    }

    /**
     * Get address by ip address.
     *
     * @param $ip
     *
     * @return Varien_Object
     */
    public function getAddressByIp($ip)
    {
        $this->address = new Varien_Object(array(
            'type' => 0,
            'latitude' => 0.0,
            'longitude' => 0.0,
        ));

        $record = $this->getRecordByIp($ip);

        if ($record) {
            $values = array(
                'latitude' => 'latitude',
                'longitude' => 'longitude',
                'country_code' => 'country2',
                'country_code3' => 'country3',
                'country_name' => 'country_name',
                'region' => 'region',
                'city' => 'city',
            );

            foreach ($values as $code => $id) {
                $this->address->setData($id, $this->setRecordValue($code, $id, $record));
            }

            $this->address->setData('type', Belvg_Storelocator_Model_Customer::TYPE_LOCATION_IP);
        }

        return $this->address;
    }

    /**
     * Get data by ip address.
     *
     * @param $ip
     *
     * @return geoiprecord|int|null
     */
    protected function getRecordByIp($ip)
    {
        $geoip = $this->getGeoIp();
        if ($geoip) {
            return geoip_record_by_addr($geoip, $ip);
        } else {
            return null;
        }
    }

    /**
     * Get GeoIP Database.
     *
     * @return mixed
     */
    protected function getGeoIp()
    {
        if (is_null($this->geoip)) {
            $this->geoip = geoip_open($this->_getPath('GeoLiteCity.dat'), GEOIP_STANDARD);
        }

        return $this->geoip;
    }

    /**
     * Set value.
     *
     * @param $code
     * @param $id
     * @param $record
     *
     * @return mixed|null|string
     */
    protected function setRecordValue($code, $id, $record)
    {
        if (isset($record->{$code}) && $record->{$code}) {
            if ($code == 'Region') {
                return $this->getRegionName($this->address->getCountryId(), $this->cleanString($record->{$code}));
            } else {
                return $this->cleanString($record->{$code});
            }
        } else {
            return null;
        }
    }

    /**
     * Get region name.
     *
     * @param $countryCode
     * @param $regionCode
     *
     * @return string
     */
    protected function getRegionName($countryCode, $regionCode)
    {
        if (isset($this->regionsNames[$countryCode]) && isset($this->regionsNames[$countryCode][$regionCode])) {
            return $this->regionsNames[$countryCode][$regionCode];
        } else {
            return '';
        }
    }

    /**
     * Clear value;
     *
     * @param $value
     *
     * @return mixed
     */
    protected function cleanString($value)
    {
        return $value;
        //return Mage::helper('core/string')->clearString($value);
    }

    public function __destruct()
    {
        if (!is_null($this->geoip)) {
            geoip_close($this->geoip);
        }
    }
}
