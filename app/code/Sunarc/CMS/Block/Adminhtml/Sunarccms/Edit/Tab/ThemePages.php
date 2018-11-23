<?php
namespace Sunarc\CMS\Block\Adminhtml\Sunarccms\Edit\Tab;
use \Sunarc\CMS\Helper\Data;
class ThemePages extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry             $registry
     * @param \Magento\Framework\Data\FormFactory     $formFactory
     * @param \Magento\Store\Model\System\Store       $systemStore
     * @param array                                   $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        Data $helperData,
        array $data = array()
    ) {
        $this->_systemStore = $systemStore;
        $this->sunarccmshelper=$helperData;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /* @var $model \Magento\Cms\Model\Page */
        $model = $this->_coreRegistry->registry('cms_sunarccms');
        $isElementDisabled = false;
        /**
 * @var \Magento\Framework\Data\Form $form 
*/
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('base_fieldset', array('legend' => __('Theme Pages')));

        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', array('name' => 'id'));
        }

        $themeArray=$this->sunarccmshelper->loadThemeList();
        $cmsPages=$this->sunarccmshelper->getAllCMSPages();
        $cmsBlock=$this->sunarccmshelper->getAllStaticBlock();
        $cmsMobilePages=array();
        foreach ($cmsPages as $key=>$value) {
            $check=$value;
            if (strpos(strtolower($check), 'mobile') !== false) {
                $cmsMobilePages[$key] = $value;
            }
        }

        $fieldset->addField(
            'theme_id',
            'select',
            array(
                'name' => 'theme_id',
                'label' => __('Theme'),
                'title' => __('Theme'),
                'values' => $themeArray,
                'required' => true,
            )
        );
        $fieldset->addField(
            'cms_page',
            'select',
            array(
                'name' => 'cms_page',
                'label' => __('CMS Page'),
                'title' => __('CMS Page'),
                'values' => $cmsPages,
                'required' => true,
            )
        );
        $fieldset->addField(
            'mobile_details',
            'select',
            array(
                'name' => 'mobile_details',
                'label' => __('Mobile Page'),
                'title' => __('Mobile Page'),
                'values' => $cmsMobilePages,
                'required' => true,
            )
        );
        $fieldset->addField(
            'block_details',
            'multiselect',
            array(
                'name' => 'block_details',
                'label' => __('Static Block'),
                'title' => __('Static Block'),
                'values' => $cmsBlock,
                'required' => true,
            )
        );



        /*{{CedAddFormField}}*/
        
        if (!$model->getId()) {
            $model->setData('status', $isElementDisabled ? '2' : '1');
        }

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();   
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Theme Pages');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Theme Pages');
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

    /**
     * Check permission for passed action
     *
     * @param  string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
