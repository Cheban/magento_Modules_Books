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
class Belvg_Storelocator_Adminhtml_StoresController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Main action.
     */
    public function indexAction()
    {
        $this->_initAction();
        $this->renderLayout();
    }

    /**
     * Init action.
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
                Mage::helper('storelocator')->__('Manage Stores'),
                Mage::helper('storelocator')->__('Manage Stores')
            );

        $this->_title($this->__('Stores'))
            ->_title($this->__('Manage Stores'));

        return $this;
    }

    /**
     * Create action.
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * Edit action.
     *
     * @return $this|Mage_Core_Controller_Varien_Action
     */
    public function editAction()
    {
        $this->_title($this->__('Stores'))
            ->_title($this->__('Manage Stores'));

        /* @var $model Belvg_Storelocator_Model_Location */
        $model = Mage::getModel('storelocator/location');

        $itemId = $this->getRequest()->getParam('id');

        if ($itemId) {
            $model->load($itemId);

            if (!$model->getEntityId()) {
                $this->_getSession()->addError(
                    Mage::helper('storelocator')->__('Item does not exist.')
                );

                $this->_redirect('*/*/');
            }

            $this->_title($model->getTitle());
            $breadCrumb = Mage::helper('storelocator')->__('Edit Item');
        } else {
            $this->_title(Mage::helper('storelocator')->__('New Item'));
            $breadCrumb = Mage::helper('storelocator')->__('New Item');
        }

        $this->_initAction()->_addBreadcrumb($breadCrumb, $breadCrumb);

        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);

        if (!empty($data)) {
            $model->addData($data);
        }

        Mage::register('storelocator_item', $model);

        $this->renderLayout();
    }

    public function saveAction()
    {
        $redirectPath = '*/*';
        $redirectParams = array();

        // check if data sent
        $data = $this->getRequest()->getPost('main');

        if ($data) {
            /* @var $model Belvg_Storelocator_Model_Location */
            $model = Mage::getModel('storelocator/location');

            // if item exists, try to load it
            $itemId = '';

            if (isset($data['id'])) {
                $itemId = (int) $data['id'];

                $model->load($itemId);

                $data['entity_id'] = $model->getId();
                unset($data['id']);
            }

            $media = $this->getRequest()->getPost('media');

            // save image data and remove from data array
            if (isset($media['image'])) {
                $imageData = $media['image'];
                unset($media['image']);
            } else {
                $imageData = array();
            }

            // save icon data and remove from data array
            if (isset($media['icon'])) {
                $iconData = $media['icon'];
                unset($media['icon']);
            } else {
                $iconData = array();
            }

            if ($data) {
                $model->addData($data);
            }

            if ($media) {
                $model->addData($media);
            }

            try {
                $hasError = false;
                /* @var $imageHelper Belvg_Storelocator_Helper_Image */
                $imageHelper = Mage::helper('storelocator/image');
                // remove image

                if (isset($imageData['delete']) && $model->getImage()) {
                    $imageHelper->removeImage($model->getImage());
                    $model->setImage(null);
                    Mage::helper('storelocator/icon')->flushImagesCache();
                }

                // upload new image
                $imageFile = $imageHelper->uploadImage('image');
                if ($imageFile) {
                    if ($model->getImage()) {
                        $imageHelper->removeImage($model->getImage());
                    }

                    $model->setImage($imageFile);
                }

                /* @var $iconHelper Belvg_Storelocator_Helper_Icon */
                $iconHelper = Mage::helper('storelocator/icon');
                // remove image

                if (isset($iconData['delete']) && $model->getIcon()) {
                    $iconHelper->removeImage($model->getIcon());
                    $model->setIcon(null);
                    Mage::helper('storelocator/icon')->flushIconsCache();
                }

                // upload new icon
                $iconFile = $iconHelper->uploadImage('icon');
                if ($iconFile) {
                    if ($model->getIcon()) {
                        $iconHelper->removeImage($model->getIcon());
                    }

                    $model->setIcon($iconFile);
                }

                if (isset($data['in_location']) && !empty($data['in_location']) && !$data['all_product']) {
                    $model->parseProducts($data['in_location']);
                } else {
                    $model->setProductsClear(1);
                    $model->setAllProduct(1);
                }

                Mage::dispatchEvent('storelocator_location_prepare_save', array(
                    'location' => $model,
                    'request' => $this->getRequest(),
                ));

                // save the data
                if ($model->validate()) {
                    $model->save();

                    // display success message
                    $this->_getSession()->addSuccess(
                        Mage::helper('storelocator')->__('The item has been saved.')
                    );

                    // check if 'Save and Continue'
                    if ($this->getRequest()->getParam('back')) {
                        $redirectPath = '*/*/edit';
                        $redirectParams = array('id' => $model->getId());
                    }
                } else {
                    $hasError = true;
                }
            } catch (Mage_Core_Exception $e) {
                $hasError = true;
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $hasError = true;
                $this->_getSession()->addException($e, Mage::helper('storelocator')->__('An error occurred while saving the item.'));
            }

            if ($hasError) {
                $this->_getSession()->setFormData($data);
                if ($model->isObjectNew()) {
                    $redirectPath = '*/*/new';
                } else {
                    $redirectPath = '*/*/edit';
                }

                $redirectParams = array('id' => $itemId);
            }
        }

        $this->_redirect($redirectPath, $redirectParams);
    }

    public function deleteAction()
    {
        $itemId = $this->getRequest()->getParam('id');
        if ($itemId) {
            try {
                /** @var $model Belvg_Storelocator_Model_Location */
                $model = Mage::getModel('storelocator/location');
                $model->load($itemId);
                if (!$model->getId()) {
                    Mage::throwException(Mage::helper('storelocator')->__('Unable to find a item.'));
                }

                $model->delete();

                // display success message
                $this->_getSession()->addSuccess(
                    Mage::helper('storelocator')->__('The item has been deleted.')
                );
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addException($e, Mage::helper('storelocator')->__('An error occurred while deleting the item.'));
            }
        }

        // go to grid
        $this->_redirect('*/*/');
    }

    public function gridAction()
    {
        $this->_forward('index');
    }

    /**
     * Export customer grid to CSV format
     */
    public function exportCsvAction()
    {
        $fileName = 'location.csv';
        $content = $this->getLayout()->createBlock('storelocator/adminhtml_store_grid')
            ->getCsvFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Product grid for AJAX request
     */
    public function productsAction()
    {
        /* @var $model Belvg_Storelocator_Model_Location */
        $model = Mage::getModel('storelocator/location');

        $itemId = $this->getRequest()->getParam('id');

        if ($itemId) {
            $model->load($itemId);

            Mage::register('storelocator_item', $model);
        }

        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('storelocator/adminhtml_store_edit_tab_product', 'store_edit_tab_product')
                ->toHtml()
        );
    }

    /**
     * Process mass delete action
     *
     * @return void
     */
    public function massDeleteAction()
    {
        $stores_ids = $this->getRequest()->getParam('stores');
        if (!is_array($stores_ids)) {
            $this->_getSession()->addError($this->__('Please select location(s).'));
        } else {
            if (!empty($stores_ids)) {
                try {
                    Mage::getModel('storelocator/location')->getCollection()
                        ->addFieldToFilter('entity_id', array('in', $stores_ids))
                        ->walk('delete');

                    $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) have been deleted.', count($stores_ids))
                    );
                } catch (Exception $e) {
                    $this->_getSession()->addError($e->getMessage());
                }
            }
        }

        $this->_redirect('*/*/index');
    }

    /**
     * Process mass change status action
     *
     * @return void
     */
    public function massStatusAction()
    {
        $stores_ids = $this->getRequest()->getParam('stores');

        $storeId = $this->getStoreId();

        $status = (int) $this->getRequest()->getParam('status', 0);

        if (!is_array($stores_ids)) {
            $this->_getSession()->addError($this->__('Please select item(s).'));
        } else {
            if (!empty($stores_ids)) {
                try {
                    Mage::getModel('storelocator/location')->getCollection()
                        ->setDataToAll('is_active', $status)
                        ->walk('save');

                    $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) have been updated.', count($stores_ids))
                    );
                } catch (Exception $e) {
                    $this->_getSession()->addError($e->getMessage());
                }
            }
        }

        $this->_redirect('*/*/index', array('store' => $storeId));
    }

    /**
     * Return current store id
     *
     * @return int
     */
    private function getStoreId()
    {
        return (int) $this->getRequest()->getParam('store', Mage_Core_Model_App::ADMIN_STORE_ID);
    }

    /**
     * Process mass change status action
     *
     * @return void
     */
    public function massProductAction()
    {
        $products_ids = $this->getRequest()->getParam('products');

        $status = (int) $this->getRequest()->getParam('status', 0);
        $location_id = (int) $this->getRequest()->getParam('location', 0);

        if (!is_array($products_ids)) {
            $this->_getSession()->addError($this->__('Please select products(s).'));
        } else {
            if (!empty($products_ids)) {
                try {
                    /* @var $location = Belvg_Storelocator_Model_Location */
                    $location = Mage::getModel('storelocator/location')->load($location_id);

                    $location->setAllProduct(0);
                    $location->setProducts($products_ids);
                    $location->save();

                    Mage::dispatchEvent('brands_controller_store_status_update', array(
                        'location' => $location,
                        'products' => $products_ids,
                        'status' => $status,
                    ));

                    $this->_getSession()->addSuccess(
                        $this->__('Total of %d product(s) have been added.', count($products_ids))
                    );
                } catch (Exception $e) {
                    $this->_getSession()->addError($e->getMessage());
                }
            }
        }

        $this->_redirect('*/*/edit', array('id' => $location_id));
    }

    /**
     * Flush Stores Images Cache action
     */
    public function flushAction()
    {
        if (Mage::helper('storelocator/image')->flushImagesCache()) {
            $this->_getSession()->addSuccess('Cache image successfully flushed');
        } else {
            $this->_getSession()->addError('There was error during flushing cache');
        }

        if (Mage::helper('storelocator/icon')->flushIconsCache()) {
            $this->_getSession()->addSuccess('Cache icon successfully flushed');
        } else {
            $this->_getSession()->addError('There was error during flushing cache');
        }

        $this->_forward('index');
    }

    /**
     * Check ACL rules
     *
     * @return bool
     */
    protected function _isAllowed()
    {

        switch ($this->getRequest()->getActionName()) {
            case 'save':
                $result = Mage::getSingleton('admin/session')->isAllowed('store/manage/save');
                break;

            case 'delete':
                $result = Mage::getSingleton('admin/session')->isAllowed('store/manage/delete');
                break;

            case 'new':
                $result = Mage::getSingleton('admin/session')->isAllowed('store/manage/new');
                break;

            case 'edit':
                $result = Mage::getSingleton('admin/session')->isAllowed('store/manage/edit');
                break;

            default:
                $result = true;
                break;
        }

        return $result;
    }
}
