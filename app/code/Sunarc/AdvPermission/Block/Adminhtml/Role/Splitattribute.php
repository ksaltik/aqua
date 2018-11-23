<?php
namespace Sunarc\AdvPermission\Block\Adminhtml\Role;

class Splitattribute extends \Magento\User\Block\Role\Tab\Edit
{
    /**
     * Get restrict by split attribute
     *
     * @return bool
     */
    public function getRestrictBySplitAttribute()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $role = $objectManager->get('Magento\Framework\Registry')->registry('current_role');
        if ($role && $role->getId()) {
            return (bool) $role->getRestrictBySplitattribute();
        }
        return false;
    }
}
