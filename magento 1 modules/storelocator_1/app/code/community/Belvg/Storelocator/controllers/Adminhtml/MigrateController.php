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
class Belvg_Storelocator_Adminhtml_MigrateController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_initAction();
        $this->renderLayout();
    }

    /**
     * Prepare header grid.
     *
     * @return $this
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('storelocator/manage')
            ->_addBreadcrumb(
                Mage::helper('storelocator')->__('Stores'),
                Mage::helper('storelocator')->__('Stores')
            )
            ->_addBreadcrumb(
                Mage::helper('storelocator')->__('Migrate from Stores 1.0'),
                Mage::helper('storelocator')->__('Migrate from Stores 1.0')
            );

        $this->_title($this->__('Stores'))
            ->_title($this->__('Migrate from Stores 1.0'));

        return $this;
    }

    public function runAction()
    {
        if ($this->getRequest()->isPost()) {
            /* @var $model Belvg_Storelocator_Model_Migrate */
            $model = Mage::getModel('storelocator/migrate');

            $result = $model->megrateRows();

            if ($result) {
                foreach ($result as $error) {
                    Mage::getSingleton('core/session')->addError($error);
                }
            }

            Mage::getSingleton('core/session')->addSuccess(Mage::helper('storelocator')->__('Migrate successed'));

            $this->_forward('index');
        }
    }

    /**
     * Check ACL rules
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        /** @noinspection PhpUnreachableStatementInspection */
        switch ($this->getRequest()->getActionName()) {
            case 'run':
                $result = Mage::getSingleton('admin/session')->isAllowed('store/migrate/run');
                break;

            default:
                $result = true;
                break;
        }

        return $result;
    }
}
