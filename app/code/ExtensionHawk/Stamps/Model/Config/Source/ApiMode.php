<?php
namespace ExtensionHawk\Stamps\Model\Config\Source;

use Magento\Shipping\Model\Carrier\Source\GenericInterface;

class ApiMode
{
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('Live')],
        ];
    }
}
