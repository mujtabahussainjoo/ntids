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


namespace Mirasvit\Helpdesk\Reports;

use Mirasvit\Report\Api\Data\Query\ColumnInterface;
use Mirasvit\Report\Model\AbstractReport;


class Overview extends AbstractReport
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return __('Helpdesk Tickets');
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return 'helpdesk_overview';
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->setTable('mst_helpdesk_ticket_aggregated_hour');
        $this->addFastFilters([
            'mst_helpdesk_ticket_aggregated_hour|created_at',
            'mst_helpdesk_ticket_aggregated_hour|user',
        ]);

        $this->setDefaultColumns([
            'mst_helpdesk_ticket_aggregated_hour|new_ticket_cnt__sum',
            'mst_helpdesk_ticket_aggregated_hour|changed_ticket_cnt__sum',
            'mst_helpdesk_ticket_aggregated_hour|solved_ticket_cnt__sum',
            'mst_helpdesk_ticket_aggregated_hour|first_reply_time',
            'mst_helpdesk_ticket_aggregated_hour|total_reply_cnt__sum',
        ]);

        $this->addColumns([
            'mst_helpdesk_ticket_aggregated_hour|first_resolution_time',
            'mst_helpdesk_ticket_aggregated_hour|full_resolution_time',
            'mst_helpdesk_ticket_aggregated_hour|satisfaction_rate_1_cnt',
            'mst_helpdesk_ticket_aggregated_hour|satisfaction_rate_2_cnt',
            'mst_helpdesk_ticket_aggregated_hour|satisfaction_rate_3_cnt',
            'mst_helpdesk_ticket_aggregated_hour|satisfaction_response_cnt',
            'mst_helpdesk_ticket_aggregated_hour|satisfaction_response_rate',
        ]);

        $this->setDefaultDimension('mst_helpdesk_ticket_aggregated_hour|created_at__day');

        $this->addDimensions([
            'mst_helpdesk_ticket_aggregated_hour|created_at__hour',
            'mst_helpdesk_ticket_aggregated_hour|created_at__day',
            'mst_helpdesk_ticket_aggregated_hour|created_at__week',
            'mst_helpdesk_ticket_aggregated_hour|created_at__month',
            'mst_helpdesk_ticket_aggregated_hour|created_at__year',
            'admin_user|name',
        ]);

        $this->getChartConfig()
            ->setType('column')
            ->setDefaultColumns([
                'mst_helpdesk_ticket_aggregated_hour|new_ticket_cnt__sum',
        ]);
    }
}