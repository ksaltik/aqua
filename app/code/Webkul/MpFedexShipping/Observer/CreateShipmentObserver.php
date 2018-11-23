<?php
/**
 * Webkul Software
 *
 * @category Webkul
 * @package Webkul_MpFedexShipping
 * @author Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */
namespace Webkul\MpFedexShipping\Observer;

use Magento\Framework\Event\Manager;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Session\SessionManager;

class CreateShipmentObserver implements ObserverInterface
{
    /**
     * @var eventManager
     */
    protected $eventManager;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

/**
 * @param \Magento\Framework\Event\Manager $eventManager
 * @param \Magento\Framework\ObjectManagerInterface $objectManager
 * @param Magento\Customer\Model\Session $customerSession
 * @param SessionManager $session
 */
    public function __construct(
        \Magento\Framework\Event\Manager $eventManager,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->_eventManager = $eventManager;
        $this->objectManager = $objectManager;
    }

    /**
     * when shipment generates from seller panel
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $request = new \Magento\Framework\DataObject();
        $orderId = $observer->getOrderId();
        $request->setOrderId($orderId);
        $request->setSellerShipment(true);
        $this->objectManager->create('Webkul\MpFedexShipping\Model\Carrier')->_doShipmentRequest($request);
    }
}
