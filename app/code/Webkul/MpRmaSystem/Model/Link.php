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
namespace Webkul\MpRmaSystem\Model;

use Magento\Framework\View\Element\Html\Link\Current;

class Link extends \Magento\Framework\View\Element\Html\Link\Current
{
    /**
     * Render block HTML.
     *
     * @return string
     */
    protected function _toHtml()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $helper = $objectManager->get('Webkul\MpRmaSystem\Helper\Data');
        if (!$helper->isSeller()) {
            return '';
        }

        return parent::_toHtml();
    }
}
