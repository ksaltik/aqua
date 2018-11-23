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

namespace FME\Productvideos\Model\ResourceModel;

class Productvideos extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**---Functions---*/
    /**
     * Store model
     *
     * @var null|\Magento\Store\Model\Store
     */
    protected $_store = null;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $dateTime;

    protected $_objectManager;

    /**
     * Construct
     *
     * @param \Magento\Framework\Model\Resource\Db\Context $context
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param string|null $resourcePrefix
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        $resourcePrefix = null
    ) 
    {
        parent::__construct($context, $resourcePrefix);
        $this->_date = $date;
        $this->_storeManager = $storeManager;
        $this->dateTime = $dateTime;
        $this->_objectManager = $objectManager;
    }
    public function _construct()
    {
        $this->_init('productvideos', 'video_id');
    }
    
    protected function _afterLoad(
        \Magento\Framework\Model\AbstractModel $object
    )
    {
        
        $select = $this->getConnection()->select()
          ->from($this->getTable('productvideos_store'))
          ->where('productvideos_id = ?', $object->getId());

        if ($data = $this->getConnection()->fetchAll($select)) {
            $storesArray = [];
            foreach ($data as $row) {
                $storesArray[] = $row['store_id'];
            }

            $object->setData('store_id', $storesArray);
        }

        

        $select = $this->getConnection()->select()
          ->from($this->getTable('productvideos_products'))
          ->where('productvideos_id = ?', $object->getId());

        if ($data = $this->getConnection()->fetchAll($select)) {
            $productsArray = [];
            foreach ($data as $row) {
                $productsArray[] = $row['product_id'];
            }

            $object->setData('product_id', $productsArray);
        }

        
        return parent::_afterLoad($object);
    }
    
    /**
     * _afterSave
     * @param  \Magento\Framework\Model\AbstractModel $object
     * @return extended
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $condition = $this->getConnection()->quoteInto('productvideos_id = ?', $object->getId());
        if (isset($object['product_id'])) {
            $links = $object['product_id'];
            $productIds = $links;
            $this->getConnection()->delete(
                $this->getTable('productvideos_products'),
                $condition
            );   
            $this->getConnection()->delete(
                $this->getTable('productvideos_products'),
                $condition
            );         
            foreach ($productIds as $_productId) {
                $productsArray = [];
                $productsArray['productvideos_id'] = $object->getId();
                $productsArray['product_id'] = $_productId;
                $this->getConnection()
                ->insert(
                    $this->getTable('productvideos_products'),
                    $productsArray
                );
            }
        }

        if (isset($object['store_id'])) {
            $this->getConnection()
            ->delete(
                $this->getTable('productvideos_store'),
                $condition
            );
            foreach ((array)$object['store_id'] as $store) {
                $storeArray = [];
                $storeArray['productvideos_id'] = $object->getId();
                $storeArray['store_id'] = $store;
                $this->getConnection()
                ->insert(
                    $this->getTable('productvideos_store'),
                    $storeArray
                );
            }
        }
        return parent::_afterSave($object);
    }
}
