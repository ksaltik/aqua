<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_MpRmaSystem
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\MpRmaSystem\Controller\Adminhtml\Rma;

class Message extends \Webkul\MpRmaSystem\Controller\Adminhtml\Rma
{
    /**
     * @var \Webkul\MpRmaSystem\Helper\Data
     */
    protected $_mpRmaHelper;

    /**
     * @var \Webkul\MpRmaSystem\Model\ConversationFactory
     */
    protected $_conversation;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Webkul\MpRmaSystem\Helper\Data $mpRmaHelper
     * @param \Webkul\MpRmaSystem\Model\ConversationFactory $conversation
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Webkul\MpRmaSystem\Helper\Data $mpRmaHelper,
        \Webkul\MpRmaSystem\Model\ConversationFactory $conversation
    ) {
        $this->_mpRmaHelper = $mpRmaHelper;
        $this->_conversation = $conversation;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\View\Result\Layout
     */
    public function execute()
    {
        $data = $this->getRequest()->getParams();
        $data['sender_type'] = 0;
        $data['created_time'] = date("Y-m-d H:i:s");
        $rmaId = $data['rma_id'];
        $model = $this->_conversation->create();
        $model->setData($data)->save();
        $this->messageManager->addSuccess(__("Message sent"));
        $this->_mpRmaHelper->sendNewMessageEmail($data);
        return $this->resultRedirectFactory
                ->create()
                ->setPath(
                    '*/rma/edit',
                    ['id' => $rmaId, 'back' => null, '_current' => true]
                );
    }
}
