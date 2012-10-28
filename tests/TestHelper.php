<?php
namespace DreamblazeNet\CrazyDataMapper\Tests;
/**
 * Created by JetBrains PhpStorm.
 * User: mriedmann
 * Date: 25.10.12
 * Time: 21:56
 * To change this template use File | Settings | File Templates.
 */
class TestHelper
{
    public static function getObjProp($obj, $prop){
        $refl = new \ReflectionClass($obj);
        $prop = $refl->getProperty($prop);
        $prop->setAccessible(true);
        return $prop->getValue($obj);
    }
}
