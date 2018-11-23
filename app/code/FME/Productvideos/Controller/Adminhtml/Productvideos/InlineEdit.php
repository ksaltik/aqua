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

use Magento\Backend\App\Action\Context;
use FME\Productvideos\Model\Productvideos as Productvideos;
use Magento\Framework\Controller\Result\JsonFactory;

class InlineEdit extends \Magento\Backend\App\Action
{
    /**
     * @var PostDataProcessor
     */
    protected $dataProcessor;

    /**
     * @var Productvideos
     */
    protected $productvideos;

    /**
     * @var JsonFactory
     */
    protected $jsonFactory;

    /**
     * @param Context           $context
     * @param PostDataProcessor $dataProcessor
     * @param Productvideos     $productvideos
     * @param JsonFactory       $jsonFactory
     */
    public function __construct(
        Context $context,
        PostDataProcessor $dataProcessor,
        Productvideos $productvideos,
        JsonFactory $jsonFactory
    ) 
    {
        parent::__construct($context);
        $this->dataProcessor = $dataProcessor;
        $this->productvideos = $productvideos;
        $this->jsonFactory = $jsonFactory;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /**
 * @var \Magento\Framework\Controller\Result\Json $resultJson
*/
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        $postItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && count($postItems))) {
            return $resultJson->setData(
                [
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
                ]
            );
        }

        foreach (array_keys($postItems) as $productvideosId) {
            /**
 * @var \Magento\Productvideos\Model\Productvideos $productvideos
*/
            $productvideos = $this->productvideos->load($productvideosId);
            try {
                $productvideosData = $this->dataProcessor->filter(
                    $postItems[$productvideosId]
                );
                $productvideos->setData($productvideosData);
                $productvideos->save();
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $messages[] = $this->getErrorWithVideoId(
                    $productvideos,
                    $e->getMessage()
                );
                $error = true;
            } catch (\RuntimeException $e) {
                $messages[] = $this->getErrorWithVideoId(
                    $productvideos,
                    $e->getMessage()
                );
                $error = true;
            } catch (\Exception $e) {
                $messages[] = $this->getErrorWithVideoId(
                    $productvideos,
                    __('Something went wrong while saving the productvideos.')
                );
                $error = true;
            }
        }

        return $resultJson->setData(
            [
            'messages' => $messages,
            'error' => $error
            ]
        );
    }

    /**
     * Add productvideos title to error message
     *
     * @param  ProductvideosInterface $productvideos
     * @param  string                 $errorText
     * @return string
     */
    protected function getErrorWithVideoId(
        Productvideos $productvideos,
        $errorText
    )
    {
        return '[Productvideos ID: ' . $productvideos->getVideoId() . '] ' . $errorText;
    }
    protected function _isAllowed()
    {
        return true;
    }
}
