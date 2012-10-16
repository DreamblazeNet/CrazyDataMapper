<?php
namespace DreamblazeNet\CrazyDataMapper\Database;
/**
 * Created by JetBrains PhpStorm.
 * User: mriedmann
 * Date: 08.10.12
 * Time: 22:30
 * To change this template use File | Settings | File Templates.
 */
class DummyDatabaseStatement implements IDatabaseStatement
{
    public static $testResult = array();

    public $query;
    public $values;

    public function execute($values = array())
    {
        $this->values = $values;
    }

    public function fetch($options = array())
    {
        return self::$testResult;
    }

    public function affectedRowsCount()
    {
        return count(self::$testResult);
    }

    function __construct($query)
    {
        $this->query = $query;
    }
}
