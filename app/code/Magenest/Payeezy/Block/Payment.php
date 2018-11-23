<?php
namespace Magenest\Paybox\Block;

use Magento\Payment\Gateway\ConfigInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class Payment
 * @package Magenest\Paybox\Block
 */
class Payment extends Template
{
    const PAYBOX_CODE = 'paybox';

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * Constructor
     *
     * @param Context $context
     * @param ConfigInterface $config
     * @param array $data
     */
    public function __construct(
        Context $context,
        ConfigInterface $config,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->config = $config;
    }

    /**
     * @return string
     */
    public function getPaymentConfig()
    {
        return json_encode(
            [
                'code' => self::PAYBOX_CODE,
            ],
            JSON_UNESCAPED_SLASHES
        );
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return self::PAYBOX_CODE;
    }

    /**
     * @inheritdoc
     */
    public function toHtml()
    {
        return parent::toHtml();
    }
}
