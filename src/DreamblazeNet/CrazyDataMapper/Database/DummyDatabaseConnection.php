<?php
namespace DreamblazeNet\CrazyDataMapper\Database;
/**
 * Created by JetBrains PhpStorm.
 * User: mriedmann
 * Date: 08.10.12
 * Time: 22:29
 * To change this template use File | Settings | File Templates.
 */
class DummyDatabaseConnection implements IDatabaseConnection
{
    public $dsn;
    public $user;
    public $pass;
    public $options = array();

    public function __construct($dsn, $user=null, $pass=null)
    {
        $this->dsn = $dsn;
        $this->user = $user;
        $this->pass = $pass;
    }

    public function setOptions($options)
    {
        $this->options = $options;
    }

    /**
     * @param $query String
     * @return \DreamblazeNet\CrazyDataMapper\Database\IDatabaseStatement
     */
    public function prepare($query)
    {
        return new DummyDatabaseStatement($query);
    }
}
