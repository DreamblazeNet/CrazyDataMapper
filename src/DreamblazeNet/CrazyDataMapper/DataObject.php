<?php
namespace DreamblazeNet\CrazyDataMapper;
/**
 * Created by JetBrains PhpStorm.
 * User: mriedmann
 * Date: 12.10.12
 * Time: 17:45
 * To change this template use File | Settings | File Templates.
 */
class DataObject implements IDataObject, ISerializeable
{
    /**
     * @var \DreamblazeNet\CrazyDataMapper\ObjectMapper
     */
    private $mapper;
    private $map = null;
    private $connection = null;
    private $data = array();

    public function delete()
    {
        $mapper = $this->getMapper();
        $map = $this->getMap();
        $mapFields = $map->getFields();

        $pkFields = array();
        foreach($mapFields as $field=>$fieldInfos){
            if(isset($fieldInfos['type']) && $fieldInfos['type'] == 'primary_key')
                $pkFields[] = $field;
        }
        if(empty($pkFields))
            throw new \Exception("Only Dataobject with primary-keys can be deleted");

        $quary = $mapper->getDeleteQuery($this);

        foreach($pkFields as $pkField){
            $quary->where(array($pkField => $this->$pkField));
        }

        $affectedRows = $mapper->executeOnDatabase($this, $quary);
        return $affectedRows > 0;
    }

    public function save()
    {
        if(empty($this->data))
            return $this->create();
        else
            return $this->update();
    }

    public function setData(Array $data){
        $this->data = $data;
        $map = $this->getMap();
        $mapFields = $map->getFields();

        foreach($mapFields as $field=>$fieldInfo){
            if(isset($fieldInfo['name']) && isset($data[$fieldInfo['name']]))
                $this->$field = $data[$fieldInfo['name']];
        }

        $this->parseRelations();
    }

    public function setMapper(ObjectMapper $mapper){
        $this->mapper = $mapper;
    }

    private function create(){
        $mapper = $this->getMapper();
        $map = $this->getMap();
        $mapFields = $map->getFields();

        $quary = $mapper->getInsertQuery($this);
        $values = array();
        foreach ($mapFields as $field=>$fieldInfos) {
            if(isset($this->$field) && isset($fieldInfos['name']))
                $values[$fieldInfos['name']] = $this->$field;
        }

        $quary->values($values);
        $affectedRows = $mapper->executeOnDatabase($this, $quary);
        return $affectedRows > 0;
    }

    private function update(){
        $mapper = $this->getMapper();
        $map = $this->getMap();
        $mapFields = $map->getFields();

        $quary = $mapper->getUpdateQuery($this);
        $newValues = array();
        foreach ($mapFields as $field=>$fieldInfos) {
            if(
                isset($this->$field) &&
                isset($fieldInfos['name']) &&
                isset($this->data[$fieldInfos['name']]) &&
                $this->data[$fieldInfos['name']] != $this->$field
            )
                $newValues[$fieldInfos['name']] = $this->$field;
        }
        $quary->set($newValues);
        return $mapper->executeOnDatabase($this,$quary) > 0;
    }

    public function toArray(){
        $dump = array();
        $refl = new \ReflectionClass($this);
        $props = $refl->getProperties(\ReflectionProperty::IS_PUBLIC);
        foreach ($props as $prop) {
            $propName = $prop->getName();
            $propValue = $this->$propName;
            if(is_string($propValue) || is_numeric($propValue)){
                $dump[$propName] = $propValue;
            } elseif(is_object($propValue) && $propValue instanceof ISerializeable){
                $dump[$propName] = $propValue->toArray();
            }
        }
        return $dump;
    }

    private function getMapper(){
        if(!is_object($this->mapper))
            throw new \Exception("Mapper not set");

        return $this->mapper;
    }

    private function getMap(){
        if(is_null($this->map))
            $this->map = $this->mapper->getMap($this);

        return $this->map;
    }

    private function getConnection(){
        if(is_null($this->connection))
            $this->connection = $this->mapper->getConnection($this);
        return $this->connection;
    }

    private function parseRelations(){
        $map = $this->getMap();
        $mapFields = $map->getFields();

        $relations = array_filter($mapFields, function($elem){
            return isset($elem['type']) && $elem['type'] == 'relation';
        });

        if(count($relations) > 0){
            foreach ($relations as $field=>$relation) {
                $relObjectType = $relation['itemType'];
                if(strpos($relObjectType, '\\') === false){
                    $refClass = new \ReflectionClass($map->getObject());
                    $relObjectType = '\\' . $refClass->getNamespaceName() . "\\" . $relObjectType;
                }

                if(!class_exists($relObjectType)){
                    throw new \Exception("Can't find relating type " . $relObjectType);
                }

                $relCollection = new DataObjectCollection(new $relObjectType());
                $relCollection->setMapper($this->mapper);

                $conds = array();
                foreach($relation['conditions'] as $key=>$fkey){
                    if(isset($this->$key))
                        $conds[$fkey] = $this->$key;
                    else
                        throw new \Exception("Can't resolve relation ({$key} => {$fkey}) for " . get_class($this));
                }

                $relCollection->filter($conds);
                $this->$field = $relCollection;
            }
        }
    }
}
