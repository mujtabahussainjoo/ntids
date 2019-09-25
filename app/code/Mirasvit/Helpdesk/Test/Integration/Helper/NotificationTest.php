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

class NotificationTest extends \PHPUnit\Framework\TestCase
{
    /** @var  \Mirasvit\Helpdesk\Helper\Notification */
    protected $helper;

    /** @var  \Magento\TestFramework\ObjectManager */
    protected $objectManager;

    /**
     * setUp.
     */
    public function setUp()
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->helper = $this->objectManager->create('Mirasvit\Helpdesk\Helper\Notification');
        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
            'Magento has bug CSS compilation from source .table is undefined in. Temporary skip all tests'
        );
    }

    /**
     * @param string $code
     *
     * @return string
     */
    protected function getExpectedMail($code)
    {
        return file_get_contents(dirname(__FILE__)."/NotificationTest/expected/$code.html");
    }

    /**
     * @param string $template
     * @magentoDataFixture Mirasvit/Helpdesk/_files/ticket.php
     * @magentoDataFixture Mirasvit/Helpdesk/_files/customer.php
     * @doNotIndex catalog_product_price
     * @dataProvider provider
     */
    public function testMail($template)
    {
        $customer = $this->objectManager->create('Magento\Customer\Model\Customer');
        $customer->load(1);
        /** @var \Mirasvit\Helpdesk\Model\Ticket $ticket */
        $ticket = $this->objectManager->create('Mirasvit\Helpdesk\Model\Ticket')->getCollection()->getFirstItem();

        $this->helper->mail($ticket, $customer, false, 'recipient@example.com', 'recipientName@example.com', $template);

        /** @var \Magento\TestFramework\Mail\Template\TransportBuilderMock $transportBuilder */
        $transportBuilder = $this->objectManager->get('Magento\TestFramework\Mail\Template\TransportBuilderMock');
        $actual = $this->htmlToText($transportBuilder->getSentMessage()->getUnsafeBodyHtml()->getRawContent());
        //        echo $actual;die;
        $this->assertEquals(
            $this->getExpectedMail($template),
            $actual
        );
    }

    /**
     * @return array
     */
    public function provider()
    {
        return [
            ['helpdesk_notification_new_ticket_template'],
            ['helpdesk_notification_staff_new_ticket_template'],
            ['helpdesk_notification_new_message_template'],
            ['helpdesk_notification_staff_new_message_template'],

            ['helpdesk_notification_third_new_message_template'],
            ['helpdesk_notification_reminder_template'],
            ['helpdesk_notification_rule_template'],
            ['helpdesk_notification_staff_new_satisfaction_template'],
        ];
    }

    /**
     * @param string $text
     *
     * @return string
     */
    public function htmlToText($text)
    {
        $htmlToText = new html2text($text);
        $htmlToText->set_allowed_tags('<a>');
        $text = $htmlToText->get_text();

        $lines = explode("\n", $text);
        foreach ($lines as $key => $value) {
            $value = preg_replace('/\s+/', ' ', $value);
            $lines[$key] = trim($value);
        }
        $text = implode("\n", $lines);
        $text = str_replace('index.php/', '', $text);

        return trim($text);
    }
}

//@codingStandardsIgnoreStart
class html2text
{
    /*
     *  Contains the HTML content to convert.
     *
     *  @var string $html
     *  @access public
     */
    public $html;

    /*
     *  Contains the converted, formatted text.
     *
     *  @var string $text
     *  @access public
     */
    public $text;

    /*
     *  Maximum width of the formatted text, in columns.
     *
     *  Set this value to 0 (or less) to ignore word wrapping
     *  and not constrain text to a fixed-width column.
     *
     *  @var integer $width
     *  @access public
     */
    public $width = 70;

    /*
     *  List of preg* regular expression patterns to search for,
     *  used in conjunction with $replace.
     *
     *  @var array $search
     *  @access public
     *  @see $replace
     */
    public $search = array(
        "/\r/",                                  // Non-legal carriage return
        "/[\n\t]+/",                             // Newlines and tabs
        '/[ ]{2,}/',                             // Runs of spaces, pre-handling
        '/<script[^>]*>.*?<\/script>/i',         // <script>s -- which strip_tags supposedly has problems with
        '/<style[^>]*>.*?<\/style>/i',           // <style>s -- which strip_tags supposedly has problems with
        //'/<!-- .* -->/',                         // Comments -- which strip_tags might have problem a with
        '/<h[123][^>]*>(.*?)<\/h[123]>/ie',      // H1 - H3
        '/<h[456][^>]*>(.*?)<\/h[456]>/ie',      // H4 - H6
        '/<p[^>]*>/i',                           // <P>
        '/<br[^>]*>/i',                          // <br>
        '/<b[^>]*>(.*?)<\/b>/ie',                // <b>
        '/<strong[^>]*>(.*?)<\/strong>/ie',      // <strong>
        '/<i[^>]*>(.*?)<\/i>/i',                 // <i>
        '/<em[^>]*>(.*?)<\/em>/i',               // <em>
        '/(<ul[^>]*>|<\/ul>)/i',                 // <ul> and </ul>
        '/(<ol[^>]*>|<\/ol>)/i',                 // <ol> and </ol>
        '/<li[^>]*>(.*?)<\/li>/i',               // <li> and </li>
        '/<li[^>]*>/i',                          // <li>
        '/<a [^>]*href="([^"]+)"[^>]*>(.*?)<\/a>/ie',
        // <a href="">
        '/<hr[^>]*>/i',                          // <hr>
        '/(<table[^>]*>|<\/table>)/i',           // <table> and </table>
        '/(<tr[^>]*>|<\/tr>)/i',                 // <tr> and </tr>
        '/<td[^>]*>(.*?)<\/td>/i',               // <td> and </td>
        '/<th[^>]*>(.*?)<\/th>/ie',              // <th> and </th>
        '/&(nbsp|#160);/i',                      // Non-breaking space
        '/&(quot|rdquo|ldquo|#8220|#8221|#147|#148);/i',
        // Double quotes
        '/&(apos|rsquo|lsquo|#8216|#8217);/i',   // Single quotes
        '/&gt;/i',                               // Greater-than
        '/&lt;/i',                               // Less-than
        '/&(amp|#38);/i',                        // Ampersand
        '/&(copy|#169);/i',                      // Copyright
        '/&(trade|#8482|#153);/i',               // Trademark
        '/&(reg|#174);/i',                       // Registered
        '/&(mdash|#151|#8212);/i',               // mdash
        '/&(ndash|minus|#8211|#8722);/i',        // ndash
        '/&(bull|#149|#8226);/i',                // Bullet
        '/&(pound|#163);/i',                     // Pound sign
        '/&(euro|#8364);/i',                     // Euro sign
        '/&[^&;]+;/i',                           // Unknown/unhandled entities
        '/[ ]{2,}/',                              // Runs of spaces, post-handling
    );

    /*
     *  List of pattern replacements corresponding to patterns searched.
     *
     *  @var array $replace
     *  @access public
     *  @see $search
     */
    public $replace = array(
        '',                                     // Non-legal carriage return
        ' ',                                    // Newlines and tabs
        ' ',                                    // Runs of spaces, pre-handling
        '',                                     // <script>s -- which strip_tags supposedly has problems with
        '',                                     // <style>s -- which strip_tags supposedly has problems with
        //'',                                     // Comments -- which strip_tags might have problem a with
        "strtoupper(\"\n\n\\1\n\n\")",          // H1 - H3
        "ucwords(\"\n\n\\1\n\n\")",             // H4 - H6
        "\n\n\t",                               // <P>
        "\n",                                   // <br>
        'strtoupper("\\1")',                    // <b>
        'strtoupper("\\1")',                    // <strong>
        '_\\1_',                                // <i>
        '_\\1_',                                // <em>
        "\n\n",                                 // <ul> and </ul>
        "\n\n",                                 // <ol> and </ol>
        "\t* \\1\n",                            // <li> and </li>
        "\n\t* ",                               // <li>
        '$this->_build_link_list("\\1", "\\2")',
        // <a href="">
        "\n-------------------------\n",        // <hr>
        "\n\n",                                 // <table> and </table>
        "\n",                                   // <tr> and </tr>
        "\t\t\\1\n",                            // <td> and </td>
        "strtoupper(\"\t\t\\1\n\")",            // <th> and </th>
        ' ',                                    // Non-breaking space
        '"',                                    // Double quotes
        "'",                                    // Single quotes
        '>',
        '<',
        '&',
        '(c)',
        '(tm)',
        '(R)',
        '--',
        '-',
        '*',
        '�',
        'EUR',                                  // Euro sign. � ?
        '',                                     // Unknown/unhandled entities
        ' ',                                     // Runs of spaces, post-handling
    );

    /*
     *  Contains a list of HTML tags to allow in the resulting text.
     *
     *  @var string $allowed_tags
     *  @access public
     *  @see set_allowed_tags()
     */
    public $allowed_tags = '';

    /*
     *  Contains the base URL that relative links should resolve to.
     *
     *  @var string $url
     *  @access public
     */
    public $url;

    /*
     *  Indicates whether content in the $html variable has been converted yet.
     *
     *  @var boolean $_converted
     *  @access private
     *  @see $html, $text
     */
    public $_converted = false;

    /*
     *  Contains URL addresses from links to be rendered in plain text.
     *
     *  @var string $_link_list
     *  @access private
     *  @see _build_link_list()
     */
    public $_link_list = '';

    /*
     *  Number of valid links detected in the text, used for plain text
     *  display (rendered similar to footnotes).
     *
     *  @var integer $_link_count
     *  @access private
     *  @see _build_link_list()
     */
    public $_link_count = 0;

    /**
     *  Constructor.
     *
     *  If the HTML source string (or file) is supplied, the class
     *  will instantiate with that source propagated, all that has
     *  to be done it to call get_text().
     *
     *  @param string $source HTML content
     *  @param bool $from_file Indicates $source is a file to pull content from
     */
    public function __construct($source = '', $from_file = false)
    {
        if (!empty($source)) {
            $this->set_html($source, $from_file);
        }
        $this->set_base_url();
    }

    /**
     *  Loads source HTML into memory, either from $source string or a file.
     *
     *  @param string $source HTML content
     *  @param bool $from_file Indicates $source is a file to pull content from
     */
    public function set_html($source, $from_file = false)
    {
        $this->html = $source;

        if ($from_file && file_exists($source)) {
            $fp = fopen($source, 'r');
            $this->html = fread($fp, filesize($source));
            fclose($fp);
        }

        $this->_converted = false;
    }

    /**
     *  Returns the text, converted from HTML.
     *
     *  @return string
     */
    public function get_text()
    {
        if (!$this->_converted) {
            $this->_convert();
        }

        return $this->text;
    }

    /**
     *  Prints the text, converted from HTML.
     */
    public function print_text()
    {
        print $this->get_text();
    }

    /**
     *  Sets the allowed HTML tags to pass through to the resulting text.
     *
     *  Tags should be in the form "<p>", with no corresponding closing tag.
     */
    public function set_allowed_tags($allowed_tags = '')
    {
        if (!empty($allowed_tags)) {
            $this->allowed_tags = $allowed_tags;
        }
    }

    /**
     *  Sets a base URL to handle relative links.
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
     *  Workhorse function that does actual conversion.
     *
     *  First performs custom tag replacement specified by $search and
     *  $replace arrays. Then strips any remaining HTML tags, reduces whitespace
     *  and newlines to a readable format, and word wraps the text to
     *  $width characters.
     */
    public function _convert()
    {
        // Variables used for building the link list
        $this->_link_count = 0;
        $this->_link_list = '';

        $text = trim(stripslashes($this->html));

        // Run our defined search-and-replace
        $text = @preg_replace($this->search, $this->replace, $text);

        // Strip any other HTML tags
        $text = strip_tags($text, $this->allowed_tags);

        // Bring down number of empty lines to 2 max
        $text = preg_replace("/\n\s+\n/", "\n\n", $text);
        $text = preg_replace("/[\n]{3,}/", "\n\n", $text);

        // Add link list
        //if ( !empty($this->_link_list) ) {
        //$text .= "\n\nLinks:\n------\n" . $this->_link_list;
        //}

        // Wrap the text to a readable format
        // for PHP versions >= 4.0.2. Default width is 75
        // If width is 0 or less, don't wrap the text.
        if ($this->width > 0) {
            $text = wordwrap($text, $this->width);
        }

        $this->text = $text;

        $this->_converted = true;
    }

    /**
     *  Helper function called by preg_replace() on link replacement.
     *
     *  Maintains an internal list of links to be displayed at the end of the
     *  text, with numeric indices to the original point in the text they
     *  appeared. Also makes an effort at identifying and handling absolute
     *  and relative links.
     *
     *  @param string $link URL of the link
     *  @param string $display Part of the text to associate number with
     *
     *  @return string
     */
    public function _build_link_list($link, $display)
    {
        if (substr($link, 0, 7) == 'http://' || substr($link, 0, 8) == 'https://' ||
            substr($link, 0, 7) == 'mailto:') {
            ++$this->_link_count;
            $this->_link_list .= '['.$this->_link_count."] $link\n";
            //$additional = ' [' . $this->_link_count . ']';
            $additional = ' ['.$link.']';
        } elseif (substr($link, 0, 11) == 'javascript:') {
            // Don't count the link; ignore it
            $additional = '';
            // what about href="#anchor" ?
        } else {
            ++$this->_link_count;
            $this->_link_list .= '['.$this->_link_count.'] '.$this->url;
            if (substr($link, 0, 1) != '/') {
                $this->_link_list .= '/';
            }
            $this->_link_list .= "$link\n";
            $additional = ' ['.$this->_link_count.']';
        }

        return $display.$additional;
        //return $display;
    }
}
//@codingStandardsIgnoreStop

