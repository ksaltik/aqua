<?php

namespace Sunarc\AdvPermission\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Api\WebsiteRepositoryInterface;
use Magento\Store\Api\GroupRepositoryInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Ui\Component\Form;
use Magento\Ui\Component\DynamicRows;

class Websites extends \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\Websites
{	
	/**
     * Prepares children for the parent fieldset
     *
     * @return array
     * @since 101.0.0
     */
    protected function getFieldsForFieldset()
    {
        $children = [];
        $websiteIds = $this->getWebsitesValues();
        $websitesList = $this->getWebsitesList();

        $_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $authSession = $_objectManager->get('Magento\Backend\Model\Auth\Session');
        $userFactory = $_objectManager->get('Magento\User\Model\UserFactory');

        $isNewProduct = !$this->locator->getProduct()->getId();
        $tooltip = [
            'link' => 'http://docs.magento.com/m2/ce/user_guide/configuration/scope.html',
            'description' => __(
                'If your Magento installation has multiple websites, ' .
                'you can edit the scope to use the product on specific sites.'
            ),
        ];
        $sortOrder = 0;
        $label = __('Websites');

        $defaultWebsiteId = $this->websiteRepository->getDefault()->getId();
        foreach ($websitesList as $website) {
            $isChecked = in_array($website['id'], $websiteIds)
                || ($defaultWebsiteId == $website['id'] && $isNewProduct);


            $children[$website['id']] = [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'dataType' => Form\Element\DataType\Number::NAME,
                            'componentType' => Form\Field::NAME,
                            'formElement' => Form\Element\Checkbox::NAME,
                            'description' => __($website['name']),
                            'tooltip' => $tooltip,
                            'sortOrder' => $sortOrder,
                            'dataScope' => 'website_ids.' . $website['id'],
                            'label' => $label,
                            'valueMap' => [
                                'true' => (string)$website['id'],
                                'false' => '0',
                            ],
                            'value' => $isChecked ? (string)$website['id'] : '0',
                        ],
                    ],
                ],
            ];


            if( $authSession->getUser() != null ){
                $user = $authSession->getUser();
                $userId = $user->getUserId();
                $userDetails = $userFactory->create()->load($userId);
                $role = $userDetails->getRole();
            
                if(  $role->getwebsiteId() != null ){

                    if( $website['id'] != $role->getwebsiteId() ){


                    	$children[$website['id']] = [
			                'arguments' => [
			                    'data' => [
			                        'config' => [
			                            'dataType' => Form\Element\DataType\Number::NAME,
			                            'componentType' => Form\Field::NAME,
			                            'formElement' => Form\Element\Checkbox::NAME,
			                            'description' => __($website['name']),
			                            'tooltip' => $tooltip,
			                            'sortOrder' => $sortOrder,
			                            'dataScope' => 'website_ids.' . $website['id'],
			                            'label' => $label,
			                            'valueMap' => [
			                                'true' => (string)$website['id'],
			                                'false' => '0',
			                            ],
			                            'value' => $isChecked ? (string)$website['id'] : '0',
			                            'disabled' => 'disabled',
			                        ],
			                    ],
			                ],
			            ];

                    } 
                }
            }
            

            $sortOrder++;
            $tooltip = null;
            $label = ' ';

            if (!$isNewProduct && !in_array($website['id'], $websiteIds) && $website['storesCount']) {
                $children['copy_to_stores.' . $website['id']] = $this->getDynamicRow($website['id'], $sortOrder);
                $sortOrder++;
            }
        }

        return $children;
    }

}