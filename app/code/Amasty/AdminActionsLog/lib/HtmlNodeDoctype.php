<?php

namespace Amasty\AdminActionsLog\lib;

/**
 * Node subclass for doctype tags
 */
class HtmlNodeDoctype extends HtmlNode {
    #php4 Compatibility with PHP4, this gets changed to a regular var in release tool
    #static $NODE_TYPE = self::NODE_DOCTYPE;
    #php4e
    #php5
    const NODE_TYPE = self::NODE_DOCTYPE;
    #php5e
    var $tag = '!DOCTYPE';

    /**
     * @var string
     */
    var $dtd = '';

    /**
     * Class constructor
     * @param HTML_Node $parent
     * @param string $dtd
     */
    function __construct($parent, $dtd = '') {
        $this->parent = $parent;
        $this->dtd = $dtd;
    }

    #php4 PHP4 class constructor compatibility
    #function HtmlNodeDoctype($parent, $dtd = '') {return $this->__construct($parent, $dtd);}
    #php4e

    protected function filter_element() {return false;}
    function toString_attributes() {return '';}
    function toString_content($attributes = true, $recursive = true, $content_only = false) {return $this->text;}
    function toString($attributes = true, $recursive = true, $content_only = false) {return '<'.$this->tag.' '.$this->dtd.'>';}
}
