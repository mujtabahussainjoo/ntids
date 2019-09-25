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

namespace Serole\Productattachment\Block\Adminhtml\Productattachment\Renderer;

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

    protected $product;

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
        \Magento\Catalog\Model\Product $product,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\Registry $registry
    ) {
        $this->dataHelper = $dataHelper;
        $this->assetRepo = $assetRepo;
        $this->helper = $helper;
        $this->urlBuilder = $urlBuilder;
        $this->coreRegistry = $registry;
        $this->product = $product;
    }

    /**
     * get customer group name
     * @param  DataObject $row
     * @return string
     */
    public function getElementHtml()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $request = $objectManager->get('Magento\Framework\App\Request\Http');
        $id = $request->getParam('id');

        $fileIcon = '<h3>No File Uploded</h3>';
        $file = $this->getValue();
        $result = array();
        $data = '';
        if ($file) {
            $fileExt = pathinfo($file, PATHINFO_EXTENSION);
            if ($fileExt) {
                $iconImage = $this->assetRepo->getUrl(
                    'Serole_Productattachment::images/'.$fileExt.'.png'
                );
                $url = $this->dataHelper->getBaseUrl().$file;
                $fileIcon = "<a href=".$url." target='_blank'><div>".$file."</div></a>";
            } else {
                $iconImage = $this->assetRepo->getUrl('Serole_Productattachment::images/unknown.png');
                $fileIcon = "<img src='".$iconImage."' style='float: left;' />";
            }
            //$attachId = $this->coreRegistry->registry('id');
            if($id) {
                $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
                $connection = $resource->getConnection();
                //$sql = "SELECT * FROM  `catalog_product_entity_int` WHERE  `attribute_id` =268 and `value` =" . $id;
                $sql = "SELECT eav.entity_id, product.sku
                        FROM catalog_product_entity_int AS eav
                        INNER JOIN catalog_product_entity AS product ON eav.entity_id = product.entity_id
                        AND eav.attribute_id =268
                        AND eav.value =".$id;
                $result = $connection->fetchAll($sql);
            }

           if(empty($result)) {
               $fileIcon .= "<a href='" . $this->urlBuilder->getUrl(
                       'productattachment/productattachment/docdelete', $param = ['id' => $id]) . "'>
                <div style='color:red;'>DELETE FILE</div></a>";
           }else{
               /*foreach ($result as $key => $item){
                   foreach ($result as $item){
                       $data .= $item['sku'].",";
                   }
               }
               $fileIcon .=  "<p> Assigned to Following Products,".$data."</p>";*/
           }
        }
        return $fileIcon;
    }
}
