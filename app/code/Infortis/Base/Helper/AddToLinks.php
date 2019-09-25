<?php

namespace Infortis\Base\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Escaper;
use Magento\Wishlist\Helper\Data as WishlistHelperData;
use Magento\Catalog\Helper\Product\Compare as CompareHelperData;
use Magento\Catalog\Model\Product;

class AddToLinks extends AbstractHelper
{
    /**
     * @var WishlistHelperData
     */
    protected $wishlistHelper;

    /**
     * @var CompareHelperData
     */
    protected $compareHelper;

    /**
     * @var Escaper
     */
    protected $escaper;

    /**
     * @var string
     */
    protected $wishlistLabel;

    /**
     * @var string
     */
    protected $compareLabel;

    /**
     * @var bool
     */
    protected $showCompare = true;

    public function __construct(
        Context $context, 
        WishlistHelperData $wishlistHelper,
        CompareHelperData $compareHelper,
        Escaper $escaper
    ) {
        $this->wishlistHelper = $wishlistHelper;
        $this->compareHelper = $compareHelper;
        $this->escaper = $escaper;

        parent::__construct($context);

        $this->wishlistLabel = $this->escaper->escapeHtml(__('Add to Wish List'));
        $this->compareLabel = $this->escaper->escapeHtml(__('Add to Compare'));
    }

    /**
     * Render "Add to" links for category view, simple icons
     *
     * @param Product $product
     * @return string
     */
    public function getLinks($product)
    {
        $html = '';

        if ($this->wishlistHelper->isAllow())
        {
            $html .= '
            <a href="#"
                class="action towishlist"
                title="' . $this->wishlistLabel . '"
                aria-label="' . $this->wishlistLabel . '"
                data-post=\'' . /* @escapeNotVerified */ $this->wishlistHelper->getAddParams($product) . '\'
                data-action="add-to-wishlist"
                role="button">' . $this->wishlistLabel . '</a>
            ';
        }

        if ($this->showCompare)
        {
            $html .= '
            <a href="#"
                class="action tocompare"
                title="' . $this->compareLabel . '"
                aria-label="' . $this->compareLabel . '"
                data-post=\'' . /* @escapeNotVerified */ $this->compareHelper->getPostDataParams($product) . '\'
                role="button">' . $this->compareLabel . '</a>
            ';
        }

        return $html;
    }

    /**
     * Render "Add to" links for category view, simple icons
     *
     * @param Product $product
     * @param string $linkClass
     * @param string $iconClass
     * @return string
     */
    public function getLinksIcons($product, $linkClass = '', $iconClass = '')
    {
        $html = '';

        if ($this->wishlistHelper->isAllow())
        {
            $html .= '
            <a href="#"
                class="action towishlist '. $linkClass .'"
                title="' . $this->wishlistLabel . '"
                aria-label="' . $this->wishlistLabel . '"
                data-post=\'' . /* @escapeNotVerified */ $this->wishlistHelper->getAddParams($product) . '\'
                data-action="add-to-wishlist"
                role="button">
                    <span class="icon ib ib-hover ic ic-heart '. $iconClass .'"></span>
                    <span class="label">' . $this->wishlistLabel . '</span>
            </a>
            ';
        }

        if ($this->showCompare)
        {
            $html .= '
            <a href="#"
                class="action tocompare '. $linkClass .'"
                title="' . $this->compareLabel . '"
                aria-label="' . $this->compareLabel . '"
                data-post=\'' . /* @escapeNotVerified */ $this->compareHelper->getPostDataParams($product) . '\'
                role="button">
                    <span class="icon ib ib-hover ic ic-compare '. $iconClass .'"></span>
                    <span class="label">' . $this->compareLabel . '</span>
            </a>
            ';
        }

        return $html;
    }
}
