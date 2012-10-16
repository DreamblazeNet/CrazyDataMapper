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
    private $mapper;

    public function setMapper(ObjectMapper $mapper){
        $this->mapper = $mapper;
    }

    public function reload()
    {

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
}
