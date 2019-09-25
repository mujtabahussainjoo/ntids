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



namespace Mirasvit\Helpdesk\Block\Contacts\Schedule;

use Magento\Framework\DataObject\IdentityInterface;

/**
 *
 * @method \Mirasvit\Helpdesk\Model\Schedule getSchedule()
 */
class Schedule extends \Magento\Framework\View\Element\Template implements IdentityInterface
{
    /**
     * Cache group Tag
     */
    const CACHE_GROUP = 'helpdesk_schedule_block';

    /**
     * @var \Mirasvit\Helpdesk\Helper\Schedule
     */
    protected $helpdeskSchedule;

    /**
     * @var \Mirasvit\Helpdesk\Model\Config
     */
    protected $config;

    /**
     * @var \Magento\Framework\View\Element\Template\Context
     */
    protected $context;

    /**
     * @param \Mirasvit\Helpdesk\Helper\Schedule               $helpdeskSchedule
     * @param \Mirasvit\Helpdesk\Model\Config                  $config
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Url                           $urlManager
     * @param array                                            $data
     */
    public function __construct(
        \Mirasvit\Helpdesk\Helper\Schedule $helpdeskSchedule,
        \Mirasvit\Helpdesk\Model\Config $config,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Url $urlManager,
        array $data = []
    ) {
        $this->_isScopePrivate = true;
        $this->helpdeskSchedule = $helpdeskSchedule;
        $this->config = $config;
        $this->context = $context;
        $this->urlManager = $urlManager;
        parent::__construct($context, $data);
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_GROUP];
    }
}
