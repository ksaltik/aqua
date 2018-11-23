<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_MpRmaSystem
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\MpRmaSystem\Api\Data;

/**
 * MpRmaSystem Conversation interface.
 *
 * @api
 */
interface ConversationInterface
{
    /**
     * Constants for keys of data array.
     */
    const ENTITY_ID = 'id';
    /**#@-*/

    /**
     * Get ID.
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set ID.
     *
     * @param int $id
     *
     * @return \Webkul\MpRmaSystem\Api\Data\ConversationInterface
     */
    public function setId($id);
}
