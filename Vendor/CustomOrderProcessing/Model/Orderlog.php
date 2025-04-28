<?php
namespace Vendor\CustomOrderProcessing\Model;

class Orderlog extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Vendor\CustomOrderProcessing\Model\ResourceModel\Orderlog');
    }
}

