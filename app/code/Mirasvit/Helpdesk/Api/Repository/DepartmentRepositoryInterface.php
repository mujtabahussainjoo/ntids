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
 * @package   mirasvit/module-helpdesk
 * @version   1.1.77
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\Helpdesk\Api\Repository;

use Mirasvit\Helpdesk\Api\Data\DepartmentInterface;

interface DepartmentRepositoryInterface
{
    /**
     * @return \Mirasvit\Helpdesk\Model\ResourceModel\Department\Collection | DepartmentInterface[]
     */
    public function getCollection();

    /**
     * @return DepartmentInterface
     */
    public function create();

    /**
     * @param int|string $id Index ID or Identifier
     * @return DepartmentInterface
     */
    public function get($id);

    /**
     * @param DepartmentInterface $department
     * @return DepartmentInterface
     */
    public function save(DepartmentInterface $department);

    /**
     * @param DepartmentInterface $department
     * @return $this
     */
    public function delete(DepartmentInterface $department);
}