<?php
/**
 * Sunarc_AdvPermission extension
 * NOTICE OF LICENSE
 *
 * This source file is subject to the SunArc Technologies License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://sunarctechnologies.com/end-user-agreement/
 *
 * @category  Sunarc
 * @package   Sunarc_AdvPermission
 * @copyright Copyright (c) 2017
 * @license
 */
namespace Sunarc\AdvPermission\Ui\Component\Listing\Column;

use \Magento\Sales\Api\OrderRepositoryInterface;
use \Magento\Framework\View\Element\UiComponent\ContextInterface;
use \Magento\Framework\View\Element\UiComponentFactory;
use \Magento\Ui\Component\Listing\Columns\Column;
use \Magento\Framework\Api\SearchCriteriaBuilder;

class SplitAttribute extends Column
{
    protected $_orderRepository;
    protected $_searchCriteria;
    protected $eavConfig;
    public function __construct(ContextInterface $context, UiComponentFactory $uiComponentFactory, \Magento\Eav\Model\Config $eavConfig, OrderRepositoryInterface $orderRepository, SearchCriteriaBuilder $criteria, array $components = [], array $data = [])
    {
        $this->eavConfig=$eavConfig;
        $this->_orderRepository = $orderRepository;
        $this->_searchCriteria  = $criteria;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $uniqueSplitAttribute = [];
                $ordersplitAttributeValue=[];
                $orderSplitAttributeCode=[];
                $spliAttribute = '';
                $splitOptionLabel = [];

                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $orderItems = $objectManager->get('Magento\Sales\Model\Order\Item')->getCollection();
                $orderItems->addFieldToSelect(['split_attribute_value','split_attribute_code','order_id'])
                    ->addFieldToFilter('order_id', $item['entity_id']);

                foreach ($orderItems as $value) {
                    $ordersplitAttributeValue[] =$value->getSplitAttributeValue();
                    $orderSplitAttributeCode[] = $value->getSplitAttributeCode();
                }
                $uniqueSplitAttributeValue = array_unique($ordersplitAttributeValue);
                $uniqueSplitAttributeCode = array_unique($orderSplitAttributeCode);
                foreach ($uniqueSplitAttributeCode as $key => $value) {
                    $attributeDetails = $this->eavConfig->getAttribute("catalog_product", $value);
                    foreach ($uniqueSplitAttributeValue as $uniqueAttributeValuekey => $uniqueAttributeValue) {
                        if ($uniqueAttributeValue != '') {
                            $splitOptionLabel[] = ucfirst($attributeDetails->getSource()->getOptionText($uniqueAttributeValue));
                        }
                    }
                }
                $spliAttribute = implode(',', $splitOptionLabel);
                $item['split_attribute_value'] = $spliAttribute;
            }
        }

        return $dataSource;
    }
}
