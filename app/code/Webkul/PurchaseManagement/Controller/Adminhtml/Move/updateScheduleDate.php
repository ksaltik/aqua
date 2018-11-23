<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_PurchaseManagement
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\PurchaseManagement\Controller\Adminhtml\Move;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Action;
use Magento\TestFramework\ErrorLog\Logger;

class updateScheduleDate extends Action
{
    protected $_storeManager;

    protected $_move;

    protected $resultJsonFactory;

    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        \Webkul\PurchaseManagement\Model\MoveFactory  $moveFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_storeManager=$storeManager;
        $this->_move=$moveFactory;
        $this->_resultJsonFactory=$resultJsonFactory;
    }

    public function execute()
    {
        try {
            $id = $this->getRequest()->getParam("id");
            $move = $this->_move->create()->load($id);
            if (count($move)) {
                $move->setScheduleDate($this->getRequest()->getParam('updateddate'));
                $move->setId($id);
                $move->save();
                $this->messageManager->addSuccess("Shipment has been Updated successfully.");
            } else {
                $this->messageManager->addError("Failed to update the shipment");
            }
            
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        $result = $this->_resultJsonFactory->create();
        $result->setData([true]);
        return $result;
    }
}
