<?php
namespace Magenest\Payeezy\Model\Config\Source;

use Magento\Payment\Model\Source\Cctype as PaymentCctype;

/**
 * Class Cctype
 * @package Magenest\Payeezy\Model\Config\Source
 */
class Cctype extends PaymentCctype
{
    /**
     * @return array
     */
    public function getAllowedTypes()
    {
        return ['AE', 'VI', 'MC', 'DI', 'JCB', 'DN'];
    }

    /**
     * Geting credit cards types
     *
     * @return array
     */
    public function getCcTypes()
    {
        return $this->_paymentConfig->getCcTypes();
    }
}
