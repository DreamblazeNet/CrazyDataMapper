<?php
namespace DreamblazeNet\CrazyDataMapper;
/**
 * Created by JetBrains PhpStorm.
 * User: mriedmann
 * Date: 09.10.12
 * Time: 18:39
 * To change this template use File | Settings | File Templates.
 */
class DataObjectCollection implements \Iterator, \Countable, ISerializeable
{
    /**
     * @var \DreamblazeNet\CrazyDataMapper\ObjectMapper
     */
    protected $mapper = null;
    protected $object = null;

    protected $sqlCache = array();

    /**
     * @var \DreamblazeNet\CrazyDataMapper\DataObject[]
     */
    protected $objects = null;
    protected $objectsPointer = 0;

    /**
     * @var \DreamblazeNet\GenSql\Select
     */
    protected $sqlQuery = null;

    public function __construct(IDataObject $object){
        $this->object = $object;
    }

    public function setMapper(ObjectMapper $mapper){
        $this->mapper = $mapper;
    }

    public function getMapper(){
        if(!is_object($this->mapper))
            throw new \Exception("Mapper not set");

        return $this->mapper;
    }

    public function filter(Array $filterClauses){
        $map = $this->getMap();
        $mapFields = $map->getFields();
        foreach ($filterClauses as $field=>$value) {
            $dbField = $mapFields[$field]['name'];
            $this->getQuery()->where(array($dbField => $value));
        }
        return $this;
    }

    public function order(Array $orderClauses){
        $order = array();
        $map = $this->getMap();
        $mapFields = $map->getFields();
        foreach ($orderClauses as $orderClause) {
            $pos = strpos($orderClause,' ');
            if($pos === false){
                $field = $orderClause;
                $direction = '';
            } else {
                $field = substr($orderClause,0,$pos);
                $direction = substr($orderClause,$pos);
            }
            $dbField = $mapFields[$field]['name'];
            $order[] = $dbField.$direction;
        }
        $this->getQuery()->order($order);
        return $this;
    }

    public function limit($maxResults, $offset = null){
        $this->getQuery()->limit($maxResults);
        $this->getQuery()->offset($offset);
        return $this;
    }

    public function reset(){
        $this->sqlQuery = null;
    }

    public function getValues(){
        return $this->getQuery()->give_sql_values();
    }

    public function getSql(){
        return $this->getQuery()->give_sql();
    }

    protected function getQuery(){
        if(!is_object($this->sqlQuery))
            $this->sqlQuery = $this->getMapper()->getSelectQuery($this->object);

        return $this->sqlQuery;
    }

    protected function getMap(){
        return $this->getMapper()->getMap($this->object);
    }

    protected function fetchData(){
        $result = $this->getMapper()->fetchFromDatabase($this->object, $this->getQuery());
        $data = array();
        foreach ($result as $row) {
            $obj = clone $this->object;
            $obj->setMapper($this->mapper);
            $obj->setData($row);

            $data[] = $obj;
        }
        $this->objects = $data;
    }

    /**
     * @return IDataObject[]
     */
    private function getObjects(){
        if(is_null($this->objects))
            $this->fetchData();

        return $this->objects;
    }

    /**
     * @param $i int
     * @return IDataObject|null
     */
    private function getObject($i){
        if(is_null($this->objects))
            $this->objects = $this->getObjects();

        if(count($this->objects) > 0 && isset($this->objects[$i]))
            return $this->objects[$i];
        else
            return null;
    }

    public function toArray(){
        $objects = $this->objects;
        $dump = array();
        foreach ($objects as $key=>$object) {
            $dump[$key] = $object->toArray();
        }
        return $dump;
    }

    /**
     * @return IDataObject[]
     */
    public function all(){
        return $this->getObjects();
    }

    /**
     * @return IDataObject|null
     */
    public function first(){
        return $this->getObject(0);
    }

    /**
     * @return IDataObject|null
     */
    public function last(){
        $lastIndex = count($this->getObjects()) - 1;
        return $this->getObject($lastIndex);
    }

    public function add(IDataObject $dataObject){
        if(!is_array($this->objects))
            $this->objects = array();

        $this->objects[] = $dataObject;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return IDataObject
     */
    public function current()
    {
        $objects = $this->getObjects();
        return $objects[$this->objectsPointer];
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

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     */
    public function count()
    {
        return count($this->getObjects());
    }
}
