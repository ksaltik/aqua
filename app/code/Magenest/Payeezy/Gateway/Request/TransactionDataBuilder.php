<?php
namespace Magenest\Payeezy\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;

/**
 * Class TransactionDataBuilder
 * @package Magenest\Payeezy\Gateway\Request
 */
class TransactionDataBuilder extends AbstractDataBuilder implements BuilderInterface
{
    /**
     * Transaction Type
     */
    const TRANSACTION_TYPE = 'transaction_type';

    /**
     * Method
     */
    const METHOD = 'method';

    /**
     * @var string
     */
    private $method;

    /**
     * @var string
     */
    private $transactionType;

    /**
     * TransactionDataBuilder constructor.
     *
     * @param $transactionType
     * @param string $method
     */
    public function __construct(
        $transactionType,
        $method = 'credit_card'
    ) {
        $this->method = $method;
        $this->transactionType = $transactionType;
    }

    /**
     * @inheritdoc
     */
    public function build(array $buildSubject)
    {
        return [
            self::TRANSACTION_TYPE => $this->transactionType,
            self::METHOD => $this->method,
        ];
    }
}
