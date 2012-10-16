<?php
namespace DreamblazeNet\CrazyDataMapper;
/**
 * Created by JetBrains PhpStorm.
 * User: mriedmann
 * Date: 12.10.12
 * Time: 14:53
 * To change this template use File | Settings | File Templates.
 */
interface IDataMap
{
    public function getTableName();
    public function getFields();
    public function getObject();
}
