<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-19
 * Time: 20:48
 */
namespace Aigisu\Api\Data;

use Aigisu\Api\Data\Tables\CG;
use Aigisu\Api\Data\Tables\OauthAccessTokens;
use Aigisu\Api\Data\Tables\OauthRefreshTokens;
use Aigisu\Api\Data\Tables\Table;
use Aigisu\Api\Data\Tables\Tags;
use Aigisu\Api\Data\Tables\TagsUnits;
use Aigisu\Api\Data\Tables\Units;
use Aigisu\Api\Data\Tables\Users;
use Aigisu\Core\Configuration;
use Aigisu\Core\Model;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;


class Schema
{
    /** @var Builder */
    protected $builder;

    /**
     * Schema constructor.
     */
    public function __construct()
    {
        $settings = new Configuration();
        /** @var $connection Connection */
        $connection = $settings->get(Connection::class);
        $this->builder = $connection->getSchemaBuilder();
        $this->builder->disableForeignKeyConstraints();
    }

    /**
     * @param Model $firstModel
     * @param Model $secondModel
     * @return string
     */
    public static function pivot(Model $firstModel, Model $secondModel) : string
    {
        $fistTable = rtrim($firstModel->getTable(), 's');
        $secondTable = rtrim($secondModel->getTable(), 's');

        return ($fistTable <=> $secondTable) === -1 ? $fistTable . '_' . $secondTable : $secondTable . '_' . $fistTable;
    }

    /**
     * Run schema builder
     */
    public function run()
    {
        /** @var $table Table */
        foreach ($this->tables() as $table) {
            if ($this->builder->hasTable($table->getTableName())) {
                $this->backupTableData($table);
                $this->builder->dropIfExists($table->getTableName());
            };

            $schema = $this->makeClosure($table);
            try {
                $this->builder->create($table->getTableName(), $schema);
            } catch (\Exception $e) {
                dump($e->getTrace());
            }
        }
    }

    /**
     * @return array
     */
    private function tables() : array
    {
        return [
            new Units(),
            new Users(),
            new CG(),
            new Tags(),
            new TagsUnits(),
            new OauthAccessTokens(),
            new OauthRefreshTokens(),
        ];
    }

    /**
     * @param Table $table
     */
    public function backupTableData(Table $table)
    {
        $tableName = $table->getTableName();
        /** @var $collection Collection */
        $collection = $this->builder->getConnection()->table($tableName)->get();
        if (!$collection->isEmpty()) {
            $filename = __DIR__ . '/../../../backup/' . $tableName . date('_Y-m-d-hs') . '.json';
            file_put_contents($filename, $collection->toJson(JSON_PRETTY_PRINT));
        }
    }

    /**
     * @param Table $table
     * @return \Closure
     */
    private function makeClosure(Table $table)
    {
        return function (Blueprint $blueprint) use ($table) {
            $table->onCreate($blueprint);
        };
    }

    public function backupTables()
    {
        foreach ($this->tables() as $table) {
            $this->backupTableData($table);
        }
    }

    /**
     * @param string $tableName
     * @param string $filename
     * @throws \Exception
     * @throws \Throwable
     */
    public function restoreTableData(string $tableName, string $filename)
    {
        try {
            $this->builder->getConnection()->transaction(function (Connection $connection) use ($filename, $tableName) {
                $data = json_decode(file_get_contents(__DIR__ . '/../../../backup/' . $filename), true);
                $connection->table($tableName)->insert($data);
            });
        } catch (\Exception $e) {
            dump($e->getTrace());
        }
    }
}
