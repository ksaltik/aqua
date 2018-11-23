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

abstract class MassAction extends \Magento\Backend\App\Action
{
    /**
     * Splitattr repository
     *
     * @var \Sunarc\AdvPermission\Api\SplitattrRepositoryInterface
     */
    protected $splitattrRepository;

    /**
     * Mass Action filter
     *
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    protected $filter;

    /**
     * Splitattr collection factory
     *
     * @var \Sunarc\AdvPermission\Model\ResourceModel\Splitattr\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Action success message
     *
     * @var string
     */
    protected $successMessage;

    /**
     * Action error message
     *
     * @var string
     */
    protected $errorMessage;

    /**
     * constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Sunarc\AdvPermission\Api\SplitattrRepositoryInterface $splitattrRepository
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param \Sunarc\AdvPermission\Model\ResourceModel\Splitattr\CollectionFactory $collectionFactory
     * @param string $successMessage
     * @param string $errorMessage
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Sunarc\AdvPermission\Api\SplitattrRepositoryInterface $splitattrRepository,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Sunarc\AdvPermission\Model\ResourceModel\Splitattr\CollectionFactory $collectionFactory,
        $successMessage,
        $errorMessage
    ) {
        $this->splitattrRepository = $splitattrRepository;
        $this->filter              = $filter;
        $this->collectionFactory   = $collectionFactory;
        $this->successMessage      = $successMessage;
        $this->errorMessage        = $errorMessage;
        parent::__construct($context);
    }

    /**
     * @param \Sunarc\AdvPermission\Api\Data\SplitattrInterface $splitattr
     * @return mixed
     */
    abstract protected function massAction(\Sunarc\AdvPermission\Api\Data\SplitattrInterface $splitattr);

    /**
     * execute action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $collectionSize = $collection->getSize();
            foreach ($collection as $splitattr) {
                $this->massAction($splitattr);
            }
            $this->messageManager->addSuccessMessage(__($this->successMessage, $collectionSize));
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, $this->errorMessage);
        }
        $redirectResult = $this->resultRedirectFactory->create();
        $redirectResult->setPath('Sunarc_AdvPermission/*/index');
        return $redirectResult;
    }
}
