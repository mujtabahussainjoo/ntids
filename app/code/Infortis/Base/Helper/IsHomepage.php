<?php
/**
 * This helper is called only by controller Infortis\Base\Controller\Index\Index.
 * It can be removed if it's not needed anywhere else.
 */

namespace Infortis\Base\Helper;

class IsHomepage
{
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlInterface;
    
    public function __construct(
        \Magento\Framework\UrlInterface $urlInterface
    ) {
        $this->urlInterface = $urlInterface;
    }

    public function isHomepage()
    {
        return $this->urlInterface->getUrl('') == $this->urlInterface->getUrl('*/*/*', ['_current'=>true, '_use_rewrite'=>true]);
    }
}
