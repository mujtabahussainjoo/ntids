<?php
/**
 * Connector for UltraMegamenu module
 */

namespace Infortis\Infortis\Helper\Connector\Infortis;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class UltraMegamenu extends AbstractHelper
{
    /**
     * Module names
     */
    const MODULE_NAME = 'Infortis_UltraMegamenu';

    /**
     * Module helper class
     */
    const CLASS_HELPER_DATA = 'Infortis\UltraMegamenu\Helper\Data';

    /**
     * Module enabled flag
     *
     * @var bool
     */
    protected $isModEnabled;

    /**
     * Initialization
     */
    public function __construct(
        Context $context
    ) {
        parent::__construct($context);

        $this->isModEnabled = $this->_moduleManager->isEnabled(self::MODULE_NAME);
    }

    /**
     * Get mobile menu threshold from the menu module.
     * If module not enabled, return NULL.
     *
     * @return string|NULL
     */
    public function getMobileMenuThreshold()
    {
        if($this->isModEnabled)
        {
            $h = \Magento\Framework\App\ObjectManager::getInstance()->get(self::CLASS_HELPER_DATA);
            return $h->getMobileMenuThreshold();
        }
        return NULL;
    }
}
