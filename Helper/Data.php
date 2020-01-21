<?php

namespace Magento\BilliePaymentMethod\Helper;

use \Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
    const sandboxMode = 'payment/magento_billiePaymentMethod/sandbox';
    const apiConsumerKey = 'payment/magento_billiePaymentMethod/consumer_key';
    const apiConsumerSecretKey = 'payment/magento_billiePaymentMethod/consumer_secret_key';
    const duration = 'payment/magento_billiePaymentMethod/duration';
    const housenumberField = 'billie_core/config/housenumber';
    const invoiceUrl = 'billie_core/config/invoice_url';

    public function getConfig($config_path)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function clientCreate()
    {
//        return \Billie\HttpClient\BillieClient::create($this->getConfig(self::apiConsumerKey), $this->getConfig(self::apiConsumerSecretKey), $this->getMode());

        return \Billie\HttpClient\BillieClient::create('bfebbc05-d1f0-4e47-be21-c99e7fd2ffcc', 'cv8hfihix4gso0koc0cgs8wosks4gwwwgo04cg00c4k4okggccg4wo8s88w8c4', $this->getMode());
    }

    public function mapShipOrderData($order)
    {
        $order->setData('billie_reference_id','13703b10-e2a2-4d77-becc-7880d30c564b');
        $command = new \Billie\Command\ShipOrder($order->getBillieReferenceId());

        $command->orderId = $order->getIncrementId();
        $command->invoiceNumber = $order->getInvoiceCollection()->getFirstItem()->getIncrementId();

        $command->invoiceUrl = $this->getConfig(self::invoiceUrl) . '/' . $order->getIncrementId() . '.pdf';

        return $command;
    }

    public function getMode(){

        return $this->getConfig(self::sandboxMode);

    }
}