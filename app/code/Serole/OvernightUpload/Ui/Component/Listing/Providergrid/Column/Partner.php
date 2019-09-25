<?php

namespace Serole\OvernightUpload\Ui\Component\Listing\Providergrid\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

class Partner extends Column
{

    private $eav;


    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Magento\Eav\Model\Config $eav,
        array $components = [],
        array $data = []
    ) {
        $this->eav = $eav;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }


    public function prepareDataSource(array $dataSource)
    {
        $partnerCodeAttribute = $this->eav->getAttribute('catalog_product', 'partnercode');
        $partnerCodeOptions = $partnerCodeAttribute->getSource()->getAllOptions();

        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                if($item['patner_groupid']){
                    $key = array_search($item['patner_groupid'], array_column($partnerCodeOptions, 'value'));
                    $item['patner_groupid'] = $partnerCodeOptions[$key]['label'];
                }else{
                    $item['patner_groupid'] = "NA";
                }
            }
        }

        return $dataSource;
    }
}
