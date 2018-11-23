<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace FME\Productvideos\Block\Adminhtml\Productvideos\Edit;

class AssignProducts extends \Magento\Backend\Block\Template
{
    
    protected $_template = 'productvideos/assign_products.phtml';

   
    protected $blockGrid;

    protected $registry;

    protected $jsonEncoder;

   
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        array $data = []
    ) 
    {
        $this->registry = $registry;
        $this->jsonEncoder = $jsonEncoder;
        parent::__construct($context, $data);
    }

    
    public function getBlockGrid()
    {
        
        
        if (null === $this->blockGrid) {
            $this->blockGrid = $this->getLayout()->createBlock(
                'FME\Productvideos\Block\Adminhtml\Productvideos\Edit\Tab\Products',
                'category.product.grid'
            );
        }

        return $this->blockGrid;
    }

    /**
     * Return HTML of grid block
     *
     * @return string
     */
    public function getGridHtml()
    {

        return $this->getBlockGrid()->toHtml();
    }

    /**
     * @return string
     */
    public function getProductsJson()
    {
        

        $products = $this->getCategory()->getProductsPosition();
        
        if (!empty($products)) {
            return $this->jsonEncoder->encode($products);
        }

        return '{}';
    }
    /**
     * Retrieve current category instance
     *
     * @return array|null
     */
    public function getCategory()
    {
        return $this->registry->registry('productvideos_data');
    }
}
