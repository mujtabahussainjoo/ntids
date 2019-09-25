<?php

namespace Amasty\AdminActionsLog\lib;

/**
 * FineDiff ops
 *
 * Collection of ops
 */
class FineDiffOps {
    public function appendOpcode($opcode, $from, $from_offset, $from_len) {
        if ( $opcode === 'c' ) {
            $edits[] = new FineDiffCopyOp($from_len);
        }
        else if ( $opcode === 'd' ) {
            $edits[] = new FineDiffDeleteOp($from_len);
        }
        else /* if ( $opcode === 'i' ) */ {
            $edits[] = new FineDiffInsertOp(substr($from, $from_offset, $from_len));
        }
    }
    public $edits = array();
}
