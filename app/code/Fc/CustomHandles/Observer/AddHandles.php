<?php
/**
 * MIT License
 *
 * Copyright (c) 2017 Frank Clark

 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:

 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.

 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */
namespace Fc\CustomHandles\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Model\Session as CustomerSession;

/**
 * Class AddHandles
 * @package Fc\CustomHandles\Observer
 * @author Frank Clark <mrfrankclark1@gmail.com>
 */
class AddHandles implements ObserverInterface
{

    /** @var CustomerSession  */
    protected $customerSession;

    /**
     * AddHandles constructor.
     * @param CustomerSession $customerSession
     */
    public function __construct(
        CustomerSession $customerSession
    ) {
        $this->customerSession = $customerSession;
    }

    /**
     * Determine if the customer is logged in and if so add a layout handle
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $layout = $observer->getEvent()->getLayout();

        if ($this->customerSession->isLoggedIn()) {
        /*Hide reward point link from customer dashbard navigation*/
            /*$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $sunarcRewardHelper = $objectManager->create('\Sunarc\Vendor\Helper\Reward'); 
            $sunarcRewardHelper->getEnableVendorList();
            if($sunarcRewardHelper->getEnableVendorList()==false){   
                $layout->getUpdate()->addHandle('customer');
            }*/
            //$layout->getUpdate()->addHandle('customer');
        } else {
            $layout->getUpdate()->addHandle('customer_logged_out');
        }
    }
}
