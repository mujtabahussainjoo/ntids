<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */


namespace Amasty\Rolepermissions\Plugin\Product;

class Collection
{
    /**
     * @var \Amasty\Rolepermissions\Helper\Data
     */
    private $helper;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    private $restrictedObjects = [];

    /**
     * @var \Magento\Framework\App\State
     */
    private $appState;

    public function __construct(
        \Amasty\Rolepermissions\Helper\Data $helper,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\App\State $appState
    ) {
        $this->helper = $helper;
        $this->request = $request;
        $this->appState = $appState;
    }

    public function beforeLoad(
        \Magento\Catalog\Model\ResourceModel\Product\Collection $subject
    ) {
        if ($this->request->getModuleName() == 'api') {
            return;
        }

        if ($this->appState->getAreaCode() != \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE) {
            return;
        }

        $objectId = spl_object_hash($subject);

        if (isset($this->restrictedObjects[$objectId])) {
            return;
        }

        $rule = $this->helper->currentRule();
        if (is_object($rule)) {
            $rule->restrictProductCollection($subject);
            $this->restrictedObjects[$objectId] = true;
        }
    }
}
