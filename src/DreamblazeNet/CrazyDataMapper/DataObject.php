<?php
namespace DreamblazeNet\CrazyDataMapper;
/**
 * Created by JetBrains PhpStorm.
 * User: mriedmann
 * Date: 12.10.12
 * Time: 17:45
 * To change this template use File | Settings | File Templates.
 */
class DataObject implements IDataObject
{
    /**
     * @var \DreamblazeNet\CrazyDataMapper\ObjectMapper
     */
    private $mapper;
    private $map = null;
    private $connection = null;

    public function setMapper(ObjectMapper $mapper){
        $this->mapper = $mapper;
    }

    public function delete()
    {

    }

    public function update()
    {

    }

    public function create()
    {

    }

    private function buildDeleteQuery(){
        $map = $this->getMap();
        $fields = $map->getFields();
        $save = false;
        $query = new \DreamblazeNet\GenSql\Delete($map->getTableName(), $fields);

        foreach($fields as $objField=>$details){
            if($details['type'] == 'primary_key'){
                $query->where(array($details['name'] => $this->$objField));
                $save = true;
            }
        }
        if(!$save)
            throw new \Exception("Can't delete DataObject without primary-key");
        else
            return $query;
    }
//TODO: Unfinshed
    private function buildUpdateQuery(){
        $map = $this->getMap();
        $fields = $map->getFields();

        $query = new \DreamblazeNet\GenSql\Update($map->getTableName(), $fields);

        foreach($fields as $objField=>$details){
            if($details['type'] == 'primary_key'){
                $query->where(array($details['name'] => $this->$objField));
                $save = true;
            }
        }

        return $query;
    }
//TODO: Unfinished
    private function buildInsertQuery(){
        $map = $this->getMap();
        $fields = $map->getFields();

        $query = new \DreamblazeNet\GenSql\Insert($map->getTableName(), $fields);

        foreach($fields as $objField=>$details){
            if($details['type'] == 'primary_key'){
                $query->where(array($details['name'] => $this->$objField));
                $save = true;
            }
        }

        return $query;
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
}
