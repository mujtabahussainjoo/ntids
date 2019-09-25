<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rbslider\Ui\Component\Listing\Columns;

use Aheadworks\Rbslider\Api\Data\SlideInterface;
use Aheadworks\Rbslider\Api\BannerRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

/**
 * Class Banners
 * @package Aheadworks\Rbslider\Ui\Component\Listing\Columns
 */
class Banners extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @var BannerRepositoryInterface
     */
    private $bannerRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param BannerRepositoryInterface $bannerRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        BannerRepositoryInterface $bannerRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        array $components = [],
        array $data = []
    ) {
        parent::__construct(
            $context,
            $uiComponentFactory,
            $components,
            $data
        );
        $this->bannerRepository = $bannerRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $slide) {
                if (is_array($slide[SlideInterface::BANNER_IDS])) {
                    $banners = $this->bannerRepository
                        ->getList($this->getSearchCriteria($slide[SlideInterface::BANNER_IDS]))
                        ->getItems();
                    $bannerLinks = [];
                    foreach ($banners as $banner) {
                        $bannerLinks[] = [
                            'name' => $banner->getName(),
                            'url' => $this->getLink($banner->getId())
                        ];
                    }
                    $slide['banners'] = $bannerLinks;
                }
            }
        }
        return $dataSource;
    }

    /**
     * Retrieve link for for banner
     *
     * @param int $entityId
     * @return string
     */
    private function getLink($entityId)
    {
        return $this->context->getUrl('aw_rbslider_admin/banner/edit', ['id' => $entityId]);
    }

    /**
     * Create SearchCriteria for bannerIds
     *
     * @param array $bannerIds
     * @return \Magento\Framework\Api\SearchCriteria
     */
    private function getSearchCriteria(array $bannerIds)
    {
        return $this->searchCriteriaBuilder
            ->addFilter('id', $bannerIds, 'in')
            ->create();
    }
}
