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
class MKleine_Categorymerge_Model_Merge extends Mage_Core_Model_Abstract
{
    protected $_deleteSource = false;

    /**
     * @var Mage_Catalog_Model_Category
     */
    protected $_source = null;

    /**
     * @var Mage_Catalog_Model_Category
     */
    protected $_target = null;

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
        if (null === $this->_write) {
            $this->_write = Mage::getSingleton('core/resource')->getConnection('core_write');
        }
        return $this->_write;
    }

    /**
     * @return string
     */
    protected function _getTable()
    {
        return Mage::getSingleton('core/resource')->getTableName('catalog/category_product');
    }

    /**
     * @param bool $deleteSource
     *
     * @return bool
     */
    public function mergeCategories($deleteSource = false)
    {
        $this->_deleteSource = $deleteSource;
        try {
            // Check if both categories exist
            if (!$this->_source->getId() || !$this->_target->getId()) {
                return false;
            }

            $this->_moveProducts();
            $this->_moveChildrenCategories();

            // Just delete a category which is not parent of the target
            if ($this->_deleteSource && !in_array($this->_source->getId(), $this->_target->getParentIds())) {
                $this->_source->delete();
            }

            Mage::dispatchEvent('mkleine_category_merge_finished',
                ['source_category' => $this->_source, 'target_category' => $this->_target]
            );

            Mage::getSingleton('index/indexer')->processEntityAction(
                $this->_target, 'catalog_category_product', Mage_Index_Model_Event::TYPE_SAVE
            );

            return true;
        } catch (Exception $e) {
            Mage::logException($e);
            return false;
        }
    }

    /**
     * This method is pretty resource hungry and time consuming ...
     * move also triggers a clear cache and a reindex
     *
     */
    private function _moveChildrenCategories()
    {
        /** @var Mage_Catalog_Model_Resource_Category_Collection $sourceChildren */
        $sourceChildren = $this->_source->getChildrenCategoriesWithInactive();

        foreach ($sourceChildren as $child) {
            /** @var $child Mage_Catalog_Model_Category */
            $child->move($this->_target->getId(), null);
        }
    }

    /**
     */
    private function _moveProducts()
    {
        $productSourceIds   = $this->_source->getProductCollection()->setOrder('position', 'asc')->getAllIds();
        $productSourceItems = array_fill_keys($productSourceIds, 1);
        $productInsert      = array_diff_key($productSourceItems, $this->_target->getProductsPosition());

        if (!empty($productInsert)) {
            $data = [];
            foreach ($productInsert as $productId => $position) {
                $data[] = [
                    'category_id' => (int)$this->_target->getId(),
                    'product_id'  => (int)$productId,
                    'position'    => (int)$position
                ];
            }
            $this->_getWriteAdapter()->insertMultiple($this->_getTable(), $data);
        }
    }

    /**
     * @param int $sourceId
     *
     * @return $this
     */
    public function setSource($sourceId)
    {
        $this->_source = Mage::getModel('catalog/category')->load((int)$sourceId);
        return $this;
    }

    /**
     * @param int $targetId
     *
     * @return $this
     */
    public function setTarget($targetId)
    {
        $this->_target = Mage::getModel('catalog/category')->load((int)$targetId);;
        return $this;
    }

    /**
     * @return Mage_Catalog_Model_Category
     */
    public function getSource()
    {
        return $this->_source;
    }

    /**
     * @return Mage_Catalog_Model_Category
     */
    public function getTarget()
    {
        return $this->_target;
    }
}
