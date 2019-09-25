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



namespace Mirasvit\Kb\Observer;

use Magento\Framework\Event\ObserverInterface;

class RegisterUrlRewriteObserver implements ObserverInterface
{
    /**
     * @var \Mirasvit\Kb\Model\Config
     */
    protected $config;

    /**
     * @var \Mirasvit\Core\Api\UrlRewriteHelperInterface
     */
    protected $urlRewrite;

    /**
     * @var \Mirasvit\Kb\Helper\Data
     */
    protected $kbData;

    /**
     * @var bool
     */
    public static $isRegistered;

    /**
     * @param \Mirasvit\Kb\Model\Config                    $config
     * @param \Mirasvit\Core\Api\UrlRewriteHelperInterface $urlRewrite
     * @param \Mirasvit\Kb\Helper\Data                     $kbData
     */
    public function __construct(
        \Mirasvit\Kb\Model\Config $config,
        \Mirasvit\Core\Api\UrlRewriteHelperInterface $urlRewrite,
        \Mirasvit\Kb\Helper\Data $kbData
    ) {
        $this->config = $config;
        $this->urlRewrite = $urlRewrite;
        $this->kbData = $kbData;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     *
     *
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (self::$isRegistered) {
            return;
        }
        $this->urlRewrite->setRewriteMode('KBASE', $this->config->isUrlRewriteEnabled());
        $this->urlRewrite->registerBasePath('KBASE', $this->config->getBaseUrl());
        $this->urlRewrite->registerPath(
            'KBASE',
            'ARTICLE',
            '[category_key]/[article_key]',
            'kbase_article_view'
        );
        $this->urlRewrite->registerPath('KBASE', 'CATEGORY', '[category_key]', 'kbase_category_view');
        $this->urlRewrite->registerPath(
            'KBASE',
            'CATEGORY_ROOT',
            '',
            'kbase_category_view',
            ['id' => $this->kbData->getRootCategory()->getId()]
        );
        $this->urlRewrite->registerPath('KBASE', 'TAG', 'tags/[tag_key]', 'kbase_tag_view');
        $this->urlRewrite->registerPath('KBASE', 'TAG_LIST', 'tags', 'kbase_tag_index');

        self::$isRegistered = true;
    }
}
