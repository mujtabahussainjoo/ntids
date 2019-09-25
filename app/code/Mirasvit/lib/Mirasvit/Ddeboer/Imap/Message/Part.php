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


// @codingStandardsIgnoreFile
// namespace Mirasvit_Ddeboer\Imap\Message;

// use Doctrine\Common\Collections\ArrayCollection;

/**
 * A message part.
 * @SuppressWarnings(PHPMD)
 */
class Mirasvit_Ddeboer_Imap_Message_Part implements RecursiveIterator
{
    const TYPE_TEXT = 'text';
    const TYPE_MULTIPART = 'multipart';
    const TYPE_MESSAGE = 'message';
    const TYPE_APPLICATION = 'application';
    const TYPE_AUDIO = 'audio';
    const TYPE_IMAGE = 'image';
    const TYPE_VIDEO = 'video';
    const TYPE_OTHER = 'other';

    const ENCODING_7BIT = '7bit';
    const ENCODING_8BIT = '8bit';
    const ENCODING_BINARY = 'binary';
    const ENCODING_BASE64 = 'base64';
    const ENCODING_QUOTED_PRINTABLE = 'quoted-printable';
    const ENCODING_UNKNOWN = 'unknown';

    const SUBTYPE_TEXT = 'TEXT';
    const SUBTYPE_HTML = 'HTML';

    /**
     * @var array
     */
    protected $typesMap = [
        0 => self::TYPE_TEXT,
        1 => self::TYPE_MULTIPART,
        2 => self::TYPE_MESSAGE,
        3 => self::TYPE_APPLICATION,
        4 => self::TYPE_AUDIO,
        5 => self::TYPE_IMAGE,
        6 => self::TYPE_VIDEO,
        7 => self::TYPE_OTHER,
    ];

    /**
     * @var array
     */
    protected $encodingsMap = [
        0 => self::ENCODING_7BIT,
        1 => self::ENCODING_8BIT,
        2 => self::ENCODING_BINARY,
        3 => self::ENCODING_BASE64,
        4 => self::ENCODING_QUOTED_PRINTABLE,
        5 => self::ENCODING_UNKNOWN,
    ];

    protected $type;

    protected $subtype;

    protected $encoding;

    protected $bytes;

    protected $lines;

    protected $parameters;

    protected $messageNumber;

    protected $partNumber;

    protected $structure;

    protected $content;

    protected $decodedContent;

    protected $parts = array();

    protected $key = 0;

    protected $disposition;

    /**
     * Constructor.
     *
     * @param \stdClass $part   The part
     * @param string    $number The part number
     */
    public function __construct($stream, $messageNumber, $partNumber = null, $structure = null)
    {
        $this->stream = $stream;
        $this->messageNumber = $messageNumber;
        $this->partNumber = $partNumber;
        $this->structure = $structure;
        $this->parseStructure($structure);
    }

    public function getCharset()
    {
        $charsetHelper = new Mirasvit_Ddeboer_Imap_Charset();
        return $charsetHelper->getCharset($this->parameters->get('charset'));
    }

    public function getType()
    {
        return $this->type;
    }

    public function getSubtype()
    {
        return $this->subtype;
    }

    public function getEncoding()
    {
        return $this->encoding;
    }

    public function getBytes()
    {
        return $this->bytes;
    }

    public function getLines()
    {
        return $this->lines;
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Get raw part content.
     *
     * @return string
     */
    public function getContent()
    {
        if (null === $this->content) {
            $this->content = $this->doGetContent();
        }

        return $this->content;
    }

    /**
     * Get decoded part content.
     *
     * @return string
     */
    public function getDecodedContent()
    {
        if (null === $this->decodedContent) {
            switch ($this->getEncoding()) {
                case self::ENCODING_BASE64:
                    $this->decodedContent = base64_decode($this->getContent());
                    break;

                case self::ENCODING_QUOTED_PRINTABLE:
                    $this->decodedContent = quoted_printable_decode($this->getContent());
                    break;

                case self::ENCODING_7BIT:
                case self::ENCODING_8BIT:
                    $this->decodedContent = $this->getContent();
                    break;

                default:
                    throw new UnexpectedValueException('Cannot decode '.$this->getEncoding());
            }

            // If this part is a text part, try to convert its encoding to UTF-8.
            // We don't want to convert an attachment's encoding.
            if ($this->getType() === self::TYPE_TEXT
                && null !== $this->getCharset()
                && strtolower($this->getCharset()) != 'utf-8'
            ) {
                if (extension_loaded('iconv')) {
                    $this->decodedContent = iconv(
                        $this->getCharset(),
                        'UTF-8//TRANSLIT//IGNORE',
                        $this->decodedContent
                    );
                } else {
                    $this->decodedContent = mb_convert_encoding(
                        $this->decodedContent,
                        'UTF-8'
                    );
                }
            }
        }

        return $this->decodedContent;
    }

    public function getStructure()
    {
        return $this->structure;
    }

    protected function fetchStructure($partNumber = null)
    {
        if (null === $this->structure) {
            $this->loadStructure();
        }

        if ($partNumber) {
            return $this->structure->parts[$partNumber];
        }

        return $this->structure;
    }

    protected function parseStructure(stdClass $structure)
    {
        if (isset($this->typesMap[$structure->type])) {
            $this->type = $this->typesMap[$structure->type];
        } else {
            $this->type = $structure->type;
        }
        if (isset($this->encodingsMap[$structure->encoding])) {
            $this->encoding = $this->encodingsMap[$structure->encoding];
        } else {
            $this->encoding = $structure->encoding;
        }
        $this->subtype = $structure->subtype;

        if (isset($structure->bytes)) {
            $this->bytes = $structure->bytes;
        }

        foreach (array('disposition', 'bytes', 'description') as $optional) {
            if (isset($structure->$optional)) {
                $this->$optional = $structure->$optional;
            }
        }

        $this->parameters = new ArrayCollection();
        foreach ($structure->parameters as $parameter) {
            $this->parameters->set(strtolower($parameter->attribute), $parameter->value);
        }

        if (isset($structure->dparameters)) {
            foreach ($structure->dparameters as $parameter) {
                $this->parameters->set(strtolower($parameter->attribute), $parameter->value);
            }
        }

        if (isset($structure->parts)) {
            foreach ($structure->parts as $key => $partStructure) {
                if (null === $this->partNumber) {
                    $partNumber = ($key + 1);
                } else {
                    $partNumber = (string) ($this->partNumber.'.'.($key + 1));
                }

                if ((isset($partStructure->disposition)
                    && (strtolower($partStructure->disposition) == 'attachment' || strtolower($partStructure->disposition) == 'inline')
                    // && strtoupper ($partStructure->subtype) != "PLAIN"
                    )
                    || (in_array(strtoupper($partStructure->subtype), array('JPEG', 'JPG', 'PNG', 'GIF')))
                    ) {
                    $attachment = new Mirasvit_Ddeboer_Imap_Message_Attachment($this->stream, $this->messageNumber, $partNumber, $partStructure);
                    $this->parts[] = $attachment;
                } else {
                    $this->parts[] = new self($this->stream, $this->messageNumber, $partNumber, $partStructure);
                }
            }
        }
    }

    /**
     * Get an array of all parts for this message.
     *
     * @return self[]
     */
    public function getParts()
    {
        return $this->parts;
    }

    public function current()
    {
        return $this->parts[$this->key];
    }

    public function getChildren()
    {
        return $this->current();
    }

    public function hasChildren()
    {
        return count($this->parts) > 0;
    }

    public function key()
    {
        return $this->key;
    }

    public function next()
    {
        ++$this->key;
    }

    public function rewind()
    {
        $this->key = 0;
    }

    public function valid()
    {
        return isset($this->parts[$this->key]);
    }

    public function getDisposition()
    {
        return $this->disposition;
    }

    /**
     * Get raw message content.
     *
     * @param bool $keepUnseen Whether to keep the message unseen.
     *                         Default behaviour is set set the seen flag when
     *                         getting content.
     *
     * @return string
     */
    protected function doGetContent($keepUnseen = false)
    {
        $c = imap_fetchbody(
            $this->stream,
            $this->messageNumber,
            $this->partNumber ? $this->partNumber : 1,
            ($keepUnseen ? FT_PEEK : null)
        );

        return $c;
    }
}

class ArrayCollection
//implements Collection, Selectable
{
    /**
     * An array containing the entries of this collection.
     *
     * @var array
     */
    private $_elements;

    /**
     * Initializes a new ArrayCollection.
     *
     * @param array $elements
     */
    public function __construct(array $elements = array())
    {
        $this->_elements = $elements;
    }

    /**
     * {@inheritDoc}
     */
    public function toArray()
    {
        return $this->_elements;
    }

    /**
     * {@inheritDoc}
     */
    public function first()
    {
        return reset($this->_elements);
    }

    /**
     * {@inheritDoc}
     */
    public function last()
    {
        return end($this->_elements);
    }

    /**
     * {@inheritDoc}
     */
    public function key()
    {
        return key($this->_elements);
    }

    /**
     * {@inheritDoc}
     */
    public function next()
    {
        return next($this->_elements);
    }

    /**
     * {@inheritDoc}
     */
    public function current()
    {
        return current($this->_elements);
    }

    /**
     * {@inheritDoc}
     */
    public function remove($key)
    {
        if (isset($this->_elements[$key]) || array_key_exists($key, $this->_elements)) {
            $removed = $this->_elements[$key];
            unset($this->_elements[$key]);

            return $removed;
        }

        return;
    }

    /**
     * {@inheritDoc}
     */
    public function removeElement($element)
    {
        $key = array_search($element, $this->_elements, true);

        if ($key !== false) {
            unset($this->_elements[$key]);

            return true;
        }

        return false;
    }

    /**
     * Required by interface ArrayAccess.
     *
     * {@inheritDoc}
     */
    public function offsetExists($offset)
    {
        return $this->containsKey($offset);
    }

    /**
     * Required by interface ArrayAccess.
     *
     * {@inheritDoc}
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Required by interface ArrayAccess.
     *
     * {@inheritDoc}
     */
    public function offsetSet($offset, $value)
    {
        if (!isset($offset)) {
            return $this->add($value);
        }

        return $this->set($offset, $value);
    }

    /**
     * Required by interface ArrayAccess.
     *
     * {@inheritDoc}
     */
    public function offsetUnset($offset)
    {
        return $this->remove($offset);
    }

    /**
     * {@inheritDoc}
     */
    public function containsKey($key)
    {
        return isset($this->_elements[$key]) || array_key_exists($key, $this->_elements);
    }

    /**
     * {@inheritDoc}
     */
    public function contains($element)
    {
        return in_array($element, $this->_elements, true);
    }

    /**
     * {@inheritDoc}
     */
    public function exists(Closure $p)
    {
        foreach ($this->_elements as $key => $element) {
            if ($p($key, $element)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function indexOf($element)
    {
        return array_search($element, $this->_elements, true);
    }

    /**
     * {@inheritDoc}
     */
    public function get($key)
    {
        if (isset($this->_elements[$key])) {
            return $this->_elements[$key];
        }

        return;
    }

    /**
     * {@inheritDoc}
     */
    public function getKeys()
    {
        return array_keys($this->_elements);
    }

    /**
     * {@inheritDoc}
     */
    public function getValues()
    {
        return array_values($this->_elements);
    }

    /**
     * {@inheritDoc}
     */
    public function count()
    {
        return count($this->_elements);
    }

    /**
     * {@inheritDoc}
     */
    public function set($key, $value)
    {
        $this->_elements[$key] = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function add($value)
    {
        $this->_elements[] = $value;

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function isEmpty()
    {
        return !$this->_elements;
    }

    /**
     * Required by interface IteratorAggregate.
     *
     * {@inheritDoc}
     */
    public function getIterator()
    {
        return new ArrayIterator($this->_elements);
    }

    // /**
    //  * {@inheritDoc}
    //  */
    // public function map(Closure $func)
    // {
    //     return new static(array_map($func, $this->_elements));
    // }

    // /**
    //  * {@inheritDoc}
    //  */
    // public function filter(Closure $p)
    // {
    //     return new static(array_filter($this->_elements, $p));
    // }

    // /**
    //  * {@inheritDoc}
    //  */
    // public function forAll(Closure $p)
    // {
    //     foreach ($this->_elements as $key => $element) {
    //         if ( ! $p($key, $element)) {
    //             return false;
    //         }
    //     }

    //     return true;
    // }

    // /**
    //  * {@inheritDoc}
    //  */
    // public function partition(Closure $p)
    // {
    //     $coll1 = $coll2 = array();
    //     foreach ($this->_elements as $key => $element) {
    //         if ($p($key, $element)) {
    //             $coll1[$key] = $element;
    //         } else {
    //             $coll2[$key] = $element;
    //         }
    //     }
    //     return array(new static($coll1), new static($coll2));
    // }

    /**
     * Returns a string representation of this object.
     *
     * @return string
     */
    public function __toString()
    {
        return __CLASS__.'@'.spl_object_hash($this);
    }

    /**
     * {@inheritDoc}
     */
    public function clear()
    {
        $this->_elements = array();
    }

    /**
     * {@inheritDoc}
     */
    public function slice($offset, $length = null)
    {
        return array_slice($this->_elements, $offset, $length, true);
    }

    // /**
    //  * {@inheritDoc}
    //  */
    // public function matching(Criteria $criteria)
    // {
    //     $expr     = $criteria->getWhereExpression();
    //     $filtered = $this->_elements;

    //     if ($expr) {
    //         $visitor  = new ClosureExpressionVisitor();
    //         $filter   = $visitor->dispatch($expr);
    //         $filtered = array_filter($filtered, $filter);
    //     }

    //     if ($orderings = $criteria->getOrderings()) {
    //         $next = null;
    //         foreach (array_reverse($orderings) as $field => $ordering) {
    //             $next = ClosureExpressionVisitor::sortByField($field, $ordering == 'DESC' ? -1 : 1, $next);
    //         }

    //         usort($filtered, $next);
    //     }

    //     $offset = $criteria->getFirstResult();
    //     $length = $criteria->getMaxResults();

    //     if ($offset || $length) {
    //         $filtered = array_slice($filtered, (int)$offset, $length);
    //     }

    //     return new static($filtered);
    // }
}
