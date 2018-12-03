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
class Belvg_Storelocator_Helper_Image extends Mage_Core_Helper_Abstract
{
    /**
     * Maximum size for image in bytes
     * Default value is 1M
     *
     * @var int
     */
    const MAX_FILE_SIZE = 1048576;
    /**
     * Minimum image height in pixels
     *
     * @var int
     */
    const MIN_HEIGHT = 20;
    /**
     * Maximum image height in pixels
     *
     * @var int
     */
    const MAX_HEIGHT = 1000;
    /**
     * Minimum image width in pixels
     *
     * @var int
     */
    const MIN_WIDTH = 20;
    /**
     * Maximum image width in pixels
     *
     * @var int
     */
    const MAX_WIDTH = 1000;
    /**
     * Array of image size limitation
     *
     * @var array
     */
    protected $imageSize = array(
        'minheight' => self::MIN_HEIGHT,
        'minwidth' => self::MIN_WIDTH,
        'maxheight' => self::MAX_HEIGHT,
        'maxwidth' => self::MAX_WIDTH,
    );
    /**
     * Media path to extension imahes
     *
     * @var string
     */
    protected $media_path = 'stories';
    /**
     * Array of allowed file extensions
     *
     * @var array
     */
    protected $allowedExtensions = array('jpg', 'gif', 'png');

    /**
     * Remove item image by image filename
     *
     * @param string $imageFile
     *
     * @return bool
     */
    public function removeImage($imageFile)
    {
        $io = new Varien_Io_File();
        $io->open(array('path' => $this->getBaseDir()));
        if ($io->fileExists($imageFile)) {
            return $io->rm($imageFile);
        }

        return false;
    }

    /**
     * Return the base media directory for Item images
     *
     * @return string
     */
    public function getBaseDir()
    {
        return Mage::getBaseDir('media').DS.$this->media_path;
    }

    /**
     * Upload image and return uploaded image file name or false
     *
     * @throws Mage_Core_Exception
     *
     * @param string $scope the request key for file
     *
     * @return bool|string
     */
    public function uploadImage($scope)
    {
        $adapter = new Zend_File_Transfer_Adapter_Http();
        $adapter->addValidator('ImageSize', true, $this->imageSize);
        $adapter->addValidator('Size', true, self::MAX_FILE_SIZE);
        if ($adapter->isUploaded($scope)) {
            // validate image
            if (!$adapter->isValid($scope)) {
                foreach ($adapter->getMessages() as $error) {
                    Mage::throwException($error);
                }
            }

            $upload = new Varien_File_Uploader($scope);
            $upload->setAllowCreateFolders(true);
            $upload->setAllowedExtensions($this->allowedExtensions);
            $upload->setAllowRenameFiles(true);
            $upload->setFilesDispersion(false);
            if ($upload->save($this->getBaseDir())) {
                return $upload->getUploadedFileName();
            }
        }

        return false;
    }

    /**
     * Return URL for resized Item Image
     *
     * @param Belvg_Storelocator_Model_Location $item
     * @param integer                           $width
     * @param integer                           $height
     *
     * @return bool|string
     */
    public function resize(Belvg_Storelocator_Model_Location $item, $width, $height = null)
    {
        if (!$item->getImage()) {
            return false;
        }

        if ($width < self::MIN_WIDTH || $width > self::MAX_WIDTH) {
            return false;
        }

        $width = (int) $width;

        if (!is_null($height)) {
            if ($height < self::MIN_HEIGHT || $height > self::MAX_HEIGHT) {
                return false;
            }

            $height = (int) $height;
        }

        $imageFile = $item->getImage();
        $cacheDir = $this->getBaseDir().DS.'cache'.DS.$width;
        $cacheUrl = $this->getBaseUrl().'/cache/'.$width.'/';

        $io = new Varien_Io_File();
        $io->checkAndCreateFolder($cacheDir);
        $io->open(array('path' => $cacheDir));
        if ($io->fileExists($imageFile)) {
            return $cacheUrl.$imageFile;
        }

        try {
            $image = new Varien_Image($this->getBaseDir().DS.$imageFile);
            $image->resize($width, $height);
            $image->save($cacheDir.DS.$imageFile);

            return $cacheUrl.$imageFile;
        } catch (Exception $e) {
            Mage::logException($e);

            return false;
        }
    }

    /**
     * Return the Base URL for Item images
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return Mage::getBaseUrl('media').$this->media_path;
    }

    /**
     * Removes folder with cached images
     *
     * @return boolean
     */
    public function flushImagesCache()
    {
        $cacheDir = $this->getBaseDir().DS.'cache'.DS;
        $io = new Varien_Io_File();
        if ($io->fileExists($cacheDir, false)) {
            return $io->rmdir($cacheDir, true);
        }

        return true;
    }
}
