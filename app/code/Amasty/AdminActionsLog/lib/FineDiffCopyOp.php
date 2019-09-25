<?php

namespace Amasty\AdminActionsLog\lib;

include_once 'FineDiffOp.php';

class FineDiffCopyOp extends FineDiffOp {

    protected $len;

    public function __construct($len) {
        $this->len = $len;
    }
    public function getFromLen() {
        return $this->len;
    }
    public function getToLen() {
        return $this->len;
    }
    public function getOpcode() {
        if ( $this->len === 1 ) {
            return 'c';
        }
        return "c{$this->len}";
    }
    public function increase($size) {
        return $this->len += $size;
    }
}
