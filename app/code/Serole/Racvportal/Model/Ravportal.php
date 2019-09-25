<?php
namespace Serole\Racvportal\Model;

class Ravportal extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Serole\Racvportal\Model\ResourceModel\Ravportal');
    }
}
?>