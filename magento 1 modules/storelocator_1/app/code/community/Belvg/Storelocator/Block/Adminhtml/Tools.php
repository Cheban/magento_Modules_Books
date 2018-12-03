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
class Belvg_Storelocator_Block_Adminhtml_Tools extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare form.
     */
    public function __construct()
    {
        parent::__construct();

        $this->setTemplate('belvg/storelocator/import.phtml');
        $this->setId('import');
    }

    /**
     * Url for import.
     *
     * @return string
     */
    public function getImportUrl()
    {
        return $this->getUrl('*/*/import');
    }

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('import', array('legend' => Mage::helper('core')->__('Import Settings')));

        $fieldset->addField('file_import', 'file', array(
            'label' => Mage::helper('storelocator')->__('Select File'),
            'required' => true,
            'name' => 'file_import',
            'class' => 'required-entry equired-file',
        ));

        $fieldset->addField('send', 'submit', array(
            'value' => Mage::helper('storelocator')->__('Import'),
        ));

        $this->setForm($form);
    }
}
