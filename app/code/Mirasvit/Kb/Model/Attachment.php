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

/**
 * @method \Mirasvit\Kb\Model\Resource\Attachment\Collection|\Mirasvit\Kb\Model\Attachment[] getCollection()
 * @method \Mirasvit\Kb\Model\Attachment load(int $id)
 * @method bool getIsMassDelete()
 * @method \Mirasvit\Kb\Model\Attachment setIsMassDelete(bool $flag)
 * @method bool getIsMassStatus()
 * @method \Mirasvit\Kb\Model\Attachment setIsMassStatus(bool $flag)
 * @method \Mirasvit\Kb\Model\Resource\Attachment getResource()
 */
class Attachment extends \Magento\Framework\Model\AbstractModel implements IdentityInterface
{
    const CACHE_TAG = 'kb_attachment';

    /**
     * @var string
     */
    protected $_cacheTag = 'kb_attachment';//@codingStandardsIgnoreLine

    /**
     * @var string
     */
    protected $_eventPrefix = 'kb_attachment';//@codingStandardsIgnoreLine

    /**
     * Get identities.
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG.'_'.$this->getId()];
    }

    /**
     *
     */
    protected function _construct()
    {
        $this->_init('Mirasvit\Kb\Model\ResourceModel\Attachment');
    }

    /**
     * @param bool|false $emptyOption
     * @return array
     */
    public function toOptionArray($emptyOption = false)
    {
        return $this->getCollection()->toOptionArray($emptyOption);
    }

    /************************/
}
