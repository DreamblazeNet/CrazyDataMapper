<?php
namespace DreamblazeNet\CrazyDataMapper;
/**
 * Created by JetBrains PhpStorm.
 * User: mriedmann
 * Date: 02.10.12
 * Time: 00:03
 * To change this template use File | Settings | File Templates.
 */
interface IDataObject
{
    public function setData(Array $data);
    public function setMapper(ObjectMapper $mapper);

    public function delete();
    public function save();

    public function toArray();
}
