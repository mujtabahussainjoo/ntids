<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-report-api
 * @version   1.0.16
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\ReportApi\Api\Config;

interface RelationInterface
{
    const TYPE_ONE  = '1';
    const TYPE_MANY = 'n';

    const TYPE_ONE_TO_ONE   = '11'; //ONE row in A has ONE row in B, ONE row in A has ONE row in B
    const TYPE_ONE_TO_MANY  = '1n'; //ONE row in A has ONE row in B, ONE row in B has N rows in A
    const TYPE_MANY_TO_ONE  = 'n1'; //ONE row in A has N rows in B, ONE row in B has ONE row in A
    const TYPE_MANY_TO_MANY = 'nn';

    /**
     * @return TableInterface
     */
    public function getLeftTable();

    /**
     * @return FieldInterface
     */
    public function getLeftField();

    /**
     * @return TableInterface
     */
    public function getRightTable();

    /**
     * @return FieldInterface
     */
    public function getRightField();

    /**
     * @return string
     */
    public function getCondition();

    /**
     * @param TableInterface $table
     * @return string
     */
    public function getType(TableInterface $table);

    /**
     * @param TableInterface $table
     * @return TableInterface|false
     */
    public function getOppositeTable(TableInterface $table);

    /**
     * @param FieldInterface $field
     * @return FieldInterface
     */
    public function getOppositeField(FieldInterface $field);
}
