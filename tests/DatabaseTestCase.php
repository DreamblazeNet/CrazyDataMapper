<?php
namespace DreamblazeNet\CrazyDataMapper\Tests;
/**
 * Created by JetBrains PhpStorm.
 * User: mriedmann
 * Date: 13.10.12
 * Time: 01:25
 * To change this template use File | Settings | File Templates.
 */
abstract class DatabaseTestCase extends \PHPUnit_Extensions_Database_TestCase
{
    // only instantiate pdo once for test clean-up/fixture load
    static private $pdo = null;

    // only instantiate PHPUnit_Extensions_Database_DB_IDatabaseConnection once per test
    private $conn = null;

    /**
     * @return null|\PHPUnit_Extensions_Database_DB_DefaultDatabaseConnection|\PHPUnit_Extensions_Database_DB_IDatabaseConnection
     * @throws \Exception
     */
    final public function getConnection()
    {
        if ($this->conn === null) {
            if (self::$pdo == null) {
                //$dsn = 'sqlite:' . __DIR__ . DIRECTORY_SEPARATOR . 'db1.sqlite';
                $dsn = 'sqlite::memory:';
                //$dsn = "mysql:host=localhost;dbname=cdm_test";
                self::$pdo = new \PDO($dsn);//, 'root', 'root');
                $schemaQuery = file_get_contents(__DIR__ . '/testschema.sql');
                if(self::$pdo->exec($schemaQuery) === false) throw new \Exception(self::$pdo->errorInfo());
            }
            $this->conn = $this->createDefaultDBConnection(self::$pdo);
        }

        return $this->conn;
    }

    /**
     * @return \PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    public function getDataSet()
    {
        return $this->createXMLDataSet(dirname(__FILE__).'/seed.xml');
    }
}
