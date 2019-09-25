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


namespace Mirasvit\Helpdesk\Service\Ticket;

use Mirasvit\Helpdesk\Api\Service\Ticket\TicketManagementInterface;

class TicketManagement implements TicketManagementInterface
{
    /**
     * @var \Mirasvit\Helpdesk\Logger\Logger
     */
    private $logger;

    /**
     * @var \Mirasvit\Helpdesk\Model\Config
     */
    private $config;

    /**
     * @var \Magento\Framework\App\State
     */
    private $state;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $customerSession;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    private $userSession;

    public function __construct(
        \Mirasvit\Helpdesk\Logger\Logger $logger,
        \Mirasvit\Helpdesk\Model\Config $config,
        \Magento\Framework\App\State $state,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Backend\Model\Auth\Session $userSession
    ) {
        $this->logger = $logger;
        $this->config = $config;
        $this->state = $state;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->customerSession = $customerSession;
        $this->userSession = $userSession;
    }

    /**
     * {@inheritdoc}
     */
    public function isRmaExists($ticket)
    {
        return (bool)count($this->getRmas($ticket));
    }

    /**
     * {@inheritdoc}
     */
    public function getRmas($ticket)
    {
        if (!$this->config->isActiveRma()) {
            return [];
        }

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $rmaRepository = $objectManager->create('\Mirasvit\Rma\Api\Repository\RmaRepositoryInterface');

        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('ticket_id', $ticket->getId())
        ;

        return $rmaRepository->getList($searchCriteria->create())->getItems();
    }

    /**
     * {@inheritdoc}
     */
    public function getRmasOptions($ticket)
    {
        $options = [];
        if (!$this->config->isActiveRma()) {
            return $options;
        }
        $rmas = $this->getRmas($ticket);

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var \Mirasvit\Rma\Helper\Rma\Url $rmaUrlHelpder */
        $rmaUrlHelpder = $objectManager->create('\Mirasvit\Rma\Helper\Rma\Url');

        foreach ($rmas as $rma) {
            $options[] = [
                'name' => $rma->getIncrementId(),
                'id'   => $rma->getId(),
                'url'  => $rmaUrlHelpder->getBackendUrl($rma),
            ];
        }

        return $options;
    }

    /**
     * {@inheritdoc}
     */
    public function logTicketDeletion($ticket)
    {
        if (!$this->config->getDeveloperLogTicketDeletion()) {
            return;
        }

        $message = '';
        $message .= 'Area: "' . $this->state->getAreaCode() . '"' . PHP_EOL;
        $message .= 'Ticket ID: "' . $ticket->getId() . '"' . PHP_EOL;
        if ($this->customerSession->getCustomerId()) {
            $message .= 'Customer ID: "' . $this->customerSession->getCustomerId() . '"' . PHP_EOL;
        }
        if ($userId = $this->userSession->getUser()->getId()) {
            $message .= 'User ID: "' . $userId . '"' . PHP_EOL;
        }
        $this->logger->addInfo($message);
    }
}

