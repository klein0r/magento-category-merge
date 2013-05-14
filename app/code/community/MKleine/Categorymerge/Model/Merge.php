<?php

class MKleine_Categorymerge_Model_Merge extends Mage_Core_Model_Abstract
{
    /**
     * @param $sourceId Source category Id
     * @param $targetId Target category Id
     * @return bool
     */
    public function mergeCategories($sourceId, $targetId)
    {
        try {
            $sourceCategory = $this->getModel()->load($sourceId);
            $targetCategory = $this->getModel()->load($targetId);

            $sourceItems = $sourceCategory->getProductCollection()->setOrder('position', 'asc')->getAllIds();

            foreach ($sourceItems as $itemId) {
                $product = Mage::getModel('catalog/product')->load($itemId);
                $product->setCategoryIds(array_merge($product->getCategoryIds(), array($targetCategory->getId())));
                $product->save();
            }

            $sourceCategory->delete();

            return true;
        } catch (Exception $e) {
            Mage::logException($e);
            return false;
        }
    }

    /**
     * @return false|Mage_Core_Model_Abstract
     */
    protected function getModel()
    {
        return Mage::getModel('catalog/category');
    }
}