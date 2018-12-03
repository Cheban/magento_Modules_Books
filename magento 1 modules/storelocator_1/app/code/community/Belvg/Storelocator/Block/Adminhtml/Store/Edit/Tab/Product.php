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
class Belvg_Storelocator_Block_Adminhtml_Store_Edit_Tab_Product extends Mage_Adminhtml_Block_Widget_Grid implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Init Grid default properties
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('product_section');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * Grid url getter.
     *
     * @return string current grid url
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/products', array('_current' => true));
    }

    /**
     * Return products.
     *
     * @return string
     */
    public function getProductsJson()
    {
        $products = $this->_getSelectedProducts();
        if (!empty($products)) {
            return Mage::helper('core')->jsonEncode($products);
        }

        return '{}';
    }

    /**
     * Return products attached to the location.
     *
     * @return mixed
     */
    protected function _getSelectedProducts()
    {
        $products = $this->getRequest()->getPost('products');

        if (is_null($products) && $this->getLocation() instanceof Varien_Object && !$this->getLocation()->isObjectNew()) {
            return $this->getLocation()->getProducts();
        }

        return $products;
    }

    /**
     * Return current location.
     *
     * @return mixed
     */
    public function getLocation()
    {
        return Mage::registry('storelocator_item');
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('storelocator')->__('Products');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('storelocator')->__('Products');
    }

    /**
     * Returns status flag about this tab can be shown or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        /**
         * @var $model Belvg_Storelocator_Model_Location
         */
        $model = Mage::helper('storelocator')->getItemInstance();

        if ($model->getAllProduct()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Added for current location products.
     *
     * @param $column
     *
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_location') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }

            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in' => $productIds));
            } elseif (!empty($productIds)) {
                $this->getCollection()->addFieldToFilter('entity_id', array('nin' => $productIds));
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }

        return $this;
    }

    /**
     * Prepare collection for Grid
     *
     * @return Belvg_Storelocator_Block_Adminhtml_Store_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('sku')
            ->addStoreFilter($this->getRequest()->getParam('store'));

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
        $this->addColumn('in_location', array(
            'header_css_class' => 'a-center',
            'type' => 'checkbox',
            'name' => 'in_location',
            'values' => $this->_getSelectedProducts(),
            'align' => 'center',
            'index' => 'entity_id',
        ));

        $this->addColumn('entity_id', array(
            'header' => Mage::helper('catalog')->__('ID'),
            'sortable' => true,
            'width' => '60',
            'index' => 'entity_id',
        ));

        $this->addColumn('name', array(
            'header' => Mage::helper('catalog')->__('Name'),
            'index' => 'name',
        ));

        $this->addColumn('sku', array(
            'header' => Mage::helper('catalog')->__('SKU'),
            'width' => '80',
            'index' => 'sku',
        ));

        return parent::_prepareColumns();
    }
}
