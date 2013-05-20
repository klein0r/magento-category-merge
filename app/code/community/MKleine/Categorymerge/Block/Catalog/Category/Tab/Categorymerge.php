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
class MKleine_Categorymerge_Block_Catalog_Category_Tab_Categorymerge extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('catalog_category_categorymerge');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
    }

    public function getCategory()
    {
        return Mage::registry('category');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('catalog/category')->getCollection()
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('is_active')
            ->addAttributeToSelect('level')
            ->addAttributeToFilter('entity_id', array('neq' => $this->getCategory()->getId()));

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {

        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('mk_categorymerge')->__('ID'),
            'sortable'  => true,
            'width'     => '60',
            'index'     => 'entity_id'
        ));

        $this->addColumn('name', array(
            'header'    => Mage::helper('mk_categorymerge')->__('Name'),
            'index'     => 'name'
        ));

        $this->addColumn('is_active', array(
            'header'    => Mage::helper('mk_categorymerge')->__('Is Active'),
            'width'     => '70',
            'index'     => 'is_active',
            'type'      => 'options',
            'options'   => array(
                0   => Mage::helper('mk_categorymerge')->__('No'),
                1   => Mage::helper('mk_categorymerge')->__('Yes')
            )
        ));

        $this->addColumn('level', array(
            'header'    => Mage::helper('mk_categorymerge')->__('Level'),
            'width'     => '70',
            'index'     => 'level',
        ));

        $this->addColumn('merge_to_action', array(
            'header'    =>  Mage::helper('mk_categorymerge')->__('Action To'),
            'width'     => '200',
            'type'      => 'action',
            'getter'    => 'getId',
            'confirm'   => 'ka',
            'actions'   => array(
                array(
                    'caption'   => Mage::helper('mk_categorymerge')->__('Merge to and delete current'),
                    'url'       => array('base'=> '*/*/merge', 'params' => array( 'source' => $this->getCategory()->getId(), 'delete' => 1 )),
                    'field'     => 'target'
                ),
                array(
                    'caption'   => Mage::helper('mk_categorymerge')->__('Merge to and keep current'),
                    'url'       => array('base'=> '*/*/merge', 'params' => array( 'source' => $this->getCategory()->getId(), 'delete' => 0 )),
                    'field'     => 'target'
                ),
            ),
            'filter'    => false,
            'sortable'  => false,
            'index'     => 'stores',
            'is_system' => true,
        ));

        $this->addColumn('merge_from_action', array(
            'header'    =>  Mage::helper('mk_categorymerge')->__('Action From'),
            'width'     => '200',
            'type'      => 'action',
            'getter'    => 'getId',
            'confirm'   => 'ka',
            'actions'   => array(
                array(
                    'caption'   => Mage::helper('mk_categorymerge')->__('Merge into current and delete source'),
                    'url'       => array('base'=> '*/*/merge', 'params' => array( 'target' => $this->getCategory()->getId(), 'delete' => 1 )),
                    'field'     => 'source'
                ),
                array(
                    'caption'   => Mage::helper('mk_categorymerge')->__('Merge into current and keep source'),
                    'url'       => array('base'=> '*/*/merge', 'params' => array( 'target' => $this->getCategory()->getId(), 'delete' => 0 )),
                    'field'     => 'source'
                ),
            ),
            'filter'    => false,
            'sortable'  => false,
            'index'     => 'stores',
            'is_system' => true,
        ));

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/mergegrid', array('_current' => true));
    }

}