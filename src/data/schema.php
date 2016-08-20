<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-19
 * Time: 20:48
 */
namespace Aigisu;

use Illuminate\Database\Schema\Blueprint;
use Models\Image;
use Models\Tag;
use Models\Unit;
use Models\User;

require_once '../../vendor/autoload.php';


$settings = new Configuration();
$database = new Database($settings->database);

$database->bootEloquent();

$connection = $database->getConnection();
$builder = $connection->getSchemaBuilder();
$builder->disableForeignKeyConstraints();
$createTable = function (string $table, \Closure $schema) use ($builder) {
    $builder->dropIfExists($table);
    $builder->create($table, $schema);
};

$pivot = function (Model $firstModel, Model $secondModel) : string {
    $fistTable = rtrim($firstModel->getTable(), 's');
    $secondTable = rtrim($secondModel->getTable(), 's');

    return ($fistTable <=> $secondTable) === -1 ? $fistTable . '_' . $secondTable : $secondTable . '_' . $fistTable;
};

$createTable((new User())->getTable(), function (Blueprint $table) {
    $table->collation = 'utf8mb4_unicode_ci';
    $table->charset = 'utf8mb4';
    $table->engine = 'InnoDB';

    $table->increments('id')->unsigned();
    $table->string('name', 15)->unique();
    $table->string('password_hash', 255);
    $table->string('email', 64)->unique();;
    $table->string('access_token', 255)->nullable();
    $table->string('recovery_hash', 255)->nullable();
    $table->string('remember_identifier', 255)->nullable();
    $table->string('remember_hash', 255)->nullable();
    $table->timestamps();
});

$createTable((new Unit())->getTable(), function (Blueprint $table) {
    $table->collation = 'utf8mb4_unicode_ci';
    $table->charset = 'utf8mb4';
    $table->engine = 'InnoDB';

    $table->increments('id')->unsigned();
    $table->string('name', 25);
    $table->string('original', 45)->unique();
    $table->string('icon', 100);
    $table->string('link', 100)->nullable();
    $table->string('linkgc', 100)->nullable();
    $table->enum('rarity', Unit::getRarities());
    $table->boolean('is_male');
    $table->boolean('is_only_dmm');
    $table->boolean('has_aw_image');
    $table->timestamps();
});

$createTable((new Image())->getTable(), function (Blueprint $table) {
    $table->collation = 'utf8mb4_unicode_ci';
    $table->charset = 'utf8mb4';
    $table->engine = 'InnoDB';

    $table->increments('id')->unsigned();
    $table->string('md5', 32);
    $table->integer('unit_id', false, true);
    $table->enum('server', Image::getServersNames());
    $table->tinyInteger('scene', false, true);
    $table->string('google_id', 64);
    $table->string('imgur_id', 64);
    $table->string('imgur_delhash', 64);
    $table->timestamps();

    $table->foreign('unit_id')->references('id')->on((new Unit())->getTable())->onDelete('cascade');
});

$createTable((new Tag())->getTable(), function (Blueprint $table) {
    $table->collation = 'utf8mb4_unicode_ci';
    $table->charset = 'utf8mb4';
    $table->engine = 'InnoDB';

    $table->increments('id')->unsigned();
    $table->string('name', 25);
    $table->timestamps();
});

$unitToTag = $pivot(new Unit(), new Tag());
$createTable($unitToTag, function (Blueprint $table) {
    $table->collation = 'utf8mb4_unicode_ci';
    $table->charset = 'utf8mb4';
    $table->engine = 'InnoDB';

    $table->increments('id')->unsigned();
    $table->integer('unit_id')->unsigned();
    $table->integer('tag_id')->unsigned();
    $table->foreign('unit_id')->references('id')->on((new Unit())->getTable())->onDelete('cascade');
    $table->foreign('tag_id')->references('id')->on((new Tag())->getTable())->onDelete('cascade');
});
