<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rbslider\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Group
 * @package Aheadworks\Rbslider\Ui\Component\Listing\Columns
 */
class Group extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @var GroupRepositoryInterface
     */
    private $customerGroupRepository;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param GroupRepositoryInterface $customerGroupRepository
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        GroupRepositoryInterface $customerGroupRepository,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->customerGroupRepository = $customerGroupRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareDataSource(array $dataSource)
    {
        foreach ($dataSource['data']['items'] as &$item) {
            $item['customer_group_ids'] = $this->prepareItem($item['customer_group_ids']);
        }
        return $dataSource;
    }

    /**
     * Prepare customer group item
     *
     * @param array $customerGroup
     * @return string
     */
    private function prepareItem($customerGroup)
    {
        $content = [];
        if (!is_array($customerGroup)) {
            $customerGroup = [$customerGroup];
        }
        foreach ($customerGroup as $groupId) {
            try {
                $group = $this->customerGroupRepository->getById($groupId);
            } catch (NoSuchEntityException $noEntityException) {
                continue;
            }
            $content[] = $group->getCode();
        }
        return implode(', ', $content);
    }
}
