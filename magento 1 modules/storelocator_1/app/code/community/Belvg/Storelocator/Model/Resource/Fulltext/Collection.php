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
class Belvg_Storelocator_Model_Resource_Fulltext_Collection extends Mage_CatalogSearch_Model_Resource_Fulltext_Collection
{
    private $isGetFoundIds = false;
    
    /**
     * Add search query filter
     *
     * @param string $query
     *
     * @return Mage_CatalogSearch_Model_Resource_Fulltext_Collection
     */
    public function addSearchFilter($query)
    {
        if($this->isGetFoundIds){
            return $this;
        }
        
        Mage::getSingleton('storelocator/fulltext')->prepareResult();
        
        $this->getSelect()->joinInner(
            array('search_result' => $this->getTable('catalogsearch/result')),
            $this->getConnection()->quoteInto(
                'search_result.product_id=e.entity_id AND search_result.query_id=?',
                $this->_getQuery()->getId()
            ),
            array('relevance' => 'relevance')
        );
        
        return $this;
    }
    
    /**
     * Get found products ids
     *
     * @return array
     */
    public function getFoundIds()
    {
        if (is_null($this->_foundData)) {
            $preparedResult = Mage::getSingleton('storelocator/fulltext');
            
            $preparedResult->prepareResult();
            
            $this->_foundData = $preparedResult->getResource()->getFoundData();
        }
        
        if (isset($this->_orders[self::RELEVANCE_ORDER_NAME])) {
            $this->_resortFoundDataByRelevance();
        }
        
        $this->isGetFoundIds = true;
        return array_keys($this->_foundData);
    }

    /**
     * Retrieve query model object
     *
     * @return Mage_CatalogSearch_Model_Query
     */
    protected function _getQuery()
    {
        return Mage::helper('storelocator/search')->getQuery();
    }
}
