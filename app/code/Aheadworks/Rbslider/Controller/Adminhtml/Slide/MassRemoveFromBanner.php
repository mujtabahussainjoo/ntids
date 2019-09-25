<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rbslider\Controller\Adminhtml\Slide;

/**
 * Class MassRemoveFromBanner
 * @package Aheadworks\Rbslider\Controller\Adminhtml\Slide
 */
class MassRemoveFromBanner extends AbstractMassAction
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
                $foundBannerId = array_search($bannerId, $bannerIds);
                if (false !== $foundBannerId) {
                    unset($bannerIds[$foundBannerId]);
                    $slideDataObject->setBannerIds($bannerIds);
                    $this->slideRepository->save($slideDataObject);
                    $count++;
                }
            }
        }
        $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been updated', $count));
    }
}
