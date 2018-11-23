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
namespace Webkul\PurchaseManagement\Controller\Adminhtml\Suppliers;
 
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
 
class Massdelete extends Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    protected $_helper;

    protected $filter;

    protected $_suppliers;

    /**
     * @param Context     $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Webkul\PurchaseManagement\Model\SuppliersFactory  $supplierFactory,
        \Webkul\PurchaseManagement\Helper\Data $helper,
        \Magento\Ui\Component\MassAction\Filter $filter
    ) {
        parent::__construct($context);
        $this->_resultPageFactory = $resultPageFactory;
        $this->_suppliers=$supplierFactory;
        $this->_helper=$helper;
        $this->filter=$filter;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {   
        $resultRedirect = $this->resultRedirectFactory->create();
        $collection = $this->filter->getCollection($this->_suppliers->create()->getCollection());
        $moveIds = $collection->getAllIds();

        if (!is_array($moveIds)) {
            $this->messageManager->addError(__('Please select item(s)'));
        } else {
            try {
                foreach ($moveIds as $moveId) {
                    $move = $this->_suppliers->create()->load($moveId);
                    $move->setId($moveId);
                    $move->delete();
                }
                $this->messageManager->addSuccess(__('Suppliers deleted successfully'));
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        return $resultRedirect->setPath('*/*/');
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
