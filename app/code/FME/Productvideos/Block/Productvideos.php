<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace FME\Productvideos\Block;

class Productvideos extends \Magento\Framework\View\Element\Template
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * Productvideos resource model
     *
     * @var \FME\Productvideos\Model\Productvideos
     */
    protected $_productvideos;

    public $_helper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry                      $registry
     * @param \FME\Productvideos\Model\Productvideos           $model
     * @param \FME\Productvideos\Helper\Data                   $helper
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \FME\Productvideos\Model\Productvideos $model,
        \FME\Productvideos\Helper\Data $helper,
        array $data = []
    ) 
    {
         $this->_coreRegistry = $registry;
        $this->_productvideos = $model;
        $this->_helper = $helper;
        parent::__construct($context, $data);

        $this->setTabTitle();
    }

    /**
     * Get current product id
     *
     * @return null|int
     */
    public function getProductId()
    {
        $product = $this->_coreRegistry->registry('product');
        return $product ? $product->getId() : null;
    }

    
    /**
     * Set tab title
     *
     * @return void
     */
    public function setTabTitle()
    {
        $title = $this->_helper->getTitle();
        if ($title == null) {
            $title = "Product Videos";
        }

        $this->setTitle(__($title));
    }

    public function getProductVideos()
    {
       
        $productId = $this->getProductId();
        $collection = $this->_productvideos->getProductRelatedVideos($productId);
       // print_r($collection->getData());
        //exit;
        return $collection;
    }
}
