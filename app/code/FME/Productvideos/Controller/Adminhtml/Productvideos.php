<?php

/* ////////////////////////////////////////////////////////////////////////////////
  \\\\\\\\\\\\\\\\\\\\\\\\\  FME Productvideos Module  \\\\\\\\\\\\\\\\\\\\\\\\\
  /////////////////////////////////////////////////////////////////////////////////
  \\\\\\\\\\\\\\\\\\\\\\\\\ NOTICE OF LICENSE\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
  ///////                                                                   ///////
  \\\\\\\ This source file is subject to the Open Software License (OSL 3.0)\\\\\\\
  ///////   that is bundled with this package in the file LICENSE.txt.      ///////
  \\\\\\\   It is also available through the world-wide-web at this URL:    \\\\\\\
  ///////          http://opensource.org/licenses/osl-3.0.php               ///////
  \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
  ///////                      * @category   FME                            ///////
  \\\\\\\                      * @package    FME_Productvideos              \\\\\\\
  ///////    * @author    FME Extensions <support@fmeextensions.com>   ///////
  \\\\\\\                                                                   \\\\\\\
  /////////////////////////////////////////////////////////////////////////////////
  \\* @copyright  Copyright 2015 © fmeextensions.com All right reserved\\\
  /////////////////////////////////////////////////////////////////////////////////
 */

namespace FME\Productvideos\Controller\Adminhtml;

use \Magento\Framework\View\Result\LayoutFactory;

abstract class Productvideos extends \Magento\Backend\App\Action
{

    protected $_resultLayoutFactory;
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param \Magento\Backend\App\Action\Context                   $context
     * @param \Magento\Catalog\Controller\Adminhtml\Product\Builder $productBuilder
     * @param \Magento\Framework\View\Result\PageFactory            $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Catalog\Controller\Adminhtml\Product\Builder $productBuilder,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        LayoutFactory $resultLayoutFactory
    ) 
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_resultLayoutFactory = $resultLayoutFactory;
    }

    protected function _initAction()
    {
        $resultPage = $this->resultPageFactory->create();
        return $resultPage;
    }

    protected function _initProductProductvideos()
    {
        $productvideos = $this->_objectManager->create('FME\Productvideos\Model\Productvideos');
        $productvideosId = (int) $this->getRequest()->getParam('video_id');

        if ($productvideosId) {
            $productvideos->load($productvideosId);
        }

        $this->_objectManager->get(
            'Magento\Framework\Registry'
        )->register(
            'current_productvideos_products',
            $productvideos
        );

        return $productvideos;
    }

    protected function _isAllowed()
    {
        return true;
    }
}
