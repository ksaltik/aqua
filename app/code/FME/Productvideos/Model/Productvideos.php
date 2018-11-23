<?php
/*////////////////////////////////////////////////////////////////////////////////
 \\\\\\\\\\\\\\\\\\\\\\\\\  FME Productvideos Module  \\\\\\\\\\\\\\\\\\\\\\\\\
 /////////////////////////////////////////////////////////////////////////////////
 \\\\\\\\\\\\\\\\\\\\\\\\\ NOTICE OF LICENSE\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
 ///////                                                                   ///////
 \\\\\\\ This source file is subject to the Open Software License (OSL 3.0)\\\\\\\
 ///////   that is bundled with this package in the file LICENSE.txt.      ///////
 \\\\\\\   It is also available through the world-wide-web at this URL:    \\\\\\\
 ///////          http://opensource.org/licenses/osl-3.0.php               ///////
 \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
 ///////                      * @category   FME                            ///////
 \\\\\\\                      * @package    FME_Productvideos              \\\\\\\
 ///////    * @author    FME Extensions <support@fmeextensions.com>   ///////
 \\\\\\\                                                                   \\\\\\\
 /////////////////////////////////////////////////////////////////////////////////
 \\* @copyright  Copyright 2015 � fmeextensions.com All right reserved\\\
 /////////////////////////////////////////////////////////////////////////////////
 */
namespace FME\Productvideos\Model;

class Productvideos extends \Magento\Framework\Model\AbstractModel
{
        
    protected $_objectManager;

    protected $_coreResource;

    protected $_storeManager; 

    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    /**
     * @param \Magento\Framework\Model\Context                               $context            [description]
     * @param \Magento\Framework\Registry                                    $registry           [description]
     * @param \Magento\Framework\ObjectManagerInterface                      $objectManager      [description]
     * @param \Magento\Framework\App\Resource                                $coreResource       [description]
     * @param \FME\Productvideos\Model\Resource\Productvideos            $resource           [description]
     * @param \FME\Productvideos\Model\Resource\Productvideos\Collection $resourceCollection [description]
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\ResourceConnection $coreResource,
        \FME\Productvideos\Model\ResourceModel\Productvideos $resource,
        \FME\Productvideos\Model\ResourceModel\Productvideos\Collection $resourceCollection,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) 
    {
    
     
     
     
     
     
     
     
     
        $this->_storeManager = $storeManager;
    
        $this->_objectManager = $objectManager;
        $this->_coreResource = $coreResource;
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection
        );
    }

    /**
     * _construct
     *
     */
    public function _construct()
    {
        $this->_init('FME\Productvideos\Model\ResourceModel\Productvideos');
    }


    public function getAvailableStatuses()
    {
        return [self::STATUS_ENABLED => __('Enabled'), self::STATUS_DISABLED => __('Disabled')];
    }

    /**
     * getRelatedProducts
     * @param  $productvideosId
     * @return array
     */
    public function getRelatedProducts($productvideosId)
    {
                    
        $productvideosTable = $this->_coreResource
                                    ->getTableName('productvideos_products');
            
        $collection = $this->_objectManager->create('FME\Productvideos\Model\Productvideos')
                        ->getCollection()
                        ->addStoreFilter($this->_storeManager->getStore())
                        ->addFieldToFilter(
                            'main_table.video_id',
                            $productvideosId
                        );
                      
                      
        $collection->getSelect()
            ->joinLeft(
                ['related' => $productvideosTable],
                'main_table.video_id = related.productvideos_id'
            )
            ->order('main_table.video_id');
                    return $collection->getData();
    }


    public function getProductRelatedVideos($productId)
    {
        
        $productvideosTable = $this->_coreResource
                                    ->getTableName('productvideos_products');
            
        $collection = $this->_objectManager->create('FME\Productvideos\Model\Productvideos')
                        ->getCollection()
                        ->addStoreFilter($this->_storeManager->getStore()->getId());
                              
        $collection->getSelect()
            ->joinLeft(
                ['related' => $productvideosTable],
                'main_table.video_id = related.productvideos_id'
            )
            ->where('related.product_id = '.$productId .' and main_table.status = 1')
            //->where('store_id= (0,'.$this->_storeManager->getStore()->getId() .')')
            ->order('main_table.video_id');
    //echo (string)$collection->getSelect();exit;
           
        return $collection;
    }

    public function getProducts(\FME\Productvideos\Model\Productvideos $object)
    {
        $select = $this->_getResource()
        ->getConnection()
        ->select()
        ->from(
            $this->_getResource()
            ->getTable('productvideos_products')
        )->where(
            'productvideos_id = ?',
            $object->getId()
        );
        $data = $this->_getResource()->getConnection()->fetchAll($select);
        if ($data) {
            $productsArr = [];
            foreach ($data as $_i) {
                $productsArr[] = $_i['product_id'];
            }

            
            return $productsArr;
        }
    }

    public function getProductsPosition()
    {
        if (!$this->getId()) {
            return [];
        }

        $array = $this->getData('products_position');
        if ($array === null) {
            $temp = $this->getData('product_id');

            for ($i = 0; $i < sizeof($this->getData('product_id')); $i++) {
                $array[$temp[$i]] = 0;
            }

            $this->setData('products_position', $array);
        }

        return $array;
    }//end getProductsPosition()
}
