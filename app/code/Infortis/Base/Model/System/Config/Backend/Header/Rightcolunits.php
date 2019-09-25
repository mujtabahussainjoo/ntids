<?php

namespace Infortis\Base\Model\System\Config\Backend\Header;

use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Value;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;

class Rightcolunits extends Value
{
    /**
     * @var ManagerInterface
     */
    protected $_messageManagerInterface;

    public function __construct(
        Context $context, 
        Registry $registry, 
        ScopeConfigInterface $config, 
        TypeListInterface $cacheTypeList,
        ManagerInterface $messageManagerInterface,
        AbstractResource $resource = null, 
        AbstractDb $resourceCollection = null, 
        array $data = []
    ) {
        $this->_messageManagerInterface = $messageManagerInterface;

        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }
    
    public function afterSave()
    {
        //Get the saved value
        $value = $this->getValue();
        
        //Get the value from config (previous value)
        $oldValue = $this->getOldValue();
        
        if ($value != $oldValue)
        {
            if (empty($value) || trim($value) === '')
            {
                $this->_messageManagerInterface->addNotice(
                    __('Right Column in the header has been disabled and will not be displayed in the header. IMPORTANT: note that any blocks assigned to the Right Column will also not be displayed.')
                );
            }
            else
            {
                $this->_messageManagerInterface->addNotice(
                    __('Width of the Right Column in the header has changed (previous value: %1). Note that sum of these columns has to be equal 12 grid units.', $oldValue)
                );
            }
        }
        
        return parent::afterSave();
    }
}
