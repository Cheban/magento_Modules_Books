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
class Belvg_Storelocator_Controller_Front_Router extends Mage_Core_Controller_Varien_Router_Abstract
{
    /**
     * @param Varien_Event_Observer $observer
     *
     * @return $this
     */
    public function initControllerRouters(Varien_Event_Observer $observer)
    {
        $front = $observer->getEvent()->getFront();
        $front->addRouter('storelocator', $this);

        return $this;
    }

    /**
     * @param Zend_Controller_Request_Http $request
     *
     * @return bool
     */
    public function match(Zend_Controller_Request_Http $request)
    {
        if (!Mage::isInstalled()) {
            Mage::app()->getFrontController()->getResponse()
                ->setRedirect(Mage::getUrl('install'))
                ->sendResponse();
            exit;
        }

        if (!Mage::helper('storelocator')->getConfigValue('enabled')) {
            return false;
        }

        $pathInfo = trim($request->getPathInfo(), '/');
        $params = explode('/', $pathInfo);
        if (isset($params[0]) && $params[0] == Mage::helper('storelocator')->getConfigValue('route')) {
            $request->setModuleName('storelocator')
                ->setControllerName('storelocator');

            if (isset($params[1])) {
                $request->setActionName($params[1]);
            }

            $request->setAlias(
                Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,
                $pathInfo
            );

            return true;
        }

        return false;
    }
}
