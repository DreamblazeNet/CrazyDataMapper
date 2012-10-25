<?php
namespace DreamblazeNet\CrazyDataMapper;
use DreamblazeNet\CrazyDataMapper\Database\IDatabaseConnection;
/**
 * Created by JetBrains PhpStorm.
 * User: mriedmann
 * Date: 12.10.12
 * Time: 14:27
 * To change this template use File | Settings | File Templates.
 */
class ObjectMapper
{
    protected  $mapRegistry = array();

    public function __construct(){

    }

    public function registerMap(IDataMap $map, IDatabaseConnection $dbConnection){
        $key = get_class($map->getObject());

        $this->checkDataObject($key);

        if(is_string($map))
            $map = new $map();

        $this->mapRegistry[$key] = array('map' => $map, 'connection' => $dbConnection);
    }

    public function find(IDataObject $object){
        $key = self::getObjectName($object);
        if(array_key_exists($key, $this->mapRegistry))
            return new DataObjectCollection($this,$object);
        else
            throw new \Exception("Can't find Map for " . get_class($object));
    }

    protected function getObjectName($object){
        if(is_object($object) || is_string($object)){
            try{
                $reflector = new \ReflectionClass($object);
                $objectName = $reflector->getName();
            } catch(\ReflectionException $e){
                throw new \Exception("Can't resolve class " . var_export($object, true));
            }
        } else {
            throw new \Exception("Only Objects or full-qualified-types are allowed");
        }

        return $objectName;
    }

    public function getObjectFields(IDataObject $object){
        $map = $this->getMap($object);
        return $map->getFields();
    }

    public function fetchFromDatabase(IDataObject $object, $query, Array $values){
        $connection = $this->getConnection($object);
        $stmt = $connection->prepare($query);
        $stmt->execute($values);
        return $stmt->fetch();
    }

    /**
     * @param $dataObject
     * @return IDataMap
     * @throws \Exception
     */

    public function getMap($dataObject){
        $mappingEntry = $this->getMappingEntry($dataObject);
        return $mappingEntry['map'];
    }

    /**
     * @param $dataObject
     * @return IDatabaseConnection
     * @throws \Exception
     */

    public function getConnection($dataObject){
        $mappingEntry = $this->getMappingEntry($dataObject);
        return $mappingEntry['connection'];
    }

    private function getMappingEntry($dataObject){
        $key = $this->getObjectName($dataObject);
        $this->checkDataObject($key);
        if($this->isMapRegistered($key)){
            return $this->mapRegistry[$key];
        } else {
            if(is_object($dataObject)) $dataObject = get_class($dataObject);
            throw new \Exception("Can't find Map for " . $dataObject);
        }
    }

    protected function checkDataObject($dataObject){
        $implementaions = class_implements($dataObject, true);
        if(!in_array('DreamblazeNet\\CrazyDataMapper\\IDataObject', $implementaions))
            throw new \Exception("Given DataObject '" . $dataObject . "' have to implement the IDataObject-Interface");
    }

    protected function isMapRegistered($mapKey){
        return array_key_exists($mapKey, $this->mapRegistry);
    }
}
