<?php
namespace Serole\Racparkpasses\Model;

class Racparkpass extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Serole\Racparkpasses\Model\ResourceModel\Racparkpass');
    }
}
?>