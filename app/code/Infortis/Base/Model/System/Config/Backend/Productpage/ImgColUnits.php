<?php
/**
 * @deprecated
 */

namespace Infortis\Base\Model\System\Config\Backend\Productpage;

use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Value;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;

class ImgColUnits extends Value
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
            $this->_messageManagerInterface->addNotice(
                __('"Image Column Width" has changed (previous value: %1). Adjust the "Main Image Width" value in System > Configuration > Zoom > Image Size', $oldValue)
            );
        }
        
        return parent::afterSave();
    }
}
