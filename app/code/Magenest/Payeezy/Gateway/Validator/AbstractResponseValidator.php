<?php
namespace Magenest\Payeezy\Gateway\Validator;

use Magento\Payment\Gateway\Validator\AbstractValidator;

/**
 * Class AbstractResponseValidator
 * @package Magenest\Payeezy\Gateway\Validator
 */
abstract class AbstractResponseValidator extends AbstractValidator
{

    /**
     * The amount that was authorised for this transaction
     */
    const TOTAL_AMOUNT = 'amount';

    /**
     * The transaction type that this transaction was processed under
     * One of: Purchase, MOTO, Recurring
     */
    const TRANSACTION_TYPE = 'transaction_type';

    /**
     * A unique identifier that represents the transaction in eWAY’s system
     */
    const TRANSACTION_ID = 'transaction_id';

    /**
     * A code that describes the result of the action performed
     */
    const RESPONSE_MESSAGE = 'gateway_message';

    /**
     * The two digit response code returned from the bank
     */
    const RESPONSE_CODE = 'gateway_resp_code';

    /**
     * Transaction Tag
     * Needed as part of the payload to process secondary transactions like capture/void/refund/recurring/split-shipment.
     */
    const TRANSACTION_TAG = 'transaction_tag';

    /**
     * Value of response code
     */
    const RESPONSE_CODE_ACCEPT = '00';

    /**
     * @param array $response
     * @return bool
     */
    protected function validateErrors(array $response)
    {
        return isset($response[self::RESPONSE_CODE]) && (string)$response[self::RESPONSE_CODE] == self::RESPONSE_CODE_ACCEPT;
    }

    /**
     * @param array $response
     * @param array|number|string $amount
     * @return bool
     */
    protected function validateTotalAmount(array $response, $amount)
    {
        return isset($response[self::TOTAL_AMOUNT])
        && (float)$response[self::TOTAL_AMOUNT] === (float)$amount;
    }

    /**
     * @param array $response
     * @return bool
     */
    protected function validateTransactionType(array $response)
    {
        return !empty($response[self::TRANSACTION_TYPE]);
    }

    /**
     * @param array $response
     * @return bool
     */
    protected function validateTransactionId(array $response)
    {
        return !empty($response[self::TRANSACTION_ID]);
    }

    /**
     * @param array $response
     * @return bool
     */
    protected function validateResponseCode(array $response)
    {
        return isset($response[self::RESPONSE_CODE]);
    }

    /**
     * @param array $response
     * @return bool
     */
    protected function validateResponseMessage(array $response)
    {
        return !empty($response[self::RESPONSE_MESSAGE]);
    }
}
