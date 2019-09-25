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
 * @package   mirasvit/module-helpdesk
 * @version   1.1.77
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Helpdesk\Model;

use Magento\Framework\DataObject\IdentityInterface;

/**
 * @method \Mirasvit\Helpdesk\Model\ResourceModel\Permission\Collection|
 * \Mirasvit\Helpdesk\Model\Permission[] getCollection()
 * @method \Mirasvit\Helpdesk\Model\Permission load(int $id)
 * @method bool getIsMassDelete()
 * @method \Mirasvit\Helpdesk\Model\Permission setIsMassDelete(bool $flag)
 * @method bool getIsMassStatus()
 * @method \Mirasvit\Helpdesk\Model\Permission setIsMassStatus(bool $flag)
 * @method \Mirasvit\Helpdesk\Model\ResourceModel\Permission getResource()
 * @method int[] getDepartmentIds()
 * @method \Mirasvit\Helpdesk\Model\Permission setDepartmentIds(array $ids)
 * @method bool getIsTicketRemoveAllowed()
 * @method \Mirasvit\Helpdesk\Model\Permission setIsTicketRemoveAllowed(bool $flag)
 */
class Permission extends \Magento\Framework\Model\AbstractModel implements IdentityInterface
{
    const CACHE_TAG = 'helpdesk_permission';

    /**
     * @var string
     */
    protected $_cacheTag = 'helpdesk_permission';

    /**
     * @var string
     */
    protected $_eventPrefix = 'helpdesk_permission';

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
        $this->_init('Mirasvit\Helpdesk\Model\ResourceModel\Permission');
    }

    /**
     * @param bool|false $emptyOption
     * @return array
     */
    public function toOptionArray($emptyOption = false)
    {
        return $this->getCollection()->toOptionArray($emptyOption);
    }

    /**
     *
     */
    public function loadDepartmentIds()
    {
        return $this->getResource()->loadDepartmentIds($this);
    }
}
