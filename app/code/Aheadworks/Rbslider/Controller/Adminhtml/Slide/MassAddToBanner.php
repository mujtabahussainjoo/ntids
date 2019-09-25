<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rbslider\Controller\Adminhtml\Slide;

/**
 * Class MassAddToBanner
 * @package Aheadworks\Rbslider\Controller\Adminhtml\Slide
 */
class MassAddToBanner extends AbstractMassAction
{
    /**
     * {@inheritdoc}
     */
    protected function massAction($collection)
    {
        $bannerId = (int) $this->getRequest()->getParam('banner_id');
        $count = 0;
        if ($bannerId) {
            foreach ($collection->getItems() as $item) {
                $slideDataObject = $this->slideRepository->get($item->getId());
                $bannerIds = $slideDataObject->getBannerIds();
                if (false === array_search($bannerId, $bannerIds)) {
                    $bannerIds[] = $bannerId;
                    $slideDataObject->setBannerIds($bannerIds);
                    $this->slideRepository->save($slideDataObject);
                    $count++;
                }
            }
        }
        $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been updated', $count));
    }
}
