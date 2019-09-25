<?php

namespace Amasty\AdminActionsLog\lib;

class FineDiffDeleteOp extends FineDiffOp {

    protected $fromLen;

    public function __construct($len) {
        $this->fromLen = $len;
    }
    public function getFromLen() {
        return $this->fromLen;
    }
    public function getToLen() {
        return 0;
    }
    public function getOpcode() {
        if ( $this->fromLen === 1 ) {
            return 'd';
        }
        return "d{$this->fromLen}";
    }
}
