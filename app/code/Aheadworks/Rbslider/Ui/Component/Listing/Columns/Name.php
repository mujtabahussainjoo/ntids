<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rbslider\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\Filter\FilterManager;

/**
 * Class Name
 * @package Aheadworks\Rbslider\Ui\Component\Listing\Columns
 */
class Name extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @var FilterManager
     */
    private $filterManager;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param FilterManager $filterManager
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        FilterManager $filterManager,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->filterManager = $filterManager;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareDataSource(array $dataSource)
    {
        foreach ($dataSource['data']['items'] as &$item) {
            if ($this->getName() == 'slide_name') {
                $item['slide_name_url'] = $this->getLink($item['slide_id'], 'slide');
            } elseif ($this->getName() == 'banner_name') {
                $item['banner_name_url'] = $this->getLink($item['banner_id'], 'banner');
            } else {
                $itemConfig = $this->getConfig();
                $item['name_url'] = $this->getLink($item['id'], $itemConfig['type']);
            }
        }

        return $dataSource;
    }

    /**
     * Retrieve link for entity
     *
     * @param int $entityId
     * @param string $type
     * @return string
     */
    private function getLink($entityId, $type)
    {
        return $this->context->getUrl('aw_rbslider_admin/'.$type.'/edit', ['id' => $entityId]);
    }
}
