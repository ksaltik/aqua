<?php
namespace Magenest\Payeezy\Gateway\Http;

use Magenest\Payeezy\Gateway\Request\AbstractDataBuilder;

/**
 * Class TransferFactory
 * @package Magenest\Payeezy\Gateway\Http
 */
class TransferFactory extends AbstractTransferFactory
{
    /**
     * @inheritdoc
     */
    public function create(array $request)
    {
        $id = null;
        if (isset($request[AbstractDataBuilder::TRANSACTION_ID])) {
            $id = $request[AbstractDataBuilder::TRANSACTION_ID];
            unset($request[AbstractDataBuilder::TRANSACTION_ID]);
        }
        $header = $this->getAuthorization()
            ->setParameter($request)
            ->getHeaders();

        return $this->transferBuilder
            ->setMethod('POST')
            ->setHeaders($header)
            ->setBody(json_encode($request))
            ->setUri($this->getUrl($id))
            ->build();
    }

    /**
     * @param null $id
     * @return mixed
     */
    private function getUrl($id = null)
    {
        $prefix = $this->isSandboxMode() ? 'sandbox_' : '';
        $path = $prefix . 'payeezy_url';

        return $this->config->getValue($path) . $id;
    }
}
