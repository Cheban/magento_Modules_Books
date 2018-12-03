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
class Belvg_Storelocator_Model_Import extends Mage_Core_Model_Abstract
{
    /**
     * Maximum size for file in bytes
     * Default value is 100M
     *
     * @var int
     */
    const MAX_FILE_SIZE = 104857600;
    /**
     * Path to import
     *
     * @var string
     */
    protected $path = 'storelocator';

    /**
     * First row
     *
     * @var array
     */
    protected $title = array();

    /**
     * Mapping first row
     *
     * @var array
     */
    protected $headreMap = array();

    /**
     * Raw data for import
     *
     * @var mixed
     */
    protected $data;

    /**
     * Array of allowed file extensions
     *
     * @var array
     */
    protected $allowedExtensions = array('csv');

    /**
     * Import items from file.
     *
     * @param string $scope
     *
     * @return bool
     */
    public function importItems($scope = 'file_import')
    {
        try {
            $this->getDataCsv($scope);

            if (empty($this->data)) {
                Mage::getSingleton('core/session')->addError(Mage::helper('storelocator')->__('No data in file.'));

                return false;
            }

            /**
             * @var $collection Belvg_Storelocator_Model_Resource_Location_Collection
             */
            $collection = Mage::getModel('storelocator/location')->getCollection();

            foreach ($this->data as $row) {
                $model = Mage::getModel('storelocator/location');

                $this->setValues($model, $row);

                $errors = $model->validate(true);

                if (!is_array($errors)) {
                    $model->setStores(array($this->getStoreId()));
                    $collection->addItem($model);
                    sleep(2);
                } else {
                    foreach ($errors as $error) {
                        Mage::getSingleton('core/session')->addError(Mage::helper('storelocator')->__('Invalidate data in row'.$error));
                    }

                    return false;
                }

                $collection->walk('save');
            }
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError($e->getMessage());

            return false;
        }

        return true;
    }

    /**
     * @param $scope
     *
     * @return bool|string
     */
    protected function getDataCsv($scope)
    {
        $fullPath = $this->uploadFile($scope);

        $fileProvider = new Varien_File_Csv();

        $data = $fileProvider->getData($fullPath);

        if (is_array($data)) {
            $this->data = $data;
            $this->setFirstRow();
            unset($fullPath, $fileProvider, $data);
        } else {
            Mage::throwException(Mage::helper('storelocator')->__('No data in file.'));
        }
    }

    /**
     * Upload file and return uploaded file name or false.
     *
     * @throws Mage_Core_Exception
     *
     * @param string $scope the request key for file
     *
     * @return bool|string
     */
    public function uploadFile($scope)
    {
        $adapter = new Zend_File_Transfer_Adapter_Http();
        $adapter->addValidator('Size', true, self::MAX_FILE_SIZE);

        if ($adapter->isUploaded($scope)) {
            // validate
            if (!$adapter->isValid($scope)) {
                Mage::getSingleton('core/session')->addError(Mage::helper('storelocator')->__('Uploaded file is not valid'));
            }

            $upload = new Varien_File_Uploader($scope);
            $upload->setAllowCreateFolders(true);
            $upload->setAllowedExtensions($this->allowedExtensions);
            $upload->setAllowRenameFiles(true);
            $upload->setFilesDispersion(false);

            if ($upload->save($this->getBaseDir())) {
                return $this->getBaseDir().DS.$upload->getUploadedFileName();
            }
        }

        return false;
    }

    /**
     * Return the base directory for file.
     *
     * @return string
     */
    public function getBaseDir()
    {
        return Mage::getBaseDir('var').DS.$this->path;
    }

    /**
     * Set header row
     */
    protected function setFirstRow()
    {
        if (is_array($this->data)) {
            $this->title = array_shift($this->data);
        } else {
            Mage::getSingleton('core/session')->addError(Mage::helper('storelocator')->__('In the import file is missing header row.'));
        }
    }

    /**
     * Set model data
     *
     * @param Mage_Core_Model_Abstract $model
     * @param $row
     */
    protected function setValues(Mage_Core_Model_Abstract $model, $row)
    {
        foreach ($row as $key => $value) {
            $model->setData($this->getKeyData($key), $value);
        }
    }

    /**
     * Return name key
     *
     * @param $key
     *
     * @return mixed
     */
    protected function getKeyData($key)
    {
        if (empty($this->headreMap)) {
            $this->headreMap = array_flip(Mage::helper('storelocator')->getHeaderFile());
        }

        if (!isset($this->headreMap[$this->title[$key]])) {
            Mage::throwException(Mage::helper('storelocator')->__('In the import file is missing header row.'));
        }

        return $this->headreMap[$this->title[$key]];
    }

    /**
     * Return current store id
     *
     * @return int
     */
    protected function getStoreId()
    {
        return (int) Mage::app()->getRequest()->getParam('store', Mage_Core_Model_App::ADMIN_STORE_ID);
    }
}
