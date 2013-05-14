<?php

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

        $this->addColumn('merge_action', array(
            'header'    =>  Mage::helper('mk_categorymerge')->__('Action'),
            'width'     => '100',
            'type'      => 'action',
            'getter'    => 'getId',
            'confirm'   => 'ka',
            'actions'   => array(
                array(
                    'caption'   => Mage::helper('mk_categorymerge')->__('Merge'),
                    'url'       => array('base'=> '*/*/merge', 'params' => array( 'source' => $this->getCategory()->getId() )),
                    'field'     => 'target'
                )
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