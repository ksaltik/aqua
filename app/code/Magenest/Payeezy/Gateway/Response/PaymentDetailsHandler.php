<?php
namespace Magenest\Payeezy\Gateway\Response;

use Magento\Sales\Model\Order\Payment;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magenest\Payeezy\Gateway\Validator\AbstractResponseValidator;

/**
 * Class PaymentDetailsHandler
 * @package Magenest\Payeezy\Gateway\Response
 */
class PaymentDetailsHandler implements HandlerInterface
{
    /**
     * @var array
     */
    private $additionalInformationMapping = [
        'transaction_type' => AbstractResponseValidator::TRANSACTION_TYPE,
        'transaction_id' => AbstractResponseValidator::TRANSACTION_ID,
        'response_code' => AbstractResponseValidator::RESPONSE_CODE,
        'transaction_tag' => AbstractResponseValidator::TRANSACTION_TAG
    ];

    /**
     * @param array $handlingSubject
     * @param array $response
     */
    public function handle(array $handlingSubject, array $response)
    {
        $paymentDO = \Magento\Payment\Gateway\Helper\SubjectReader::readPayment($handlingSubject);

        /** @var Payment $payment */
        $payment = $paymentDO->getPayment();
        \Magento\Payment\Gateway\Helper\ContextHelper::assertOrderPayment($payment);

        $payment->setTransactionId($response[AbstractResponseValidator::TRANSACTION_ID]);
        $payment->setLastTransId($response[AbstractResponseValidator::TRANSACTION_ID]);
        $payment->setIsTransactionClosed(false);

        foreach ($this->additionalInformationMapping as $informationKey => $responseKey) {
            if (isset($response[$responseKey])) {
                $payment->setAdditionalInformation($informationKey, $response[$responseKey]);
            }
        }
    }
}
