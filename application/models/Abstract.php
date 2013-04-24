<?php
/**
 * @file		Abstract.php
 *
 * @category    Compassites_Model
 * @package    	Compassites_Model_DbTable
 * @author     	Compassites Team
 * @copyright  	Copyright (c) 2011 Compassites (http://www.compassitesinc.com)
 *
 * @version		SVN: $Id: $
 */

/**
 * @brief 		Abstract Model class
 *
 * @package    	Compassites_Model_DbTable
 * @class		Compassites_Model_DbTable_Abstract
 * @see
 */
abstract class Compassites_Model_Abstract
{
    /**
     * @brief	dbtable handle
     *
     * @var 	object
     */
	protected $_dbTable;

    /**
     * @brief	set the dbtable handle
     *
     * @param  	string database table
     * @return 	void
     */
    public function setDbTable($dbTable)
    {
        if (is_string($dbTable))
        {
            $dbTable = new $dbTable();
        }
        if (!$dbTable instanceof Zend_Db_Table_Abstract)
        {
            throw new Exception('Invalid table data gateway provided');
        }

        $this->_dbTable = $dbTable;
        # Log file Init here
        $this->logger = Zend_Registry::get('Zend_Log');
    }

    /**
     * @brief	get the dbtable handle
     *
     * @return 	object
     */
    public function getDbTable()
    {
        return $this->_dbTable;
    }

	/**
     * @brief	Insert new record
     *
     * @param  	array insert data array
     * @return 	int   insert id
     */
	public function insert(array $data)
	{
		$insertId 	= 0;
		$insertData	= $this->getValidColumns($data);
		try
		{
			$insertId	= $this->getDbTable()->insert($insertData);
		}
		catch(Exception $e )
		{
			$this->logger->log($e->getMessage(), Zend_Log::ERR);
		}

		return $insertId;
	}

    /**
     * @brief	Update record
     *
     * @param  	array  	update data (primarykey is must to update data)
     * @param 	boolean	disable cache [true]
     * @return 	int		no of affected/updated rows
     */
	public function update(array $data, $cleanCache=true)
	{
		$dbTable		= $this->getDbTable();
		$updatedRows 	= 0;
		$whereArray		= array();
		$primaryKeyName	= $this->getPrimaryKey();
		$updateData		= $this->getValidColumns($data);

		if( !empty($updateData[$primaryKeyName]) )
		{
			# update by primary key
			$primaryKey = (int) $updateData[$primaryKeyName];
			unset($updateData[$primaryKeyName]);

			$whereArray = array($primaryKeyName . ' = ?' => $primaryKey);
		}

		# only update if primarykeyId is set
		if( !empty($whereArray) )
		{
			try
			{
				$updatedRows = $dbTable->update($updateData, $whereArray);
			}
			catch(Exception $e)
			{
				$this->logger->log($e->getMessage(), Zend_Log::ERR);
			}

			# clean up cache
			if( $cleanCache )
			{
				$this->get($primaryKey, true);
			}
		}

		return $updatedRows;
	}


	/**
     * @brief	Delete new record
     *
     * @param  	array key value pair to delete with
     * @return 	int   Affected rows
     */
	public function delete($primaryKeyId)
	{
		$dbTable		= $this->getDbTable();
		$affectedRows 	= 0;
		$whereArray		= array();
		$primaryKeyName	= $this->getPrimaryKey();
		$primaryKeyId 	= (int) $primaryKeyId;

		if( !empty($primaryKeyId) )
		{
			$whereArray = array($primaryKeyName . ' = ?' => $primaryKeyId);
		}

		# only update if primarykeyId value is set
		if( !empty($whereArray) )
		{
			try
			{
				$affectedRows	= $dbTable->delete($whereArray);
			}
			catch(Exception $e )
			{
				$this->logger->log($e->getMessage(), Zend_Log::ERR);
			}
		}

		return $affectedRows;
	}

    /**
     * @brief	Get record
     *
     * @param  	int  	primarykey based get record
     * @param 	boolean	disable cache [false]
     * @return 	Object
     */
	public function get($primaryKey, $noCache=false)
    {
    	$dbTable 	= $this->getDbTable();
		$primaryKey	= (int) $primaryKey;
		$cacheKey	= APPLICATION_NAME . get_class($dbTable) . '_get_' . $primaryKey;
		$cache		= Zend_Registry::get('cache');
		$data		= array();

		if( (!$data = $cache->load($cacheKey)) || ($noCache == true) )
		{
			$select = $dbTable->select()->where( $this->getPrimaryKey() . ' = ?', $primaryKey);
			$data 	= $dbTable->fetchRow($select)->toArray();
			$cache->save($data, $cacheKey);
		}

		return $data;
    }

	/**
     * @brief	Get All Records
     *
     * @param 	int			no of records to fetch [100]
     * @param 	boolean		disable cache [false]
     * @return 	Object
     */
	public function getAll($limit=100, $offset=0, $order='ASC', $orderBy='', $noCache=false)
    {
    	$dbTable 		= $this->getDbTable();
    	$primaryKeyName = $this->getPrimaryKey();

		$cacheKey		= APPLICATION_NAME . get_class($dbTable) . "_getAll_{$limit}_{$offset}_{$order}_{$orderBy}";
		$cache			= Zend_Registry::get('cache');
		$data			= array();

		if( (!$data = $cache->load($cacheKey)) || ($noCache == true) )
		{
			$select = $dbTable->select();
			$select->limit($limit, $offset);
			if(!empty($orderBy))
			{
				$select->order("{$orderBy} {$order}");
			}
			$rawData= $dbTable->fetchAll($select)->toArray();

			foreach ($rawData as $value)
			{
				$data[$value[$primaryKeyName]] = $value;
			}

			$cache->save($data, $cacheKey);
		}

		return $data;
    }

	/**
     * @brief	Get the count of rows in the table
     *
     * @return 	Array 	count
     */
	public function getCount( $whereArray=array(), $noCache=false )
    {
    	$dbTable 	= $this->getDbTable();
		$cacheKey	= APPLICATION_NAME . get_class($dbTable) . '_getCount';
		$cache		= Zend_Registry::get('cache');
		$count 		= 0;
		$where		= '1';

		if( (!$count = $cache->load($cacheKey)) || ($noCache == true) )
		{
			$select = $dbTable->select();
			# form the where clause using the fieldArray
			foreach($whereArray as $key => $value)
			{
				$where .= " AND {$key} = '{$value}'";
			}
			$select->where($where);
			$count  = $dbTable->getAdapter()->query($select)->rowCount();

			$cache->save($count, $cacheKey);
		}

		return $count;
    }

	/**
     * @brief	Get All Records by field in the where clause
     *
     * @param 	array		key value pair to fetch with
     * @param 	int 		count of records be fetched [100]
     * @param 	boolean		disable cache
     * @return 	array		result set array
	*/
	public function getAllByField(array $fieldArray, $limit=100, $offset=0, $order='ASC', $orderBy='', $noCache=false)
	{
		$dbTable 		= $this->getDbTable();
		$primaryKeyName = $this->getPrimaryKey();
		$data 			= array();

		$where			= '1';
		# form the where clause using the fieldArray
		foreach($fieldArray as $key => $value)
		{
			$where .= " AND {$key} = '{$value}'";
		}

		$cacheKey 	= APPLICATION_NAME . get_class($dbTable) . '_getAllByField_' . md5($where) .'_' . $limit .'_'. $offset .'_'. $order .'_'.$orderBy;
		$cache 		= Zend_Registry::get('cache');

		if( (!$data = $cache->load($cacheKey)) || ($noCache == true) )
		{
			$data	= array(); # reset the data array again as we are re-using the variable
			$select = $dbTable->select();
			$select->limit($limit, $offset);
			if(!empty($orderBy))
			{
				$select->order("{$orderBy} {$order}");
			}
			$select->where($where);

			$rawData = $dbTable->fetchAll($select)->toArray();

			#To get the associated Array, so that it can be accessed by the primarykey value.
			foreach ($rawData as $value)
			{
				$data[$value[$primaryKeyName]] = $value;
			}
			# in case of empty results reset the $data variable from bool to array
			if( empty($data) )
			{
				$data = array();
			}

			$cache->save($data, $cacheKey);
		}

		return $data;
	}

    /**
     * @brief	Compares with table fields and return only associated data
     *
     * @param 	null
     * @return 	array $columnNames	Array containing column names
     */
    public function getValidColumns(array $data)
    {
    	$dbTable 	= $this->getDbTable()->info();

		$validData	= array();
		$fields 	= $dbTable['cols'];

		foreach($fields as $field)
		{
			if( isset($data[$field]) )
			{
				$validData[$field] = $data[$field];
			}
		}

    	return $validData;
    }

    /**
     * @brief	Get primary key
     *
     * @param 	null
     * @return 	array $columnNames	Array containing column names
     */
    public function getPrimaryKey()
    {
    	$dbTable 	= $this->getDbTable()->info();

		return $dbTable['primary'][1];
    }
};