<?php
class DB {
	const DBHOST = 'localhost';
	const DBNAME = 'test';
	const DBUSER = 'root';
	const DBPASS = '';
	
	
	private $_parts,
		$_error,
		$_query,
		$_params,
		$_count;

	public function __construct() {
		try {
			$this->_pdo = new PDO(
				"mysql:host=" . self::DBHOST . ";dbname=".self::DBNAME, self::DBUSER, self::DBPASS,
				array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8", PDO::ATTR_EMULATE_PREPARES => false)
			);
		} catch(PDOException $e) {
			die($e->getMessage());
		}
	}

	public static function getInstance() {
		if (!ISSET(self::$_instance)) {
			self::$_instance = new DB;
		}
		return self::$_instance;
	}
	
	public function query() {
		list($sql, $params, $errors) = $this->_buildQuery();
		if (empty($errors)) {
			$this->_results = null;
			$this->_error = false;
			$this->_query = $this->_pdo->prepare($sql);
			$i = 1;
			if (count($params)) {
				foreach($params as $param) {
					$this->_query->bindValue($i, $param);
					$i++;
				}
				$this->_params = implode(',',$params);
			}
			if ($this->_query->execute()) {
				if ($rowset = $this->_query->fetchAll(PDO::FETCH_OBJ)) {
					$this->_results = $rowset;					
				}
				WHILE($this->_query->nextRowset()) {
					$rowset = $this->_query->fetchAll(PDO::FETCH_OBJ);
					if ($rowset) {
						$this->_results = $rowset;
					}
				}
				$this->_count = $this->_query->rowCount();
			} else {
				/* only for development stage */
				// die($sql);
				$this->_error = $this->_query->errorInfo();
			}
			return $this;			
		} else {
			return $errors;
		}
	}

	private function _setValues($array, $operators) {
		$placeHolders = [];
		foreach ($array as $arr) {
			if (count($arr) === 3) {
				$field		= $arr[0];
				$operator	= $arr[1];
				$value		= $arr[2];
						
				if (in_array($operator,$operators)) {
					$placeHolders[]= $field . ' ' . $operator . ' ?';
					$this->_parts->_values[] = $value;
				}
			}
		}
		return $placeHolders;
	}
	
	private function _params() {
		return (!empty($this->_parts->_wheres))? implode(' ' , $this->_parts->_wheres) : '';
	}
	
	public function table($table_name = '') {
		$this->_parts = new stdClass;
		$this->_parts->_wheres = null;
		$this->_parts->_whereCondition = false;
		$this->_parts->_values = [];
		$this->_parts->_action[1] = $table_name;
		return $this;			
	}	

	public function whereCols($array = []) {
		$w = '';
		$operators = array('=','>','<','>=','<=','LIKE','REGEXP');
		if (!EMPTY($array)) {
			$placeHolders = $this->_setValues($array, $operators);
			$this->_parts->_wheres[] = (count($this->_parts->_wheres))? 'AND ' . implode(' AND ', $placeHolders) : implode(' AND ', $placeHolders);
		} else {
			$this->_parts->_wheres[] = '';
		}
		return $this;
	}
	
	public function whereOrs($array = []) {
		$w = '';
		$operators = array('=','>','<','>=','<=','LIKE','REGEXP');
		if (!EMPTY($array)) {
			$placeHolders = $this->_setValues($array, $operators);
			$this->_parts->_wheres[] = (count($this->_parts->_wheres))? 'OR (' . implode(' AND ', $placeHolders) . ')' : implode(' AND ', $placeHolders);
		} else {
			$this->_parts->_wheres[] = '';
		}
		return $this;
	}
	
	public function whereIns($array = []) {
		$w = '';
		if (!EMPTY($array)) {
			foreach ($array as $arr) {
				if (count($arr) === 2) {
					$field		= $arr[0];
					$values		= explode(',', preg_replace('/\s+/', '', $arr[1]));
					$this->_parts->_values = (isset($this->_parts->_values))? array_merge($this->_parts->_values, $values) : $values;
					$v = rtrim(str_repeat('?,', count($values)), ',');
					$placeHolders[] = $field . ' IN (' . $v . ')'; 
				}
			}

			$this->_parts->_wheres[] = (count($this->_parts->_wheres))? 'AND (' . implode(' AND ', $placeHolders) . ')' : implode(' AND ', $placeHolders);
		} else {
			$this->_parts->_wheres[] = '';
		}
		return $this;
	}
	
	public function whereNotIns($array = []) {
		$w = '';
		if (!EMPTY($array)) {
			foreach ($array as $arr) {
				if (count($arr) === 2) {
					$field		= $arr[0];
					$values		= explode(',', preg_replace('/\s+/', '', $arr[1]));
					$this->_parts->_values = (isset($this->_parts->_values))? array_merge($this->_parts->_values, $values) : $values;
					$v = rtrim(str_repeat('?,', count($values)), ',');
					$placeHolders[] = $field . ' NOT IN (' . $v . ')'; 
				}
			}

			$this->_parts->_wheres[] = (count($this->_parts->_wheres))? 'AND (' . implode(' AND ', $placeHolders) . ')' : implode(' AND ', $placeHolders);
		} else {
			$this->_parts->_wheres[] = '';
		}
		return $this;
	}
	
	public function whereBetweens($array = []) {
		$w = '';
		if (!EMPTY($array)) {
			foreach ($array as $arr) {
				if (count($arr) === 2) {
					$field		= $arr[0];
					$values		= explode(',', preg_replace('/\s+/', '', $arr[1]));
					$this->_parts->_values = array_merge($this->_parts->_values, $values);
					$placeHolders[] = $field . ' BETWEEN  ? AND ?'; 
				}
			}
			$this->_parts->_wheres[] = (count($this->_parts->_wheres))? 'AND (' . implode(' AND ', $placeHolders) . ')' : implode(' AND ', $placeHolders);
		} else {
			$this->_parts->_wheres[] = '';
		}
		return $this;
	}

	public function select($fields) {
		$this->_parts->_whereCondition = true;
		$this->_parts->_action[0] = "SELECT {$fields} FROM";			
		return $this;
	}
	
	public function update($fields) {
		$this->_parts->_whereCondition = true;
		foreach($fields as $name => $value) {
			$sets[] = "`{$name}` = ?";
			$v[] = $value;
		}
		$this->_parts->_action[0] = "UPDATE";
		/* $this->_parts->_action[1] reserved for table name */
		$this->_parts->_action[2] = "SET";
		$this->_parts->_action[3] = implode(', ', $sets);
		$this->_parts->_values = array_merge($this->_parts->_values, $v);
		return $this;	
	}
	
	public function insert($fields) {
		$this->_parts->_whereCondition = false;
		$keys = array_keys($fields);
		$this->_parts->_action[0] = "INSERT INTO";
		$this->_parts->_values = array_values($fields);
		$this->_parts->_wheres[] = "(".implode(',', $keys).") VALUES (".rtrim(str_repeat('?,', count(array_values($fields))), ',').")";
		return $this;
	}
	
	public function delete() {
		$this->_parts->_whereCondition = true;
		$this->_parts->_action[0] = "DELETE";
		return $this;
	}
	
	public function options($options) {
		$this->_parts->_whereCondition = true;
		$this->_parts->_options = $options;
		return $this;
	}
	
	private function _buildQuery() {
		$errors = [];
		ksort($this->_parts->_action);			
		$action = implode(' ',$this->_parts->_action);
		$params = $this->_params();
		$options = (!empty($this->_options))? ' ' . $this->_options: '';
		$operator = ($this->_parts->_whereCondition == true && !empty($params))? ' WHERE ' : ' ';
		$sql = "{$action}{$operator}{$params}{$options} ";
		$values = ($this->_parts->_values)? $this->_parts->_values : [];
		$this->_parts = null;
		return [$sql,$values,$errors];
	}
	
	public function check() {
		return $this->_buildQuery();
	}
	
	public function results() {
		if ($this->query()) {
			return $this->_results;
		}
		return false;
	}
	
	public function first() {
		if ($this->query()) {
			return $this->_results[0];
		}
		return false;
	}
	
	public function rows() {
		if ($this->query()) {
			return $this->_count;
		}
		return false;
	}
	
	public function insertId() {
		if ($this->query()) {
			return $this->_pdo->lastInsertId();
		}
		return false;
	}
}
