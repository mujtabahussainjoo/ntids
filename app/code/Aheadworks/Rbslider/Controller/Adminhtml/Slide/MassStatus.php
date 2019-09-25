<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rbslider\Controller\Adminhtml\Slide;

/**
 * Class MassStatus
 * @package Aheadworks\Rbslider\Controller\Adminhtml\Slide
 */
class MassStatus extends AbstractMassAction
{
    /**
     * {@inheritdoc}
     */
    protected function massAction($collection)
    {
        $status = (int) $this->getRequest()->getParam('status');
        $count = 0;
        foreach ($collection->getItems() as $item) {
            $slideDataObject = $this->slideRepository->get($item->getId());
            $slideDataObject->setStatus($status);
            $this->slideRepository->save($slideDataObject);
            $count++;
        }
        $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been updated', $count));
    }
}
