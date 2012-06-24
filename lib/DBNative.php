<?PHP

/**
 * Class for MySQL Connections and Queries
 */
class DBNative {
	private $link; // Database connection
	private $transactionStarted = false;//Prevent nested transaction, mysql don't support it.
	private $db;
	private static $obj;
	public $DSN;
	public $debug = false;//print query and answer (boolean or rows number)
	public $ajax;//defined into the constructor, if true, is ajax request, else is not.
	public $ajaxDebug = false;//Do debug in ajax request, if is ON, json answer will not working on browser

	private function DBNative($DSN = false, $host = false, $user = false,
			$passwd = false, $db = false) {
		$this->db = $db;
		$this->DSN = $DSN;
		$this->ajax = (((isset($_SERVER['HTTP_X_REQUESTED_WITH'])
				&& strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])
						== 'xmlhttprequest') === false) ? false : true);
		if (!empty($DSN)) {
			$aTmp = parse_url($DSN);
			if (empty($aTmp))
				die("The DSN Format is invalid!");
			$aTmp["db"] = trim(str_replace("/", "", $aTmp["path"]));
			$this
					->connect($aTmp["host"], $aTmp["user"], @$aTmp["pass"],
							$aTmp["db"]);
		} else if (!empty($host) && !empty($user) && !empty($passwd)
				&& !empty($db)) {
			$this->connect($host, $user, $passwd, $db);
		} else
			die(
					"Please your must to pass either the DSN String or the Connection Parameters");
		$this->db = $aTmp["db"];
	}
	public static function get($DSN = false, $host = false, $user = false,
			$passwd = false, $db = false) {
		if (!self::$obj)
			self::$obj = new DBNative($DSN, $host, $user, $passwd, $db);
		return self::$obj;
	}
	/**
	 * Connect to the Database
	 * 
	 * @param mixed $host Server Name
	 * @param mixed $user User Name 
	 * @param mixed $password Password for the User
	 * @param mixed $db Database Name
	 */
	public function connect($host, $user, $password, $db, $c = 0) {
		$link = @mysql_connect($host, $user, $password);
		if (!$link) {
			if ($c > 30) {
				header("HTTP/1.1 503 Service Temporarily Unavailable");
				header("Status: 503 Service Temporarily Unavailable");
				die('Not connected : ' . mysql_error());
			}
			sleep(1);
			return $this->connect($host, $user, $password, $db, $c + 1);
		}
		register_shutdown_function(array($this, 'disconnect'));//close connection on exit
		// make foo the current db
		$db_selected = mysql_select_db($db, $link);
		if (!$db_selected) {
			die("Can\'t use $db : " . mysql_error());
		}
		$this->link = $link;
	}
	public function disconnect() {
		mysql_close($this->link);
	}
	/**
	 * Executes a query in the Database
	 * 
	 * @param mixed $SQL Query String
	 * @return int
	 */
	public function query($SQL, $numericArray = 0) {
		//echo "<pre>$SQL</pre><br/>";
		$db_selected = mysql_select_db($this->db, $this->link);//Si hay varias instancias de DBNative con diferentes base de datos, solo abra un link, en cada consulta se debe seleccionar (cambiar) la base de datos
		if (empty($SQL)) {
			if ($this->transactionStarted === true) {
				$this->query("ROLLBACK");//ROLLBACK TRANSACTION IN COURSE
				$this->transactionStarted = false;
			}
			die("Query must be non-empty!!");
		}
		$error = '';
		ob_start();
		$start = microtime(true);
		$result = mysql_unbuffered_query($SQL, $this->link); //Test, is good for slow querys, big data in result
		//$result = mysql_query($SQL, $this->link);//is good for fast querys, some rows
		$end = microtime(true);
		$total = number_format(($end - $start) * 1000, 3);//ms
		$error = ob_get_contents() . " - " . @mysql_error($this->link);
		ob_end_clean();
		if (($this->debug && !$this->ajax) || ($this->ajax && $this->ajaxDebug)) {
			echo "<pre>$SQL</pre> <br />";
			echo "<pre> result in " . $total . " ms :";
			var_dump($result);
			echo "</pre>";
		}
		if ($result === FALSE) {
			if ($this->transactionStarted === true) {
				$this->query("ROLLBACK");//ROLLBACK TRANSACTION IN COURSE
				$this->transactionStarted = false;
			}
			echo "<pre>";
			print_r($this) . debug_print_backtrace();
			echo "</pre>";
			echo "<pre>Database: {$this->db}"
					. print_r($this->query("SHOW PROCESSLIST"), true)
					. "</pre>";
			$msg = "Invalid query: $SQL <br />\r\n [" . $error . "]";
			$body = $msg . "\r\n" . print_r($_SERVER, true) . "\r\n"
					. print_r(@$_SESSION, true) . "\r\n"
					. print_r($_REQUEST, true);

			die(
					$msg . "<br />\r\n"
							. "Please report this error to support@librosoft.com");
		}
		if (is_resource($result)) {
			$aTmp = array();
			$fetch_function = "mysql_fetch_assoc";
			if ($numericArray)
				$fetch_function = "mysql_fetch_array";
			while ($row = @$fetch_function($result))
				$aTmp[] = $row;
			if (($this->debug && !$this->ajax)
					|| ($this->ajax && $this->ajaxDebug)) {
				echo "Rows: " . count($aTmp) . "<br />\r\n";
			}
			return $aTmp;
		} else {
			return $this->getLastAffectedRows();
		}
	}
	public function autoInsert($fields, $table) {
		$values = implode(",",
				array_map(array($this, "quote"), array_values($fields)));
		$cols = implode(",", array_keys($fields));
		$sql = "INSERT INTO $table ($cols) VALUES ($values)";
		$this->query($sql);
		return $this->getLastID($table);
	}
	public function autoUpdate($fields, $table, $whereString, $autoQuote = true) {
		$sql = "UPDATE  $table SET ";
		$sqlA = array();
		foreach ($fields as $name => $value)
			$sqlA[] = "`" . $name . "` = "
					. ($autoQuote ? $this->quote($value) : $value);
		$sql .= implode(", ", $sqlA) . " WHERE $whereString";
		return $this->query($sql);
	}
	//Start a Transaction
	public function begin() {
		if ($this->transactionStarted === false) {
			$this->query("START TRANSACTION");
			$this->transactionStarted = true;//transaction started
		} else {
			$msg = "Transaction already started <br />\r\n"
					. @mysql_error($this->link);
			$body = $msg . "\r\n " . print_r($GLOBALS, true);
			die(
					$msg . "<br />\r\n"
							. "Please report this error to support@librosoft.com");
		}
	}
	//Save transaction
	public function commit() {
		if ($this->transactionStarted === true) {
			$this->query("COMMIT");
			$this->transactionStarted = false;//end of transaction
		} else {
			$msg = "Transaction is not already started (not commit possible) <br />\r\n"
					. @mysql_error($this->link);
			$body = $msg . "\r\n " . print_r($GLOBALS, true);
			die(
					$msg . "<br />\r\n"
							. "Please report this error to support@librosoft.com");
		}
	}
	//Rollback the transaction
	public function rollback() {
		if ($this->transactionStarted === true) {
			$this->query("ROLLBACK");
			$this->transactionStarted = false;//end of transaction
		} else {
			$msg = "Transaction is not already started (not rollback possible) <br />\r\n"
					. @mysql_error($this->link);
			$body = $msg . "\r\n " . print_r($GLOBALS, true);
			die(
					$msg . "<br />\r\n"
							. "Please report this error to support@librosoft.com");
		}
	}
	//execute a array of queries in a transaction, if query return none affected rows... rollback transaction
	public function transaction($q_array) {
		$retval = 1;
		$this->begin();
		foreach ($q_array as $qa) {
			$result = $this->query($qa);
			if ($this->getLastAffectedRows() == 0)
				$retval = 0;
		}
		if ($retval == 0) {
			$this->rollback();
			return false;
		} else {
			$this->commit();
			return true;
		}
	}
	public function getLastQuery() {
		return mysql_info($this->link);
	}
	/**
	 * Get the Last ID for a table
	 * 
	 * @param mixed $table The table name
	 * @return mixed
	 */
	public function getLastID($table) {
		if ($this->transactionStarted === TRUE) {
			return mysql_insert_id($this->link);//only for transactions
		}
		if (empty($table))
			die("Table must be non-empty!");
		$SQL = "SELECT LAST_INSERT_ID() AS ID FROM $table ORDER BY ID DESC LIMIT 1";
		$rst = $this->query($SQL);
		return $rst[0]["ID"];
	}
	public function getLastAffectedRows() {
		return mysql_affected_rows($this->link);
	}
	/**
	 * Quote and scape a values
	 * 
	 * @param mixed $value The Value to escape
	 */
	public function quote($value) {
		//$value = strip_tags($value);
		$ovalue = $value;
		if (get_magic_quotes_gpc())
			$value = stripslashes($value);
		$escapedValue = mysql_real_escape_string($value, $this->link);
		if ($escapedValue === FALSE) {
			echo ("Unknow error, please send the next text to it@latinoaustralia.com <br />"
					. mysql_error($this->link) . " escaping: <br >"
					. htmlentities($value) . " length:" . strlen($ovalue));
			var_dump($escapedValue);
		}
		return "\"$escapedValue\"";
	}
	/**
	 * Quote and scape a values
	 * 
	 * @param mixed $value The Value to escape
	 */
	public function escape($value) {
		//$value = strip_tags($value);
		$ovalue = $value;
		if (get_magic_quotes_gpc())
			$value = stripslashes($value);
		$escapedValue = mysql_real_escape_string($value, $this->link);
		if ($escapedValue === FALSE) {
			echo ("Unknow error, please send the next text to support@librosoft.com <br />"
					. mysql_error($this->link) . " escaping: <br >"
					. htmlentities($value) . " length:" . strlen($ovalue));
			var_dump($escapedValue);
		}
		return "$escapedValue";
	}

	// Function to Return All Possible ENUM Values for a Field
	public function getEnumValues($table, $field) {
		$enum_array = array();
		$query = 'SHOW COLUMNS FROM `' . $table . '` LIKE "' . $field . '"';
		$res = $this->query($query);
		$error = print_r($res, true);
		preg_match_all('/\'(.*?)\'/', $res[0]["Type"], $enum_array);
		if (!empty($enum_array[1])) {
			// Shift array keys to match original enumerated index in MySQL (allows for use of index values instead of strings)
			foreach ($enum_array[1] as $mkey => $mval)
				$enum_fields[$mkey + 1] = $mval;
			return $enum_fields;
		} else
			return array($error); // Return an empty array to avoid possible errors/warnings if array is passed to foreach() without first being checked with !empty().
	}
}

?>
