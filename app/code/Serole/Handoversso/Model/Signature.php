<?php
namespace Serole\Handoversso\Model;

class Signature extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Serole\Handoversso\Model\ResourceModel\Signature');
    }
}
?>