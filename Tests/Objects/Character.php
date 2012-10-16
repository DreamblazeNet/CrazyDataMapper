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
class Character extends DataObject
{
    public $id;
    public $accountId;
    public $name;
    public $level;
    public $money;

    public $account;
    public $guildMemberships;
}
