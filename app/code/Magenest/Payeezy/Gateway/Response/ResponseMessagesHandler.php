<?php
namespace Magenest\Payeezy\Gateway\Response;

use Magento\Sales\Model\Order\Payment;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Helper\ContextHelper;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magenest\Payeezy\Gateway\Validator\AbstractResponseValidator;

/**
 * Class ResponseMessagesHandler
 * @package Magenest\Payeezy\Gateway\Response
 */
class ResponseMessagesHandler implements HandlerInterface
{

    /**
     * @inheritdoc
     */
    public function handle(array $handlingSubject, array $response)
    {
        $paymentDO = SubjectReader::readPayment($handlingSubject);
        /** @var Payment $payment */
        $payment = $paymentDO->getPayment();
        ContextHelper::assertOrderPayment($payment);

        $responseCode = $response[AbstractResponseValidator::RESPONSE_CODE];
        $messages = $response[AbstractResponseValidator::RESPONSE_MESSAGE];
        $state = $this->getState($responseCode);

        if ($state) {
            $payment->setAdditionalInformation(
                'approve_messages',
                $messages
            );
        } else {
            $payment->setIsTransactionPending(false);
            $payment->setIsFraudDetected(true);
            $payment->setAdditionalInformation('error_messages', $messages);
        }
    }

    /**
     * @param int $responseCode
     * @return boolean
     */
    protected function getState($responseCode)
    {
        if ((string)$responseCode !== '00') {
            return false;
        }
        return true;
    }
}
