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
namespace Webkul\MpRmaSystem\Block\Email;

use Webkul\MpRmaSystem\Model\ResourceModel\Conversation\CollectionFactory;

class Items extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Webkul\MpRmaSystem\Helper\Data
     */
    protected $_mpRmaHelper;

    /**
     * @var CollectionFactory
     */
    protected $_conversationCollection;

    /**
     * @var Collection
     */
    protected $_conversations;

    /**
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Webkul\MpRmaSystem\Helper\Data $mpRmaHelper
     * @param CollectionFactory $conversationCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Webkul\MpRmaSystem\Helper\Data $mpRmaHelper,
        CollectionFactory $conversationCollection,
        array $data = []
    ) {
        $this->_mpRmaHelper = $mpRmaHelper;
        $this->_conversationCollection = $conversationCollection;
        parent::__construct($context, $data);
    }

    /**
     * @return bool | collection object
     */
    public function getAllItems()
    {
        return $this->_mpRmaHelper->getAllItems();
    }
}
