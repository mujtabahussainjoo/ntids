<?php

/**
 * MagePrince
 * Copyright (C) 2018 Mageprince
 *
 * NOTICE OF LICENSE
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see http://opensource.org/licenses/gpl-3.0.html
 *
 * @category MagePrince
 * @package Prince_Productattach
 * @copyright Copyright (c) 2018 MagePrince
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author MagePrince
 */

namespace Serole\Bulkemails\Block\Adminhtml\Sampledoc\Renderer;

use Magento\Framework\DataObject;

/**
 * Class FileIconAdmin
 * @package Prince\Productattach\Block\Adminhtml\Productattach\Renderer
 */
class FileIconAdmin extends \Magento\Framework\Data\Form\Element\AbstractElement
{

    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    private $assetRepo;

    /**
     * @var \Prince\Productattach\Helper\Data
     */
    private $dataHelper;

    /**
     * @var \Prince\Productattach\Helper\Data
     */
    private $helper;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlBuider;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry = null;

    /**
     * @param \Magento\Framework\View\Asset\Repository $assetRepo
     * @param \Prince\Productattach\Helper\Data $dataHelper
     * @param \Magento\Backend\Helper\Data $helper
     * @param \Magento\Framework\UrlInterface $urlBuilder
     */
    public function __construct(
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Serole\Productattachment\Helper\Data $dataHelper,
        \Magento\Backend\Helper\Data $helper,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\Registry $registry
    ) {
        $this->dataHelper = $dataHelper;
        $this->assetRepo = $assetRepo;
        $this->helper = $helper;
        $this->urlBuilder = $urlBuilder;
        $this->coreRegistry = $registry;
    }

    /**
     * get customer group name
     * @param  DataObject $row
     * @return string
     */
    public function getElementHtml()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $dirObj = $objectManager->get('\Magento\Framework\Filesystem\DirectoryList');
        $libDirPath = $dirObj->getPath('pub').'/';
        $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $libUrl = $storeManager->getStore()->getBaseUrl().'pub/';
        $fileName = 'docfiles/sample-bulkemails.csv';
        $filepath = $libDirPath.$fileName;
            if (file_exists($filepath)) {
                $fileUrl = $libUrl.$fileName;
                $fileIcon = "<a href=".$fileUrl." target='_blank'><div>Download Sample CSV File Here</div></a>";
                return $fileIcon;
            }
            return false;
    }
}
