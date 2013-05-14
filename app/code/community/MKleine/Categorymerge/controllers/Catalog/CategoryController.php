<?php
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
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('mk_categorymerge/catalog_category_tab_categorymerge', 'category.product.categorymerge')
                ->toHtml()
        );
    }

    public function mergeAction()
    {
        /** @var $mergeModel MKleine_Categorymerge_Model_Merge */
        $mergeModel = Mage::getModel('mk_categorymerge/merge');

        $source = (int)$this->getRequest()->getParam('source');
        $target = (int)$this->getRequest()->getParam('target');

        if( !empty($source) && !empty($target) && $mergeModel->mergeCategories($source, $target) ) {
            Mage::getSingleton('core/session')->addSuccess($this->__('Your categories have been merged successfully'));
        }
        else {
            Mage::getSingleton('core/session')->addError($this->__('Category merge failed'));
        }

        $this->_forward('edit', null, null, array( 'id' => $target ));

        //Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getUrl('*/*/edit', array( 'id' => $target )));

    }

}