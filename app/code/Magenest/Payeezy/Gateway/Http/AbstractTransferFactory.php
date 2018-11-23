<?php
namespace Magenest\Payeezy\Gateway\Http;

use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\Http\TransferBuilder;
use Magento\Payment\Gateway\Http\TransferFactoryInterface;
use Magenest\Payeezy\Gateway\Helper\Authorization;

/**
 * Class AbstractTransferFactory
 * @package Magenest\Payeezy\Gateway\Http
 */
abstract class AbstractTransferFactory implements TransferFactoryInterface
{
    /**
     * @var ConfigInterface
     */
    protected $config;

    /**
     * @var TransferBuilder
     */
    protected $transferBuilder;

    /**
     * Authenticate & generate Headers
     *
     * @var Authorization
     */
    private $authorization;

    /**
     * AbstractTransferFactory constructor.
     *
     * @param ConfigInterface $config
     * @param TransferBuilder $transferBuilder
     * @param Authorization $authorization
     */
    public function __construct(
        ConfigInterface $config,
        TransferBuilder $transferBuilder,
        Authorization $authorization
    ) {
        $this->config = $config;
        $this->transferBuilder = $transferBuilder;
        $this->authorization = $authorization;
    }

    /**
     * @return bool
     */
    protected function isSandboxMode()
    {
        return (bool)$this->config->getValue('sandbox_flag');
    }

    /**
     * @return Authorization
     */
    protected function getAuthorization()
    {
        return $this->authorization;
    }
}
