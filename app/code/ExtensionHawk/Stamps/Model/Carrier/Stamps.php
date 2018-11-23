<?php
namespace ExtensionHawk\Stamps\Model\Carrier;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Rate\Result;
use Magento\Quote\Model\Quote\Address\RateResult\Error;
use Magento\Shipping\Model\Carrier\AbstractCarrierOnline;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Simplexml\Element;
use ExtensionHawk\Stamps\Helper\Config;
use Magento\Framework\Xml\Security;

define("WF_USPS_STAMPS_ACCESS_KEY", "570f77ac-5374-46f1-aee7-84375876174b");

class Stamps extends \Magento\Shipping\Model\Carrier\AbstractCarrierOnline implements \Magento\Shipping\Model\Carrier\CarrierInterface
{
    protected $_code = 'stamps';
    protected $_scopeConfig;
    protected $_storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
    protected $_request;
    protected $_result;
    protected $_basePath;
    protected $_authenticator;
    protected $_customizableContainerTypes = ['custompackaging'];
    protected $_localeFormat;
    protected $_logger;
    protected $configHelper;
    protected $_errors = [];
    protected $_cart;

     /**
      * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
      * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
      * @param \Psr\Log\LoggerInterface $logger
      * @param \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory
      * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
      * @param array $data
      */

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        Security $xmlSecurity,
        \Magento\Shipping\Model\Rate\ResultFactory $rateFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        \Magento\Shipping\Model\Simplexml\ElementFactory $xmlElFactory,
        \Magento\Shipping\Model\Tracking\ResultFactory $trackFactory,
        \Magento\Shipping\Model\Tracking\Result\ErrorFactory $trackErrorFactory,
        \Magento\Shipping\Model\Tracking\Result\StatusFactory $trackStatusFactory,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Directory\Helper\Data $directoryData,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        Config $configHelper,
        $data = []
    ) {
        $this->_rateResultFactory = $rateFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->_localeFormat = $localeFormat;
        $this->configHelper = $configHelper;
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
            $data
        );
    }

    public function _getAllowedMethods()
    {
        return [$this->_code => $this->getConfigData('title')];
    }

    //function to get rates and customize rates if applicable
    public function collectRates(RateRequest $request)
    {
        $result = $this->_rateResultFactory->create();
        if (!$this->getConfigFlag('active')) {
            return false;
        }
        try {
            $auth = $this->getAuthenticateResponse();
        } catch (\Exception $e) {
            $error = $this->_setErrorToDisplay([ $e->getMessage()]);
            $result->append($error);
            $debugData['result'] = ['error' => $e->getMessage()];
            $this->_debug($debugData);
            return $result;
        }
        $this->_authenticator = (!empty($auth->Authenticator)) ? $auth->Authenticator : '';
        $allowedMethods = $this->getAllowedMethods();
        $packageRequests = $this->_getPackageRequests($request);
        $responses = [];
        if (is_array($packageRequests)) {
            foreach ($packageRequests as $packageRequest) {
                $responses[] = [
                    'response' => $this->_getRates($packageRequest['request']),
                ];
            }
        }
        return $this->createRates($responses, $allowedMethods);
    }

    //function to
    public function createRates($responses, $allowedMethods)
    {
        $services = $this->configHelper->getCode('method');
        $foundRates     = [];
        foreach ($responses as $response) {
            $responseObj = $response['response'];
            if (isset($responseObj->Rates)) {
                $stampRates     = $responseObj->Rates->Rate;
                foreach ($stampRates as $stampRate) {
                    if (!empty($allowedMethods) &&
                    ((array_key_exists($stampRate->ServiceType, $allowedMethods)) &&
                    in_array($stampRate->PackageType, $allowedMethods[$stampRate->ServiceType]))) {
                        $serviceType = (string) $stampRate->ServiceType;
                        $packageType = (string) $stampRate->PackageType;
                        $totalAmount = $stampRate->Amount;
                        $foundRates[$serviceType][$packageType]['label'] =
                        (string) (isset($services[$serviceType]['label']) &&
                        !empty($services[$serviceType]['label']))?$services[$serviceType]['label']:$serviceType;
                        $foundRates[$serviceType][$packageType]['cost']     = $totalAmount;
                    }
                }
            }
        }
        return $this->createResult($foundRates, $services);
    }

    //function to create result to be sent for displaying in front
    public function createResult($foundRates, $services)
    {
        $displayRates = [];
        $foundRate = [];
        $result = $this->_rateResultFactory->create();
        if (is_array($foundRates)) {
            foreach ($foundRates as $foundRateServiceType => $packageTypes) {
                $prev_rate = [
                    'service_type' => '',
                    'cost' => '',
                    'service_name' => ''
                ];
                foreach ($packageTypes as $packageType => $servicesDetails) {
                    $foundRate[] = $servicesDetails;
                    if (empty($prev_rate['service_type']) || ($prev_rate['service_type'] == $packageType &&
                    $prev_rate['cost'] > $servicesDetails['cost'])) {
                        $prev_rate['service_type'] = $foundRateServiceType;
                        $prev_rate['cost'] = $servicesDetails['cost'];
                        $prev_rate['service_name'] = $servicesDetails['label'];
                    }
                }
                if (!empty($prev_rate['service_type'])) {
                    $method = $this->_rateMethodFactory->create();
                    $method->setCarrier($this->_code);
                    $method->setCarrierTitle($this->getConfigData('title'));
                    $method->setMethod(str_replace('-', '_', $prev_rate['service_type']));
                    $method->setMethodTitle($services[$prev_rate['service_type']]['label']);
                    $amount = $this->getMethodPrice((string)$prev_rate['cost'], $prev_rate['service_type']);
                    $method->setPrice($amount);
                    $method->setCost($amount);
                    $result->append($method);
                }
            }
        }
        return $result;
    }

    public function getEndPoint()
    {
        $stamps_uri = 'https://swsim.stamps.com/swsim/swsimv50.asmx?wsdl';
        return $stamps_uri;
    }

    protected function _createSoapClient($wsdl, $trace = true, $connection_timeout = 100)
    {
        $client = new \SoapClient($wsdl, ['trace' => $trace, 'connection_timeout' => $connection_timeout]);
        return $client;
    }

    protected function _setError($error)
    {
        $this->_errors[]    =   $error;
    }

    protected function _getError()
    {
        return $this->_errors;
    }

    protected function _setErrorToDisplay($errors)
    {
        $errormsg = $this->_rateErrorFactory->create();
        $errormsg->setCarrier($this->_code);
        $errormsg->setCarrierTitle($this->getConfigData('title'));
        if (is_array($errors)) {
            foreach ($errors as $error) {
                $errormsg->setErrorMessage($error);
            }
        }
        return  $errormsg;
    }

    public function getAuthenticateResponse()
    {
        $stamps_user_id = $this->getConfigData('api_user');
        $stamps_password = $this->getConfigData('api_pass');
        $stamps_access_key = WF_USPS_STAMPS_ACCESS_KEY;
        $request['Credentials'] = [
            'IntegrationID' => '570f77ac-5374-46f1-aee7-84375876174b',
            'Username' => $stamps_user_id,
            'Password' => $stamps_password
        ];
        try {
            $client     = $this->_createSoapClient($this->getEndPoint());
            $result = $client->authenticateUser($request);
            return $result;
        } catch (\Exception $e) {
            
            $debugData['result'] = ['error' => $e->getMessage(), 'code' => $e->getCode()];
            $this->_debug($debugData);
            return $e->getMessage();
        }
    }

    public function _getPackageRequests(RateRequest $request)
    {
        $weight = $this->getTotalNumOfBoxes($request->getPackageWeight());
        $packages[] = [
            "request"=>[
                "FromZIPCode" => $request->getPostcode(),
                "ToZIPCode" => $request->getDestPostcode(),
                "ToCountry" => $request->getDestCountryId(),
                //"Amount" => "12",
                "WeightLb" => $weight,
                "WeightOz" => "0.00",
                "PackageType" => "",
                "Length" => 0,
                "Width" => "0",
                "Height" => "0",
                "ShipDate" => date('Y-m-d'),
                "InsuredValue" => "0",
                "RectangularShaped" => false
            ],
        ];
        return $packages;
    }

    public function _getRates($package)
    {
        $result = '';
        try {
            $client     = $this->_createSoapClient($this->getEndPoint());
            $auth = $this->getAuthenticateResponse();
            $request1=[
                "Authenticator" => (!empty($auth->Authenticator)) ? $auth->Authenticator : '',
                "Rate" => $package
            ];
            $result = $client->GetRates($request1);
            $debugData2 = ['Rates Request' => $request1];
        } catch (\Exception $e) {
            $debugData['result'] = ['error' => $e->getMessage(), 'code' => $e->getCode()];
            $this->_debug($debugData);
        }
        return $result;
    }

    public function getAllowedMethods()
    {
        $allowed_methods = [];
        $methods = explode(',', $this->getConfigData('allowed_methods'));
        if (is_array($methods)) {
            foreach ($methods as $method) {
                $methodParts = explode(":", $method);
                if ($this->getMethodCount($methodParts)>1) {
                    $allowed_methods[$methodParts[0]][]     =   $methodParts[1];
                }
            }
        }
        return $allowed_methods;
    }
    
    //function to get number of methods in a service
    public function getMethodCount($method)
    {
        return count($method);
    }

    //Do shipment request to carrier web service, obtain Print Shipping Labels and process errors in response
    protected function _doShipmentRequest(\Magento\Framework\DataObject $request)
    {
        $this->_prepareShipmentRequest($request);
        $shipRequest = [];
        $result = new \Magento\Framework\DataObject();
        $shipRequest = $this->_createShipmentRequest($request);
        $debugData = ['Shipment Create Request' => $shipRequest ];
        $this->_debug($debugData);
        try {
            $client =   $this->_createSoapClient($this->getEndPoint());
            $response = $client->CreateIndicium($shipRequest);
            $debugData = ['Shipment Create Response' => $response ];
        } catch (\Exception $e) {
            $debugData = ['Shipment Create Failed Response' => $e->getMessage() ];
        }
        $this->_debug($debugData);
        $result = new \Magento\Framework\DataObject();
        if (isset($response->ImageData->base64Binary)) {
            $result->setShippingLabelContent($response->ImageData->base64Binary);
            $result->setTrackingNumber($response->TrackingNumber);
        } else {
            $result->setErrors('Shipping label creation failed');
        }
        return $result;
    }

    //Form Shipping request for create shipment
    protected function _createShipmentRequest(\Magento\Framework\DataObject $request)
    {
        $packageParams = $request->getPackageParams();
        $packageWeight = $request->getPackageWeight();
        if ($packageParams->getWeightUnits() != \Zend_Measure_Weight::POUND) {
            $packageWeight = round(
                $this->_carrierHelper->convertMeasureWeight(
                    $request->getPackageWeight(),
                    $packageParams->getWeightUnits(),
                    \Zend_Measure_Weight::POUND
                )
            );
        }
        $packageWeightLbs = intval($packageWeight);
        $packageWeightOz = ($packageWeight - intval($packageWeight)) * 16;
        $shipRequest = [];
        //$orderId = $request->getOrderShipment()->getOrder()->getIncrementId();
        //$order = Mage::getModel('sales/order')->load($orderId);
        //$orderAmount = $order->getGrandTotal() - $order->getShippingAmount();

        $shipRequest['Credentials']     = [
            'IntegrationID' => '570f77ac-5374-46f1-aee7-84375876174b',
            'Username' => $this->getConfigData('api_user'),
            'Password' => $this->getConfigData('api_pass')
        ];
        $service_packagetype = $this->getServiceAndPackageType($request->getPackagingType());
        $shipRequest['IntegratorTxID'] = uniqid('EH_122_');
        $shipRequest['TrackingNumber'] = '';
        $shipRequest['ReturnImageData'] = true;
        $shipRequest['Rate'] = [
            'FromZIPCode' => $request->getShipperAddressPostalCode(),
            'ToZIPCode'     => $request->getRecipientAddressPostalCode(),
            'ToCountry'     => $request->getRecipientAddressCountryCode(),
            'ServiceType' => $service_packagetype[0],
            'DeliverDays' => '1-1',
            'WeightLb' => $packageWeightLbs,
            'WeightOz' => $packageWeightOz,
            'PackageType' => $service_packagetype[1],
            'Length' => '',
            'Width'     => '',
            'Height' => '',
            'ShipDate' => date('Y-m-d'),
            'InsuredValue' => ('yes' == $this->getConfigFlag('insurance'))?ceil($packageParams->getCustomsValue()):'',
            'DeclaredValue'     => ceil($packageParams->getCustomsValue()),
            'RectangularShaped'     => 'RectangularShaped',
        ];
        
        if ($this->getConfigData('hiddenpostage')) {
            $shipRequest['Rate']['AddOns']  = [
                'AddOnV7'   =>  [
                    0 =>  [
                        'AddOnType' => 'SC-A-HP'
                    ]
                ]
            ];
        }
        if ($this->getConfigData('insurance')) {
            $shipRequest['Rate']['AddOns']  = [
                'AddOn'     =>  [
                    0 =>  [
                        'AddOnType' => 'SC-A-INS',
                        'Amount' =>  ''//$orderAmount
                    ]
                ]
            ];
        }
        
        
        if ($this->getConfigData('printlayout')) {
            $shipRequest['Rate']['PrintLayout'] = $this->getConfigData('printlayout');
        }
        $shipRequest['From'] = [
            'FullName' => $request->getShipperContactPersonFirstName(),
            'Company' => $request->getShipperContactCompanyName(),
            'Address1' => $request->getShipperAddressStreet1(),
            'Address2' => $request->getShipperAddressStreet2(),
            'City' => $request->getShipperAddressCity(),
            'State' => $request->getShipperAddressStateOrProvinceCode(),
            'ZIPCode' => $request->getShipperAddressPostalCode(),
            'PhoneNumber' => $request->getShipperContactPhoneNumber(),
        ];
        if ('US' == $request->getRecipientAddressCountryCode()) {
            $shipRequest['To'] = $this->domesticShipment($request);
        } else {
            $shipRequest['To'] = $this->intlShipment($request);
            $shipRequest['Customs'] = $this->customsDetails($request);
        }
        $shipRequest['ImageType']   = 'Gif';
        return $shipRequest;
    }

    //function to create doemstic shipment request
    public function domesticShipment(\Magento\Framework\DataObject $request)
    {
        $zip = $request->getRecipientAddressPostalCode();
    $temp = [
        'FullName' => '',//$request->getRecipientContactPersonFirstName(),
            'NamePrefix' => '',
            'FirstName'     => $request->getRecipientContactPersonFirstName(),
            'MiddleName' => '',
            'LastName' => $request->getRecipientContactPersonLastName(),
            'NameSuffix' => '',
            'Title'     => '',
            'Department' => '',
            'Company' => $request->getRecipientContactCompanyName(),
            'Address1' => $request->getRecipientAddressStreet1(),
            'Address2' => $request->getRecipientAddressStreet2(),
            'City' => $request->getRecipientAddressCity(),
            'State' => $request->getRecipientAddressStateOrProvinceCode(),
        'ZIPCode' => $zip,
        'ZIPCodeAddOn' => '',
            'DPB' => '',
            'CheckDigit' => '',
            'Province' => '',
            'PostalCode' => '',
            'Country' => $request->getRecipientAddressCountryCode(),
            'Urbanization' => '',
            'PhoneNumber' => $request->getRecipientContactPhoneNumber(),
            'CleanseHash' => ''
        ];
    if(strlen($zip)>5)
    {
        $zip = explode('-', $zip);
        $temp['ZIPCode'] = $zip[0]; 
        $temp['ZIPCodeAddOn'] = $zip[1];
    }
    return $temp;
    }

    //function to create international shipment request
    public function intlShipment(\Magento\Framework\DataObject $request)
    {
        return [
            'FullName'          => '',
            'NamePrefix'        => '',
            'FirstName'             => $request->getRecipientContactPersonFirstName(),
            'MiddleName'        => '',
            'LastName'          => $request->getRecipientContactPersonLastName(),
            'NameSuffix'        => '',
            'Title'                 => '',
            'Department'        => '',
            'Company'           => $request->getRecipientContactCompanyName(),
            'Address1'          => $request->getRecipientAddressStreet1(),
            'Address2'          => $request->getRecipientAddressStreet2(),
            'City'              => $request->getRecipientAddressCity(),
            'State'                 => $request->getRecipientAddressStateOrProvinceCode(),
            'ZIPCodeAddOn'      => '',
            'DPB'               => '',
            'CheckDigit'        => '',
            'Province'          => '',
            'PostalCode'        => $request->getRecipientAddressPostalCode(),
            'Country'           => $request->getRecipientAddressCountryCode(),
            'Urbanization'      => '',
            'PhoneNumber'       => $request->getRecipientContactPhoneNumber(),
            'CleanseHash'       => ''
        ];
    }

    //function to add customs details for international shipments
    public function customsDetails(\Magento\Framework\DataObject $request)
    {
        $packageParams = $request->getPackageParams();
        $packageItems = $request->getPackageItems();
        $custom_line_array = [];
        foreach ($packageItems as $itemShipment) {
            $item = new \Magento\Framework\DataObject();
            $item->setData($itemShipment);
            $itemWeight = $item->getWeight() * $item->getQty();
            if ($packageParams->getWeightUnits() != \Zend_Measure_Weight::POUND) {
                $itemWeight = $this->_carrierHelper->convertMeasureWeight(
                    $itemWeight,
                    $packageParams->getWeightUnits(),
                    \Zend_Measure_Weight::POUND
                );
            }
            if (!empty($countriesOfManufacture[$item->getProductId()])) {
                $countryOfManufacture = $countriesOfManufacture[$item->getProductId()];
            } else {
                $countryOfManufacture = '';
            }
            $ceiledQty = ceil($item->getQty());
            if ($ceiledQty < 1) {
                $ceiledQty = 1;
            }
            $custom_line = [];
            $custom_line['Description']     = $item->getName();
            $custom_line['Quantity'] = $ceiledQty;
            $custom_line['Value'] = $item->getCustomsValue() * $item->getQty();
            $custom_line['WeightLb'] = (string)( $itemWeight );
            $custom_line['CountryOfOrigin']     = 'US';
            $custom_line_array[] = $custom_line;
        }
        $debugData['Customs'] = ['Customs' => $custom_line_array ];
        $this->_debug($debugData);
        return [
            'ContentType'       => 'Other',
            'OtherDescribe'         => '',
            'CustomsLines'      => [
                'CustomsLine'   => $custom_line_array,
            ],
        ];
    }

    //Function to get service and package type from  the give '_' seperated string
    protected function getServiceAndPackageType($inputstring)
    {
        return explode('_', $inputstring);
    }
    
    //Return container types of carrier
    public function getContainerTypes(\Magento\Framework\DataObject $params = null)
    {
        if (is_null($params)) {
            return $this->_getAllowedContainers();
        }
        return $this->_isUSCountry($params->getCountryRecipient()) ? $this->_getAllowedContainers($params) :
        $this->_getAllowedContainers($params);
    }

    public function getContainerTypesAll()
    {
        return $this->configHelper->getCode('container');
    }

    public function getTracking($trackings)
    {
    }
}
