<?php

namespace Sunarc\AdvPermission\Model\ResourceModel\Block\Widget;

class Button extends \Magento\Backend\Block\Widget\Button
{
	/**
     * Prepare attributes
     *
     * @param string $title
     * @param array $classes
     * @param string $disabled
     * @return array
     */
    protected function _prepareAttributes($title, $classes, $disabled)
    {
        $attributes = [
            'id' => $this->getId(),
            'name' => $this->getElementName(),
            'title' => $title,
            'type' => $this->getType(),
            'class' => join(' ', $classes),
            'onclick' => $this->getOnClick(),
            'style' => $this->getStyle(),
            'value' => $this->getValue(),
            'disabled' => $disabled,
        ];

        $_objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $authSession = $_objectManager->get('Magento\Backend\Model\Auth\Session');
        $userFactory = $_objectManager->get('Magento\User\Model\UserFactory');
        
        if( $authSession->getUser() ){
            $user = $authSession->getUser();
            $userId = $user->getUserId();
            $userDetails = $userFactory->create()->load($userId);
            $role = $userDetails->getRole();

            if( $role->getRoleName() != null && $role->getRoleName() != 'Administrators' && $this->getId() != null && $this->getId() == 'add_root_category_button' ){
                $attributes = [
                    'style' => 'display:none;',
                ];  
            } 
        }

        if ($this->getDataAttribute()) {
            foreach ($this->getDataAttribute() as $key => $attr) {
                $attributes['data-' . $key] = is_scalar($attr) ? $attr : json_encode($attr);
            }
        }
        return $attributes;
    }
}