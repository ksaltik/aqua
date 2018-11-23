<?php
namespace Magenest\Payeezy\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Data\AddressAdapterInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magenest\Payeezy\Observer\DataAssignObserver;
use Magento\Framework\Exception\NotFoundException;

/**
 * Class CardDetailsDataBuilder
 * @package Magenest\Payeezy\Gateway\Request
 */
class CardDetailsDataBuilder extends AbstractDataBuilder implements BuilderInterface
{

    const CREDIT_CARD = 'credit_card';

    /**
     * Card Holder Name
     */
    const CARDHOLDER_NAME = 'cardholder_name';

    /**
     * Card Type
     */
    const TYPE = 'type';

    /**
     * Card Number
     */
    const CARD_NUMBER = 'card_number';

    /**
     * Expired date
     */
    const EXP_DATE = 'exp_date';

    /**
     * Cvv card
     */
    const CVV = 'cvv';

    /**
     * @inheritdoc
     */
    public function build(array $buildSubject)
    {
        $paymentDO = SubjectReader::readPayment($buildSubject);

        $payment = $paymentDO->getPayment();
        $order = $paymentDO->getOrder();
        $address = $order->getBillingAddress();
        $data = $payment->getAdditionalInformation();
        $cardType = $data[OrderPaymentInterface::CC_TYPE];
        $month = $this->formatMonth($data[OrderPaymentInterface::CC_EXP_MONTH]);
        $year = $this->formatYear($data[OrderPaymentInterface::CC_EXP_YEAR]);
        $cardNumber = $payment->decrypt($data[OrderPaymentInterface::CC_NUMBER_ENC]);
        $cvv = $payment->decrypt($data[DataAssignObserver::CC_CID_ENC]);
        return [
            self::CREDIT_CARD => [
                self::TYPE => $this->getCardType($cardType),
                self::CARDHOLDER_NAME => $this->getName($address),
                self::CARD_NUMBER => $cardNumber,
                self::EXP_DATE => $month . $year,
                self::CVV => $cvv
            ]
        ];
    }

    /**
     * Convert Cart Type
     *
     * @param $cardType
     * @return mixed
     * @throws NotFoundException
     */
    private function getCardType($cardType)
    {
        $listCard = [
            'MC' => 'Mastercard',
            'AE' => 'American Express',
            'VI' => 'Visa',
            'DI' => 'Discover',
            'JCB' => 'JCB',
            'DN' => 'Diners Club'
        ];

        if (array_key_exists($cardType, $listCard)) {
            return $listCard[$cardType];
        } else {
            throw new NotFoundException(__('We don\'t support Card Type ' . $cardType));
        }
    }

    /**
     * Get full customer name
     *
     * @param AddressAdapterInterface $address
     * @return string
     */
    private function getName(AddressAdapterInterface $address)
    {
        $name = '';
        if ($address->getPrefix()) {
            $name .= $address->getPrefix() . ' ';
        }
        $name .= $address->getFirstname();
        if ($address->getMiddlename()) {
            $name .= ' ' . $address->getMiddlename();
        }
        $name .= ' ' . $address->getLastname();
        if ($address->getSuffix()) {
            $name .= ' ' . $address->getSuffix();
        }
        return $name;
    }


    /**
     * @param string $month
     * @return null|string
     */
    private function formatMonth($month)
    {
        return !empty($month) ? sprintf('%02d', $month) : null;
    }

    /**
     * @param $year
     * @return null|string
     */
    private function formatYear($year)
    {
        return !empty($year) ? substr($year, -2, 2) : null;
    }
}
