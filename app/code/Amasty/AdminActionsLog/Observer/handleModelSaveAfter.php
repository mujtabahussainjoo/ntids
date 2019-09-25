<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_AdminActionsLog
 */


namespace Amasty\AdminActionsLog\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Logger;

class handleModelSaveAfter implements ObserverInterface
{
    protected $_objectManager;
    protected $_helper;
    protected $_scopeConfig;
    protected $_appState;
    protected $_isConfigSaved;
    protected $_registryManager;

    protected $_arrayKeysToString = ['associated_product_ids'];

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Amasty\AdminActionsLog\Helper\Data $helper,
        \Magento\Framework\App\State $appState
    ) {
        $this->_objectManager = $objectManager;
        $this->_registryManager = $coreRegistry;
        $this->_helper = $helper;
        $this->_scopeConfig = $scopeConfig;
        $this->_appState = $appState;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            if ($this->_appState->getAreaCode() === \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE) {
                $object = $observer->getObject();

                if ($this->_helper->needToSave($object)) {
                    $this->_saveLog($object);
                }
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            // no action is Area Code in not set
        }
    }

    protected function _saveLog($object)
    {
        if (!$this->_registryManager->registry('amaudit_log_saved')
            || $this->_isMassAction()
        ) {
            /** @var \Amasty\AdminActionsLog\Model\Log $logModel */
            $logModel = $this->_objectManager->create('Amasty\AdminActionsLog\Model\Log');
            $data = $logModel->prepareLogData($object);
            if (!isset($data['username'])) {
                return;
            }
            $logModel->addData($data);
            $logModel->save();
            $this->_registryManager->register('amaudit_log_saved', $logModel, true);
        } else {
            /** @var \Amasty\AdminActionsLog\Model\Log $logModel */
            $logModel = $this->_registryManager->registry('amaudit_log_saved');
            if ($this->_helper->isCompletedOrder($object, $logModel)
            ) {
                $data = $logModel->prepareLogData($object);
                $logModel->setType('New');
                $logModel->setData($data);
                $logModel->save();
            }
        }
        $this->_saveLogDetails($object, $logModel);
    }

    protected function _isMassAction()
    {
        $isMassAction = false;

        $massActions = [
            'massDisable',
            'massEnable',
            'inlineEdit',
        ];

        $action = $this->_registryManager->registry('amaudit_action');

        if (in_array($action, $massActions)) {
            $isMassAction = true;
        }

        return $isMassAction;
    }

    protected function _saveLogDetails($object, $logModel)
    {
        $isConfig = $object instanceof \Magento\Framework\App\Config\Value;
        if ($isConfig) {
            $path = $object->getPath();
            $newData[$path] = $object->getValue();
            $oldData[$path] = $this->_scopeConfig->getValue($path);
        } else {
            $oldData = $object->getOrigData();
            if ($this->_helper->needOldData($object)) {
                $oldDataBeforeSave = $this->_registryManager->registry('amaudit_data_before');
                if (is_array($oldData)) {
                    $oldData = $oldData + $oldDataBeforeSave;
                } else {
                    $oldData = $oldDataBeforeSave;
                }
            }

            $newData = $object->getData();

            if ($object instanceof \Magento\Catalog\Model\Product\Option) {
                $oldData = $this->_prepareOldProductOptionData($newData, $object);
                if (empty($oldData)) {
                    foreach ($newData as $key => $value) {
                        $oldData[$key] = '';
                    }
                }
            }
        }

        $typeLog = $logModel->getType();

        if ($typeLog == 'New' && !$isConfig) {
            foreach ($newData as $key => $value) {
                $this->_saveOneDetail($logModel, $object, $key, '', $newData[$key]);
            }
        }

        if (is_array($oldData)) {
            foreach ($oldData as $key => $value) {
                if ($typeLog == 'New' || (is_array($oldData) && array_key_exists($key, $oldData))) {
                    if ($typeLog != 'New' || $isConfig) {
                        $newKey = $this->_changeNewKey($key, $logModel->getCategory());
                        if (array_key_exists($newKey, $newData)) {
                            $this->_saveOneDetail($logModel, $object, $key, $oldData[$key], $newData[$newKey]);
                        }
                    }
                }
            }
        }
    }

    protected function _prepareOldProductOptionData($newData)
    {
        $options = $this->_registryManager->registry('amaudit_product_options_before');

        $data = [];

        if (isset($newData['id'], $options[$newData['id']])) {
            $data = $options[$newData['id']]->getData();
        }

        return $data;
    }

    protected function _saveOneDetail($logModel, $object, $key, $oldValue, $newValue)
    {
        $saveArrayAsString = [
            'website_ids',
            'store_id',
            'category_ids',
        ];

        $keysNotForLogging = [
            '_cache_instance_product_set_attributes',
            '_cache_editable_attributes',
            'extension_attributes',
            'updated_at',
        ];

        $keyNotForSaving = [
            '0',
        ];

        $keysAlwaysSave = [
            'comment',
        ];

        if (in_array($key, $keysAlwaysSave)) {
            $oldValue = '';
        }

        if($oldValue instanceof \DateTime) {
            $oldValue = $oldValue->format('Y-m-d H:i:s');
        }

        if($newValue instanceof \DateTime) {
            $newValue = $newValue->format('Y-m-d H:i:s');
        }

        if (strpos($key, 'password') !== false) {
            $stars = '*****';
            $newValue = $stars;
            if (!empty($oldValue)) {
                $oldValue = $stars;
            }
        }

        if (in_array($key, $this->_arrayKeysToString, true)) {
            if (is_array($oldValue)) {
                $oldValue = implode(',', $oldValue);
            } else {
                $oldValue = (string)$oldValue;
            }
            if (is_array($newValue)) {
                $newValue = implode(',', $newValue);
            } else {
                $newValue = (string)$newValue;
            }
        }

        if (!in_array($key, $keysNotForLogging) || is_int($key)) {
            if (is_array($oldValue)) {
                if (in_array($key, $saveArrayAsString) && $key !== 0) {
                    if (is_array($newValue)) {
                        $newValue = implode(',', $newValue);
                    }
                    $this->_saveOneDetail($logModel, $object, $key, implode(',', $oldValue), $newValue);
                } else {
                    if (get_class($object) == 'Magento\Downloadable\Model\Link') {
                        unset($oldValue['product']);
                    }
                    foreach ($oldValue as $k => $v) {
                        if (!in_array($k, $keysNotForLogging) || is_int($k)) {
                            if (is_object($v) && is_callable($v, 'getData')) {
                                if (array_key_exists($k, $newValue) && is_callable($newValue[$k], 'getData')) {
                                    $this->_saveOneDetail($logModel, $v, $k, $v->getData(), $newValue[$k]->getData());
                                }
                            } elseif (is_array($newValue)) {
                                if (array_key_exists($k, $newValue)) {
                                    $this->_saveOneDetail($logModel, $object, $k, $v, $newValue[$k]);
                                }
                            } else {
                                $this->_saveOneDetail($logModel, $object, $k, $v, (string) $newValue);
                            }
                        }
                    }
                }
            } elseif (is_object($oldValue) && is_object($newValue) && is_callable($oldValue, 'getData') && is_callable($newValue, 'getData')) {
                $this->_saveOneDetail($logModel, $oldValue, $key, $oldValue->getData(), $newValue->getData());
            } else {
                if ($oldValue != $newValue
                    && $newValue !== false) {
                    $typeLog = $logModel->getType();
                    $logDetailsModel = $this->_objectManager->get('Amasty\AdminActionsLog\Model\LogDetails');

                    if ($typeLog == 'Edit') {
                        $newKey = $this->_changeNewKey($key, $logModel->getCategory());
                    } else {
                        $newKey = $key;
                    }

                    $data = [];
                    $data['log_id'] = $logModel->getId();
                    $data['new_value'] = $this->_prepareNewData($newKey, $newValue);
                    $data['name'] = $key;
                    $data['model'] = get_class($object);
                    $data['old_value'] = $this->_prepareOldData($key, $oldValue);
                    if (($data['old_value'] != $data['new_value']) && !in_array($key, $keyNotForSaving)) {
                        $logDetailsModel->setData($data);
                        $logDetailsModel->save();
                    }
                }
            }
        }

    }

    protected function _isConfig($logModel)
    {
        $isConfig = false;

        if ($logModel->getCategory() == 'admin/system_config')
        {
            $isConfig = true;
        }

        return $isConfig;
    }

    /**
     * Change keys for example store_id in cms pages
     * @param int $key
     * @param \Amasty\AdminActionsLog\Model\Log $category
     * @return int $key
     */
    protected function _changeNewKey($key, $category)
    {
        switch ($key) {
            case 'store_id':
                if ($category == 'cms/page') {
                    $key = 'stores';
                }
                break;
            case 'quantity_and_stock_status':
                $key = 'stock_data';
                break;
        }

        return $key;
    }

    protected function _prepareNewData($key, $value)
    {
        $keyNotForLogging = [
            'media_attributes',
            'media_gallery',
            'options',
            'product_options'
        ];

        if (in_array($key, $keyNotForLogging)) {
            $value = 'not logged now';
        }

        switch ($key) {
            case 'dob':
            case 'custom_theme_from':
            case 'custom_theme_to':
            case 'special_from_date':
            case 'special_to_date':
            case 'news_from_date':
            case 'custom_design_from':
                $value = date('Y-m-d', strtotime($value));
                break;
        }

        if (is_object($value)) {
            $value = get_class($value);
        }
        elseif (is_array($value)) {
            foreach ($value as $k => $v) {
                if (is_object($v)) {
                    $value[$k] = get_class($v);
                }
            }
            $value = $this->_prepareArrayOfValues($value);
        }

        if (is_bool($value)) {
            $value = (int)$value;
        }

        return $value;
    }

    protected function _prepareArrayOfValues($array)
    {
        $value = '';

        foreach ($array as $key => $value) {
            if (is_array($value) || is_object($value)) {
                unset($array[$key]);
            }
        }

        if (is_array($value)) {
            try {
                $value = implode(',', $value);
            } catch (\Exception $e) {
                $value = 'array()';
            }
        }

        return $value;
    }

    protected function _prepareOldData($key, $value)
    {
        switch ($key) {
            case 'qty':
                $value = (int)$value;
                break;
            case 'quantity_and_stock_status':

                break;
        }

        if (is_array($value)) {
            $value = $this->_prepareArrayOfValues($value);
        }

        return $value;
    }
}
