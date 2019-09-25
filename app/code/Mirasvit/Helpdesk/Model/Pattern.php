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

use Magento\Checkout\Exception;
use Magento\Framework\DataObject\IdentityInterface;

/**
 * @method \Mirasvit\Helpdesk\Model\ResourceModel\Pattern\Collection|\Mirasvit\Helpdesk\Model\Pattern[] getCollection()
 * @method \Mirasvit\Helpdesk\Model\Pattern load(int $id)
 * @method bool getIsMassDelete()
 * @method \Mirasvit\Helpdesk\Model\Pattern setIsMassDelete(bool $flag)
 * @method bool getIsMassStatus()
 * @method \Mirasvit\Helpdesk\Model\Pattern setIsMassStatus(bool $flag)
 * @method \Mirasvit\Helpdesk\Model\ResourceModel\Pattern getResource()
 * @method string getScope()
 * @method \Mirasvit\Helpdesk\Model\Pattern setScope(string $param)
 * @method string getPattern()
 * @method \Mirasvit\Helpdesk\Model\Pattern setPattern(string $param)
 * @method string getCreatedAt()
 * @method $this setCreatedAt(string $param)
 * @method string getUpdatedAt()
 * @method $this setUpdatedAt(string $param)
 */
class Pattern extends \Magento\Framework\Model\AbstractModel implements IdentityInterface
{
    const CACHE_TAG = 'helpdesk_pattern';

    /**
     * @var string
     */
    protected $_cacheTag = 'helpdesk_pattern';

    /**
     * @var string
     */
    protected $_eventPrefix = 'helpdesk_pattern';

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
        $this->_init('Mirasvit\Helpdesk\Model\ResourceModel\Pattern');
    }

    /**
     * @param string|false $emptyOption
     * @return array
     */
    public function toOptionArray($emptyOption = false)
    {
        return $this->getCollection()->toOptionArray($emptyOption);
    }

    /************************/

    /**
     * @param \Mirasvit\Helpdesk\Model\Email $email
     *
     * @return bool
     */
    public function checkEmail($email)
    {
        $subject = '';
        switch ($this->getScope()) {
            case 'headers':
                $subject = $email->getHeaders();
                break;
            case 'subject':
                $subject = $email->getSubject();
                break;
            case 'body':
                $subject = $email->getBody();
                break;
        }
        $matches = [];
        preg_match(preg_quote($this->getPattern()), $subject, $matches);
        if (count($matches) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeSave() 
    {
        try {
            preg_match($this->getPattern(), '');
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(__("Pattern is invalid"));
        }
        return parent::beforeSave();
    }
}
