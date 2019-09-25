<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rbslider\Controller\Adminhtml\Slide;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Aheadworks\Rbslider\Model\Slide\ImageFileUploader;

/**
 * Class UploadImage
 * @package Aheadworks\Rbslider\Controller\Adminhtml\Slide
 */
class UploadImage extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Aheadworks_Rbslider::slides';

    /**
     * @var ImageFileUploader
     */
    private $imageFileUploader;

    /**
     * @param Context $context
     * @param ImageFileUploader $imageFileUploader
     */
    public function __construct(
        Context $context,
        ImageFileUploader $imageFileUploader
    ) {
        parent::__construct($context);
        $this->imageFileUploader = $imageFileUploader;
    }

    /**
     * Image upload action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $result = $this->imageFileUploader->saveImageToMediaFolder('img_file');
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}
