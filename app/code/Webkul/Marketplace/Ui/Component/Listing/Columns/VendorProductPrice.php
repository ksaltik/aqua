<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Marketplace
 * @author    Webkul
 * @copyright Copyright (c) 2010-2018 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Marketplace\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Webkul\Marketplace\Helper\Data as HelperData;
use Webkul\Marketplace\Model\ResourceModel\Orders\CollectionFactory;
use Webkul\Marketplace\Helper\Orders as HelperOrders;

/**
 * Class VendorProductPrice.
 */
class VendorProductPrice extends Column
{
    /**
     * @var HelperData
     */
    protected $_helper;

    /**
     * @var CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var HelperOrders
     */
    protected $helperOrders;

    protected $resourceConnection;


    /**
     * Constructor.
     *
     * @param ContextInterface   $context
     * @param UiComponentFactory $uiComponentFactory
     * @param HelperData         $helper
     * @param CollectionFactory  $collectionFactory
     * @param HelperOrders       $helperOrders
     * @param array              $components
     * @param array              $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        HelperData $helper,
        CollectionFactory $collectionFactory,
        HelperOrders $helperOrders,
        array $components = [],
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        array $data = []
    ) {
        $this->_helper = $helper;
        $this->_collectionFactory = $collectionFactory;
        $this->helperOrders = $helperOrders;
        $this->_resourceConnection = $resourceConnection;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source.
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        $taxToSeller = $this->_helper->getConfigTaxManage();
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as &$item) {

                $resource = $this->_resourceConnection;
                $connection = $resource->getConnection();
                $sql = "SELECT * FROM marketplace_product where mageproduct_id=" . $item['mageproduct_id'];
                $resultProduct = $connection->fetchRow($sql);
                //echo "<pre>"; print_r($resultProduct);print_r($item);exit;



                $productName = $resultProduct['vendor_purchase_price'];
                /*prepare product quantity status*/
                $productName=number_format((float)$productName, 2, '.', '');
                $item[$fieldName] =$productName;
            }
        }

        return $dataSource;
    }

    public function getOrderItemTaxShipping($marketplaceOrders, $item, $taxToSeller, $totalshipping)
    {
        $marketplaceOrdersData = [];
        foreach ($marketplaceOrders as $tracking) {
            $marketplaceOrdersData = $tracking->getData();
            $taxToSeller = $tracking['tax_to_seller'];
        }
        if ($item['is_shipping'] == 1) {
            foreach ($marketplaceOrders as $tracking) {
                $shippingamount = $marketplaceOrdersData['shipping_charges'];
                $refundedShippingAmount = $marketplaceOrdersData['refunded_shipping_charges'];
                $totalshipping = $shippingamount - $refundedShippingAmount;
                if ($totalshipping * 1) {
                    $item['total_shipping'] = $totalshipping;
                }
            }
        }
        return [
            'taxToSeller' => $taxToSeller,
            'totalshipping' => $totalshipping,
            'item' => $item
        ];
    }

    /**
     * Get Order Product Name Html Data Method.
     *
     * @param array  $result
     * @param string $productName
     *
     * @return string
     */
    public function getProductNameHtml($result, $productName)
    {
        if ($_options = $result) {
            $proOptionData = '<dl class="item-options">';
            /*foreach ($_options as $_option) {
                $proOptionData .= '<dt>'.$_option['label'].'</dt>';
                if (!$this->getPrintStatus()) {
                    $_formatedOptionValue = $_option;
                    $class = '';
                    if (isset($_formatedOptionValue['full_view'])) {
                        $class = 'truncated';
                    }
                    $proOptionData .= '<dd class="'.$class.'">'.$_option['value'];
                    if (isset($_formatedOptionValue['full_view'])) {
                        $proOptionData .= '<div class="truncated_full_value">
                        <dl class="item-options"><dt>'.
                        $_option['label'].
                        '</dt><dd>'.
                        $_formatedOptionValue['full_view'].
                        '</dd></dl></div>';
                    }
                    $proOptionData .= '</dd>';
                } else {
                    $printValue = $_option['print_value'];
                    $proOptionData .= '<dd>'.
                    nl2br((isset($printValue) ? $printValue : $_option['value'])).
                    '</dd>';
                }
            }*/
            //$proOptionData .= '</dl>';
            $productName = $productName;//.'<br/>'.$proOptionData;
        } else {
            $productName = $productName;
        }

        return $productName;
    }
}
