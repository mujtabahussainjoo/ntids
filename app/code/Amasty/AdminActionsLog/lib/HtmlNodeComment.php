<?php

namespace Amasty\AdminActionsLog\lib;

/**
 * Node subclass for comments
 */
class HtmlNodeComment extends HtmlNode {
    #php4 Compatibility with PHP4, this gets changed to a regular var in release tool
    #static $NODE_TYPE = self::NODE_COMMENT;
    #php4e
    #php5
    const NODE_TYPE = self::NODE_COMMENT;
    #php5e
    var $tag = '~comment~';

    /**
     * @var string
     */
    var $text = '';

    /**
     * Class constructor
     * @param HTML_Node $parent
     * @param string $text
     */
    function __construct($parent, $text = '') {
        $this->parent = $parent;
        $this->text = $text;
    }

    #php4 PHP4 class constructor compatibility
    #function HtmlNodeComment($parent, $text = '') {return $this->__construct($parent, $text);}
    #php4e

    function isComment() {return true;}
    function isTextOrComment() {return true;}
    protected function filter_element() {return false;}
    protected function filter_comment() {return true;}
    function toString_attributes() {return '';}
    function toString_content($attributes = true, $recursive = true, $content_only = false) {return $this->text;}
    function toString($attributes = true, $recursive = true, $content_only = false) {return '<!--'.$this->text.'-->';}
}
