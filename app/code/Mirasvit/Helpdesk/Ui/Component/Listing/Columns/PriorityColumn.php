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

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

class PriorityColumn extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @param ContextInterface                                           $context
     * @param UiComponentFactory                                         $uiComponentFactory
     * @param \Mirasvit\Helpdesk\Model\ResourceModel\Priority\Collection $priorityCollection
     * @param array                                                      $components
     * @param array                                                      $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Mirasvit\Helpdesk\Model\ResourceModel\Priority\Collection $priorityCollection,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->priorityCollection = $priorityCollection;
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $item[$this->getData('name')] = $this->prepareItem($this->getData('name'), $item);
            }
        }

        return $dataSource;
    }


    /**
     * Format data
     *
     * @param string $fieldName
     * @param array  $item
     * @return string
     */
    protected function prepareItem($fieldName, array $item)
    {
        $priorityId = $item[$fieldName];
        foreach ($this->priorityCollection as $priority) {
            if ($priority->getId() == $priorityId) {
                return
                    "<span class='color'><span class='{$priority->getColor()}'>".
                    $priority->getName().
                    '</span></span>';
            }
        }
    }
}
