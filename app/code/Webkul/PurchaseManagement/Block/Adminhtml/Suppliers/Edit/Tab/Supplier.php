<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_PurchaseManagement
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\PurchaseManagement\Block\Adminhtml\Suppliers\Edit\Tab;

use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Widget\Form\Generic;

class Supplier extends Generic implements TabInterface
{

    /**
    * @var \Magento\Store\Model\System\Store
    */
    protected $_systemStore;

    protected $_helper;

    /**
    * @param \Magento\Backend\Block\Template\Context $context
    * @param \Magento\Framework\Registry $registry
    * @param \Magento\Framework\Data\FormFactory $formFactory
    * @param \Magento\Store\Model\System\Store $systemStore
    * @param array $data
    */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Webkul\PurchaseManagement\Helper\Data $helper,
        array $data = []
    ) {
        $this->_helper = $helper;
        $this->_systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
    * Prepare form
    *
    * @return $this
    */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('purchasemanagement_suppliers');
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('supplier_');
            $fieldset = $form->addFieldset(
                'base_fieldset',
                ['legend' => __('Supplier Information'), 'class' => 'fieldset-wide']
            );
            if ($model->getId()) {
                $fieldset->addField('id', 'hidden', ['name' => 'id']);
            }
            $fieldset->addField(
                'name',
                'text',
                [
                    'name' => 'name',
                    'label' => __('Name'),
                    'title' => __('Name'),
                    'required' => true,
                ]
            );
            $fieldset->addField(
                'email',
                'text',
                [
                    'name' => 'email',
                    'label' => __('Email'),
                    'title' => __('Email'),
                    'required' => true,
                    'class' => 'validate-email',
                ]
            );
            $fieldset->addField(
                'company',
                'text',
                [
                    'name' => 'company',
                    'label' => __('Company'),
                    'title' => __('Company'),
                    'required' => true,
                ]
            );
            $fieldset->addField(
                'vat',
                'text',
                [
                    'name' => 'vat',
                    'label' => __('Tax/VAT Number'),
                    'title' => __('Tax/VAT Number'),
                ]
            );
            $fieldset->addField(
                'website',
                'text',
                [
                    'name' => 'website',
                    'label' => __('Website'),
                    'title' => __('Website'),
                ]
            );

            $fieldset->addField(
                'gender',
                'select',
                [
                    'label' => __('Gender'),
                    'title' => __('Gender'),
                    'name' => 'gender',
                    'required' => true,
                    'options' => [ '' => __('Please Select'), 1 => __('Male'), 2 => __('Female')]
                ]
            );

            $fieldset->addField(
                'street',
                'text',
                [
                    'name' => 'street',
                    'label' => __('Street Address'),
                    'title' => __('Street 1'),
                    'required' => true,
                ]
            );
            $fieldset->addField(
                'street1',
                'text',
                [
                    'name' => 'street1',
                    'title' => __('Street 2'),
                ]
            );
            $fieldset->addField(
                'city',
                'text',
                [
                    'name' => 'city',
                    'label' => __('City'),
                    'title' => __('City'),
                    'required' => true,
                ]
            );
            $fieldset->addField(
                'state',
                'text',
                [
                    'name' => 'state',
                    'label' => __('State/Province'),
                    'title' => __('State/Province'),
                    'required' => true,
                ]
            );
            $fieldset->addField(
                'country',
                'select',
                [
                    'name' => 'country',
                    'label' => __('Country'),
                    'title' => __('Country'),
                    'required' => true,
                    'values' => $this->_helper->getCountryList()
                ]
            );
            $fieldset->addField(
                'zip',
                'text',
                [
                    'name' => 'zip',
                    'label' => __('Zip/Postal Code'),
                    'title' => __('Zip/Postal Code'),
                    'required' => true,
                ]
            );
            $fieldset->addField(
                'phone',
                'text',
                [
                    'name' => 'phone',
                    'label' => __('Telephone'),
                    'title' => __('Telephone'),
                    'required' => true,
                    'class' => 'validate-digits',
                ]
            );
            $fieldset->addField(
                'fax',
                'text',
                [
                    'name' => 'fax',
                    'label' => __('Fax'),
                    'title' => __('Fax'),
                    'class' => 'validate-digits',
                ]
            );
            $form->setValues($model->getData());
            $this->setForm($form);
            return parent::_prepareForm();
    }

    /**
    * Check permission for passed action
    *
    * @param string $resourceId
    * @return bool
    */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Supplier Data');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Supplier Data');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
}
