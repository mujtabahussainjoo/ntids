<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ShippingRules\Block\Adminhtml\Shippingrules\Rate\Edit\Button;

/**
 * Class Back
 */
class Back extends Generic
{
    /**
     * Get back button data
     * Can redirect to the corresponding method form
     *
     * @param int $sortOrder
     * @return array
     */
    public function getButtonData($sortOrder = 10)
    {
        $url = $this->resolveRedirectBackUrl();
        $label = __('Back');
        $onClick = sprintf("location.href = '%s';", $url);
        $result = [
            'label' => $label,
            'on_click' => $onClick,
            'class' => 'back',
            'sort_order' => $sortOrder
        ];

        return $result;
    }

    /**
     * Resolve the redirect back url
     *
     * @return string
     */
    private function resolveRedirectBackUrl()
    {
        if ($this->isBackToMethod() && ($this->getRate()->getMethodId() || $this->request->getParam('method_id'))) {
            // Return to the corresponding method's edit form
            $methodId = $this->getRate()->getMethodId() ?
                $this->getRate()->getMethodId() :
                $this->request->getParam('method_id');
            $url = $this->getUrl('mageworx_shippingrules/shippingrules_method/edit', ['id' => $methodId]);
        } else {
            // Return to the grid
            $url = $this->getUrl('*/*/');
        }

        return $url;
    }
}
