<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_MultiInventory
 */


namespace Amasty\MultiInventory\Api\Data;

interface CustomerCoordinatesInterface
{
    const ID = 'id';
    const ADDRESS_ID = 'address_id';
    const LAT = 'lat';
    const LNG = 'lng';

    /**
     * @return int
     */
    public function getId();

    /**
     * @return int
     */
    public function getAddressId();

    /**
     * @param int $addressId
     * @return $this
     */
    public function setAddressId($addressId);

    /**
     * @return string
     */
    public function getLat();

    /**
     * @param int $lat
     * @return $this
     */
    public function setLat($lat);

    /**
     * @return string
     */
    public function getLng();

    /**
     * @param int $lat
     * @return $this
     */
    public function setLng($lat);
}
