<?php
namespace DreamblazeNet\CrazyDataMapper\Tests\Objects;
use DreamblazeNet\CrazyDataMapper\DataObject;
/**
 * Created by JetBrains PhpStorm.
 * User: mriedmann
 * Date: 09.10.12
 * Time: 18:51
 * To change this template use File | Settings | File Templates.
 */
class Account extends DataObject
{
    public $id;
    public $name;
    public $password;
    public $lastLogin;

    public $banEntries;
    public $characters;
}
