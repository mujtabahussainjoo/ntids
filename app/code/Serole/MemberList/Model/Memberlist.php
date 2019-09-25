<?php
namespace Serole\MemberList\Model;

class Memberlist extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Serole\MemberList\Model\ResourceModel\Memberlist');
    }
}
?>