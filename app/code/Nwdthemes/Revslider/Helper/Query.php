<?php

namespace Nwdthemes\Revslider\Helper;

use \Nwdthemes\Revslider\Helper\Data;
use \Nwdthemes\Revslider\Model\Revslider\Framework\RevSliderFunctions;

class Query extends \Magento\Framework\App\Helper\AbstractHelper {

    const ARRAY_A = 'ARRAY_A';

    protected $_context;
    protected $_resource;
    protected $_animationFactory;
    protected $_backupFactory;
    protected $_cssFactory;
    protected $_navigationFactory;
    protected $_slideFactory;
    protected $_sliderFactory;
    protected $_staticSlideFactory;

    public $prefix = '';
    public $base_prefix = '';
    public $last_error = '';
    public $last_query = '';
    public $insert_id = '';

    /**
     *	Constructor
     */

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\ResourceConnection $resource,
        \Nwdthemes\Revslider\Model\AnimationFactory $animationFactory,
        \Nwdthemes\Revslider\Model\BackupFactory $backupFactory,
        \Nwdthemes\Revslider\Model\CssFactory $cssFactory,
        \Nwdthemes\Revslider\Model\NavigationFactory $navigationFactory,
        \Nwdthemes\Revslider\Model\SlideFactory $slideFactory,
        \Nwdthemes\Revslider\Model\SliderFactory $sliderFactory,
        \Nwdthemes\Revslider\Model\StaticSlideFactory $staticSlideFactory
    ) {
        $this->_context = $context;
        $this->_resource = $resource;
        $this->_animationFactory = $animationFactory;
        $this->_backupFactory = $backupFactory;
        $this->_cssFactory = $cssFactory;
        $this->_navigationFactory = $navigationFactory;
        $this->_slideFactory = $slideFactory;
        $this->_sliderFactory = $sliderFactory;
        $this->_staticSlideFactory = $staticSlideFactory;

        parent::__construct($this->_context);
    }

    /**
     *	Get query results
     *
     *	@param	string	Query
     *	@param	string	Result format
     *	@return	array
     */
    public function get_results($query, $mode = self::ARRAY_A) {

        $queryArray = explode('FROM', $query);
        if (count($queryArray) < 2) {
            die();
        }
        $queryArray = explode('WHERE', $queryArray[1]);
        $table = trim($queryArray[0]);
        $collection = $this->getFactory($table)->create()->getCollection();

        $where = isset($queryArray[1]) ? trim($queryArray[1]) : '';
        if ($where) {
            if (strpos($where, '!=') === false) {
                list($field, $value) = explode('=', $where);
                $collection->addFieldToFilter(trim($field, '`"\' '), trim($value, '"\' '));
            } else {
                list($field, $value) = explode('!=', $where);
                $collection->addFieldToFilter(trim($field, '`"\' '), array('neq' => trim($value, '"\' ')));
            }
        }

        $response = array();
        foreach ($collection as $_item) {
            $response[] = $_item->getData();
        }
        return $response;
    }

    /**
     *	Get query row
     *
     *	@param	string	Query
     *	@param	string	Result format
     *	@return	array
     */
    public function get_row($query, $mode = self::ARRAY_A) {
        preg_match('#\b(nwdthemes_revslider_\w+)\b#', $query,	$modelNames);
        $modelNames = array_unique($modelNames);
        foreach ($modelNames as $modelName) {
            $query = str_replace($modelName, $this->_resource->getTableName($modelName), $query);
        }
        $readConnection = $this->_resource->getConnection('core_read');
        $result = $readConnection->fetchRow($query);
        return $mode == self::ARRAY_A ? $result : (object) $result;
    }

    /**
     *	Insert row
     *
     *	@param	string	Table name
     *	@param	array	Data
     *	@return	int
     */

    public function insert($table, $data = array()) {
        $model = $this->getFactory($table)->create();
        $model->setData($data);
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
     *	Update row
     *
     *	@param	string	Table name
     *	@param	array	Data
     *	@param	array	Where
     */

    public function update($table, $data = array(), $where) {
        if (is_array($where) && $where) {
            $collection = $this->getFactory($table)->create()->getCollection();
            foreach ($where as $_field => $_value) {
                $collection->addFieldToFilter($_field, $_value);
            }
            $item = $collection->getFirstItem();
            try {
                $item
                    ->addData($data)
                    ->setId( $item->getId() )
                    ->save();
            } catch (\Exception $e) {
                Data::logException($e);
                $this->throwError($e->getMessage());
            }
        } else {
            $this->throwError('No id provided.');
        }
        return true;
    }

    /**
     *	Delete row
     *
     *	@param	string	Table name
     *	@param	array	Data
     *	@param	array	Where
     */

    public function delete($table, $where) {
        RevSliderFunctions::validateNotEmpty($table,"table name");
        RevSliderFunctions::validateNotEmpty($where,"where");
        $collection = $this->getFactory($table)->create()->getCollection();
        foreach ($where as $field => $value) {
            $collection->addFieldToFilter($field, $value);
        }
        foreach ($collection as $_item) {
            $_item->delete();
        }
    }

    /**
     *	Prepare query
     *
     *	@param	string	Query
     *	@param	mixed	Args
     *	@return	array
     */
    public function prepare($query, $args) {
        $args = func_get_args();
        array_shift( $args );
        // If args were passed as an array (as in vsprintf), move them up
        if ( isset( $args[0] ) && is_array($args[0]) )
            $args = $args[0];
        $query = str_replace( "'%s'", '%s', $query ); // in case someone mistakenly already singlequoted it
        $query = str_replace( '"%s"', '%s', $query ); // doublequote unquoting
        $query = preg_replace( '|(?<!%)%f|' , '%F', $query ); // Force floats to be locale unaware
        $query = preg_replace( '|(?<!%)%s|', "%s", $query ); // quote the strings, avoiding escaped strings like %%s
        array_walk( $args, array( $this, 'escape_by_ref' ) );
        return @vsprintf( $query, $args );
    }

    public function escape_by_ref(&$arg) {
        if( (string)(int)$arg != $arg) $arg = $this->_resource->getConnection('core_write')->quote($arg);
    }

    /**
     *	Run sql query
     *
     *	param	string	Query
     */

    public function query($query) {
        preg_match('#\b(nwdthemes_revslider_\w+)\b#', $query,	$modelNames);
        $modelNames = array_unique($modelNames);
        foreach ($modelNames as $modelName) {
            $query = str_replace($modelName, $this->_resource->getTableName($modelName), $query);
        }
        $writeConnection = $this->_resource->getConnection('core_write');
        $result = $writeConnection->query($query);
        $this->insert_id = $writeConnection->lastInsertId();
        return $result;
    }

    /**
     *	Run sql query and get result variable
     *
     *	param	string	Query
     *	return	var
     */

    public function get_var($query) {
        preg_match('#\b(nwdthemes_revslider_\w+)\b#', $query,	$modelNames);
        $modelNames = array_unique($modelNames);
        foreach ($modelNames as $modelName) {
            $query = str_replace($modelName, $this->_resource->getTableName($modelName), $query);
        }
        $readConnection = $this->_resource->getConnection('core_read');
        return $readConnection->fetchOne($query);
    }

    /**
     *	Generate DB alter query by difference
     *
     *	@param  string  Query
     *	@param  boolean Is Execute
     *	@return string
     */

    public function dbDelta($sql, $isExecute = true) {
        return false;
    }

    /**
     *  Get model factory by name
     *
     *  @param  string  Name
     *  @return object
     */

    public function getFactory($name) {
        switch ($name) {
            case 'nwdthemes_revslider_animations' :
                $factory = $this->_animationFactory;
                break;
            case 'nwdthemes_revslider_backup' :
                $factory = $this->_backupFactory;
                break;
            case 'nwdthemes_revslider_css' :
                $factory = $this->_cssFactory;
                break;
            case 'nwdthemes_revslider_navigations' :
                $factory = $this->_navigationFactory;
                break;
            case 'nwdthemes_revslider_slides' :
                $factory = $this->_slideFactory;
                break;
            case 'nwdthemes_revslider_sliders' :
                $factory = $this->_sliderFactory;
                break;
            case 'nwdthemes_revslider_static_slides' :
                $factory = $this->_staticSlideFactory;
                break;
        }
        return $factory;
    }

}