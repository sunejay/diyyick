<?php
namespace Diyyick\Lib\PadamORM;

use Diyyick\Lib\Core\EntityForm;
/**
 * Description of Entity
 *
 * @author Sune
 */
abstract class Entity extends EntityForm
{
    private $db;
    
    public function __construct()
    {
        $this->db = new Database();
    }

    public function add()
    {
        foreach ($this->entityFields as $key) {
            $data[$key] = $this->$key;
        }
        $this->db->insert($this->entityTable, $data);
    }

    public function update()
    {
        foreach ($this->entityFields as $key) {
            if (!is_null($this->$key)) {
                $data[$key] = $this->$key;
            }
        }
        $where = '';
        foreach ($this->primaryKeys as $key) {
            $where .=' '.$key ." = ".$this->$key." &&";
        }
        $where = rtrim($where, '&');
        $this->db->update($this->entityTable, $data, $where);
    }

    public function remove()
    {
        $where = '';
        foreach ($this->primaryKeys as $key) {
            $where .=' '.$key ." = ".$this->$key." &&";
        }
        $where = rtrim($where, '&');
        $this->db->delete($this->entityTable, $where);
    }
    
    public function create(array $conditions)
    {
        foreach ($conditions as $key => $value) {
            $data[$key] = $value;
        }
        $this->db->insert($this->entityTable, $data);
    }
    
    public function findOne($conditions=array(), $fields='*', $order='', $limit=null, $offset=''){
    	$db = new Database();
        $where = '';
        foreach ($conditions as $key => $value) {
            if (is_string($value)) {
                $where .=' '.$key .' = "'.$value.'"'." &&";
            } else {
                $where .=' '.$key .' = '.$value." &&";
            }
        }
        $where = rtrim($where, '&');
        $db->select(static::tableName(), $where, $fields, $order, $limit, $offset);
        return $db->single();
    }
    
    public function findAll($conditions=array(), $fields='*', $order='', $limit=null, $offset=''){
    	$db = new Database();
        $where = '';
        foreach ($conditions as $key => $value) {
            if (is_string($value)) {
                $where .=' '.$key .' = "'.$value.'"'." &&";
            } else {
                $where .=' '.$key .' = '.$value." &&";
            }
        }
        $where = rtrim($where, '&');
        $db->select(static::tableName(), $where, $fields, $order, $limit, $offset);
        return $db->resultSet();
    }

    public function getOne($conditions=array(), $fields='*', $order='', $limit=null, $offset=''){
    	$db = new Database();
        $where = '';
        foreach ($conditions as $key => $value) {
            if (is_string($value)) {
                $where .=' '.$key .' = "'.$value.'"'." &&";
            } else {
                $where .=' '.$key .' = '.$value." &&";
            }
        }
        $where = rtrim($where, '&');
        $db->select(static::tableName(), $where, $fields, $order, $limit, $offset);
        return $db->singleObject(static::className());
    }
    
    public function getAll($conditions=array(), $fields='*', $order='', $limit=null, $offset=''){
    	$db = new Database();
        $where = '';
        foreach ($conditions as $key => $value) {
            if (is_string($value)) {
                $where .=' '.$key .' = "'.$value.'"'." &&";
            } else {
                $where .=' '.$key .' = '.$value." &&";
            }
        }
        $where = rtrim($where, '&');
        $db->select(static::tableName(), $where, $fields, $order, $limit, $offset);
        return $this->db->objectSet(static::className());
    }
    
    public function slugify(string $param) {
        $slug = preg_replace('/[^a-z0-9]+/i', '-', trim(strtolower($param)));
        return $slug . '-' . time();
    }
}
