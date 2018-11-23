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
namespace Webkul\PurchaseManagement\Controller\Adminhtml\Picking;
 
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
 
class Massdelete extends Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    protected $_picking;

    protected $_move;

    protected $_order;

    protected $_orderitem;

    protected $_helper;

    protected $filter;

    /**
     * @param Context     $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Webkul\PurchaseManagement\Model\PickingFactory  $pickingFactory,
        \Webkul\PurchaseManagement\Model\MoveFactory  $moveFactory,
        \Webkul\PurchaseManagement\Model\OrderFactory  $orderFactory,
        \Webkul\PurchaseManagement\Model\OrderitemFactory  $orderitemFactory,
        \Webkul\PurchaseManagement\Helper\Data $helper,
        \Magento\Ui\Component\MassAction\Filter $filter
    ) {
        parent::__construct($context);
        $this->_resultPageFactory = $resultPageFactory;
        $this->_move=$moveFactory;
        $this->_picking=$pickingFactory;
        $this->_order=$orderFactory;
        $this->_orderitem=$orderitemFactory;
        $this->_helper=$helper;
        $this->filter=$filter;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $collection = $this->filter->getCollection($this->_picking->create()->getCollection());
        $pickingIds = $collection->getAllIds();

        if (!is_array($pickingIds)) {
            $this->messageManager->addError(__('Please select item(s)'));
        } else {
            try {
                foreach ($pickingIds as $pickingId) {
                    $picking = $this->_picking->create()->load($pickingId);
                    $picking->setId($picking->getEntityId());
                    $picking->delete();
                    $moves = $this->_move->create()->getCollection()->addFieldToFilter("picking_id", $pickingId);
                    foreach ($moves as $move) {
                        $this->_move->create()->load($move->getEntityId())->setId($move->getEntityId())->delete();
                    }
                }
                $this->messageManager->addSuccess(__(
                        'Record(s) were successfully deleted'
                    )
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        return $resultRedirect->setPath('*/*/index');
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
