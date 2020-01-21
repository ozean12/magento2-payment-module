<?php
/**
 * Created by HIGHDIGITAL
 * @package     billie-magento-2
 * @copyright   Copyright (c) 2020 HIGHDIGITAL UG (https://www.highdigital.de)
 * User: ngongoll
 * Date: 19.01.20
 */

namespace Magento\BilliePaymentMethod\Observer;

use Magento\Framework\Event\ObserverInterface;
use \Magento\BilliePaymentMethod\Helper\Data;
use \Magento\Framework\Exception\LocalizedException;
use \Magento\Store\Model\StoreManagerInterface;
use \Magento\Framework\Message\ManagerInterface;

class CreateOrder implements ObserverInterface
{

    const paymentmethodCode = 'payafterdelivery';
    const duration = 'payment/payafterdelivery/duration';

    protected $_storeManager;
    protected $_messageManager;
    protected $logger;

    public function __construct(Data $helper, \Magento\Framework\Message\ManagerInterface $messageManager,\Magento\Store\Model\StoreManagerInterface $storeManager, \Psr\Log\LoggerInterface $logger )
    {
        $this->helper = $helper;
        $this->_messageManager = $messageManager;
        $this->_storeManager = $storeManager;
        $this->logger = $logger;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        $order = $observer->getEvent()->getOrder();
        $payment = $order->getPayment();
        $paymentMethod = $payment->getMethodInstance();

        if ($payment->getCode() == self::paymentmethodCode) {
            return;
        }

        $this->helper->setStoreId($order->getStoreId());
        $billieOrderData = $this->helper->mapCreateOrderData($order);

        try {
            // initialize Billie Client

            $client = $this->helper->clientCreate();

            $billieResponse = $client->createOrder($billieOrderData);
//            Mage::Helper('billie_core/log')->billieLog($order, $billieOrderData, $billieResponse);
            $this->logger->debug(print_r($billieResponse,true));
            $order->setData('billie_reference_id', $billieResponse->referenceId);
            $order->addStatusHistoryComment(__('Billie PayAfterDelivery: payment accepted for %s', $billieResponse->referenceId));

            $payment->setData('billie_viban', $billieResponse->bankAccount->iban);
            $payment->setData('billie_vbic', $billieResponse->bankAccount->bic);
            $payment->setData('billie_duration', intval( $this->helper->getConfig(self::duration,$order->getStoreId())));

            $order->save();
            $payment->save();

        }catch (\Billie\Exception\BillieException $e){
            $errorMsg = __($e->getBillieCode());

//            Mage::Helper('billie_core/log')->billieLog($order, $billieOrderData,$errorMsg );
            throw new LocalizedException(__($errorMsg));

        }catch (\Billie\Exception\InvalidCommandException $e){

            $errorMsg = __($e->getViolations()['0']);

//            Mage::Helper('billie_core/log')->billieLog($order, $billieOrderData,$errorMsg );
            throw new LocalizedException(__($errorMsg));

        }
        catch (Exception $e) {
            throw new LocalizedException(__($e->getMessage()));

        }

    }
}