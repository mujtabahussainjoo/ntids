<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-kb
 * @version   1.0.49
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Kb\Block\TopMenu;

class MainLink extends \Magento\Framework\View\Element\Html\Link
{
    /**
     * @var \Mirasvit\Kb\Model\Config
     */
    protected $config;

    /**
     * @var \Mirasvit\Kb\Helper\Data
     */
    protected $kbData;

    /**
     * @var \Mirasvit\Core\Api\UrlRewriteHelperInterface
     */
    protected $urlRewrite;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Mirasvit\Kb\Model\Config                        $config
     * @param \Mirasvit\Kb\Helper\Data                         $kbData
     * @param \Mirasvit\Core\Api\UrlRewriteHelperInterface     $urlRewrite
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Mirasvit\Kb\Model\Config $config,
        \Mirasvit\Kb\Helper\Data $kbData,
        \Mirasvit\Core\Api\UrlRewriteHelperInterface $urlRewrite,
        array $data = []
    ) {
        $this->config = $config;
        $this->kbData = $kbData;
        $this->urlRewrite = $urlRewrite;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getHref()
    {
        if ($this->config->isUrlRewriteEnabled()) {
            return $this->urlRewrite->getUrl('KBASE', 'CATEGORY_ROOT');
        }

        return $this->kbData->getHomeUrl();
    }
}
