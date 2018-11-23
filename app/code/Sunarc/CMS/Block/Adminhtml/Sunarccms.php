<?php
namespace Sunarc\CMS\Block\Adminhtml;
class Sunarccms extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        
        $this->_controller = 'adminhtml_sunarccms';/*block grid.php directory*/
        $this->_blockGroup = 'Sunarc_CMS';
        $this->_headerText = __('Sunarc CMS');
        $this->_addButtonLabel = __('Add New Entry'); 
        parent::_construct();
        
    }
}
