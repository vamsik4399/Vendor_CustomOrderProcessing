<?php

namespace Vendor\CustomOrderProcessing\Observer;


use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\Escaper;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class OrderSaveAfter
 *
 * @package Vendor\CustomOrderProcessing\Observer
 */
class OrderSaveAfter implements \Magento\Framework\Event\ObserverInterface
{
     /**
     * Custom shipped Order-Status code
     */
    const ORDER_STATUS_SHIPPED = 'shipped';

    public const EMAIL_TEMPLATE_PATH = 'general/store_information/contactus_template';
   
    public const SENDER_NAME_PATH = 'trans_email/ident_support/name';
   
    public const SENDER_EMAIL_PATH = 'trans_email/ident_support/email';

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Vendor\CustomOrderProcessing\Model\OrderlogFactory $orderlogFactory,
        StateInterface $inlineTranslation,
        Escaper $escaper,
        TransportBuilder $transportBuilder,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager

    ) {
        $this->logger = $logger;
        $this->orderlogFactory = $orderlogFactory;
        $this->inlineTranslation = $inlineTranslation;
        $this->escaper = $escaper;
        $this->transportBuilder = $transportBuilder;
        $this->scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
    }

    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) { 
        $order = $observer->getEvent()->getOrder(); 
        $log_orders =  $this->orderlogFactory->create();
        $log_order = $log_orders->load($order->getIncrementId(),'order_id');
        $log_order->setNewStatus($order->getStatus());
        $log_order->save();
        if($order->getStatus()==self::ORDER_STATUS_SHIPPED){
            $this->sendEmail($order);
        }
       
    } 
   
    /**
     * Send email
     *
     * @param object $order
     * @return void
     */
    public function sendEmail($order)
    {
        try {
            $this->inlineTranslation->suspend();
            $email_template = $this->scopeConfig->getValue(
                self::EMAIL_TEMPLATE_PATH,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $this->_storeManager->getStore()->getId(),
            );
            $sender_name = $this->scopeConfig->getValue(
                self::SENDER_NAME_PATH,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $this->_storeManager->getStore()->getId(),
            );
            $sender_email = $this->scopeConfig->getValue(
                self::SENDER_EMAIL_PATH,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $this->_storeManager->getStore()->getId(),
            );
            $sender = [
                'name' => $sender_name,
                'email' => $sender_email,
            ];
          

            // Send mail to user
            $send_to = $order->getCustomerEmail();
            $_transport = $this->transportBuilder
                ->setTemplateIdentifier($email_template)
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    ]
                )
                ->setTemplateVars([
                    'name'  => $order->getCustomerFirstname(),
                    'last_name' => $order->getCustomerLastname(),
                    'email' => $order->getCustomerEmail()
                ])
                ->setFrom($sender)
                ->addTo($send_to)
                ->getTransport();
            $_transport->sendMessage();
            $this->inlineTranslation->resume();
        } catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
        }
    }
}
                    