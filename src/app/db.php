<?php

/*
 * This class is used as interface to the database. It wraps a PDO object
 * https://secure.php.net/manual/en/intro.pdo.php
 */
class DB {

	/* The PDO object to wrap */
	private $pdo;

	/* The database config */
	private $conf;

	/*
	 * Creates a new instance of the DB class, establishes a connection to the
	 * database by instantiating a PDO
	 */
	function __construct() {
		global $DBCONFIG;

		// set up the config
		if($DBCONFIG &&
			is_array($DBCONFIG) &&
			count($DBCONFIG) > 0 &&
			isset($DBCONFIG['HOST']) &&
			isset($DBCONFIG['NAME']) &&
			isset($DBCONFIG['USER']) &&
			isset($DBCONFIG['PASS'])){
			$this->conf = $DBCONFIG;
		}
		else {
			throw new DBException('The database config is wrong');
		}

		// create connection
		try {
			$this->pdo = new \PDO($this->getDSN());
		}
		catch (Exception $e) {
			throw new DBException($e->getMessage(), $e->getCode(), $e->getPrevious());
		}
	}

	/*
	 * Starts a transaction
	 */
	public function beginTransaction() {
		$this->checkPDO();

		return $this->pdo->beginTransaction();
	}

	/*
	 * Commit changes
	 */
	public function commit() {
		$this->checkPDO();

		return $this->pdo->commit();
	}

	/*
	 * Rollback transaction
	 */
	public function rollBack() {
		$this->checkPDO();

		return $this->pdo->rollBack();
	}

	/*
	 * Fetches a table and returns its content as an array of objects
	 */
	public function fetch(string $tableName, array $params = array(), array $columns = array()) {
		$this->checkPDO();

		static::checkTableName($tableName);

		$this->checkTableExists($tableName);

		$columns = empty($columns) ? '*' : implode('" ,"', $columns);
		$columns = $columns === '*' ? '*' : "\"{$columns}\"";

		return $this->query("SELECT {$columns} FROM {$tableName}", $params);
	}

	/*
	 * Checks whether a table exists
	 */
	public function tableExists(string $tableName) {
		$this->checkPDO();

		static::checkTableName($tableName);

		$chunks = explode('.', $tableName);
		$tableName = $chunks[count($chunks)-1];

		$res = $this->query("SELECT count(*) = 1 as exists FROM pg_catalog.pg_tables WHERE tablename = '{$tableName}'");

		return $res[0]->exists;
	}

	/*
	 * Checks whether a table exists, and throws an exeption if it doesn't
	 */
	private function checkTableExists($tableName) {
		if(!$this->tableExists($tableName)) {
			throw new DBException('Table doesn\'t exists');
		}

		return true;
	}


	/*
	 * Returns the DSN string by putting together the config
	 * https://en.wikipedia.org/wiki/Data_source_name
	 */
	private function getDSN() {
		if($this->conf) {
			return sprintf("pgsql:host=%s;dbname=%s;user=%s;password=%s",
				$this->conf['HOST'],
				$this->conf['NAME'],
				$this->conf['USER'],
				$this->conf['PASS']);
		}
		else {
			throw new DBException('No database config found');
		}
	}

	/*
	 * Check whether the current object has a valid connection open
	 */
	private function checkPDO() {
		if(!$this->pdo) {
			throw new DBException('No connection to the database established');
		}
	}

	/*
	 * Sends a query to the database and returns an array of objects
	 */
	private function query(string $query, array $params = array()) {
		// prepare statement
		try {
			$stmt = $this->pdo->prepare($query, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
		}
		catch (\Exception $e) {
			throw new DBException($e);
		}
		if(!$stmt) {
			throw new DBException('Couldn\'t prepare SQL statement');
		}

		// execute statement
		if(empty($params)) {
			if(!$stmt->execute()) {
				throw new DBException('An error occurred while executing a prepared statement');			
			}
		}
		else {
			if(!$stmt->execute($params)) {
				throw new DBException('An error occurred while executing a prepared statement');			
			}
		}

		// fetch results
		$resultSet = $stmt->fetchAll(\PDO::FETCH_OBJ);
		if(!$resultSet) {
			throw new DBException('An error occurred while fetching data from the database');
		}

		return $resultSet;
	}

	/*
	 * Check whether a string is a valid table name: only letters, numbers, underscores and one dot
	 */
	private static function checkTableName($tableName) {
		if(!preg_match('/^[a-zA-Z_]+[a-zA-Z0-9_]*\.?[a-zA-Z_]+[a-zA-Z0-9_]*$/', $tableName)) {
			throw new DBException('Invalid table name');
		}
		
		return true;		
	} 

	/*
	 * Insert a new record into the database
	 */
	public function insert($tableName, $KVPairs) {
		$this->checkPDO();

		static::checkTableName($tableName);

		// check parameters for consistency
		if(!is_array($KVPairs) || empty($KVPairs)) {
			throw new DBException('Cannot insert data: no data available');
		}
		
		$columns = array_keys($KVPairs);
		$values = array_values($KVPairs);

		// escape column names with "double quotes"
		$columns = array_map(function($name) {
			return "\"{$name}\"";
		}, $columns);

		// sanitize column names to prevent SQL injections
		$columns = array_map(function($name) {
			return pg_escape_string($name);
		}, $columns);

		// sanitize values to prevent SQL injections
		$values = array_map(function($val) {
			return pg_escape_string($val);
		}, $values);

		// escape non-numeric values with 'single quotes'
		for($i = 0; $i < count($values); $i++) {
			if(!is_numeric($values[$i])) {
				$values[$i] = "'{$values[$i]}'";
			}
		}

		// build SQL
		$columns = implode($columns, ', ');
		$values = implode($values, ', ');
		$sql = "INSERT INTO \"{$tableName}\" ({$columns}) VALUES ({$values})";

		// do the job
		return $this->pdo->exec($sql);
	}

	/*
	 * Update a table (only numbers and strings allowed)
	 */
	public function update($tableName, $set = array(), $where = array()) {
		$this->checkPDO();

		static::checkTableName($tableName);

		// check parameters for consistency
		if(!is_array($set) || empty($set)) {
			throw new DBException('No SET clause supplied, cannot update table '.$tableName);
		}
		if(count($set) != 2) {
			throw new DBException('Wrong parameters passed to the SET clause, expecting only two, but got: '.implode($set, ', '));
		}
		if(!is_array($where) || empty($where)) {
			throw new DBException('No WHERE clause supplied, cannot update table '.$tableName);
		}

		// select quotation mode, depending whether we have to update a number or a string
		if(!is_numeric($set[1])) {
			// string: need to use 'single quotes' in SQL
			$q = '\'';
		}
		else {
			// number: no need to use 'single quotes' in SQL
			$q = '';
		}

		// build SET clause
		$setClause = "\"{$set[0]}\" = {$q}{$set[1]}{$q}";

		// build WHERE clause
		$whereClause = implode($where, ' AND ');

		// build SQL
		$sql = "UPDATE \"{$tableName}\" SET {$setClause} WHERE {$whereClause}";

		// do the job
		return $this->pdo->exec($sql);
	}
}


/*
 * Custom exception class
 */
class DBException extends \Exception {
	function __construct($data, $code = 0, $previous = null) {
		if(gettype($data) === 'string') {
			parent::__construct($data, $code, $previous);
		}
		elseif(gettype($data === 'object')) {
			if(get_class($data) === 'Exception') {
				parent::__construct($data->getMessage(), $data->getCode(), $data);
			}
		}
	}
}