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
class Belvg_Storelocator_Helper_Search extends Mage_CatalogSearch_Helper_Data
{
    const QUERY_VAR_NAME = 'psearch';

    /**
     * Query object
     *
     * @var Mage_CatalogSearch_Model_Query
     */
    protected $query;

    /**
     * Query string
     *
     * @var string
     */
    protected $queryText;

    /**
     * Note messages
     *
     * @var array
     */
    protected $_messages = array();

    /**
     * Is a maximum length cut
     *
     * @var bool
     */
    protected $_isMaxLength = false;

    /**
     * Search engine model
     *
     * @var Mage_CatalogSearch_Model_Resource_Fulltext_Engine
     */
    protected $_engine;

    /**
     * Retrieve query model object
     *
     * @return Mage_CatalogSearch_Model_Query
     */
    public function getQuery()
    {
        if (!$this->query) {
            if ($this->getQueryText()) {
                $this->query = Mage::getModel('storelocator/query')
                    ->loadByQuery($this->getQueryText());
                if (!$this->query->getId()) {
                    $this->query->setQueryText($this->getQueryText());
                }
            }
        }

        return $this->query;
    }

    /**
     * Retrieve search query text
     *
     * @return string
     */
    public function getQueryText()
    {
        if (!isset($this->queryText)) {
            $this->queryText = $this->_getRequest()->getParam($this->getQueryParamName());
            if ($this->queryText === null) {
                $this->queryText = '';
            }

            if ($this->queryText) {
                /* @var $stringHelper Mage_Core_Helper_String */
                $stringHelper = Mage::helper('core/string');
                $this->queryText = is_array($this->queryText) ? ''
                    : $stringHelper->cleanString(trim($this->queryText));

                $maxQueryLength = $this->getMaxQueryLength();
                if ($maxQueryLength !== '' && $stringHelper->strlen($this->queryText) > $maxQueryLength) {
                    $this->queryText = $stringHelper->substr($this->queryText, 0, $maxQueryLength);
                    $this->_isMaxLength = true;
                }
            }
        }

        return $this->queryText;
    }

    /**
     * Retrieve search query parameter name
     *
     * @return string
     */
    public function getQueryParamName()
    {
        return self::QUERY_VAR_NAME;
    }

    /**
     * Get current search engine resource model
     *
     * @return object
     */
    public function getEngine()
    {
        if (!$this->_engine) {
            $this->_engine = Mage::getResourceSingleton('storelocator/fulltext_engine');
        }

        return $this->_engine;
    }
}
