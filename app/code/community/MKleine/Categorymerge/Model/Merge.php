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
     * Write connection
     *
     * @var Varien_Db_Adapter_Pdo_Mysql
     */
    protected $_write;

    /**
     * Retrieve connection for write data
     *
     * @return Varien_Db_Adapter_Interface
     */
    protected function _getWriteAdapter()
    {
        if (is_null($this->_write)) {
            $this->_write = Mage::getSingleton('core/resource')->getConnection('core_write');
        }
        return $this->_write;
    }

    protected function _getTable()
    {
        return Mage::getSingleton('core/resource')->getTableName('catalog/category_product');
    }

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

            $sourceItems = array_fill_keys($sourceCategory->getProductCollection()->setOrder('position', 'asc')->getAllIds(), 1);
            $insert = array_diff_key($sourceItems, $targetCategory->getProductsPosition());

            // Add products to category
            if (!empty($insert)) {
                $data = array();
                foreach ($insert as $productId => $position) {
                    $data[] = array(
                        'category_id' => (int)$targetId,
                        'product_id' => (int)$productId,
                        'position' => (int)$position
                    );
                }
                $this->_getWriteAdapter()->insertMultiple($this->_getTable(), $data);
            }

            // Just delete a category which is not parent of the target
            if ($deleteSource && !in_array($sourceCategory->getId(), $targetCategory->getParentIds())) {
                $sourceCategory->delete();
            }

            Mage::dispatchEvent('mkleine_category_merge_finished',
                array('source_category' => $sourceCategory, 'target_category' => $targetCategory));

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