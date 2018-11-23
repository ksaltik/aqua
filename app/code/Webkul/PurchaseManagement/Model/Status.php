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
namespace Webkul\PurchaseManagement\Model;

use Magento\Framework\Data\OptionSourceInterface;

class Status implements OptionSourceInterface
{
    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
                        [
                            'label' => 'Disable',
                            'value' => 0
                        ],
                        [
                            'label' => 'Enable',
                            'value' => 1
                        ]
                    ];
        return $options;
    }

    public function getStatusArray() {
        return [
            0 => __("New"),
            1 => __("RFQ"),
            2 => __("Confirm"),
            3 => __("Done"),
            4 => __("Canceled")
        ];
    }

    public function getPickingStatusArray() {
        return [
            0 => __("New"),
            1 => __("Ready To Receive"),
            2 => __("Received"),
            3 => __("Canceled")
        ];
    }

    public function getOptionsStatusArray() {
        return [
            0 => __("Disabled"),
            1 => __("Enabled"),          
        ];
    }
}