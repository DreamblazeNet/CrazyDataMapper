<?php
namespace DreamblazeNet\CrazyDataMapper\Tests;
use \DreamblazeNet\CrazyDataMapper\Database\DummyDatabaseStatement;
use \DreamblazeNet\CrazyDataMapper\Database\PdoDatabaseConnection;
use \DreamblazeNet\CrazyDataMapper\ObjectMapper;
use \DreamblazeNet\CrazyDataMapper\Tests\Objects\Dummy;
use \DreamblazeNet\CrazyDataMapper\Tests\Maps\DummyMap;
use \DreamblazeNet\CrazyDataMapper\Tests\Maps\AccountMap;
use \DreamblazeNet\CrazyDataMapper\Tests\Maps\CharacterMap;
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

    private $connection;

    protected function setUp()
    {
        $this->connection = new PdoDatabaseConnection('blub', 'testUser', 'testPass');

        parent::setUp();
    }


    public function testConstruction(){
        $mapper = $this->generateMapper();
        $mapper->registerMap(new DummyMap(), $this->connection);
        $mapRegistry = $this->getObjProp($mapper, 'mapRegistry');
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

        $mapRegistry = $this->getObjProp($mapper, 'mapRegistry');
        $this->assertTrue(array_key_exists(get_class($object), $mapRegistry));

        $map = $mapper->getMap($object);
        $mapType = __NAMESPACE__ . "\\Maps\\" . $this->DATAMAP_NAME;
        $this->assertInstanceOf($mapType ,$map);
    }

    /**
     * @depends testConstruction
     */
    public function testRegisterMap(ObjectMapper $mapper){
        $mapper->registerMap(new AccountMap(), $this->connection);
        $mapper->registerMap(new CharacterMap(), $this->connection);
        $mapRegistry = $this->getObjProp($mapper, 'mapRegistry');
        $this->assertArrayHasKey('DreamblazeNet\\CrazyDataMapper\\Tests\\Objects\\Account', $mapRegistry);
        $this->assertArrayHasKey('DreamblazeNet\\CrazyDataMapper\\Tests\\Objects\\Character', $mapRegistry);
    }

    private function generateMapper(){
        $mapper = new ObjectMapper();
        return $mapper;
    }

    private function getObjProp($obj, $prop){
        $refl = new \ReflectionClass($obj);
        $prop = $refl->getProperty($prop);
        $prop->setAccessible(true);
        return $prop->getValue($obj);
    }
}
