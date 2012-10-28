<?php
namespace DreamblazeNet\CrazyDataMapper\Tests\Db;
use \DreamblazeNet\CrazyDataMapper\ObjectMapper;
use \DreamblazeNet\CrazyDataMapper\Tests\Objects\Account;
use \DreamblazeNet\CrazyDataMapper\Tests\Objects\Character;
use \DreamblazeNet\CrazyDataMapper\Tests\Maps\AccountMap;
use \DreamblazeNet\CrazyDataMapper\Tests\Maps\CharacterMap;
use \DreamblazeNet\CrazyDataMapper\Tests\Maps\GuildMembershipMap;
use \DreamblazeNet\CrazyDataMapper\Tests\Maps\GuildMap;
/**
 * Created by JetBrains PhpStorm.
 * User: mriedmann
 * Date: 25.10.12
 * Time: 01:00
 * To change this template use File | Settings | File Templates.
 */
class MultiDatabaseTest extends MultiDatabaseTestCase
{
    /**
     * @var \DreamblazeNet\CrazyDataMapper\ObjectMapper
     */
    private $mapper;

    protected function setUp()
    {
        $this->generateMapper();
        parent::setUp();
    }


    public function testMultiDBRelations(){

        $objectCollection = $this->generateObjectCollection();

        $objects = $objectCollection->all();

        $this->assertCount(2, $objects);
        $this->assertInstanceOf('DreamblazeNet\CrazyDataMapper\Tests\Objects\Account', reset($objects));
        $this->assertEquals('joe', $objects[0]->name);
        $this->assertInstanceOf('DreamblazeNet\CrazyDataMapper\DataObjectCollection',$objects[0]->characters);
        $chars = $objects[0]->characters;
        $this->assertCount(1,$chars);

    }

    private function generateMapper(){
        $connection1 = new \DreamblazeNet\CrazyDataMapper\Database\PdoDatabaseConnection(null);
        $conn = $this->getConnection();

        $connection1->setConnection($conn->getConnection());

        $connection2 = new \DreamblazeNet\CrazyDataMapper\Database\PdoDatabaseConnection(null);
        $connection2->setConnection($this->getConnection()->getConnection());

        $mapper = new ObjectMapper();
        $mapper->registerMap(new AccountMap(), $connection1);
        $mapper->registerMap(new CharacterMap(), $connection2);
        $mapper->registerMap(new GuildMembershipMap(), $connection2);
        $mapper->registerMap(new GuildMap(), $connection2);

        $this->mapper = $mapper;
    }

    private function generateObjectCollection(){
        $dataObject = new Account();
        return $this->mapper->find($dataObject);
    }
}
