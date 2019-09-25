<?php 
namespace Serole\Itemsales\Ui\Component\Listing\Columns;

class CreatedAt extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * Column name
     */
    const NAME = 'column.created_at';

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }
    public function prepareDataSource(array $dataSource) {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as $r => $v) {
                if(strtotime($v['date']) < 0){
                    $dataSource['data']['items'][$r]['date'] = "-----";
                } else{
                    $dataSource['data']['items'][$r]['date'] = date('MM/dd/Y HH:mm:ss', strtotime($v['date']));
                }
            }
        }
        return $dataSource;
    }
}