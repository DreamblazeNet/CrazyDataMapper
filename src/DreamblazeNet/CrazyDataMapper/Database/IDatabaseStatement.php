<?php
namespace DreamblazeNet\CrazyDataMapper\Database;
/**
 * Created by JetBrains PhpStorm.
 * User: mriedmann
 * Date: 08.10.12
 * Time: 21:14
 * To change this template use File | Settings | File Templates.
 */
interface IDatabaseStatement
{
    public function execute($values=array());
    public function fetch($options=array());
    public function affectedRowsCount();
}
