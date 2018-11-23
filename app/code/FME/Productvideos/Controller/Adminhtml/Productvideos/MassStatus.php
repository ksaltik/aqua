<?php
/*////////////////////////////////////////////////////////////////////////////////
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

class MassStatus extends \FME\Productvideos\Controller\Adminhtml\Productvideos
{
    
    public function execute()
    {
        
        $productvideosIds = $this->getRequest()->getParam('productvideos');
        if (!is_array($productvideosIds)) {
            $this->messageManager->addError(__('Please select item(s)'));
        } else {
            try {
                foreach ($productvideosIds as $productvideosId) {
                    $productvideos = $this->_objectManager->get(
                        'FME\Productvideos\Model\Productvideos'
                    )->load($productvideosId)
                    ->setStatus($this->getRequest()->getParam('status'))
                    ->setIsMassupdate(true)
                    ->save();
                }

                $this->messageManager->addSuccess(
                    __(
                        'Total of %d record(s) were successfully updated',
                        count($productvideosIds)
                    )
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/index');
    }
}
