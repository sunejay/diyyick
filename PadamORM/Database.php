<?php
namespace Diyyick\Lib\PadamORM;
/**
 * Description of Database
 *
 * @author Sune
 */
class Database 
{
    private $host = HOST;
    private $user = USER;
    private $password = PASSWORD;
    private $dbname = DBNAME;
    private $dbtype = DBTYPE;

    private $db_handler;
    private $error;
    private $statement;

    public function __construct()
    {
        // Set DNS 
        $dns = $this->dbtype . ':host=' . $this->host . ';dbname=' . $this->dbname;
        // Set options
        $options = array(
            \PDO::ATTR_PERSISTENT => true, 
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
        );
        // Create a new PDO instance
        try {
            $this->db_handler = new \PDO($dns, $this->user, $this->password, $options);
        }
        // Catch any errors
        catch(\PDOException $e) {
            $this->error = $e->getMessage();
        }
    }
    /**
     * Prepares and  return statement
     */
    public function prepare($query)
    {
        $this->statement = $this->db_handler->prepare($query);
    }
    /**
     * Binds the values of the parameters in the statement
     */
    public function bind($param, $value, $type=null)
    {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = \PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = \PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = \PDO::PARAM_NULL;
                    break;
                default:
                    $type = \PDO::PARAM_STR;
                    break;
            }
        }
        $this->statement->bindValue($param, $value, $type);
    }
    /**
     * Execute the statement
     */
    public function execute()
    {
        $this->statement->execute();
    }
    /**
     * Select data
     */
    public function select($table, $where='', $fields='*', $order='', $limit=null, $offset='')
    {
        $query = "SELECT $fields FROM $table "
            .($where ? " WHERE $where " : '')
            .($limit ? " LIMIT $limit " : '')
            .(($offset && $limit ? " OFFSET $offset " : ''))
            .($order ? " ORDER BY $order " : '');
        $this->prepare($query);
    }
    /**
     * Insert data
     */
    public function insert($table, $data)
    {
        ksort($data);
        $fieldNames = implode(',', array_keys($data));
        $fieldValues = ':'.implode(', :', array_keys($data));
        $query = "INSERT INTO $table ($fieldNames) VALUES($fieldValues)";
        $this->prepare($query);
        foreach ($data as $key => $value) {
            $this->bind(":$key", $value);
        }
        $this->execute();
    }
    /**
     * Update data
     */
    public function update($table, array $data, $where='')
    {
        ksort($data);
        $fieldDetails = null;
        foreach ($data as $key => $value) {
            $fieldDetails .="$key = :$key,";
        }
        $fieldDetails = rtrim($fieldDetails, ',');
        $query = "UPDATE $table SET $fieldDetails ".($where ? 'WHERE '.$where : '');
        $this->prepare($query);
        foreach ($data as $key => $value) {
            $this->bind(":$key", $value);
        }
        $this->execute();
    }
    /**
     * Delete data
     */
    public function delete($table, $where, $limit=1)
    {
        $this->prepare("DELETE FROM $table WHERE $where LIMIT $limit");
        $this->execute();
    }
    /**
     * Return result set as associative array
     */
    public function resultSet()
    {
        $this->execute();
        return $this->statement->fetchAll(\PDO::FETCH_ASSOC);
    }
    /**
     * Return single associative array
     */
    public function single()
    {
        $this->execute();
        return $this->statement->fetch(\PDO::FETCH_ASSOC);
    }
    /**
     * Return objectSet
     */
    public function objectSet($entityClass)
    {
        $this->execute();
        $this->statement->setFetchMode(\PDO::FETCH_CLASS, $entityClass);
        return $this->statement->fetchAll();
    }
    /**
     * Return single object
     */
    public function singleObject($entityClass)
    {
        $this->execute();
        $this->statement->setFetchMode(\PDO::FETCH_CLASS, $entityClass);
        return $this->statement->fetch();
    }
    /**
     * Return row count
     */
    public function rowCount()
    {
        return $this->statement->rowCount();
    }
    /**
     * Return last insert id
     */
    public function lastInsertId()
    {
        return $this->db_handler->lastInsertId();
    }
    /**
     * Return begin transaction
     */
    public function beginTransaction()
    {
        return $this->db_handler->beginTransaction();
    }
    /**
     * Return end transaction
     */
    public function endTransaction()
    {
        return $this->db_handler->commit();
    }
    /**
     * Return cancel transaction
     */
    public function cancelTransaction()
    {
        return $this->db_handler->rollBack();
    }
    /**
     * Return debug dump @params
     */
    public function debugDumpParams()
    {
        return $this->db_handler->debugDumpParams();
    }
}
