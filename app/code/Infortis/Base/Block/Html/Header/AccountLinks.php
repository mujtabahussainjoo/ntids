<?php
/**
 * Account links
 */

namespace Infortis\Base\Block\Html\Header;

use Infortis\Base\Helper\Data as HelperData;
use Magento\Framework\View\Element\Template\Context;

class AccountLinks extends \Magento\Framework\View\Element\Template
{
    /**
     * Theme helper
     *
     * @var HelperData
     */
    protected $theme;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param HelperData $helperData
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        HelperData $helperData,
        array $data = []
    ) {
        $this->theme = $helperData;
        parent::__construct($context, $data);
    }

   /**
     * Get helper
     *
     * @return HelperData
     */
    public function getHelperTheme()
    {
        return $this->theme;
    }

}
