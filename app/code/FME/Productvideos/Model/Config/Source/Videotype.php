<?php
/**
 * Copyright © 2016 FME. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace FME\Productvideos\Model\Config\Source;

class Videotype implements \Magento\Framework\Option\ArrayInterface
{


    public function toOptionArray()
    {
        $type[0] = [
                           'value' => 'file',
                           'label' => __('Media File'),
                          ];
        $type[1] = [
                           'value' => 'url',
                           'label' => __('URL'),
                          ];
        
        return $type;
    }//end toOptionArray()
}//end class
