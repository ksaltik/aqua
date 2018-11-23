<?php

namespace Magenest\Payeezy\Observer;

use Magento\Framework\Event\ObserverInterface;


class PaymentMethodAvailable implements ObserverInterface
{
    /**
     * payment_method_is_active event handler.
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $cart = $objectManager->get('\Magento\Checkout\Model\Cart');
        $items = $cart->getQuote()->getAllVisibleItems();
        $enable=1;
        foreach ($items as $item) {
            //   echo $item->getSku();echo "<br/>";
            if($item->getPrice()==0){
                $enable=0;
            }
        }
        if($enable==0){
            if($observer->getEvent()->getMethodInstance()->getCode()=="payeezy"){
                $checkResult = $observer->getEvent()->getResult();
                $checkResult->setData('is_available', false); //this is disabling the payment method at checkout page
            }
            if($observer->getEvent()->getMethodInstance()->getCode()=="checkmo"){
                $checkResult = $observer->getEvent()->getResult();
                $checkResult->setData('is_available', false); //this is disabling the payment method at checkout page
            }
        }
    }
}