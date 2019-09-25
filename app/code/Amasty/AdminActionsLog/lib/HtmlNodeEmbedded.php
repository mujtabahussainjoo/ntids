<?php

namespace Amasty\AdminActionsLog\lib;

/**
 * Node subclass for embedded tags like xml, php and asp
 */
class HtmlNodeEmbedded extends HtmlNode {

    /**
     * @var string
     * @internal specific char for tags, like ? for php and % for asp
     * @access private
     */
    var $tag_char = '';

    /**
     * @var string
     */
    var $text = '';

    /**
     * Class constructor
     * @param HTML_Node $parent
     * @param string $tag_char {@link $tag_char}
     * @param string $tag {@link $tag}
     * @param string $text
     * @param array $attributes array('attr' => 'val')
     */
    function __construct($parent, $tag_char = '', $tag = '', $text = '', $attributes = array()) {
        $this->parent = $parent;
        $this->tag_char = $tag_char;
        if ($tag[0] !== $this->tag_char) {
            $tag = $this->tag_char.$tag;
        }
        $this->tag = $tag;
        $this->text = $text;
        $this->attributes = $attributes;
        $this->self_close_str = $tag_char;
    }

    #php4 PHP4 class constructor compatibility
    #function HtmlNodeEmbedded($parent, $tag_char = '', $tag = '', $text = '', $attributes = array()) {return $this->__construct($parent, $tag_char, $tag, $text, $attributes);}
    #php4e

    protected function filter_element() {return false;}
    function toString($attributes = true, $recursive = true, $content_only = false) {
        $s = '<'.$this->tag;
        if ($attributes) {
            $s .= $this->toString_attributes();
        }
        $s .= $this->text.$this->self_close_str.'>';
        return $s;
    }
}
