<?php

namespace Serole\GiftMessage\Ui\Component\Listing\Column;


class Images extends \Magento\Ui\Component\Listing\Columns\Column
{

    protected $emailTemplate;


    protected $pdfHelper;


    protected $giftImage;


    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        \Serole\Pdf\Helper\Pdf $pdfHelper,
        \Serole\GiftMessage\Model\Image $giftImage,
        array $components = [],
        array $data = []
    ) {
        $this->pdfHelper = $pdfHelper;
        $this->giftImage = $giftImage;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }


    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['image'])) {
                    if(file_exists($this->getImagePath($item['image']))){
                        $item['image'] = "<h2>Hello</h2>";
                    }

                }
            }
        }
        return $dataSource;
    }

    public function getImagePath($image){
        $giftMessageImageFolderName = 'giftimagestemplates';
        $filePath = $this->pdfHelper->getFilePath($giftMessageImageFolderName, $image);
        return $filePath;
    }

    public function getImageurl($image){
        $giftMessageImageFolderName = 'giftimagestemplates';
        $fileUrl = $this->pdfHelper->getFileUrl($giftMessageImageFolderName, $image);
        return $fileUrl;
    }
}
