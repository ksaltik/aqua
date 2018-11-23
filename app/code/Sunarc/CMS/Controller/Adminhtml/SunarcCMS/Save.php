<?php
namespace Sunarc\CMS\Controller\Adminhtml\SunarcCMS;
use Magento\Framework\App\Filesystem\DirectoryList;
class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        
        $data = $this->getRequest()->getParams();
        if ($data) {
            $model = $this->_objectManager->create('Sunarc\CMS\Model\Sunarccms');


            $data['block_details'] = implode(',', $this->getRequest()->getPost('block_details'));
            $id = $this->getRequest()->getParam('id');

            $collection = $this->_objectManager->create('Sunarc\CMS\Model\ResourceModel\Sunarccms\Collection');
            if ($id) {
                $collection->AddFieldToFilter('id', ['neq' => $id]);
                $model->load($id);
            }

            foreach($collection->getData() as $cdata){
                if($data['theme_id']==$cdata['theme_id']) {
                    $this->messageManager->addError(__('Theme already using in another item.'));
                    $this->_getSession()->setFormData($data);
                    $this->_redirect('*/*/edit', array('banner_id' => $this->getRequest()->getParam('banner_id')));
                    return;
                }
            }
            $model->setData($data);
            
            try {
                $model->save();
                $this->messageManager->addSuccess(__('The First Grid Has been Saved.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId(), '_current' => true));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the banner.'));
            }

            $this->_getSession()->setFormData($data);
            $this->_redirect('*/*/edit', array('banner_id' => $this->getRequest()->getParam('banner_id')));
            return;
        }
        $this->_redirect('*/*/');
    }
}
