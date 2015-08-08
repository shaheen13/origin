<?php

class DB {
	private static $_instance = NULL;
	private $_pdo,
			$_query,
			$_error = false,
			$_results,
			$_count = 0;

	private function __construct() {
		try {
			$this->_pdo = new PDO('mysql:host=' . Config::get('mysql/host') . ';dbname=' . Config::get('mysql/db'), Config::get('mysql/username'), Config::get('mysql/password'));
		}catch(PDOException $e) {
			die($e->getMessage());
		}
	}

	public static function getInstance()   //to ensure that there is only one connection - one instance of the DB class- to the db.
	{
		if(!isset(self::$_instance)) {
			self::$_instance = new DB();
		}

		return self::$_instance;
	}

	private function query($sql, $params = array())
	{
		$this->_error = false;

		if($this->_query = $this->_pdo->prepare($sql)) {
			if(count($params)) {
				foreach($params as $ind=>$param) {
					$this->_query->bindValue($ind+1, $param);
				}
			}
		}

		if($this->_query->execute()) {
			$this->_results = $this->_query->fetchAll(PDO::FETCH_OBJ);
			$this->_count = $this->_query->rowCount();
		} else {
			$this->_error = true;
		}

		return $this;
	}

	private function action($action, $table, $where = array())
	{
		if(count($where === 3)) {
			$operators = array('=', '!=', '>', '<', '>=', '<=');

			$field    = $where[0];
			$operator = $where[1];
			$value    = $where[2];

			if(in_array($operator, $operators)) {
				$sql = "{$action} From {$table} WHERE {$field} {$operator} ?";
				if(!$this->query($sql, array($value))->error()) {
					return $this;
				}
			}
		}

		return false;
	}

	public function get($fields = " * ", $table, $where)
	{
		return $this->action("SELECT {$fields}", $table, $where)->results();
	}

	public function delete($table, $where)
	{
		if(!$this->action("DELETE", $table, $where)->error()) {
			return true;
		}

		return false;
	}

	public function insert($table, $set = array())
	{
		if(count($set)) {

			$fields = array_keys($set);
			$values = array_values($set);
			
			$set_clause = implode(" = ?, ", $fields). " = ?" ;
			$sql = "INSERT INTO {$table} SET {$set_clause}";
			if(!$this->query($sql, $values)->error()) {
				return true;
			}
		}
		return false;
	}

	public function update($table, $set, $where_clause)
	{
		if(count($set)) {

			$fields = array_keys($set);
			$values = array_values($set);

			$set_clause = implode(' = ?, ', $fields). " = ?";
			$sql = "UPDATE {$table} SET {$set_clause} WHERE {$where_clause}";

			if(!$this->query($sql, $values)->error()) {
				return true;
			}
		}
		return false;
	}

	public function error()
	{
		return $this->_error;
	}

	public function results()
	{
		return $this->_results;
	}

	public function first()
	{
		return $this->results()[0];
	}

	public function count($table = null, $where = null)
	{
		if($table){
			return $this->action("SELECT *", $table, $where)->_count;
		} else {
			return $this->_count;
		}
	}

}