<?php
namespace DreamblazeNet\CrazyDataMapper\Tests\Objects;
use DreamblazeNet\CrazyDataMapper\DataObject;
/**
 * Created by JetBrains PhpStorm.
 * User: mriedmann
 * Date: 16.10.12
 * Time: 23:01
 * To change this template use File | Settings | File Templates.
 */
class GuildMembership extends DataObject
{
    public $guildId;
    public $characterId;
    public $rank;

    public $character;
    public $guild;
}
