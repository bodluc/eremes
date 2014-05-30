<?php


/*
V4.94 23 Jan 2007  (c) 2000-2007 John Lim (jlim#natsoft.com.my). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence.
  Set tabs to 8.
 
*/ 

require_once("adodb-pdo.inc.php");

function from_unixtime1($unixtime)
{
  return date('Y-m-d H:i:s', $unixtime);
}

function from_unixtime2($unixtime, $format)
{
	$newformat = str_replace(array("%a","%b","%Y","%c","%d","%e","%H","%M", "%W","%m" ), 
  												 array("D" ,"M" ,"Y" ,"n" ,"d" ,"j" ,"H", "F", "l","m"  ), $format);
  return date($newformat, $unixtime);
}

function unix_timestamp($date) {
	return strtotime($date);
}

function concat($param1="", $param2="", $param3="", $param4="", $param5="", $param6="", $param7="", $param8="", $param9="", $param10="") {
	return $param1 . $param2 . $param3 . $param4 . $param5 . $param6 . $param7 . $param8 . $param9 . $param10;
}

function sqlite_md5($instring) {
	return md5($instring);
}

function mysql_now() {
	return date('Y-m-d H:i:s');
}

class ADODB_pdo_sqlite extends ADODB_pdo {
	var $databaseType = "pdo";
	var $replaceQuote = "''"; // string to use to replace quotes
	var $concat_operator='||';
	var $_errorNo = 0;
	var $hasLimit = true;	
	var $hasInsertID = true; 		/// supports autoincrement ID?
	var $_autocommit = false; 	
	var $hasAffectedRows = true; 	/// supports affected rows for update/delete?
	var $metaTablesSQL = "SELECT name FROM sqlite_master WHERE type='table' ORDER BY name";
	var $fmtTimeStamp = "'Y-m-d H:i:s'";	
	var $hasTransactions = true;
	
	function _init($parentDriver)
	{
		$parentDriver->hasTransactions = true;
		$parentDriver->_bindInputArray = true;
		$parentDriver->hasInsertID = true;
	}
	
	// returns true or false
	function _connect($argDSN, $argUsername, $argPassword, $argDatabasename, $persist=false)
	{
	  if (!$argDSN) { error_log("Must specify database name when connecting to a Sqllite database.", 0) ; return false; }
		$this->dsnType = "sqlite";
		$argDSN = "sqlite:".$argDSN;

		try {
			$this->_connectionID = new PDO($argDSN, $argUsername, $argPassword);
		} catch (Exception $e) {
			$this->_connectionID = false;
			$this->_errorno = -1;
			//var_dump($e);
			$this->_errormsg = 'Connection attempt failed: '.$e->getMessage();
			return false;
		}
		
		if ($this->_connectionID) {
			switch(ADODB_ASSOC_CASE){
			case 0: $m = PDO::CASE_LOWER; break;
			case 1: $m = PDO::CASE_UPPER; break;
			default:
			case 2: $m = PDO::CASE_NATURAL; break;
			}
			
			//$this->_connectionID->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_SILENT );
			$this->_connectionID->setAttribute(PDO::ATTR_CASE,$m);
			$this->_driver = $this;
//			$this->_driver->_connectionID = $this->_connectionID;
//			$this->_UpdatePDO();
			$this->_createFunctions();
			return true;
		}
		$this->_driver = new ADODB_pdo_base();
		return false;
	}	
	
		// dayFraction is a day in floating point
	function OffsetDate($dayFraction,$date=false)
	{		
		if (!$date) $date = $this->sysDate;
		
		$fraction = $dayFraction * 24 * 3600;
		return $date . ' + INTERVAL ' .	 $fraction.' SECOND';
		
//		return "from_unixtime(unix_timestamp($date)+$fraction)";
	}
	
	function ServerInfo()
	{
		$arr['version'] = $this->_connectionID->getAttribute(PDO::ATTR_SERVER_VERSION);
		$arr['description'] = 'SQLite ';
		return $arr;
	}
	
	function qstr($s,$magic_quotes=false) {
		if (!$magic_quotes) {
			return $this->_connectionID->Quote($s);
	  }
	  // undo magic quotes for "
	  $s = str_replace('\\"','"',$s);
	  return $this->_connectionID->Quote($s);
	}
	
	
	function &MetaTables($ttype=false,$showSchema=false,$mask=false) 
	{	
		$save = $this->metaTablesSQL;
		if ($showSchema && is_string($showSchema)) {
			$this->metaTablesSQL .= " from $showSchema";
		}
		
		if ($mask) {
			$mask = $this->qstr($mask);
			$this->metaTablesSQL .= " like $mask";
		}
		$ret =& ADOConnection::MetaTables($ttype,$showSchema);
		
		$this->metaTablesSQL = $save;
		return $ret;
	}
	
	function Prepare($sql)
	{
		$stmt = $this->_connectionID->prepare($sql);
		if (!$stmt) {
			echo $this->ErrorMsg();
			return $sql;
		}
		return array($sql,$stmt);
	}
	
	function SetTransactionMode( $transaction_mode ) 
	{
		$this->_transmode  = $transaction_mode;
		if (empty($transaction_mode)) {
			$this->Execute('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ');
			return;
		}
		if (!stristr($transaction_mode,'isolation')) $transaction_mode = 'ISOLATION LEVEL '.$transaction_mode;
		$this->Execute("SET SESSION TRANSACTION ".$transaction_mode);
	}
	
	function _createFunctions()
	{
		$this->_connectionID->sqliteCreateFunction('FROM_UNIXTIME','from_unixtime1',1);
		$this->_connectionID->sqliteCreateFunction('FROM_UNIXTIME','from_unixtime2',2);
		
		$this->_connectionID->sqliteCreateFunction('UNIX_TIMESTAMP','unix_timestamp',1);
		$this->_connectionID->sqliteCreateFunction('CONCAT','concat');
		$this->_connectionID->sqliteCreateFunction('NOW',"mysql_now");
		
		$this->_connectionID->sqliteCreateFunction('MD5',"sqlite_md5", 1);
		
// 		$this->_connectionID->sqliteCreateAggregate('group_concat', 'group_concat_step', 'group_concat_finalize');
//		$this->_connectionID->sqliteCreateFunction('adodb_date', 'adodb_date', 1);
//		$this->_connectionID->sqliteCreateFunction('adodb_date2', 'adodb_date2', 2);
		
	}
	
	function& AutoExecute($table, $fields_values, $mode = 'INSERT', $where = FALSE, $forceUpdate=true, $magicq=false) {
		
		switch((string) $mode) {
		case 'UPDATE':
		case '2':
			$sql = 'SELECT * FROM '.$table;  
			if ($where!==FALSE) $sql .= ' WHERE '.$where;
			else if ($mode == 'UPDATE' || $mode == 2 /* DB_AUTOQUERY_UPDATE */) {
				ADOConnection::outp('AutoExecute: Illegal mode=UPDATE with empty WHERE clause');
				return $false;
			}
			$rs =& $this->SelectLimit($sql,1);
			if (!$rs) return $false; // table does not exist
			$rs->tableName = $table;
			
			$sql = $this->GetUpdateSQL($rs, $fields_values, $forceUpdate, $magicq);
			break;
		case 'INSERT':
		case '1':
			$rs	= $table;
			$sql = $this->GetInsertSQL($rs, $fields_values, $magicq);
			break;
		default:
			ADOConnection::outp("AutoExecute: Unknown mode=$mode");
			return $false;
		}
		$ret = false;
		if ($sql) $ret = $this->Execute($sql);
		if ($ret) $ret = true;
		return $ret;		
	}
	
	
	// mark newnham
	function &MetaColumns($tab)
	{
	  global $ADODB_FETCH_MODE;
	  $false = false;
	  $save = $ADODB_FETCH_MODE;
	  $ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	  if ($this->fetchMode !== false) $savem = $this->SetFetchMode(false);
	  $rs = $this->Execute("PRAGMA table_info('$tab')");
	  if (isset($savem)) $this->SetFetchMode($savem);
	  if (!$rs) {
	    $ADODB_FETCH_MODE = $save; 
	    return $false;
	  }
	  $arr = array();
	  while ($r = $rs->FetchRow()) {
	    $type = explode('(',$r['type']);
	    $size = '';
	    if (sizeof($type)==2)
	    $size = trim($type[1],')');
	    $fn = strtoupper($r['name']);
	    $fld = new ADOFieldObject;
	    $fld->name = $r['name'];
	    $fld->type = $type[0];
	    $fld->max_length = $size;
	    $fld->not_null = $r['notnull'];
	    $fld->default_value = $r['dflt_value'];
	    $fld->scale = 0;
	    if ($save == ADODB_FETCH_NUM) $arr[] = $fld;	
	    else $arr[strtoupper($fld->name)] = $fld;
	  }
	  $rs->Close();
	  $ADODB_FETCH_MODE = $save;
	  return $arr;
	}

		
	
	// parameters use PostgreSQL convention, not MySQL
	function &SelectLimit($sql,$nrows=-1,$offset=-1,$inputarr=false,$secs=0)
	{
		$offsetStr =($offset>=0) ? "$offset," : '';
		// jason judge, see http://phplens.com/lens/lensforum/msgs.php?id=9220
		if ($nrows < 0) $nrows = '18446744073709551615'; 
		
		if ($secs)
			$rs =& $this->CacheExecute($secs,$sql." LIMIT $offsetStr$nrows",$inputarr);
		else
			$rs =& $this->Execute($sql." LIMIT $offsetStr$nrows",$inputarr);
		return $rs;
	}
	
  function SelectDB($DatabaseName) {
  	return (true); // Always show connected - we don't have separate databases per file.
	}
	
	/*
		This algorithm is not very efficient, but works even if table locking
		is not available.
		
		Will return false if unable to generate an ID after $MAXLOOPS attempts.
	*/
	var $_genSeqSQL = "create table %s (id integer)";
	
	function GenID($seq='adodbseq',$start=1)
	{	
		// if you have to modify the parameter below, your database is overloaded,
		// or you need to implement generation of id's yourself!
		$MAXLOOPS = 100;
		//$this->debug=1;
		while (--$MAXLOOPS>=0) {
			@($num = $this->GetOne("select id from $seq"));
			if ($num === false) {
				$this->Execute(sprintf($this->_genSeqSQL ,$seq));	
				$start -= 1;
				$num = '0';
				$ok = $this->Execute("insert into $seq values($start)");
				if (!$ok) return false;
			} 
			$this->Execute("update $seq set id=id+1 where id=$num");
			
			if ($this->affected_rows() > 0) {
				$num += 1;
				$this->genID = $num;
				return $num;
			}
		}
		if ($fn = $this->raiseErrorFn) {
			$fn($this->databaseType,'GENID',-32000,"Unable to generate unique id after $MAXLOOPS attempts",$seq,$num);
		}
		return false;
	}

	function CreateSequence($seqname='adodbseq',$start=1)
	{
		if (empty($this->_genSeqSQL)) return false;
		$ok = $this->Execute(sprintf($this->_genSeqSQL,$seqname));
		if (!$ok) return false;
		$start -= 1;
		return $this->Execute("insert into $seqname values($start)");
	}
	
	var $_dropSeqSQL = 'drop table %s';
	function DropSequence($seqname)
	{
		if (empty($this->_dropSeqSQL)) return false;
		return $this->Execute(sprintf($this->_dropSeqSQL,$seqname));
	}
	
	function &MetaIndexes($table, $primary = FALSE, $owner=false)
	{
		$false = false;
		// save old fetch mode
        global $ADODB_FETCH_MODE;
        $save = $ADODB_FETCH_MODE;
        $ADODB_FETCH_MODE = ADODB_FETCH_NUM;
        if ($this->fetchMode !== FALSE) {
               $savem = $this->SetFetchMode(FALSE);
        }
		$SQL=sprintf("SELECT name,sql FROM sqlite_master WHERE type='index' AND tbl_name='%s'", strtolower($table));
        $rs = $this->Execute($SQL);
        if (!is_object($rs)) {
			if (isset($savem)) 
				$this->SetFetchMode($savem);
			$ADODB_FETCH_MODE = $save;
            return $false;
        }

		$indexes = array ();
		while ($row = $rs->FetchRow()) {
			if ($primary && preg_match("/primary/i",$row[1]) == 0) continue;
            if (!isset($indexes[$row[0]])) {

			$indexes[$row[0]] = array(
				   'unique' => preg_match("/unique/i",$row[1]),
				   'columns' => array());
			}
			/**
			  * There must be a more elegant way of doing this,
			  * the index elements appear in the SQL statement
			  * in cols[1] between parentheses
			  * e.g CREATE UNIQUE INDEX ware_0 ON warehouse (org,warehouse)
			  */
			$cols = explode("(",$row[1]);
			$cols = explode(")",$cols[1]);
			array_pop($cols);
			$indexes[$row[0]]['columns'] = $cols;
		}
		if (isset($savem)) { 
            $this->SetFetchMode($savem);
			$ADODB_FETCH_MODE = $save;
		}
        return $indexes;
	}
	
}

// class ADODB_pdo_sqlite extends ADORecordSet_pdo {


?>