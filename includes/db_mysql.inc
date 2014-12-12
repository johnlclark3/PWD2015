<?php

// JLClark: See additions and modificatiosn to base programs below
// JClark 2014-12-04: Update mysql statements to work with PHP 5...


require_once "db_utils.inc";

class mysql_dbconn extends dbconn {
	var $fconn;
	function db_extension_installed() {
// JClark 2014-12-03 update to work with PHP 5
//	    if (function_exists('mysql_connect')) {
        if (function_exists('mysqli_connect')) {
				return(true);
		} else {
				dbu_handle_error("Please make sure MySQL extension is loaded in php.ini");
		}
	}
	function db_connect($dbhost, $dbuser, $dbpass, $dbname) {
		if ($this->fconn) $this->close();
// JClark 2014-12-03 update to work with PHP 5 
//		$this->fconn = @mysql_connect($dbhost, $dbuser, $dbpass) or dbu_handle_error($this->db_lasterror());
//		@mysql_select_db($dbname) or dbu_handle_error($this->db_lasterror());
        $this->fconn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname) or dbu_handle_error($this->db_lasterror());
         
		$this->db_query('set names utf8');
		$this->db_query('set character set utf8');
		$this->db_query('set character_set_connection=utf8');
	}
	function db_close() {
// JClark 2014-12-03 update to work with PHP 5
//		if ($this->fconn) mysql_close($this->fconn);
        if ($this->fconn) mysqli_close($this->fconn);
		$this->fconn = NULL;
	}
	function db_lasterror() {
// JClark 2014-12-03 update to work with PHP 5
//		return ($this->fconn) ? mysql_error($this->fconn) : mysql_error();
        return ($this->fconn) ? mysqli_error($this->fconn) : mysqli_error();
	}
	function db_query($sql) {
// JClark 2014-12-03 update to work with PHP 5
//		return mysql_query($sql, $this->fconn);
        return mysqli_query($this->fconn, $sql);
	}
    function db_free_query($qry) {
// JClark 2014-12-03 update to work with PHP 5
//      if ($qry) mysql_free_result($qry);
        if ($qry) mysqli_free_result($qry);
    }    
	function db_query_limit($sql, $limit) {
		return $sql . (empty($limit)? "" : " LIMIT $limit");
	}
	function db_query_seek($qry, $offset) {
// JClark 2014-12-03 update to work with PHP 5
//		return @mysql_data_seek($qry, $offset);
        return @mysqli_data_seek($qry, $offset);
	}
	function db_next($qry) {
// JClark 2014-12-03 update to work with PHP 5
//      return @mysql_fetch_array($qry, MYSQL_ASSOC);
		return @mysqli_fetch_array($qry, MYSQL_ASSOC);
	}
	function db_quote_value($value) {
		if (!is_numeric($value)) {
// JClark 2014-12-03 update to work with PHP 5
//			return "'" . mysql_real_escape_string($value, $this->fconn) . "'";
            return "'" . mysqli_real_escape_string($this->fconn, $value) . "'";
		} else {
			return $value;
		}
	}
	function db_quote_field($fld) {
		return '`' . $fld . '`';
	}
    function db_quote_table($scheme, $table) {
        return empty($scheme) ? '`' . $table. '`' : '`' . $scheme . '`.`' . $table . '`';
    }
    function db_last_inserted_id() {
// JClark 2014-12-03 update to work with PHP 5
//      return mysql_insert_id($this->fconn);
        return mysqli_insert_id($this->fconn);
    }
    function db_get_one_row($sql) {
        $qry = $this->db_query($sql);
        $rslt = $qry ? $this->db_next($qry) : NULL;
        $this->db_free_query($qry);
        return $rslt;
    }
    function db_get_all_rows($sql) {
        $qry = $this->db_query($sql);
        if ($qry) {
            $rslt = array();
            while ($v = $this->db_next($qry)) {
                array_push($rslt, $v);
            }
            $this->db_free_query($qry);
        } else 
            $rslt = FALSE;
        return $rslt;
    }
    function db_rows_affected() {
// JClark 2014-12-03 update to work with PHP 5
//      return mysql_affected_rows($this->fconn);
        return mysqli_affected_rows($this->fconn);
    }
	// JClark: Add this function to strictly connect to MySQL; do not connect to a specific database schema yet	

// JClark 2014-12-04: Update function to pass in dbname... 
//	function db_connect_only($dbhost, $dbuser, $dbpass) {
    function db_connect_only($dbhost, $dbuser, $dbpass, $dbname) {
		if ($this->fconn) $this->close();
// JClark 2014-12-04: Update mysql statements to work with PHP 5...
//		$this->fconn = @mysql_connect($dbhost, $dbuser, $dbpass) or dbu_handle_error($this->db_lasterror());
        $this->fconn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname) or dbu_handle_error($this->db_lasterror());
		$this->db_query('set names utf8');
		$this->db_query('set character set utf8');
		$this->db_query('set character_set_connection=utf8');
	}

}
