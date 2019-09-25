<?php
/**
 * Page footer block
 */

namespace Infortis\Base\Block\Html;

use Infortis\Base\Helper\Data as HelperData;
use Magento\Framework\View\Element\Template\Context;

class Footer extends \Magento\Framework\View\Element\Template
{
    /**
     * Theme helper
     *
     * @var HelperData
     */
    protected $theme;

    /**
     * Header block helper
     *
     * @var ...
     */
    // protected $helperHeader;

    /**
     * @param Context $context
     * @param HelperData $helperData
     * @param array $data
     */
    public function __construct(
        Context $context,
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
