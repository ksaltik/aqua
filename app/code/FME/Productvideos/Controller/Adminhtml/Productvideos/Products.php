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

namespace FME\Productvideos\Controller\Adminhtml\Productvideos;

class Products extends \FME\Productvideos\Controller\Adminhtml\Productvideos
{

    public function execute()
    {
        //echo "this"; exit;
        $resultLayout = $this->_resultLayoutFactory->create();
        $this->_initProductProductvideos();

        $resultLayout->getLayout()->getBlock('productvideos.edit.tab.products')
            ->setRelatedProducts(
                $this->getRequest()->getPost(
                    'products_related',
                    null
                )
            );
        return $resultLayout;
    }
}
