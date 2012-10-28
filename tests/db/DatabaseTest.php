<?php
namespace DreamblazeNet\CrazyDataMapper\Tests\Db;
use \DreamblazeNet\CrazyDataMapper\ObjectMapper;
use \DreamblazeNet\CrazyDataMapper\Tests\Objects\Account;
use \DreamblazeNet\CrazyDataMapper\Tests\Objects\Character;
use \DreamblazeNet\CrazyDataMapper\Tests\Objects\GuildMembership;
use \DreamblazeNet\CrazyDataMapper\Tests\Objects\Guild;
use \DreamblazeNet\CrazyDataMapper\Tests\Maps\AccountMap;
use \DreamblazeNet\CrazyDataMapper\Tests\Maps\CharacterMap;
use \DreamblazeNet\CrazyDataMapper\Tests\Maps\GuildMembershipMap;
use \DreamblazeNet\CrazyDataMapper\Tests\Maps\GuildMap;
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

    /**
     * @var \DreamblazeNet\CrazyDataMapper\Database\PdoDatabaseConnection
     */
    private $connection;

    protected function setUp()
    {
        $connection = new \DreamblazeNet\CrazyDataMapper\Database\PdoDatabaseConnection(null);
        $connection->setConnection($this->getConnection()->getConnection());
        $this->connection = $connection;
        $this->initMapper();
        parent::setUp();
    }

    public function testRelations(){
        $objectCollection = $this->generateObjectCollection();

        $objects = $objectCollection->all();

        $this->assertCount(2, $objects,"Accounts");
        $this->assertInstanceOf('DreamblazeNet\CrazyDataMapper\Tests\Objects\Account', reset($objects));
        $this->assertEquals('joe', $objects[0]->name);
        $this->assertInstanceOf('DreamblazeNet\CrazyDataMapper\DataObjectCollection',$objects[0]->characters);
        $chars = $objects[0]->characters;
        $this->assertCount(1,$chars, "Characters");
    }

    public function testCreate(){
        $newChar = new Character();
        $newChar->setMapper($this->mapper);
        $newChar->name = "NewChar";
        $newChar->level = 70;
        $newChar->accountId = 1;

        $this->assertTrue($newChar->save());

        $conn = $this->getConnection();
        $this->assertEquals(4, $conn->getRowCount('characters'), "Inserting failed");
    }

    public function testUpdate(){
        $chars = $this->mapper->find(new Character());

        $char = $chars->first();

        $this->assertEquals('JoesChar', $char->name);

        $char->name = $char->name . "Updated";
        $char->save();

        $chars2 = $this->mapper->find(new Character());
        $char2 = $chars2->first();
        $this->assertEquals($char->name, $char2->name);
    }

    public function testDelete(){
        $chars = $this->mapper->find(new Character());
        $char = $chars->first();

        $this->assertEquals('JoesChar', $char->name);

        $char->delete();

        $chars2 = $this->mapper->find(new Character());
        $char2 = $chars2->first();
        $this->assertNotEquals($char->name, $char2->name);
    }

    private function generateObjectCollection(){
        $this->initMapper();
        $dataObject = new Account();
        return $this->mapper->find($dataObject);
    }

    private function initMapper(){
        $this->mapper = new ObjectMapper();
        $this->mapper->registerMap(new AccountMap(), $this->connection);
        $this->mapper->registerMap(new CharacterMap(), $this->connection);
        $this->mapper->registerMap(new GuildMembershipMap(), $this->connection);
        $this->mapper->registerMap(new GuildMap(), $this->connection);
    }
}
