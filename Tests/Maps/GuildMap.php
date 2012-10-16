<?php
namespace DreamblazeNet\CrazyDataMapper\Tests\Maps;
use DreamblazeNet\CrazyDataMapper\IDataMap;
use \DreamblazeNet\CrazyDataMapper\Tests\Objects\Guild;
/**
 * Created by JetBrains PhpStorm.
 * User: mriedmann
 * Date: 12.10.12
 * Time: 15:53
 * To change this template use File | Settings | File Templates.
 */
class GuildMap implements IDataMap
{
    public function getTableName()
    {
        return "guilds";
    }

    public function getObject(){
        return new Guild();
    }

    public function getFields()
    {
        return array(
            'id' => array('name' => 'id', 'type' => 'primary_key'),
            'name' => array('name' => 'name', 'type' => 'string'),
            'leaderId' => array('name' => 'leader_id', 'type' => 'integer'),
            'money' => array('name' => 'money', 'type' => 'integer'),

            'leader' => array(
                'type' => 'relation',
                'itemType' => 'Character',
                'conditions' => array('accountId' => 'id')),

            'memberships' => array(
                'type' => 'relation',
                'itemType' => 'GuildMembership',
                'conditions' => array('id' => 'guildId')),
        );
    }

}

