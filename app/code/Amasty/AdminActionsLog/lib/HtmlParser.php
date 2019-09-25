<?php
/**
 * @author Niels A.D.
 * @package Ganon
 * @link http://code.google.com/p/ganon/
 * @license http://dev.perl.org/licenses/artistic.html Artistic License
 */

namespace Amasty\AdminActionsLog\lib;

include_once 'HtmlParserBase.php';
include_once 'HtmlNode.php';

/**
 * Parses a HTML document into a HTML DOM
 */
class HtmlParser extends HtmlParserBase {

	/**
	 * Root object
	 * @internal If string, then it will create a new instance as root
	 * @var HtmlNode
	 */
	var $root = 'HtmlNode';

	/**
	 * Current parsing hierarchy
	 * @internal Root is always at index 0, current tag is at the end of the array
	 * @var array
	 * @access private
	 */
	var $hierarchy = array();

	/**
	 * Tags that don't need closing tags
	 * @var array
	 * @access private
	 */
	var	$tags_selfclose = array(
		'area'		=> true,
		'base'		=> true,
		'basefont'	=> true,
		'br'		=> true,
		'col'		=> true,
		'command'	=> true,
		'embed'		=> true,
		'frame'		=> true,
		'hr'		=> true,
		'img'		=> true,
		'input'		=> true,
		'ins'		=> true,
		'keygen'	=> true,
		'link'		=> true,
		'meta'		=> true,
		'param'		=> true,
		'source'	=> true,
		'track'		=> true,
		'wbr'		=> true
	);

	/**
	 * Class constructor
	 * @param string $doc Document to be tokenized
	 * @param int $pos Position to start parsing
	 * @param HtmlNode $root Root node, null to auto create
	 */
	function __construct($doc = '', $pos = 0, $root = null) {
		if ($root === null) {
			$root = new HtmlNode('~root~', null);
		}
		$this->root =& $root;

		parent::__construct($doc, $pos);
	}
	
	#php4 PHP4 class constructor compatibility
	#function HtmlParser($doc = '', $pos = 0, $root = null) {return $this->__construct($doc, $pos, $root);}
	#php4e

	/**
	 * Class magic invoke method, performs {@link select()}
	 * @return array
	 * @access private
	 */
	function __invoke($query = '*') {
		return $this->select($query);
	}

	/**
	 * Class magic toString method, performs {@link HTML_Node::toString()}
	 * @return string
	 * @access private
	 */
	function __toString() {
		return $this->root->getInnerText();
	}

	/**
	 * Performs a css select query on the root node
	 * @see HTML_Node::select()
	 * @return array
	 */
	function select($query = '*', $index = false, $recursive = true, $check_self = false) {
		return $this->root->select($query, $index, $recursive, $check_self);
	}

	/**
	 * Updates the current hierarchy status and checks for
	 * correct opening/closing of tags
	 * @param bool $self_close Is current tag self closing? Null to use {@link tags_selfclose}
	 * @internal This is were most of the nodes get added
	 * @access private
	 */
	protected function parse_hierarchy($self_close = null) {
		if ($self_close === null) {
			$this->status['self_close'] = ($self_close = isset($this->tags_selfclose[strtolower($this->status['tag_name'])]));
		}

		if ($self_close) {
			if ($this->status['closing_tag']) {
			
				//$c = end($this->hierarchy)->children
				$c = $this->hierarchy[count($this->hierarchy) - 1]->children;
				$found = false;
				for ($count = count($c), $i = $count - 1; $i >= 0; $i--) {
					if (strcasecmp($c[$i]->tag, $this->status['tag_name']) === 0) {
						for($ii = $i + 1; $ii < $count; $ii++) {
							$index = null; //Needs to be passed by ref
							$c[$i + 1]->changeParent($c[$i], $index);
						}
						$c[$i]->self_close = false;
						
						$found = true;
						break;
					}
				}
				
				if (!$found) {
					$this->addError('Closing tag "'.$this->status['tag_name'].'" which is not open');
				}
			
			} elseif ($this->status['tag_name'][0] === '?') {
				//end($this->hierarchy)->addXML($this->status['tag_name'], '', $this->status['attributes']);
				$index = null; //Needs to be passed by ref
				$this->hierarchy[count($this->hierarchy) - 1]->addXML($this->status['tag_name'], '', $this->status['attributes'], $index);
			} elseif ($this->status['tag_name'][0] === '%') {
				//end($this->hierarchy)->addASP($this->status['tag_name'], '', $this->status['attributes']);
				$index = null; //Needs to be passed by ref
				$this->hierarchy[count($this->hierarchy) - 1]->addASP($this->status['tag_name'], '', $this->status['attributes'], $index);
			} else {
				//end($this->hierarchy)->addChild($this->status);
				$index = null; //Needs to be passed by ref
				$this->hierarchy[count($this->hierarchy) - 1]->addChild($this->status, $index);
			}
		} elseif ($this->status['closing_tag']) {
			$found = false;
			for ($count = count($this->hierarchy), $i = $count - 1; $i >= 0; $i--) {
				if (strcasecmp($this->hierarchy[$i]->tag, $this->status['tag_name']) === 0) {

					for($ii = ($count - $i - 1); $ii >= 0; $ii--) {
						$e = array_pop($this->hierarchy);
						if ($ii > 0) {
							$this->addError('Closing tag "'.$this->status['tag_name'].'" while "'.$e->tag.'" is not closed yet');
						}
					}

					$found = true;
					break;
				}
			}

			if (!$found) {
				$this->addError('Closing tag "'.$this->status['tag_name'].'" which is not open');
			}

		} else {
			//$this->hierarchy[] = end($this->hierarchy)->addChild($this->status);
			$index = null; //Needs to be passed by ref
			$this->hierarchy[] = $this->hierarchy[count($this->hierarchy) - 1]->addChild($this->status, $index);	
		}
	}

	function parse_cdata() {
		if (!parent::parse_cdata()) {return false;}

		//end($this->hierarchy)->addCDATA($this->status['cdata']);
		$index = null; //Needs to be passed by ref
		$this->hierarchy[count($this->hierarchy) - 1]->addCDATA($this->status['cdata'], $index);
		return true;
	}

	function parse_comment() {
		if (!parent::parse_comment()) {return false;}

		//end($this->hierarchy)->addComment($this->status['comment']);
		$index = null; //Needs to be passed by ref
		$this->hierarchy[count($this->hierarchy) - 1]->addComment($this->status['comment'], $index);
		return true;
	}

	function parse_conditional() {
		if (!parent::parse_conditional()) {return false;}

		if ($this->status['comment']) {
			//$e = end($this->hierarchy)->addConditional($this->status['tag_condition'], true);
			$index = null; //Needs to be passed by ref
			$e = $this->hierarchy[count($this->hierarchy) - 1]->addConditional($this->status['tag_condition'], true, $index);
			if ($this->status['text'] !== '') {
				$index = null; //Needs to be passed by ref
				$e->addText($this->status['text'], $index);
			}
		} else {
			if ($this->status['closing_tag']) {
				$this->parse_hierarchy(false);
			} else {
				//$this->hierarchy[] = end($this->hierarchy)->addConditional($this->status['tag_condition'], false);
				$index = null; //Needs to be passed by ref
				$this->hierarchy[] = $this->hierarchy[count($this->hierarchy) - 1]->addConditional($this->status['tag_condition'], false, $index);
			}
		}

		return true;
	}

	function parse_doctype() {
		if (!parent::parse_doctype()) {return false;}

		//end($this->hierarchy)->addDoctype($this->status['dtd']);
		$index = null; //Needs to be passed by ref
		$this->hierarchy[count($this->hierarchy) - 1]->addDoctype($this->status['dtd'], $index);
		return true;
	}

	function parse_php() {
		if (!parent::parse_php()) {return false;}

		//end($this->hierarchy)->addXML('php', $this->status['text']);
		$index = null; //Needs to be passed by ref
		$this->hierarchy[count($this->hierarchy) - 1]->addXML('php', $this->status['text'], $index);
		return true;
	}

	function parse_asp() {
		if (!parent::parse_asp()) {return false;}

		//end($this->hierarchy)->addASP('', $this->status['text']);
		$index = null; //Needs to be passed by ref
		$this->hierarchy[count($this->hierarchy) - 1]->addASP('', $this->status['text'], $index);
		return true;
	}

	function parse_script() {
		if (!parent::parse_script()) {return false;}

		//$e = end($this->hierarchy)->addChild($this->status);
		$index = null; //Needs to be passed by ref
		$e = $this->hierarchy[count($this->hierarchy) - 1]->addChild($this->status, $index);
		if ($this->status['text'] !== '') {
			$index = null; //Needs to be passed by ref
			$e->addText($this->status['text'], $index);
		}
		return true;
	}

	function parse_style() {
		if (!parent::parse_style()) {return false;}

		//$e = end($this->hierarchy)->addChild($this->status);
		$index = null; //Needs to be passed by ref
		$e = $this->hierarchy[count($this->hierarchy) - 1]->addChild($this->status, $index);
		if ($this->status['text'] !== '') {
			$index = null; //Needs to be passed by ref
			$e->addText($this->status['text'], $index);
		}
		return true;
	}

	function parse_tag_default() {
		if (!parent::parse_tag_default()) {return false;}

		$this->parse_hierarchy(($this->status['self_close']) ? true : null);
		return true;
	}

	function parse_text() {
		parent::parse_text();
		if ($this->status['text'] !== '') {
			//end($this->hierarchy)->addText($this->status['text']);
			$index = null; //Needs to be passed by ref
			$this->hierarchy[count($this->hierarchy) - 1]->addText($this->status['text'], $index);
		}
	}

	function parse_all() {
		$this->hierarchy = array(&$this->root);
		return ((parent::parse_all()) ? $this->root : false);
	}
}
