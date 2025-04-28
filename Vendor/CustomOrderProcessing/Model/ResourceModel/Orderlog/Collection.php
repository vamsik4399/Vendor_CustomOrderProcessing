<?php 
namespace Vendor\CustomOrderProcessing\Model\ResourceModel\Orderlog;
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection{
	public function _construct(){
		$this->_init("Vendor\CustomOrderProcessing\Model\Orderlog","Vendor\CustomOrderProcessing\Model\ResourceModel\Orderlog");
	}
}