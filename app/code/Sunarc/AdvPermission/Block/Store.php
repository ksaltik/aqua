<?php
namespace Sunarc\AdvPermission\Block;

class Store extends \Magento\Framework\View\Element\Template {

protected $_objectManager;


public function __construct(
\Magento\Framework\View\Element\Template\Context $context,
array $data = []
) {
parent::__construct($context, $data);

}


/**
* Retrieve stores structure
*
* @param bool $isAll
*/
public function getStores($isAll = false)
{
$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
$model = $objectManager->create('\Magento\Store\Model\System\Store');

$out = $model->getStoresStructure($isAll);
$result = array();
foreach($out as $data){
$result[]=$data;
}

$_mainWebsites = array();
$i=0;
$j=1;
foreach ($result as $k => $v) {
$_mainWebsites[$i]['main_website']= $result[$k]['label'] ;
$_mainWebsites[$i]['store'] = $result[$k]['children'][$j]['label'] ;
foreach ($result[$k]['children'][$j]['children'] as $p => $q){
$_mainWebsites[$i]['store_views'][$p] = $result[$k]['children'][$j]['children'][$p]['label'] ;
}
$i++;
}

return $_mainWebsites;
}

}