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
class Belvg_Storelocator_Helper_Icon extends Belvg_Storelocator_Helper_Image
{
    /**
     * Media path to extension imahes
     *
     * @var string
     */
    protected $media_path = 'stories/icons';

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
        if (!$item->getIcon()) {
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

        $imageFile = $item->getIcon();
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
            $image->keepTransparency(true);
            $image->quality(100);
            $image->resize($width, $height);
            $image->save($cacheDir.DS.$imageFile);

            return $cacheUrl.$imageFile;
        } catch (Exception $e) {
            Mage::logException($e);

            return false;
        }
    }

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
     * Removes folder with cached images
     *
     * @return boolean
     */
    public function flushIconsCache()
    {
        $cacheDir = $this->getBaseDir().DS.'cache'.DS;
        $io = new Varien_Io_File();
        if ($io->fileExists($cacheDir, false)) {
            return $io->rmdir($cacheDir, true);
        }

        return true;
    }
}
