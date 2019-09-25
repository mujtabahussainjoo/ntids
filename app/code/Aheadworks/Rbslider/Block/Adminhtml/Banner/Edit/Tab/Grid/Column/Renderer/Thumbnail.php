<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rbslider\Block\Adminhtml\Banner\Edit\Tab\Grid\Column\Renderer;

use Aheadworks\Rbslider\Model\Source\ImageType;
use Magento\Backend\Block\Context;
use Aheadworks\Rbslider\Model\Slide\ImageFileUploader;

/**
 * Class Thumbnail
 * @package Aheadworks\Rbslider\Block\Adminhtml\Banner\Edit\Tab\Grid\Column\Renderer
 */
class Thumbnail extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Text
{
    /**
     * @var ImageFileUploader
     */
    private $imageFileUploader;

    /**
     * @param Context $context
     * @param ImageFileUploader $imageFileUploader
     * @param array $data
     */
    public function __construct(
        Context $context,
        ImageFileUploader $imageFileUploader,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->imageFileUploader = $imageFileUploader;
    }

    /**
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        if ($row->getImgType() == ImageType::TYPE_FILE) {
            $imgUrl = $this->imageFileUploader->getMediaUrl($row->getImgFile());
        } else {
            $imgUrl = $row->getImgUrl();
        }
        return '<img width="200" src="' . $imgUrl . '"/>';
    }
}
