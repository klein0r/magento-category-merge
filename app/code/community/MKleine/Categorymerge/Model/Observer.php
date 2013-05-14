<?php
/**
 * MKleine - (c) Matthias Kleine
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@mkleine.de so we can send you a copy immediately.
 *
 * @category    MKleine
 * @package     MKleine_Categorymerge
 * @copyright   Copyright (c) 2013 Matthias Kleine (http://mkleine.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MKleine_Categorymerge_Model_Observer extends Mage_Core_Model_Abstract
{
    /**
     * Adds a new tab to the
     *
     * @param $observer Varien_Event_Observer
     */
    public function adminhtml_catalog_category_tabs($observer)
    {
        /** @var $tabs Mage_Adminhtml_Block_Catalog_Category_Tabs */
        $tabs = $observer->getTabs();

        if (Mage::helper('mk_categorymerge')->getCategory()->getId()) {
            $tabs->addTab('category_merge', array(
                'label'     => Mage::helper('mk_categorymerge')->__('Category Merge'),
                'content'   => $tabs->getLayout()->createBlock(
                    'mk_categorymerge/catalog_category_tab_categorymerge',
                    'category.product.categorymerge'
                )->toHtml(),
            ));
        }
    }

    /**
     * @param $observer Varien_Event_Observer
     */
    public function catalog_category_save_after($observer)
    {
        $category = $observer->getCategory();

        if ($category) {

        }
    }
}