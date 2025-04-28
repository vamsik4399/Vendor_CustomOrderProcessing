<?php
declare(strict_types=1);

namespace Vendor\CustomOrderProcessing\Api;

interface SetCustomOrderProcessingManagementInterface
{

     /**
     * save status
     * @param string $orderId
     * @param string $status
     * @return string
     */
    public function save($orderId, $status);
}

