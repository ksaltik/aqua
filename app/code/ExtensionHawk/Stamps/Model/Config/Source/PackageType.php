<?php
namespace ExtensionHawk\Stamps\Model\Config\Source;

use Magento\Shipping\Model\Carrier\Source\GenericInterface;

class PackageType
{
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('Divide to equal weight (one request)')],
            ['value' => 1, 'label' => __('Use origin weight (few requests)')],
            ['value' => 2, 'label' => __('Single Package(few requests)')]
        ];
    }
}
