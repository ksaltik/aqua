<?php
/**
 * Webkul Software
 *
 * @category  Webkul
 * @package   Webkul_Mpsellervideo
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\Mpsellervideo\Plugin\Helper;

class Data
{
    /**
     * function to run to change the return data of GetControllerMappedPermissions.
     *
     * @param \Webkul\Marketplace\Helper\Data $helperData
     * @param array                           $result
     *
     * @return bool
     */
    public function afterGetControllerMappedPermissions(
        \Webkul\Marketplace\Helper\Data $helperData,
        $result
    ) {
        $result['mpsellervideo/mpvideo/deleteimage'] = 'mpsellervideo/mpvideo/index';
        $result['mpsellervideo/mpvideo/index'] = 'mpsellervideo/mpvideo/index';
        $result['mpsellervideo/mpvideo/savevideoimg'] = 'mpsellervideo/mpvideo/index';
        $result['mpsellervideo/mpvideo/updateimage'] = 'mpsellervideo/mpvideo/index';
        return $result;
    }
}
