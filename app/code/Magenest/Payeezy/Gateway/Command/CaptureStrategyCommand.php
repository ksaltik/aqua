<?php
namespace Magenest\Payeezy\Gateway\Command;

use Magento\Payment\Gateway\Command;
use Magento\Payment\Gateway\CommandInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Sales\Model\Order;

/**
 * Class CaptureStrategyCommand
 * @package Magenest\Payeezy\Gateway\Command
 */
class CaptureStrategyCommand implements CommandInterface
{
    /**
     * Payeezy sale command
     */
    const SALE = 'sale';

    /**
     * Payeezy pre capture command
     */
    const PRE_AUTH_CAPTURE = 'pre_auth_capture';

    /**
     * @var Command\CommandPoolInterface
     */
    private $commandPool;

    /**
     * CaptureStrategyCommand constructor.
     * @param Command\CommandPoolInterface $commandPool
     */
    public function __construct(
        \Magento\Payment\Gateway\Command\CommandPoolInterface $commandPool
    ) {
        $this->commandPool = $commandPool;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(array $commandSubject)
    {
        /** @var PaymentDataObjectInterface $paymentDO */
        $paymentDO = \Magento\Payment\Gateway\Helper\SubjectReader::readPayment($commandSubject);

        /** @var Order\Payment $paymentInfo */
        $paymentInfo = $paymentDO->getPayment();
        \Magento\Payment\Gateway\Helper\ContextHelper::assertOrderPayment($paymentInfo);

        if ($paymentInfo instanceof Order\Payment
            && $paymentInfo->getAuthorizationTransaction()
        ) {
            return $this->commandPool
                ->get(self::PRE_AUTH_CAPTURE)
                ->execute($commandSubject);
        }

        return $this->commandPool
            ->get(self::SALE)
            ->execute($commandSubject);
    }
}
