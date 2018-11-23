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

class Movestatus implements OptionSourceInterface
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
                            'label' => 'New',
                            'value' => 0
                        ],
                        [
                            'label' => 'Ready To Receive',
                            'value' => 1
                        ],
                        [
                            'label' => 'Received',
                            'value' => 2
                        ],
                        [
                            'label' => 'Canceled',
                            'value' => 3
                        ]
                    ];
        return $options;
    }
}
