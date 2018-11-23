<?php
/**
 * Sunarc_AdvPermission extension
 * NOTICE OF LICENSE
 *
 * This source file is subject to the SunArc Technologies License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://sunarctechnologies.com/end-user-agreement/
 *
 * @category  Sunarc
 * @package   Sunarc_AdvPermission
 * @copyright Copyright (c) 2017
 * @license
 */
namespace Sunarc\AdvPermission\Controller\Adminhtml\Splitattr;

class MassDelete extends \Sunarc\AdvPermission\Controller\Adminhtml\Splitattr\MassAction
{
    /**
     * @param \Sunarc\AdvPermission\Api\Data\SplitattrInterface $splitattr
     * @return $this
     */
    protected function massAction(\Sunarc\AdvPermission\Api\Data\SplitattrInterface $splitattr)
    {
        $this->splitattrRepository->delete($splitattr);
        return $this;
    }
}
