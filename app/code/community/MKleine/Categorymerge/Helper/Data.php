<?php

class MKleine_Categorymerge_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getCategory()
    {
        return Mage::registry('category');
    }
}