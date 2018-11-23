<?php
/**
 * Created by Magenest.
 */
namespace Magenest\Payeezy\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class PaymentAction
 * @package Magenest\Payeezy\Model\Config\Source
 */
class PaymentAction implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'authorize',
                'label' => __('Authorize Only (Authorisation)'),
            ],
            [
                'value' => 'authorize_capture',
                'label' => __('Authorize and Capture (Sale)')
            ]
        ];
    }
}
