<?php

namespace Nwdthemes\Revslider\Helper;

class Options extends \Magento\Framework\App\Helper\AbstractHelper {

    protected $_optionFactory;

    /**
     *	Constructor
     */

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Nwdthemes\Revslider\Model\OptionFactory $optionFactory
    ) {
        $this->_optionFactory = $optionFactory;

        parent::__construct($context);
    }


    /**
     *	Get option
     *
     *	@param	string	Handle
     *	@param	string	Default value
     *	@return	string	Option value
     */

    public function getOption($handle, $default = false) {
        $collection = $this->_optionFactory->create()
            ->getCollection()
            ->addFieldToFilter('handle', $handle)
            ->setPageSize(1);
        if ($collection->count()) {
            $option = $collection->getFirstItem();
            $value = $option->getData('option');
            if ((strpos($value, 'a:') !== false
                    || strpos($value, 's:') !== false
                    || strpos($value, 'O:') !== false
                    || strpos($value, 'i:') !== false
                    || strpos($value, 'b:') !== false)
                && (($unserializedValue = @unserialize($value)) !== false || $value == 'b:0;')) {
                $value = $unserializedValue;
            } elseif ((strpos($value, 'a:') !== false
                    || strpos($value, 's:') !== false
                    || strpos($value, 'O:') !== false
                    || strpos($value, 'i:') !== false
                    || strpos($value, 'b:') !== false)
                && ($unserializedValue = @unserialize($this->_fixSerializedString($value))) !== false) {
                // save fixed serialized string
                $this->updateOption($handle, $unserializedValue);
                $value = $unserializedValue;
            }
            return $value;
        } else {
            return $default;
        }
    }

    /**
     * Update option
     *
     * @param string $handle
     * @param string value
     */

    public function updateOption($handle, $value = '') {
        $collection = $this->_optionFactory
            ->create()
            ->getCollection()
            ->addFieldToFilter('handle', $handle)
            ->setPageSize(1);
        if ($collection->count()) {
            $option = $collection->getFirstItem();
        } else {
            $option = $this->_optionFactory
                ->create()
                ->setData('handle', $handle);
        }
        $option
            ->setData('option', is_string($value) ? $value : serialize($value))
            ->save();
    }

    /**
     * Delete option
     *
     * @param string $handle
     */

    public function deleteOption($handle) {
        $collection = $this->_optionFactory
            ->create()
            ->getCollection()
            ->addFieldToFilter('handle', $handle)
            ->setPageSize(1);
        if ($collection->count()) {
            $collection
                ->getFirstItem()
                ->delete();
        }
    }

    /**
     * Fix strings length in serialized string
     *
     * @param string $serializedString
     * @return string
     */
    protected function _fixSerializedString($serializedString) {
        $fixedString = preg_replace_callback(
            '/s:([0-9]+):\"(.*?)\";/',
            function ($matches) {
                return "s:".strlen($matches[2]).':"'.$matches[2].'";';
            },
            $serializedString
        );
        return $fixedString;
    }

}