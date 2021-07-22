<?php declare(strict_types=1);

namespace Billiepayment\BilliePaymentMethod\Block\Info;

use \Billiepayment\BilliePaymentMethod\Helper\Data;
use \Magento\Framework\View\Element\Template;
use \Magento\Framework\View\Element\Template\Context;
use \Magento\Store\Model\StoreManagerInterface;

/**
 * Class Info
 */
class Payafterdelivery extends \Magento\Payment\Block\Info
{

    /**
     * @var \Billiepayment\BilliePaymentMethod\Helper\Data
     */
    protected $helper;

    public function __construct(Context $context, StoreManagerInterface $storeManager, Data $helper)
    {
        $this->_storeManager = $storeManager;
        $this->helper = $helper;
        parent::__construct($context);
    }

    protected $_template = 'Billiepayment_BilliePaymentMethod::invoice/view/payment.phtml';
    /**
     * Render as PDF
     * @return string
     */
    public function toPdf()
    {
        $this->setTemplate('Billiepayment_BilliePaymentMethod::invoice/view/pdf/payment.phtml');
        return $this->toHtml();
    }

    /**
     * Prepare information specific to current payment method
     *
     * @param null|\Magento\Framework\DataObject|array $transport
     * @return \Magento\Framework\DataObject
     */
    protected function _prepareSpecificInformation($transport = null)
    {
        $data = [];

        $oInfo = $this->getInfo();

        /* if ($oInfo->getBillieLegalForm() && !$this->isAdmin()) {
             $data[__('Legal Form')] = $this->helper('billie_core')->getLegalFormByCode($oInfo->getBillieLegalForm());
         }*/

        if ($this->isAdmin()) {
            $data[(string)__('Account owner')] = $this->getStoreName();
        }
        if ($oInfo->getBillieViban()) {
            $data[(string)__('VIBAN')] = $oInfo->getBillieViban();
        }
        if ($oInfo->getBillieVbic()) {
            $data[(string)__('VBIC')] = $oInfo->getBillieVbic();
        }
        if ($this->getInfo()->getBillieVbic()) {
            $data[(string)__('Bank')] = $this->helper->getBankAccountByBic($this->getInfo()->getBillieVbic());
        }
        if ($oInfo->getBillieDuration() && $this->isAdmin()) {
            $data[(string)__('Duration')] = $this->getDuration();
        }
        $invoiceIncrementId = $this->getInvoiceIncrementId($oInfo->getOrder());
        if ($oInfo->getBillieViban() && $invoiceIncrementId) {
            $data[(string)__('Usage')] = $invoiceIncrementId;
        }
        if ($oInfo->getBillieRegistrationNumber() && !$this->isAdmin()) {
            $data[(string)__('Registration Number')] = $oInfo->getBillieRegistrationNumber();
        }
        if ($oInfo->getBillieTaxId() && !$this->isAdmin()) {
            $data[(string)__('VAT ID')] = $oInfo->getBillieTaxId();
        }




        if (null === $this->_paymentSpecificInformation) {
            if (null === $transport) {
                $transport = new \Magento\Framework\DataObject();
            } elseif (is_array($transport)) {
                $transport = new \Magento\Framework\DataObject($transport);
            }
            $this->_paymentSpecificInformation = $transport->setData(array_merge($data, $transport->getData()));;
        }
        return $this->_paymentSpecificInformation;
    }

    protected function isAdmin()
    {
        return true; // ('adminhtml' == $this->_state->getAreaCode());

    }

    protected function getStoreName()
    {

        return $this->getConfig('general/store_information/name');
        //return $this->getConfig('general/store_information/name', $this->getStoreId());
    }

    protected function getStoreId()
    {

        $info = $this->getInfo();
        return $info->getOrder()->getStoreId();
    }

    public function getDuration()
    {


        if ($this->is_shipped()) {

            $info = $this->getInfo();
            $order = $info->getOrder();
            $shipping = $order->getShipmentsCollection()->getFirstItem();
            $date = strtotime($shipping->getCreatedAt());
            $newDate = date('d.m.Y', strtotime( $this->getConfig('payment/payafterdelivery/duration') . " day", $date));

            $duration = $newDate;

        } else {

            $duration = __('Order is not shipped yet');

        }

        return $duration;

    }


    protected function getInvoiceIncrementId($order){

        $invoiceIncrementId = '';

        $invoiceCollection = $order->getInvoiceCollection();
        if(count($invoiceCollection) > 0){
            $invoice = $order->getInvoiceCollection()->getFirstItem();
            $invoiceIncrementId = $invoice->getIncrementId();
        }

        return $invoiceIncrementId;

    }

    public function getConfig($config_path)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $conf = $objectManager
            ->get('Magento\Framework\App\Config\ScopeConfigInterface')
            ->getValue($config_path);
        return $conf;
    }
    public function is_shipped() {

        $shipped = false;
        $info = $this->getInfo();
        $order = $info->getOrder();
        $shipping = $order->getShipmentsCollection()->getFirstItem();

        if ($shipping->getCreatedAt()) {
            $shipped = true;
        }

        return $shipped;
    }
}
