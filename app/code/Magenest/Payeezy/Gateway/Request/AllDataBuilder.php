<?php
namespace Magenest\Payeezy\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;

/**
 * Class AllDataBuilder
 * @package Magenest\Payeezy\Gateway\Request
 */
class AllDataBuilder extends AbstractDataBuilder implements BuilderInterface
{
    /**
     * @param array $buildSubject
     * @return array
     */
    public function build(array $buildSubject)
    {
        return [
            'transaction_type' => 'authorize',
            'method' => 'credit_card',
            "amount" => "2526",
            "currency_code" => "USD",
            "credit_card" => [
                "card_number" => "4111111111111111",
                "exp_date" => "1020",
                "cvv" => "123"
            ]
        ];
    }
}
