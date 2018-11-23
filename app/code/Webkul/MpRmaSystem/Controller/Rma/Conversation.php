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
namespace Webkul\MpRmaSystem\Controller\Rma;

use Magento\Framework\App\Action\Context;

class Conversation extends \Magento\Framework\App\Action\Action
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
     * @param Context $context
     * @param \Webkul\MpRmaSystem\Helper\Data $mpRmaHelper
     * @param \Webkul\MpRmaSystem\Model\ConversationFactory $conversation
     */
    public function __construct(
        Context $context,
        \Webkul\MpRmaSystem\Helper\Data $mpRmaHelper,
        \Webkul\MpRmaSystem\Model\ConversationFactory $conversation
    ) {
        $this->_mpRmaHelper = $mpRmaHelper;
        $this->_conversation = $conversation;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $data = $this->getRequest()->getParams();
        $helper = $this->_mpRmaHelper;
        if (array_key_exists("is_guest", $data)) {
            if (!$helper->isGuestLoggedIn()) {
                return $this->resultRedirectFactory
                            ->create()
                            ->setPath('mprmasystem/guest/login');
            }
        } else {
            if (!$helper->isLoggedIn()) {
                return $this->resultRedirectFactory
                            ->create()
                            ->setPath('customer/account/login');
            }
        }

        $data['created_time'] = date("Y-m-d H:i:s");
        $this->_conversation->create()->setData($data)->save();
        $this->messageManager->addSuccess(__("Message sent"));
        $helper->sendNewMessageEmail($data);
        if ($data['sender_type'] == 1) {
            return $this->resultRedirectFactory
                    ->create()
                    ->setPath(
                        '*/seller/rma',
                        ['id' => $data['rma_id'], 'back' => null, '_current' => true]
                    );
        } elseif ($data['sender_type'] == 2) {
            return $this->resultRedirectFactory
                    ->create()
                    ->setPath(
                        '*/customer/rma',
                        ['id' => $data['rma_id'], 'back' => null, '_current' => true]
                    );
        } else {
            return $this->resultRedirectFactory
                    ->create()
                    ->setPath(
                        '*/guest/rma',
                        ['id' => $data['rma_id'], 'back' => null, '_current' => true]
                    );
        }
    }
}
