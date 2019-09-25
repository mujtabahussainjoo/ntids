<?php
/**
* Magedelight
* Copyright (C) 2017 Magedelight <info@magedelight.com>
*
* @category Magedelight
* @package Magedelight_MembershipSubscription
* @copyright Copyright (c) 2017 Mage Delight (http://www.magedelight.com/)
* @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
* @author Magedelight <info@magedelight.com>
 */

namespace Magedelight\MembershipSubscription\Ui\DataProvider\Product\Modifier;
        
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Form;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;

class MembershipTab extends AbstractModifier
{
    
    const SORT_ORDER = 1;

    protected $locator;

    protected $storeManager;

    /**
     *
     * @param LocatorInterface $locator
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        LocatorInterface $locator,
        StoreManagerInterface $storeManager
    ) {
        $this->locator = $locator;
        $this->storeManager = $storeManager;
    }

    /**
     *
     * @param array $data
     * @return array
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     *
     * @param array $meta
     * @return type
     */
    public function modifyMeta(array $meta)
    {
        $model = $this->locator->getProduct();
          
        if ((!$this->storeManager->isSingleStoreMode()) && ($model->getTypeId() == "Membership")) {
            $meta = array_replace_recursive(
                $meta,
                [
                    'membership_subscription' => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'additionalClasses' => 'admin__collapsible-block-wrapper',
                                    'label' => __('Membership Subscription'),
                                    'collapsible' => true,
                                    'opened' => true,
                                    'componentType' => Form\Fieldset::NAME,
                                    'dataScope' => self::DATA_SCOPE_PRODUCT,
                                    'sortOrder' => self::SORT_ORDER,
                                ],
                            ],
                        ],
                    ],
                    'custom_options' => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'additionalClasses' => 'admin__collapsible-block-wrapper',
                                    'label' => __('Customizable Options'),
                                    'collapsible' => true,
                                    'opened' => true,
                                    'componentType' => Form\Fieldset::NAME,
                                    'dataScope' => self::DATA_SCOPE_PRODUCT,
                                    'sortOrder' => self::SORT_ORDER,
                                    'componentDisabled' => true,
                                ],
                            ],
                        ],
                    ],
                ]
            );
        }

        return $meta;
    }
}
