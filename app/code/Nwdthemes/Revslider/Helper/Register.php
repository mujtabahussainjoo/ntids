<?php

namespace Nwdthemes\Revslider\Helper;

class Register extends \Magento\Framework\App\Helper\AbstractHelper {

    protected $_registry;

    /**
     *	Constructor
     */

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Registry $registry
    ) {
        $this->_registry = $registry;

        parent::__construct($context);
    }

    /**
     *	Add action
     *
     *	@param	string	$handle
     *	@param	array	$action
     */

    public function addAction($handle, $action) {
        $this->addToRegister('actions', $handle, $action);
    }

    /**
     *	Remove action
     *
     *	@param	string	$handle
     *	@param	array	$action
     */

    function removeAction($handle, $action) {
        $actions = $this->getFromRegister('actions', $handle);
        foreach ($actions as $_key => $_action) {
            if ($_action == $action) {
                unset($actions[$_key]);
                $this->setToRegister('actions', $handle, $actions);
                continue;
            }
        }
    }

    /**
     *	Do action
     *
     *	@param	string	$handle
     *	@param	mixed	$args
     *	@return string
     */

    public function doAction($handle, $args) {

        ob_start();
        $return = false;
        foreach ($this->getFromRegister('actions', $handle) as $action) {
            $_return = call_user_func_array($action, $args);
            if ($_return) {
                $args[1] = $_return;
                $return = $_return;
            }
        }
        $output = ob_get_contents();
        ob_end_clean();

        $output = str_replace(' for WordPress', '', $output);
        if ($args != 'action_no_output') {
            echo $output;
        }

        return $output ? $output : $return;
    }

    /**
     * Check if action added
     *
     * @param	string	$handle
     * @param	array	$action
     * @return  boolean
     */
    public function hasAction($handle, $action) {
        $actions = $this->getFromRegister('actions', $handle);
        $hasAction = isset($actions[$handle]) && is_array($actions[$handle]) && in_array($action, $actions[$handle]);
        return $hasAction;
    }

    /**
     *  Adds handle data to register by key
     *
     *  @param  string  $key
     *  @param  string  $handle
     *  @param  mixed   $data
     */

    public function addToRegister($key, $handle, $data) {
        $item = $this->getFromRegister($key, $handle);
        $item[] = $data;
        $this->setToRegister($key, $handle, $item);
    }

    /**
     *  Sets handle data to register by key
     *
     *  @param  string  $key
     *  @param  string  $handle
     *  @param  mixed   $data
     */

    public function setToRegister($key, $handle, $data) {
        $registerKey = 'nwdrevslider_' . $key;
        $register = $this->getFromRegister($key);
        $register[$handle] = $data;
        $this->_registry->unregister($registerKey);
        $this->_registry->register($registerKey, $register);
    }

    /**
     *  Get data from register
     *
     *  @param  string  $key
     *  @param  string|boolean  $handle
     *  @param  mixed   $default
     *  @return mixed
     */

    public function getFromRegister($key, $handle = false, $default = array()) {
        $registerKey = 'nwdrevslider_' . $key;
        $register = $this->_registry->registry($registerKey) ?  $this->_registry->registry($registerKey) : array();
        return $handle !== false
            ? (isset($register[$handle]) ? $register[$handle] : $default)
            : $register;
    }

}