<?php
namespace Magenest\Payeezy\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Magenest\Payeezy\Gateway\Validator\AbstractResponseValidator;

/**
 * Class TransactionIdDataBuilder
 * @package Magenest\Payeezy\Gateway\Request
 */
class TransactionIdDataBuilder extends AbstractDataBuilder implements BuilderInterface
{
    /**
     * @inheritdoc
     */
    public function build(array $buildSubject)
    {
        $paymentDO = \Magento\Payment\Gateway\Helper\SubjectReader::readPayment($buildSubject);
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $paymentDO->getPayment();
        $data = $payment->getAdditionalInformation();
        return [
            self::TRANSACTION_ID => $paymentDO->getPayment()->getParentTransactionId(),
            AbstractResponseValidator::TRANSACTION_TAG => $data[AbstractResponseValidator::TRANSACTION_TAG]
        ];
    }
}
