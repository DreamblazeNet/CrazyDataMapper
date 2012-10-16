<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mriedmann
 * Date: 16.10.12
 * Time: 23:36
 * To change this template use File | Settings | File Templates.
 */

require(__DIR__ . '/../vendor/autoload.php');

const DS = DIRECTORY_SEPARATOR;
const DB_FILE_NAME = 'db$.sqlite';

spl_autoload_register(function($className){
    if(strpos($className, 'DreamblazeNet\\CrazyDataMapper\\Tests') !== false){
        $segs = explode('\\', $className);

        $class = array_pop($segs);
        $lastNamespace = array_pop($segs);

        if($lastNamespace == 'Tests')
            $path = __DIR__ . '/../';
        else
            $path = __DIR__ . '/../Tests/';

        $path .= $lastNamespace . '/' . $class . '.php';
        if(file_exists($path))
            require_once $path;
    }
});

function prepareDb($id = 1){
    $dbName = str_replace('$', $id, DB_FILE_NAME);
    $schemaQuery = file_get_contents(__DIR__ . '/testschema.sql');
    $fileDir = __DIR__ . '/' . $dbName;
    if(!file_exists(__DIR__ . '/' . $dbName)){
        touch($fileDir);
        $pdo = new PDO('sqlite:' . $dbName);
        if($pdo->exec($schemaQuery) === false) throw new Exception($pdo->errorInfo());
    }
}

prepareDb();


