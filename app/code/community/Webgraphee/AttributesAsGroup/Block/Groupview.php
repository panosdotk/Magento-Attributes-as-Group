<?php 

class Webgraphee_AttributesAsGroup_Block_Groupview extends Mage_Core_Block_Template
{
    protected $_product = null;
 
    function getProduct()
    {
        if (!$this->_product) {
            $this->_product = Mage::registry('product');
        }
        return $this->_product;
    }
 
    public function getAdditionalData(array $excludeAttr = array())
    {
        $data = array();
        $product = $this->getProduct();
        $attributes = $product->getAttributes();
        foreach ($attributes as $attribute) {
//            if ($attribute->getIsVisibleOnFront() && $attribute->getIsUserDefined() && !in_array($attribute->getAttributeCode(), $excludeAttr)) {
            if ($attribute->getIsVisibleOnFront() && !in_array($attribute->getAttributeCode(), $excludeAttr)) {

                $value = $attribute->getFrontend()->getValue($product);

                // TODO this is temporary skipping eco taxes
                if (is_string($value)) {
                    if (strlen($value) && $product->hasData($attribute->getAttributeCode())) {
                        if ($attribute->getFrontendInput() == 'price') {
                            $value = Mage::app()->getStore()->convertPrice($value,true);
                        }
                        
                    	$group = 0;
                        if( $tmp = $attribute->getData('attribute_group_id') ) {
                            $group = $tmp;
                        }
                          
                        $data[$group]['items'][ $attribute->getAttributeCode()] = array(
                           'label' => $attribute->getStoreLabel(),
                           'value' => $value,
                           'code'  => $attribute->getAttributeCode()
                        );
 
                        $data[$group]['attrid'] = $attribute->getId();
                        
                    }
                }
            }
        }
        
    	// Noch Titel lesen
        foreach( $data AS $groupId => &$group ) {
            $groupModel = Mage::getModel('eav/entity_attribute_group')->load( $groupId );
            $group['title'] = $groupModel->getAttributeGroupName();
        }
        
        return $data;
    }    
    
  
}
