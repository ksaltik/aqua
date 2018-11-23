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

namespace Webkul\MpRmaSystem\Plugin\Model\Customer;

class Session
{
    /**
     * @var \Webkul\MpRmaSystem\Helper\Data
     */
    protected $_mpHelper;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @param \Webkul\MpRmaSystem\Helper\Data $helper
     */
    public function __construct(
        \Webkul\MpRmaSystem\Helper\Data $helper,
        \Magento\Framework\UrlInterface $urlBuilder
    ) {
        $this->_helper = $helper;
        $this->_urlBuilder = $urlBuilder;
    }

    /**
     * Insert title and number for concrete document type.
     *
     * @param string $url
     */
    public function beforeAuthenticate(
        \Magento\Customer\Model\Session $session,
        $url = null
    ) {
        if ($this->_helper->getIsSeparatePanel()) {
            $currentUrl = $this->_urlBuilder->getCurrentUrl();
            if (strpos($currentUrl, 'mprmasystem/seller') !== false) {
                $url = $this->_urlBuilder->getUrl("marketplace/account/login");
            }
        }

        return [$url];
    }
}
