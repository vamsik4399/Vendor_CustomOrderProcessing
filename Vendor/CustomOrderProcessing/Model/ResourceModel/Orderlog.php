<?php 
namespace Vendor\CustomOrderProcessing\Model\ResourceModel;
class Orderlog extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb{
 public function _construct(){
 $this->_init("order_custom_log","entity_id");
 }
}