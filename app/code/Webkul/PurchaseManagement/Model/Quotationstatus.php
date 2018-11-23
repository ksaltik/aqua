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

class Quotationstatus implements OptionSourceInterface
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
                            'label' => 'RFQ',
                            'value' => 1
                        ],
                        [
                            'label' => 'Confirm',
                            'value' => 2
                        ],
                        [
                            'label' => 'Done',
                            'value' => 3
                        ],
                        [
                            'label' => 'Canceled',
                            'value' => 4
                        ]
                    ];
        return $options;
    }
}
