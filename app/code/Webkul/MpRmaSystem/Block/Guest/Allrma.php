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
namespace Webkul\MpRmaSystem\Block\Guest;

use Webkul\MpRmaSystem\Model\ResourceModel\Details\CollectionFactory;

class Allrma extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Webkul\MpRmaSystem\Helper\Data
     */
    protected $_mpRmaHelper;

    /**
     * @var CollectionFactory
     */
    protected $_detailsCollection;

    /**
     * @var Webkul\MpRmaSystem\Model\ResourceModel\Details\Collection
     */
    protected $_rma;

    /**
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Webkul\MpRmaSystem\Helper\Data $mpRmaHelper
     * @param CollectionFactory $detailsCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Webkul\MpRmaSystem\Helper\Data $mpRmaHelper,
        CollectionFactory $detailsCollection,
        array $data = []
    ) {
        $this->_mpRmaHelper = $mpRmaHelper;
        $this->_detailsCollection = $detailsCollection;
        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->pageConfig->getTitle()->set(__('RMA Panel'));
    }

    /**
     * @return bool | collection object
     */
    public function getAllRma()
    {
        $email = $this->_mpRmaHelper->getGuestEmailId();
        if (!$this->_rma) {
            $collection = $this->_detailsCollection
                                ->create()
                                ->addFieldToFilter('customer_id', 0)
                                ->addFieldToFilter('customer_email', $email);
            $this->_rma = $collection;
        }

        $this->applyFilter();
        $sortingOrder = $this->_mpRmaHelper->getSortingOrder();
        $sortingField = $this->_mpRmaHelper->getSortingField();
        $this->_rma->setOrder($sortingField, $sortingOrder);

        return $this->_rma;
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getAllRma()) {
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'mprmasystem.rma.list.pager'
            )->setCollection(
                $this->getAllRma()
            );
            $this->setChild('pager', $pager);
            $this->getAllRma()->load();
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    public function applyFilter()
    {
        $this->_rma = $this->_mpRmaHelper->applyFilter($this->_rma);
    }
}
