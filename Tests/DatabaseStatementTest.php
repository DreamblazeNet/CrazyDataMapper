<?php
namespace DreamblazeNet\CrazyDataMapper\Tests;
use DreamblazeNet\CrazyDataMapper\Database\DummyDatabaseStatement;
/**
 * Created by JetBrains PhpStorm.
 * User: mriedmann
 * Date: 12.10.12
 * Time: 22:07
 * To change this template use File | Settings | File Templates.
 */
class DatabaseStatementTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruction(){
        $stmt = new DummyDatabaseStatement("TESTQUERY");
        $this->assertInstanceOf("DreamblazeNet\\CrazyDataMapper\\Database\\IDatabaseStatement", $stmt);
    }

    public function testQuery(){
        $query = "TESTQUERY";
        $stmt = new DummyDatabaseStatement($query);
        $this->assertEquals($query, $stmt->query);
    }

    public function testExecution(){
        $values = array('v1' => 't1', 'v2' => 't2');

        $stmt = new DummyDatabaseStatement("TESTQUERY");
        $stmt->execute($values);
        $this->assertEquals($values, $stmt->values);
    }

    public function testFetching(){
        $testResult = array("TESTRESULT");
        DummyDatabaseStatement::$testResult = $testResult;
        $stmt = new DummyDatabaseStatement("TESTQUERY");
        $result = $stmt->fetch();
        $this->assertEquals($testResult, $result);
        DummyDatabaseStatement::$testResult = array();
    }
}
