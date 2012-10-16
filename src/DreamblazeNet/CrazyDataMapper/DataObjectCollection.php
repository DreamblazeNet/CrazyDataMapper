<?php
namespace DreamblazeNet\CrazyDataMapper;
/**
 * Created by JetBrains PhpStorm.
 * User: mriedmann
 * Date: 09.10.12
 * Time: 18:39
 * To change this template use File | Settings | File Templates.
 */
class DataObjectCollection implements \Iterator
{
    protected $filterClauses = array();
    protected $orderClauses = array();
    protected $includeClauses = array();
    protected $limit = null;
    protected $offset = null;

    protected $mapper = null;
    protected $object = null;

    protected $sqlCache = array();

    protected $objects = array();
    protected $objectsPointer = 0;

    /**
     * @var \Dreamblaze\GenSql\Select
     */
    protected $sqlQuery = null;

    public function __construct(ObjectMapper $mapper, IDataObject $object){
        $this->mapper = $mapper;
        $this->object = $object;
    }

    public function filter(Array $filterClauses){
        $this->filterClauses = array_merge($this->filterClauses, $filterClauses);
        return $this;
    }

    public function order(Array $orderClauses){
        $this->orderClauses = array_merge($this->orderClauses, $orderClauses);
        return $this;
    }

    public function limit($maxResults, $offset = null){
        $this->limit = $maxResults;
        $this->offset = $offset;
        return $this;
    }

    public function includes(Array $includeClauses){
        $this->includeClauses = array_merge($this->includeClauses, $includeClauses);
        return $this;
    }

    public function reset(){
        $this->filterClauses = array();
        $this->orderClauses = array();
        $this->includeClauses = array();
        $this->limit = null;
        $this->offset = null;

        $this->sqlQuery = null;
    }

    public function getValues(){
        if(is_null($this->sqlQuery))
            $this->buildSelectQuery();

        return $this->sqlQuery->give_sql_values();
    }

    public function getSql(){
        if(is_null($this->sqlQuery))
            $this->buildSelectQuery();

        return $this->sqlQuery->give_sql();
    }

    public function getMapper(){
        return $this->mapper;
    }

    public function getObjects(){
        $this->fetchData();
        return $this->objects;
    }

    protected function getMap(){
        return $this->mapper->getMap($this->object);
    }

    protected function fetchData(){
        $this->buildSelectQuery();
        $result = $this->mapper->fetchFromDatabase($this->getSql(), $this->getValues());
        $data = array();
        foreach ($result as $row) {
            $obj = clone $this->object;
            $obj->setMapper($this->mapper);
            foreach ($row as $field=>$value) {
                $obj->$field = $value;
            }
            $data[] = $obj;
        }
        $this->objects = $data;
    }

    protected function buildSelectQuery(){
        $map = $this->getMap();
        $mapFields = $map->getFields();
        $fields = array_map(function($elem){
            if(isset($elem['name']) && isset($elem['type']) && $elem['type'] != 'relation')
                return $elem['name'];
            else
                return null;
        }, $mapFields);

        $relations = array_filter($mapFields, function($elem){
            return isset($elem['type']) && $elem['type'] == 'relation';
        });

        $query = new \DreamblazeNet\GenSql\Select($map->getTableName(), $fields);

        $query->where($this->filterClauses);
        $query->limit($this->limit);
        $query->offset($this->offset);
        $query->order($this->orderClauses);

        if(count($relations) > 0 && count($this->includeClauses) > 0){
            foreach ($relations as $field=>$relation) {
                $relObjectType = $relation['itemType'];
                if(strpos($relObjectType, '\\') === false){
                    $refClass = new \ReflectionClass($map->getObject());
                    $relObjectType = $refClass->getNamespaceName() . "\\" . $relObjectType;
                }

                if(!class_exists($relObjectType)){
                    throw new \Exception("Can't find relating type " . $relObjectType);
                }
                if(in_array($field, $this->includeClauses)){
                    $mapper = $this->getMapper();
                    $joinMap = $mapper->getMap($relObjectType);

                    $query->join($joinMap->getTableName(), $relation['conditions'], $joinMap->getFields());
                }
            }
        }

        $this->sqlQuery = $query;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return IDataObject
     */
    public function current()
    {
        return $this->objects[$this->objectsPointer];
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        $this->objectsPointer++;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        return $this->objectsPointer;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid()
    {
        return $this->objectsPointer < sizeof($this->objects);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        $this->objectsPointer = 0;
    }
}
