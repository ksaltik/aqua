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
namespace Webkul\MpRmaSystem\Model\Rma\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Webkul\MpRmaSystem\Helper\Data;
/**
 * Class OrderStatus
 */
class OrderStatus implements OptionSourceInterface
{
    /**
     * Get options.
     *
     * @return array
     */
    public function toOptionArray()
    {
        $availableOptions = [
                                Data::ORDER_NOT_DELIVERED => __(Data::ORDER_NOT_DELIVERED_LABEL),
                                Data::ORDER_DELIVERED => __(Data::ORDER_DELIVERED_LABEL),
                                Data::ORDER_NOT_APPLICABLE => __(Data::ORDER_NOT_APPLICABLE_LABEL),
                                
                            ];
        $options = [];
        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }

        return $options;
    }
}
