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
class Guild extends DataObject
{
    public $id;
    public $leaderId;
    public $name;
    public $money;

    public $memberships;
}
