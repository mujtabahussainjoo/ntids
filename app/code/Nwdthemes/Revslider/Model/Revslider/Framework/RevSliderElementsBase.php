<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2015 ThemePunch
 */
 
namespace Nwdthemes\Revslider\Model\Revslider\Framework;

class RevSliderElementsBase {
	
    protected $_query;
    protected $_resource;

	protected $db;
	
	public function __construct(
        \Nwdthemes\Revslider\Helper\Query $query,
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->_query = $query;
        $this->_resource = $resource;

		$this->db = new RevSliderDB($this->_query, $this->_resource);
	}
	
}

