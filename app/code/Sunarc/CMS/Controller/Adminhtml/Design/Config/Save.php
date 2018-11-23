<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Sunarc\CMS\Controller\Adminhtml\Design\Config;

use Magento\Backend\App\Action;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Theme\Model\DesignConfigRepository;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Theme\Model\Data\Design\ConfigFactory;
use Sunarc\CMS\Helper\Data;

/**
 * Save action controller
 */
class Save extends Action
{
    /**
     * @var DesignConfigRepository
     */
    protected $designConfigRepository;

    /**
     * @var ConfigFactory
     */
    protected $configFactory;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @param Context                $context
     * @param DesignConfigRepository $designConfigRepository
     * @param ConfigFactory          $configFactory
     * @param DataPersistorInterface $dataPersistor
     */
    public function __construct(
        Context $context,
        DesignConfigRepository $designConfigRepository,
        ConfigFactory $configFactory,
        DataPersistorInterface $dataPersistor,
        Data $sunarchelper
    ) {
    
        $this->designConfigRepository = $designConfigRepository;
        $this->configFactory = $configFactory;
        $this->dataPersistor = $dataPersistor;
        $this->sunarchelper = $sunarchelper;
        parent::__construct($context);
    }


    /**
     * Check the permission to manage themes
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Config::config_design');
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $scope = $this->getRequest()->getParam('scope');
        $scopeId = (int)$this->getRequest()->getParam('scope_id');
        $data = $this->getRequestData();

        try {

            $designConfigData = $this->configFactory->create($scope, $scopeId, $data);
            $this->designConfigRepository->save($designConfigData);
            $this->messageManager->addSuccess(__('You saved the configuration.'));

            $this->dataPersistor->clear('theme_design_config');

            $returnToEdit = (bool)$this->getRequest()->getParam('back', false);
            $resultRedirect->setPath('theme/design_config/');
            if ($returnToEdit) {
                $resultRedirect->setPath('theme/design_config/edit', ['scope' => $scope, 'scope_id' => $scopeId]);
            }
            /*Website assign code by Pushpendra*/
            $this->assignWebsite($data);


            return $resultRedirect;
        } catch (LocalizedException $e) {
            $messages = explode("\n", $e->getMessage());
            foreach ($messages as $message) {
                $this->messageManager->addError(__('%1', $message));
            }
        } catch (\Exception $e) {
            $this->messageManager->addException(
                $e,
                __('Something went wrong while saving this configuration:') . ' ' . $e->getMessage()
            );
        }

        $this->dataPersistor->set('theme_design_config', $data);

        $resultRedirect->setPath('theme/design_config/edit', ['scope' => $scope, 'scope_id' => $scopeId]);
        return $resultRedirect;
    }

    /**
     * Extract data from request
     *
     * @return array
     */
    protected function getRequestData()
    {
        $data = array_merge(
            $this->getRequest()->getParams(),
            $this->getRequest()->getFiles()->toArray()
        );
        $data = array_filter(
            $data, function ($param) {
                return isset($param['error']) && $param['error'] > 0 ? false : true;
            }
        );

        /**
         * Set null to theme id in case it's empty string,
         * in order to delete value from db config but not set empty string,
         * which may cause an error in Magento/Theme/Model/ResourceModel/Theme/Collection::getThemeByFullPath().
         */
        if (isset($data['theme_theme_id']) && $data['theme_theme_id'] === '') {
            $data['theme_theme_id'] = null;
        }
        return $data;
    }

    /**
     * @param $data
     */
    public function assignWebsite($data)
    {
        if ($data['scope'] == 'websites') {
            $storeID='';
            $stores = $this->sunarchelper->getAllStore();
            foreach($stores as $store){
              // echo "<pre>"; print_r($store->getData());
              //  echo $store->getId()." ".$store->getCode()."\n";echo "<br/>";
                if($store->getWebsiteId()==$data['scope_id']){
                    $storeID=$store->getId();
                }
            }
           // echo $storeID;echo "<br/>";
           // echo $data['scope_id'];
           // exit;
            /*Load theme Data by Theme id form Sunarc CMS*/
            $themePackage = $this->sunarchelper->getThemePackage($data['theme_theme_id']);
            if ($themePackage->getSize() > 0) {   //echo "yes"; echo "<pre>"; print_r($themePackage->getData()); echo $themePackage->getSize();exit;
                /*Get website name by scope_id*/
                $website = $this->sunarchelper->getWebsiteCollection($data['scope_id']);
                $websiteName = $website->getData()[0]['name'];

                $pageData = $this->sunarchelper->getPage($themePackage->getData()[0]['cms_page']);
                $pageData['update_name'] = $pageData['title'] . '-' . $websiteName;
                $pageData['update_identifier'] = $pageData['identifier'] . '_' . str_replace(' ', '_', strtolower($websiteName));

                /*check CMS Page Exist or not*/
                $pageExist = 0;
                $cmsPages = $this->sunarchelper->getAllCMSPages(true);
                foreach ($cmsPages->getData() as $datum) {
                    if ($datum['identifier'] == $pageData['update_identifier']) {
                        $pageExist = 1;
                    }
                }
                if ($pageExist == 0) {
                    if ($themePackage->getData()[0]['block_details'] != '') {/*if block exist*/
                        $allStaticBlock = $this->sunarchelper->getAllStaticBlock(true);
                        $te = [];
                        foreach ($allStaticBlock->getData() as $tempBlock) {
                            $te[] = $tempBlock['identifier'];
                        }

                        $block_list = explode(',', $themePackage->getData()[0]['block_details']);
                        $pageData['block_data']=$pageData['block_data_already']=[];
                        //$i
                        foreach ($block_list as $block_id) {
                            $block = $this->sunarchelper->getBlockById(trim($block_id));
                            $checkBlockExist = 0;
                            $block['update_identifier'] = $block['identifier'] . '_' . str_replace(' ', '_', strtolower($websiteName));
                            if (in_array($block['update_identifier'], $te)) { /*Check duplicate block*/
                                $checkBlockExist = 1;
                                $pageData['block_data_already'][]=['identifier' =>$block['identifier'],'update_identifier' =>$block['update_identifier']];//=$block['update_identifier'];
                               // $pageData['block_data_already'][]['identifier']=$block['identifier'];
                            }
                            if ($checkBlockExist == 0) {
                                $pageData['block_data'][] = $block;
                            }
                        }
                    }
                    $update_block_id = [];

                  //  echo "<pre>"; print_r($pageData['content']);//exit;

                    /*Duplicate Static block*/

                    //if($pageData['block_data']!=0){
                        if (!empty($pageData['block_data']) && count($pageData['block_data']) > 1) {
                            foreach ($pageData['block_data'] as $tdata) {
                                $update_block_id[$tdata['identifier']] = $tdata['update_identifier'];
                                $updateBlock = [
                                    'title' => $tdata['title'] . '-' . $websiteName,
                                    'identifier' => $tdata['update_identifier'],
                                    'content' => $tdata['content'],//$text,
                                    'stores' => [$storeID],
                                    'is_active' => 1,
                                ];
                                // print_r($text);exit;
                                $this->sunarchelper->createBlock($updateBlock);
                            }
                        }


                    /*if block already exist*/
                    if (!empty($pageData['block_data_already']) && count($pageData['block_data_already'])) {
                        foreach ($pageData['block_data_already'] as $key=> $tdata) {
                            $update_block_id[$tdata['identifier']] = $tdata['update_identifier'];
                        }
                    }

                    /*Update Static ID in CMS page*/
                    foreach ($update_block_id as $key => $tdata) {
                       // $pageData['content_html'] = str_replace($key, $tdata, $pageData['content_html']);
                        $pageData['content'] = str_replace($key, $tdata, $pageData['content']);
                    }


                    //createPage
                    $updatePage = [
                        'title' => $pageData['title'] . '-' . $websiteName,
                        'identifier' => $pageData['update_identifier'],
                        'stores' => [$storeID],
                        'is_active' => 1,
                        'content_heading' => $pageData['content_heading'],
                        'content' => $pageData['content'],
                        'page_layout' => $pageData['page_layout']
                    ];
                    $this->sunarchelper->createPage($updatePage);
                    /*Add system setting value and assign cms page to website*/
                }
                $this->sunarchelper->saveAdminConfig($pageData['update_identifier'], $data['scope_id']);
            }
        }
    }
}