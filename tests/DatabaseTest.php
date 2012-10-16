<?php
namespace DreamblazeNet\CrazyDataMapper\Tests;
use \DreamblazeNet\CrazyDataMapper\ObjectMapper;
use \DreamblazeNet\CrazyDataMapper\Tests\Objects\Dummy;
use \DreamblazeNet\CrazyDataMapper\Tests\Objects\Minion;
use \DreamblazeNet\CrazyDataMapper\Tests\Maps\DummyMap;
use \DreamblazeNet\CrazyDataMapper\Tests\Maps\MinionMap;
/**
 * Created by JetBrains PhpStorm.
 * User: mriedmann
 * Date: 13.10.12
 * Time: 01:34
 * To change this template use File | Settings | File Templates.
 */
class DatabaseTest extends DatabaseTestCase
{
    /**
     * @var \DreamblazeNet\CrazyDataMapper\ObjectMapper
     */
    private $mapper;

    public function testRelations(){
/*
        $objectCollection = $this->generateObjectCollection();
        $this->mapper->registerMap(new MinionMap());

        $objectCollection->includes(array('subitems'));

        $objects = $objectCollection->getObjects();
        $expected = array();
        $expected[] = new Dummy();

        $this->assertCount(2, $objects);
        $this->assertInstanceOf('DreamblazeNet\CrazyDataMapper\Tests\DummyDataObject', reset($objects));
        $this->assertEquals('joe', $objects[0]->name);
        $this->assertCount(2,$objects[0]->subobjects);
*/
    }

    private function generateObjectCollection(){
        $dsn = 'sqlite:' . __DIR__ . DIRECTORY_SEPARATOR . 'test.sqlite3';
        $connection = new \DreamblazeNet\CrazyDataMapper\Database\PdoDatabaseConnection($dsn);
        $this->mapper = new ObjectMapper($connection);
        $dataObject = new Dummy();
        $this->mapper->registerMap(new DummyMap());
        return $this->mapper->find($dataObject);
    }
}
