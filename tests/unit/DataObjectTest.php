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
 * Date: 25.10.12
 * Time: 19:35
 * To change this template use File | Settings | File Templates.
 */
class DataObjectTest extends \PHPUnit_Framework_TestCase
{
    public function testToArray(){
        $dummy = new Dummy();
        $dummy->oid = 1;
        $dummy->oname = "test";

        $minion = new \DreamblazeNet\CrazyDataMapper\Tests\Objects\Minion();
        $minion->id = 1;
        $minion->name = "minimi";

        $minions = new \DreamblazeNet\CrazyDataMapper\DataObjectCollection(new \DreamblazeNet\CrazyDataMapper\Tests\Objects\Minion());
        $minions->add($minion);
        $dummy->minions = $minions;

        $this->assertEquals(
            array(
                'oid' => 1,
                'oname' => 'test',
                'minions' => array(
                    0 => array(
                        'id' => 1,
                        'name' => 'minimi'
                    )
                )
            ), $dummy->toArray()
        );
    }
}
