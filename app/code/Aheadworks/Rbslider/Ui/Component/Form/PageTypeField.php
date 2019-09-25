<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rbslider\Ui\Component\Form;

use Magento\Ui\Component\Form\Element\Select;
use Aheadworks\Rbslider\Model\Source\PageType;

/**
 * Class PageTypeField
 * @package Aheadworks\Rbslider\Ui\Component\Form
 */
class PageTypeField extends Select
{
    /**
     * @inheritdoc
     */
    public function prepare()
    {
        $config = $this->getData('config');
        $config['rbsliderSwitcher'] = [
            [
                'values' => [PageType::HOME_PAGE],
                'actions' => [
                    [
                        'selector' => '#rule_conditions_fieldset',
                        'action' => 'hide'
                    ],
                    [
                        'selector' => '#rule_category_fieldset',
                        'action' => 'hide'
                    ],
                    [
                        'selector' => 'div[data-index="position"]',
                        'action' => 'show'
                    ],
                ]
            ],
            [
                'values' => [PageType::PRODUCT_PAGE],
                'actions' => [
                    [
                        'selector' => '#rule_conditions_fieldset',
                        'action' => 'show'
                    ],
                    [
                        'selector' => '#rule_category_fieldset',
                        'action' => 'hide'
                    ],
                    [
                        'selector' => 'div[data-index="position"]',
                        'action' => 'show'
                    ],
                ]
            ],
            [
                'values' => [PageType::CATEGORY_PAGE],
                'actions' => [
                    [
                        'selector' => '#rule_conditions_fieldset',
                        'action' => 'hide'
                    ],
                    [
                        'selector' => '#rule_category_fieldset',
                        'action' => 'show'
                    ],
                    [
                        'selector' => 'div[data-index="position"]',
                        'action' => 'show'
                    ],
                ]
            ],
            [
                'values' => [PageType::CUSTOM_WIDGET],
                'actions' => [
                    [
                        'selector' => '#rule_conditions_fieldset',
                        'action' => 'hide'
                    ],
                    [
                        'selector' => '#rule_category_fieldset',
                        'action' => 'hide'
                    ],
                    [
                        'selector' => 'div[data-index="position"]',
                        'action' => 'hide'
                    ]
                ]
            ],
        ];
        $this->setData('config', $config);

        parent::prepare();
    }
}
