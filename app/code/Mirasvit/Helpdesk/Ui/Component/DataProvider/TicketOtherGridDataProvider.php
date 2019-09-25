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



namespace Mirasvit\Helpdesk\Ui\Component\DataProvider;

use Magento\Framework\App\RequestInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Ui\DataProvider\Modifier\PoolInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class TicketOtherGridDataProvider extends AbstractDataProvider
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Mirasvit\Helpdesk\Model\ResourceModel\Ticket\CollectionFactory $ticketCollectionFactory,
        \Mirasvit\Helpdesk\Model\TicketFactory $ticketFactory,
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory $collectionFactory,
        RequestInterface $request,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\View\Element\UiComponent\DataProvider\RegularFilter $regularFilter,
        array $meta = [],
        array $data = []
    ) {
        $this->ticketCollectionFactory = $ticketCollectionFactory;
        $this->collectionFactory = $collectionFactory;
        $this->ticketFactory = $ticketFactory;
        $this->regularFilter = $regularFilter;
        $this->registry = $registry;
        $this->request = $request;
        $this->collection = $this->ticketCollectionFactory->create()->joinFields();

        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $meta,
            $data
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $result = [];

        if (!(int)$this->request->getParam('currentTicketId')) {
            return [
                'totalRecords' => 0,
                'items'        => $result,
            ];
        }

        $ticket = $this->ticketFactory->create();
        $ticket->getResource()->load($ticket, $this->request->getParam('currentTicketId'));

        $this->collection->addOtherFilter($ticket);

        foreach ($this->collection->getItems() as $item) {
            $result[] = $item->getData();
        }

        return [
            'totalRecords' => $this->collection->getSize(),
            'items'        => $result,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getMeta()
    {
        $config = $this->getConfigData();
        $config['params']['currentTicketId'] = $this->request->getParam('id');
        $this->setConfigData($config);

        $meta = parent::getMeta();

        return $meta;
    }

    /**
     * {@inheritdoc}
     */
    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        if ($filter->getField() == 'status_id') {
            $filter->setField('main_table.status_id');
        }
        if ($filter->getField() == 'code') {
            $filter->setField('main_table.code');
        }
        if ($filter->getField() == 'subject') {
            $filter->setField('main_table.subject');
        }
        if ($filter->getField() == 'created_at') {
            $filter->setField('main_table.created_at');
        }
        if ($filter->getField() == 'user_id') {
            $filter->setField('main_table.user_id');
        }
        if ($filter->getField() == 'id') { // ticket grid on customer edit page
            $filter->setField('main_table.customer_id');
        }
        if ($filter->getField() == 'priority_id') { // ticket grid on customer edit page
            $filter->setField('main_table.priority_id');
        }

        parent::addFilter($filter);
    }
}
