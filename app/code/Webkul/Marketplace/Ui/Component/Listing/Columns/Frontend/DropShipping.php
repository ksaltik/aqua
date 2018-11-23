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

namespace Webkul\Marketplace\Ui\Component\Listing\Columns\Frontend;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Webkul\Marketplace\Helper\Data as HelperData;
use Webkul\Marketplace\Model\ResourceModel\Orders\CollectionFactory;
use Webkul\Marketplace\Helper\Orders as HelperOrders;

/**
 * Class DropShipping.
 */
class DropShipping extends Column
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
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as &$item) {
                $item[$fieldName . '_flag'] = 1;
                $resource = $this->_resourceConnection;
                $connection = $resource->getConnection();
               // echo "<pre>"; print_r($item);
                //print_r($dataSource);
                //exit;
                $sql = "SELECT * FROM marketplace_product where mageproduct_id=" . $item['entity_id'];
                $resultProduct = $connection->fetchRow($sql);
                $productName = $resultProduct['original_product_id'];
                if ($productName > 0) {
                    $item['dropshipping'] = "Yes";

                } elseif ($productName== 0)  {
                    $item['dropshipping'] = "No";
                }
            }
        }
        //  echo "<pre>"; print_r($item);print_r($dataSource);exit;
        return $dataSource;
    }
}
