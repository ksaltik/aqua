<?php
namespace Magenest\Payeezy\Observer;

use Magento\Framework\Event\Observer;
use Magento\Payment\Observer\AbstractDataAssignObserver;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;

/**
 * Class DataAssignObserver
 * @package Magenest\Payeezy\Observer
 */
class DataAssignObserver extends AbstractDataAssignObserver
{
    /**
     * Encrypt card number and Cvv number
     */
    const CC_NUMBER = 'cc_number';
    const CC_CID = 'cc_cid';
    const CC_CID_ENC = 'cc_cid_enc';
    const CC_MONTH = 'cc_exp_month';
    const CC_YEAR = 'cc_exp_year';

    /**
     * @var array
     */
    protected $additionalInformationList = [
        self::CC_NUMBER,
        self::CC_CID,
        OrderPaymentInterface::CC_TYPE,
        OrderPaymentInterface::CC_EXP_MONTH,
        OrderPaymentInterface::CC_EXP_YEAR,
    ];

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $data = $this->readDataArgument($observer);

        $additionalData = $data->getData(PaymentInterface::KEY_ADDITIONAL_DATA);
        if (!is_array($additionalData)) {
            return;
        }

        $paymentInfo = $this->readPaymentModelArgument($observer);

        foreach ($this->additionalInformationList as $additionalInformationKey) {
            $value = isset($additionalData[$additionalInformationKey])
                ? $additionalData[$additionalInformationKey]
                : null;

            if ($value === null) {
                continue;
            }

            if ($additionalInformationKey == self::CC_MONTH) {
                if ($value > 12) {
                    $value = $value % 12;
                    if ($value == 0) {
                        $value = $value + 12;
                    } else {
                        $value = sprintf("%02d", $value % '12');
                    }
                }
                $paymentInfo->setAdditionalInformation(
                    OrderPaymentInterface::CC_EXP_MONTH,
                    $paymentInfo->encrypt($value)
                );
            }
            if ($additionalInformationKey == self::CC_YEAR) {
                $year = date('Y');
                if ($value > $year + 10) {
                    $diff = $value-$year;
                    $realDiff = sprintf("%02d", $diff % '11');
                    $value = $year + $realDiff;
                }
                $paymentInfo->setAdditionalInformation(
                    OrderPaymentInterface::CC_EXP_YEAR,
                    $paymentInfo->encrypt($value)
                );
            }
            if ($additionalInformationKey == self::CC_NUMBER) {
                $paymentInfo->setAdditionalInformation(
                    OrderPaymentInterface::CC_NUMBER_ENC,
                    $paymentInfo->encrypt($value)
                );

                continue;
            } elseif ($additionalInformationKey == self::CC_CID) {
                $paymentInfo->setAdditionalInformation(
                    self::CC_CID_ENC,
                    $paymentInfo->encrypt($value)
                );

                continue;
            }
            $paymentInfo->setAdditionalInformation(
                $additionalInformationKey,
                $value
            );
        }
    }
}
