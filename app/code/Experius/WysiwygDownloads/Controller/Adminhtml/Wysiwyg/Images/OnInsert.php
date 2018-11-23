<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Experius\WysiwygDownloads\Controller\Adminhtml\Wysiwyg\Images;

class OnInsert extends \Magento\Cms\Controller\Adminhtml\Wysiwyg\Images
{
    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
    ) {
        $this->resultRawFactory = $resultRawFactory;
        parent::__construct($context, $coreRegistry);
    }

    /**
     * Fire when select image
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $helper = $this->_objectManager->get(\Magento\Cms\Helper\Wysiwyg\Images::class);
        $storeId = $this->getRequest()->getParam('store');

        $filename = $this->getRequest()->getParam('filename');
        $filename = $helper->idDecode($filename);
        $asIs = $this->getRequest()->getParam('as_is');

        $this->_objectManager->get(\Magento\Catalog\Helper\Data::class)->setStoreId($storeId);
        $helper->setStoreId($storeId);

        $image = $helper->getImageHtmlDeclaration($filename, $asIs);
        /*Replace mp4 video attribute src to video */
        $a = '.mp4';
        if (strpos($image, $a) !== false) {
            $image= str_replace("img",'video width="320" height="240" controls',$image);
        }
        /*Close*/


        /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
        $resultRaw = $this->resultRawFactory->create();
        return $resultRaw->setContents($image);
    }
}