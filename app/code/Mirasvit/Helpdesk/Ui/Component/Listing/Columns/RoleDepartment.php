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


namespace Mirasvit\Helpdesk\Ui\Component\Listing\Columns;

use Mirasvit\Helpdesk\Model\PermissionFactory;
use Mirasvit\Helpdesk\Model\DepartmentFactory;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

class RoleDepartment extends \Magento\Ui\Component\Listing\Columns\Column
{
    public function __construct(
        PermissionFactory $permissionFactory,
        DepartmentFactory $departmentFactory,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);

        $this->permissionFactory = $permissionFactory;
        $this->departmentFactory = $departmentFactory;
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        $config = $this->getConfiguration();
        if (!isset($config['columnName'])) {
            return $dataSource;
        }
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if ($this->getData('name') == $config['columnName']) {
                    $item[$this->getData('name')] = $this->prepareItem($item[$config['idColumnName']]);
                }
            }
        }

        return $dataSource;
    }

    /**
     * @param int $id
     * @return string
     */
    protected function prepareItem($id)
    {
        $permission = $this->permissionFactory->create();
        $department = $this->departmentFactory->create();
        $this->permissionFactory->create()->getResource()->load($permission, $id);
        $permission->loadDepartmentIds();

        $departments = [];
        foreach ($permission->getDepartmentIds() as $id) {
            if ($id) {
                $this->departmentFactory->create()->getResource()->load($department, $id);
                $departments[] = $department->getName();
            } else {
                $departments[] = __('All Departments');
            }
        }

        return implode(', ', $departments);
    }
}
