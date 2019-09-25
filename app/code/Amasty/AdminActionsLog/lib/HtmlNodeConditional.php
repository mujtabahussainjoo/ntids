<?php

namespace Amasty\AdminActionsLog\lib;

/**
 * Node subclass for conditional tags
 */
class HtmlNodeConditional extends HtmlNode {
    #php4 Compatibility with PHP4, this gets changed to a regular var in release tool
    #static $NODE_TYPE = self::NODE_CONDITIONAL;
    #php4e
    #php5
    const NODE_TYPE = self::NODE_CONDITIONAL;
    #php5e
    var $tag = '~conditional~';

    /**
     * @var string
     */
    var $condition = '';

    /**
     * Class constructor
     * @param HTML_Node $parent
     * @param string $condition e.g. "if IE"
     * @param bool $hidden <!--[if if true, <![if if false
     */
    function __construct($parent, $condition = '', $hidden = true) {
        $this->parent = $parent;
        $this->hidden = $hidden;
        $this->condition = $condition;
    }

    #php4 PHP4 class constructor compatibility
    #function HtmlNodeConditional($parent, $condition = '', $hidden = true) {return $this->__construct($parent, $condition, $hidden);}
    #php4e

    protected function filter_element() {return false;}
    function toString_attributes() {return '';}
    function toString($attributes = true, $recursive = true, $content_only = false) {
        if ($content_only) {
            if (is_int($content_only)) {
                --$content_only;
            }
            return $this->toString_content($attributes, $recursive, $content_only);
        }

        $s = '<!'.(($this->hidden) ? '--' : '').'['.$this->condition.']>';
        if($recursive) {
            $s .= $this->toString_content($attributes);
        }
        $s .= '<![endif]'.(($this->hidden) ? '--' : '').'>';
        return $s;
    }
}
