<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_PurchaseManagement
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\PurchaseManagement\Model\Pdf;

use Magento\Customer\Model\Session;

/**
 * Marketplace Order PDF Invoice model.
 */
class Quotation extends \Magento\Sales\Model\Order\Pdf\AbstractPdf
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var ObjectManagerInterface
     */
    protected $_string;

    protected $_quotation;

    protected $_pricehelper;

    protected $_supplier;

    protected $_orderitem;

    /**
     * @param Session                                              $customerSession,
     * @param \Magento\Framework\ObjectManagerInterface            $objectManager,
     * @param \Magento\Payment\Helper\Data                         $paymentData
     * @param \Magento\Framework\Stdlib\StringUtils                $string
     * @param \Magento\Framework\App\Config\ScopeConfigInterface   $scopeConfig
     * @param \Magento\Framework\Filesystem                        $filesystem
     * @param Config                                               $pdfConfig
     * @param \Magento\Sales\Model\Order\Pdf\Total\Factory         $pdfTotalFactory
     * @param \Magento\Sales\Model\Order\Pdf\ItemsFactory          $pdfItemsFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Framework\Translate\Inline\StateInterface   $inlineTranslation
     * @param \Magento\Sales\Model\Order\Address\Renderer          $addressRenderer
     * @param \Magento\Store\Model\StoreManagerInterface           $storeManager
     * @param \Magento\Framework\Locale\ResolverInterface          $localeResolver
     * @param array                                                $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Session $customerSession,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Sales\Model\Order\Pdf\Config $pdfConfig,
        \Magento\Sales\Model\Order\Pdf\Total\Factory $pdfTotalFactory,
        \Magento\Sales\Model\Order\Pdf\ItemsFactory $pdfItemsFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Sales\Model\Order\Address\Renderer $addressRenderer,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        \Webkul\PurchaseManagement\Model\OrderFactory $orderFactory,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Webkul\PurchaseManagement\Model\OrderitemFactory $orderitemFactory,
        \Webkul\PurchaseManagement\Model\SuppliersFactory $supplierFactory,
        array $data = []
    ) {
        $this->_customerSession = $customerSession;
        $this->_objectManager = $objectManager;
        $this->_string = $string;
        $this->_quotation=$orderFactory;
        $this->_pricehelper=$priceHelper;
        $this->_orderitem=$orderitemFactory;
        $this->_supplier=$supplierFactory;
        parent::__construct(
            $paymentData,
            $string,
            $scopeConfig,
            $filesystem,
            $pdfConfig,
            $pdfTotalFactory,
            $pdfItemsFactory,
            $localeDate,
            $inlineTranslation,
            $addressRenderer,
            $data
        );
    }

    /**
     * Retrieve customer session object.
     *
     * @return \Magento\Customer\Model\Session
     */
    public function getString()
    {
        return $this->_string;
    }

    /**
     * Retrieve customer session object.
     *
     * @return \Magento\Customer\Model\Session
     */
    public function getObjectManager()
    {
        return $this->_objectManager;
    }

    /**
     * Retrieve customer session object.
     *
     * @return \Magento\Customer\Model\Session
     */
    protected function _getSession()
    {
        return $this->_customerSession;
    }



    /**
     * Return PDF document
     *
     * @param array|Collection $invoices
     * @return \Zend_Pdf
     */
    public function getPdf($quotations = [])
    {
        $this->_beforeGetPdf();
        // $this->_initRenderer('invoice');

        $pdf = new \Zend_Pdf();
        $this->_setPdf($pdf);
        $style = new \Zend_Pdf_Style();
        $this->_setFontBold($style, 10);

        foreach ($quotations as $quotation_id) {
            
            $page = $this->newPage();
            $order = $this->_quotation->create()->load($quotation_id);
            /* Add image */
            $this->insertLogo($page);
            /* Add address */
            $this->insertAddress($page);
            /* Add head */
            $this->insertPurchase(
                $page,
                $order
            );

            /* Add table */
            $this->_drawLineHeader($page);
            /* Add body */
            $order_items=$this->_orderitem->create()->getCollection()->addFieldToFilter('purchase_id', $quotation_id);
            $grand_total=0;
            foreach ($order_items as $item) {
                /* Draw item */
                $this->drawPurchaseItem($item, $page, $order);
                $page = end($pdf->pages);
                $grand_total = $grand_total + $item->getSubtotal();
            }
           
            /* Add Grand totals */
            $this->insertGrandTotals($page, $grand_total);
        }
        $this->_afterGetPdf();
        return $pdf;
    }

    protected function insertPurchase(&$page, $order)
    {
        $this->y = $this->y ? $this->y : 815;
        $top = $this->y;

        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0.45));
        $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.45));
        $page->drawRectangle(25, $top, 570, $top - 55);
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(1));
        $this->setDocHeaderCoordinates(array(25, $top, 570, $top - 55));
        $this->_setFontRegular($page, 10);
        
        $page->drawText(
            __('Purchase Order # ') . $order->getIncrementId(), 35, ($top -= 20), 'UTF-8'
        );
        $date = date_create($order->getCreatedAt()); 
        $page->drawText(
            __('Order Date: ') .date_format($date, "g:ia \o\\n l jS F Y"),
            35,
            ($top -= 15),
            'UTF-8'
        );

        $top -= 10;
        $page->setFillColor(new \Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
        $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        $page->drawRectangle(25, $top, 570, ($top - 25));

        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $this->_setFontBold($page, 12);
        $page->drawText(__('Supplier Details:'), 35, ($top - 15), 'UTF-8');

        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(1));
        $page->drawRectangle(25, ($top - 25), 570, $top - 33 - 70);
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $this->_setFontRegular($page, 10);
        $this->y = $top - 40;

        $supplier = $this->_supplier->create()->load($order->getSupplierId());

        $page->drawText($supplier->getName(), 35, $this->y, 'UTF-8');
        $page->drawText($supplier->getCity(), 35, ($this->y -= 10), 'UTF-8');
        $page->drawText($supplier->getState().',', 35, ($this->y -= 10), 'UTF-8');
        $page->drawText($supplier->getZip(), 35, ($this->y -= 10), 'UTF-8');
        $countryName = $supplier->getCountry();
        $page->drawText($countryName, 35, ($this->y -= 10), 'UTF-8');
        $page->drawText("Tel: ".$supplier->getPhone(), 35, ($this->y -= 10), 'UTF-8');
        $this->y -= 25;
    }

    public function drawPurchaseItem($item, &$page, $quotation)
    {
        $lines  = array();

        // draw Product name
        $lines[0] = array(array(
            'text' => $item->getDescription(),
            'feed' => 35,
        ));

        $option_array = unserialize($item->getCustomOptions());
        if (isset($option_array["options"])) {
            foreach ($option_array["options"] as $option) {
                // draw options label
                $lines[][] = array(
                    'text' => strip_tags($option["label"]),
                    'font' => 'italic',
                    'feed' => 35
                );

                if ($option["value"]) {
                    $lines[][] = array(
                            'text' => $option["value"],
                            'feed' => 40
                    );
                }
            }
        }

        // draw SKU
        $lines[0][] = array(
            'text'  => $item->getSku(),
            'feed'  => 290,
            'align' => 'right'
        );

        // $BasePrice = Mage::helper('core')->currency($item->getBasePrice(), true, false);

        $lines[0][] = array(
            'text'  => $this->_pricehelper->currency($item->getBasePrice(), true, false),
            'feed'  => 365,
            'font' => 'bold',
            'align' => 'right'
        );

        // draw QTY
        $lines[0][] = array(
            'text'  => $item->getQuantity() * 1,
            'feed'  => 435,
            'align' => 'right'
        );

        $SubtotalPrice = $this->_pricehelper->currency($item->getSubtotal(), true, false);

        $lines[0][] = array(
            'text'  => $SubtotalPrice,
            'feed'  => 565,
            'font' => 'bold',
            'align' => 'right'
        );

        $lineBlock = array(
            'lines'  => $lines,
            'height' => 20
        );

        $this->drawLineBlocks($page, array($lineBlock), array('table_header' => true));
        $this->setPage($page);
    }

    protected function _drawLineHeader(&$page)
    {
        /* Add table head */
        $this->_setFontRegular($page, 10);
        $page->setFillColor(new \Zend_Pdf_Color_RGB(0.93, 0.92, 0.92));
        $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        $page->drawRectangle(25, $this->y, 570, $this->y -15);
        $this->y -= 10;
        $page->setFillColor(new \Zend_Pdf_Color_RGB(0, 0, 0));

        //columns headers
        $lines[0][] = array(
            'text' => __('Products'),
            'feed' => 35
        );

        $lines[0][] = array(
            'text'  => __('SKU'),
            'feed'  => 290,
            'align' => 'right'
        );

        $lines[0][] = array(
            'text'  => __('Qty'),
            'feed'  => 435,
            'align' => 'right'
        );

        $lines[0][] = array(
            'text'  => __('Price'),
            'feed'  => 360,
            'align' => 'right'
        );       

        $lines[0][] = array(
            'text'  => __('Subtotal'),
            'feed'  => 565,
            'align' => 'right'
        );

        $lineBlock = array(
            'lines'  => $lines,
            'height' => 5
        );

        $this->drawLineBlocks($page, array($lineBlock), array('table_header' => true));
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $this->y -= 20;
    }

    protected function insertGrandTotals($page, $grand_total){
        $lineBlock = array(
            'lines'  => array(),
            'height' => 15
        );
        $Total = $this->_pricehelper->currency($grand_total, true, false);
        $lineBlock['lines'][] = array(
            array(
                'text'      => "Grand Total",
                'feed'      => 475,
                'align'     => 'right',
                'font_size' => 14,
                'font'      => 'bold'
            ),
            array(
                'text'      => $Total,
                'feed'      => 565,
                'align'     => 'right',
                'font_size' => 14,
                'font'      => 'bold'
            ),
        );

        $this->y -= 20;
        $page = $this->drawLineBlocks($page, array($lineBlock));
        return $page;
    }
}
