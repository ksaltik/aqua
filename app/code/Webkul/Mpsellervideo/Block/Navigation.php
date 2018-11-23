<?php
/**
 * Webkul Software
 *
 * @category  Webkul
 * @package   Webkul_Mpsellervideo
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Mpsellervideo\Block;

/**
 * Webkul Mpsellervideo Video Navigation Block
 */

class Navigation extends \Magento\Framework\View\Element\Template
{
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * [getIsSecure check is secure or not]
     *
     * @return [boolean]
     */
    public function getIsSecure()
    {
        return $this->getRequest()->isSecure();
    }
}
