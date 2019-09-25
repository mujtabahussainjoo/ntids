<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rbslider\Ui\DataProvider;

use Magento\Framework\View\Element\UiComponent\DataProvider\DataProviderInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Aheadworks\Rbslider\Model\Slide\ImageFileUploader;
use Aheadworks\Rbslider\Model\Source\ImageType;
use Magento\Framework\App\RequestInterface;
use Aheadworks\Rbslider\Model\ResourceModel\Slide\Grid\CollectionFactory;

/**
 * Class SlideDataProvider
 * @package Aheadworks\Rbslider\Ui\DataProvider
 */
class SlideDataProvider extends AbstractDataProvider implements DataProviderInterface
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var ImageFileUploader
     */
    private $imageFileUploader;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param RequestInterface $request
     * @param DataPersistorInterface $dataPersistor
     * @param ImageFileUploader $imageFileUploader
     * @param array $meta
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        RequestInterface $request,
        DataPersistorInterface $dataPersistor,
        ImageFileUploader $imageFileUploader,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->request = $request;
        $this->dataPersistor = $dataPersistor;
        $this->imageFileUploader = $imageFileUploader;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $data = [];
        $dataFromForm = $this->dataPersistor->get('aw_rbslider_slide');
        if (!empty($dataFromForm)) {
            $data[$dataFromForm['id']] = $dataFromForm;
            $this->dataPersistor->clear('aw_rbslider_slide');
        } else {
            $id = $this->request->getParam($this->getRequestFieldName());
            /** @var \Aheadworks\Rbslider\Model\Slide $slide */
            foreach ($this->getCollection()->getItems() as $slide) {
                if ($id == $slide->getId()) {
                    $data[$id] = $this->prepareFormData($slide->getData());
                }
            }
        }
        return $data;
    }

    /**
     * Prepare form data
     *
     * @param array $itemData
     * @return array
     */
    private function prepareFormData(array $itemData)
    {
        if ($itemData['img_type'] == ImageType::TYPE_FILE) {
            $itemData['img_file'] = [
                0 => [
                    'file' => $itemData['img_file'],
                    'url' => $this->imageFileUploader->getMediaUrl($itemData['img_file'])
                ]
            ];
        }
        return $itemData;
    }
}
