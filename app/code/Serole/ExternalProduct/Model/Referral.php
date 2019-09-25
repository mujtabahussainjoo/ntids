<?php
namespace Serole\ExternalProduct\Model;

class Referral extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Serole\ExternalProduct\Model\ResourceModel\Referral');
    }
}
?>