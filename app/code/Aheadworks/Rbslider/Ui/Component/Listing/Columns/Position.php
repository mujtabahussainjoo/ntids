<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rbslider\Ui\Component\Listing\Columns;

use Aheadworks\Rbslider\Model\Source\PageType;

/**
 * Class Position
 * @package Aheadworks\Rbslider\Ui\Component\Listing\Columns
 */
class Position extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * {@inheritdoc}
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $banner) {
                if ($banner['page_type'] == PageType::CUSTOM_WIDGET) {
                    $banner['position'] = '';
                }
            }
        }
        return $dataSource;
    }
}
