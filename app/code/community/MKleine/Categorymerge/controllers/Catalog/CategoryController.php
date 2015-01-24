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

require_once Mage::getModuleDir('controllers', 'Mage_Adminhtml') . DS . 'Catalog' . DS . 'CategoryController.php';

class MKleine_Categorymerge_Catalog_CategoryController extends Mage_Adminhtml_Catalog_CategoryController
{
    /**
     * Product grid for AJAX request
     */
    public function mergegridAction()
    {
        if (!$category = $this->_initCategory(true)) {
            return;
        }
        /** @var MKleine_Categorymerge_Block_Catalog_Category_Tab_Categorymerge $block */
        $block = $this->getLayout()->createBlock(
            'mk_categorymerge/catalog_category_tab_categorymerge',
            'category.product.categorymerge'
        );

        $this->getResponse()->setBody($block->toHtml());
    }

    public function mergeAction()
    {
        /** @var $mergeModel MKleine_Categorymerge_Model_Merge */
        $mergeModel = Mage::getModel('mk_categorymerge/merge');

        $source       = (int)$this->getRequest()->getParam('source');
        $target       = (int)$this->getRequest()->getParam('target');
        $deleteSource = $this->getRequest()->getParam('delete') ? true : false;

        if (!empty($source) && !empty($target) && $mergeModel->mergeCategories($source, $target, $deleteSource)) {
            Mage::getSingleton('core/session')->addSuccess($this->__('Your categories have been merged successfully'));
        } else {
            Mage::getSingleton('core/session')->addError($this->__('Category merge failed'));
        }

        $this->_forward('edit', null, null, array('id' => $target));
    }
}
