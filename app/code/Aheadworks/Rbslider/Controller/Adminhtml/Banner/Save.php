<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rbslider\Controller\Adminhtml\Banner;

use Magento\Backend\App\Action\Context;
use Aheadworks\Rbslider\Model\Source\PageType;
use Aheadworks\Rbslider\Model\Source\Position;
use Aheadworks\Rbslider\Api\Data\BannerInterfaceFactory;
use Aheadworks\Rbslider\Api\Data\BannerInterface;
use Aheadworks\Rbslider\Api\BannerRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\Request\DataPersistorInterface;
use Aheadworks\Rbslider\Model\Converter\Condition as ConditionConverter;

/**
 * Class Save
 * @package Aheadworks\Rbslider\Controller\Adminhtml\Banner
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Save extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Aheadworks_Rbslider::banners';

    /**
     * @var BannerRepositoryInterface
     */
    private $bannerRepository;

    /**
     * @var BannerInterfaceFactory
     */
    private $bannerDataFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var ConditionConverter
     */
    private $conditionConverter;

    /**
     * @param Context $context
     * @param BannerRepositoryInterface $bannerRepository
     * @param BannerInterfaceFactory $bannerDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataPersistorInterface $dataPersistor
     * @param ConditionConverter $conditionConverter
     */
    public function __construct(
        Context $context,
        BannerRepositoryInterface $bannerRepository,
        BannerInterfaceFactory $bannerDataFactory,
        DataObjectHelper $dataObjectHelper,
        DataPersistorInterface $dataPersistor,
        ConditionConverter $conditionConverter
    ) {
        parent::__construct($context);
        $this->bannerRepository = $bannerRepository;
        $this->bannerDataFactory = $bannerDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataPersistor = $dataPersistor;
        $this->conditionConverter = $conditionConverter;
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $data = $this->prepareData($data);
            $id = isset($data['id']) ? $data['id'] : false;
            try {
                $bannerDataObject = $id
                    ? $this->bannerRepository->get($id)
                    : $this->bannerDataFactory->create();
                $this->dataObjectHelper->populateWithArray(
                    $bannerDataObject,
                    $data,
                    BannerInterface::class
                );
                if (!$bannerDataObject->getId()) {
                    $bannerDataObject->setId(null);
                }
                $banner = $this->bannerRepository->save($bannerDataObject);
                $this->dataPersistor->clear('aw_rbslider_banner');
                $this->messageManager->addSuccessMessage(__('Banner was successfully saved'));
                if ($this->getRequest()->getParam('back') == 'edit') {
                    return $resultRedirect->setPath('*/banner/edit', ['id' => $banner->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the banner'));
            }
            $this->dataPersistor->set('aw_rbslider_banner', $data);
            if ($id) {
                return $resultRedirect->setPath('*/*/edit', ['id' => $id, '_current' => true]);
            }
            return $resultRedirect->setPath('*/*/new', ['_current' => true]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Prepare data after save
     *
     * @param array $data
     * @return array
     */
    private function prepareData(array $data)
    {
        if (isset($data['slide_position'])) {
            $data['slide_ids'] = array_keys(json_decode($data['slide_position'], true));
        }
        if ($data['page_type'] == PageType::PRODUCT_PAGE) {
            if (isset($data['rule']['rbslider'])) {
                $conditionArray = $this->convertFlatToRecursive($data['rule'], ['rbslider']);
                if (is_array($conditionArray['rbslider']['1'])) {
                    $data['product_condition'] = $this->conditionConverter
                        ->arrayToDataModel($conditionArray['rbslider']['1']);
                } else {
                    $data['product_condition'] = '';
                }
            }
        } else {
            $data['product_condition'] = '';
        }
        if ($data['page_type'] != PageType::CATEGORY_PAGE) {
            $data['category_ids'] = '';
        }
        if ($data['page_type'] == PageType::CUSTOM_WIDGET) {
            $data['position'] = Position::CONTENT_TOP;
        }
        unset($data['rule']);
        return $data;
    }

    /**
     * Get conditions data recursively
     *
     * @param array $data
     * @param array $allowedKeys
     * @return array
     */
    private function convertFlatToRecursive(array $data, $allowedKeys = [])
    {
        $result = [];
        foreach ($data as $key => $value) {
            if (in_array($key, $allowedKeys) && is_array($value)) {
                foreach ($value as $id => $data) {
                    $path = explode('--', $id);
                    $node = & $result;

                    for ($i = 0, $l = sizeof($path); $i < $l; $i++) {
                        if (!isset($node[$key][$path[$i]])) {
                            $node[$key][$path[$i]] = [];
                        }
                        $node = & $node[$key][$path[$i]];
                    }
                    foreach ($data as $k => $v) {
                        $node[$k] = $v;
                    }
                }
            }
        }
        return $result;
    }
}
