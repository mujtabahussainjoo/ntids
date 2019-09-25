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


namespace Mirasvit\Helpdesk\Ui\Component\Listing;

use Mirasvit\Helpdesk\Model\ResourceModel\Field\CollectionFactory;
use Mirasvit\Helpdesk\Model\Field;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

class Columns extends \Magento\Ui\Component\Listing\Columns
{
    /**
     * @var array
     */
    protected $filterMap = [
        'default'            => 'text',
        Field::TYPE_TEXT     => 'text',
        Field::TYPE_TEXTAREA => 'text',
        Field::TYPE_SELECT   => 'select',
        Field::TYPE_CHECKBOX => 'select',
        Field::TYPE_DATE     => 'dateRange',
    ];

    public function __construct(
        CollectionFactory $fieldCollectionFactory,
        ContextInterface $context,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $components, $data);

        $this->fieldCollectionFactory = $fieldCollectionFactory;
    }
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD)
     */
    public function prepare()
    {
        parent::prepare();

        $sortOrder = 160;
        $collection = $this->fieldCollectionFactory->create()
            ->addFieldToFilter('is_active', true);
        /** @var \Mirasvit\Helpdesk\Model\Field $field */
        foreach ($collection as $field) {
            switch ($field->getType()) {
                case Field::TYPE_CHECKBOX:
                    $this->addCheckboxColumn($field, $sortOrder);
                    break;
                case Field::TYPE_DATE:
                    $this->addDateColumn($field, $sortOrder);
                    break;
                case Field::TYPE_SELECT:
                    $this->addSelectColumn($field, $sortOrder);
                    break;
                case Field::TYPE_TEXT:
                case Field::TYPE_TEXTAREA:
                    $this->addTextColumn($field, $sortOrder);
                    break;
            }
            $sortOrder += 10;
        }
    }

    /**
     * @param string $type
     * @return string
     */
    protected function getFilterType($type)
    {
        return isset($this->filterMap[$type]) ? $this->filterMap[$type] : $this->filterMap['default'];
    }

    /**
     * @param \Mirasvit\Helpdesk\Model\Field $field
     * @param int                       $sortOrder
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    protected function addTextColumn($field, $sortOrder)
    {
        $arguments = [
            'data'    => [
                'config' => [
                    'label'               => __($field->getName()),
                    'visible'             => false,
                    'filter'              => $this->getFilterType($field->getType()),
                    'sortOrder'           => $sortOrder,
                ],
            ],
            'context' => $this->context,
        ];
        $column = $this->context->getUiComponentFactory()->create($field->getCode(), 'column', $arguments);

        $column->prepare();

        $this->addComponent($field->getCode(), $column);
    }

    /**
     * @param \Mirasvit\Helpdesk\Model\Field $field
     * @param int                       $sortOrder
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    protected function addCheckboxColumn($field, $sortOrder)
    {
        $arguments = [
            'data'    => [
                'options' => [
                    'active' => [
                        'value' => 1,
                        'label' => __('Yes'),
                    ],
                    'inactive' => [
                        'value' => 0,
                        'label' => __('No'),
                    ],
                ],
                'config'  => [
                    'label'               => __($field->getName()),
                    'visible'             => false,
                    'component'           => 'Magento_Ui/js/grid/columns/select',
                    'editor'              => 'select',
                    'dataType'            => 'select',
                    'filter'              => $this->getFilterType($field->getType()),
                    'sortOrder'           => $sortOrder,
                ],
            ],
            'context' => $this->context,
        ];
        $column = $this->context->getUiComponentFactory()->create($field->getCode(), 'column', $arguments);

        $column->prepare();

        $this->addComponent($field->getCode(), $column);
    }

    /**
     * @param \Mirasvit\Helpdesk\Model\Field $field
     * @param int                       $sortOrder
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    protected function addSelectColumn($field, $sortOrder)
    {
        $options = [];
        foreach ($field->getValues() as $value => $label) {
            $options[] = [
                'value' => $value,
                'label' => __($label),
            ];
        }
        $arguments = [
            'data'    => [
                'options' => $options,
                'config'  => [
                    'label'               => __($field->getName()),
                    'visible'             => false,
                    'component'           => 'Magento_Ui/js/grid/columns/select',
                    'editor'              => 'select',
                    'dataType'            => 'select',
                    'filter'              => $this->getFilterType($field->getType()),
                    'sortOrder'           => $sortOrder,
                ],
            ],
            'context' => $this->context,
        ];
        $column = $this->context->getUiComponentFactory()->create($field->getCode(), 'column', $arguments);

        $column->prepare();

        $this->addComponent($field->getCode(), $column);
    }

    /**
     * @param \Mirasvit\Helpdesk\Model\Field $field
     * @param int                       $sortOrder
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    protected function addDateColumn($field, $sortOrder)
    {
        $arguments = [
            'config'  => [
                'class'               => 'Mirasvit\Helpdesk\Ui\Component\Listing\Columns\FriendlyDateAt',
            ],
            'data'    => [
                'config'  => [
                    'label'               => __($field->getName()),
                    'visible'             => false,
                    'dataType'            => 'text',
                    'filter'              => $this->getFilterType($field->getType()),
                    'sortOrder'           => $sortOrder,
                ],
            ],
            'context' => $this->context,
            'components' => [],
        ];
        $column = $this->context->getUiComponentFactory()->create($field->getCode(), 'column', $arguments);

        $column->prepare();

        $this->addComponent($field->getCode(), $column);
    }
}