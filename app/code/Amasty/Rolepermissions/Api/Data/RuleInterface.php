<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */


namespace Amasty\Rolepermissions\Api\Data;

interface RuleInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const ID = 'id';
    const ROLE_ID = 'role_id';
    const LIMIT_ORDERS = 'limit_orders';
    const LIMIT_INVOICES = 'limit_invoices';
    const LIMIT_SHIPMENTS = 'limit_shipments';
    const LIMIT_MEMOS = 'limit_memos';
    const PRODUCT_ACCESS_MODE = 'product_access_mode';
    const CATEGORY_ACCESS_MODE = 'category_access_mode';
    const SCOPE_ACCESS_MODE = 'scope_access_mode';
    const ATTRIBUTE_ACCESS_MODE = 'attribute_access_mode';
    const ROLE_ACCESS_MODE = 'role_access_mode';
    /**#@-*/

    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     *
     * @return \Amasty\Rolepermissions\Api\Data\RuleInterface
     */
    public function setId($id);

    /**
     * @return int
     */
    public function getRoleId();

    /**
     * @param int $roleId
     *
     * @return \Amasty\Rolepermissions\Api\Data\RuleInterface
     */
    public function setRoleId($roleId);

    /**
     * @return int
     */
    public function getLimitOrders();

    /**
     * @param int $limitOrders
     *
     * @return \Amasty\Rolepermissions\Api\Data\RuleInterface
     */
    public function setLimitOrders($limitOrders);

    /**
     * @return int
     */
    public function getLimitInvoices();

    /**
     * @param int $limitInvoices
     *
     * @return \Amasty\Rolepermissions\Api\Data\RuleInterface
     */
    public function setLimitInvoices($limitInvoices);

    /**
     * @return int
     */
    public function getLimitShipments();

    /**
     * @param int $limitShipments
     *
     * @return \Amasty\Rolepermissions\Api\Data\RuleInterface
     */
    public function setLimitShipments($limitShipments);

    /**
     * @return int
     */
    public function getLimitMemos();

    /**
     * @param int $limitMemos
     *
     * @return \Amasty\Rolepermissions\Api\Data\RuleInterface
     */
    public function setLimitMemos($limitMemos);

    /**
     * @return int
     */
    public function getProductAccessMode();

    /**
     * @param int $productAccessMode
     *
     * @return \Amasty\Rolepermissions\Api\Data\RuleInterface
     */
    public function setProductAccessMode($productAccessMode);

    /**
     * @return int
     */
    public function getCategoryAccessMode();

    /**
     * @param int $categoryAccessMode
     *
     * @return \Amasty\Rolepermissions\Api\Data\RuleInterface
     */
    public function setCategoryAccessMode($categoryAccessMode);

    /**
     * @return int
     */
    public function getScopeAccessMode();

    /**
     * @param int $scopeAccessMode
     *
     * @return \Amasty\Rolepermissions\Api\Data\RuleInterface
     */
    public function setScopeAccessMode($scopeAccessMode);

    /**
     * @return int
     */
    public function getAttributeAccessMode();

    /**
     * @param int $attributeAccessMode
     *
     * @return \Amasty\Rolepermissions\Api\Data\RuleInterface
     */
    public function setAttributeAccessMode($attributeAccessMode);

    /**
     * @return int
     */
    public function getRoleAccessMode();

    /**
     * @param int $roleAccessMode
     *
     * @return \Amasty\Rolepermissions\Api\Data\RuleInterface
     */
    public function setRoleAccessMode($roleAccessMode);
}
