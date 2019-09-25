<?php

namespace Amasty\AdminActionsLog\lib;

/**
 * Tokenizes a css selector query
 */
class TokenizerCSSQuery extends TokenizerBase {

    /**
     * Opening bracket token, used for "["
     */
    const TOK_BRACKET_OPEN = 100;
    /**
     * Closing bracket token, used for "]"
     */
    const TOK_BRACKET_CLOSE = 101;
    /**
     * Opening brace token, used for "("
     */
    const TOK_BRACE_OPEN = 102;
    /**
     * Closing brace token, used for ")"
     */
    const TOK_BRACE_CLOSE = 103;
    /**
     * String token
     */
    const TOK_STRING = 104;
    /**
     * Colon token, used for ":"
     */
    const TOK_COLON = 105;
    /**
     * Comma token, used for ","
     */
    const TOK_COMMA = 106;
    /**
     * "Not" token, used for "!"
     */
    const TOK_NOT = 107;

    /**
     * "All" token, used for "*" in query
     */
    const TOK_ALL = 108;
    /**
     * Pipe token, used for "|"
     */
    const TOK_PIPE = 109;
    /**
     * Plus token, used for "+"
     */
    const TOK_PLUS = 110;
    /**
     * "Sibling" token, used for "~" in query
     */
    const TOK_SIBLING = 111;
    /**
     * Class token, used for "." in query
     */
    const TOK_CLASS = 112;
    /**
     * ID token, used for "#" in query
     */
    const TOK_ID = 113;
    /**
     * Child token, used for ">" in query
     */
    const TOK_CHILD = 114;

    /**
     * Attribute compare prefix token, used for "|="
     */
    const TOK_COMPARE_PREFIX = 115;
    /**
     * Attribute contains token, used for "*="
     */
    const TOK_COMPARE_CONTAINS = 116;
    /**
     * Attribute contains word token, used for "~="
     */
    const TOK_COMPARE_CONTAINS_WORD = 117;
    /**
     * Attribute compare end token, used for "$="
     */
    const TOK_COMPARE_ENDS = 118;
    /**
     * Attribute equals token, used for "="
     */
    const TOK_COMPARE_EQUALS = 119;
    /**
     * Attribute not equal token, used for "!="
     */
    const TOK_COMPARE_NOT_EQUAL = 120;
    /**
     * Attribute compare bigger than token, used for ">="
     */
    const TOK_COMPARE_BIGGER_THAN = 121;
    /**
     * Attribute compare smaller than token, used for "<="
     */
    const TOK_COMPARE_SMALLER_THAN = 122;
    /**
     * Attribute compare with regex, used for "%="
     */
    const TOK_COMPARE_REGEX = 123;
    /**
     * Attribute compare start token, used for "^="
     */
    const TOK_COMPARE_STARTS = 124;

    /**
     * Sets query identifiers
     * @see Tokenizer_Base::$identifiers
     * @access private
     */
    var $identifiers = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ01234567890_-?';

    /**
     * Map characters to match their tokens
     * @see Tokenizer_Base::$custom_char_map
     * @access private
     */
    var $custom_char_map = array(
        '.' => self::TOK_CLASS,
        '#' => self::TOK_ID,
        ',' => self::TOK_COMMA,
        '>' => 'parse_gt',//self::TOK_CHILD,

        '+' => self::TOK_PLUS,
        '~' => 'parse_sibling',

        '|' => 'parse_pipe',
        '*' => 'parse_star',
        '$' => 'parse_compare',
        '=' => self::TOK_COMPARE_EQUALS,
        '!' => 'parse_not',
        '%' => 'parse_compare',
        '^' => 'parse_compare',
        '<' => 'parse_compare',

        '"' => 'parse_string',
        "'" => 'parse_string',
        '(' => self::TOK_BRACE_OPEN,
        ')' => self::TOK_BRACE_CLOSE,
        '[' => self::TOK_BRACKET_OPEN,
        ']' => self::TOK_BRACKET_CLOSE,
        ':' => self::TOK_COLON
    );

    /**
     * Parse ">" character
     * @internal Could be {@link TOK_CHILD} or {@link TOK_COMPARE_BIGGER_THAN}
     * @return int
     */
    protected function parse_gt() {
        if ((($this->pos + 1) < $this->size) && ($this->doc[$this->pos + 1] === '=')) {
            ++$this->pos;
            return ($this->token = self::TOK_COMPARE_BIGGER_THAN);
        } else {
            return ($this->token = self::TOK_CHILD);
        }
    }

    /**
     * Parse "~" character
     * @internal Could be {@link TOK_SIBLING} or {@link TOK_COMPARE_CONTAINS_WORD}
     * @return int
     */
    protected function parse_sibling() {
        if ((($this->pos + 1) < $this->size) && ($this->doc[$this->pos + 1] === '=')) {
            ++$this->pos;
            return ($this->token = self::TOK_COMPARE_CONTAINS_WORD);
        } else {
            return ($this->token = self::TOK_SIBLING);
        }
    }

    /**
     * Parse "|" character
     * @internal Could be {@link TOK_PIPE} or {@link TOK_COMPARE_PREFIX}
     * @return int
     */
    protected function parse_pipe() {
        if ((($this->pos + 1) < $this->size) && ($this->doc[$this->pos + 1] === '=')) {
            ++$this->pos;
            return ($this->token = self::TOK_COMPARE_PREFIX);
        } else {
            return ($this->token = self::TOK_PIPE);
        }
    }

    /**
     * Parse "*" character
     * @internal Could be {@link TOK_ALL} or {@link TOK_COMPARE_CONTAINS}
     * @return int
     */
    protected function parse_star() {
        if ((($this->pos + 1) < $this->size) && ($this->doc[$this->pos + 1] === '=')) {
            ++$this->pos;
            return ($this->token = self::TOK_COMPARE_CONTAINS);
        } else {
            return ($this->token = self::TOK_ALL);
        }
    }

    /**
     * Parse "!" character
     * @internal Could be {@link TOK_NOT} or {@link TOK_COMPARE_NOT_EQUAL}
     * @return int
     */
    protected function parse_not() {
        if ((($this->pos + 1) < $this->size) && ($this->doc[$this->pos + 1] === '=')) {
            ++$this->pos;
            return ($this->token = self::TOK_COMPARE_NOT_EQUAL);
        } else {
            return ($this->token = self::TOK_NOT);
        }
    }

    /**
     * Parse several compare characters
     * @return int
     */
    protected function parse_compare() {
        if ((($this->pos + 1) < $this->size) && ($this->doc[$this->pos + 1] === '=')) {
            switch($this->doc[$this->pos++]) {
                case '$':
                    return ($this->token = self::TOK_COMPARE_ENDS);
                case '%':
                    return ($this->token = self::TOK_COMPARE_REGEX);
                case '^':
                    return ($this->token = self::TOK_COMPARE_STARTS);
                case '<':
                    return ($this->token = self::TOK_COMPARE_SMALLER_THAN);
            }
        }
        return false;
    }

    /**
     * Parse strings (" and ')
     * @return int
     */
    protected function parse_string() {
        $char = $this->doc[$this->pos];

        while (true) {
            if ($this->next_search($char.'\\', false) !== self::TOK_NULL) {
                if($this->doc[$this->pos] === $char) {
                    break;
                } else {
                    ++$this->pos;
                }
            } else {
                $this->pos = $this->size - 1;
                break;
            }
        }

        return ($this->token = self::TOK_STRING);
    }

}
