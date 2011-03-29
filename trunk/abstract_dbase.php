<?php
abstract class AbstractDBase
{
    private $m_dbAddress; /* Address of SQL server */
    private $m_dbUser; /* Account name (username) */
    private $m_dbPwd;     /* Password */
    private $m_dbName;    /* Initial database */
 
    /* Base constructor */
    public function __construct($address, $account, $pwd, $name) {
        $this->m_dbAddress = $address;
        $this->m_dbUser = $account;
        $this->m_dbPwd = $pwd;
        $this->m_dbName = $name;
 
        /* Call the connect method in the child class */
        $this->connect($this->m_dbAddress, $this->m_dbUser,
            $this->m_dbPwd, $this->m_dbName);
    }
 
    /* Let the child disconnect when the object is destroyed */
    public function __destruct() {
        $this->disconnect();
    }
    
    public function getDBName() {
    	return $this->m_dbName;
    }
    
    abstract public function select($query);
    //abstract public function insert();
    //abstract public function update();
    //abstract public function delete();
    
    //abstract function fetchRow();
    //abstract function fetchAll();
    //abstract function getNumRows();
    //abstract function freeResult();
    //abstract function getError();
 
    /* We are about to be serialized, disconnect
     * and return data needed for serialization
     */
    public function __sleep() {
        $this->disconnect();
        return array('m_dbAddress', 'm_dbUser',
            'm_dbPwd', 'm_dbName');
    }
 
    /* We have been unserialized, re-connect */
    public function __wakeup() {
        $this->connect($this->m_dbAddress, $this->m_dbUser,
            $this->m_dbPwd, $this->m_dbName);
    }
 
    /*
     * Abstract methods that needs to be implemented by child classes
     */
    abstract protected function connect($address, $account, $pwd, $name);
    abstract protected function disconnect();
    
}
?>
