<?php

namespace Amasty\AdminActionsLog\lib;

include_once 'TokenizerBase.php';

/**
 * Parses a HTML document
 *
 * Functionality can be extended by overriding functions or adjusting the tag map.
 * Document may contain small errors, the parser will try to recover and resume parsing.
 */
class HtmlParserBase extends TokenizerBase {

    /**
     * Tag open token, used for "<"
     */
    const TOK_TAG_OPEN = 100;
    /**
     * Tag close token, used for ">"
     */
    const TOK_TAG_CLOSE = 101;
    /**
     * Forward slash token, used for "/"
     */
    const TOK_SLASH_FORWARD = 103;
    /**
     * Backslash token, used for "\"
     */
    const TOK_SLASH_BACKWARD = 104;
    /**
     * String token, used for attribute values (" and ')
     */
    const TOK_STRING = 104;
    /**
     * Equals token, used for "="
     */
    const TOK_EQUALS = 105;

    /**
     * Sets HTML identifiers, tags/attributes are considered identifiers
     * @see TokenizerBase::$identifiers
     * @access private
     */
    var $identifiers = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890:-_!?%';

    /**
     * Status of the parser (tagname, closing tag, etc)
     * @var array
     */
    var $status = array();

    /**
     * Map characters to match their tokens
     * @see TokenizerBase::$custom_char_map
     * @access private
     */
    var $custom_char_map = array(
        '<' => self::TOK_TAG_OPEN,
        '>' => self::TOK_TAG_CLOSE,
        "'" => 'parse_string',
        '"' => 'parse_string',
        '/' => self::TOK_SLASH_FORWARD,
        '\\' => self::TOK_SLASH_BACKWARD,
        '=' => self::TOK_EQUALS
    );

    function __construct($doc = '', $pos = 0) {
        parent::__construct($doc, $pos);
        $this->parse_all();
    }

    #php4 PHP4 class constructor compatibility
    #function HtmlParserBase($doc = '', $pos = 0) {return $this->__construct($doc, $pos);}
    #php4e

    /**
    Callback functions for certain tags
    @var array (TAG_NAME => FUNCTION_NAME)
    @internal Function should be a method in the class
    @internal Tagname should be lowercase and is everything after <, e.g. "?php" or "!doctype"
    @access private
     */
    var $tag_map = array(
        '!doctype' => 'parse_doctype',
        '?' => 'parse_php',
        '?php' => 'parse_php',
        '%' => 'parse_asp',
        'style' => 'parse_style',
        'script' => 'parse_script'
    );

    /**
     * Parse a HTML string (attributes)
     * @internal Gets called with ' and "
     * @return int
     */
    protected function parse_string() {
        if ($this->next_pos($this->doc[$this->pos], false) !== self::TOK_UNKNOWN) {
            --$this->pos;
        }
        return self::TOK_STRING;
    }

    /**
     * Parse text between tags
     * @internal Gets called between tags, uses {@link $status}[last_pos]
     * @internal Stores text in {@link $status}[text]
     */
    function parse_text() {
        $len = $this->pos - 1 - $this->status['last_pos'];
        $this->status['text'] = (($len > 0) ? substr($this->doc, $this->status['last_pos'] + 1, $len) : '');
    }

    /**
     * Parse comment tags
     * @internal Gets called with HTML comments ("<!--")
     * @internal Stores text in {@link $status}[comment]
     * @return bool
     */
    function parse_comment() {
        $this->pos += 3;
        if ($this->next_pos('-->', false) !== self::TOK_UNKNOWN) {
            $this->status['comment'] = $this->getTokenString(1, -1);
            --$this->pos;
        } else {
            $this->status['comment'] = $this->getTokenString(1, -1);
            $this->pos += 2;
        }
        $this->status['last_pos'] = $this->pos;

        return true;
    }

    /**
     * Parse doctype tag
     * @internal Gets called with doctype ("<!doctype")
     * @internal Stores text in {@link $status}[dtd]
     * @return bool
     */
    function parse_doctype() {
        $start = $this->pos;
        if ($this->next_search('[>', false) === self::TOK_UNKNOWN)  {
            if ($this->doc[$this->pos] === '[') {
                if (($this->next_pos(']', false) !== self::TOK_UNKNOWN) || ($this->next_pos('>', false) !== self::TOK_UNKNOWN)) {
                    $this->addError('Invalid doctype');
                    return false;
                }
            }

            $this->token_start = $start;
            $this->status['dtd'] = $this->getTokenString(2, -1);
            $this->status['last_pos'] = $this->pos;
            return true;
        } else {
            $this->addError('Invalid doctype');
            return false;
        }
    }

    /**
     * Parse cdata tag
     * @internal Gets called with cdata ("<![cdata")
     * @internal Stores text in {@link $status}[cdata]
     * @return bool
     */
    function parse_cdata() {
        if ($this->next_pos(']]>', false) === self::TOK_UNKNOWN) {
            $this->status['cdata'] = $this->getTokenString(9, -1);
            $this->status['last_pos'] = $this->pos + 2;
            return true;
        } else {
            $this->addError('Invalid cdata tag');
            return false;
        }
    }

    /**
     * Parse php tags
     * @internal Gets called with php tags ("<?php")
     * @return bool
     */
    function parse_php() {
        $start = $this->pos;
        if ($this->next_pos('?>', false) !== self::TOK_UNKNOWN) {
            $this->pos -= 2; //End of file
        }

        $len = $this->pos - 1 - $start;
        $this->status['text'] = (($len > 0) ? substr($this->doc, $start + 1, $len) : '');
        $this->status['last_pos'] = ++$this->pos;
        return true;
    }

    /**
     * Parse asp tags
     * @internal Gets called with asp tags ("<%")
     * @return bool
     */
    function parse_asp() {
        $start = $this->pos;
        if ($this->next_pos('%>', false) !== self::TOK_UNKNOWN) {
            $this->pos -= 2; //End of file
        }

        $len = $this->pos - 1 - $start;
        $this->status['text'] = (($len > 0) ? substr($this->doc, $start + 1, $len) : '');
        $this->status['last_pos'] = ++$this->pos;
        return true;
    }

    /**
     * Parse style tags
     * @internal Gets called with php tags ("<style>")
     * @return bool
     */
    function parse_style() {
        if ($this->parse_attributes() && ($this->token === self::TOK_TAG_CLOSE) && ($start = $this->pos) && ($this->next_pos('</style>', false) === self::TOK_UNKNOWN)) {
            $len = $this->pos - 1 - $start;
            $this->status['text'] = (($len > 0) ? substr($this->doc, $start + 1, $len) : '');

            $this->pos += 7;
            $this->status['last_pos'] = $this->pos;
            return true;
        } else {
            $this->addError('No end for style tag found');
            return false;
        }
    }

    /**
     * Parse script tags
     * @internal Gets called with php tags ("<script>")
     * @return bool
     */
    function parse_script() {
        if ($this->parse_attributes() && ($this->token === self::TOK_TAG_CLOSE) && ($start = $this->pos) && ($this->next_pos('</script>', false) === self::TOK_UNKNOWN)) {
            $len = $this->pos - 1 - $start;
            $this->status['text'] = (($len > 0) ? substr($this->doc, $start + 1, $len) : '');

            $this->pos += 8;
            $this->status['last_pos'] = $this->pos;
            return true;
        } else {
            $this->addError('No end for script tag found');
            return false;
        }
    }

    /**
     * Parse conditional tags (+ all conditional tags inside)
     * @internal Gets called with IE conditionals ("<![if]" and "<!--[if]")
     * @internal Stores condition in {@link $status}[tag_condition]
     * @return bool
     */
    function parse_conditional() {
        if ($this->status['closing_tag']) {
            $this->pos += 8;
        } else {
            $this->pos += (($this->status['comment']) ? 5 : 3);
            if ($this->next_pos(']', false) !== self::TOK_UNKNOWN) {
                $this->addError('"]" not found in conditional tag');
                return false;
            }
            $this->status['tag_condition'] = $this->getTokenString(0, -1);
        }

        if ($this->next_no_whitespace() !== self::TOK_TAG_CLOSE) {
            $this->addError('No ">" tag found 2 for conditional tag');
            return false;
        }

        if ($this->status['comment']) {
            $this->status['last_pos'] = $this->pos;
            if ($this->next_pos('-->', false) !== self::TOK_UNKNOWN) {
                $this->addError('No ending tag found for conditional tag');
                $this->pos = $this->size - 1;

                $len = $this->pos - 1 - $this->status['last_pos'];
                $this->status['text'] = (($len > 0) ? substr($this->doc, $this->status['last_pos'] + 1, $len) : '');
            } else {
                $len = $this->pos - 10 - $this->status['last_pos'];
                $this->status['text'] = (($len > 0) ? substr($this->doc, $this->status['last_pos'] + 1, $len) : '');
                $this->pos += 2;
            }
        }

        $this->status['last_pos'] = $this->pos;
        return true;
    }

    /**
     * Parse attributes (names + value)
     * @internal Stores attributes in {@link $status}[attributes] (array(ATTR => VAL))
     * @return bool
     */
    function parse_attributes() {
        $this->status['attributes'] = array();

        while ($this->next_no_whitespace() === self::TOK_IDENTIFIER) {
            $attr = $this->getTokenString();
            if (($attr === '?') || ($attr === '%')) {
                //Probably closing tags
                break;
            }

            if ($this->next_no_whitespace() === self::TOK_EQUALS) {
                if ($this->next_no_whitespace() === self::TOK_STRING) {
                    $val = $this->getTokenString(1, -1);
                } else {
                    if (!isset($stop)) {
                        $stop = $this->whitespace;
                        $stop['<'] = true;
                        $stop['>'] = true;
                    }

                    while ((++$this->pos < $this->size) && (!isset($stop[$this->doc[$this->pos]]))) {}
                    --$this->pos;
                    $val = $this->getTokenString();

                    if (trim($val) === '') {
                        $this->addError('Invalid attribute value');
                        return false;
                    }
                }
            } else {
                $val = $attr;
                $this->pos = (($this->token_start) ? $this->token_start : $this->pos) - 1;
            }

            $this->status['attributes'][$attr] = $val;
        }

        return true;
    }

    /**
     * Default callback for tags
     * @internal Gets called after the tagname (<html*ENTERS_HERE* attribute="value">)
     * @return bool
     */
    function parse_tag_default() {
        if ($this->status['closing_tag']) {
            $this->status['attributes'] = array();
            $this->next_no_whitespace();
        } else {
            if (!$this->parse_attributes()) {
                return false;
            }
        }

        if ($this->token !== self::TOK_TAG_CLOSE) {
            if ($this->token === self::TOK_SLASH_FORWARD) {
                $this->status['self_close'] = true;
                $this->next();
            } elseif ((($this->status['tag_name'][0] === '?') && ($this->doc[$this->pos] === '?')) || (($this->status['tag_name'][0] === '%') && ($this->doc[$this->pos] === '%'))) {
                $this->status['self_close'] = true;
                $this->pos++;

                if (isset($this->char_map[$this->doc[$this->pos]]) && (!is_string($this->char_map[$this->doc[$this->pos]]))) {
                    $this->token = $this->char_map[$this->doc[$this->pos]];
                } else {
                    $this->token = self::TOK_UNKNOWN;
                }
            }/* else {
				$this->status['self_close'] = false;
			}*/
        }

        if ($this->token !== self::TOK_TAG_CLOSE) {
            $this->addError('Expected ">", but found "'.$this->getTokenString().'"');
            if ($this->next_pos('>', false) !== self::TOK_UNKNOWN) {
                $this->addError('No ">" tag found for "'.$this->status['tag_name'].'" tag');
                return false;
            }
        }

        return true;
    }

    /**
     * Parse tag
     * @internal Gets called after opening tag (<*ENTERS_HERE*html attribute="value">)
     * @internal Stores information about the tag in {@link $status} (comment, closing_tag, tag_name)
     * @return bool
     */
    function parse_tag() {
        $start = $this->pos;
        $this->status['self_close'] = false;
        $this->parse_text();

        $next = (($this->pos + 1) < $this->size) ? $this->doc[$this->pos + 1] : '';
        if ($next === '!') {
            $this->status['closing_tag'] = false;

            if (substr($this->doc, $this->pos + 2, 2) === '--') {
                $this->status['comment'] = true;

                if (($this->doc[$this->pos + 4] === '[') && (strcasecmp(substr($this->doc, $this->pos + 5, 2), 'if') === 0)) {
                    return $this->parse_conditional();
                } else {
                    return $this->parse_comment();
                }
            } else {
                $this->status['comment'] = false;

                if ($this->doc[$this->pos + 2] === '[') {
                    if (strcasecmp(substr($this->doc, $this->pos + 3, 2), 'if') === 0) {
                        return $this->parse_conditional();
                    } elseif (strcasecmp(substr($this->doc, $this->pos + 3, 5), 'endif') === 0) {
                        $this->status['closing_tag'] = true;
                        return $this->parse_conditional();
                    } elseif (strcasecmp(substr($this->doc, $this->pos + 3, 5), 'cdata') === 0) {
                        return $this->parse_cdata();
                    }
                }
            }
        } elseif ($next === '/') {
            $this->status['closing_tag'] = true;
            ++$this->pos;
        } else {
            $this->status['closing_tag'] = false;
        }

        if ($this->next() !== self::TOK_IDENTIFIER) {
            $this->addError('Tagname expected');
            //if ($this->next_pos('>', false) === self::TOK_UNKNOWN) {
            $this->status['last_pos'] = $start - 1;
            return true;
            //} else {
            //	return false;
            //}
        }

        $tag = $this->getTokenString();
        $this->status['tag_name'] = $tag;
        $tag = strtolower($tag);

        if (isset($this->tag_map[$tag])) {
            $res = $this->{$this->tag_map[$tag]}();
        } else {
            $res = $this->parse_tag_default();
        }

        $this->status['last_pos'] = $this->pos;
        return $res;
    }

    /**
     * Parse full document
     * @return bool
     */
    function parse_all() {
        $this->errors = array();
        $this->status['last_pos'] = -1;

        if (($this->token === self::TOK_TAG_OPEN) || ($this->next_pos('<', false) === self::TOK_UNKNOWN)) {
            do {
                if (!$this->parse_tag()) {
                    return false;
                }
            } while ($this->next_pos('<') !== self::TOK_NULL);
        }

        $this->pos = $this->size;
        $this->parse_text();

        return true;
    }
}
