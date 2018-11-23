<?php
/**
 * Webkul Software
 *
 * @category Webkul
 * @package Webkul_MpFedexShipping
 * @author Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */
namespace Magento\Fedex\Test\Unit\Model;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Framework\DataObject;
use Magento\Framework\Xml\Security;

class CarrierTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    protected $_helper;

    /**
     * @var \Magento\Fedex\Model\Carrier
     */
    protected $_model;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_scope;

    /**
     * Model under test
     *
     * @var \Magento\Quote\Model\Quote\Address\RateResult\Error|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $error;

    /**
     * @var \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $errorFactory;

    protected $customerFactory;

    /**
     * @return void
     */
    public function setUp()
    {
        $this->_helper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->_scope = $this->getMockBuilder(
            '\Magento\Framework\App\Config\ScopeConfigInterface'
        )->disableOriginalConstructor()->getMock();

        $this->_scope->expects(
            $this->any()
        )->method(
            'getValue'
        )->will(
            $this->returnCallback([$this, 'scopeConfiggetValue'])
        );

        $rate = $this->getMock('Magento\Shipping\Model\Rate\Result', ['getError'], [], '', false);
        $rateFactory = $this->getMock('Magento\Shipping\Model\Rate\ResultFactory', ['create'], [], '', false);
        $rateFactory->expects($this->any())->method('create')->will($this->returnValue($rate));

        $store = $this->getMock('Magento\Store\Model\Store', ['getBaseCurrencyCode', '__wakeup'], [], '', false);
        $storeManager = $this->getMockForAbstractClass('Magento\Store\Model\StoreManagerInterface');
        $storeManager->expects($this->any())->method('getStore')->will($this->returnValue($store));

        $this->error = $this->getMockBuilder('\Magento\Quote\Model\Quote\Address\RateResult\Error')
            ->setMethods(['setCarrier', 'setCarrierTitle', 'setErrorMessage'])
            ->getMock();
        $this->errorFactory = $this->getMockBuilder('Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->errorFactory->expects($this->any())->method('create')->willReturn($this->error);

        
        $priceCurrency = $this->getMockBuilder('Magento\Framework\Pricing\PriceCurrencyInterface')->getMock();
        
        $country = $this->getMock(
            'Magento\Directory\Model\Country',
            ['load', 'getData', '__wakeup'],
            [],
            '',
            false
        );
        $country->expects($this->any())->method('load')->will($this->returnSelf());
        $countryFactory = $this->getMock('Magento\Directory\Model\CountryFactory', ['create'], [], '', false);
        $countryFactory->expects($this->any())->method('create')->will($this->returnValue($country));

        $rateMethod = $this->getMock(
            'Magento\Quote\Model\Quote\Address\RateResult\Method',
            null,
            ['priceCurrency' => $priceCurrency]
        );
        $rateMethodFactoryTest = $this->getMock(
            'Magento\Quote\Model\Quote\Address\RateResult\MethodFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->customerFactory = $this->getMock(
            'Magento\Customer\Model\customerFactory',
            ['create'],
            [],
            '',
            false
        );

        $rateMethodFactoryTest->expects($this->any())->method('create')->will($this->returnValue($rateMethod));
        $this->_model = $this->getMock(
            'Webkul\MpFedexShipping\Model\Carrier',
            ['_getCachedQuotes', '_debug'],
            [
                'scopeConfig' => $this->_scope,
                'rateErrorFactory' => $this->errorFactory,
                'logger' => $this->getMock('Psr\Log\LoggerInterface'),
                'xmlSecurity' => new Security(),
                'xmlElFactory' => $this->getMock('Magento\Shipping\Model\Simplexml\ElementFactory', [], [], '', false),
                'rateFactory' => $rateFactory,
                'rateMethodFactory' => $rateMethodFactoryTest,
                'trackFactory' => $this->getMock('Magento\Shipping\Model\Tracking\ResultFactory', [], [], '', false),
                'trackErrorFactory' =>
                    $this->getMock('Magento\Shipping\Model\Tracking\Result\ErrorFactory', [], [], '', false),
                'trackStatusFactory' =>
                    $this->getMock('Magento\Shipping\Model\Tracking\Result\StatusFactory', [], [], '', false),
                'regionFactory' => $this->getMock('Magento\Directory\Model\RegionFactory', [], [], '', false),
                'countryFactory' => $countryFactory,
                'currencyFactory' => $this->getMock('Magento\Directory\Model\CurrencyFactory', [], [], '', false),
                'directoryData' => $this->getMock('Magento\Directory\Helper\Data', [], [], '', false),
                'stockRegistry' => $this->getMock('Magento\CatalogInventory\Model\StockRegistry', [], [], '', false),
                'storeManager' => $storeManager,
                'configReader' => $this->getMock('Magento\Framework\Module\Dir\Reader', [], [], '', false),
                'productCollectionFactory' =>
                    $this->getMock('Magento\Catalog\Model\ResourceModel\Product\CollectionFactory', [], [], '', false),
                'objectManager' => $this->getMock('Magento\Framework\ObjectManagerInterface', [], [], '', false),
                'coreSession' => $this->getMock('Magento\Framework\Session\SessionManager', [], [], '', false),
                'customerSession' => $this->getMock('Magento\Customer\Model\Session', [], [], '', false),
                'labelGenerator' => $this->getMock('Magento\Shipping\Model\Shipping\LabelGenerator', [], [], '', false),
                'customerFactory' => $this->customerFactory,
                'data' => []
            ]
        );
    }

    /**
     * Callback function, emulates getValue function
     * @param $path
     * @return null|string
     */
    public function scopeConfiggetValue($path)
    {
        switch ($path) {
            case 'carriers/fedex/allowed_methods':
                return 'ServiceType';
                break;
            case 'carriers/mpfedex/showmethod':
                return 1;
                break;
        }
    }

    /**
     * @dataProvider collectRatesDataProvider
     */
    public function testCollectRatesRateAmountOriginBased($amount, $rateType, $expected)
    {
        $this->_scope->expects($this->any())->method('isSetFlag')->will($this->returnValue(true));

        $customerMock = $this->getMock('\Magento\Customer\Model\Customer', [], [], '', false);
        $this->customerFactory->method('create')->willReturn($customerMock);

        // @codingStandardsIgnoreStart
        $netAmount = new \Magento\Framework\DataObject([]);
        $netAmount->Amount = $amount;

        $totalNetCharge = new \Magento\Framework\DataObject([]);
        $totalNetCharge->TotalNetCharge = $netAmount;
        $totalNetCharge->RateType = $rateType;

        $ratedShipmentDetail = new \Magento\Framework\DataObject([]);
        $ratedShipmentDetail->ShipmentRateDetail = $totalNetCharge;

        $rate = new \Magento\Framework\DataObject([]);
        $rate->ServiceType = 'ServiceType';
        $rate->RatedShipmentDetails = [$ratedShipmentDetail];

        $response = new \Magento\Framework\DataObject([]);
        $response->HighestSeverity = 'SUCCESS';
        $response->RateReplyDetails = $rate;

        $this->_model->expects($this->any())->method('_getCachedQuotes')->will(
            $this->returnValue(serialize($response))
        );
         $request = $this->_helper->getObject(
            'Magento\Quote\Model\Quote\Address\RateRequest',
            require __DIR__ . '/_files/rates_request_data.php'
        );

        foreach ($this->_model->collectRates($request)->getAllRates() as $allRates) {
            $this->assertEquals($expected, $allRates->getData('cost'));
        }
        // @codingStandardsIgnoreEnd
    }

    public function collectRatesDataProvider()
    {
        return [
            [20.0, 'RATED_ACCOUNT_PACKAGE', 20],
            [10.50, 'PAYOR_ACCOUNT_PACKAGE', 10.5],
            [105.01, 'RATED_ACCOUNT_SHIPMENT', 105.01],
            [31.2, 'PAYOR_ACCOUNT_SHIPMENT', 31.2],
            [12.0, 'RATED_LIST_PACKAGE', 12],
            [128.25, 'PAYOR_LIST_PACKAGE', 128.25],
            [12.12, 'RATED_LIST_SHIPMENT', 12.12],
            [39.9, 'PAYOR_LIST_SHIPMENT', 39.9],
        ];
    }
}
