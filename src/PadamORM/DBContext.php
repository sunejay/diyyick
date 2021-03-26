<?php
namespace Diyyick\PadamORM;
/**
 * Description of DBContext
 *
 * @author Sune
 */
class DBContext 
{
    private $db;
    private $entities = array();

    public function __construct()
    {
        $this->db = new Database();
    }

    public function findOne(Entity $entity, $conditions=array(), $fields='*', $order='', $limit=null, $offset='')
    {
        $where = '';
        foreach ($conditions as $key => $value) {
            if (is_string($value)) {
                $where .=' '.$key .' = "'.$value.'"'." &&";
            } else {
                $where .=' '.$key .' = '.$value." &&";
            }
        }
        $where = rtrim($where, '&');
        $this->db->select($entity->tableName(), $where, $fields, $order, $limit, $offset);
        return $this->db->singleObject($entity->className());
    }
    
    public function findAll(Entity $entity, $conditions=array(), $fields='*', $order='', $limit=null, $offset='')
    {
        $where = '';
        foreach ($conditions as $key => $value) {
            if (is_string($value)) {
                $where .=' '.$key .' = "'.$value.'"'." &&";
            } else {
                $where .=' '.$key .' = '.$value." &&";
            }
        }
        $where = rtrim($where, '&');
        $this->db->select($entity->tableName(), $where, $fields, $order, $limit, $offset);
        return $this->db->objectSet($entity->className());
    }
    
    public function create(Entity $entity, array $conditions)
    {
        foreach ($conditions as $key => $value) {
            $data[$key] = $value;
        }
        $this->db->insert($entity->tableName(), $data);
    }

    public function commit()
    {
        foreach ($this->entities as $entity) {
            switch ($entity->entityState) {
                case EntityState::Created:
                    foreach ($entity->entityFields as $key) {
                        $data[$key] = $entity->$key;
                    }
                    return $this->db->insert($entity->tableName(), $data);
                    break;

                case EntityState::Modified:
                    foreach ($entity->entityFields as $key) {
                        if (!is_null($entity->$key)) {
                            $data[$key] = $entity->$key;
                        }
                    }
                    $where = '';
                    foreach ($entity->primaryKeys as $key) {
                        $where .=' '.$key ." = ".$entity->$key." &&";
                    }
                    $where = rtrim($where, '&');
                    return $this->db->update($entity->tableName(), $data, $where);
                    break;

                case EntityState::Deleted:
                    $where = '';
                    foreach ($entity->primaryKeys as $key) {
                        $where .=' '.$key ." = ".$entity->$key." &&";
                    }
                    $where = rtrim($where, '&');
                    return $this->db->delete($entity->tableName(), $where);
                    break;

                default:
                    # code...
                    break;
            }
        }
        unset($this->entities);
    }

    public function add(Entity $entity)
    {
        $entity->entityState = EntityState::Created;
        array_push($this->entities, $entity);
    }

    public function update(Entity $entity)
    {
        $entity->entityState = EntityState::Modified;
        array_push($this->entities, $entity);
    }

    public function remove(Entity $entity)
    {
        $entity->entityState = EntityState::Deleted;
        array_push($this->entities, $entity);
    }
}
