<?php

namespace Sunarc\CMS\Plugin;

/**
 * Created by PhpStorm.
 * User: sunarctechnologies
 * Date: 2/11/17
 */

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;

class TopmenuPlugin
{
    protected $scopeConfig;
    protected $storeManager;
    /**
     * TopmenuPlugin constructor.
     *
     * @param ScopeConfigInterface  $scopeConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(ScopeConfigInterface $scopeConfig, StoreManagerInterface $storeManager)
    {
        $this->scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
    }

    /**
     * @param \Magento\Theme\Block\Html\Topmenu $topmenu
     * @param $html
     * @return string
     */
    public function afterGetHtml(\Magento\Theme\Block\Html\Topmenu $topmenu, $html)
    {
        /*$moduleStatus = $this->scopeConfig->getValue('reversebid/general/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        $enablePath = 'reversebid/general/auction_page_status';
        $enablePageStatus = $this->scopeConfig->getValue($enablePath, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        $closePath = 'reversebid/general/auction_expired';
        $enableClosePageStatus = $this->scopeConfig->getValue($closePath, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $menuPath = 'reversebid/general/auction_page_menu';
        $menuTitle = trim($this->scopeConfig->getValue($menuPath, \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
        if ($moduleStatus == 1 && $enablePageStatus == 1) {
            $html = $this->getMenu($html, $menuTitle, $storeUrl, $enableClosePageStatus);
        }*/
        $storeUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_LINK);


        $html = $this->getMenu($html, "Test", $storeUrl, 0);
        return $html;
    }

    /**
     * @param $html
     * @param $menuTitle
     * @param $storeUrl
     * @param $enableClosePageStatus
     * @return string
     */
    protected function getMenu($html, $menuTitle, $storeUrl, $enableClosePageStatus)
    {  

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $state = $objectManager->get('\Magento\Framework\App\State');


        $menuitemCollection  = $objectManager->get('\Sm\MegaMenu\Block\MegaMenu\View')->getItems();


        //$state->setAreaCode(‘frontend’);
        /// Get Website ID
        $websiteId = $storeManager->getWebsite()->getWebsiteId();//exit;
        if($websiteId==4){ //http://distribution.aquanexus.com/

            if( count($menuitemCollection) > 0 ){
                foreach ($menuitemCollection as $key => $value) {
                    $html .= '<li class="level1 nav-4 first ui-menu-item">';
                    $html .= '<a href="'.$storeUrl.$value['description'].'" class="level-top ui-corner-all" aria-haspopup="true" tabindex="-1" role="menuitem"><span class="ui-menu-icon ui-icon ui-icon-carat-1-e"></span><span>'.$value["title"].'</span></a>';
                    $html .= '</li>';
                }
            }

            /*$html .= '<li class="level1 nav-4 first ui-menu-item">';
            $html .= '<a href="' . $storeUrl . '" class="level-top ui-corner-all" aria-haspopup="true" tabindex="-1" role="menuitem"><span class="ui-menu-icon ui-icon ui-icon-carat-1-e"></span><span>Uline Products</span></a>';
            $html .= '</li>';

            $html .= '<li class="level1 nav-4 first ui-menu-item">';
            $html .= '<a href="' . $storeUrl . '" class="level-top ui-corner-all" aria-haspopup="true" tabindex="-1" role="menuitem"><span class="ui-menu-icon ui-icon ui-icon-carat-1-e"></span><span>Quick Order</span></a>';
            $html .= '</li>';

            $html .= '<li class="level1 nav-4 first ui-menu-item">';
            $html .= '<a href="' . $storeUrl . '" class="level-top ui-corner-all" aria-haspopup="true" tabindex="-1" role="menuitem"><span class="ui-menu-icon ui-icon ui-icon-carat-1-e"></span><span>Catalog Request</span></a>';
            $html .= '</li>';

            $html .= '<li class="level1 nav-4 first ui-menu-item">';
            $html .= '<a href="' . $storeUrl . '" class="level-top ui-corner-all" aria-haspopup="true" tabindex="-1" role="menuitem"><span class="ui-menu-icon ui-icon ui-icon-carat-1-e"></span><span>Special Offers</span></a>';
            $html .= '</li>';

            $html .= '<li class="level1 nav-4 first ui-menu-item">';
            $html .= '<a href="' . $storeUrl . '" class="level-top ui-corner-all" aria-haspopup="true" tabindex="-1" role="menuitem"><span class="ui-menu-icon ui-icon ui-icon-carat-1-e"></span><span>About Us</span></a>';
            $html .= '</li>';

            $html .= '<li class="level1 nav-4 first ui-menu-item">';
            $html .= '<a href="' . $storeUrl . '" class="level-top ui-corner-all" aria-haspopup="true" tabindex="-1" role="menuitem"><span class="ui-menu-icon ui-icon ui-icon-carat-1-e"></span><span>Careers</span></a>';
            $html .= '</li>';

            $html .= '<li class="level1 nav-4 first ui-menu-item">';
            $html .= '<a href="'.$storeUrl.'blog.html" class="level-top ui-corner-all" aria-haspopup="true" tabindex="-1" role="menuitem"><span class="ui-menu-icon ui-icon ui-icon-carat-1-e"></span><span>Blog</span></a>';
            $html .= '</li>';
            $html .= '</li>';*/
            //  return $html;
        }else{
            $html .= '<li class="level1 nav-4 first ui-menu-item">';
            $html .= '<a href="'.$storeUrl.'blog.html" class="level-top ui-corner-all" aria-haspopup="true" tabindex="-1" role="menuitem"><span class="ui-menu-icon ui-icon ui-icon-carat-1-e"></span><span>Blog</span></a>';
            $html .= '</li>';
        }
        return $html;

    }
}
