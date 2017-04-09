<?php

namespace _2ndhand;

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
	 * Fetches a table and returns its content as an array of objects
	 */
	public function fetch(string $tableName, array $columns = array(), array $params = array()) {
		$this->checkPDO();

		static::checkTableName($tableName);

		$this->checkTableExists($tableName);

		$columns = empty($columns) ? '*' : implode(',', $columns);

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
			throw new DBException("Table doesn't exists");
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
			throw new DBException("No connection to the database established");
		}
	}

	/*
	 * Sends a query to the database and returns an array of objects
	 */
	private function query(string $query, array $params = null) {
		// prepare statement
		try {
			$stmt = $this->pdo->prepare($query, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
		}
		catch (\Exception $e) {
			throw new DBException($e);
		}
		if(!$stmt) {
			throw new DBException("Couldn't prepare SQL statement");
		}

		// execute statement
		if(!$stmt->execute($params)) {
			throw new DBException("An error occurred while executing a prepared statement");			
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
			throw new DBException("Invalid table name");
		}
		
		return true;		
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