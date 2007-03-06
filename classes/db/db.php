<?php
	ini_set('include_path', ini_get('include_path').':/var/www/localhost/htdocs/piny/classes/');
	
	include_once('server/settings.php');
	include_once('db/errors.php');
	
	class db {
		var $conn;				// connection resource
		var $table;				// name of the table
		
		function db($table) {
			if (!is_string(DB_TYPE) || strlen(DB_TYPE) == 0) die('DB_TYPE has not been set!');
			$connect = DB_TYPE.'_connect';
			$select_db = DB_TYPE.'_select_db';
			$error = DB_TYPE.'_error';
			
			$this->conn = $connect(DB_HOST.':'.DB_PORT, DB_USER, DB_PWD, false) or die(ERR_DB_CONNECT.':'. $error());
			$select_db(DB_NAME, $this->conn) or die(ERR_DB_SELECT.':'. $error());
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
			return $this->fetchArray($sql, constant(strtoupper(DB_TYPE).'_ASSOC'), $colarrkey);
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
			return $this->fetchArray($sql, constant(strtoupper(DB_TYPE).'_ASSOC'), $colarrkey = null);
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
		 * @param string colorarrkey <p>
		 * @return             <p>an non-associative array <code>[line-number][column-number] => value</code> or <code>false</code>
		 *                        if no row matching <code>cols</code> or <code>where</code> could be found.</p>
		 */
		/* public */
		function get($cols, $where = null, $colarrkey = null) {
			$sql = $this->assSelect($cols) .' FROM `'. $this->table .'` '. $this->assWhere($where) .';';
			return $this->fetchArray($sql, constant(strtoupper(DB_TYPE).'_NUM'), $colarrkey);
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
		 */
		/* public */
		function getSingle($cols, $where) {
			if ($col == null || !is_string($col)) die('Illegal Argument: $col: '. $col);
			if ($where == null || strlen($w = self::assWhere($where)) == 0) die('Illegal Argument: $where: '. $where);
			$sql = self::assSelect($cols) .' FROM `'. $this->table ."` $w;";
			$query = DB_TYPE.'_query';
			$res = $query($sql, $this->conn) or die(ERR_DB_QUERY.':'. $this->error());
			$r = $this->fetch_object($res);
			$free_result = DB_TYPE.'_free_result';
			$free_result($res);
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
						$sql .= "$value,";
					}
				}
				$sql = substr($sql, 0, -1) .'), (';
			}
			$sql = substr($sql, 0, -2);
			$query = DB_TYPE.'_query';
			return $query($sql, $this->conn);
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
					$sql .= "$value,";
				}
			}
			$sql = substr($sql, 0, -1) .';';
			$query = DB_TYPE.'_query';
			$error = DB_TYPE.'_error';
			return $query($sql, $this->conn) or die(ERR_DB_QUERY.':'. $error());
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
			$query = DB_TYPE.'_query';
			$error = DB_TYPE.'_error';
			$res = $query($sql, $this->conn) or die(ERR_DB_QUERY.':'. $error());
			$r = array();
			$fetch_array = DB_TYPE.'_fetch_array';
			while ($row = $fetch_array($res, $flags)) {
				if ($colarrkey == null) {
					$r[] = $row;
				} else {
					$r[$row[$colarrkey]] = $row;
				}
			}
			$free_result = DB_TYPE.'_free_result';
			$free_result($res);
			return $r;
		}
		
		/* private */
		function assSelect($cols) {
			$sql = 'SELECT ';
			$escape_string = DB_TYPE.'_escape_string';
			if (is_array($cols)) {
				foreach ($cols as $col => $colname) {
					if (!is_integer($col)) $sql .= '`'. $escape_string($col) .'` AS ';
					$sql .= $escape_string($colname) .', ';
				}
				$sql = substr($sql, 0, -2);
			} else {
				$sql .= '*';
			}
			return $sql;
		}
		
		/* private */
		function assWhere($where) {
			$sql = '';
			$escape_string = DB_TYPE.'_escape_string';
			if (is_array($where)) {
				$sql .= 'WHERE ';
				foreach ($where as $key => $expr) {
					$sql .= '`'. $escape_string($key) .'` = \''. $escape_string($expr) .'\' AND ';
				}
				$sql = substr($sql, 0, -5);
			} else if (is_string($where)) {
				$sql .= 'WHERE '. $escape_string($where);
			}
			return $sql;
		}
		
		function __destruct() {
			$close = DB_TYPE.'_close';
			$close($this->conn);
		}
	}
	
?>