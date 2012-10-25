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

        $testObject = new Dummy();
        $testObject->id = 1;
        $testObject->name = "TestName";
        $testResult = array($testObject);
        DummyDatabaseStatement::$testResult = $testResult;

        $objects = $objectCollection->all();

        $this->assertCount(1, $objects);
        $this->assertInstanceOf('DreamblazeNet\\CrazyDataMapper\\IDataObject', $objects[0]);
        $this->assertAttributeEquals('1', 'id', $objects[0]);
        $this->assertAttributeEquals('TestName', 'name', $objects[0]);
    }

    public function testIteration(){
        $objectCollection = $this->generateObjectCollection();

        $testObjects = $this->fillWithDummies(4);

        foreach($objectCollection as $key=>$object){
            $this->assertTrue($key > 0 && $key < 4);
            $this->assertContains($object, $testObjects);
        }
        $this->assertCount(4, $objectCollection);
    }

    public function testFirst(){
        $objectCollection = $this->generateObjectCollection();
        $testObjects = $this->fillWithDummies(3);

        $this->assertEquals($testObjects[0]->name, $objectCollection->first()->name);
    }

    public function testLast(){
        $objectCollection = $this->generateObjectCollection();
        $testObjects = $this->fillWithDummies(3);

        $this->assertEquals($testObjects[2]->name, $objectCollection->last()->name);
    }

    private function fillWithDummies($amount){
        $testObjects = array();
        for($i=0;$i<$amount;$i++){
            $testObject = new Dummy();
            $testObject->id = $i + 1;
            $testObject->name = "TestName" . $i;
            $testObjects[] = $testObject;
        }
        DummyDatabaseStatement::$testResult = $testObjects;
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
        return $this->mapper->find($dataObject);
    }
}