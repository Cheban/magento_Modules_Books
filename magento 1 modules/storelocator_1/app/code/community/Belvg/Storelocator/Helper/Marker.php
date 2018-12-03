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
class Belvg_Storelocator_Helper_Marker extends Mage_Core_Helper_Abstract
{
    /**
     * Render info for marker
     *
     * @param Belvg_Storelocator_Model_Location $location
     *
     * @return string
     */
    public function renderInfoWindow(Belvg_Storelocator_Model_Location $location)
    {
        /* @var $layout Mage_Core_Model_Layout */
        $layout = Mage::getSingleton('core/layout');

        /* @var $infoWindow Mage_Core_Block_Abstract */
        $infoWindow = $layout->createBlock(
            'storelocator/location',
            'infoWindow',
            array(
                'template' => 'belvg/storelocator/infowindow.phtml'
            )
        );

        $infoWindow->setLocation($location);

        Mage::dispatchEvent('gmap_render_marker', array(
            'markerInfo' => $infoWindow,
        ));

        return Mage::helper('core')->jsonEncode($infoWindow->toHtml());
    }
}
