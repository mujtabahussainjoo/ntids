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



namespace Mirasvit\Helpdesk\Block\Contacts;

class Schedule extends \Magento\Framework\View\Element\Template
{
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
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    protected $localeResolver;

    /**
     * @param \Magento\Framework\Locale\ResolverInterface      $localeResolver
     * @param \Mirasvit\Helpdesk\Helper\Schedule               $helpdeskSchedule
     * @param \Mirasvit\Helpdesk\Model\Config                  $config
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Url                           $urlManager
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        \Mirasvit\Helpdesk\Helper\Schedule $helpdeskSchedule,
        \Mirasvit\Helpdesk\Model\Config $config,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Url $urlManager,
        array $data = []
    ) {
        $this->_isScopePrivate = true;
        $this->localeResolver = $localeResolver;
        $this->helpdeskSchedule = $helpdeskSchedule;
        $this->config = $config;
        $this->context = $context;
        $this->urlManager = $urlManager;
        parent::__construct($context, $data);
    }

    /**
     * @param \Mirasvit\Helpdesk\Model\Schedule $schedule
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getScheduleHtml($schedule)
    {
        $layout = $this->getLayout();
        $block = $layout->createBlock('\Mirasvit\Helpdesk\Block\Contacts\Schedule\Schedule')
        ->setTemplate('contacts/schedule/schedule.phtml')
        ->setSchedule($schedule);
        return $block->toHtml();
    }

    /**
     * @return bool|null
     */
    public function getScheduleIsShowScheduleOnContactUs()
    {
        return $this->config->getScheduleIsShowScheduleOnContactUs();
    }

    /**
     * @return \Mirasvit\Helpdesk\Model\Schedule
     */
    public function getCurrentSchedule()
    {
        return $this->helpdeskSchedule->getCurrentSchedule();
    }

    /**
     * @return \Mirasvit\Helpdesk\Model\Schedule[]
     */
    public function getUpcomingScheduleCollection()
    {
        return $this->helpdeskSchedule->getUpcomingScheduleCollection();
    }

    /**
     * @return string
     */
    public function getWorkingScheduleTitle()
    {
        return $this->config->getWorkingScheduleTitle();
    }

    /**
     * @return string
     */
    public function getUpcomingScheduleTitle()
    {
        return $this->config->getUpcomingScheduleTitle();
    }

    /**
     * @return string
     */
    public function _toHtml()
    {
        if (!$this->getCurrentSchedule()) {
            return "";
        }
        return parent::_toHtml();
    }

    /**
     * @return \Magento\Framework\View\Element\BlockInterface $templateContext
     */
    public function getTemplateContext()
    {
        return $this->templateContext;
    }
}
