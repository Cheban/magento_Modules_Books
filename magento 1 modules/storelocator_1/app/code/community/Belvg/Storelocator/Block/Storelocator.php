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
class Belvg_Storelocator_Block_Storelocator extends Belvg_Storelocator_Block_Abstract
{
    /**
     * Default pager block name
     *
     * @var string
     */
    protected $defaultPagerBlock = 'storelocator_pager';

    /**
     * Prepare render
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _beforeToHtml()
    {
        /* @var $_pager Mage_Page_Block_Html_Pager */
        $_pager = $this->getPagerBlock();

        //Set limit
        $_pager->setLimit($this->getLimit());

        $_collection = $this->getCollection();

        // set collection to pager
        $_pager->setCollection($_collection);

        /* @var $_collection Belvg_Storelocator_Model_Resource_Location_Collection */
        $_collection = $_pager->getCollection()->load();

        $this->setCollection($_collection);

        Mage::dispatchEvent('storelocator_collection', array(
            'object' => $this,
            'collection' => $this->getCollection(),
        ));

        return parent::_beforeToHtml();
    }

    /**
     * Retrieve Pager block
     *
     * @return Mage_Page_Block_Html_Pager
     */
    public function getPagerBlock()
    {
        return $this->getChild($this->defaultPagerBlock);
    }
}
