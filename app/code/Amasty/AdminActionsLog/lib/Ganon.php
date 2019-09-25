<?php
/**
 * @author Niels A.D.
 * @package Ganon
 * @link http://code.google.com/p/ganon/
 * @license http://dev.perl.org/licenses/artistic.html Artistic License
 */

namespace Amasty\AdminActionsLog\lib;

include_once 'HtmlParserHTML5.php';
include_once 'HTMLFormatter.php';

class Ganon
{
    /**
     * Returns HTML DOM from string
     * @param string $str
     * @param bool $return_root Return root node or return parser object
     * @return HtmlParserHTML5|HtmlNode
     */
    static function str_get_dom($str, $return_root = true) {
        $a = new HtmlParserHTML5($str);
        return (($return_root) ? $a->root : $a);
    }

    /**
     * Returns HTML DOM from file/website
     * @param string $str
     * @param bool $return_root Return root node or return parser object
     * @param bool $use_include_path Use include path search in file_get_contents
     * @param resource $context Context resource used in file_get_contents (PHP >= 5.0.0)
     * @return HtmlParserHTML5|HtmlNode
     */
    static function file_get_dom($file, $return_root = true, $use_include_path = false, $context = null) {
        if (version_compare(PHP_VERSION, '5.0.0', '>='))
            $f = file_get_contents($file, $use_include_path, $context);
        else {
            if ($context !== null)
                trigger_error('Context parameter not supported in this PHP version');
            $f = file_get_contents($file, $use_include_path);
        }

        return (($f === false) ? false : self::str_get_dom($f, $return_root));
    }

    /**
     * Format/beautify DOM
     * @param HtmlNode $root
     * @param array $options Extra formatting options {@link HTMLFormatter::$options}
     * @return bool
     */
    static function dom_format(&$root, $options = array()) {
        $formatter = new HTMLFormatter($options);
        return $formatter->format($root);
    }
}

