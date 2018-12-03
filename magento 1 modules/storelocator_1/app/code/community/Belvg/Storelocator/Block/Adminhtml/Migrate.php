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
class Belvg_Storelocator_Block_Adminhtml_Migrate extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare form.
     */
    public function __construct()
    {
        parent::__construct();

        $this->setTemplate('belvg/storelocator/migrate.phtml');
        $this->setId('migrate');
    }

    /**
     * Url for import.
     *
     * @return string
     */
    public function getMigrateUrl()
    {
        return $this->getUrl('*/*/run');
    }

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset(
            'migrate',
            array(
                'legend' => Mage::helper('core')->__('Rows for migrate: %s', $this->getAllRows())
            )
        );

        $fieldset->addField('send', 'submit', array(
            'value' => Mage::helper('storelocator')->__('Run'),
        ));

        $this->setForm($form);
    }

    /**
     * Return count rows for migrate.
     *
     * @return int
     */
    public function getAllRows()
    {
        /**
         * @var $collection Belvg_Storelocator_Model_Resource_Migrate_Collection
         */
        $count = 0;

        if ($this->helper('storelocator/migrate')->canMigrate()) {
            $collection = Mage::getModel('storelocator/migrate')->getCollection();
            $count = (int) count($collection->getAllIds());
        }

        return $count;
    }
}
