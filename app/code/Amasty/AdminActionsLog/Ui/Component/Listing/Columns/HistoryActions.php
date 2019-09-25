<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_AdminActionsLog
 */


namespace Amasty\AdminActionsLog\Ui\Component\Listing\Columns;

use Magento\Ui\Component\Listing\Columns\Column;

class HistoryActions extends Column
{
    /**
     * @var array
     */
    private $previewTypes = [
        'Edit',
        'New',
        'Restore'
    ];

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        $dataSource = parent::prepareDataSource($dataSource);

        if (empty($dataSource['data']['items'])) {
            return $dataSource;
        }

        foreach ($dataSource['data']['items'] as &$item) {
            $item[$this->getData('name')]['details'] = [
                'href' => $this->context->getUrl(
                    'amaudit/actionslog/edit',
                    ['id' => $item['id']]
                ),
                'label' => __('View Details'),
                'hidden' => false,
            ];
            if (in_array($item['type'], $this->previewTypes)) {
                $item[$this->getData('name')]['preview'] = [
                    'callback' => [
                        'target' => 'open',
                        'provider' => 'history_listing.history_listing_data_source',
                        'params' => [
                            $this->context->getUrl('amaudit/actionslog/preview'),
                            $item['id']
                        ],
                    ],
                    'label' => __('Preview Details'),
                    'hidden' => false,
                ];
            }
        }

        return $dataSource;
    }
}
