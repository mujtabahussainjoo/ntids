<?php
namespace Folio3\MaintenanceMode\Plugin;

use Magento\Framework\App\Helper\Context;

class Session
{
    protected $_adminUser;
    protected $_remoteAddress;
    protected $_date;

    public function __construct(
        Context $context,
        \Magento\Backend\Model\Auth $auth,
        \Magento\Framework\Stdlib\DateTime\DateTime $date
    )
    {
        $this->_adminUser = $auth->getUser();
        $this->_remoteAddress = $context->getRemoteAddress();
        $this->_date = $date;
    }

    public function afterProlong()
    {
        //--- Update LastActivity TIme in User Table
        $extra = $this->_adminUser->getExtra();
        if(isset($extra['configState'])){
            $extra['configState']['last_activity'] = $this->_date->timestamp();
            $extra['configState']['last_active_ip'] = $this->_remoteAddress->getRemoteAddress();
        }

        $this->_adminUser->setExtra($extra)->save();
    }

    public function beforeProcessLogout(){
        $extra = $this->_adminUser->getExtra();
        if(isset($extra['configState'])){
            unset($extra['configState']['last_activity']);
            unset($extra['configState']['last_active_ip']);
        }

        $this->_adminUser->setExtra($extra)->save();
    }
}