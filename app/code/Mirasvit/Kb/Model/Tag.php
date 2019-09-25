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



namespace Mirasvit\Kb\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Mirasvit\Core\Api\UrlRewriteHelperInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;

/**
 * @method string getName()
 * @method string getUrlKey()
 */
class Tag extends AbstractModel implements IdentityInterface
{
    const CACHE_TAG = 'kb_tag';

    /**
     * @var string
     */
    protected $_cacheTag = 'kb_tag';

    /**
     * @var string
     */
    protected $_eventPrefix = 'kb_tag';

    /**
     * @var UrlRewriteHelperInterface
     */
    protected $urlRewrite;

    /**
     * @param UrlRewriteHelperInterface $urlRewrite
     * @param Context                   $context
     * @param Registry                  $registry
     */
    public function __construct(
        UrlRewriteHelperInterface $urlRewrite,
        Context $context,
        Registry $registry
    ) {
        $this->urlRewrite = $urlRewrite;

        parent::__construct($context, $registry);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('Mirasvit\Kb\Model\ResourceModel\Tag');
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->urlRewrite->getUrl('KBASE', 'TAG', $this);
    }
}
