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
class Belvg_Storelocator_Block_Adminhtml_Store_Edit_Tab_Main extends Mage_Adminhtml_Block_Widget_Form implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('storelocator')->__('Store Info');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('storelocator')->__('Store Info');
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
        return false;
    }

    /**
     * Prepare form elements for tab
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        /**
         * @var $model Belvg_Storelocator_Model_Location
         */
        $model = Mage::helper('storelocator')->getItemInstance();

        $isElementDisabled = false;

        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('store_main_');
        $form->setFieldNameSuffix('main');

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => Mage::helper('storelocator')->__('Item Info'),
        ));

        if ($model->getId()) {
            $fieldset->addField('entity_id', 'hidden', array(
                'name' => 'id',
            ));
        }

        $fieldset->addField('title', 'text', array(
            'name' => 'title',
            'label' => Mage::helper('storelocator')->__('Title'),
            'title' => Mage::helper('storelocator')->__('Title'),
            'required' => true,
            'class' => 'required-entry validate-no-html-tags validate-length maximum-length-100 minimum-length-1 validate-no-space-begin validate-no-space-end',
            'disabled' => $isElementDisabled,
        ));

        $fieldset->addField('description', 'textarea', array(
            'name' => 'description',
            'label' => Mage::helper('storelocator')->__('Description'),
            'title' => Mage::helper('storelocator')->__('Description'),
            'required' => true,
            'class' => 'required-entry validate-no-html-tags validate-length maximum-length-250 minimum-length-1',
            'disabled' => $isElementDisabled,
        ));

        $fieldset->addField('address', 'text', array(
            'name' => 'address',
            'label' => Mage::helper('storelocator')->__('Address'),
            'title' => Mage::helper('storelocator')->__('Address'),
            'required' => true,
            'class' => 'required-entry validate-no-html-tags validate-length maximum-length-250 minimum-length-1 validate-no-space-begin validate-no-space-end',
            'disabled' => $isElementDisabled,
        ));

        $fieldset->addField('website', 'text', array(
            'name' => 'website',
            'label' => Mage::helper('storelocator')->__('Website Url'),
            'title' => Mage::helper('storelocator')->__('Website Url'),
            'class' => 'validate-clean-url-http',
            'disabled' => $isElementDisabled,
        ));

        $fieldset->addField('phone', 'text', array(
            'name' => 'phone',
            'label' => Mage::helper('storelocator')->__('Phone'),
            'title' => Mage::helper('storelocator')->__('Phone'),
            'class' => 'validate-no-space-begin validate-no-space-end',
            'disabled' => $isElementDisabled,
        ));

        /**
         * Check is single store mode
         */
        if (!Mage::app()->isSingleStoreMode()) {
            $fieldset->addField('store_id', 'multiselect', array(
                'name' => 'stores[]',
                'label' => Mage::helper('storelocator')->__('Store View'),
                'title' => Mage::helper('storelocator')->__('Store View'),
                'required' => true,
                'class' => 'required-entry',
                'values' => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
                'disabled' => $isElementDisabled,
            ));
        } else {
            $fieldset->addField('store_id', 'hidden', array(
                'name' => 'stores[]',
                'value' => Mage::app()->getStore(true)->getId(),
            ));
            $model->setStoreId(Mage::app()->getStore(true)->getId());
        }

        $fieldset->addField('favorite', 'select', array(
            'name' => 'favorite',
            'label' => Mage::helper('storelocator')->__('Mark as favorite'),
            'title' => Mage::helper('storelocator')->__('Mark as favorite'),
            'value' => '0',
            'values' => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray(),
            'disabled' => $isElementDisabled,
        ));

        $fieldset->addField('is_active', 'select', array(
            'name' => 'is_active',
            'label' => Mage::helper('storelocator')->__('Enable/Disable'),
            'title' => Mage::helper('storelocator')->__('Enable/Disable'),
            'value' => '0',
            'values' => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray(),
            'disabled' => $isElementDisabled,
        ));

        $fieldset->addField('all_product', 'select', array(
            'name' => 'all_product',
            'label' => Mage::helper('storelocator')->__('Show for All Products'),
            'title' => Mage::helper('storelocator')->__('Show for All Products'),
            'value' => '1',
            'onchange' => "

                    if($('store_main_all_product').getValue() == 1) {
                        $('page_tabs_product_section').up('li').setStyle({
                            display: 'none'
                        });
                    } else {
                        $('page_tabs_product_section').up('li').setStyle({
                            display: 'block'
                        });
                    }
                ",
            'values' => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray(),
            'disabled' => $isElementDisabled,
        ));

        $fieldset->addField('latitude', 'text', array(
            'name' => 'latitude',
            'label' => Mage::helper('storelocator')->__('Latitude'),
            'title' => Mage::helper('storelocator')->__('Latitude'),
            'required' => !$model->isObjectNew(),
            'class' => 'validate-number validate-no-space-begin validate-no-space-end',
            'disabled' => $isElementDisabled,
        ));

        $fieldset->addField('longitude', 'text', array(
            'name' => 'longitude',
            'label' => Mage::helper('storelocator')->__('Longitude'),
            'title' => Mage::helper('storelocator')->__('Longitude'),
            'required' => !$model->isObjectNew(),
            'class' => 'validate-number validate-no-space-begin validate-no-space-end',
            'disabled' => $isElementDisabled,
        ));

        $fieldset->addField('in_location', 'hidden', array(
            'name' => 'in_location',
            'disabled' => $isElementDisabled,
        ));

        if (!$model->isObjectNew()) {
            $fieldset->addField('updatelatlng', 'checkbox', array(
                'label' => Mage::helper('storelocator')->__('Update Latitude/Longitude'),
                'title' => Mage::helper('storelocator')->__('Update Latitude/Longitude'),
                'name' => 'updatelatlng',
                'onclick' => "

                if($('store_main_updatelatlng').readAttribute('value') == 1) {
                    $('store_main_updatelatlng').writeAttribute('value','0');
                } else {
                    $('store_main_updatelatlng').writeAttribute('value','1');
                }
            ",
            ));
        }

        Mage::dispatchEvent('adminhtml_store_edit_tab_main_prepare_form', array('form' => $form));

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
