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
namespace Webkul\PurchaseManagement\Block\Adminhtml\Picking\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('picking_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Shipments Information'));
    }

    /**
    * Prepare Layout
    *
    * @return $this
    */
    protected function _prepareLayout()
    {
        $this->addTab(
            'form_section',
            [
                'label' => __('Incoming Shipments'),
                'title' => __('Incoming Shipments')
            ]
        );
        return parent::_prepareLayout();
    }
}
