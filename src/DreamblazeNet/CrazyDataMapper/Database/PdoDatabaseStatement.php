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
        $affectedRows = $this->pdoStatement->execute($values);
        $this->checkForErrors();
        return $affectedRows;
    }

    public function fetch($options=0)
    {
        $result = $this->pdoStatement->fetchAll(\PDO::FETCH_ASSOC);
        $this->checkForErrors();
        return $result;
    }

    public function affectedRowsCount()
    {
        $rowCount = $this->pdoStatement->rowCount();
        $this->checkForErrors();
        return $rowCount;
    }

    function __construct(\PDOStatement $pdoStmt)
    {
        $this->pdoStatement = $pdoStmt;
    }

    private function checkForErrors(){
        $errorCode = $this->pdoStatement->errorInfo();
        if($errorCode != '00000')
            new \Exception("DB-Error " . $errorCode . ": " . var_export($this->pdoStatement->errorInfo(), true));
    }
}
