<?php

namespace Nwdthemes\Revslider\Controller\Adminhtml;

use \Nwdthemes\Revslider\Model\Revslider\Framework\RevSliderBase;

abstract class Revslider extends \Magento\Backend\App\Action {

    public function __construct(
        \Magento\Backend\App\Action\Context $context
    ) {
        parent::__construct($context);
        $this->_wp_magic_quotes();
    }

    /**
     *  Add magic quotes for WP compatiblity
     */

    private function _wp_magic_quotes() {
        // If already slashed, strip.
        if ( get_magic_quotes_gpc() ) {
            $_GET    = RevSliderBase::stripslashes_deep( $_GET    );
            $_POST   = RevSliderBase::stripslashes_deep( $_POST   );
            $_COOKIE = RevSliderBase::stripslashes_deep( $_COOKIE );
        }

        // Escape with wpdb.
        $_GET    = $this->_add_magic_quotes( $_GET    );
        $_POST   = $this->_add_magic_quotes( $_POST   );
        $_COOKIE = $this->_add_magic_quotes( $_COOKIE );
        $_SERVER = $this->_add_magic_quotes( $_SERVER );

        // Force REQUEST to be GET + POST.
        $_REQUEST = array_merge( $_GET, $_POST );
    }

    /**
     * Walks the array while sanitizing the contents.
     *
     * @param array $array Array to walk while sanitizing contents.
     * @return array Sanitized $array.
     */

    private function _add_magic_quotes( $array ) {
        foreach ( (array) $array as $k => $v ) {
            if ( is_array( $v ) ) {
                $array[$k] = $this->_add_magic_quotes( $v );
            } elseif (is_string($v)) {
                $array[$k] = addslashes( $v );
            }
        }
        return $array;
    }

}
