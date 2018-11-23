<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_SlideTweet
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\SlideTweet\Model\Config\Source;

class Effects implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $data = [
                    ['value' => '1', 'label' => __('Enable')],
                    ['value' => '0', 'label' => __('Disable')]

                ];
        return $data;
    }
}
