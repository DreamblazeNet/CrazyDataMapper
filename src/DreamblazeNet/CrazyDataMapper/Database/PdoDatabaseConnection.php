<?php
namespace DreamblazeNet\CrazyDataMapper\Database;
/**
 * Created by JetBrains PhpStorm.
 * User: mriedmann
 * Date: 08.10.12
 * Time: 21:19
 * To change this template use File | Settings | File Templates.
 */
class PdoDatabaseConnection implements IDatabaseConnection
{

    /**
     * @var \PDO
     */
    private $pdo = null;

    private $dns;
    private $user;
    private $pass;
    private $options;

    private $optionsChanged = false;

    public function __construct($dsn, $user=null, $pass=null)
    {
        $this->dns = $dsn;
        $this->user = $user;
        $this->pass = $pass;
    }

    public function setOptions($options)
    {
        $this->options = $options;
        $this->optionsChanged = true;
    }

    /**
     * @param $query String
     * @return IDatabaseStatement
     */
    public function prepare($query)
    {
        if(is_null($this->pdo) || $this->optionsChanged)
            $this->initDb();
        $stmt = $this->pdo->prepare($query);
        if(!$stmt)
            throw new \Exception(var_export($this->pdo->errorInfo(),true));

        return new PdoDatabaseStatement($stmt);
    }

    private function initDb(){
        $this->pdo = new \PDO($this->dns, $this->user, $this->pass, $this->options);

        if($this->pdo == false)
            throw new \Exception(var_export($this->pdo->errorInfo(),true));

        $this->optionsChanged = false;
    }
}
