<?php
namespace Magenest\Payeezy\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Gateway\Helper\SubjectReader;

/**
 * Class VoidDataBuilder
 * @package Magenest\Payeezy\Gateway\Request
 */
class VoidDataBuilder extends AbstractDataBuilder implements BuilderInterface
{
    /**
     * Merchant Ref
     */
    const MERCHANT_REF = 'merchant_ref';

    /**
     * Amount
     */
    const AMOUNT = 'amount';

    /**
     * Currency Code
     */
    const CURRENCY_CODE = 'currency_code';

    /**
     * @inheritdoc
     */
    public function build(array $buildSubject)
    {
        $paymentDO = SubjectReader::readPayment($buildSubject);
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $paymentDO->getPayment();
        $order = $payment->getOrder();

        return [
            self::MERCHANT_REF => $order->getIncrementId(),
            self::AMOUNT => round((float)$order->getGrandTotal(), 2) * 100,
            self::CURRENCY_CODE => $order->getOrderCurrencyCode(),
        ];
    }
}
