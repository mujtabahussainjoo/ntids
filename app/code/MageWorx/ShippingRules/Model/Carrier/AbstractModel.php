<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Model\Carrier;

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\State;
use Magento\Framework\App\Area;

abstract class AbstractModel extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * AbstractModel constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param StoreManagerInterface $storeManager
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        StoreManagerInterface $storeManager,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->storeManager = $storeManager;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Columns which will be ignored during import/export process
     * @see \MageWorx\ShippingRules\Model\Carrier\AbstractModel::getIgnoredColumnsForImportExport()
     */
    const IMPORT_EXPORT_IGNORE_COLUMNS = [
        'id'
    ];

    /**
     * Get columns which should be removed during import\export process
     *
     * @return array
     */
    public static function getIgnoredColumnsForImportExport()
    {
        return static::IMPORT_EXPORT_IGNORE_COLUMNS;
    }

    /**
     * Convert current object to string
     *
     * @param string $format
     * @return mixed|string
     */
    public function toString($format = '')
    {
        if (empty($format)) {
            $result = implode(', ', $this->getData());
        } else {
            preg_match_all('/\{\{([a-z0-9_]+)\}\}/is', $format, $matches);
            foreach ($matches[1] as $var) {
                $method = 'get' . implode('', array_map('ucfirst', explode('_', $var)));
                if (method_exists($this, $method)) {
                    $data = $this->{$method}();
                } else {
                    $data = $this->getData($var);
                }

                // Format array values
                if (is_array($data)) {
                    $formattedData = json_encode(
                        $data,
                        JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
                    );
                } else {
                    $formattedData = $data;
                }

                $formattedData = str_ireplace(',"', '`"', $formattedData);
                $formattedData = str_ireplace('",', '"`', $formattedData);

                $format = str_replace('{{' . $var . '}}', $formattedData, $format);
            }
            $result = $format;
        }

        return $result;
    }

    /**
     * Get Carrier label by specified store
     *
     * @param \Magento\Store\Model\Store|int|bool|null $store
     * @return string|bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getStoreLabel($store = null)
    {
        if ($this->_appState->getAreaCode() === Area::AREA_ADMINHTML) {
            return false;
        }

        $storeId = $this->storeManager->getStore($store)->getId();
        $labels = (array)$this->getStoreLabels();

        if (isset($labels[$storeId])) {
            return $labels[$storeId];
        } elseif (isset($labels[0]) && $labels[0]) {
            return $labels[0];
        }

        return false;
    }
}