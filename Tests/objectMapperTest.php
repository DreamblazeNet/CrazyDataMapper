<?php
namespace DreamblazeNet\CrazyDataMapper\Tests;
use \DreamblazeNet\CrazyDataMapper\Database\DummyDatabaseStatement;
use \DreamblazeNet\CrazyDataMapper\Database\DummyDatabaseConnection;
use \DreamblazeNet\CrazyDataMapper\ObjectMapper;
use \DreamblazeNet\CrazyDataMapper\Tests\Objects\Dummy;
use \DreamblazeNet\CrazyDataMapper\Tests\Maps\DummyMap;
/**
 * Created by JetBrains PhpStorm.
 * User: mriedmann
 * Date: 12.10.12
 * Time: 15:48
 * To change this template use File | Settings | File Templates.
 */
class ObjectMapperTest extends \PHPUnit_Framework_TestCase
{
    private $DATAOBJECT_NAME = "Dummy";
    private $DATAMAPPER_NAME = "ObjectMapper";
    private $DATAMAP_NAME = "DummyMap";


    public function testConstruction(){
        $connection = new DummyDatabaseConnection('blub', 'testUser', 'testPass');
        $mapper = new ObjectMapper($connection);

        $mapper->registerMap(new DummyMap());
        $mapRegistry = $this->getMapRegistry($mapper);
        $this->assertTrue(array_key_exists(__NAMESPACE__ . "\\Objects\\" . $this->DATAOBJECT_NAME, $mapRegistry));
        return $mapper;
    }

    /**
     * @depends testConstruction
     */
    public function testFind(ObjectMapper $mapper){
        $dataObjectCollection = $mapper->find(new Dummy());
        $this->assertInstanceOf("DreamblazeNet\\CrazyDataMapper\\DataObjectCollection", $dataObjectCollection);
        $this->assertEquals($mapper, $dataObjectCollection->getMapper());
    }

    /**
     * @depends testConstruction
     */
    public function testGetMap(ObjectMapper $mapper){
        $object = new Dummy();

        $mapRegistry = $this->getMapRegistry($mapper);
        $this->assertTrue(array_key_exists(get_class($object), $mapRegistry));

        $map = $mapper->getMap($object);
        $mapType = __NAMESPACE__ . "\\Maps\\" . $this->DATAMAP_NAME;
        $this->assertInstanceOf($mapType ,$map);
    }

    private function getMapRegistry(ObjectMapper $mapper){
        $refl = new \ReflectionClass($mapper);
        $prop = $refl->getProperty('mapRegistry');
        $prop->setAccessible(true);
        return $prop->getValue($mapper);
    }
}
