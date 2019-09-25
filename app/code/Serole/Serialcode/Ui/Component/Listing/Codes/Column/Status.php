<?php

namespace Serole\Serialcode\Ui\Component\Listing\Codes\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

class Status extends Column
{
      protected $_urlBuilder;

    /**
     * @var string
     */
    private $_editUrl;

    /**
     * @param ContextInterface   $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface       $urlBuilder
     * @param array              $components
     * @param array              $data
     * @param string             $editUrl
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []

    ) {
        $this->_urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source.
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
		//echo "<pre>";
		
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as $key=>$item) {
				
                if($item['status'] == 1){
                    $item['status'] = 'Assigned';
                }elseif ($item['status'] == 0){
                    $item['status'] = 'Released';
                }
				elseif ($item['status'] == 2){
                    $item['status'] = 'Invalid';
                }
				$dataSource['data']['items'][$key] = $item;
            }
			
        }
//print_r($dataSource['data']['items']);
        return $dataSource;
    }
}
