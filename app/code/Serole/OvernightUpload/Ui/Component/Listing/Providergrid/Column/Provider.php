<?php

namespace Serole\OvernightUpload\Ui\Component\Listing\Providergrid\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

class Provider extends Column
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
        $providerCodeAttribute = $this->eav->getAttribute('catalog_product', 'provider');
        $providerCodeOptions = $providerCodeAttribute->getSource()->getAllOptions();

        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                if($item['providerid']){
                    $key = array_search($item['providerid'], array_column($providerCodeOptions, 'value'));
                    $item['providerid'] = $providerCodeOptions[$key]['label'];
                }else{
                    $item['providerid'] = "NA";
                }
            }
        }

        return $dataSource;
    }
}
