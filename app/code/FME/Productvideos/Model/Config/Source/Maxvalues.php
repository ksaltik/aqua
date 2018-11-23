<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace FME\Productvideos\Model\Config\Source;

class Maxvalues implements \Magento\Framework\Option\ArrayInterface
{


    public function toOptionArray()
    {
        $groups_array = [];
      
        
            $groups_array[] = [
                               'value' => ini_get("upload_max_filesize"),
                               'label' => 'upload_max_filesize',
                              ];

            $groups_array[] = [
                               'value' => ini_get("post_max_size"),
                               'label' => 'post_max_size',
                              ];
        

            return $groups_array;
    }//end toOptionArray()
}//end class
