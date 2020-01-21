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


    /** @var mixed */
    protected $storeId = null;

    /**
     * @param mixed $storeId
     */
    public function setStoreId($storeId)
    {
        $this->storeId = $storeId;
    }

    /**
     * @return mixed
     */
    public function getStoreId()
    {
        return $this->storeId;
    }

    public function getConfig($config_path)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function clientCreate()
    {
        return \Billie\HttpClient\BillieClient::create($this->getConfig(self::apiConsumerKey), $this->getConfig(self::apiConsumerSecretKey), $this->getMode());

    }

    public function mapCreateOrderData($order)
    {

        $billingAddress = $order->getBillingAddress();
        $shippingAddress = $order->getShippingAddress();
        $payment = $order->getPayment();

        $customerId = $order->getCustomerId()?$order->getCustomerId():$order->getCustomerEmail();

        $command = new \Billie\Command\CreateOrder();

        // Company information
        $command->debtorCompany = new \Billie\Model\Company($customerId, $payment->getBillieCompany()?$payment->getBillieCompany():$shippingAddress->getCompany(), $this->mapAddress($billingAddress));
        $command->debtorCompany->legalForm = $payment->getBillieLegalForm()?$payment->getBillieLegalForm():'10001';
        $command->debtorCompany->taxId = $payment->getBillieTaxId()?$payment->getBillieTaxId():'123456';
        $command->debtorCompany->registrationNumber = $payment->getBillieRegistrationNumber()?$payment->getBillieRegistrationNumber():'Amtsgericht Charlottenburg';

        // Information about the person
        $command->debtorPerson = new \Billie\Model\Person($order->getCustomerEmail());
        $command->debtorPerson->salution = ($payment->getBillieSalutation() ? 'm' : 'f');

        $command->deliveryAddress = $this->mapAddress($shippingAddress);

        // Amount
        $command->amount = new \Billie\Model\Amount(($order->getBaseGrandTotal() - $order->getBaseTaxAmount())*100, $order->getGlobalCurrencyCode(), $order->getBaseTaxAmount()*100); // amounts are in cent!

        // Define the due date in DAYS AFTER SHIPPMENT
        $command->duration = intval( $this->getConfig(self::duration,$this->getStoreId()) );

        return $command;
    }

    /**
     * @param $order
     * @return CancelOrder
     *
     */

    public function cancel($order){

        return  new \Billie\Command\CancelOrder($order->getBillieReferenceId());

    }

    public function mapShipOrderData($order)
    {
        $command = new \Billie\Command\ShipOrder($order->getBillieReferenceId());

        $command->orderId = $order->getIncrementId();
        $command->invoiceNumber = $order->getInvoiceCollection()->getFirstItem()->getIncrementId();

        $command->invoiceUrl = $this->getConfig(self::invoiceUrl) . '/' . $order->getIncrementId() . '.pdf';

        return $command;
    }

    public function mapAddress($address)
    {

//        if(!$this->getConfig(self::housenumberField,$this->getStoreId())) {
//            $housenumber = '';
//        }else if($this->getConfig(self::housenumberField,$this->getStoreId()) != 'street'){
//            $housenumber = $address->getData($this->getConfig(self::housenumberField,$this->getStoreId()));
//        }else{
//            $housenumber = $address->getStreet()[1];
//        }

        $housenumber = $address->getStreet()[1];

        $addressObj = new \Billie\Model\Address();
        $addressObj->street = $address->getStreet()[0];
        $addressObj->houseNumber = $housenumber;
        $addressObj->postalCode = $address->getPostcode();
        $addressObj->city = $address->getCity();
        $addressObj->countryCode = $address->getCountryId();

        return $addressObj;
    }

    public function getMode(){

        return $this->getConfig(self::sandboxMode);

    }
}