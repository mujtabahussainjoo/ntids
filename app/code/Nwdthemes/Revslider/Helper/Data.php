<?php

namespace Nwdthemes\Revslider\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {

    protected static $logger;
    protected static $loggerQueue = array();
    protected $_systemStore;

    public static $_GET = array();
    public static $_REQUEST = array();

	/**
	 *	Constructor
	 */

	public function __construct(
        \Magento\Framework\App\Helper\Context $context,
		\Magento\Store\Model\System\Store $systemStore
	) {
		$this->_systemStore = $systemStore;

        parent::__construct($context);

        self::$logger = $context->getLogger();

        if (self::$loggerQueue) {
            foreach (self::$loggerQueue as $logMessage)
                self::$logger->info($logMessage);
            self::$loggerQueue = array();
        }

		$requestParams = $context->getRequest()->getParams();

        self::$_GET = array_merge(self::$_GET, $requestParams);
        self::$_REQUEST = array_merge(self::$_REQUEST, $requestParams);
	}

    /**
     *  Set page for get imitation
     *
     *  @param  string  $page
     */

    public static function setPage($page) {
        self::$_GET['page'] = $page;
    }

    /**
     *  Set page for get imitation
     *
     *  @param  string  $view
     */

    public static function setView($view) {
        self::$_GET['view'] = $view;
    }

    /**
	 * Get store options for multiselect
	 *
	 * @return array Array of store options
	 */

	public function getStoreOptions() {
		$storeValues = $this->_systemStore->getStoreValuesForForm(false, true);
		$storeValues = $this->_makeFlatStoreOptions($storeValues);
		return $storeValues;
	}

	/**
	 * Make flat store options
	 *
	 * @param array $storeValues Store values tree array
	 * @retrun array Flat store values array
	 */

	private function _makeFlatStoreOptions($storeValues) {
		$arrStoreValues = array();
		foreach ($storeValues as $_storeValue) {
			if ( ! is_array($_storeValue['value']) ) {
				$arrStoreValues[] = $_storeValue;
			} else {
				$arrStoreValues[] = array(
					'label'	=> $_storeValue['label'],
					'value' => 'option_disabled'
				);
				$_arrSubStoreValues = $this->_makeFlatStoreOptions($_storeValue['value']);
				foreach ($_arrSubStoreValues as $_subStoreValue) {
					$arrStoreValues[] = $_subStoreValue;
				}
			}
		}
		return $arrStoreValues;
	}

    /**
     * Log variable
     *
     * @param mixed $var
     */
    public static function log($var) {
        $log = [];
        foreach (func_get_args() as $arg)
            $log[] = is_string($arg)
                ? $arg
                : (is_bool($arg)
                    ? var_export($arg, true)
                    : (is_object($arg)
                        ? 'class ' . get_class($arg)
                        : print_r($arg, true)));
        $logMessage = '[NWD::Revslider] ' . implode(', ', $log);
        if (self::$logger) {
            self::$logger->info($logMessage);
        } else {
            self::$loggerQueue[] = $logMessage;
        }
    }

    /**
     * Log exception details to nwd_revslider.log
     *
     * @param \Exception $e
     */
    public static function logException($e) {
        $trace = [];
        foreach ($e->getTrace() as $data) if (isset($data['file']))
            $trace[] = $data['file'].':'.$data['line'];
        self::log('Revolution Slider Exception: ' . $e->getMessage() . ' in ' .  $e->getFile() . ' on line ' . $e->getLine(), $trace);
    }
}
