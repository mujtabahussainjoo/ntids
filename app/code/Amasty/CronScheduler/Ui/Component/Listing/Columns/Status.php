<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CronScheduler
 */


namespace Amasty\CronScheduler\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Amasty\CronScheduler\Model\OptionSource\StatusFilter;

class Status extends Column
{
    /**
     * @var StatusFilter
     */
    private $statusFilter;

    public function __construct(
        StatusFilter $statusFilter,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->statusFilter = $statusFilter;
    }

    public function prepare()
    {
        $data = $this->getData();
        $data['config']['editor']['options'] = $this->statusFilter->toOptionArray();
        $this->setData($data);
        parent::prepare();
    }
}
