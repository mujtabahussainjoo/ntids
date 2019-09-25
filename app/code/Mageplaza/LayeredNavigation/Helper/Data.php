<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_LayeredNavigation
 * @copyright   Copyright (c) 2017 Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\LayeredNavigation\Helper;

use Mageplaza\Core\Helper\AbstractData;

/**
 * Class Data
 * @package Mageplaza\LayeredNavigation\Helper
 */
class Data extends AbstractData
{
    const FILTER_TYPE_SLIDER = 'slider';
    const FILTER_TYPE_LIST = 'list';

    /** @var \Mageplaza\LayeredNavigation\Model\Layer\Filter */
    protected $filterModel;

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function isEnabled($storeId = null)
    {
        return $this->getGeneralConfig('enable', $storeId) && $this->isModuleOutputEnabled();
    }

    /**
     * @param string $code
     * @param null $storeId
     * @return mixed
     */
    public function getGeneralConfig($code = '', $storeId = null)
    {
        $code = ($code !== '') ? '/' . $code : '';

        return $this->getConfigValue('layered_navigation/general' . $code, $storeId);
    }

    /**
     * @param $filters
     * @return mixed
     */
    public function getLayerConfiguration($filters)
    {
        $filterParams = $this->_getRequest()->getParams();
        foreach ($filterParams as $key=>$param){
            $filterParams[$key] = htmlspecialchars($param);
        }

        $config       = new \Magento\Framework\DataObject([
            'active' => array_keys($filterParams),
            'params' => $filterParams
        ]);

        $this->getFilterModel()->getLayerConfiguration($filters, $config);

        return $this->objectManager->get('Magento\Framework\Json\EncoderInterface')->encode($config->getData());
    }

    /**
     * @return \Mageplaza\LayeredNavigation\Model\Layer\Filter
     */
    public function getFilterModel()
    {
        if (!$this->filterModel) {
            $this->filterModel = $this->objectManager->create('Mageplaza\LayeredNavigation\Model\Layer\Filter');
        }

        return $this->filterModel;
    }

    /**
     * @return \Magento\Framework\ObjectManagerInterface
     */
    public function getObjectManager()
    {
        return $this->objectManager;
    }
}
