<?php
namespace DreamblazeNet\CrazyDataMapper\Tests;
use \DreamblazeNet\CrazyDataMapper\ObjectMapper;
use \DreamblazeNet\CrazyDataMapper\Tests\Objects\Account;
use \DreamblazeNet\CrazyDataMapper\Tests\Objects\Character;
use \DreamblazeNet\CrazyDataMapper\Tests\Maps\AccountMap;
use \DreamblazeNet\CrazyDataMapper\Tests\Maps\CharacterMap;
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
    private $connection;

    protected function setUp()
    {
        $connection = new \DreamblazeNet\CrazyDataMapper\Database\PdoDatabaseConnection(null);
        $connection->setConnection($this->getConnection()->getConnection());
        $this->connection = $connection;
        parent::setUp();
    }

    public function testRelations(){
        $objectCollection = $this->generateObjectCollection();
        $this->mapper->registerMap(new AccountMap(), $this->connection);
        $this->mapper->registerMap(new CharacterMap(), $this->connection);

        $objectCollection->includes(array('characters'));

        $objects = $objectCollection->all();

        $this->assertCount(2, $objects);
        $this->assertInstanceOf('DreamblazeNet\CrazyDataMapper\Tests\Objects\Account', reset($objects));
        $this->assertEquals('joe', $objects[0]->name);
        $this->assertInstanceOf('DreamblazeNet\CrazyDataMapper\DataObjectCollection',$objects[0]->characters);
        $chars = $objects[0]->characters;
        $this->assertCount(2,$chars);
    }

    private function generateObjectCollection(){
        $this->mapper = new ObjectMapper();
        $dataObject = new Account();
        $this->mapper->registerMap(new AccountMap(), $this->connection);
        return $this->mapper->find($dataObject);
    }
}
