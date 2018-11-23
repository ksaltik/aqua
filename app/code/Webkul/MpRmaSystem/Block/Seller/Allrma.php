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
namespace Webkul\MpRmaSystem\Block\Seller;

use Webkul\MpRmaSystem\Model\ResourceModel\Details\CollectionFactory;

class Allrma extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute
     */
    protected $_eav;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var CollectionFactory
     */
    protected $_detailsCollection;

    /**
     * @var Webkul\MpRmaSystem\Model\ResourceModel\Details\Collection
     */
    protected $_allrma;

    /**
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute $eav
     * @param \Magento\Customer\Model\Session $customerSession
     * @param CollectionFactory $detailsCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute $eav,
        \Magento\Customer\Model\Session $customerSession,
        CollectionFactory $detailsCollection,
        \Webkul\MpRmaSystem\Helper\Data $mpRmaHelper,
        array $data = []
    ) {
        $this->_resource = $resource;
        $this->_eav = $eav;
        $this->_customerSession = $customerSession;
        $this->_detailsCollection = $detailsCollection;
        $this->_mpRmaHelper = $mpRmaHelper;
        parent::__construct($context, $data);
    }

    /**
     */
    protected function _construct()
    {
        parent::_construct();
        $this->pageConfig->getTitle()->set(__('Marketplace RMA'));
    }

    /**
     * @return bool | collection object
     */
    public function getAllRma()
    {
        if (!($sellerId = $this->_mpRmaHelper->getSellerId())) {
            return false;
        }

        if (!$this->_allrma) {
            $collection = $this->_detailsCollection
                                ->create()
                                ->addFieldToFilter('seller_id', $sellerId);
            $this->_allrma = $collection;
        }

        $this->applyFilter();
        $type = \Webkul\MpRmaSystem\Helper\Data::TYPE_SELLER;
        $sortingOrder = $this->_mpRmaHelper->getSortingOrder($type);
        $sortingField = $this->_mpRmaHelper->getSortingField($type);
        $this->_allrma->setOrder($sortingField, $sortingOrder);
        return $this->_allrma;
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
                'mprmasystem.allrma.list.pager'
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
        $type = \Webkul\MpRmaSystem\Helper\Data::TYPE_SELLER;
        $this->_allrma = $this->_mpRmaHelper->applyFilter($this->_allrma, $type);
    }
}
