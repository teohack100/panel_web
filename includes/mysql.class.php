<?PHP
if (preg_match("/mysql.class.php/i", $_SERVER['SCRIPT_NAME'])) {
    Header("Location: index.php"); die();
}

class ProgrammitPdoCompatResult
{
    public $num_rows = 0;
    public $num_fields = 0;

    private $rows = array();
    private $fields = array();
    private $pointer = 0;

    public function __construct(array $rows)
    {
        $this->rows = array_values($rows);
        $this->num_rows = count($this->rows);
        if ($this->num_rows > 0 && is_array($this->rows[0])) {
            $this->fields = array_keys($this->rows[0]);
        }
        $this->num_fields = count($this->fields);
    }

    public function fetch_assoc()
    {
        if ($this->pointer >= $this->num_rows) {
            return null;
        }
        $row = $this->rows[$this->pointer];
        $this->pointer++;
        return is_array($row) ? $row : null;
    }

    public function fetch_array($mode = null)
    {
        $row = $this->fetch_assoc();
        if (!is_array($row)) {
            return null;
        }

        $assocMode = defined('MYSQLI_ASSOC') ? MYSQLI_ASSOC : 1;
        $numMode = defined('MYSQLI_NUM') ? MYSQLI_NUM : 2;
        if ($mode === $assocMode) {
            return $row;
        }
        if ($mode === $numMode) {
            return array_values($row);
        }
        return array_merge(array_values($row), $row);
    }

    public function fetch_object()
    {
        $row = $this->fetch_assoc();
        if (!is_array($row)) {
            return null;
        }
        return (object)$row;
    }

    public function data_seek($rownum)
    {
        $rownum = (int)$rownum;
        if ($rownum < 0 || $rownum >= $this->num_rows) {
            return false;
        }
        $this->pointer = $rownum;
        return true;
    }

    public function free_result()
    {
        $this->rows = array();
        $this->fields = array();
        $this->pointer = 0;
        $this->num_rows = 0;
        $this->num_fields = 0;
        return true;
    }

    public function field($offset)
    {
        $offset = (int)$offset;
        if (!isset($this->fields[$offset])) {
            return null;
        }
        $field = new stdClass();
        $field->name = (string)$this->fields[$offset];
        return $field;
    }
}

class ProgrammitPdoCompatConnection
{
    public $affected_rows = 0;
    public $insert_id = 0;
    public $error = '';
    public $errno = 0;

    private $pdo;
    private $driver = 'pgsql';
    private $schema = '';

    public function __construct(PDO $pdo, $driver = 'pgsql', $schema = '')
    {
        $this->pdo = $pdo;
        $this->driver = strtolower(trim((string)$driver));
        $this->schema = trim((string)$schema);
        if ($this->driver === 'pgsql' && $this->schema !== '') {
            $safeSchema = preg_replace('/[^a-zA-Z0-9_]/', '', $this->schema);
            if ($safeSchema !== '') {
                $this->pdo->exec('SET search_path TO "' . $safeSchema . '", public');
            }
        }
    }

    public function query($query)
    {
        $query = (string)$query;
        if ($query === '') {
            return false;
        }

        $sql = $this->translateSql($query);
        try {
            $stmt = $this->pdo->query($sql);
            if (!($stmt instanceof PDOStatement)) {
                $this->affected_rows = 0;
                $this->insert_id = 0;
                $this->error = '';
                $this->errno = 0;
                return true;
            }

            if ($stmt->columnCount() > 0) {
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $this->affected_rows = count($rows);
                $this->insert_id = 0;
                $this->error = '';
                $this->errno = 0;
                return new ProgrammitPdoCompatResult($rows);
            }

            $this->affected_rows = (int)$stmt->rowCount();
            $this->insert_id = 0;
            if ($this->shouldReadInsertId($sql)) {
                try {
                    $this->insert_id = (int)$this->pdo->lastInsertId();
                } catch (Exception $e) {
                    $this->insert_id = 0;
                }
            }
            $this->error = '';
            $this->errno = 0;
            return true;
        } catch (Exception $e) {
            $this->error = (string)$e->getMessage();
            $this->errno = (int)$e->getCode();
            $this->affected_rows = 0;
            $this->insert_id = 0;
            return false;
        }
    }

    public function select_db($database)
    {
        return true;
    }

    public function set_charset($charset)
    {
        return true;
    }

    public function real_escape_string($str)
    {
        $quoted = $this->pdo->quote((string)$str);
        if (!is_string($quoted) || strlen($quoted) < 2) {
            return addslashes((string)$str);
        }
        return substr($quoted, 1, -1);
    }

    public function begin_transaction()
    {
        try {
            if ($this->pdo->inTransaction()) {
                return true;
            }
            $ok = $this->pdo->beginTransaction();
            $this->error = '';
            $this->errno = 0;
            return (bool)$ok;
        } catch (Exception $e) {
            $this->error = (string)$e->getMessage();
            $this->errno = (int)$e->getCode();
            return false;
        }
    }

    public function commit()
    {
        try {
            if (!$this->pdo->inTransaction()) {
                return true;
            }
            $ok = $this->pdo->commit();
            $this->error = '';
            $this->errno = 0;
            return (bool)$ok;
        } catch (Exception $e) {
            $this->error = (string)$e->getMessage();
            $this->errno = (int)$e->getCode();
            return false;
        }
    }

    public function rollback()
    {
        try {
            if (!$this->pdo->inTransaction()) {
                return true;
            }
            $ok = $this->pdo->rollBack();
            $this->error = '';
            $this->errno = 0;
            return (bool)$ok;
        } catch (Exception $e) {
            $this->error = (string)$e->getMessage();
            $this->errno = (int)$e->getCode();
            return false;
        }
    }

    public function close()
    {
        $this->pdo = null;
        return true;
    }

    private function shouldReadInsertId($sql)
    {
        $sql = ltrim((string)$sql);
        if ($sql === '') {
            return false;
        }

        return (preg_match('/^(INSERT|REPLACE)\b/i', $sql) === 1);
    }

    private function translateSql($sql)
    {
        if ($this->driver !== 'pgsql') {
            return $sql;
        }

        $sql = str_replace('`', '"', (string)$sql);
        $sql = $this->translateShowTables($sql);
        $sql = $this->translateShowColumns($sql);
        $sql = $this->translateDateIntervals($sql);
        $sql = $this->translateInsertIgnore($sql);
        $sql = $this->translateOnDuplicateKey($sql);
        $sql = $this->translateIfFunctions($sql);
        $sql = $this->translateLimitOffset($sql);
        $sql = $this->translateUpdateDeleteLimit($sql);
        $sql = $this->translateCreateTable($sql);
        $sql = $this->translateAlterTable($sql);
        return $sql;
    }

    private function translateShowTables($sql)
    {
        if (preg_match('/^\s*SHOW\s+TABLES\s+LIKE\s+\'([^\']+)\'\s*;?\s*$/i', $sql, $m)) {
            $like = $this->pdo->quote((string)$m[1]);
            return "SELECT table_name AS \"table_name\"
                    FROM information_schema.tables
                    WHERE table_schema = current_schema()
                      AND table_name LIKE " . $like;
        }
        return $sql;
    }

    private function translateShowColumns($sql)
    {
        if (preg_match('/^\s*SHOW\s+COLUMNS\s+FROM\s+([`"a-zA-Z0-9_\.]+)(?:\s+LIKE\s+\'([^\']+)\')?\s*;?\s*$/i', $sql, $m)) {
            $table = trim((string)$m[1], "\"` \t\r\n");
            $tableParts = explode('.', $table);
            $table = trim((string)end($tableParts));
            $where = "table_schema = current_schema() AND table_name = " . $this->pdo->quote($table);
            if (isset($m[2]) && trim((string)$m[2]) !== '') {
                $where .= " AND column_name LIKE " . $this->pdo->quote((string)$m[2]);
            }
            return "SELECT
                        column_name AS \"Field\",
                        data_type AS \"Type\",
                        is_nullable AS \"Null\",
                        column_default AS \"Default\"
                    FROM information_schema.columns
                    WHERE " . $where . "
                    ORDER BY ordinal_position";
        }
        return $sql;
    }

    private function translateDateIntervals($sql)
    {
        return preg_replace_callback(
            '/DATE_(ADD|SUB)\(\s*NOW\(\)\s*,\s*INTERVAL\s+([0-9]+)\s+(SECOND|MINUTE|HOUR|DAY|WEEK|MONTH|YEAR)\s*\)/i',
            function ($m) {
                $op = (strtoupper((string)$m[1]) === 'ADD') ? '+' : '-';
                $amount = (int)$m[2];
                $unit = strtolower((string)$m[3]);
                return "(NOW() " . $op . " INTERVAL '" . $amount . " " . $unit . "')";
            },
            $sql
        );
    }

    private function translateInsertIgnore($sql)
    {
        if (!preg_match('/^\s*INSERT\s+IGNORE\s+INTO\s+/i', $sql)) {
            return $sql;
        }
        $sql = preg_replace('/^\s*INSERT\s+IGNORE\s+INTO\s+/i', 'INSERT INTO ', $sql, 1);
        if (stripos($sql, 'ON CONFLICT') !== false) {
            return $sql;
        }
        $hasSemicolon = (substr(rtrim($sql), -1) === ';');
        $sql = rtrim($sql, " \t\r\n;");
        $sql .= " ON CONFLICT DO NOTHING";
        if ($hasSemicolon) {
            $sql .= ';';
        }
        return $sql;
    }

    private function translateOnDuplicateKey($sql)
    {
        if (stripos($sql, 'ON DUPLICATE KEY UPDATE') === false) {
            return $sql;
        }
        $parts = preg_split('/\s+ON\s+DUPLICATE\s+KEY\s+UPDATE\s+/i', $sql, 2);
        if (!is_array($parts) || count($parts) !== 2) {
            return $sql;
        }
        $hasSemicolon = (substr(rtrim($sql), -1) === ';');
        $base = rtrim((string)$parts[0], " \t\r\n;");
        $out = $base . " ON CONFLICT DO NOTHING";
        if ($hasSemicolon) {
            $out .= ';';
        }
        return $out;
    }

    private function translateLimitOffset($sql)
    {
        return preg_replace_callback(
            '/\bLIMIT\s+([0-9]+)\s*,\s*([0-9]+)\b/i',
            function ($m) {
                return 'LIMIT ' . (int)$m[2] . ' OFFSET ' . (int)$m[1];
            },
            $sql
        );
    }

    private function translateUpdateDeleteLimit($sql)
    {
        if (!preg_match('/^\s*(UPDATE|DELETE)\b/i', $sql)) {
            return $sql;
        }
        return preg_replace('/\s+LIMIT\s+[0-9]+\s*;?\s*$/i', '', $sql);
    }

    private function translateIfFunctions($sql)
    {
        $offset = 0;
        while (true) {
            $pos = stripos($sql, 'IF(', $offset);
            if ($pos === false) {
                break;
            }
            if ($pos > 0 && preg_match('/[a-zA-Z0-9_]/', $sql[$pos - 1])) {
                $offset = $pos + 3;
                continue;
            }
            $openParen = $pos + 2;
            $closeParen = $this->findMatchingParen($sql, $openParen);
            if ($closeParen < 0) {
                $offset = $pos + 3;
                continue;
            }
            $inner = substr($sql, $openParen + 1, $closeParen - $openParen - 1);
            $args = $this->splitTopLevelArgs($inner);
            if (count($args) !== 3) {
                $offset = $closeParen + 1;
                continue;
            }
            $replacement = '(CASE WHEN ' . $this->translateIfFunctions($args[0]) .
                ' THEN ' . $this->translateIfFunctions($args[1]) .
                ' ELSE ' . $this->translateIfFunctions($args[2]) . ' END)';
            $sql = substr($sql, 0, $pos) . $replacement . substr($sql, $closeParen + 1);
            $offset = $pos + strlen($replacement);
        }
        return $sql;
    }

    private function findMatchingParen($sql, $openIndex)
    {
        $len = strlen($sql);
        if ($openIndex < 0 || $openIndex >= $len || $sql[$openIndex] !== '(') {
            return -1;
        }
        $depth = 0;
        $quote = '';
        for ($i = $openIndex; $i < $len; $i++) {
            $ch = $sql[$i];
            if ($quote !== '') {
                if ($ch === $quote && ($i === 0 || $sql[$i - 1] !== '\\')) {
                    $quote = '';
                }
                continue;
            }
            if ($ch === "'" || $ch === '"') {
                $quote = $ch;
                continue;
            }
            if ($ch === '(') {
                $depth++;
                continue;
            }
            if ($ch === ')') {
                $depth--;
                if ($depth === 0) {
                    return $i;
                }
            }
        }
        return -1;
    }

    private function splitTopLevelArgs($text)
    {
        $args = array();
        $current = '';
        $depth = 0;
        $quote = '';
        $len = strlen((string)$text);
        for ($i = 0; $i < $len; $i++) {
            $ch = $text[$i];
            if ($quote !== '') {
                $current .= $ch;
                if ($ch === $quote && ($i === 0 || $text[$i - 1] !== '\\')) {
                    $quote = '';
                }
                continue;
            }
            if ($ch === "'" || $ch === '"') {
                $quote = $ch;
                $current .= $ch;
                continue;
            }
            if ($ch === '(') {
                $depth++;
                $current .= $ch;
                continue;
            }
            if ($ch === ')') {
                if ($depth > 0) {
                    $depth--;
                }
                $current .= $ch;
                continue;
            }
            if ($ch === ',' && $depth === 0) {
                $args[] = trim($current);
                $current = '';
                continue;
            }
            $current .= $ch;
        }
        $args[] = trim($current);
        return $args;
    }

    private function translateCreateTable($sql)
    {
        if (!preg_match('/^\s*CREATE\s+TABLE/i', $sql)) {
            return $sql;
        }

        $sql = preg_replace('/\)\s*ENGINE\s*=\s*[^\s;]+/i', ')', $sql);
        $sql = preg_replace('/\s+DEFAULT\s+CHARSET\s*=\s*[^\s;]+/i', '', $sql);
        $sql = preg_replace('/\s+CHARSET\s*=\s*[^\s;]+/i', '', $sql);
        $sql = preg_replace('/\s+COLLATE\s*=\s*[^\s;]+/i', '', $sql);

        $lines = preg_split('/\R/', $sql);
        $out = array();
        foreach ($lines as $line) {
            $trim = trim((string)$line);
            if ($trim === '') {
                $out[] = $line;
                continue;
            }
            if (preg_match('/^KEY\s+/i', $trim)) {
                continue;
            }
            if (preg_match('/^UNIQUE\s+KEY\s+[^(]+\((.+)\)\s*,?$/i', $trim, $m)) {
                $indent = substr($line, 0, strlen($line) - strlen(ltrim($line)));
                $comma = (substr(rtrim($line), -1) === ',') ? ',' : '';
                $out[] = $indent . 'UNIQUE (' . $m[1] . ')' . $comma;
                continue;
            }

            $line = preg_replace('/\bINT\s*\(\s*\d+\s*\)/i', 'INTEGER', $line);
            $line = preg_replace('/\bBIGINT\s*\(\s*\d+\s*\)/i', 'BIGINT', $line);
            $line = preg_replace('/\bTINYINT\s*\(\s*\d+\s*\)/i', 'SMALLINT', $line);
            $line = preg_replace('/\bMEDIUMTEXT\b/i', 'TEXT', $line);
            $line = preg_replace('/\bLONGTEXT\b/i', 'TEXT', $line);
            $line = preg_replace('/\bDATETIME\b/i', 'TIMESTAMP', $line);
            $line = preg_replace('/\bUNSIGNED\b/i', '', $line);
            $line = preg_replace('/\bAUTO_INCREMENT\b/i', 'GENERATED BY DEFAULT AS IDENTITY', $line);
            $out[] = $line;
        }

        $sql = implode("\n", $out);
        $sql = preg_replace('/,\s*\)/m', "\n)", $sql);
        $sql = preg_replace('/\s+,/', ',', $sql);
        return $sql;
    }

    private function translateAlterTable($sql)
    {
        if (preg_match('/^\s*ALTER\s+TABLE\s+([`"a-zA-Z0-9_\.]+)\s+ADD\s+(UNIQUE\s+)?KEY\s+([`"a-zA-Z0-9_]+)\s*\(([^)]+)\)\s*;?\s*$/i', $sql, $m)) {
            $table = (string)$m[1];
            $unique = (trim((string)$m[2]) !== '') ? 'UNIQUE ' : '';
            $index = trim((string)$m[3], "\"`");
            $cols = (string)$m[4];
            return "CREATE " . $unique . "INDEX IF NOT EXISTS \"" . $index . "\" ON " . $table . " (" . $cols . ")";
        }

        if (preg_match('/^\s*ALTER\s+TABLE\s+([`"a-zA-Z0-9_\.]+)\s+MODIFY\s+([`"a-zA-Z0-9_]+)\s+(.+)\s*;?\s*$/i', $sql, $m)) {
            $table = (string)$m[1];
            $column = (string)$m[2];
            $type = $this->mapMysqlTypeToPg((string)$m[3]);
            return "ALTER TABLE " . $table . " ALTER COLUMN " . $column . " TYPE " . $type;
        }

        $sql = preg_replace('/\s+AFTER\s+[`"]?[a-zA-Z0-9_]+[`"]?/i', '', $sql);
        $sql = preg_replace('/\bINT\s*\(\s*\d+\s*\)/i', 'INTEGER', $sql);
        $sql = preg_replace('/\bBIGINT\s*\(\s*\d+\s*\)/i', 'BIGINT', $sql);
        $sql = preg_replace('/\bTINYINT\s*\(\s*\d+\s*\)/i', 'SMALLINT', $sql);
        $sql = preg_replace('/\bMEDIUMTEXT\b/i', 'TEXT', $sql);
        $sql = preg_replace('/\bLONGTEXT\b/i', 'TEXT', $sql);
        $sql = preg_replace('/\bDATETIME\b/i', 'TIMESTAMP', $sql);
        $sql = preg_replace('/\bUNSIGNED\b/i', '', $sql);
        return $sql;
    }

    private function mapMysqlTypeToPg($rawType)
    {
        $type = strtoupper(trim((string)$rawType));
        if ($type === '') {
            return 'TEXT';
        }
        $type = preg_replace('/\bUNSIGNED\b/i', '', $type);
        if (preg_match('/^INT\s*\(\s*\d+\s*\)/i', $type)) {
            return 'INTEGER';
        }
        if (preg_match('/^BIGINT\s*\(\s*\d+\s*\)/i', $type)) {
            return 'BIGINT';
        }
        if (preg_match('/^TINYINT\s*\(\s*\d+\s*\)/i', $type)) {
            return 'SMALLINT';
        }
        if (preg_match('/^MEDIUMTEXT/i', $type) || preg_match('/^LONGTEXT/i', $type)) {
            return 'TEXT';
        }
        if (preg_match('/^DATETIME/i', $type)) {
            return 'TIMESTAMP';
        }
        return trim((string)$rawType);
    }
}

class mysql_db
{
    var $success_message;
    var $error_message;
	
    var $username;
    var $pwd;
    var $database;
    var $connection;
    var $db_host;
    var $db_driver = 'mysql';
    var $db_port = 0;
    var $db_schema = '';

	var $query_result;
	var $row = array();
	var $rowset = array();
	var $num_queries = 0;
	
	var $siteTitle;
	var $sitename;

    function InitDB($host,$uname,$pwd,$database)
    {
        global $DB_driver, $DB_port, $DB_schema;
        $this->db_host  = $host;
        $this->username = $uname;
        $this->pwd  = $pwd;
        $this->database  = $database;
        $this->db_driver = strtolower(trim((string)$DB_driver));
        if ($this->db_driver === '') {
            $this->db_driver = 'mysql';
        }
        $this->db_port = (int)$DB_port;
        $this->db_schema = trim((string)$DB_schema);
    }

	function query($query)
	{
		return $this->sql_query($query);
	}

	function get_db_driver()
	{
		$driver = strtolower(trim((string)$this->db_driver));
		if ($driver === '') {
			$driver = 'mysql';
		}
		if ($driver === 'postgres' || $driver === 'postgresql') {
			$driver = 'pgsql';
		}
		return $driver;
	}

	function is_pgsql()
	{
		return ($this->get_db_driver() === 'pgsql');
	}

	function num_rows($query_id = 0)
	{
		return $this->sql_numrows($query_id);
	}

	function real_escape_string($str)
	{
		return $this->SanitizeForSQL($str);
	}

	function sql_query($query)
	{
        if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }

		if($query != "")
		{
			$this->query_result = $this->connection->query($query);
		}
		
		if($this->query_result)
		{
			return $this->query_result;
		}
		return false;
	}

	function sql_numrows($query_id = 0)
	{
		if(!$query_id)
		{
			$query_id = $this->query_result;
		}
		if($query_id && isset($query_id->num_rows))
		{
			$result = (int)$query_id->num_rows;
			return $result;
		}
		return false;
	}
	function sql_affectedrows()
	{
		if($this->connection && isset($this->connection->affected_rows))
		{
			$result = (int)$this->connection->affected_rows;
			return $result;
		}
		return false;
	}

	function sql_fetchrow($query_id = null)
	{
		if(empty($query_id))
		{
			$query_id = $this->query_result;
		}
		
		if($query_id && method_exists($query_id, 'fetch_assoc'))
		{
			$result = $query_id->fetch_assoc();
			return $result;
		}
		return false;
	}

	function sql_fetcharray($query_id = null)
	{
		if(empty($query_id))
		{
			$query_id = $this->query_result;
		}
		if($query_id && method_exists($query_id, 'fetch_array'))
		{
			$result = $query_id->fetch_array();
			return $result;
		}
		return false;
	}

	function sql_fetchobject($query_id = null)
	{
		if(empty($query_id))
		{
			$query_id = $this->query_result;
		}
		if($query_id && method_exists($query_id, 'fetch_object'))
		{
			$result = $query_id->fetch_object();
			return $result;
		}
		return false;
	}

	function sql_fetchrowset($query_id = 0)
	{
		$result = array();
		if(!$query_id)
		{
			$query_id = $this->query_result;
		}
		if($query_id && method_exists($query_id, 'fetch_assoc'))
		{
			while($row = $query_id->fetch_assoc())
			{
				$result[] = $row;
			}
			return $result;
		}
		return false;
	}

	function sql_fetcharrayset($query_id = 0)
	{
		$result = array();
		if(!$query_id)
		{
			$query_id = $this->query_result;
		}
		if($query_id && method_exists($query_id, 'fetch_array'))
		{
			while($row = $query_id->fetch_array())
			{
				$result[] = $row;
			}
			return $result;
		}
		return false;
	}

	function sql_numfields($query_id = 0)
	{
		if(!$query_id)
		{
			$query_id = $this->query_result;
		}
		if($query_id && isset($query_id->num_fields))
		{
			$result = (int)$query_id->num_fields;
			return $result;
		}
		return false;
	}

	function sql_fieldname($offset, $query_id = 0)
	{
		if(!$query_id)
		{
			$query_id = $this->query_result;
		}
		if($query_id && method_exists($query_id, 'field'))
		{
			$result = $query_id->field($offset);
			return $result;
		}
		return false;
	}

	function sql_fieldtype($offset, $query_id = 0)
	{
		if(!$query_id)
		{
			$query_id = $this->query_result;
		}
		if($query_id && method_exists($query_id, 'field'))
		{
			$result = $query_id->field($offset);
			return $result;
		}
		return false;
	}

	function sql_rowseek($rownum, $query_id = 0){
		if(!$query_id)
		{
			$query_id = $this->query_result;
		}
		if($query_id && method_exists($query_id, 'data_seek'))
		{
			$result = $query_id->data_seek($rownum);
			return $result;
		}
		return false;
	}
	function sql_nextid(){
		if($this->connection && isset($this->connection->insert_id))
		{
			$result = (int)$this->connection->insert_id;
			return $result;
		}
		return false;
	}
	function sql_freeresult($query_id = 0){
		if(!$query_id)
		{
			$query_id = $this->query_result;
		}

		if ( $query_id && method_exists($query_id, 'free_result') )
		{
			$query_id->free_result();
			return true;
		}
		return false;
	}
	function sql_error($query_id = 0)
	{
		$result["message"] = isset($this->connection->error) ? (string)$this->connection->error : '';
		$result["code"] = isset($this->connection->errno) ? (int)$this->connection->errno : 0;

		return $result;
	}
	
    function DBLogin()
    {
        if($this->connection instanceof MySQLi || $this->connection instanceof ProgrammitPdoCompatConnection)
        {
            return true;
        }

        $driver = strtolower(trim((string)$this->db_driver));
        if ($driver === 'pgsql' || $driver === 'postgres' || $driver === 'postgresql') {
            $port = (int)$this->db_port;
            if ($port <= 0) {
                $port = 5432;
            }
            try {
                $dsn = "pgsql:host=".$this->db_host.";port=".$port.";dbname=".$this->database;
                $pdo = new PDO($dsn, $this->username, $this->pwd, array(
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ));
                $this->connection = new ProgrammitPdoCompatConnection($pdo, 'pgsql', $this->db_schema);
                return true;
            } catch (Exception $e) {
                $this->HandleDBError("Database Login failed! " . $e->getMessage());
                return false;
            }
        }

        $host = $this->db_host;
        if ((int)$this->db_port > 0 && strpos((string)$host, ':') === false) {
            $host .= ':' . (int)$this->db_port;
        }
        $this->connection = @new MySQLi($host, $this->username, $this->pwd);
        if(!($this->connection instanceof MySQLi) || (int)$this->connection->connect_errno !== 0)
        {
            $msg = 'Database Login failed! Please make sure that the DB login credentials provided are correct';
            if ($this->connection instanceof MySQLi) {
                $msg .= ' (' . (int)$this->connection->connect_errno . ') ' . (string)$this->connection->connect_error;
            }
            $this->HandleDBError($msg);
            return false;
        }
        if(!$this->connection->select_db($this->database))
        {
            $this->HandleDBError('Failed to select database: '.$this->database.' Please make sure that the database name provided is correct');
            return false;
        }
        if(!$this->connection->query("SET NAMES 'UTF8'"))
        {
            $this->HandleDBError('Error setting utf8 encoding');
            return false;
        }
		$this->connection->query("SET NAMES 'utf8mb4'");
		$this->connection->query("SET CHARACTER SET utf8mb4");
		$this->connection->query("SET SESSION sql_mode = ''");

        return true;
    }

    function SetWebsiteTitle($siteTitle)
    {
        $this->siteTitle = $siteTitle;
    }

    function SetWebsiteName($sitename)
    {
        $this->sitename = $sitename;
    }

    function GetAbsoluteURLFolder()
    {
        $scriptFolder = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) ? 'https://' : 'http://';
        $scriptFolder .= $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']);
        return $scriptFolder;
    }

	function base_url()
	{
        $isHttps = isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == '1');
        $scheme = $isHttps ? 'https://' : 'http://';
        $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
        $basePath = '';
        $resolvedPath = false;

        $projectRoot = defined('DOC_ROOT_PATH') ? realpath(rtrim(DOC_ROOT_PATH, '/\\')) : realpath(dirname(__DIR__));
        $scriptFile = isset($_SERVER['SCRIPT_FILENAME']) ? realpath($_SERVER['SCRIPT_FILENAME']) : false;
        $scriptName = isset($_SERVER['SCRIPT_NAME']) ? str_replace('\\', '/', $_SERVER['SCRIPT_NAME']) : '';

        // Preferred path detection: compare script URL with script filesystem path relative to project root.
        if ($projectRoot !== false && $scriptFile !== false && $scriptName !== '') {
            $projectNorm = str_replace('\\', '/', rtrim($projectRoot, '/\\'));
            $scriptNorm = str_replace('\\', '/', $scriptFile);
            $projectPrefix = strtolower($projectNorm . '/');
            if (strpos(strtolower($scriptNorm), $projectPrefix) === 0) {
                $relativeFile = ltrim(substr($scriptNorm, strlen($projectNorm)), '/');
                $suffix = '/' . $relativeFile;
                if (substr($scriptName, -strlen($suffix)) === $suffix) {
                    $candidatePath = rtrim(substr($scriptName, 0, -strlen($suffix)), '/');
                    $basePath = ($candidatePath === '.' ? '' : $candidatePath);
                    $resolvedPath = true;
                }
            }
        }

        // Fallback: derive from DOCUMENT_ROOT and project root.
        if (!$resolvedPath && $projectRoot !== false && isset($_SERVER['DOCUMENT_ROOT'])) {
            $docRoot = realpath($_SERVER['DOCUMENT_ROOT']);
            if ($docRoot !== false) {
                $docNorm = str_replace('\\', '/', rtrim($docRoot, '/\\'));
                $projectNorm = str_replace('\\', '/', rtrim($projectRoot, '/\\'));
                $docPrefix = strtolower($docNorm . '/');
                if (strpos(strtolower($projectNorm), $docPrefix) === 0) {
                    $relativePath = trim(substr($projectNorm, strlen($docNorm)), '/');
                    if ($relativePath !== '') {
                        $basePath = '/' . $relativePath;
                    }
                    $resolvedPath = true;
                }
            }
        }

        // Last resort for edge cases.
        if (!$resolvedPath && $scriptName !== '') {
            $scriptDir = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
            if ($scriptDir !== '' && $scriptDir !== '.') {
                $basePath = $scriptDir;
            }
        }

        return $scheme . $host . $basePath . '/';
	}

    function GetSelfScript()
    {
        return htmlentities($_SERVER['PHP_SELF']);
    }

	function encrypt_key($paswd)
	{
	  $mykey=$this->getEncryptKey();
	  $encryptedPassword=$this->encryptPaswd($paswd,$mykey);
	  return $encryptedPassword;
	}
	 
	// function to get the decrypted user password
	function decrypt_key($paswd)
	{
	  $mykey=$this->getEncryptKey();
	  $decryptedPassword=$this->decryptPaswd($paswd,$mykey);
	  return $decryptedPassword;
	}
	 
	function getEncryptKey()
	{
		$secret_key = md5('eugcar');
		$secret_iv = md5('sanchez');
		$keys = $secret_key . $secret_iv;
		return $this->encryptor('encrypt', $keys);
	}
	function encryptPaswd($string, $key)
	{
	  $result = '';
	  for($i=0; $i<strlen ($string); $i++)
	  {
		$char = substr($string, $i, 1);
		$keychar = substr($key, ($i % strlen($key))-1, 1);
		$char = chr(ord($char)+ord($keychar));
		$result.=$char;
	  }
		return base64_encode($result);
	}
	 
	function decryptPaswd($string, $key)
	{
	  $result = '';
	  $string = base64_decode($string);
	  for($i=0; $i<strlen($string); $i++)
	  {
		$char = substr($string, $i, 1);
		$keychar = substr($key, ($i % strlen($key))-1, 1);
		$char = chr(ord($char)-ord($keychar));
		$result.=$char;
	  }
	 
		return $result;
	}
	
	function encryptor($action, $string) {
		$output = false;

		$encrypt_method = "AES-256-CBC";
		//pls set your unique hashing key
		$secret_key = md5('eugcar sanchez');
		$secret_iv = md5('sanchez eugcar');

		// hash
		$key = hash('sha256', $secret_key);
		
		// iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
		$iv = substr(hash('sha256', $secret_iv), 0, 16);

		//do the encyption given text/string/number
		if( $action == 'encrypt' ) {
			$output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
			$output = base64_encode($output);
		}
		else if( $action == 'decrypt' ){
			//decrypt the given text/string/number
			$output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
		}

		return $output;
	}

	function time_elapsed_string($ptime)
	{
		$etime = time() - $ptime;

		if ($etime < 1)
		{
			return '0 seconds';
		}

		$a = array( 365 * 24 * 60 * 60  =>  'year',
					 30 * 24 * 60 * 60  =>  'month',
						  24 * 60 * 60  =>  'day',
							   60 * 60  =>  'hour',
									60  =>  'minute',
									 1  =>  'second'
					);
		$a_plural = array( 'year'   => 'years',
						   'month'  => 'months',
						   'day'    => 'days',
						   'hour'   => 'hours',
						   'minute' => 'minutes',
						   'second' => 'seconds'
					);

		foreach ($a as $secs => $str)
		{
			$d = $etime / $secs;
			if ($d >= 1)
			{
				$r = round($d);
				return $r . ' ' . ($r > 1 ? $a_plural[$str] : $str) . ' ago';
			}
		}
	}

	function gen_id() 
	{ 
		$id = 'a'; 

		for ($i=1; $i<=12; $i++) { 
			if (rand(0,1)) { 
				// letter 
				$id .= chr(rand(65, 90)); 
			} else {
				// number;
				$id .= rand(0, 9); 
			}
		}
		return $id;
	}

	function get_client_ip() {
		$ipaddress = '';
		if (getenv('HTTP_CLIENT_IP'))
			$ipaddress = getenv('HTTP_CLIENT_IP');
		else if(getenv('HTTP_X_FORWARDED_FOR'))
			$ipaddress = getenv('HTTP_X_FORWARDED_FOR');
		else if(getenv('HTTP_X_FORWARDED'))
			$ipaddress = getenv('HTTP_X_FORWARDED');
		else if(getenv('HTTP_FORWARDED_FOR'))
			$ipaddress = getenv('HTTP_FORWARDED_FOR');
		else if(getenv('HTTP_FORWARDED'))
		   $ipaddress = getenv('HTTP_FORWARDED');
		else if(getenv('REMOTE_ADDR'))
			$ipaddress = getenv('REMOTE_ADDR');
		else
			$ipaddress = 'UNKNOWN';
		return $ipaddress;
	}

	function getBrowser()
	{
		$u_agent = isset($_SERVER['HTTP_USER_AGENT']) ? (string)$_SERVER['HTTP_USER_AGENT'] : '';
		$bname = 'Unknown';
		$platform = 'Unknown';
		$version= "";
		$ub = 'other';
		$matches = array(
			'browser' => array(),
			'version' => array()
		);

		//First get the platform?
		if (preg_match('/linux/i', $u_agent)) {
			$platform = 'linux';
		}
		elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
			$platform = 'mac';
		}
		elseif (preg_match('/windows|win32/i', $u_agent)) {
			$platform = 'windows';
		}

		// Next get the name of the useragent yes seperately and for good reason
		if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent))
		{
			$bname = 'Internet Explorer';
			$ub = "MSIE";
		}
		elseif(preg_match('/Firefox/i',$u_agent))
		{
			$bname = 'Mozilla Firefox';
			$ub = "Firefox";
		}
		elseif(preg_match('/Chrome/i',$u_agent))
		{
			$bname = 'Google Chrome';
			$ub = "Chrome";
		}
		elseif(preg_match('/Safari/i',$u_agent))
		{
			$bname = 'Apple Safari';
			$ub = "Safari";
		}
		elseif(preg_match('/Opera/i',$u_agent))
		{
			$bname = 'Opera';
			$ub = "Opera";
		}
		elseif(preg_match('/Netscape/i',$u_agent))
		{
			$bname = 'Netscape';
			$ub = "Netscape";
		}

		// finally get the correct version number
		$known = array('Version', $ub, 'other');
		$pattern = '#(?<browser>' . join('|', $known) .
		')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
		if (!preg_match_all($pattern, $u_agent, $matches)) {
			// we have no matching number just continue
		}

		// see how many we have
		$i = count($matches['browser']);
		if ($i > 1) {
			//we will have two since we are not using 'other' argument yet
			//see if version is before or after the name
			if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
				$version = isset($matches['version'][0]) ? $matches['version'][0] : '';
			}
			else {
				$version = isset($matches['version'][1]) ? $matches['version'][1] : (isset($matches['version'][0]) ? $matches['version'][0] : '');
			}
		}
		elseif ($i === 1) {
			$version = isset($matches['version'][0]) ? $matches['version'][0] : '';
		}
		else {
			$version = '';
		}

		// check if we have a number
		if ($version==null || $version=="") {$version="?";}

		return array(
			'userAgent' => $u_agent,
			'name'      => $bname,
			'version'   => $version,
			'platform'  => $platform,
			'pattern'    => $pattern
		);
	}

    function RedirectToURL($url)
    {
        header("Location: $url");
        exit;
    }

    function GetErrorMessage()
    {
        if(empty($this->error_message))
        {
            return '';
        }
        $errormsg = '';
		$errormsg .= "<div class='alert alert-danger'>";
		$errormsg .= "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;";
		$errormsg .= "</button>";
		$errormsg .= "<strong>".nl2br(htmlentities($this->error_message))."</strong>";
		$errormsg .= "</div>";
        return $errormsg;
    }

    function GetSuccessMessage()
    {
        if(empty($this->success_message))
        {
            return '';
        }
        $successmsg = '';
		$successmsg .= "<div class='alert alert-success'>";
		$successmsg .= "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;";
		$successmsg .= "</button>";
		$successmsg .= "<strong>".nl2br(htmlentities($this->success_message))."</strong>";
		$successmsg .= "</div>";
        return $successmsg;
    }

    function HandleSuccess($suc)
    {
		$this->success_message = $suc."\r\n";
    }

    function HandleError($err)
    {
		$this->error_message = $err."\r\n";
    }

    function HandleDBError($err)
    {
		$dbErr = '';
		if (is_object($this->connection) && isset($this->connection->error)) {
			$dbErr = (string)$this->connection->error;
		}
        $this->HandleError($err."\r\n ". $dbErr . ":");
    }

    function SanitizeForSQL($str)
    {
		if(!$this->DBLogin())
		{
			$this->HandleError("Database login failed!");
			return false;
		}
		
        if( function_exists("mysqli_real_escape_string") )
        {
			$ret_str = $this->connection->real_escape_string($str);
        }
        else
        {
              $ret_str = addslashes($str);
        }
        return $ret_str;
    }

    function Sanitize($str,$remove_nl=true)
    {
        $str = $this->StripSlashes($str);

        if($remove_nl)
        {
            $injections = array('/(\n+)/i',
                '/(\r+)/i',
                '/(\t+)/i',
                '/(%0A+)/i',
                '/(%0D+)/i',
                '/(%08+)/i',
                '/(%09+)/i'
                );
            $str = preg_replace($injections,'',$str);
        }

        return $str;
    }    
    function StripSlashes($str)
    {
        if(function_exists('get_magic_quotes_gpc')) {
            if(get_magic_quotes_gpc()) {
                $str = stripslashes($str);
            }
        }
        return $str;
    }

	function calc_time($seconds) {
		$days = (int)($seconds / 86400);
		$seconds -= ($days * 86400);
		$hours = 0;
		$minutes = 0;
		if ($seconds) {
			$hours = (int)($seconds / 3600);
			$seconds -= ($hours * 3600);
		}
		if ($seconds) {
			$minutes = (int)($seconds / 60);
			$seconds -= ($minutes * 60);
		}
		$time = array('days'=>(int)$days,
				'hours'=>(int)$hours,
				'minutes'=>(int)$minutes,
				'seconds'=>(int)$seconds);
		return $time;
	}
	
	function time_to_iso8601_duration($time) {
		$units = array(
			"Y" => 365*24*3600,
			"D" =>     24*3600,
			"H" =>        3600,
			"M" =>          60,
			"S" =>           1,
		);

		$str = "P";
		$istime = false;

		foreach ($units as $unitName => &$unit) {
			$quot  = intval($time / $unit);
			$time -= $quot * $unit;
			$unit  = $quot;
			if ($unit > 0) {
				if (!$istime && in_array($unitName, array("H", "M", "S"))) { // There may be a better way to do this
					$str .= "T";
					$istime = true;
				}
				$str .= strval($unit) . $unitName;
			}
		}

		return $str;
	}

	function openvpnLogs($log) {
		$handle = fopen($log, "r");
		$uid = 0;
		while (!feof($handle)) {
			$buffer = fgets($handle, 4096);
			unset($match);
			if (preg_match("^Updated,(.+)", $buffer, $match)) { 
				$status['updated'] = $match[1];
			}
			if (preg_match("/^(.+),(\d+\.\d+\.\d+\.\d+\:\d+),(\d+),(\d+),(.+)$/", $buffer, $match)) {
				if ($match[1] <> "Common Name") {
					$cn = $match[1];

					$userlookup[$match[2]] = $uid;

					$status['users'][$uid]['CommonName'] = $match[1];
					$status['users'][$uid]['RealAddress'] = $match[2];
					$status['users'][$uid]['BytesReceived'] = $match[3];
					$status['users'][$uid]['BytesSent'] = $match[4];
					$status['users'][$uid]['Since'] = $match[5];

					$uid++;
				}
			}

			if (preg_match("/^(\d+\.\d+\.\d+\.\d+),(.+),(\d+\.\d+\.\d+\.\d+\:\d+),(.+)$/", $buffer, $match)) {
				if ($match[1] <> "Virtual Address") {
					$address = $match[3];

					$uid = $userlookup[$address];

					$status['users'][$uid]['VirtualAddress'] = $match[1];
					$status['users'][$uid]['LastRef'] = $match[4];
				}
			}

		}

		fclose($handle);

		return($status);
	}

	function sizeformat($bytesize){
		$i=0;
		while(abs($bytesize) >= 1024){
			$bytesize=$bytesize/1024;
			$i++;
			if($i==4) break;
		}

		$units = array("Bytes","KB","MB","GB","TB");
		$newsize=round($bytesize,2);
		return("$newsize $units[$i]");
	}
	
	function get_data($url) {
		$ch = curl_init();
		$timeout = 5;
		$userAgent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)';
		curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
		curl_setopt($ch, CURLOPT_FAILONERROR, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_AUTOREFERER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}
	
	function ran_code() {
		$chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$pwd = '';
		srand((double)microtime()*1000000);
		$i = 0;
		while ($i <= 4)
		{
			$num = rand() % 33;
			$tmp = substr($chars, $num, 1);
			$pwd = $pwd . $tmp;
			$i++;
		}
		return $pwd;
	}
	
	function Contact()
	{
        $formvars = array();
		
        if(!$this->ValidateContactSubmission())
        {
            return false;
        }

        $this->CollectContactSubmission($formvars);

        $this->SendingEmail($formvars);
        return true;
	}
	
	function ValidateContactSubmission()
	{
		if(empty($_POST['contact_name'])){
			$this->HandleError("Contact Name is empty!");
			return false;
		}

		if(empty($_POST['contact_phone'])){
			$this->HandleError("Contact Phone is empty!");
			return false;
		}

		if(empty($_POST['contact_email']) || !filter_var($_POST['contact_email'],FILTER_VALIDATE_EMAIL)){
			$this->HandleError("Sorry! the email address is Invalid!". "\r\n" ."Please enter a valid email address!");
			return false;
		}

		if(empty($_POST['contact_subject'])){
			$this->HandleError("Contact Subject is empty!");
			return false;
		}

		if(empty($_POST['contact_message'])){
			$this->HandleError("Contact Message is empty!");
			return false;
		}

		return true;
	}

    function CollectContactSubmission(&$formvars)
    {
        $formvars['name'] = $this->Sanitize($_POST['contact_name']);
        $formvars['phone'] = $this->Sanitize($_POST['contact_phone']);
        $formvars['email'] = $this->Sanitize($_POST['contact_email']);
		$formvars['subject'] = $this->Sanitize($_POST['contact_subject']);
		$formvars['message'] = $this->Sanitize($_POST['contact_message']);
		$formvars['attachment'] = $_FILES['attachment']['name'];
		$formvars['tmp_name'] = $_FILES['attachment']['tmp_name'];
		$formvars['size'] = $_FILES['attachment']['size'];
    }

   function SendingEmail(&$formvars)
    {
        if (!class_exists('PHPMailer')) {
            require_once __DIR__ . '/phpmailer/PHPMailerAutoload.php';
        }
        $mailer = new PHPMailer();
        $mailer->CharSet = 'utf-8'; 

		$mailer->From		= $formvars['email'];
		$mailer->FromName	= $formvars['name'];
		$mailer->AddAddress("admin@gmail.com");

		$mailer->addReplyTo($formvars['email']);
		$mailer->addCC($formvars['email']);

		if(isset($formvars['attachment']) && $formvars['attachment'] != '') { // name|type|tmp_name|error|size|
			$target_dir		= "../_uploads/";
			$target_file 	= basename($formvars['attachment']);
			$rename 		= time() . '_' . $target_file;
			$uploadOk 		= 1;
			$imageFileType  = pathinfo($target_dir .$target_file,PATHINFO_EXTENSION);
			
			if(is_dir( $target_dir ) == false)
			{
				mkdir( $target_dir, 0777, true) or die('Error: ' . $this->connection->error);
			}
			// Check if image file is a actual image or fake image
			if(isset($_POST["submitted"])) {
				$check = getimagesize($formvars['tmp_name']);
				if($check !== false) {
					$uploadOk = 1;
				} else {
					$this->HandleError("File is not an image.");
					$uploadOk = 0;
				}
			}
			// Check if file already exists
			if (file_exists($target_file)) {
				$this->HandleError("Sorry, file already exists.");
				$uploadOk = 0;
			}
			// Check file size
			if ($formvars['size'] > 10000000) {
				$this->HandleError("Sorry, your file is too large.");
				$uploadOk = 0;
			}
			// Allow certain file formats
			if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
			&& $imageFileType != "gif" && $imageFileType != "zip" && $imageFileType != "rar" 
			&& $imageFileType != "doc" && $imageFileType != "docx" && $imageFileType != "pdf") {
				$this->HandleError("Sorry, only JPG, JPEG, PNG & GIF & ZIP & RAR & PDF files are allowed.");
				$uploadOk = 0;
			}
			// Check if $uploadOk is set to 0 by an error
			if ($uploadOk == 0) {
				$this->HandleError("Sorry, your file was not uploaded.");
			// if everything is ok, try to upload file
			} else {
				if (move_uploaded_file($formvars['tmp_name'], $target_dir . $rename)) {
					$mailer->AddAttachment($path = $target_dir . $rename, $name = $rename, $encoding = 'base64', $type = 'application/octet-stream');
				} else {
					$this->HandleError("Sorry, there was an error uploading your file.");
				}
			}
		}

        $mailer->Subject	= $formvars['subject']." - Contact Form:  ".$formvars['name'];
        $mailer->Body		= "You have received a new message from your website's contact form.\r\n" .
							  "Here are the details:\r\n" .
							  "Name: " . $formvars['name'] . "\r\n" . "\r\n" .
							  "Contact Number: " . $formvars['phone'] . "\r\n" . "\r\n" .
							  "Message: " . $formvars['message'] . "\r\n" . "\r\n" . "\r\n" . 
							  "Supported by: " . $this->siteTitle . "\r\n" .
							  "Web Developer: " . "jhoexii" . "\r\n" .
							  "Please Visit Our website: " . $this->base_url() . "\r\n" . "\r\n" .
							  "IP Address: " .  $this->get_client_ip() . "\r\n" .
							  "Browser: " . $_SERVER['HTTP_USER_AGENT'] . "\r\n";

		if(!$mailer->Send())
        {
            echo 'Mailer Error: ' . $mailer->ErrorInfo;
			$this->HandleError("Failed sending registration confirmation email.");
			return false;
        }
		return true;
    }
}
