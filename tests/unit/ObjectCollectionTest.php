<?php
namespace DreamblazeNet\CrazyDataMapper\Tests;
use \DreamblazeNet\CrazyDataMapper\DataObjectCollection;
use \DreamblazeNet\CrazyDataMapper\ObjectMapper;
use \DreamblazeNet\CrazyDataMapper\Database\DummyDatabaseConnection;
use \DreamblazeNet\CrazyDataMapper\Database\DummyDatabaseStatement;
use \DreamblazeNet\CrazyDataMapper\Tests\Objects\Dummy;
use \DreamblazeNet\CrazyDataMapper\Tests\Objects\Minion;
use \DreamblazeNet\CrazyDataMapper\Tests\Maps\DummyMap;
use \DreamblazeNet\CrazyDataMapper\Tests\Maps\MinionMap;

/**
 * Created by JetBrains PhpStorm.
 * User: mriedmann
 * Date: 12.10.12
 * Time: 18:21
 * To change this template use File | Settings | File Templates.
 */
class ObjectCollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \DreamblazeNet\CrazyDataMapper\ObjectMapper
     */
    private $mapper;

    /**
     * @var \DreamblazeNet\CrazyDataMapper\Database\IDatabaseConnection
     */
    private $connection;

    public function testConstruction(){
        $objectCollection = $this->generateObjectCollection();
        $this->assertEquals($this->mapper, $objectCollection->getMapper());

        return $objectCollection;
    }

    public function testFilters(){
        $objectCollection = $this->generateObjectCollection();

        $objectCollection->filter(array('oid' => 1, 'oname' => 'test'));

        $expectedSql = 'SELECT noDummyTable.id, noDummyTable.name, noDummyTable.date FROM noDummyTable WHERE noDummyTable.id = :id AND noDummyTable.name = :name';
        $expectedValues = Array (
            ':id' => 1,
            ':name' => 'test'
        );

        $this->checkSql($objectCollection, $expectedSql, $expectedValues);
        return $objectCollection;
    }

    public function testLimit(){
        $objectCollection = $this->generateObjectCollection();

        $objectCollection->limit(1);

        $expectedSql = 'SELECT noDummyTable.id, noDummyTable.name, noDummyTable.date FROM noDummyTable LIMIT 0,1';
        $expectedValues = Array();

        $this->checkSql($objectCollection, $expectedSql, $expectedValues);
    }

    public function testOrder(){
        $objectCollection = $this->generateObjectCollection();

        $objectCollection->order(array('oname'));

        $expectedSql = 'SELECT noDummyTable.id, noDummyTable.name, noDummyTable.date FROM noDummyTable ORDER BY name';
        $expectedValues = Array();

        $this->checkSql($objectCollection, $expectedSql, $expectedValues);
    }

    public function testOrderWithDirection(){
        $objectCollection = $this->generateObjectCollection();

        $objectCollection->order(array('oname DESC'));

        $expectedSql = 'SELECT noDummyTable.id, noDummyTable.name, noDummyTable.date FROM noDummyTable ORDER BY name DESC';
        $expectedValues = Array();

        $this->checkSql($objectCollection, $expectedSql, $expectedValues);
    }

    /**
     * @depends testFilters
     */
    public function testReset(DataObjectCollection $objectCollection){
        $objectCollection->reset();
        $objectCollection->limit(2);

        $expectedSql = 'SELECT noDummyTable.id, noDummyTable.name, noDummyTable.date FROM noDummyTable LIMIT 0,2';
        $expectedValues = Array();

        $this->checkSql($objectCollection, $expectedSql, $expectedValues);
    }

    public function testFetching(){
        $objectCollection = $this->generateObjectCollection();

        $testObjects = $this->fillWithDummies(1);
        $testObject = $testObjects[0];

        $objects = $objectCollection->all();

        $this->assertCount(1, $objects);
        $this->assertInstanceOf('DreamblazeNet\\CrazyDataMapper\\IDataObject', $objects[0]);
        $this->assertAttributeEquals('1', 'oid', $objects[0]);
        $this->assertAttributeEquals($testObject->oname, 'oname', $objects[0]);
    }

    public function testIteration(){
        $objectCollection = $this->generateObjectCollection();

        $testObjects = $this->fillWithDummies(4);

        $this->assertCount(4, $objectCollection);
        foreach($objectCollection as $key=>$object){
            $this->assertTrue($key >= 0 && $key < 4);
            $this->assertEquals($object->oname, $testObjects[$key]->oname);
        }
    }

    public function testFirst(){
        $objectCollection = $this->generateObjectCollection();
        $testObjects = $this->fillWithDummies(3);

        $this->assertEquals($testObjects[0]->oname, $objectCollection->first()->oname);

    }

    public function testLast(){
        $objectCollection = $this->generateObjectCollection();
        $testObjects = $this->fillWithDummies(3);

        $this->assertEquals($testObjects[2]->oname, $objectCollection->last()->oname);
    }

    private function fillWithDummies($amount){
        $testObjects = array();
        DummyDatabaseStatement::$testResult = array();

        for($i=0;$i<$amount;$i++){
            $testDummy = new Dummy();
            $testDummy->oid = $i + 1;
            $testDummy->oname = "TestName" . $i;
            $testDummy->minions = new DataObjectCollection(new Minion());
            $testObjects[] = $testDummy;
            DummyDatabaseStatement::$testResult[] = array(
                'id' => $testDummy->oid,
                'name' => $testDummy->oname,
            );
        }

        return $testObjects;
    }

    private function checkSql(DataObjectCollection $objectCollection, $expectedSql, $expectedValues){
        $this->assertEquals($expectedSql, $objectCollection->getSql());
        $this->assertEquals($expectedValues , $objectCollection->getValues());
    }

    private function generateObjectCollection(){
        $this->connection = new DummyDatabaseConnection('blub', 'testUser', 'testPass');
        $this->mapper = new ObjectMapper();
        $dataObject = new Dummy();
        $this->mapper->registerMap(new DummyMap(), $this->connection);
        $this->mapper->registerMap(new MinionMap(), $this->connection);
        return $this->mapper->find($dataObject);
    }
}
