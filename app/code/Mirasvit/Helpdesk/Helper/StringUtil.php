<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-helpdesk
 * @version   1.1.77
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Helpdesk\Helper;

use Mirasvit\Helpdesk\Model\Config as Config;

class StringUtil extends \Magento\Framework\DataObject
{
    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Ticket\CollectionFactory
     */
    protected $ticketCollectionFactory;

    /**
     * @var \Mirasvit\Helpdesk\Model\Config
     */
    protected $config;

    /**
     * @param \Mirasvit\Helpdesk\Model\ResourceModel\Ticket\CollectionFactory $ticketCollectionFactory
     * @param \Mirasvit\Helpdesk\Model\Config                                 $config
     * @param array                                                           $data
     */
    public function __construct(
        \Mirasvit\Helpdesk\Model\ResourceModel\Ticket\CollectionFactory $ticketCollectionFactory,
        \Mirasvit\Helpdesk\Model\Config $config,
        array $data = []
    ) {
        $this->ticketCollectionFactory = $ticketCollectionFactory;
        $this->config = $config;
        parent::__construct($data);
    }

    /**
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return string
     */
    public function generateTicketCode()
    {
        $code = $this->generateRandString(3).'-'.$this->generateRandNum(3).'-'.$this->generateRandNum(5);
        $collection = $this->ticketCollectionFactory->create()
            ->addFieldToFilter('code', $code);
        if ($collection->count() == 0) {
            return $code;
        } else {
            return $this->generateTicketCode();
        }
    }

    /**
     * @param string $subject
     *
     * @return bool|string
     */
    public function getTicketCodeFromSubject($subject)
    {
        if ($subject && preg_match('[[#][A-Z]{1,3}-[0-9]{1,3}-[0-9]{1,5}]', $subject, $regs)) {
            return ltrim($regs[0], '#');
        }

        //aw tickets
        if (($this->getConfig()->getGeneralAcceptForeignTickets() == Config::ACCEPT_FOREIGN_TICKETS_AW) &&
                $subject && preg_match('[[#][A-Z]{1,3}-[0-9]{1,5}]', $subject, $regs)) {
            return ltrim($regs[0], '#');
        }

        //mw tickets
        if (($this->config->getGeneralAcceptForeignTickets() == Config::ACCEPT_FOREIGN_TICKETS_MW) &&
                $subject && preg_match('[Ticket #[0-9]{1,10}]', $subject, $regs)) {
            return $regs[0];
        }

        return false;
    }

    /**
     * Parse body for code like "Message-Id:--#AAA-123-45678--".
     *
     * @param string $body
     *
     * @return bool|string
     */
    public function getTicketCodeFromBody($body)
    {
        if ($body && preg_match('[Message-Id:--[#][a-zA-Z0-9-]+--]', $body, $regs)) {
            $res = ltrim($regs[0], 'Message-Id:--');
            $res = rtrim($res, '--');
            $res = ltrim($res, '#');

            return $res;
        }

        return false;
    }

    /**
     * @param string $length
     *
     * @return string
     */
    public function generateRandNum($length)
    {
        $characters = '0123456789';
        $randomString = '';
        for ($i = 0; $i < $length; ++$i) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $randomString;
    }

    /**
     * @param string $length
     *
     * @return string
     */
    public function generateRandString($length)
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; ++$i) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $randomString;
    }

    /**
     * Convert to plain format to store in database.
     *
     * @param string $body
     * @param string $format
     *
     * @return string
     */
    public function parseBody($body, $format)
    {
        // require_once Mage::getBaseDir('base').'/lib/Mirasvit/EmailReplyParser/load.php';
        // $parser = (new EmailParser());
        // $body = $parser->parse($body);
        // $body = $body->getVisibleText();

        if ($format == Config::FORMAT_HTML) {
            $body = $this->convertToPlain($body);
        }

        $body = $this->removeQuotedText($body);
        $body = trim($body);
        $body = $this->removeTime($body);

        return $body;
    }

    /**
     * @param string $body
     *
     * @return string
     */
    public function removeQuotedText($body)
    {
        //@fixme the same code in Mirasvit\Helpdesk\Helper\Email
        $separator = '##- '.__('please type your reply above this line').' -##';
        if ($pos = strpos($body, $separator)) {
            $body = substr($body, 0, $pos);
        }

        return $body;
    }

    /**
     * @param string $body
     *
     * @return string
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function removeTime($body)
    {
        $lines = explode("\n", $body);
        while (count($lines) > 0 && (trim(end($lines)) == '' || substr(trim(end($lines)), 0, 1) == '>')) {
            array_pop($lines);
        }
        $lastline = end($lines);
        $prevLastline = '';
        if (count($lines) > 1) {
            $prevLastline = $lines[count($lines) - 2];
        }
        //2014-03-26 19:47 GMT+02:00 Sales <support2@mirasvit.com.ua>:
        //2014-03-25 0:00 GMT+02:00 COPPERLAB Customer Support <support..m>:
        //On Mon, Mar 24, 2014 at 10:58 PM, Sales wrote:
        //2014-03-24 19:22 GMT+02:00 Sales :
        //On Dec 8, 2014, at 9:24 AM, Mirasvit Support <a8v1oq0kggnvsinmg6dv@mirasvit.com> wrote:
        //El 11-12-2014, a las 12:22 p.m., Mirasvit Support <a8v1oq0kggnvsinmg6dv@mirasvit.com> escribió:
        //Em 11-12-2014, a las 12:22 p.m., Mirasvit Support <a8v1oq0kggnvsinmg6dv@mirasvit.com> escribió:
        if (($this->validateDate($lastline) || substr($lastline, 0, 2) == 'On'
                || substr($lastline, 0, 2) == 'El' || substr($lastline, 0, 2) == 'Em')
            && substr($lastline, -1) == ':'
            ) {
            array_pop($lines);
            //On Mon, Dec 28, 2015 at 2:54 PM Main Website Store, Sales
            //<helpdeskmx2+sales@gmail.com> wrote:
        } elseif (($this->validateDate($prevLastline) || substr($prevLastline, 0, 2) == 'On'
                || substr($prevLastline, 0, 2) == 'El' || substr($prevLastline, 0, 2) == 'Em')
            && substr($lastline, -1) == ':'
        ) {
            array_pop($lines);
            array_pop($lines);
        }
        $body = implode("\n", $lines);

        return trim($body);
    }

    /**
     * @param string $date
     * @return bool
     */
    public function validateDate($date)
    {
        return preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{1,2}:[0-9]{2} GMT/', $date);
    }

    /**
     * @param string $html
     *
     * @return string
     */
    public function convertToPlain($html)
    {
        $htmlToText = new MHtml2Text2($html);
        $text = $htmlToText->get_text();

        return $text;
    }

    /**
     * @param string $string
     *
     * @return string
     */
    protected function stripEmptyLines($string)
    {
        return preg_replace("/\n{3,}/", "\n\n", $string);
    }

    /**
     * @param string $text
     *
     * @return string
     */
    public function htmlEscapeAndLinkUrls($text)
    {
        $text = preg_replace('/(((f|ht){1}tp:\/\/)[-a-zA-Z0-9@:%_\+.~#?&\/\/=]+)/i', '<a target="_blank" href="\\1">\\1</a>', $text);
        $text = preg_replace('/(((f|ht){1}tps:\/\/)[-a-zA-Z0-9@:%_\+.~#?&\/\/=]+)/i', '<a target="_blank" href="\\1">\\1</a>', $text);
        $text = preg_replace(
            '/([[:space:]()[{}])(www.[-a-zA-Z0-9@:%_\+.~#?&\/\/=]+)/i',
            '\\1<a target="_blank" href="http://\\2">\\2</a>',
            $text
        );
        $text = preg_replace(
            '/ ([_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,3}) /i',
            '&nbsp;<a href="mailto:\\1">\\1</a>&nbsp;',
            $text
        );

        return $text;
    }

    /**
     * @param string $html Plain text.
     *
     * @return string
     */
    public function convertToHtml($html)
    {
        $html = preg_replace(
            [
                // Remove invisible content
                '@<head[^>]*?>.*?</head>@siu',
                '@<style[^>]*?>.*?</style>@siu',
                '@<script[^>]*?.*?</script>@siu',
                '@<object[^>]*?.*?</object>@siu',
                '@<embed[^>]*?.*?</embed>@siu',
                '@<applet[^>]*?.*?</applet>@siu',
                '@<noframes[^>]*?.*?</noframes>@siu',
                '@<noscript[^>]*?.*?</noscript>@siu',
                '@<noembed[^>]*?.*?</noembed>@siu',
                // Add line breaks before and after blocks
                '@</?((address)|(blockquote)|(center)|(del))@iu',
                '@</?((div)|(h[1-9])|(ins)|(isindex)|(p)|(pre))@iu',
                '@</?((dir)|(dl)|(dt)|(dd)|(li)|(menu)|(ol)|(ul))@iu',
                '@</?((table)|(th)|(td)|(caption))@iu',
                '@</?((form)|(button)|(fieldset)|(legend)|(input))@iu',
                '@</?((label)|(select)|(optgroup)|(option)|(textarea))@iu',
                '@</?((frameset)|(frame)|(iframe))@iu',
            ],
            [
                ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
                "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0",
                "\n\$0", "\n\$0",
            ],
            $html
        );
        //        $html = strip_tags($html); //we can't strip tags, because if we post code, it will not show it

        $html = htmlspecialchars($html);
        $html = str_replace('&amp;', '&', $html); //links can have &. so we need to return them.

        $html = $this->htmlEscapeAndLinkUrls($html); // move here to prevent adding &nbsp to links as & part of link

        $html = str_replace('   ', '&nbsp;&nbsp;&nbsp;', $html);
        $html = str_replace('  ', '&nbsp;&nbsp;', $html);
        $html = str_replace('   ', '&nbsp;&nbsp;&nbsp;&nbsp;', $html);
        // $html = highlight_string($html);
        $html = trim($this->stripEmptyLines($html));
        $html = nl2br($html);

        return $html;
    }

    /**
     * Takes a unix timestamp and returns a relative time string such as "3 minutes ago",
     *   "2 months from now", "1 year ago", etc
     * The detailLevel parameter indicates the amount of detail. The examples above are
     * with detail level 1. With detail level 2, the output might be like "3 minutes 20
     *   seconds ago", "2 years 1 month from now", etc.
     * With detail level 3, the output might be like "5 hours 3 minutes 20 seconds ago",
     *   "2 years 1 month 2 weeks from now", etc.
     *
     * @param int $timestamp
     * @param int $detailLevel
     *
     * @return string
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function nicetime($timestamp, $detailLevel = 1)
    {
        $periods = ['sec', 'min', 'hour', 'day', 'week', 'month', 'year', 'decade'];
        $lengths = ['60', '60', '24', '7', '4.35', '12', '10'];

        $now = time();

        // check validity of date
        if (empty($timestamp)) {
            return __('Unknown time');
        }

        // is it future date or past date
        if ($now > $timestamp) {
            $difference = $now - $timestamp;
            $tense = __('ago');
        } else {
            $difference = $timestamp - $now;
            $tense = __('from now');
        }

        if ($difference == 0) {
            return __('1 sec ago');
        }

        $remainders = [];

        for ($j = 0; $j < count($lengths); ++$j) {
            $remainders[$j] = floor(fmod($difference, $lengths[$j]));
            $difference = floor($difference / $lengths[$j]);
        }

        $difference = round($difference);

        $remainders[] = $difference;

        $string = '';

        for ($i = count($remainders) - 1; $i >= 0; --$i) {
            if ($remainders[$i]) {
                // on last detail level get next period and round current
                if ($detailLevel == 1 && isset($remainders[$i-1]) && $remainders[$i-1] > $lengths[$i-1]/2) {
                    $remainders[$i]++;
                }
                $period = $periods[$i];
                if ($remainders[$i] != 1) {
                    $period .= 's';
                }

                $string .= $remainders[$i].' '.__($period);

                $string .= ' ';

                --$detailLevel;

                if ($detailLevel <= 0) {
                    break;
                }
            }
        }

        return $string.$tense;
    }
}

// @codingStandardsIgnoreStart

/*************************************************************************
 *                                                                       *
 * Converts HTML to formatted plain text                                 *
 *                                                                       *
 * Portions Copyright (c) 2005-2007 Jon Abernathy <jon@chuggnutt.com>    *
 *                                                                       *
 * This script is free software; you can redistribute it and/or modify   *
 * it under the terms of the GNU General Public License as published by  *
 * the Free Software Foundation; either version 2 of the License, or     *
 * (at your option) any later version.                                   *
 *                                                                       *
 * The GNU General Public License can be found at                        *
 * http://www.gnu.org/copyleft/gpl.html.                                 *
 *                                                                       *
 * This script is distributed in the hope that it will be useful,        *
 * but WITHOUT ANY WARRANTY; without even the implied warranty of        *
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the          *
 * GNU General Public License for more details.                          *
 *                                                                       *
 *************************************************************************/

/**
 * Class MHtml2Text2.
 *
 * @SuppressWarnings(PHPMD)
 */
class MHtml2Text2
{
    /**
     * Contains the HTML content to convert.
     *
     * @var string
     */
    protected $html;

    /**
     * Contains the converted, formatted text.
     *
     * @var string
     */
    protected $text;

    /**
     * Maximum width of the formatted text, in columns.
     *
     * Set this value to 0 (or less) to ignore word wrapping
     * and not constrain text to a fixed-width column.
     *
     * @var int
     */
    protected $width = 70;

    /**
     * List of preg* regular expression patterns to search for,
     * used in conjunction with $replace.
     *
     * @var array
     *
     * @see $replace
     */
    protected $search = [
        "/\r/",                                  // Non-legal carriage return
        "/[\n\t]+/",                             // Newlines and tabs
        '/<head[^>]*>.*?<\/head>/i',             // <head>
        '/<script[^>]*>.*?<\/script>/i',         // <script>s -- which strip_tags supposedly has problems with
        '/<style[^>]*>.*?<\/style>/i',           // <style>s -- which strip_tags supposedly has problems with
        '/<p[^>]*>/i',                           // <P>
        '/<br[^>]*>/i',                          // <br>
        '/<i[^>]*>(.*?)<\/i>/i',                 // <i>
        '/<em[^>]*>(.*?)<\/em>/i',               // <em>
        '/(<ul[^>]*>|<\/ul>)/i',                 // <ul> and </ul>
        '/(<ol[^>]*>|<\/ol>)/i',                 // <ol> and </ol>
        '/(<dl[^>]*>|<\/dl>)/i',                 // <dl> and </dl>
        '/<li[^>]*>(.*?)<\/li>/i',               // <li> and </li>
        '/<dd[^>]*>(.*?)<\/dd>/i',               // <dd> and </dd>
        '/<dt[^>]*>(.*?)<\/dt>/i',               // <dt> and </dt>
        '/<li[^>]*>/i',                          // <li>
        '/<hr[^>]*>/i',                          // <hr>
        '/<div[^>]*>/i',                         // <div>
        '/(<table[^>]*>|<\/table>)/i',           // <table> and </table>
        '/(<tr[^>]*>|<\/tr>)/i',                 // <tr> and </tr>
        '/<td[^>]*>(.*?)<\/td>/i',               // <td> and </td>
        '/<span class="_html2text_ignore">.+?<\/span>/i',  // <span class="_html2text_ignore">...</span>
    ];

    /**
     * List of pattern replacements corresponding to patterns searched.
     *
     * @var array
     *
     * @see $search
     */
    protected $replace = [
        '',                                     // Non-legal carriage return
        ' ',                                    // Newlines and tabs
        '',                                     // <head>
        '',                                     // <script>s -- which strip_tags supposedly has problems with
        '',                                     // <style>s -- which strip_tags supposedly has problems with
        "\n\n",                                 // <P>
        "\n",                                   // <br>
        '\\1',                                // <i>
        '\\1',                                // <em>
        "\n\n",                                 // <ul> and </ul>
        "\n\n",                                 // <ol> and </ol>
        "\n\n",                                 // <dl> and </dl>
        "\t* \\1\n",                            // <li> and </li>
        " \\1\n",                               // <dd> and </dd>
        "\t* \\1",                              // <dt> and </dt>
        "\n\t* ",                               // <li>
        "\n-------------------------\n",        // <hr>
        "<div>\n",                              // <div>
        "\n\n",                                 // <table> and </table>
        "\n",                                   // <tr> and </tr>
        "\t\t\\1\n",                            // <td> and </td>
        '',                                      // <span class="_html2text_ignore">...</span>
    ];

    /**
     * List of preg* regular expression patterns to search for,
     * used in conjunction with $ent_replace.
     *
     * @var array
     *
     * @see $ent_replace
     */
    protected $ent_search = [
        '/&(nbsp|#160);/i',                      // Non-breaking space
        '/&(quot|rdquo|ldquo|#8220|#8221|#147|#148);/i',
                                         // Double quotes
        '/&(apos|rsquo|lsquo|#8216|#8217);/i',   // Single quotes
        '/&gt;/i',                               // Greater-than
        '/&lt;/i',                               // Less-than
        '/&(copy|#169);/i',                      // Copyright
        '/&(trade|#8482|#153);/i',               // Trademark
        '/&(reg|#174);/i',                       // Registered
        '/&(mdash|#151|#8212);/i',               // mdash
        '/&(ndash|minus|#8211|#8722);/i',        // ndash
        '/&(bull|#149|#8226);/i',                // Bullet
        '/&(pound|#163);/i',                     // Pound sign
        '/&(euro|#8364);/i',                     // Euro sign
        '/&(amp|#38);/i',                        // Ampersand: see _converter()
        '/[ ]{2,}/',                             // Runs of spaces, post-handling
    ];

    /**
     * List of pattern replacements corresponding to patterns searched.
     *
     * @var array
     *
     * @see $ent_search
     */
    protected $ent_replace = [
        ' ',                                    // Non-breaking space
        '"',                                    // Double quotes
        "'",                                    // Single quotes
        '>',
        '<',
        '(c)',
        '(tm)',
        '(R)',
        '--',
        '-',
        '*',
        '£',
        'EUR',                                  // Euro sign. € ?
        '|+|amp|+|',                            // Ampersand: see _converter()
        ' ',                                    // Runs of spaces, post-handling
    ];

    /**
     * List of preg* regular expression patterns to search for
     * and replace using callback function.
     *
     * @var array
     */
    protected $callback_search = [
        '/<(a) [^>]*href=("|\')([^"\']+)\2([^>]*)>(.*?)<\/a>/i', // <a href="">
        '/<(h)[123456]( [^>]*)?>(.*?)<\/h[123456]>/i',           // h1 - h6
/*        '/<(b)( [^>]*)?>(.*?)<\/b>/i',                           // <b>
        '/<(strong)( [^>]*)?>(.*?)<\/strong>/i',                 // <strong>
*/
        '/<(th)( [^>]*)?>(.*?)<\/th>/i',                         // <th> and </th>
    ];

    /**
     * List of preg* regular expression patterns to search for in PRE body,
     * used in conjunction with $pre_replace.
     *
     * @var array
     *
     * @see $pre_replace
     */
    protected $pre_search = [
        "/\n/",
        "/\t/",
        '/ /',
        '/<pre[^>]*>/',
        '/<\/pre>/',
    ];

    /**
     * List of pattern replacements corresponding to patterns searched for PRE body.
     *
     * @var array
     *
     * @see $pre_search
     */
    protected $pre_replace = [
        '<br>',
        '&nbsp;&nbsp;&nbsp;&nbsp;',
        '&nbsp;',
        '',
        '',
    ];

    /**
     * Contains a list of HTML tags to allow in the resulting text.
     *
     * @var string
     *
     * @see set_allowed_tags()
     */
    protected $allowed_tags = '';

    /**
     * Contains the base URL that relative links should resolve to.
     *
     * @var string
     */
    protected $url;

    /**
     * Indicates whether content in the $html variable has been converted yet.
     *
     * @var bool
     *
     * @see $html, $text
     */
    protected $_converted = false;

    /**
     * Contains URL addresses from links to be rendered in plain text.
     *
     * @var array
     *
     * @see _build_link_list()
     */
    protected $_link_list = [];

    /**
     * Various configuration options (able to be set in the constructor).
     *
     * @var array
     */
    protected $_options = [

        // 'none'
        // 'inline' (show links inline)
        // 'nextline' (show links on the next line)
        // 'table' (if a table of link URLs should be listed after the text.
        'do_links' => 'inline',

        //  Maximum width of the formatted text, in columns.
        //  Set this value to 0 (or less) to ignore word wrapping
        //  and not constrain text to a fixed-width column.
        'width' => 70,
    ];

    /**
     * Constructor.
     *
     * If the HTML source string (or file) is supplied, the class
     * will instantiate with that source propagated, all that has
     * to be done it to call get_text().
     *
     * @param string $source    HTML content
     * @param bool   $from_file Indicates $source is a file to pull content from
     * @param array  $options   Set configuration options
     */
    public function __construct($source = '', $from_file = false, $options = [])
    {
        $this->_options = array_merge($this->_options, $options);

        if (!empty($source)) {
            $this->set_html($source, $from_file);
        }

        $this->set_base_url();
    }

    /**
     * Loads source HTML into memory, either from $source string or a file.
     *
     * @param string $source    HTML content
     * @param bool   $from_file Indicates $source is a file to pull content from
     */
    public function set_html($source, $from_file = false)
    {
        if ($from_file && file_exists($source)) {
            $this->html = file_get_contents($source);
        } else {
            $this->html = $source;
        }

        $this->_converted = false;
    }

    /**
     * Returns the text, converted from HTML.
     *
     * @return string
     */
    public function get_text()
    {
        if (!$this->_converted) {
            $this->_convert();
        }

        return $this->text;
    }

    /**
     * Prints the text, converted from HTML.
     */
    public function print_text()
    {
        print $this->get_text();
    }

    /**
     * Sets the allowed HTML tags to pass through to the resulting text.
     *
     * Tags should be in the form "<p>", with no corresponding closing tag.
     *
     * @param string $allowed_tags
     */
    public function set_allowed_tags($allowed_tags = '')
    {
        if (!empty($allowed_tags)) {
            $this->allowed_tags = $allowed_tags;
        }
    }

    /**
     * Sets a base URL to handle relative links.
     *
     * @param string $url
     */
    public function set_base_url($url = '')
    {
        if (empty($url)) {
            if (!empty($_SERVER['HTTP_HOST'])) {
                $this->url = 'http://'.$_SERVER['HTTP_HOST'];
            } else {
                $this->url = '';
            }
        } else {
            // Strip any trailing slashes for consistency (relative
            // URLs may already start with a slash like "/file.html")
            if (substr($url, -1) == '/') {
                $url = substr($url, 0, -1);
            }
            $this->url = $url;
        }
    }

    /**
     * Workhorse function that does actual conversion (calls _converter() method).
     */
    protected function _convert()
    {
        // Variables used for building the link list
        $this->_link_list = [];

        $text = trim(stripslashes($this->html));

        // Convert HTML to TXT
        $this->_converter($text);

        // Add link list
        if (!empty($this->_link_list)) {
            $text .= "\n\nLinks:\n------\n";
            foreach ($this->_link_list as $idx => $url) {
                $text .= '['.($idx + 1).'] '.$url."\n";
            }
        }

        $this->text = $text;

        $this->_converted = true;
    }

    /**
     * Workhorse function that does actual conversion.
     *
     * First performs custom tag replacement specified by $search and
     * $replace arrays. Then strips any remaining HTML tags, reduces whitespace
     * and newlines to a readable format, and word wraps the text to
     * $this->_options['width'] characters.
     *
     * @param string $text Reference to HTML content string
     */
    protected function _converter(&$text)
    {
        // Convert <BLOCKQUOTE> (before PRE!)
        $this->_convert_blockquotes($text);

        // Convert <PRE>
        $this->_convert_pre($text);

        // Run our defined tags search-and-replace
        $text = preg_replace($this->search, $this->replace, $text);

        // Run our defined tags search-and-replace with callback
        $text = preg_replace_callback($this->callback_search, [$this, '_preg_callback'], $text);

        // Strip any other HTML tags
        $text = strip_tags($text, $this->allowed_tags);

        // Run our defined entities/characters search-and-replace
        $text = preg_replace($this->ent_search, $this->ent_replace, $text);

        // Replace known html entities
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');

        // Remove unknown/unhandled entities (this cannot be done in search-and-replace block)
        $text = preg_replace('/&([a-zA-Z0-9]{2,6}|#[0-9]{2,4});/', '', $text);

        // Convert "|+|amp|+|" into "&", need to be done after handling of unknown entities
        // This properly handles situation of "&amp;quot;" in input string
        $text = str_replace('|+|amp|+|', '&', $text);

        // Bring down number of empty lines to 2 max
        $text = preg_replace("/\n\s+\n/", "\n\n", $text);
        $text = preg_replace("/[\n]{3,}/", "\n\n", $text);

        // remove leading empty lines (can be produced by eg. P tag on the beginning)
        $text = ltrim($text, "\n");

//@dva fix
        $ar = explode("\n", $text);
        foreach ($ar as $key => $value) {
            $ar[$key] = ltrim($value);
        }
        $text = implode("\n", $ar);
//@dva fix end

        // Wrap the text to a readable format
        // for PHP versions >= 4.0.2. Default width is 75
        // If width is 0 or less, don't wrap the text.
        if ($this->_options['width'] > 0) {
            $text = wordwrap($text, $this->_options['width']);
        }
    }

    /**
     * Helper function called by preg_replace() on link replacement.
     *
     * Maintains an internal list of links to be displayed at the end of the
     * text, with numeric indices to the original point in the text they
     * appeared. Also makes an effort at identifying and handling absolute
     * and relative links.
     *
     * @param string $link          URL of the link
     * @param string $display       Part of the text to associate number with
     * @param string $link_override
     *
     * @return string
     */
    protected function _build_link_list($link, $display, $link_override = null)
    {
        $link_method = ($link_override) ? $link_override : $this->_options['do_links'];
        if ($link_method == 'none') {
            return $display;
        }

        // Ignored link types
        if (preg_match('!^(javascript:|mailto:|#)!i', $link)) {
            return $display;
        }

        $url = $link; //@dva fix
        // if (preg_match('!^([a-z][a-z0-9.+-]+:)!i', $link)) {
        //     $url = $link;
        // } else {
        //     $url = $this->url;
        //     if (substr($link, 0, 1) != '/') {
        //         $url .= '/';
        //     }
        //     $url .= "$link";
        // }

        if ($link_method == 'table') {
            if (($index = array_search($url, $this->_link_list)) === false) {
                $index = count($this->_link_list);
                $this->_link_list[] = $url;
            }

            return $display.' ['.($index + 1).']';
        } elseif ($link_method == 'nextline') {
            return $display."\n[".$url.']';
        } else { // link_method defaults to inline

            return $url;
         //@dva   // return $display . ' [' . $url . ']';
        }
    }

    protected $pre_content;

    /**
     * Helper function for PRE body conversion.
     *
     * @param string $text HTML content
     */
    protected function _convert_pre(&$text)
    {
        // get the content of PRE element
        while (preg_match('/<pre[^>]*>(.*)<\/pre>/ismU', $text, $matches)) {
            $this->pre_content = $matches[1];

            // Run our defined tags search-and-replace with callback
            $this->pre_content = preg_replace_callback($this->callback_search,
                [$this, '_preg_callback'], $this->pre_content);

            // convert the content
            $this->pre_content = sprintf('<div><br>%s<br></div>',
                preg_replace($this->pre_search, $this->pre_replace, $this->pre_content));

            // replace the content (use callback because content can contain $0 variable)
            $text = preg_replace_callback('/<pre[^>]*>.*<\/pre>/ismU',
                [$this, '_preg_pre_callback'], $text, 1);

            // free memory
            $this->pre_content = '';
        }
    }

    /**
     * Helper function for BLOCKQUOTE body conversion.
     *
     * @param string $text HTML content
     */
    protected function _convert_blockquotes(&$text)
    {
        if (preg_match_all('/<\/*blockquote[^>]*>/i', $text, $matches, PREG_OFFSET_CAPTURE)) {
            $start = 0;
            $taglen = 0;
            $level = 0;
            $diff = 0;
            foreach ($matches[0] as $m) {
                if ($m[0][0] == '<' && $m[0][1] == '/') {
                    --$level;
                    if ($level < 0) {
                        $level = 0; // malformed HTML: go to next blockquote
                    } elseif ($level > 0) {
                        // skip inner blockquote
                    } else {
                        $end = $m[1];
                        $len = $end - $taglen - $start;
                        // Get blockquote content
                        $body = substr($text, $start + $taglen - $diff, $len);

                        // Set text width
                        $p_width = $this->_options['width'];
                        if ($this->_options['width'] > 0) {
                            $this->_options['width'] -= 2;
                        }
                        // Convert blockquote content
                        $body = trim($body);
                        $this->_converter($body);
                        // Add citation markers and create PRE block
                        $body = preg_replace('/((^|\n)>*)/', '\\1> ', trim($body));
                        $body = '<pre>'.htmlspecialchars($body).'</pre>';
                        // Re-set text width
                        $this->_options['width'] = $p_width;
                        // Replace content
                        $text = substr($text, 0, $start - $diff)
                            .$body.substr($text, $end + strlen($m[0]) - $diff);

                        $diff = $len + $taglen + strlen($m[0]) - strlen($body);
                        unset($body);
                    }
                } else {
                    if ($level == 0) {
                        $start = $m[1];
                        $taglen = strlen($m[0]);
                    }
                    ++$level;
                }
            }
        }
    }

    /**
     * Callback function for preg_replace_callback use.
     *
     * @param array $matches PREG matches
     *
     * @return string
     */
    protected function _preg_callback($matches)
    {
        switch (strtolower($matches[1])) {
        case 'b':
        case 'strong':
            return $this->_toupper($matches[3]);
        case 'th':
            return $this->_toupper("\t\t".$matches[3]."\n");
        case 'h':
            return $this->_toupper("\n\n".$matches[3]."\n\n");
        case 'a':
            // override the link method
            $link_override = null;
            if (preg_match('/_html2text_link_(\w+)/', $matches[4], $link_override_match)) {
                $link_override = $link_override_match[1];
            }
            // Remove spaces in URL (#1487805)
            $url = str_replace(' ', '', $matches[3]);

            return $this->_build_link_list($url, $matches[5], $link_override);
        }
    }

    /**
     * Callback function for preg_replace_callback use in PRE content handler.
     *
     * @param array $matches
     *
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _preg_pre_callback($matches)
    {
        return $this->pre_content;
    }

    /**
     * Strtoupper function with HTML tags and entities handling.
     *
     * @param string $str Text to convert
     *
     * @return string Converted text
     */
    private function _toupper($str)
    {
        // string can containg HTML tags
        $chunks = preg_split('/(<[^>]*>)/', $str, null, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

        // convert toupper only the text between HTML tags
        foreach ($chunks as $idx => $chunk) {
            if ($chunk[0] != '<') {
                $chunks[$idx] = $this->_strtoupper($chunk);
            }
        }

        return implode($chunks);
    }

    /**
     * Strtoupper multibyte wrapper function with HTML entities handling.
     * Forces mb_strtoupper-call to UTF-8.
     *
     * @param string $str Text to convert
     *
     * @return string Converted text
     */
    private function _strtoupper($str)
    {
        $str = html_entity_decode($str, ENT_COMPAT);

        if (function_exists('mb_strtoupper')) {
            $str = mb_strtoupper($str, 'UTF-8');
        } else {
            $str = strtoupper($str);
        }

        $str = htmlspecialchars($str, ENT_COMPAT);

        return $str;
    }
}
// @codingStandardsIgnoreEnd

