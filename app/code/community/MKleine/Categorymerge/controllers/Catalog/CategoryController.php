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
 * @copyright   Copyright (c) 2015 Zookal Pty Ltd Cyrill Schumacher (https://github.com/zookal)
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
        $this->loadLayout(false);
        $this->renderLayout();
    }

    public function mergeAction()
    {
        /** @var $mergeModel MKleine_Categorymerge_Model_Merge */
        $mergeModel = Mage::getModel('mk_categorymerge/merge');

        $source       = (int)$this->getRequest()->getParam('source', 0);
        $target       = (int)$this->getRequest()->getParam('target', 0);
        $deleteSource = (int)$this->getRequest()->getParam('delete', 0) === 1;

        $mergeModel->setSource($source);
        $mergeModel->setTarget($target);
        if ($source > 0 && $target > 0 && $mergeModel->mergeCategories($deleteSource)) {
            $this->_getSession()->addSuccess($this->__(
                'Category %s and its children has been merged successfully into %s',
                $mergeModel->getSource()->getName(),
                $mergeModel->getTarget()->getName()
            ));
        } else {
            $this->_getSession()->addError($this->__('Category merge failed'));
        }
        $this->_redirect('*/*/edit', array('id' => $target));
    }
}
