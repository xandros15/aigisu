<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-19
 * Time: 20:48
 */
namespace Aigisu;

use Illuminate\Database\Schema\Blueprint;
use Models\User;

require_once '../../vendor/autoload.php';


$settings = new Configuration();
$database = new Database($settings->database);

$database->bootEloquent();

$connection = $database->getConnection();
$builder = $connection->getSchemaBuilder();
$createTable = function (string $table, \Closure $schema) use ($builder) {
    $builder->dropIfExists($table);
    $builder->create($table, $schema);
};

$createTable((new User())->getTable(), function (Blueprint $table) {
    $table->collation = 'utf8mb4_unicode_ci';
    $table->charset = 'utf8mb4';
    $table->engine = 'InnoDB';

    $table->increments('id')->unsigned();
    $table->string('name', 15);
    $table->string('password', 255);
    $table->string('email', 64);
    $table->string('access_token', 255)->nullable();
    $table->string('recovery_hash', 255)->nullable();
    $table->string('remember_identifier', 255)->nullable();
    $table->string('remember_hash', 255)->nullable();
    $table->timestamps();
});
