<?php

require_once('class.abstract_dbase.php');

class MySQLDBase extends AbstractDBase
{
	private $m_dbConn;
    private $m_Result;
 
    /* 
     * Connect function, also selects correct database
     * Returns 1 upon success, otherwise 0
     */
    protected function connect($address, $user, $pwd, $name) {
        $this->m_dbConn = mysql_connect($address, $user, $pwd);
 
        if (! $this->m_dbConn) 
        	throw new Exception("Не удалось соединиться с базой данных", E_USER_ERROR);
        
        if (! mysql_select_db($name, $this->m_dbConn))
        	throw new Exception("Не удалось выбрать базу данных", E_USER_ERROR);
    }	
 
    /*Отсоединение от БД*/
    protected function disconnect() {
    	if (is_resource($this->m_dbConn)) {
    		mysql_close($this->m_dbConn);
    	}
    }
    
    public function select($query) {
    	$this->m_Result = mysql_query($query, $this->m_dbConn);
    	if (! $this->m_Result) {
    		throw new Exception("Ошибка при выполнении запроса.<br>".mysql_error($this->m_dbConn)."<br>");
    	}
    	$arReturn = array();
    	while ($row = mysql_fetch_assoc($this->m_Result)) {
    		$arReturn[] = $row;
    	}
    	
    	return $arReturn;
    }
 
    /*
     * Does a mysql-query, returns 1 upon sucess otherwise 0
     */
    /*function query($query) {
        $this->m_Result = mysql_query($query, $this->m_dbConn);
 
        if ($this->m_Result != 0) {
            return 1;
        }
        else {
            return 0;
        }
    }
 
    /*
     * Fetches an array row
     */
    /*function fetchRow() {
        return mysql_fetch_array($this->m_Result);
    }
 
    function fetchAll() {
        while ($row = mysql_fetch_array($this->m_Result)) {
            $a_rs[] = $row;
        }
        mysql_free_result($this->m_Result);
        return $a_rs;
    }
    /*
     * Get number of rows
     */
    /*function getNumRows() {
        return mysql_num_rows($this->m_Result);
    }
 
    /*
     * Free resources allocated by a query 
     */
    /*function freeResult() {
        mysql_free_result($this->m_Result);
    }
 
    /*
     * Get errorstring
     */
    /*function getError() {
        return mysql_error($this->m_dbConn);
    }*/
}
?>