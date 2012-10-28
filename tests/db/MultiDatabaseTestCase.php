<?php
namespace DreamblazeNet\CrazyDataMapper\Tests\Db;
/**
 * Created by JetBrains PhpStorm.
 * User: mriedmann
 * Date: 13.10.12
 * Time: 01:25
 * To change this template use File | Settings | File Templates.
 */
abstract class MultiDatabaseTestCase extends \PHPUnit_Extensions_Database_TestCase
{
    // only instantiate pdo once for test clean-up/fixture load
    /**
     * @var \PDO[]
     */
    static private $conns = array();

    static private $connPointer = 1;
    static private $maxConns = 2;
    // only instantiate PHPUnit_Extensions_Database_DB_IDatabaseConnection once per test
    private $conn = null;

    protected function setUp()
    {
        parent::setUp();
        $this->seedDatabases();
    }

    /**
     * @return null|\PHPUnit_Extensions_Database_DB_DefaultDatabaseConnection|\PHPUnit_Extensions_Database_DB_IDatabaseConnection
     * @throws \Exception
     */
    final public function getConnection()
    {
        if(count(self::$conns)<self::$maxConns)
            $this->createConnections();

        return self::$conns[self::$connPointer - 1];
    }

    public function getDataSet(){
        return $this->createXMLDataSet(dirname(__FILE__).'/multi_seed0.xml');
    }

    private function seedDatabases(){
        foreach(self::$conns as $i=>$conn){
            $setUpOperation    = \PHPUnit_Extensions_Database_Operation_Factory::CLEAN_INSERT();
            $setUpOperation->execute($conn, $this->createXMLDataSet(dirname(__FILE__).'/multi_seed' . $i . '.xml'));
        }
    }

    private function createConnections(){
        for($i=1;$i<self::$maxConns;$i++){
            $dsn = 'sqlite:' . __DIR__ . DIRECTORY_SEPARATOR . 'test' . self::$connPointer . '.sqlite';
            $pdo = new \PDO($dsn);
            $schemaQuery = file_get_contents(__DIR__ . '/testschema.sql');
            if($pdo->exec($schemaQuery) === false) throw new \Exception(var_export($pdo->errorInfo(),true));

            $conn = $this->createDefaultDBConnection($pdo);

            self::$conns[self::$connPointer] = $conn;
            self::$connPointer++;
        }
    }

    protected function tearDown()
    {
        for($i=1;$i<self::$connPointer;$i++){
            unlink(__DIR__ . DIRECTORY_SEPARATOR . 'test' . $i . '.sqlite');
        }
        self::$connPointer = 0;
        parent::tearDown();
    }
}
