<?php
/**
 * Copyright © 2015 Sunarc . All rights reserved.
 */

namespace Sunarc\CMS\Helper;

use \Magento\Framework\App\ResourceConnection;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    protected $connection;
    protected $pageCollectionFactory;
    protected $pageFactory;
    protected $blockFactory;
    protected $blockRepository;
    protected $configWriter;
    protected $storeManager;


    /**
     * Data constructor.
     *
     * @param \Magento\Framework\App\Helper\Context                        $context
     * @param ResourceConnection                                           $resource
     * @param \Magento\Cms\Model\ResourceModel\Page\CollectionFactory      $pageCollectionFactory
     * @param \Magento\Cms\Model\PageFactory                               $pageFactory
     * @param \Magento\Cms\Model\BlockFactory                              $blockFactory
     * @param \Magento\Cms\Model\BlockRepository                           $blockRepository
     * @param \Sunarc\CMS\Model\ResourceModel\Sunarccms\Collection         $collectionFactory
     * @param \Magento\Framework\App\Config\Storage\WriterInterface        $configWriter
     * @param \Magento\Store\Model\ResourceModel\Website\CollectionFactory $websiteCollectionFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        ResourceConnection $resource,
        \Magento\Cms\Model\ResourceModel\Page\CollectionFactory $pageCollectionFactory,
        \Magento\Cms\Model\PageFactory $pageFactory,
        \Magento\Cms\Model\BlockFactory $blockFactory,
        \Magento\Cms\Model\BlockRepository $blockRepository,
        \Sunarc\CMS\Model\ResourceModel\Sunarccms\Collection $collectionFactory,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
        \Magento\Store\Model\ResourceModel\Website\CollectionFactory $websiteCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
    
        $this->resource = $resource;
        $this->pageCollectionFactory = $pageCollectionFactory;
        $this->pageFactory = $pageFactory;
        $this->blockFactory = $blockFactory;
        $this->blockRepository = $blockRepository;
        $this->_collectionFactory = $collectionFactory;
        $this->_websiteCollectionFactory = $websiteCollectionFactory;
        $this->configWriter = $configWriter;
        $this->storeManager=$storeManager;
        parent::__construct($context);
    }

    /**
     * @return array
     */
    public function getAllCMSPages($return = '')
    {
        $pages = $collection = $this->pageCollectionFactory->create();
        if ($return == 1) {
            return $pages;
        }
        $cmsArray = array();
        if (!empty($pages)) {
            foreach ($pages as $value) {
                $check = $value;
                if (strpos(strtolower($check['title']), 'home') !== false) {
                    $cmsArray[$value['page_id']] = $value['title'];
                }
            }
        }
        return $cmsArray;
    }

    /**
     * @param string $return
     * @return array|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     */
    public function getAllStaticBlock($return = '')
    {
        $pages = $this->blockFactory->create()->getCollection(); //return $pages;
        if ($return == 1) {
            return $pages;
        }
        $cmsStaticArray = array();
        if (!empty($pages)) {
            foreach ($pages as $value) {
                $cmsStaticArray[] = ['value' => $value['block_id'], 'label' => __($value['identifier'])];//["value"=>$value['block_id'],"label"=>$value['identifier']];
            }
        }

        return $cmsStaticArray;
    }

    /**
     * @return array
     */
    public function loadThemeList()
    {
        //ini_set('display_errors', 1);

        $connection = $this->resource->getConnection(ResourceConnection::DEFAULT_CONNECTION);
        $theme_table = $connection->getTableName('theme');
        $theme_data = $connection->fetchAll("SELECT * FROM " . $theme_table);
        $themeArray = array();
        if (!empty($theme_data)) {
            foreach ($theme_data as $value) {
                $check=$value;
                if (strpos(strtolower($check['theme_title']), 'mobile') == false) {
                    $themeArray[$value['theme_id']] = $value['theme_title'];
                }
            }
        }
        return $themeArray;
    }

    /**
     * @param $id
     * @return \Sunarc\CMS\Model\ResourceModel\Sunarccms\Collection
     */
    public function getThemePackage($id)
    {
        $collection = $this->_collectionFactory->load();
        $collection->addFieldToFilter('theme_id', $id);
        $collection->setPageSize(1); // only get 10 products
        $collection->setCurPage(1);  // firs

        return $collection;

    }

    /**
     * @param $id
     * @return \Magento\Store\Model\ResourceModel\Website\Collection
     */
    public function getWebsiteCollection($id)
    {
        $collection = $this->_websiteCollectionFactory->create();
        $collection->addFieldToFilter('website_id', $id);
        return $collection;
    }

    /**
     * @param $id
     * @return array
     */
    public function getPage($id)
    {
        $pages = $collection = $this->pageCollectionFactory->create();
        $pages->addFieldToFilter("page_id", $id);
        $returnData = [];
        $cmsArray = array();
        if (!empty($pages)) {
            foreach ($pages as $value) {
                //echo "<pre>";                print_r($value->getData());//exit;
                $returnData['title'] = $value->getData()['title'];
                $returnData['page_layout'] = $value->getData()['page_layout'];
                $returnData['meta_keywords'] = htmlspecialchars($value->getData()['meta_keywords']);
                $returnData['meta_description'] = htmlspecialchars($value->getData()['meta_description']);
                $returnData['identifier'] = $value->getData()['identifier'];
                $returnData['content_heading'] = $value->getData()['content_heading'];
                $returnData['content_html'] = htmlspecialchars($value->getData()['content']);
                $returnData['content'] = $value->getData()['content'];
                $returnData['layout_update_xml'] = htmlspecialchars($value->getData()['layout_update_xml']);
                /*Get Used block from string*/
                //{{block class="Magento\\Cms\\Block\\Block" block_id="id-block-1"}}
                //block class="Magento\\Cms\\Block\\Block" block_id="id-block-1"}}
                //block_id="id-block-2"}}
                //id-16-block-2"}}
                //id-16-block-2
                $newdata = explode("{{", $value['content']);
                foreach ($newdata as $new) {
                    if (trim($new) != '') {
                        if (strpos($new, 'block_id') !== false) {
                            $str = substr($new, strpos($new, 'block_id="'));
                            $str = str_replace('block_id="', "", $str);
                            $str = str_replace('"}}', "", $str);
                            //     $str= preg_replace("/<div>(.*?)<\/div>/", "$1", $str);
                            $returnData['use_block'][] = $str;
                            //echo strip_tags("Hello <b>world!</b>");

                        }
                    }
                }
            }
        }
        return $returnData;
    }

    /**
     * @param $id
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getBlockById($id)
    {

        $value = $this->blockRepository->getById($id);
        $returnData['block_id'] = $value->getData()['block_id'];
        $returnData['title'] = $value->getData()['title'];
        $returnData['identifier'] = $value->getData()['identifier'];
        $returnData['content_html'] = htmlspecialchars($value->getData()['content']);
        $returnData['content'] = $value->getData()['content'];

        return $returnData;
    }

    /**
     * @param $pageData
     * @throws \Exception
     */
    public function createPage($pageData)
    {

        $this->pageFactory->create()->setData($pageData)->save();
    }

    /**
     * @param $blockData
     * @throws \Exception
     */
    public function createBlock($blockData)
    {
        $this->blockFactory->create()->setData($blockData)->save();
    }

    /**
     * @param $value
     * @param $scopeId
     */
    public function saveAdminConfig($value, $scopeId)
    {
        $this->configWriter->save('web/default/cms_home_page', $value, "websites", $scopeId);
    }
    public function getAllStore(){
        return $this->storeManager->getStores(true, false);
    }

}