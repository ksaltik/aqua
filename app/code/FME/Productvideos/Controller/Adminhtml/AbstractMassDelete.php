<?php
/**
 * FME
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
 * Do not edit or add to this file if you wish to upgrade FME Productvideoss to newer
 * versions in the future. If you wish to customize our Productvideoss extension for your
 * needs please contact us on support@jorhna.com.
 *
 * @category  FME
 * @package   FME_Productvideoss
 * @copyright Copyright (c) 2014 FME. (http://www.jorhna.com)
 * @author    Ahmed Javed <info@jorhna.com>
 */


namespace FME\Productvideos\Controller\Adminhtml;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class AbstractMassDelete
 */
class AbstractMassDelete extends \Magento\Backend\App\Action
{

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


    protected $cat = false;

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

        try {
            if (isset($excluded)) {
                if (!empty($excluded)) {
                    $this->excludedDelete($excluded);
                } else {
                    $this->deleteAll();
                }
            } elseif (!empty($selected)) {
                $this->selectedDelete($selected);
            } else {
                $this->messageManager->addError(__('Please select item(s).'));
            }
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }

        /**
 * @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect
*/
        $resultRedirect = $this->resultFactory->create(
            ResultFactory::TYPE_REDIRECT
        );
        return $resultRedirect->setPath(static::REDIRECT_URL);
    }

    /**
     * Delete all
     *
     * @return void
     * @throws \Exception
     */
    protected function deleteAll()
    {
        /**
 * @var AbstractCollection $collection
*/
        $collection = $this->_objectManager->get($this->collection);
        $this->delete($collection);
    }

    /**
     * Delete all but the not selected
     *
     * @param  array $excluded
     * @return void
     * @throws \Exception
     */
    protected function excludedDelete(array $excluded)
    {
        /**
 * @var AbstractCollection $collection
*/
        $collection = $this->_objectManager->get($this->collection);
        $collection->addFieldToFilter(static::ID_FIELD, ['nin' => $excluded]);
        $this->delete($collection);
    }

    /**
     * Delete selected items
     *
     * @param  array $selected
     * @return void
     * @throws \Exception
     */
    protected function selectedDelete(array $selected)
    {
        /**
 * @var AbstractCollection $collection
*/
        $collection = $this->_objectManager->get($this->collection);
        $collection->addFieldToFilter(static::ID_FIELD, ['in' => $selected]);
        $this->delete($collection);
    }

    /**
     * Delete collection items
     *
     * @param  AbstractCollection $collection
     * @return int
     */
    protected function delete(AbstractCollection $collection)
    {
        $count = 0;
        foreach ($collection->getAllIds() as $id) {
            /**
 * @var \Magento\Framework\Model\AbstractModel $model
*/
            if ($this->cat) {
                $msg = $this->_objectManager->create(
                    'FME\Productvideos\Model\Productcats'
                )->deleteCategory($id);
            } else {
                $msg = null;
            }

            if ($msg!=null) {
                $this->messageManager->addError($msg);
                return;
            } else {
                $model = $this->_objectManager->get($this->model);
                $model->load($id);
                $model->delete();
                ++$count;
            }
        }

        $this->setSuccessMessage($count);
        return $count;
    }

    /**
     * Set error messages
     *
     * @param  int $count
     * @return void
     */
    protected function setSuccessMessage($count)
    {
        $this->messageManager->addSuccess(
            __(
                'A total of %1 record(s) have been deleted.',
                $count
            )
        );
    }

    protected function _isAllowed()
    {
        return true;
    }
}
