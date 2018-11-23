<?php
namespace ExtensionHawk\Stamps\Model\Config\Source;

use Magento\Shipping\Model\Carrier\Source\GenericInterface;

class PrintLayout
{
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('Default')],
            ['value' => 'Normal', 'label' => __('Normal')],
            ['value' => 'NormalLeft', 'label' => __('NormalLeft')],
            ['value' => 'NormalRight', 'label' => __('NormalRight')],
            ['value' => 'Normal4X6', 'label' => __('Normal4X6')],
            ['value' => 'Normal6X4', 'label' => __('Normal6X4')],
            ['value' => 'Normal75X2', 'label' => __('Normal75X2')],
            ['value' => 'NormalReceipt', 'label' => __('NormalReceipt')],
            ['value' => 'NormalCN22', 'label' => __('NormalCN22')]
        ];
    }
}
