<?php
namespace DreamblazeNet\CrazyDataMapper\Tests\Maps;
use DreamblazeNet\CrazyDataMapper\IDataMap;
use \DreamblazeNet\CrazyDataMapper\Tests\Objects\Character;
/**
 * Created by JetBrains PhpStorm.
 * User: mriedmann
 * Date: 12.10.12
 * Time: 15:53
 * To change this template use File | Settings | File Templates.
 */
class CharacterMap implements IDataMap
{
    public function getTableName()
    {
        return "characters";
    }

    public function getObject(){
        return new Character();
    }

    public function getFields()
    {
        return array(
            'id' => array('name' => 'id', 'type' => 'primary_key'),
            'name' => array('name' => 'name', 'type' => 'string'),
            'accountId' => array('name' => 'account_id', 'type' => 'integer'),
            'level' => array('name' => 'level', 'type' => 'integer'),
            'money' => array('name' => 'money', 'type' => 'integer'),

            'account' => array(
                'type' => 'relation',
                'itemType' => 'Account',
                'conditions' => array('accountId' => 'id')),

            'guildMemberships' => array(
                'type' => 'relation',
                'itemType' => 'GuildMembership',
                'conditions' => array('id' => 'characterId')),
        );
    }

}
