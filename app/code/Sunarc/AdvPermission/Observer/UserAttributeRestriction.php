<?php

namespace Sunarc\AdvPermission\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class UserAttributeRestriction implements ObserverInterface
{

    public function execute(Observer $observer)
    {
        $user = $observer->getEvent()->getObject();
        if ($user->getIsOrderRestrictByScope()) {
            $isOrderRestrictByScope = $user->getIsOrderRestrictByScope();
            $user->setIsOrderRestrictByScope($isOrderRestrictByScope);
        }
        if ($user->getIsInvoiceRestrictByScope()) {
            $isInvoiceRestrictByScope = $user->getIsInvoiceRestrictByScope();
            $user->setIsInvoiceRestrictByScope($isInvoiceRestrictByScope);
        }
        if ($user->getIsShipmentRestrictByScope()) {
            $isShipmentRestrictByScope = $user->getIsShipmentRestrictByScope();
            $user->setIsShipmentRestrictByScope($isShipmentRestrictByScope);
        }
        if ($user->getIsCreditmemoRestrictByScope()) {
            $isCreditmemoRestrictByScope = $user->getIsCreditmemoRestrictByScope();
            $user->setIsCreditmemoRestrictByScope($isCreditmemoRestrictByScope);
        }
        if ($user->getSplitattributeRestrictions()) {
        $splitAttributeRestrictions = implode(',', $user->getSplitattributeRestrictions());
        $user->setSplitattributeRestrictions($splitAttributeRestrictions);
        }
    }
}
