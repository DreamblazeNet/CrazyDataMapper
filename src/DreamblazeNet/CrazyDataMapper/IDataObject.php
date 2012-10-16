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
    public function setMapper(ObjectMapper $mapper);

    public function reload();
    public function delete();
    public function update();
    public function create();
}
