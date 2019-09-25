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



namespace Mirasvit\Helpdesk\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Mirasvit\Helpdesk\Api\Data\TicketInterface;
use Mirasvit\Helpdesk\Model\Message;
use Mirasvit\Helpdesk\Model\ResourceModel\Message\CollectionFactory as MessageCollectionFactory;
use Mirasvit\Helpdesk\Helper\StringUtil;

class SubjectColumn extends Column
{
    /**
     * @var MessageCollectionFactory
     */
    private $messageCollectionFactory;

    /**
     * @var StringUtil
     */
    private $stringUtil;

    public function __construct(
        MessageCollectionFactory $messageCollectionFactory,
        StringUtil $stringUtil,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        $this->messageCollectionFactory = $messageCollectionFactory;
        $this->stringUtil = $stringUtil;

        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @var int
     */
    private $len = 50;

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $item[$this->getData('name')] = $this->prepareItem($this->getData('name'), $item);

                $this->addQuickViewInfo($item);
            }
        }

        return $dataSource;
    }

    /**
     * Format data.
     *
     * @param string $fieldName
     * @param array $item
     * @return string
     */
    protected function prepareItem($fieldName, array $item)
    {
        $subject = $item[$fieldName];
        if (strlen($subject) > $this->len) {
            $subject = mb_substr($subject, 0, $this->len) . '...';
        }

        return $subject;
    }

    protected function addQuickViewInfo(array &$item)
    {
        $collection = $this->messageCollectionFactory->create();
        $collection->addFieldToFilter(TicketInterface::KEY_ID, $item[TicketInterface::KEY_ID])
            ->setOrder('main_table.created_at', 'asc');

        $firstMessage = null;
        $lastMessage = null;

        /** @var Message $message */
        foreach ($collection as $message) {
            if (!$firstMessage) {
                $firstMessage = $this->prepareMessage($message);
            }

            $lastMessage = $this->prepareMessage($message);
        }

        if ($lastMessage && $lastMessage == $firstMessage) {
            $lastMessage = null;
        }

        $item['firstMessage'] = $firstMessage;
        $item['lastMessage'] = $lastMessage;

        return $item;
    }

    /**
     * @param Message $message
     * @return array
     */
    private function prepareMessage($message)
    {
        $data = $message->getData();

        $data['body'] = $message->getUnsafeBodyHtml();

        if (strlen($data['body']) > 400) {
            $data['body'] = mb_substr($data['body'], 0, 400) . '...';
        }

        $data['created_at'] = $this->stringUtil->nicetime(strtotime($data['created_at']));

        return $data;
    }
}
