<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2015 ThemePunch
 */

namespace Nwdthemes\Revslider\Model\Revslider\Framework;

use \Nwdthemes\Revslider\Helper\Data;
use \Nwdthemes\Revslider\Helper\Query;
use \Magento\Framework\App\ResourceConnection;

class RevSliderDB {

    protected $_query;
    protected $_resource;
	
	private $lastRowID;
	
	/**
	 * 
	 * constructor - set database object
	 */
	public function __construct(
        \Nwdthemes\Revslider\Helper\Query $query,
		\Magento\Framework\App\ResourceConnection $resource
	) {
        $this->_query = $query;
        $this->_resource = $resource;
	}

	/**
	 * 
	 * throw error
	 */
	private function throwError($message,$code=-1){
		RevSliderFunctions::throwError($message,$code);
	}
	
	//------------------------------------------------------------
	// validate for errors
	private function checkForErrors($prefix = ""){
		$wpdb = $this->_query;
		
		if($wpdb->last_error !== ''){
			$query = $wpdb->last_query;
			$message = $wpdb->last_error;
			
			if($prefix) $message = $prefix.' - <b>'.$message.'</b>';
            if($query) $message .=  '<br>---<br> Query: ' . $this->_framework->esc_attr($query);
			
			$this->throwError($message);
		}
	}
	
	
	/**
	 * 
	 * insert variables to some table
	 */
	public function insert($table,$arrItems) {

		$model = $this->_query->getFactory($table)->create();
		$model->setData($arrItems);
		try {
			$model->save();
		} catch (\Exception $e) {
            Data::logException($e);
			$this->throwError($e->getMessage());
		}

		$this->lastRowID = $model->getId();

		return $this->lastRowID;
	}
	
	/**
	 * 
	 * get last insert id
	 */
	public function getLastInsertID(){
		$wpdb = $this->_query;

		$this->lastRowID = $wpdb->insert_id;
		return($this->lastRowID);
	}


	/**
	 *
	 * delete rows
	 */
	public function delete($table,$where){

		RevSliderFunctions::validateNotEmpty($table,"table name");
		RevSliderFunctions::validateNotEmpty($where,"where");

		list($field, $value) = explode('=', $where);
		$collection = $this->_query->getFactory($table)->create()->getCollection();
		$collection->addFieldToFilter(trim($field, '"\' '), trim($value, '"\' '));
		foreach ($collection as $_item) {
			$_item->delete();
		}
	}


	/**
	 *
	 * run some sql query
	 */
	public function runSql($query){
		$wpdb = $this->_query;

		$wpdb->query($query);
		$this->checkForErrors("Regular query error");
	}


	/**
	 * 
     * run some sql query
     */
    public function runSqlR($query){
        $wpdb = $this->_query;
        
        $return = $wpdb->get_results($query, Query::ARRAY_A);
        
        return $return;
    }
    
    
    /**
     * 
	 * insert variables to some table
	 */
	public function update($table,$arrItems,$where){

		if (is_array($where) && $where)
		{
			$collection = $this->_query->getFactory($table)->create()->getCollection();
			foreach ($where as $_field => $_value) {
				$collection->addFieldToFilter($_field, $_value);
			}
			$item = $collection->getFirstItem();
			try {
				$item
					->addData($arrItems)
					->setId( $item->getId() )
					->save();
			} catch (\Exception $e) {
                Data::logException($e);
				$this->throwError($e->getMessage());
			}
		}
		else
		{
			$this->throwError('No id provided.');
		}

		return true;
	}
	
	
	/**
	 * 
	 * get data array from the database
	 * 
	 */
	public function fetch($tableName,$where="",$orderField="",$groupByField="",$sqlAddon=""){

		$query = 'SELECT * FROM `' . $this->_resource->getTableName($tableName) . '`';
		if ($where)
		{
			$query .= ' WHERE ' . $where;
		}
		if ($orderField)
		{
			$query .= ' ORDER BY ' . $orderField;
		}

		$result = $this->_resource->getConnection('core_read')->fetchAll($query);
		
		return $result;
	}
	
	/**
	 * 
	 * fetch only one item. if not found - throw error
	 */
	public function fetchSingle($tableName,$where="",$orderField="",$groupByField="",$sqlAddon=""){
		$response = $this->fetch($tableName, $where, $orderField, $groupByField, $sqlAddon);
		$record = $response ? $response[0] : false;
		return $record;
	}
	
	/**
	 * 
     * prepare statement to avoid sql injections
	 */
    public function prepare($query, $array){
        $wpdb = $this->_query;

        $query = $wpdb->prepare($query, $array);

        return($query);
	}
	
}
