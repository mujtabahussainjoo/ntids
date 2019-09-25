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



namespace Mirasvit\Helpdesk\Model\ResourceModel\Report;

use Mirasvit\Helpdesk\Model\Config as Config;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Ticket extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    const FLAG_CODE = 'report_ticket';

    /**
     * @var \Magento\Reports\Model\FlagFactory
     */
    protected $flagFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Report
     */
    protected $helpdeskReport;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Model\ResourceModel\Db\Context
     */
    protected $context;

    /**
     * @var object
     */
    protected $resourcePrefix;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $localeDate;

    /**
     * @param \Magento\Reports\Model\FlagFactory                   $flagFactory
     * @param \Magento\Framework\Stdlib\DateTime\DateTime          $date
     * @param \Mirasvit\Helpdesk\Helper\Report                     $helpdeskReport
     * @param \Magento\Store\Model\StoreManagerInterface           $storeManager
     * @param \Magento\Framework\Model\ResourceModel\Db\Context    $context
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param null                                                 $resourcePrefix
     * @param array                                                $data
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(
        \Magento\Reports\Model\FlagFactory $flagFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Mirasvit\Helpdesk\Helper\Report $helpdeskReport,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        $resourcePrefix = null,
        array $data = []
    ) {
        $this->flagFactory = $flagFactory;
        $this->date = $date;
        $this->helpdeskReport = $helpdeskReport;
        $this->storeManager = $storeManager;
        $this->context = $context;
        $this->resourcePrefix = $resourcePrefix;
        $this->localeDate = $localeDate;
        parent::__construct($context, $resourcePrefix);
    }

    /**
     *
     */
    protected function _construct()
    {
        $this->_init('mst_helpdesk_ticket_aggregated', 'ticket_aggregated_id');

        $this->_setResource(['read', 'write']);
    }

    /**
     * @param null $from
     *
     * @return $this
     */
    public function aggregate($from = null)
    {
        if ($from !== null) {
            $from = $this->localeDate->formatDate($from);
        }

        if ($from == null) {
            $from = new \Zend_Date(
                $this->date->gmtTimestamp(),
                null,
                $this->storeManager->getStore()->getLocaleCode()
            );

            $from->subYear(10);

            $this->_aggregateTickets($from->get(\Magento\Framework\Stdlib\DateTime::DATETIME_INTERNAL_FORMAT));
        } else {
            $this->_aggregateTickets($from);
        }

        $this->_refreshFlag();

        return $this;
    }

    /**
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _refreshFlag()
    {
        $flag = $this->flagFactory->create();
        $flag->setReportFlagCode(self::FLAG_CODE)
            ->unsetData()
            ->loadSelf()
            ->setLastUpdate($this->localeDate->formatDate(new \DateTime()))
            ->save()
            ;
    }

    /**
     * @param string $from
     *
     * @return $this
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _aggregateTickets($from)
    {
        $adapter = $this->getConnection();

        $ticketTable = $this->getTable('mst_helpdesk_ticket');
        $messageTable = $this->getTable('mst_helpdesk_message');
        $satisfactionTable = $this->getTable('mst_helpdesk_satisfaction');

        $aggregateTables = [
            $this->getTable('mst_helpdesk_ticket_aggregated_hour') => '%Y-%m-%d %H:00:00',
        //            $this->getTable('mst_helpdesk_ticket_aggregated_day') => '%Y-%m-%d 00:00:00',
        //            $this->getTable('mst_helpdesk_ticket_aggregated_month') => '%Y-%m-01 00:00:00',
        ];

        foreach ($aggregateTables as $tableName => $periodFormat) {
            # remove data before insert
            $adapter->delete($tableName, "period >= '$from'");

            $periodStatement = new \Zend_Db_Expr('DATE_FORMAT(created_at, "'.$periodFormat.'")');

            $ticketSelect = $adapter->select()
                ->from(
                    [$ticketTable],
                    [
                        'user_id' => 'user_id',
                        'period' => $periodStatement,
                    ]
                )
                ->where('folder <> '.Config::FOLDER_SPAM)
                ->where('created_at >= ?', $from)
                ->group($periodStatement)
                ->group('user_id')
                ;

            $messageSelect = $adapter->select()
                ->from(
                    [$messageTable],
                    [
                        'user_id' => 'user_id',
                        'period' => $periodStatement,
                    ]
                )
                ->where('created_at >= ?', $from)
                ->group($periodStatement)
                ->group('user_id')
                ;

            $satisfactionSelect = $adapter->select()
                ->from(
                    [$satisfactionTable],
                    [
                        'user_id' => 'user_id',
                        'period' => $periodStatement,
                    ]
                )
                ->where('created_at >= ?', $from)
                ->group($periodStatement)
                ->group('user_id')
                ;

            # number of new tickes
            $newTicketSql = clone $ticketSelect;
            $newTicketSql
                ->columns(['new_ticket_cnt' => new \Zend_Db_Expr('COUNT(ticket_id)')])
                ;

            $this->_insertOnDublicate($tableName, $newTicketSql, ['new_ticket_cnt']);

            # number of changed tickets (number of unique tickets with replies)
            $changedTicketSql = clone $messageSelect;
            $changedTicketSql
                ->columns(['changed_ticket_cnt' => new \Zend_Db_Expr('COUNT(DISTINCT(ticket_id))')])
                ->where('triggered_by = ?', 'user')
                ;
            $this->_insertOnDublicate($tableName, $changedTicketSql, ['changed_ticket_cnt']);

            # number of replies
            $replyCntSql = clone $messageSelect;
            $replyCntSql
                ->columns(['total_reply_cnt' => new \Zend_Db_Expr('COUNT(message_id)')])
                ->where('triggered_by = ?', 'user')
                ;
            $this->_insertOnDublicate($tableName, $replyCntSql, ['total_reply_cnt']);

            # number of solved tickets
            $solvedTicketSql = clone $ticketSelect;
            $solvedTicketSql
                ->columns(['solved_ticket_cnt' => new \Zend_Db_Expr('COUNT(ticket_id)')])
                ->where('status_id IN(?)', $this->helpdeskReport->getSolvedStatuses())
                ;
            $this->_insertOnDublicate($tableName, $solvedTicketSql, ['solved_ticket_cnt']);

            # average first reply time (seconds)
            $firstReplyTimeSql = clone $ticketSelect;
            $firstReplyTimeSql
                ->columns([
                    'first_reply_time' => new \Zend_Db_Expr(
                        'AVG(UNIX_TIMESTAMP(first_reply_at) - UNIX_TIMESTAMP(created_at))'
                    ),
                ])
                ->where('first_reply_at IS NOT NULL')
                ;
            $this->_insertOnDublicate($tableName, $firstReplyTimeSql, ['first_reply_time']);

            # average full resolution time (status solved, seconds)
            $fullResolutionTimeSql = clone $ticketSelect;
            $fullResolutionTimeSql
                ->columns([
                    'full_resolution_time' => new \Zend_Db_Expr(
                        'AVG(UNIX_TIMESTAMP(last_reply_at) - UNIX_TIMESTAMP(created_at))'
                    ),
                ])
                ->where('first_reply_at IS NOT NULL')
                ->where('last_reply_at IS NOT NULL')
                ->where('status_id IN(?)', $this->helpdeskReport->getSolvedStatuses())
                ;
            $this->_insertOnDublicate($tableName, $fullResolutionTimeSql, ['full_resolution_time']);

            # number of rates (1, 2, 3)
            $satisfactionRateNSql = clone $satisfactionSelect;
            $satisfactionRateNSql
                ->columns(
                    [
                        'satisfaction_rate_1_cnt' => new \Zend_Db_Expr('SUM(IF(rate = 1, 1, 0))'),
                        'satisfaction_rate_2_cnt' => new \Zend_Db_Expr('SUM(IF(rate = 2, 1, 0))'),
                        'satisfaction_rate_3_cnt' => new \Zend_Db_Expr('SUM(IF(rate = 3, 1, 0))'),
                    ]
                )
                ;
            $this->_insertOnDublicate($tableName, $satisfactionRateNSql, [
                    'satisfaction_rate_1_cnt',
                    'satisfaction_rate_2_cnt',
                    'satisfaction_rate_3_cnt',
                ]);

            # satisfaction rate
            $satisfactionRateSql = clone $satisfactionSelect;
            $satisfactionRateSql
                ->columns(
                    [
                        'satisfaction_rate' => new \Zend_Db_Expr('SUM(rate) / COUNT(rate) / 3 * 100'),
                    ]
                )
                ;
            $this->_insertOnDublicate($tableName, $satisfactionRateSql, [
                    'satisfaction_rate',
                ]);

            # satisfaction response count
            $satisfactionResponseCntSql = clone $satisfactionSelect;
            $satisfactionResponseCntSql
                ->columns(
                    [
                        'satisfaction_response_cnt' => new \Zend_Db_Expr('COUNT(rate)'),
                    ]
                )
                ;

            $this->_insertOnDublicate($tableName, $satisfactionResponseCntSql, [
                    'satisfaction_response_cnt',
                ]);

            // satisfaction response rate
            $adapter->query(
                "UPDATE $tableName
                SET satisfaction_response_rate = (satisfaction_response_cnt / total_reply_cnt) * 100"
            );
        }

        return $this;
    }

    /**
     * @param string $tableName
     * @param string $select
     * @param array  $columns
     *
     * @return $this
     */
    protected function _insertOnDublicate($tableName, $select, $columns)
    {
        $adapter = $this->getConnection();

        $rows = $adapter->fetchAll($select);

        if (count($rows) == 0) {
            return $this;
        }
        $adapter->insertOnDuplicate(
            $tableName,
            $rows,
            $columns
        );

        return $this;
    }
}
