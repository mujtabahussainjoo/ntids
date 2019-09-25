<?php

namespace Amasty\AdminActionsLog\lib;

/**
 * Node subclass for "?" tags, like php and xml
 */
class HtmlNodeXML extends HtmlNodeEmbedded {
    #php4 Compatibility with PHP4, this gets changed to a regular var in release tool
    #static $NODE_TYPE = self::NODE_XML;
    #php4e
    #php5
    const NODE_TYPE = self::NODE_XML;
    #php5e

    /**
     * Class constructor
     * @param HTML_Node $parent
     * @param string $tag {@link $tag}
     * @param string $text
     * @param array $attributes array('attr' => 'val')
     */
    function __construct($parent, $tag = 'xml', $text = '', $attributes = array()) {
        return parent::__construct($parent, '?', $tag, $text, $attributes);
    }

    #php4 PHP4 class constructor compatibility
    #function HtmlNodeXML($parent, $tag = 'xml', $text = '', $attributes = array()) {return $this->__construct($parent, $tag, $text, $attributes);}
    #php4e
}
