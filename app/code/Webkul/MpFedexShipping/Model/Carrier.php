<?php
/**
 * Webkul Software.
 *
 * @category Webkul
 * @package Webkul_MpFedexShipping
 * @author Webkul
 * @copyright Copyright (c) 2010-2016 Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */
// @codingStandardsIgnoreFile
namespace Webkul\MpFedexShipping\Model;

use Magento\Framework\Module\Dir;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Rate\Result;
use Magento\Framework\Xml\Security;
use Magento\Framework\Session\SessionManager;
use Magento\Shipping\Model\Shipping\LabelGenerator;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Serialize\Serializer\Json;
use Webkul\MarketplaceBaseShipping\Model\Carrier\AbstractCarrierOnline;
use Magento\Fedex\Model\Carrier as FedexCarrier;
use Webkul\MarketplaceBaseShipping\Model\ShippingSettingRepository;
/**
 * Marketplace Fedex shipping.
 *
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Carrier extends AbstractCarrierOnline
{
    /**
     * Code of the carrier.
     *
     * @var string
     */
    const CODE = 'mpfedex';
    /**
     * Code of the carrier.
     *
     * @var string
     */
    protected $_code = self::CODE;
    /**
     * @var ObjectManagerInterface
     */
    protected $_objectManager = null;
    /**
     * [$_coreSession description].
     *
     * @var [type]
     */
    protected $_coreSession;
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;
    /**
     * @var [type]
     */
    protected $_region;
    /**
     * @var LabelGenerator
     */
    protected $_labelGenerator;
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;
    /**
     * Rate result data.
     *
     * @var Result|null
     */
    protected $_result = null;
    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $_customerFactory;

    /**
     * @var string
     */
    protected $_rateServiceWsdl;

    /**
     * @var string
     */
    protected $_shipServiceWsdl;

    /**
     * @var string
     */
    protected $_totalPriceArr = [];

    /**
     * @var boolean
     */
    protected $_check = false;

    /**
     * @var boolean
     */
    protected $_flag = false;

    /**
     * @var array
     */
    protected $_fedexConfiguration = [
        'meter_number' => 'fedex_meter_number',
        'account' => 'fedex_account_id',
        'password' => 'fedex_password',
        'key' => 'fedex_key',
    ];

     /**
     * Types of rates, order is important
     *
     * @var array
     */
    protected $rateOrder = [
        'RATED_ACCOUNT_PACKAGE',
        'PAYOR_ACCOUNT_PACKAGE',
        'RATED_ACCOUNT_SHIPMENT',
        'PAYOR_ACCOUNT_SHIPMENT',
        'RATED_LIST_PACKAGE',
        'PAYOR_LIST_PACKAGE',
        'RATED_LIST_SHIPMENT',
        'PAYOR_LIST_SHIPMENT',
    ];

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface             $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory     $rateErrorFactory
     * @param \Psr\Log\LoggerInterface                                       $logger
     * @param Security                                                       $xmlSecurity
     * @param \Magento\Shipping\Model\Simplexml\ElementFactory               $xmlElFactory
     * @param \Magento\Shipping\Model\Rate\ResultFactory                     $rateFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory    $rateMethodFactory
     * @param \Magento\Shipping\Model\Tracking\ResultFactory                 $trackFactory
     * @param \Magento\Shipping\Model\Tracking\Result\ErrorFactory           $trackErrorFactory
     * @param \Magento\Shipping\Model\Tracking\Result\StatusFactory          $trackStatusFactory
     * @param \Magento\Directory\Model\RegionFactory                         $regionFactory
     * @param \Magento\Directory\Model\CountryFactory                        $countryFactory
     * @param \Magento\Directory\Model\CurrencyFactory                       $currencyFactory
     * @param \Magento\Directory\Helper\Data                                 $directoryData
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface           $stockRegistry
     * @param \Magento\Store\Model\StoreManagerInterface                     $storeManager
     * @param \Magento\Framework\Module\Dir\Reader                           $configReader
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Framework\ObjectManagerInterface                      $objectManager
     * @param SessionManager                                                 $coreSession
     * @param \Magento\Customer\Model\Session                                $customerSession
     * @param LabelGenerator                                                 $labelGenerator
     * @param array                                                          $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        Security $xmlSecurity,
        \Magento\Shipping\Model\Simplexml\ElementFactory $xmlElFactory,
        \Magento\Shipping\Model\Rate\ResultFactory $rateFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        \Magento\Shipping\Model\Tracking\ResultFactory $trackFactory,
        \Magento\Shipping\Model\Tracking\Result\ErrorFactory $trackErrorFactory,
        \Magento\Shipping\Model\Tracking\Result\StatusFactory $trackStatusFactory,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Directory\Helper\Data $directoryData,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Webkul\Marketplace\Helper\Orders $marketplaceOrderHelper,
        \Webkul\Marketplace\Model\ProductFactory $marketplaceProductFactory,
        \Webkul\Marketplace\Model\SaleslistFactory $saleslistFactory,
        ShippingSettingRepository $shippingSettingRepository,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Customer\Model\AddressFactory $addressFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Shipping\Helper\Carrier $carrierHelper,
        \Magento\Quote\Model\Quote\Item\OptionFactory $quoteOptionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Module\Dir\Reader $configReader,
        \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\RequestInterface $requestInterface,
        SessionManager $coreSession,
        \Magento\Framework\App\Request\Http $requestParam,
        \Magento\Customer\Model\Session $customerSession,
        LabelGenerator $labelGenerator,
        FedexCarrier $fedexCarrier,
        \Webkul\MpFedexShipping\Logger\Logger $fedexLogger,
        array $data = [],
        Json $serializer = null
    ) {
        $this->_storeManager = $storeManager;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->requestInterface = $requestInterface;
        parent::__construct(
            $scopeConfig,
            $rateErrorFactory,
            $logger,
            $xmlSecurity,
            $xmlElFactory,
            $rateFactory,
            $rateMethodFactory,
            $trackFactory,
            $trackErrorFactory,
            $trackStatusFactory,
            $regionFactory,
            $countryFactory,
            $currencyFactory,
            $directoryData,
            $stockRegistry,
            $marketplaceOrderHelper,
            $marketplaceProductFactory,
            $saleslistFactory,
            $shippingSettingRepository,
            $productFactory,
            $addressFactory,
            $customerFactory,
            $customerSession,
            $requestParam,
            $quoteOptionFactory,
            $storeManager,
            $requestInterface,
            $httpClientFactory,
            $carrierHelper,
            $labelGenerator,
            $coreSession,
            $data
        );
        $this->fedexLogger = $fedexLogger;
        $this->fedexCarrier = $fedexCarrier;
        $this->_objectManager = $objectManager;
        $wsdlBasePath = $configReader->getModuleDir(Dir::MODULE_ETC_DIR, 'Webkul_MpFedexShipping') . '/wsdl/';
        $this->_shipServiceWsdl = $wsdlBasePath . 'ShipService_v19.wsdl';
        $this->_rateServiceWsdl = $wsdlBasePath . 'RateService_v20.wsdl';
        $this->serializer = $serializer ?: ObjectManager::getInstance()->get(Json::class);
    }

    /**
     * Collect and get rates.
     *
     * @param RateRequest $request
     *
     * @return \Magento\Quote\Model\Quote\Address\RateResult\Error|bool|Result
     */
    public function collectRates(RateRequest $request)
    {
        if (!$this->canCollectRates() || $this->isMultiShippingActive()) {
            return false;
        }
        $this->setRequest($request);

        return $this->getShippingPricedetail($this->_rawRequest);
    }

    /**
     * Create soap client with selected wsdl
     *
     * @param string $wsdl
     * @param bool|int $trace
     * @return \SoapClient
     */
    protected function _createSoapClient($wsdl, $trace = false)
    {
        $client = new \SoapClient($wsdl, ['trace' => $trace]);
        $client->__setLocation(
            $this->getConfigFlag(
                'sandbox_mode'
            ) ? $this->getConfigData('sandbox_webservices_url') : $this->getConfigData('production_webservices_url')
        );

        return $client;
    }

    /**
     * Create rate soap client
     *
     * @return \SoapClient
     */
    protected function _createRateSoapClient()
    {
        return $this->_createSoapClient($this->_rateServiceWsdl);
    }

    /**
     * Create ship soap client
     *
     * @return \SoapClient
     */
    protected function _createShipSoapClient()
    {
        return $this->_createSoapClient($this->_shipServiceWsdl, 1);
    }


    /**
     * set the configuration values.
     *
     * @param \Magento\Framework\DataObject $request
     */
    public function setConfigData(\Magento\Framework\DataObject $request)
    {
        $r = $request;

        $r->setFedexMeterNumber($this->getConfigData('meter_number'));
        $r->setFedexKey($this->getConfigData('key'));
        $r->setFedexPassword($this->getConfigData('password'));

        if ($request->getFedexAccount()) {
            $account = $request->getFedexAccount();
        } else {
            $account = $this->getConfigData('account');
        }
        $r->setFedexAccount($account);

        if ($request->getFedexDropoff()) {
            $dropoff = $request->getFedexDropoff();
        } else {
            $dropoff = $this->getConfigData('dropoff');
        }
        $r->setDropoffType($dropoff);

        if ($request->getFedexPackaging()) {
            $packaging = $request->getFedexPackaging();
        } else {
            $packaging = $this->getConfigData('packaging');
        }
        $r->setPackaging($packaging);

        return $r;
    }

    /**
     * set seller credentials if he/she has.
     *
     * @param RateRequest $request
     * @param int                           $sellerId
     *
     * @return \Magento\Framework\DataObject
     */
    protected function _isSellerHasOwnCredentials(\Magento\Framework\DataObject $request, $sellerId)
    {
        if (!$this->getConfigData('allow_seller')) {
            return $request;
        }
        $customer = $this->customerFactory->create()->load($sellerId);
        $configuration = [];
        foreach ($this->_fedexConfiguration as $config => $attribute) {
            if (isset($customer[$attribute])) {
                $request->setData($attribute, $customer[$attribute]);
            } elseif ($this->getConfigData($config) == '' && !isset($customer[$attribute])) {
                $request->setData($attribute, '');
            }
        }

        return $request;
    }
    /**
     * Makes remote request to the carrier and returns a response.
     *
     * @param string $purpose
     *
     * @return mixed
     */
    protected function _createRatesRequest(\Magento\Framework\DataObject $request, $ratesRequest)
    {
        $debugData = ['request' => $ratesRequest];
        try {
            $client = $this->_createRateSoapClient();
            $response = $client->getRates($ratesRequest);
            $debugData['result'] = $response;
        } catch (\Exception $e) {
            $this->fedexLogger->info($e->getMessage());
            $debugData['result'] = ['error' => $e->getMessage(), 'code' => $e->getCode()];
            $this->_logger->critical($e);
        }
        
        $this->_debug($debugData);
        return $response;
    }
    /**
     * Get version of rates request
     *
     * @return array
     */
    public function getVersionInfo()
    {
        return ['ServiceId' => 'crs', 'Major' => '20', 'Intermediate' => '0', 'Minor' => '0'];
    }

    protected function _formatRateRequest($request, $shipdetail, $purpose)
    {
        $currency = $this->_storeManager->getStore()->getBaseCurrencyCode();
        if ($shipdetail['origin_country_id'] == 'IN' && $request->getDestCountryId() == 'IN') {
            $currency = 'INR';
        }
        $params = [
            'WebAuthenticationDetail' => [
                'UserCredential' => ['Key' => $request->getFedexKey(), 'Password' => $request->getFedexPassword()],
            ],
            'ClientDetail' => [
                'AccountNumber' => $request->getFedexAccount(),
                'MeterNumber' => $request->getFedexMeterNumber(),
            ],
            'Version' => $this->getVersionInfo(),
            'RequestedShipment' => [
                'DropoffType' => $request->getDropoffType(),
                'ShipTimestamp' => date('c'),
                'PackagingType' => $request->getPackaging(),
                'TotalInsuredValue' => [
                    'Amount' => $shipdetail['price'] * $shipdetail['qty'],
                    'Currency' => $currency
                ],
                'Shipper' => [
                    'Address' => [
                        'PostalCode' => $shipdetail['origin_postcode'],
                        'CountryCode' => $shipdetail['origin_country_id'],
                    ],
                ],
                'Recipient' => [
                    'Address' => [
                        'PostalCode' => $request->getDestPostal(),
                        'CountryCode' => $request->getDestCountryId(),
                        'Residential' => (bool) $this->getConfigData('residence_delivery'),
                    ],
                ],
                'ShippingChargesPayment' => [
                    'PaymentType' => 'SENDER',
                    'Payor' => [
                        'AccountNumber' => $request->getFedexAccount(),
                        'CountryCode' => $shipdetail['origin_country_id'],
                    ],
                ],
                'CustomsClearanceDetail' => [
                    'CustomsValue' => [
                        'Amount' => $shipdetail['price'] * $shipdetail['qty'],
                        'Currency' => $currency
                    ],
                    'CommercialInvoice' => [
                        'Purpose' => 'SOLD',
                    ]
                ],
                'RateRequestTypes' => 'LIST',
                'PackageCount' => '1',
                'PackageDetail' => 'INDIVIDUAL_PACKAGES',
                'RequestedPackageLineItems' => [
                    '0' => [
                        'Weight' => [
                            'Value' => (double) $shipdetail['items_weight'],
                            'Units' => $this->getConfigData('unit_of_measure'),
                        ],
                        'GroupPackageCount' => 1,
                    ],
                ],
            ],
        ];

        if ($purpose == FedexCarrier::RATE_REQUEST_GENERAL) {
            $params['RequestedShipment']['RequestedPackageLineItems'][0]['InsuredValue'] = [
                'Amount' => $shipdetail['price'] * $shipdetail['qty'],
                'Currency' => $currency,
            ];
        } else {
            if ($purpose == FedexCarrier::RATE_REQUEST_SMARTPOST) {
                $params['RequestedShipment']['ServiceType'] = FedexCarrier::RATE_REQUEST_SMARTPOST;
                $params['RequestedShipment']['SmartPostDetail'] = [
                    'Indicia' => (double)$shipdetail['items_weight'] >= 1 ? 'PARCEL_SELECT' : 'PRESORTED_STANDARD',
                    'HubId' => $this->getConfigData('smartpost_hubid'),
                ];
            }
        }
        return $params;
    }

    /**
     * Build RateV3 request, send it to Fedex gateway and retrieve quotes in XML format.
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @return Result
     */
    public function getShippingPricedetail(\Magento\Framework\DataObject $request)
    {
        $this->setConfigData($request);
        $r = $request;
        $submethod = [];
        $shippinginfo = [];
        $totalpric = [];
        $serviceCodeToActualNameMap = [];
        $costArr = [];
        $debugData = [];
        $price = 0;
        $flag = false;
        $check = false;
        foreach ($r->getShippingDetails() as $shipdetail) {
            $priceArr = [];
            $currency = $this->_storeManager->getStore()->getBaseCurrencyCode();
            if ($shipdetail['origin_country_id'] == 'IN' && $r->getDestCountryId() == 'IN') {
                $currency = 'INR';
            }
            $this->_isSellerHasOwnCredentials($request, $shipdetail['seller_id']);
            
            $allowedMethods = explode(',', $this->getConfigData('allowed_methods'));
            
            if (in_array(FedexCarrier::RATE_REQUEST_SMARTPOST, $allowedMethods)) {
                $ratesRequest = $this->_formatRateRequest($request, $shipdetail, FedexCarrier::RATE_REQUEST_SMARTPOST);
                $ratesRequest['RequestedShipment']['ServiceType'] = FedexCarrier::RATE_REQUEST_SMARTPOST;
                $ratesRequest['RequestedShipment']['SmartPostDetail'] = [
                    'Indicia' => (double) $shipdetail['items_weight'] >= 1 ? 'PARCEL_SELECT' : 'PRESORTED_STANDARD',
                    'HubId' => $this->getConfigData('smartpost_hubid'),
                ];
                $response = $this->_createRatesRequest($request, $ratesRequest);
                $smartpostResult = $this->_parseQuoteResponse($response);
                
            }

            $ratesRequest = $this->_formatRateRequest($request, $shipdetail, FedexCarrier::RATE_REQUEST_GENERAL);
            //if ($request->getPurpose() == FedexCarrier::RATE_REQUEST_GENERAL) {
                $ratesRequest['RequestedShipment']['RequestedPackageLineItems'][0]['InsuredValue'] = [
                    'Amount' => $shipdetail['price'] * $shipdetail['qty'],
                    'Currency' => $currency,
                ];
                $response = $this->_createRatesRequest($request, $ratesRequest);
                $generalResult = $this->_parseQuoteResponse($response);
            //} 

            if (!empty($smartpostResult)) {
                $priceArr = array_merge($smartpostResult, $generalResult);
            } else {
                $priceArr = $generalResult;
            }

            $this->_filterSellerRate($priceArr);
            if ($this->_flag) {
                $debugData['result'] = ['error' => 1];
                if ($this->_scopeConfig->getValue('carriers/mpmultishipping/active')) {
                    return [];
                } else {
                    return $this->_parseXmlResponse($debugData);
                }
            }
            $submethod = [];

            foreach ($priceArr as $index => $price) {
                $submethod[$index] = [
                    'method' => $this->getCode('method', $index).' (Fedex)',
                    'cost' => $price,
                    'base_amount' => $price,
                    'error' => 0,
                ];
            }
            array_push(
                $shippinginfo,
                [
                    'seller_id' => $shipdetail['seller_id'],
                    'methodcode' => $this->_code,
                    'shipping_ammount' => $price,
                    'product_name' => $shipdetail['product_name'],
                    'submethod' => $submethod,
                    'item_ids' => $shipdetail['item_id'],
                ]
            );
        }
        $totalpric = ['totalprice' => $this->_totalPriceArr, 'costarr' => $costArr];
        $debugData['result'] = $totalpric;
        $result = ['handlingfee' => $totalpric, 'shippinginfo' => $shippinginfo, 'error' => 0];
        $shippingAll = $this->_coreSession->getShippingInfo();
        $shippingAll[$this->_code] = $result['shippinginfo'];
        $this->_coreSession->setShippingInfo($shippingAll);

        if ($this->_scopeConfig->getValue('carriers/mpmultishipping/active')) {
            return $result;
        } else {
            return $this->_parseXmlResponse($totalpric);
        }
    }

    protected function _parseQuoteResponse($response)
    {
        $costArr = [];
        $priceArr = [];
        $errorTitle = 'For some reason we can\'t retrieve tracking info right now.';
        if (is_object($response)) {
            if ($response->HighestSeverity == 'FAILURE' ||
                $response->HighestSeverity == 'ERROR'
            ) {
                if (is_array($response->Notifications)) {
                    $notification = array_pop($response->Notifications);
                    $errorTitle = (string) $notification->Message;
                } else {
                    $errorTitle = (string) $response->Notifications->Message;
                }
                $this->fedexLogger->info('Response', (array)$response, true);
            } elseif (isset($response->RateReplyDetails)) {
                $allowedMethods = explode(',', $this->getConfigData('allowed_methods'));
                if (is_array($response->RateReplyDetails)) {
                    foreach ($response->RateReplyDetails as $rate) {
                        $serviceName = (string) $rate->ServiceType;
                        if (in_array($serviceName, $allowedMethods)) {
                            $amount = $this->_getRatesAmountOriginBased($rate);
                            $costArr[$serviceName] = $amount;
                            $priceArr[$serviceName] = $this->getMethodPrice($amount, $serviceName);
                        }
                    }
                    asort($priceArr);
                } else {
                    $rate = $response->RateReplyDetails;
                    $serviceName = (string) $rate->ServiceType;
                    if (in_array($serviceName, $allowedMethods)) {
                        $amount = $this->_getRatesAmountOriginBased($rate);
                        $costArr[$serviceName] = $amount;
                        $priceArr[$serviceName] = $this->getMethodPrice($amount, $serviceName);
                    }
                }
            }
        }

        return $priceArr;
    }

    /**
     * Get origin based amount form response of rate estimation
     *
     * @param \stdClass $rate
     * @return null|float
     */
    protected function _getRatesAmountOriginBased($rate)
    {
        $amount = null;
        $rateTypeAmounts = [];

        if (is_object($rate)) {
            // The "RATED..." rates are expressed in the currency of the origin country
            foreach ($rate->RatedShipmentDetails as $ratedShipmentDetail) {
                $netAmount = (string)$ratedShipmentDetail->ShipmentRateDetail->TotalNetCharge->Amount;
                $rateType = (string)$ratedShipmentDetail->ShipmentRateDetail->RateType;
                $rateTypeAmounts[$rateType] = $netAmount;
            }

            foreach ($this->rateOrder as $rateType) {
                if (!empty($rateTypeAmounts[$rateType])) {
                    $amount = $rateTypeAmounts[$rateType];
                    break;
                }
            }

            if (is_null($amount)) {
                $amount = (string)$rate->RatedShipmentDetails[0]->ShipmentRateDetail->TotalNetCharge->Amount;
            }
        }

        return $amount;
    }


    /**
     * Parse calculated rates.
     *
     * @param string $response
     *
     * @return Result
     */
    protected function _parseXmlResponse($response)
    {
        $result = $this->_rateFactory->create();
        if (isset($response['result']['error'])) {
            $error = $this->_rateErrorFactory->create();
            $error->setCarrier($this->_code);
            $error->setCarrierTitle($this->getConfigData('title'));
            $error->setErrorMessage($this->getConfigData('specificerrmsg'));
            $result->append($error);
        } else {
            $totalPriceArr = $response['totalprice'];
            $costArr = $response['costarr'];
            foreach ($totalPriceArr as $method => $price) {
                $rate = $this->_rateMethodFactory->create();
                $rate->setCarrier($this->_code);
                $rate->setCarrierTitle($this->getConfigData('title'));
                $rate->setMethod($method);
                $rate->setMethodTitle($this->fedexCarrier->getCode('method', $method));
                $rate->setCost($price);
                $rate->setPrice($price);
                $result->append($rate);
            }
        }

        return $result;
    }


    /**
     * Do shipment request to carrier web service,.
     *
     * @param \Magento\Framework\DataObject $request
     * @return \Magento\Framework\DataObject
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function _doShipmentRequest(\Magento\Framework\DataObject $request)
    {
        $this->_prepareShipmentRequest($request);
        $client = $this->_createShipSoapClient();
        $requestClient = $this->_createShipmentRequest($request);
        
        $result = new \Magento\Framework\DataObject();
        $response = $client->processShipment($requestClient);
        
        if ($response->HighestSeverity != 'FAILURE' && $response->HighestSeverity != 'ERROR') {
            $labelContent = $response->CompletedShipmentDetail->CompletedPackageDetails->Label->Parts->Image;
            $trackingNumber = $this->getTrackingNumber(
                $response->CompletedShipmentDetail->CompletedPackageDetails->TrackingIds
            );

            $shipmentData = [
                'api_name' => 'Fedex',
                'tracking_number' => $trackingNumber,
            ];
            $this->_customerSession->setData('shipment_data', $shipmentData);
            
            $result->setShippingLabelContent($labelContent);
            $result->setTrackingNumber($trackingNumber);

            $debugContent = [
                'request' => $client->__getLastRequest(),
                'result' => $client->__getLastResponse(),
            ];
            $this->_debug($debugContent);

            return $result;
        } else {
            $debugContent = [
                'request' => $client->__getLastRequest(),
                'result' => [
                    'error' => '',
                    'code' => '',
                    'xml' => $client->__getLastResponse(),
                ],
            ];
            //collect error message.
            if (is_array($response->Notifications)) {
                foreach ($response->Notifications as $notification) {
                    $debugContent['result']['code'] .= $notification->Code.'; ';
                    $debugContent['result']['error'] .= $notification->Message.'; ';
                }
            } else {
                $debugContent['result']['code'] = $response->Notifications->Code.' ';
                $debugContent['result']['error'] = $response->Notifications->Message.' ';
            }
            $this->_debug($debugContent);
            throw new LocalizedException(__($debugContent['result']['error']));
        }
        //}
           
    }
    /**
     * Return array of authenticated information.
     *
     * @return array
     */
    protected function _getAuthCredentials(\Magento\Framework\DataObject $request)
    {
        return [
            'WebAuthenticationDetail' => [
                'UserCredential' => [
                    'Key' => $request->getKey(),
                    'Password' => $request->getPassword(),
                ],
            ],
            'ClientDetail' => [
                'AccountNumber' => $request->getAccount(),
                'MeterNumber' => $request->getMeterNumber(),
            ],
            'TransactionDetail' => [
                'CustomerTransactionId' => '*** Express Domestic Shipping Request v9 using PHP ***',
            ],
            'Version' => ['ServiceId' => 'ship', 'Major' => '19', 'Intermediate' => '0', 'Minor' => '0'],
        ];
    }
    /**
     * @param  \Magento\Framework\DataObject $request
     * @return \Magento\Framework\DataObject
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _createShipmentRequest(\Magento\Framework\DataObject $request)
    {
        $packageParams = $request->getPackageParams();
        $customsValue = $packageParams->getCustomsValue();
        $height = $packageParams->getHeight();
        $width = $packageParams->getWidth();
        $length = $packageParams->getLength();
        $weightUnits = $packageParams->getWeightUnits() == \Zend_Measure_Weight::POUND ? 'LB' : 'KG';
        $unitPrice = 0;
        $itemsQty = 0;
        $itemsDesc = [];
        $countriesOfManufacture = [];
        $productIds = [];
        $packageItems = $request->getPackageItems();

        $request->setAccount($this->getConfigData('account'));
        $request->setMeterNumber($this->getConfigData('meter_number'));
        $request->setKey($this->getConfigData('key'));
        $request->setPassword($this->getConfigData('password'));

        //set seller credentials
        if ($this->_customerSession->getCustomerId()) {
            $this->_isSellerHasOwnCredentials($request, $this->_customerSession->getCustomerId());
        }
        
        $currency = $this->_storeManager->getStore()->getBaseCurrencyCode();
        if ($request->getShipperAddressCountryCode() == 'IN' && $request->getRecipientAddressCountryCode() == 'IN') {
            $currency = 'INR';
        }
        if ($request->getReferenceData()) {
            $referenceData = $request->getReferenceData() . $request->getPackageId();
        } else {
            $referenceData = 'Order #' .
                $request->getOrderShipment()->getOrder()->getIncrementId() .
                ' P' .
                $request->getPackageId();
        }

        foreach ($packageItems as $itemShipment) {
            $item = new \Magento\Framework\DataObject();
            $item->setData($itemShipment);

            $unitPrice += $item->getPrice();
            $itemsQty += $item->getQty();

            $itemsDesc[] = $item->getName();
            $productIds[] = $item->getProductId();
        }

        $productCollection = $this->_productCollectionFactory->create()->addStoreFilter(
            $request->getStoreId()
        )->addFieldToFilter(
            'entity_id',
            ['in' => $productIds]
        )->addAttributeToSelect(
            'country_of_manufacture'
        );
        foreach ($productCollection as $product) {
            $countriesOfManufacture[] = $product->getCountryOfManufacture();
        }

        $paymentType = $request->getIsReturn() ? 'RECIPIENT' : 'SENDER';
        $optionType = $request->getShippingMethod() == FedexCarrier::RATE_REQUEST_SMARTPOST
            ? 'SERVICE_DEFAULT' : $packageParams->getDeliveryConfirmation();
        $requestParam = [
            'RequestedShipment' => [
                'ShipTimestamp' => time(),
                'DropoffType' => $this->getConfigData('dropoff'),
                'PackagingType' => $this->getConfigData('packaging'),
                'ServiceType' => $request->getShippingMethod(),
                'Shipper' => [
                    'Contact' => [
                        'PersonName' => $request->getShipperContactPersonName(),
                        'CompanyName' => $request->getShipperContactCompanyName(),
                        'PhoneNumber' => $request->getShipperContactPhoneNumber(),
                    ],
                    'Address' => [
                        'StreetLines' => [$request->getShipperAddressStreet()],
                        'City' => $request->getShipperAddressCity(),
                        'StateOrProvinceCode' => $request->getShipperAddressStateOrProvinceCode(),
                        'PostalCode' => $request->getShipperAddressPostalCode(),
                        'CountryCode' => $request->getShipperAddressCountryCode(),
                    ],
                ],
                'Recipient' => [
                    'Contact' => [
                        'PersonName' => $request->getRecipientContactPersonName(),
                        'CompanyName' => $request->getRecipientContactCompanyName(),
                        'PhoneNumber' => $request->getRecipientContactPhoneNumber(),
                    ],
                    'Address' => [
                        'StreetLines' => [$request->getRecipientAddressStreet()],
                        'City' => $request->getRecipientAddressCity(),
                        'StateOrProvinceCode' => $request->getRecipientAddressStateOrProvinceCode(),
                        'PostalCode' => $request->getRecipientAddressPostalCode(),
                        'CountryCode' => $request->getRecipientAddressCountryCode(),
                        'Residential' => (bool) $this->getConfigData('residence_delivery'),
                    ],
                ],
                'ShippingChargesPayment' => [
                    'PaymentType' => $paymentType,
                    'Payor' => [
                        'ResponsibleParty' => [
                            'AccountNumber' => $request->getAccount(),
                            'CountryCode' => $request->getShipperAddressCountryCode(),
                        ],
                    ],
                ],
                'LabelSpecification' => [
                    'LabelFormatType' => 'COMMON2D',
                    'ImageType' => 'PNG',
                    'LabelStockType' => 'PAPER_8.5X11_TOP_HALF_LABEL',
                ],
                'RateRequestTypes' => ['ACCOUNT'],
                'PackageCount' => 1,
                'RequestedPackageLineItems' => [
                    'SequenceNumber' => '1',
                    'Weight' => ['Units' => $weightUnits, 'Value' => $request->getPackageWeight()],
                    'CustomerReferences' => [
                        'CustomerReferenceType' => 'CUSTOMER_REFERENCE',
                        'Value' => $referenceData,
                    ],
                ],
            ],
        ];

        // for international shipping
        if ($request->getShipperAddressCountryCode() != $request->getRecipientAddressCountryCode() ||
            ($request->getShipperAddressCountryCode() == 'IN' && $request->getRecipientAddressCountryCode() == 'IN')
            ) {
           
            if ($countriesOfManufacture[0] == '') {
                $countriesOfManufacture[0] = $request->getShipperAddressCountryCode();
            }
            $requestParam['RequestedShipment']['CustomsClearanceDetail'] = [
                'CustomsValue' => [
                    'Currency' => $request->getBaseCurrencyCode(),
                    'Amount' => $customsValue,
                ],
                'CommercialInvoice' => [
                    'Purpose' => 'SOLD',
                ],
                'DutiesPayment' => [
                    'PaymentType' => $paymentType,
                    'Payor' => [
                        'ResponsibleParty' => [
                            'AccountNumber' => $request->getAccount(),
                        ],
                    ],
                ],
                'Commodities' => [
                    'Weight' => ['Units' => $weightUnits, 'Value' => $request->getPackageWeight()],
                    'NumberOfPieces' => 1,
                    'CountryOfManufacture' => implode(',', array_unique($countriesOfManufacture)),
                    'Description' => implode(', ', $itemsDesc),
                    'Quantity' => ceil($itemsQty),
                    'QuantityUnits' => 'pcs',
                    'UnitPrice' => [
                        'Currency' => $request->getBaseCurrencyCode(),
                        'Amount' => $unitPrice,
                    ],
                    'CustomsValue' => [
                        'Currency' => $request->getBaseCurrencyCode(),
                        'Amount' => $customsValue,
                    ],
                ],
            ];
        }

        return $this->_getAuthCredentials($request) + $requestParam;
    }
   
    /**
     * @param array|object $trackingIds
     *
     * @return string
     */
    private function getTrackingNumber($trackingIds)
    {
        return is_array($trackingIds) ? array_map(
            function ($val) {
                return $val->TrackingNumber;
            },
            $trackingIds
        ) : $trackingIds->TrackingNumber;
    }

    /**
     * Get tracking
     *
     * @param string|string[] $trackings
     * @return Result|null
     */
    public function getTracking($trackings)
    {
        return $this->fedexCarrier->getTracking($trackings);
    }

     /**
     * Return container types of carrier
     *
     * @param \Magento\Framework\DataObject|null $params
     * @return array|bool
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getContainerTypes(\Magento\Framework\DataObject $params = null)
    {
        return $this->fedexCarrier->getContainerTypes($params);
    }


    /**
     * Get allowed shipping methods
     *
     * @return array
     */
    public function getAllowedMethods()
    {
        return parent::getAllowedMethods();
    }


    /**
     * load model.
     *
     * @param int    $id
     * @param string $model
     *
     * @return object
     */
    protected function _loadModel($id, $model)
    {
        return $this->_objectManager->create($model)->load($id);
    }
}
