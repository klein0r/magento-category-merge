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
class MKleine_Categorymerge_Model_Merge extends Mage_Core_Model_Abstract
{
    /**
     * @param $sourceId Source category Id
     * @param $targetId Target category Id
     * @return bool
     */
    public function mergeCategories($sourceId, $targetId, $deleteSource = false)
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

            // Just delete a category which is not parent of the target
            if ($deleteSource && !in_array($sourceCategory->getId(), $targetCategory->getParentIds())) {
                $sourceCategory->delete();
            }

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