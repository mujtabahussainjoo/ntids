<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Block\Adminhtml\Form\Field;


class RuleMethodsTitle extends MethodsTitle
{
    /**
     * Prepare to render
     *
     * @return void
     */
    protected function _prepareToRender()
    {
        $this->addColumn(
            'methods_id',
            ['label' => __('Shipping Method'), 'renderer' => $this->_getMethodsRenderer()]
        );
        $this->addColumn('title_0', ['label' => __('Default Title')]);
        // All stores without admin
        $stores = $this->_storeManager->getStores(false);
        foreach ($stores as $store) {
            $this->addColumn('title_' . $store->getId(), ['label' => $store->getName()]);
        }
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Title');
    }
}