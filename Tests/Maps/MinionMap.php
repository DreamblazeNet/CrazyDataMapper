<?php
namespace DreamblazeNet\CrazyDataMapper\Tests\Maps;
use DreamblazeNet\CrazyDataMapper\IDataMap;
use DreamblazeNet\CrazyDataMapper\Tests\Objects\Minion;
/**
 * Created by JetBrains PhpStorm.
 * User: mriedmann
 * Date: 17.10.12
 * Time: 00:27
 * To change this template use File | Settings | File Templates.
 */
class MinionMap implements IDataMap
{
    public function getTableName()
    {
        return "noMinionTable";
    }

    public function getObject(){
        return new Minion();
    }

    public function getFields()
    {
        return array(
            'id' => array('name' => 'id', 'type' => 'primary_key'),
            'name' => array('name' => 'name', 'type' => 'string'),

            'dummy' => array(
                'type' => 'relation',
                'itemType' => 'Dummy',
                'conditions' => array('id' => 'id')),
        );
    }
}
