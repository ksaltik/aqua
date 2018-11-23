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
namespace Sunarc\AdvPermission\Api;

/**
 * @api
 */
interface SplitattrRepositoryInterface
{
    /**
     * Save Splitattr.
     *
     * @param \Sunarc\AdvPermission\Api\Data\SplitattrInterface $splitattr
     * @return \Sunarc\AdvPermission\Api\Data\SplitattrInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Sunarc\AdvPermission\Api\Data\SplitattrInterface $splitattr);

    /**
     * Retrieve Splitattr
     *
     * @param int $splitattrId
     * @return \Sunarc\AdvPermission\Api\Data\SplitattrInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($splitattrId);

    /**
     * Retrieve Splitattrs matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Sunarc\AdvPermission\Api\Data\SplitattrSearchResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete Splitattr.
     *
     * @param \Sunarc\AdvPermission\Api\Data\SplitattrInterface $splitattr
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\Sunarc\AdvPermission\Api\Data\SplitattrInterface $splitattr);

    /**
     * Delete Splitattr by ID.
     *
     * @param int $splitattrId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($splitattrId);
}
