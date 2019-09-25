<?php
/**
 * Connector for Base module
 */

namespace Infortis\Infortis\Helper\Connector\Infortis;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class Base extends AbstractHelper
{
    /**
     * Module names
     */
    const MODULE_NAME = 'Infortis_Base';

    /**
     * Header helper class
     */
    const CLASS_HELPER_TEMPLATE_THEME_HTML_HEADER = 'Infortis\Base\Helper\Template\Theme\Html\Header';

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
     * Get array of flags indicating if child blocks of the header (e.g. cart) are displayed inside main menu
     * If module not enabled, return NULL.
     *
     * @return array|NULL
     */
    public function getIsDisplayedInMenu()
    {
        if ($this->isModEnabled)
        {
            $h = \Magento\Framework\App\ObjectManager::getInstance()->get(self::CLASS_HELPER_TEMPLATE_THEME_HTML_HEADER);
            return $h->getIsDisplayedInMenu();
        }
        return NULL;
    }
}
