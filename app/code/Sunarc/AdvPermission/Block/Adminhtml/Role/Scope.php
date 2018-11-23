<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Sunarc\AdvPermission\Block\Adminhtml\Role;

use Magento\Framework\App\RequestInterface;
//use Sunarc\AdvPermission\Helper\Data;
/**
 * @api
 * @since 100.0.2
 */
class Scope extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * Password input filed name
     */
    const IDENTITY_VERIFICATION_PASSWORD_FIELD = 'current_password';


   protected $request;
    protected $_coreRegistry = null;


    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\DataObjectFactory $objectFactory,
        \Magento\Framework\Stdlib\DateTime\Timezone $dateTime,
        \Magento\Store\Model\System\Store $systemStore,
      //  \Magento\Framework\App\Request\Http $request,
        array $data = [])
    {
      // $this->request = $request;
      /// $this->_helper = $helper;
        $this->_objectFactory = $objectFactory;
         $this->_systemStore = $systemStore;
        $this->dateTime = $dateTime; $this->_systemStore = $systemStore;
        $this->_coreRegistry = $registry;
        parent::__construct($context, $registry, $formFactory, $data);


    }


    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Advanced:Scope');
    }

    /**
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * @return $this
     */
    public function _beforeToHtml()
    {
        $this->_initForm();

        return parent::_beforeToHtml();
    }

    /**
     * @return void
     */
    protected function _initForm()
    {
        /** @var \Magento\Framework\Data\Form $form */

        $role = '';
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $request = $objectManager->get('Magento\Framework\App\RequestInterface');

        if($request->getParam('rid'))
          {
              $role = $this->_coreRegistry->registry('current_role');
          }
        if ($role && is_array($role->getData())) {
            $data = $role->getData();
            $val = explode(',', $data['store_ids']);


        }

        $form = $this->_formFactory->create();

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Access Scope')]);

        $all = array('label' => 'All Website', 'value' => null );

        $webOptionArr = $this->_systemStore->getWebsiteValuesForForm(true);
        $webOptionArr[0] = $all;

        $fieldset->addField(
            'website_id',
            'select',
            [
                'name' => 'website_id',
                'label' => __('Associate to Website'),
                'title' => __('Associate to Website'),
                'required' => false,
                'values' =>  $webOptionArr,

            ]
        );

        $fieldset->addField(
            'store_ids',
            'multiselect',
            [
                'name'     => 'store_ids[]',
                'label'    => __('Store Views'),
                'title'    => __('Store Views'.''),
                'required' => false,
                'values'   => $this->_systemStore->getStoreValuesForForm(false, true)
            ]
        );

        /*$fieldset->addField(
        'is_order_restrict_by_scope',
        'select',
        [
            'name' => 'is_order_restrict_by_scope',
            'label' => __('Limit Access To Orders'),
            'title' => __('Limit Access To Orders'),
            'class' => 'input-select',
            'options' => ['1' => __('Yes'), '0' => __('No')]
        ]
    );*/

        /*$fieldset->addField(
            'is_invoice_restrict_by_scope',
            'select',
            [
                'name' => 'is_invoice_restrict_by_scope',
                'label' => __('Limit Access To Invoices and Transaction'),
                'title' => __('Limit Access To Invoices and Transaction'),
                'class' => 'input-select',
                'options' => ['1' => __('Yes'), '0' => __('No')]
            ]
        );*/

       /* $fieldset->addField(
            'is_shipment_restrict_by_scope',
            'select',
            [
                'name' => 'is_shipment_restrict_by_scope',
                'label' => __('Limit Access To Shipments'),
                'title' => __('Limit Access To Shipments'),
                'class' => 'input-select',
                'options' => ['1' => __('Yes'), '0' => __('No')]
            ]
        );*/

        /*$fieldset->addField(
            'is_creditmemo_restrict_by_scope',
            'select',
            [
                'name' => 'is_creditmemo_restrict_by_scope',
                'label' => __('Limit Access To Credit Memos'),
                'title' => __('Limit Access To Credit Memos'),
                'class' => 'input-select',
                'options' => ['1' => __('Yes'), '0' => __('No')]
            ]
        );*/


       $data =  array();
        if ($role && is_array($role->getData())) {
            $data = $role->getData();
           
            $data['store_ids'] = explode(',',$data['store_ids']);
        }

        $form->setValues($data);
        $this->setForm($form);

    }

    /**
     * Get old Users Form Data
     *
     * @return null|string
     */
    protected function getOldUsers()
    {
        return $this->_coreRegistry->registry(
            \Magento\User\Controller\Adminhtml\User\Role\SaveRole::IN_ROLE_OLD_USER_FORM_DATA_SESSION_KEY
        );
    }
}
