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
class Belvg_Storelocator_Block_Adminhtml_Store_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Init Grid default properties
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('stories_list_grid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        //$this->setUseAjax(TRUE);
    }

    /**
     * Return row URL for js event handlers.
     *
     * @param $row
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    /**
     * Grid url getter
     *
     * @return string current grid url
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }

    /**
     * Prepare collection for Grid
     *
     * @return Belvg_Storelocator_Block_Adminhtml_Store_Grid
     */
    protected function _prepareCollection()
    {
        /* @var $collection Belvg_Storelocator_Model_Resource_Location_Collection */
        $collection = Mage::getModel('storelocator/location')->getResourceCollection();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare Grid columns
     *
     * @return Mage_Adminhtml_Block_Catalog_Search_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header' => Mage::helper('storelocator')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'entity_id',
        ));

        $this->addColumn('title', array(
            'header' => Mage::helper('storelocator')->__('Title'),
            'index' => 'title',
        ));

        $this->addColumn('address', array(
            'header' => Mage::helper('storelocator')->__('Address'),
            'index' => 'address',
        ));

        /**
         * Check is single store mode
         */
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header' => Mage::helper('storelocator')->__('Store View'),
                'index' => 'store_id',
                'type' => 'store',
                'store_all' => true,
                'store_view' => true,
                'sortable' => false,
                'filter_condition_callback' => array(
                    $this,
                    '_filterStoreCondition',
                ),
            ));
        }

        $this->addColumn('favorite', array(
            'header' => Mage::helper('storelocator')->__('Mark as favorite?'),
            'index' => 'favorite',
            'type' => 'options',
            'align' => 'center',
            'options' => array(
                0 => Mage::helper('adminhtml')->__('No'),
                1 => Mage::helper('adminhtml')->__('Yes'),
            ),
        ));

        $this->addColumn('active', array(
            'header' => Mage::helper('storelocator')->__('Status'),
            'index' => 'is_active',
            'type' => 'options',
            'align' => 'center',
            'options' => array(
                0 => Mage::helper('adminhtml')->__('Disabled'),
                1 => Mage::helper('adminhtml')->__('Enabled'),
            ),
        ));

        $this->addColumn(
            'action',
            array(
                'header' => Mage::helper('storelocator')->__('Action'),
                'width' => '100px',
                'type' => 'action',
                'getter' => 'getId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('storelocator')->__('Edit'),
                        'url' => array('base' => '*/*/edit'),
                        'field' => 'id',
                    ),
                ),
                'filter' => false,
                'sortable' => false,
                'index' => 'entity_id',
            )
        );

        $this->addExportType('*/*/exportCsv', Mage::helper('customer')->__('CSV'));

        return parent::_prepareColumns();
    }

    /**
     * Write item data to csv export file
     *
     * @param Varien_Object  $item
     * @param Varien_Io_File $adapter
     */
    protected function _exportCsvItem(Varien_Object $item, Varien_Io_File $adapter)
    {
        $row = array();

        foreach ($this->_getExportHeaders() as $key => $name) {
            $row[] = $item->getData($key);
        }

        $adapter->streamWriteCsv($row);
    }

    /**
     * Retrieve Headers row array for Export
     *
     * @return array
     */
    protected function _getExportHeaders()
    {
        return Mage::helper('storelocator')->getHeaderFile();
    }

    /**
     * Massive operation
     *
     * @return Belvg_Storelocator_Block_Adminhtml_Store_Grid|Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('stores');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('adminhtml')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('storelocator')->__('Are you sure?'),
        ));

        $statuses = Mage::getSingleton('adminhtml/system_config_source_enabledisable')->toOptionArray();

        array_unshift($statuses, array('label' => '', 'value' => ''));

        $this->getMassactionBlock()->addItem('status', array(
            'label' => Mage::helper('storelocator')->__('Change status'),
            'url' => $this->getUrl('*/*/massStatus', array('_current' => true)),
            'additional' => array(
                'visibility' => array(
                    'name' => 'status',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('storelocator')->__('Status'),
                    'values' => $statuses,
                ),
            ),
        ));

        Mage::dispatchEvent('adminhtml_store_grid_prepare_massaction', array('block' => $this));

        return $this;
    }

    /**
     * Filter collection by store.
     *
     * @param $collection
     * @param $column
     */
    protected function _filterStoreCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }

        $this->getCollection()->addStoreFilter($value);
    }
}
