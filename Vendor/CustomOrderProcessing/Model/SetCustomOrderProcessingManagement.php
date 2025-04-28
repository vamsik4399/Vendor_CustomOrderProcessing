<?php
declare(strict_types=1);

namespace Vendor\CustomOrderProcessing\Model;

use Vendor\CustomOrderProcessing\Api\SetCustomOrderProcessingManagementInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Service\InvoiceService;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use Magento\Framework\DB\Transaction;
use Magento\Sales\Model\Order;

class SetCustomOrderProcessingManagement implements SetCustomOrderProcessingManagementInterface
{
     /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var InvoiceService
     */
    protected $invoiceService;

    /**
     * @var InvoiceSender
     */
    protected $invoiceSender;

    /**
     * @var Transaction
     */
    protected $transaction;
    
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        InvoiceService $invoiceService,
        InvoiceSender $invoiceSender,
        Transaction $transaction,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Vendor\CustomOrderProcessing\Model\OrderlogFactory $orderlogFactory
    ) {
        $this->orderRepository = $orderRepository;
        $this->invoiceService = $invoiceService;
        $this->invoiceSender = $invoiceSender;
        $this->transaction = $transaction;
        $this->orderFactory = $orderFactory;
        $this->orderlogFactory = $orderlogFactory;
    }

    /**
     * save status
     * @param string $orderId
     * @param string $status
     * @return string
     */
    public function save($orderId, $status)
    {
        try {
            $order = $this->orderFactory->create()->loadByIncrementId($orderId);
            $orderlog = $this->orderlogFactory->create();
            $log_order = $orderlog->load($order->getIncrementId(),'order_id');
            if(isset($log_order)){
                $orderlog->setOrderId($orderId);
                $orderlog->setOldStatus($order->getStatus());
                $orderlog->save();
            }
           
            if($order) {
                // change order status
                $order->setStatus($status)->setState($status);
                $this->orderRepository->save($order);
               
                //send notification comment
                $order->addStatusHistoryComment(
                    __('Notified customer about status changed #%1.', $order->getId())
                )
                ->setIsCustomerNotified(true)
                ->save();
            } else {
                throw new \Magento\Framework\Exception\LocalizedException(
                        __('Status not updated.')
                    );
            }

            $response[0]["data"] = $order->getId();
            $response[0]['status'] = 1;
            return $response;

        } catch (\Exception $e) {
            $response[0]["data"] = [];
            $response[0]['status'] = 0;
            $response[0]['error']['message'] = $e->getMessage();
            return $response;
        }
    }

}

