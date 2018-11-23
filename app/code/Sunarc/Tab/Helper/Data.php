<?php


namespace Sunarc\Tab\Helper;

use Magento\Framework\App\Helper\AbstractHelper;


class Data extends AbstractHelper
{
    protected $resultPageFactory;
    protected $stockState;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(\Magento\Eav\Model\Entity\Attribute $entityAttribute,
                                \Magento\Framework\View\Result\PageFactory $resultPageFactory,
                                \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->scopeConfig = $scopeConfig;
        $this->_entityAttribute = $entityAttribute;

    }

    public function getAttributeInfo($entityType, $attributeCode)
    {
        return $this->_entityAttribute
            ->loadByCode($entityType, $attributeCode);
    }

    public function getTabFirstName()
    {
        $attributeCode = 'tab_one';
        $entityType = 'catalog_product';
        $attributeInfo = $this->getAttributeInfo($entityType, $attributeCode);
        return $attributeInfo->getFrontendLabel();
    }

    public function getTabTwoName()
    {
        $attributeCode = 'tab_two';
        $entityType = 'catalog_product';
        $attributeInfo = $this->getAttributeInfo($entityType, $attributeCode);
        return $attributeInfo->getFrontendLabel();
    }

    public function getTabThreeName()
    {
        $attributeCode = 'tab_three';
        $entityType = 'catalog_product';
        $attributeInfo = $this->getAttributeInfo($entityType, $attributeCode);
        return $attributeInfo->getFrontendLabel();
    }
}
