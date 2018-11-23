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
namespace Sunarc\AdvPermission\Model;

/**
 * @method \Sunarc\AdvPermission\Model\ResourceModel\Splitattr _getResource()
 * @method \Sunarc\AdvPermission\Model\ResourceModel\Splitattr getResource()
 */
class Permissions extends \Magento\Framework\View\Element\AbstractBlock
{
    protected $authSession;
    public function __construct(
        \Magento\Backend\Model\Auth\Session $authSession
    ) {
        $this->authSession = $authSession;
    }
    /**
     * Get all of the admin user split attribute restrictions if they exist
     *
     * @param bool $user
     * @return array|bool
     */
    public function getSplitattributeRestrictions($user = false)
    {
        if (!$user) {
            $user = $this->authSession->getUser();
        }

        if (!$user) {
            return false;
        }

        $roles = $user->getRoles();
        $totalRestrictions = [];
        $role = $this->authSession->getUser()->getRole()->getData();
        if ($role && $role['restrict_by_splitattribute']) {
            if ($splitAttributeRestrictions = $user->getRestrictBySplitattribute()) {
                foreach (explode(',', $splitAttributeRestrictions) as $attributeRestriction) {
                    $totalRestrictions[] = $attributeRestriction;
                }
            }
        }
        $totalRestrictions = array_unique($totalRestrictions);
        if (empty($totalRestrictions)) {
            return false;
        }
        return $totalRestrictions;
    }
}
