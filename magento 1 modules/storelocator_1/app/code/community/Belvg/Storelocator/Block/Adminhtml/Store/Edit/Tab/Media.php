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
class Belvg_Storelocator_Block_Adminhtml_Store_Edit_Tab_Media extends Mage_Adminhtml_Block_Widget_Form implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('storelocator')->__('Store Media');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('storelocator')->__('Store Media');
    }

    /**
     * Returns status flag about this tab can be showen or not
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
     * Prepare form elements
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $isElementDisabled = false;

        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('store_media_');
        $form->setFieldNameSuffix('media');

        $model = Mage::helper('storelocator')->getItemInstance();

        $fieldset = $form->addFieldset('image_fieldset', array(
            'legend' => Mage::helper('storelocator')->__('Image Thumbnail'),
            'class' => 'fieldset-wide',
        ));

        $this->_addElementTypes($fieldset);

        $fieldset->addField('image', 'image', array(
            'name' => 'image',
            'label' => Mage::helper('storelocator')->__('Image'),
            'title' => Mage::helper('storelocator')->__('Image'),
            'required' => false,
            'disabled' => $isElementDisabled,
        ));

        $fieldset = $form->addFieldset('icon_fieldset', array(
            'legend' => Mage::helper('storelocator')->__('Icon Thumbnail'),
            'class' => 'fieldset-wide',
        ));

        $this->_addElementTypes($fieldset);

        $fieldset->addField('icon', 'icon', array(
            'name' => 'icon',
            'label' => Mage::helper('storelocator')->__('Icon'),
            'title' => Mage::helper('storelocator')->__('Icon'),
            'required' => false,
            'disabled' => $isElementDisabled,
        ));

        Mage::dispatchEvent('adminhtml_store_edit_tab_media_prepare_form', array('form' => $form));

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Retrieve predefined additional element types
     *
     * @return array
     */
    protected function _getAdditionalElementTypes()
    {
        return array(
            'image' => Mage::getConfig()->getBlockClassName('storelocator/adminhtml_store_edit_form_element_image'),
            'icon' => Mage::getConfig()->getBlockClassName('storelocator/adminhtml_store_edit_form_element_icon'),
        );
    }
}
