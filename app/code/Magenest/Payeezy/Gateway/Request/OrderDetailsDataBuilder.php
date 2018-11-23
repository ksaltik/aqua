<?php
namespace Magenest\Payeezy\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;

/**
 * Class OrderDetailsDataBuilder
 * @package Magenest\Payeezy\Gateway\Request
 */
class OrderDetailsDataBuilder extends AbstractDataBuilder implements BuilderInterface
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
        $paymentDO = \Magento\Payment\Gateway\Helper\SubjectReader::readPayment($buildSubject);
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $paymentDO->getPayment();
        $order = $payment->getOrder();

        return [
            self::MERCHANT_REF => $order->getIncrementId(),
            self::AMOUNT => round((float)\Magento\Payment\Gateway\Helper\SubjectReader::readAmount($buildSubject), 2) * 100,
            self::CURRENCY_CODE => $order->getOrderCurrencyCode(),
        ];
    }
}
