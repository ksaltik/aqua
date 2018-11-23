<?php
namespace Magenest\Payeezy\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;

/**
 * Class AbstractDataBuilder
 * @package Magenest\Payeezy\Gateway\Request
 */
abstract class AbstractDataBuilder implements BuilderInterface
{
    /**
     * Capture
     */
    const AUTHORIZE_AND_CAPTURE = 'purchase';

    /**
     * Transaction type: Authorize
     */
    const AUTHORIZE = 'authorize';

    /**
     * Transaction Type: Pre Capture
     */
    const PRE_CAPTURE = 'capture';

    /**
     * Transaction Type: Refund
     */
    const REFUND = 'refund';

    /**
     * Void
     */
    const VOID = 'void';

    /**
     * Transaction Id
     */
    const TRANSACTION_ID = 'transaction_id';
}
