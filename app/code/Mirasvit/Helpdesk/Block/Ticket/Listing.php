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



namespace Mirasvit\Helpdesk\Block\Ticket;

use Mirasvit\Helpdesk\Api\Data\TicketInterface;
use Mirasvit\Helpdesk\Model\Config as Config;

class Listing extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Ticket\CollectionFactory
     */
    protected $ticketCollectionFactory;

    /**
     * @var \Mirasvit\Helpdesk\Model\Config
     */
    protected $config;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Field
     */
    protected $helpdeskField;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Framework\View\Element\Template\Context
     */
    protected $context;

    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Mirasvit\Helpdesk\Model\Config $config,
        \Mirasvit\Helpdesk\Model\PriorityFactory $priorityFactory,
        \Mirasvit\Helpdesk\Model\DepartmentFactory $departmentFactory,
        \Mirasvit\Helpdesk\Model\ResourceModel\Ticket\CollectionFactory $ticketCollectionFactory,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Url $urlManager,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->config = $config;
        $this->priorityFactory = $priorityFactory;
        $this->departmentFactory = $departmentFactory;
        $this->ticketCollectionFactory = $ticketCollectionFactory;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->customerFactory = $customerFactory;
        $this->customerSession = $customerSession;
        $this->urlManager = $urlManager;
        $this->context = $context;

        parent::__construct($context, $data);
    }

    /**
     * @return void
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        $title = $this->config->getDefaultFrontName($this->_storeManager->getStore());

        parent::_prepareLayout();
        $this->pageConfig->getTitle()->set($title);
        $pageMainTitle = $this->getLayout()->getBlock('page.main.title');
        if ($pageMainTitle) {
            $pageMainTitle->setPageTitle($title);
        }
    }

    /**
     * @return Listing\Column\DefaultColumn[]
     */
    public function getColumns()
    {
        $columns = [];

        $names = array_intersect($this->getChildNames(), $this->getGroupChildNames('column'));

        foreach ($names as $name) {
            $columns[$name] = $this->getChildBlock($name);
        }

        return $columns;
    }

    /**
     * @param Listing\Column\DefaultColumn $column
     * @param TicketInterface $item
     * @return string
     */
    public function getColumnHtml(Listing\Column\DefaultColumn $column, TicketInterface $item)
    {
        $column->setItem($item);

        return $column->toHtml();
    }

    /**
     * @return $this
     */
    protected function getCustomer()
    {
        return $this->customerFactory->create()->load($this->customerSession->getCustomerId());
    }

    /**
     * @return object
     */
    public function getTicketCollection()
    {
        $collection = $this->ticketCollectionFactory->create()
            ->addFieldToFilter('customer_id', $this->getCustomer()->getId())
            ->addFieldToFilter('folder', ['neq' => Config::FOLDER_SPAM]);

        return $collection;
    }

    /**
     * @return string
     */
    public function getCreateUrl()
    {
        $urlManager = clone $this->urlManager;
        if ($id = $this->context->getStoreManager()->getStore()->getId()) {
            $urlManager->setScope($id);
        }

        return $urlManager->getUrl('helpdesk/ticket/create');
    }
}
