<?php
/**
 * Main menu bar
 */

namespace Infortis\UltraMegamenu\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Registry;
use Magento\Cms\Model\BlockFactory;
use Infortis\UltraMegamenu\Helper\Data as HelperData;
use Infortis\Infortis\Helper\Connector\Infortis\Base as HelperConnectorBaseTheme;

class Mainmenu extends Template
{
    /**
     * Module helper
     *
     * @var HelperData
     */
    protected $helper;

    /**
     * Connector helper for base theme module
     *
     * @var HelperConnectorBaseTheme
     */
    protected $connectorBaseTheme;

    /**
     * @var BlockFactory
     */
    protected $blockFactory;

    /**
     * @var Registry
     */
    protected $registry;

    public function __construct(
        Context $context,
        HelperData $helper,
        HelperConnectorBaseTheme $connectorBaseTheme,
        BlockFactory $blockFactory,
        Registry $registry
    ) {
        $this->helper               = $helper;
        $this->connectorBaseTheme   = $connectorBaseTheme;
        $this->blockFactory         = $blockFactory;
        $this->registry             = $registry;

        return parent::__construct($context);
    }

   /**
     * Get helper
     *
     * @return HelperData
     */
    public function getHelperData()
    {
        return $this->helper;
    }

   /**
     * Get helper
     *
     * @return HelperConnectorBaseTheme
     */
    public function getHelperConnectorBaseTheme()
    {
        return $this->connectorBaseTheme;
    }

   /**
     * Get static block title
     *
     * @return string
     */
    public function getStaticBlockTitle($id)
    {
        $theBlock = $this->blockFactory->create()
            ->setStoreId($this->_storeManager->getStore()->getId())
            ->load($id, 'identifier');

        return $theBlock->getTitle();
    }

   /**
     * Get registry
     *
     * @return Registry
     */
    public function getRegistry()
    {
        return $this->registry;
    }

}
