<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */


namespace Amasty\Rolepermissions\Block\Adminhtml\Role\Tab\Roles;

use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Grid extends Extended
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry = null;

    /**
     * @var \Magento\Authorization\Model\ResourceModel\Role\Grid\CollectionFactory
     */
    private $collectionFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Authorization\Model\ResourceModel\Role\Grid\CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('amrolepremissions_allowed_role_grid');
        $this->setDefaultSort('id');
        $this->setUseAjax(true);
        if ($this->getRule() && $this->getRule()->getId()) {
            $this->setDefaultFilter(['in_roles' => 1]);
        }
    }

    public function getRule()
    {
        return $this->coreRegistry->registry('amrolepermissions_current_rule');
    }

    /**
     * @param Column $column
     *
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in role flag
        if ($column->getId() == 'in_roles') {
            $roleIds = $this->_getSelectedRoles();
            if (empty($roleIds)) {
                $roleIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('role_id', ['in' => $roleIds]);
            } else {
                if ($roleIds) {
                    $this->getCollection()->addFieldToFilter('role_id', ['nin' => $roleIds]);
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        /** @var \Magento\Authorization\Model\ResourceModel\Role\Grid\Collection $collection */
        $collection = $this->collectionFactory->create();

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this
     * @throws \Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'in_roles',
            [
                'type' => 'checkbox',
                'name' => 'in_roles',
                'values' => $this->_getSelectedRoles(),
                'align' => 'center',
                'index' => 'role_id',
                'column_css_class' => 'col-select',
                'header_css_class' => 'col-select',
            ]
        );

        $this->addColumn(
            'id',
            [
                'header' => __('ID'),
                'sortable' => true,
                'index' => 'role_id',
                'column_css_class' => 'col-id',
                'header_css_class' => 'col-id',
            ]
        );

        $this->addColumn(
            'role_name',
            [
                'header' => __('Name'),
                'index' => 'role_name',
                'column_css_class' => 'col-name',
                'header_css_class' => 'col-name',
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * Retrieve grid URL
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->_getData(
            'grid_url'
        ) ? : $this->getUrl(
            'amasty_rolepermissions/role/allowedGrid',
            ['_current' => true]
        );
    }

    protected function _getSelectedRoles()
    {
        $roles = $this->getAllowedRoles();
        if (!is_array($roles)) {
            $roles = $this->getSelectedRuleRoles();
        }
        return $roles;
    }

    public function getSelectedRuleRoles()
    {
        if (!$this->getRule()->getRoles()) {
            return [];
        }

        return $this->getRule()->getRoles();
    }
}
