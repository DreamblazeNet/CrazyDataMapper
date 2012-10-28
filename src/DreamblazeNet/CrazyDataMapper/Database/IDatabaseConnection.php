<?php
namespace DreamblazeNet\CrazyDataMapper\Database;
/**
 * Created by JetBrains PhpStorm.
 * User: mriedmann
 * Date: 08.10.12
 * Time: 21:07
 * To change this template use File | Settings | File Templates.
 */
interface IDatabaseConnection
{
    /**
     * @param $query String
     * @return IDatabaseStatement
     */
    public function prepare($query);
}
