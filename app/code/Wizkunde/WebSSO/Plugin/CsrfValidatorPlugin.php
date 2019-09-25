<?php

namespace Wizkunde\WebSSO\Plugin;

class CsrfValidatorPlugin
{
    public function aroundValidate(
        \Magento\Framework\App\Request\CsrfValidator $validator,
        \Closure $proceed,
        \Magento\Framework\App\Request\Http $httpRequest,
        \Magento\Framework\App\ActionInterface $actionInferface
    ) {
        // Dont run the Csrf Validator on SSO POST requests
        if($httpRequest->getRouteName() == 'sso')
        {
            return true;
        }

        return $proceed($httpRequest, $actionInferface);
    }
}
