<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */


namespace Amasty\Rolepermissions\Observer\Admin\Model;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Model\AbstractModel;

class SaveBeforeObserver implements ObserverInterface
{
    /**
     * @var \Amasty\Rolepermissions\Helper\Data
     */
    private $helper;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    public function __construct(
        \Amasty\Rolepermissions\Helper\Data $helper,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->helper = $helper;
        $this->request = $request;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->request->getModuleName() == 'api') {
            return;
        }

        /** @var AbstractModel $object */
        $object = $observer->getObject();

        $rule = $this->helper->currentRule();

        if ($rule && $rule->getScopeStoreviews()) {
            if ($object->getId()) {
                $this->helper->restrictObjectByStores($object->getOrigData());
            }

            $this->helper->alterObjectStores($object);
        }
    }
}
