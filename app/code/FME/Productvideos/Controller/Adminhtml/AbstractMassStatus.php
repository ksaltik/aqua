<?php
/**
 * Jorhna
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Jorhna Affiliates to newer
 * versions in the future. If you wish to customize our Affiliates extension for your
 * needs please contact us on support@jorhna.com.
 *
 * @category  Jorhna
 * @package   Jorhna_Affiliates
 * @copyright Copyright (c) 2014 Jorhna. (http://www.jorhna.com)
 * @author    Ahmed Javed <info@jorhna.com>
 */


namespace FME\Productvideos\Controller\Adminhtml;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class AbstractMassStatus
 */
class AbstractMassStatus extends \Magento\Backend\App\Action
{
    /**
     * Field id
     */
    const ID_FIELD = '';

    /**
     * Redirect url
     */
    const REDIRECT_URL = '*/*/';

    /**
     * Resource collection
     *
     * @var string
     */
    protected $collection = 'Magento\Framework\Model\Resource\Db\Collection\AbstractCollection';

    /**
     * Model
     *
     * @var string
     */
    protected $model = 'Magento\Framework\Model\AbstractModel';


    /**
     * Item status
     *
     * @var bool
     */
    protected $status = 0;

    /**
     * Item approved
     *
     * @var bool
     */
    protected $approved = 0;

    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        $selected = $this->getRequest()->getParam('selected');
        $excluded = $this->getRequest()->getParam('excluded');

        /*  echo '<pre> selected '; print_r($_POST);
        echo '<br> excluded '; print_r($excluded);
        exit;*/
        
        try {
            if (isset($excluded)) {
                if (is_array($excluded) && !empty($excluded)) {
                    $this->excludedSetStatus($excluded);
                } else {
                    $this->setStatusAll();
                }
            } elseif (!empty($selected)) {
                $this->selectedSetStatus($selected);
            } else {
                $this->messageManager->addError(__('Please select item(s).'));
            }
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }

        /**
 * @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect
*/
        $resultRedirect = $this->resultFactory
        ->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath(static::REDIRECT_URL);
    }

    /**
     * Set status to all
     *
     * @return void
     * @throws \Exception
     */
    protected function setStatusAll()
    {
        /**
 * @var AbstractCollection $collection
*/
        $collection = $this->_objectManager->get($this->collection);
        $this->setStatus($collection);
    }

    /**
     * Set status to all but the not selected
     *
     * @param  array $excluded
     * @return void
     * @throws \Exception
     */
    protected function excludedSetStatus(array $excluded)
    {
        /**
 * @var AbstractCollection $collection
*/
        $collection = $this->_objectManager->get($this->collection);
        $collection->addFieldToFilter(static::ID_FIELD, ['nin' => $excluded]);
        $this->setStatus($collection);
    }

    /**
     * Set status to selected items
     *
     * @param  array $selected
     * @return void
     * @throws \Exception
     */
    protected function selectedSetStatus(array $selected)
    {
        /**
 * @var AbstractCollection $collection
*/
        $collection = $this->_objectManager->get($this->collection);
        $collection->addFieldToFilter(static::ID_FIELD, ['in' => $selected]);
        $this->setStatus($collection);
    }

    /**
     * Set status to collection items
     *
     * @param  AbstractCollection $collection
     * @return void
     */
    protected function setStatus(AbstractCollection $collection)
    {
        
        foreach ($collection->getAllIds() as $id) {
            /**
 * @var \Magento\Framework\Model\AbstractModel $model
*/
            $model = $this->_objectManager->get($this->model);
            $model->load($id);
            $model->setStatus($this->status);
            $model->save();
        }

        if ($this->approved !== 0) {
            foreach ($collection->getAllIds() as $id) {
                /**
 * @var \Magento\Framework\Model\AbstractModel $model
*/
                $model = $this->_objectManager->get($this->model);
                $model->load($id);
                $model->setApproved($this->approved);
                $model->save();
            }
        }

        $this->setSuccessMessage(count($collection));
    }

    protected function setSuccessMessage($count)
    {
        $this->messageManager->addSuccess(
            __(
                'A total of %1 record(s) have been Updated.',
                $count
            )
        );
    }

    protected function _isAllowed()
    {
        return true;
    }
}
