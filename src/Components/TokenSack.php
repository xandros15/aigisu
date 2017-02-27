<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-02-27
 * Time: 01:04
 */

namespace Aigisu\Components;


use Illuminate\Database\Connection;

class TokenSack
{
    /** @var  $connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getToken($name)
    {
        $stmt = "SELECT `value` FROM `settings` WHERE `name` = :name";
        return $this->connection->selectOne($stmt, ['name' => $name])->value ?? null;

    }

    public function saveToken($name, $value)
    {
        if ($this->getToken($name)) {
            $stmt = "UPDATE `settings` SET `value` = :value WHERE `name` = :name";
            $this->connection->update($stmt, [
                'name' => $name,
                'value' => $value,
            ]);
        } else {
            $stmt = "INSERT INTO `settings` (`name`, `value`) VALUES (:name, :value)";
            $this->connection->insert($stmt, [
                'name' => $name,
                'value' => $value,
            ]);
        }
    }
}
