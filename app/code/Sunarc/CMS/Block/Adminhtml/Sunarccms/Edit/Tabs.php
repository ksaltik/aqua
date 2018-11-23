<?php
namespace Sunarc\CMS\Block\Adminhtml\Sunarccms\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
        
        parent::_construct();
        $this->setId('checkmodule_sunarccms_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Sunarccms Information'));
    }
}