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
        $this->connect($this->m_dbAddress, $this->m_dbUser, $this->m_dbPwd, $this->m_dbName);
    }
 
    /* Let the child disconnect when the object is destroyed */
    public function __destruct() {
        $this->disconnect();
    }
    
    public function getDBName() {
    	return $this->m_dbName;
    }
    
    /**
     * Функция выполняет запрос SELECT
     * @param string $query код запроса
     * @return array выборка в виде двумерного ассоциативного массива, key -> № ряда, value - ассоциативный массив выборки
     */
    abstract public function select($query);
    /**
     * Функция выполняет запрос INSERT
     * @param string $table название таблицы
     * @param array $arFieldVals key -> имя поля, value -> значение поля
     * @return int id, присвоенный вставленной записи
     */
    abstract public function insert($table, $arFieldVals);
    /**
     * Функция выполняет запрос UPDATE
     * @param string $table название таблицы
     * @param array $arFieldVals key -> имя поля, которое было изменено, value -> новое значение поля
     * @param array $arConds задает условие WHERE, key-> имя поля, value -> значение поля
     */
    abstract public function update($table, $arFieldVals, $arConds);
    
    /**
     * Функция выполняет запрос DELETE
     * @param string $table название таблицы
     */
    abstract public function delete($table);
    
    /**
     * Функция возвращает список столбцов таблицы $table
     * @param string имя таблицы
     * @return array массив с именами столбцов
     */
    abstract public function getFieldsNames($table);
    
    //abstract function fetchRow();
    //abstract function fetchAll();
    //abstract function getNumRows();
    //abstract function freeResult();
    //abstract function getError();
 
    /* We are about to be serialized, disconnect
     * and return data needed for serialization
     */
    /*public function __sleep() {
        $this->disconnect();
        return array('m_dbAddress', 'm_dbUser',
            'm_dbPwd', 'm_dbName');
    }*/
 
    /* We have been unserialized, re-connect */
    /*public function __wakeup() {
        $this->connect($this->m_dbAddress, $this->m_dbUser, $this->m_dbPwd, $this->m_dbName);
    }*/
 
    /*
     * Abstract methods that needs to be implemented by child classes
     */
    abstract protected function connect($address, $account, $pwd, $name);
    abstract protected function disconnect();
    
}
?>
