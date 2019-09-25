<?php
namespace Folio3\MaintenanceMode\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;

class Data extends AbstractHelper {
    const USER_CONFIG_STATE_LABEL = 'configState';
    const USER_CONFIG_STATE_LAST_ACTIVITY_LABEL = 'last_activity';
    const USER_CONFIG_STATE_LAST_ACTIVE_IP_LABEL = 'last_active_ip';

    protected $_layout;
    protected $_objectManager;
    protected $_assetRepository;
    protected $_remoteAddress;
    protected $_version;
    protected $_date;

    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        LayoutInterface $layout,
        ProductMetadataInterface $productMetadataInterface,
        DateTime $date
    ){
        $this->_version = $productMetadataInterface->getVersion();
        $this->_layout = $layout;
        $this->_remoteAddress = $context->getRemoteAddress();
        $this->_objectManager = $objectManager;
        $this->_date = $date;

        parent::__construct($context);
    }

    /**
     * Check if Maintenance Mode is applicable
     *
     * @return boolean
     */
    public function isMaintenanceMode(){
        if($this->isEnabled()){
            $allowedIPs = $this->getAllowedIPs();
            $currentIP = $this->_remoteAddress->getRemoteAddress();

            $adminIp = null;
            $adminSessions = array();

            if ($this->getAdminAccess()) {
                $adminSessions = $this->getActiveAdminSessions();
            }

            if(!in_array($currentIP, $adminSessions)) {
                if (!in_array($currentIP, $allowedIPs)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Get the config specific to Maintenance Mode extension
     *
     * @return string
     */
    public function getConfig($xPath){
        $value = $this->scopeConfig->getValue(
            $xPath,
            ScopeInterface::SCOPE_STORE
        );

        return $value;
    }

    /**
     * Check if Maintenance Mode is enabled
     *
     * @return boolean
     */
    public function isEnabled() {
        $isEnabled = $this->getConfig('MaintenanceMode/Configuration/isEnabled');
        return $isEnabled;
    }

    /**
     * Get the allowed IPs
     *
     * @return array
     */
    public function getAllowedIPs() {
        $allowedIPs = $this->getConfig('MaintenanceMode/Configuration/allowedIPs');

        // remove spaces from string
        $allowedIPs = trim(preg_replace('/ +/', ',', preg_replace('/[\n\r]/', ' ', $allowedIPs)), ' ,');
        if ('' !== trim($allowedIPs)) {
            $allowedIPs = explode(',', $allowedIPs);
            return $allowedIPs;
        }

        return array();
    }

    /**
     * Check if the admin has access to the locked store
     *
     * @return string
     */
    public function getAdminAccess(){
        $adminAccess = $this->getConfig('MaintenanceMode/Configuration/adminAccess');
        return $adminAccess;
    }

    /**
     * Check is the Countdown is enabled on the Maintenance Mode block
     *
     * @return boolean
     */
    public function hasCountdown(){
        $hasCountdown = $this->getConfig('MaintenanceMode/Configuration/showCountdown');
        if($hasCountdown) {
            if ($this->getCountDownTime() !== '') {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the time remaining for the website to go live again
     *
     * @return string
     */
    public function getCountDownTime(){
        $upDateTime = $this->getConfig('MaintenanceMode/Configuration/upDateTime');

        if($upDateTime !== ''){
            $upDateTime = strtotime($upDateTime);
            $Now = $this->_date->timestamp();

            $Diff = $upDateTime - $Now;
            if($Diff > 0){
                return $Diff;
            }
        }

        return '';
    }

    /**
     * Get the admin user's active sessions
     *
     * @return array
     */
    protected function getActiveAdminSessions(){
        $activeIPs = array();
        $maxlifetime = ini_get("session.gc_maxlifetime");

        $users = $this->_objectManager->get('Magento\User\Model\User')->getCollection();
        foreach($users as $user){
            $now = $this->_date->timestamp();

            if(!is_null($user->getExtra())){
                if($this->isJSON($user->getExtra())){
                    $extra = json_decode($user->getExtra(), true);
                }
                else{
                    $extra = unserialize($user->getExtra());
                }

                if(is_array($extra) && isset($extra[self::USER_CONFIG_STATE_LABEL][self::USER_CONFIG_STATE_LAST_ACTIVITY_LABEL])){
                    $last_activity = $extra[self::USER_CONFIG_STATE_LABEL][self::USER_CONFIG_STATE_LAST_ACTIVITY_LABEL];
                    if(($now - $last_activity) <= $maxlifetime){
                        $last_active_ip = $extra[self::USER_CONFIG_STATE_LABEL][self::USER_CONFIG_STATE_LAST_ACTIVE_IP_LABEL];
                        array_push($activeIPs, $last_active_ip);
                    }
                }
            }
        }

        return $activeIPs;
    }

    /**
     * Check if the string is JSON
     *
     * @return bool
     */
    protected function isJSON($str){
        json_decode($str);

        return (json_last_error() == JSON_ERROR_NONE);
    }

    /**
     * Create a block for Maintenance Mode and set template
     *
     * @return \Folio3\MaintenanceMode\Block\Page
     */
    public function getMaintenancePageBlock(){
        $block = $this->_layout
            ->createBlock('\Folio3\MaintenanceMode\Block\Page')
            ->setTemplate('Folio3_MaintenanceMode::/maintenanceMode/page.phtml')
            ->setName('maintenance.page.block');

        return $block;
    }
}