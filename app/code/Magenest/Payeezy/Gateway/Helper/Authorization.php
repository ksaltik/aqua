<?php
namespace Magenest\Payeezy\Gateway\Helper;

use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Payment\Gateway\ConfigInterface;

/**
 * Class Authorization
 * @package Magenest\Payeezy\Gateway\Helper
 */
class Authorization
{

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var string
     */
    protected $timestamp;

    /**
     * @var array
     */
    protected $params;

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var string
     */
    private $nonce;

    /**
     * Authorization constructor.
     *
     * @param DateTime $dateTime
     * @param ConfigInterface $config
     */
    public function __construct(
        DateTime $dateTime,
        ConfigInterface $config
    ) {
        $this->dateTime = $dateTime;
        $this->config = $config;
    }

    /**
     * @param $params
     * @return $this
     */
    public function setParameter($params)
    {
        $this->params = json_encode($params);

        return $this;
    }

    /**
     * @return array
     */
    public function getParameter()
    {
        return $this->params;
    }

    /**
     * Get Header
     *
     * @return array
     */
    public function getHeaders()
    {
        return [
            'Content-Type: application/json',
            'Authorization:' . $this->getAuthorization(),
            'apikey:' . $this->getApiKey(),
            'token:' . $this->getMerchantToken(),
            'nonce:' . $this->getNonce(),
            'timestamp:' . $this->getTimestamp(),
        ];
    }

    /**
     * @return string
     */
    private function getAuthorization()
    {
        $data = $this->getApiKey()
            . $this->getNonce()
            . $this->getTimestamp()
            . $this->getMerchantToken()
            . $this->getParameter();
        $hmac = hash_hmac('sha256', $data, $this->getApiSecret(), false);
        $authorization = base64_encode($hmac);

        return $authorization;
    }

    /**
     * @return string
     */
    protected function getNonce()
    {
        if ($this->nonce === null) {
            $this->nonce = (string)hexdec(bin2hex(openssl_random_pseudo_bytes(4, $strong)));
        }

        return $this->nonce;
    }

    /**
     * @return string
     */
    private function getTimestamp()
    {
        if ($this->timestamp === null) {
            $this->timestamp = (string)($this->dateTime->gmtTimestamp() * 1000);
        }

        return $this->timestamp;
    }

    /**
     * @return mixed
     */
    private function getApiKey()
    {
        return $this->config->getValue('api_key');
    }

    /**
     * @return mixed
     */
    private function getMerchantToken()
    {
        return $this->config->getValue('merchant_token');
    }

    /**
     * @return mixed
     */
    private function getApiSecret()
    {
        return $this->config->getValue('api_secret');
    }
}
