<?php
namespace DreamblazeNet\CrazyDataMapper\Tests\Maps;
use DreamblazeNet\CrazyDataMapper\IDataMap;
use DreamblazeNet\CrazyDataMapper\Tests\Objects\Dummy;
/**
 * Created by JetBrains PhpStorm.
 * User: mriedmann
 * Date: 17.10.12
 * Time: 00:22
 * To change this template use File | Settings | File Templates.
 */
class DummyMap implements IDataMap
{
    public function getTableName()
    {
        return "noDummyTable";
    }

    public function getObject(){
        return new Dummy();
    }

    public function getFields()
    {
        return array(
            'oid' => array('name' => 'id', 'type' => 'primary_key'),
            'oname' => array('name' => 'name', 'type' => 'string'),
            'odate' => array('name' => 'date', 'type' => 'date'),

            'minions' => array(
                'type' => 'relation',
                'itemType' => 'Minion',
                'conditions' => array('id' => 'id')),
        );
    }
}
