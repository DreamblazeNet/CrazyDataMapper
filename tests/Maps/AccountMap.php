<?php
namespace DreamblazeNet\CrazyDataMapper\Tests\Maps;
use DreamblazeNet\CrazyDataMapper\IDataMap;
use \DreamblazeNet\CrazyDataMapper\Tests\Objects\Account;
/**
 * Created by JetBrains PhpStorm.
 * User: mriedmann
 * Date: 12.10.12
 * Time: 15:53
 * To change this template use File | Settings | File Templates.
 */
class AccountMap implements IDataMap
{
    public function getTableName()
    {
        return "accounts";
    }

    public function getObject(){
        return new Account();
    }

    public function getFields()
    {
        return array(
            'id' => array('name' => 'id', 'type' => 'primary_key'),
            'name' => array('name' => 'name', 'type' => 'string'),
            'password' => array('name' => 'password', 'type' => 'string'),
            'lastLogin' => array('name' => 'last_login', 'type' => 'date', 'default' => '#NOW'),

            'characters' => array(
                'type' => 'relation',
                'itemType' => 'Character',
                'conditions' => array('id' => 'accountId')),
        );
    }

}
