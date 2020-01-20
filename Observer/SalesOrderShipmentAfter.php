<?php
/**
 * Created by HIGHDIGITAL
 * @package     billie-magento-2
 * @copyright   Copyright (c) 2020 HIGHDIGITAL UG (https://www.highdigital.de)
 * User: ngongoll
 * Date: 19.01.20
 */

namespace Magento\BilliePaymentMethod\Model\Observer;

use Magento\Framework\Event\ObserverInterface;

class SalesOrderShipmentAfter implements ObserverInterface
{

    const paymentmethodCode = 'payafterdelivery';
    const duration = 'payment/payafterdelivery/duration';

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $shipment = $observer->getEvent()->getShipment();

        $order = $shipment->getOrder();
        $payment = $order->getPayment()->getMethodInstance();

        /** @var \Magento\Sales\Model\Order $order */

        if ($payment->getCode() != self::paymentmethodCode) {
            return;
        }

        $invoiceIds = $order->getInvoiceCollection()->getAllIds();

        if (!$invoiceIds) {

            Mage::throwException('You have to create a invoice first');

        } else {

            try {

                $billieShipData = Mage::helper('billie_core/sdk')->mapShipOrderData($order);

                $client = Mage::Helper('billie_core/sdk')->clientCreate();
                $billieResponse = $client->shipOrder($billieShipData);

                Mage::Helper('billie_core/log')->billieLog($order, $billieShipData, $billieResponse);
                $order->addStatusHistoryComment(Mage::Helper('billie_core')->__('Billie PayAfterDelivery: shipping information was send for %s. The customer will be charged now', $billieResponse->referenceId));
                $order->save();

            } catch (Exception $error) {

                Mage::throwException($error->getMessage());

            }
        }

    }
}