<?php
namespace Magenest\Payeezy\Gateway\Http\Converter;

use Magento\Payment\Gateway\Http\ConverterException;
use Magento\Payment\Gateway\Http\ConverterInterface;
use Psr\Log\LoggerInterface;

/**
 * Class JsonToArray
 *
 * @package Magenest\Paybox\Gateway\Http\Converter
 */
class JsonToArray implements ConverterInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * JsonToArray constructor.
     *
     * @param LoggerInterface $logger
     */
    public function __construct(
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
    }

    /**
     * Converts gateway response to array structure
     *
     * @param mixed $response
     * @return array
     * @throws ConverterException
     */
    public function convert($response)
    {
        $response = json_decode($response, true);
        if (!json_last_error()) {
            return $response;
        } else {
            $this->logger->critical('Can\'t read response from Payeezy');
            throw new ConverterException(__('Can\'t read response from Payeezy'));
        }
    }
}
