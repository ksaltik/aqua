<?php
/**
 * Sunarc_AdvPermission extension
 * NOTICE OF LICENSE
 *
 * This source file is subject to the SunArc Technologies License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://sunarctechnologies.com/end-user-agreement/
 *
 * @category  Sunarc
 * @package   Sunarc_AdvPermission
 * @copyright Copyright (c) 2017
 * @license
 */
namespace Sunarc\AdvPermission\Controller\Adminhtml\Splitattr;

class Save extends \Sunarc\AdvPermission\Controller\Adminhtml\Splitattr
{
    /**
     * Splitattr factory
     *
     * @var \Sunarc\AdvPermission\Api\Data\SplitattrInterfaceFactory
     */
    protected $splitattrFactory;

    /**
     * Data Object Processor
     *
     * @var \Magento\Framework\Reflection\DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * Data Object Helper
     *
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * Uploader pool
     *
     * @var \Sunarc\AdvPermission\Model\UploaderPool
     */
   // protected $uploaderPool;

    /**
     * Data Persistor
     *
     * @var \Magento\Framework\App\Request\DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Sunarc\AdvPermission\Api\SplitattrRepositoryInterface $splitattrRepository
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Sunarc\AdvPermission\Api\Data\SplitattrInterfaceFactory $splitattrFactory
     * @param \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor
     * @param \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
     * @param \Sunarc\AdvPermission\Model\UploaderPool $uploaderPool
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Sunarc\AdvPermission\Api\SplitattrRepositoryInterface $splitattrRepository,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Sunarc\AdvPermission\Api\Data\SplitattrInterfaceFactory $splitattrFactory,
        \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        //\Sunarc\AdvPermission\Model\UploaderPool $uploaderPool,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
    ) {
        $this->splitattrFactory    = $splitattrFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->dataObjectHelper    = $dataObjectHelper;
        //$this->uploaderPool        = $uploaderPool;
        $this->dataPersistor       = $dataPersistor;
        parent::__construct($context, $coreRegistry, $splitattrRepository, $resultPageFactory);
    }

    /**
     * run the action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        /** @var \Sunarc\AdvPermission\Api\Data\SplitattrInterface $splitattr */
        $splitattr = null;
        $postData = $this->getRequest()->getPostValue();
        $data = $postData;
        $data = $this->filterData($data);
        $id = !empty($data['splitattr_id']) ? $data['splitattr_id'] : null;
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            if ($id) {
                $splitattr = $this->splitattrRepository->getById((int)$id);
            } else {
                unset($data['splitattr_id']);
                $splitattr = $this->splitattrFactory->create();
            }
            $this->dataObjectHelper->populateWithArray($splitattr, $data, \Sunarc\AdvPermission\Api\Data\SplitattrInterface::class);
            $this->splitattrRepository->save($splitattr);
            $this->messageManager->addSuccessMessage(__('You saved the split order attribute'));
            $this->dataPersistor->clear('Sunarc_AdvPermission_splitattr');
            if ($this->getRequest()->getParam('back')) {
                $resultRedirect->setPath('Sunarc_AdvPermission/splitattr/edit', ['splitattr_id' => $splitattr->getId()]);
            } else {
                $resultRedirect->setPath('Sunarc_AdvPermission/splitattr');
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->dataPersistor->set('Sunarc_AdvPermission_splitattr', $postData);
            $resultRedirect->setPath('Sunarc_AdvPermission/splitattr/edit', ['splitattr_id' => $id]);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('There was a problem saving the split order attribute'));
            $this->dataPersistor->set('Sunarc_AdvPermission_splitattr', $postData);
            $resultRedirect->setPath('Sunarc_AdvPermission/splitattr/edit', ['splitattr_id' => $id]);
        }
        return $resultRedirect;
    }

    /**
     * @param string $type
     * @return \Sunarc\AdvPermission\Model\Uploader
     * @throws \Exception
     */
    /*protected function getUploader($type)
    {
        return $this->uploaderPool->getUploader($type);
    }*/
}
