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
class ProductList extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $product;
    /**
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     */
    public function __construct(
        \Magento\Catalog\Model\Product $product
    ) {
        $this->product = $product;
    }

    /**
     * get category name
     * @param  DataObject $row
     * @return string
     */
    public function render(DataObject $row)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $id = $row->getId();
        $data = '';
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
            //echo "<pre>"; print_r($result);
            if($result){
                foreach ($result as $item){
                    $data .= $item['sku'].",";
                }
            }
        }

        return rtrim($data,",");
    }
}

