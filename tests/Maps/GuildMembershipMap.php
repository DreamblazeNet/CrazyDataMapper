<?php
namespace DreamblazeNet\CrazyDataMapper\Tests\Maps;
use DreamblazeNet\CrazyDataMapper\IDataMap;
use \DreamblazeNet\CrazyDataMapper\Tests\Objects\GuildMembership;
/**
 * Created by JetBrains PhpStorm.
 * User: mriedmann
 * Date: 12.10.12
 * Time: 15:53
 * To change this template use File | Settings | File Templates.
 */
class GuildMembershipMap implements IDataMap
{
    public function getTableName()
    {
        return "guild_members";
    }

    public function getObject(){
        return new GuildMembership();
    }

    public function getFields()
    {
        return array(
            'guildId' => array('name' => 'guild_id', 'type' => 'primary_key'),
            'characterId' => array('name' => 'character_id', 'type' => 'primary_key'),
            'rank' => array('name' => 'rank', 'type' => 'integer'),

            'guild' => array(
                'type' => 'relation',
                'itemType' => 'Guild',
                'conditions' => array('guildId' => 'id')),

            'character' => array(
                'type' => 'relation',
                'itemType' => 'Character',
                'conditions' => array('characterId' => 'id')),
        );
    }

}