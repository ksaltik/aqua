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
namespace Webkul\PurchaseManagement\Controller\Adminhtml\History;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Save extends Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    protected $_helper;

    protected $_history;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $_resultJsonFactory;

    /**
     * @param Context     $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Webkul\PurchaseManagement\Model\HistoryFactory  $historyFactory,
        \Webkul\PurchaseManagement\Helper\Data $helper,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->_resultPageFactory = $resultPageFactory;
        $this->_history=$historyFactory;
        $this->_helper=$helper;
        $this->_resultJsonFactory=$resultJsonFactory;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $data = $this->getRequest()->getParams();
        if ($data) {
            $comment=$data['comment'];
            $order_id=$data['order_id'];
            $status_id=$data['status_id'];
            $is_notified=$data['is_notified'];
            if($is_notified == "true")
                $is_notified = 1;
            else
                $is_notified = 0;
            $objective=$data['objective'];
            $this->_helper->addHistory($comment, $objective, $order_id, $is_notified, $status_id);
        }
    }

    /**
     * Check for is allowed.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_PurchaseManagement::shipments');
    }
}
