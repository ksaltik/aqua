<?php

namespace Sunarc\AdvPermission\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class RolePermissions implements ObserverInterface
{

    public function execute(Observer $observer)
    {
        $request = $observer->getEvent()->getRequest();
        $role = $observer->getEvent()->getObject();
        $restrictBySplitAttribute = (bool)$request->getParam('restrict_by_splitattribute');
        $accessScope = implode(',',$request->getParam('store_ids'));
        $role->setRestrictBySplitattribute($restrictBySplitAttribute);
        $role->setStoreIds($accessScope);
        $role->setWebsiteId($request->getParam('website_id'));

        if ($request->getParam('is_order_restrict_by_scope')) {
            $isOrderRestrictByScope = (bool)$request->getParam('is_order_restrict_by_scope');
            $role->setIsOrderRestrictByScope($isOrderRestrictByScope);
        }
        if ($request->getParam('is_invoice_restrict_by_scope')) {
            $isInvoiceRestrictByScope = (bool)$request->getParam('is_invoice_restrict_by_scope');
            $role->setIsInvoiceRestrictByScope($isInvoiceRestrictByScope);
        }
        if ($request->getParam('is_shipment_restrict_by_scope')) {
            $isShipmentRestrictByScope = (bool)$request->getParam('is_shipment_restrict_by_scope');
            $role->setIsShipmentRestrictByScope($isShipmentRestrictByScope);
        }
        if ($request->getParam('is_creditmemo_restrict_by_scope')) {
            $isCreditmemoRestrictByScope = (bool)$request->getParam('is_creditmemo_restrict_by_scope');
            $role->setIsCreditmemoRestrictByScope($isCreditmemoRestrictByScope);
        }
    }
}
