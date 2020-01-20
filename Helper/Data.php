<?php

namespace Magento\BilliePaymentMethod\Helper;

use \Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{

    public function clientCreate()
    {

        return Billie\HttpClient\BillieClient::create(Mage::getStoreConfig(self::apiKey), $this->getMode());
    }

    public function mapShipOrderData($order)
    {

        $command = new Billie\Command\ShipOrder($order->getBillieReferenceId());
        $command->orderId = $order->getIncrementId();
        $command->invoiceNumber = $order->getInvoiceCollection()->getFirstItem()->getIncrementId();
        $command->invoiceUrl = Mage::getStoreConfig(self::invoiceUrl) . DS . $order->getIncrementId() . '.pdf';

        return $command;
    }
}
