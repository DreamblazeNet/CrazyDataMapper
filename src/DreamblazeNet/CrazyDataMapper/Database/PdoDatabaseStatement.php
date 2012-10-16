<?php
namespace DreamblazeNet\CrazyDataMapper\Database;
/**
 * Created by JetBrains PhpStorm.
 * User: mriedmann
 * Date: 08.10.12
 * Time: 21:20
 * To change this template use File | Settings | File Templates.
 */
class PdoDatabaseStatement implements IDatabaseStatement
{
    private $pdoStatement;

    public function execute($values=array())
    {
        $this->pdoStatement->execute($values);
    }

    public function fetch($options=0)
    {
        return $this->pdoStatement->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function affectedRowsCount()
    {
        return $this->pdoStatement->rowCount();
    }

    function __construct(\PDOStatement $pdoStmt)
    {
        $this->pdoStatement = $pdoStmt;
    }
}
