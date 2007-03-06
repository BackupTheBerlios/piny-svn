<?php
	
	include_once('../server/settings.php');
	include_once('db/errors.php');
	
	class db {
		var $conn;				// connection resource
		var $table;				// name of the table
		
		var $connect;			// name of connect-method, dependant on DB_TYPE
		var $close;				// name of close-method, dependant on DB_TYPE
		var $query;				// name of query-method, dependant on DB_TYPE
		var $fetch_array;		// name of fetch_array-method, dependant on DB_TYPE
		var $free_result;		// name of free_result-method, dependant on DB_TYPE
		var $escape_string;		// name of escape_string-method, dependant on DB_TYPE
		var $select_db;			// name of select_db-method, dependant on DB_TYPE
		var $error;				// name of error-method, depandant on DB_TYPE
		var $fetch_object;		// name of fetch_object-method, dependant on DB_TYPE
		
		function __construct($table) {
			if (!is_string(DB_TYPE) || strlen(DB_TYPE) == 0) throw new Exception('DB_TYPE has not been set!');
			$this->connect = DB_TYPE.'_connect';
			$this->close = DB_TYPE.'_close';
			$this->query = DB_TYPE.'_query';
			$this->fetch_array = DB_TYPE.'_fetch_array';
			$this->free_result = DB_TYPE.'_free_result';
			$this->escape_string = DB_TYPE.'_escape_string';
			$this->select_db = DB_TYPE.'_select_db';
			
			$this->conn = $this->connect(DB_HOST.':'.DB_PORT, DB_USER, DB_PWD, false) || die(ERR_DB_CONNECT.':'. $this->error());
			$this->select_db(DB_NAME, $this->conn) || die(ERR_DB_SELECT.':'. $this->error());
			$this->table = $table;
		}
		
		/**
		 * This method fetches all entries from the table this <code>db</code>-instance has been created with
		 * 
		 * @return an associative array <code>[line-number]['column-name'] => value</code>
		 */
		/* public */
		function getAll($colarrkey = null) {
			$sql = 'SELECT * FROM `'. $this->table .'`;';
			return $this->fetchArray($sql, DB_TYPE.'_ASSOC', $colarrkey);
		}
		
		/**
		 * This method fetches the given columns of all entries from the table this <code>db</code>-instance has been created with
		 * 
		 * @param array cols:  <p>An array holding the names of all columns to be returned.</p>
		 *                     <p>If an associative array is passed, the columns specified by it's keys will be mapped to the respective array-values.</p>
		 *                     <p>If <code>null</code> is passed, all columns will be returned.</p>
		 * @param mixed where: <p>Optional argument specifying restrictions which rows shall be selected and returned.</p>
		 *                     <p>If an associative array is passed, the keys specify the value which has to equal their respective value in the array.
		 *                        All items of the array are AND-combined.</p>
		 *                     <p>If a string is passed, it will be used as the complete <code>WHERE</code>-part of the SQL-statement.</p>
		 *                     <p>If <code>null</null> is passed, or the argument is not used at all, the selection won't be restricted.</p>
		 * @return             <p>an associative array <code>[line-number]['column-name'] => value</code> or <code>false</code>
		 *                        if no row matching <code>cols</code> or <code>where</code> could be found.</p>
		 */
		/* public */
		function getAssoc($cols, $where = null, $colarrkey = null) {
			$sql = $this->assSelect($cols) .' FROM `'. $this->table .'` '. $this->assWhere($where) .';';
			return $this->db->fetchArray($sql, DB_TYPE.'_ASSOC', $colarrkey = null);
		}
		
		/**
		 * This method fetches the given columns of all entries from the table this <code>db</code>-instance has been created with
		 * 
		 * @param array cols:  <p>An array holding the names of all columns to be returned.</p>
		 *                     <p>If an associative array is passed, the columns specified by it's keys will be mapped to the respective array-values.</p>
		 *                     <p>If <code>null</code> is passed, all columns will be returned.</p>
		 * @param mixed where: <p>Optional argument specifying restrictions which rows shall be selected and returned.</p>
		 *                     <p>If an associative array is passed, the keys specify the value which has to equal their respective value in the array.
		 *                        All items of the array are AND-combined.</p>
		 *                     <p>If a string is passed, it will be used as the complete <code>WHERE</code>-part of the SQL-statement.</p>
		 *                     <p>If <code>null</null> is passed, or the argument is not used at all, the selection won't be restricted.</p>
		 * @return             <p>an non-associative array <code>[line-number][column-number] => value</code> or <code>false</code>
		 *                        if no row matching <code>cols</code> or <code>where</code> could be found.</p>
		 */
		/* public */
		function get($cols, $where = null, $colarrkey = null) {
			$sql = $this->assSelect($cols) .' FROM `'. $this->table .'` '. $this->assWhere($where) .';';
			return $this->fetchArray($sql, DB_TYPE.'_NUM', $colarrkey);
		}
		
		/**
		 * This method fetches an stdClass-object of the first line the given parameters match
		 *
		 * @param array cols:  <p>An array holding the names of all columns to be returned.</p>
		 *                     <p>If an associative array is passed, the columns specified by it's keys will be mapped to the respective array-values.</p>
		 *                     <p>If <code>null</code> is passed, all columns will be returned.</p>
		 * @param mixed where: <p>Optional argument specifying restrictions which rows shall be selected and returned.</p>
		 *                     <p>If an associative array is passed, the keys specify the value which has to equal their respective value in the array.
		 *                        All items of the array are AND-combined.</p>
		 *                     <p>If a string is passed, it will be used as the complete <code>WHERE</code>-part of the SQL-statement.</p>
		 *                     <p>If <code>null</null> is passed, or the argument is not used at all, the selection won't be restricted.</p>
		 * @return             <p>an <code>stdClass</code>-object containing the values of all selected columns as public variables or <code>false</code>
		 *                        if no row matching <code>cols</code> or <code>where</code> could be found.</p>
		 * @throws Exception   if either <code>cols</code> or </code>where</where> is not usable.
		 */
		/* public */
		function getSingle($cols, $where) {
			if ($col == null || !is_string($col)) throw new Exception('Illegal Argument: $col: '. $col);
			if ($where == null || strlen($w = self::assWhere($where)) == 0) throw new Exception('Illegal Argument: $where: '. $where);
			$sql = self::assSelect($cols) .' FROM `'. $this->table ."` $w;";
			$res = $this->query($sql, $this->conn) || die(ERR_DB_QUERY.':'. $this->error());
			$r = $this->fetch_object($res);
			$this->free_result($res);
			return $r;
		}
		
		function add($cols) {
			$sql = 'INSERT INTO `'. $this->table .'` (';
			foreach (array_keys($cols[0]) as $col) {
				$sql .= $col .',';
			}
			$sql = substr($sql, 0, -1) .') VALUES (';
			foreach ($cols as $col) {
				foreach ($col as $value) {
					if (is_string($value)) {
						$sql .= "'$value',";
					} else {
						$sql .= "$value,":
					}
				}
				$sql = substr($sql, 0, -1) .'), (';
			}
			$sql = substr($sql, 0, -2);
			return $this->query($sql, $this->conn);
		}
		
		/* public */
		function addSingle($cols) {
			$sql = 'INSERT INTO `'. $this->table .'` (';
			foreach (array_keys($cols) as $col) {
				$sql .= $col .',';
			}
			$sql = substr($sql, 0, -1) .') VALUES (';
			foreach ($cols as $value) {
				if (is_string($value)) {
					$sql .= "'$value',";
				} else {
					$sql .= "$value,":
				}
			}
			$sql = substr($sql, 0, -1) .';';
			return $this->query($sql, $this->conn) || die(ERR_DB_QUERY.':'. $this->error());
		}
		
		/* public */
		function putSingle($cols) {
		}
		
		function updateSingle($cols, $where) {
		}
		
		function removeSingle($where) {
		}
		
		/* private */
		function fetchArray($sql, $flags, $colarrkey = null) {
			if ($sql == null || !is_string($sql)) return null;
			$res = $this->query($sql, $this->conn) || die(ERR_DB_QUERY.':'. $this->error());
			$r = array();
			while ($row = $fetch_array($res, $flags)) {
				if ($colarrkey == null) {
					$r[] = $row;
				} else {
					$r[$row[$colarrkey]] = $row;
				}
			}
			$free_result($res);
			return $r;
		}
		
		/* private */
		function assSelect($cols) {
			$sql = 'SELECT ';
			if (is_array($cols)) {
				foreach ($cols as $col => $colname) {
					if (!is_integer($col)) $sql .= '`'. $this->escape_string($col) .'` AS ';
					$sql .= $this->escape_string($colname) .',';
				}
				$sql = substr($sql, 0, -1);
			} else {
				$sql .= '*';
			}
			return $sql;
		}
		
		/* private */
		function assWhere($where) {
			$sql = '';
			if (is_array($where)) {
				$sql .= 'WHERE ';
				foreach ($where as $key => $expr) {
					$sql .= '\''. $this->escape_string($key) .'\' = `'. $this->escape_string($expr) .'` AND ';
				}
				$sql = substr($sql, 0, -5);
			} else if (is_string($where)) {
				$sql .= 'WHERE '. $this->escape_string($where);
			}
			return $sql;
		}
		
		function __destruct() {
			$this->close($this->conn);
		}
	}
	
?>