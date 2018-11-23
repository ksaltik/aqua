<?php
namespace ExtensionHawk\Stamps\Model\Config\Source;

use Magento\Shipping\Model\Carrier\Source\GenericInterface;

class Generic implements GenericInterface
{
    protected $configHelper;
    protected $_code = '';

    public function __construct(\ExtensionHawk\Stamps\Helper\Config $configHelper)
    {
        $this->configHelper = $configHelper;
    }

    public function toOptionArray()
    {
        $configData = $this->configHelper->getCode($this->_code);
        $arr = [];
        foreach ($configData as $code => $title) {
            if (isset($title['options'])) {
                if (is_array($title['options'])) {
                    foreach ($title['options'] as $optKey => $optVal) {
                        $arr[] = ['value' => $code.":".$optKey, 'label' => $title['label']." - ".$optVal];
                    }
                }
            } else {
                $arr[] = ['value' => $code, 'label' => $title];
            }
        }
        return $arr;
    }
}
