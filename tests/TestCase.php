<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\DatabaseTransactionsManager;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\Traits\CanConfigureMigrationCommands;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use PDO;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use CanConfigureMigrationCommands;

    protected static bool $testingDatabasePrepared = false;

    protected function setUp(): void
    {
        parent::setUp();

        $allowUnsafe = filter_var((string) env('ALLOW_NON_TEST_DB', false), FILTER_VALIDATE_BOOL);
        if ($allowUnsafe) {
            return;
        }

        $connection = DB::connection();
        $driver = $connection->getDriverName();
        $database = (string) $connection->getDatabaseName();

        if ($driver === 'sqlite') {
            return;
        }

        if (!preg_match('/(test|testing)/i', $database)) {
            throw new RuntimeException(
                "Refus d'executer les tests sur une base non test: '{$database}'. " .
                "Configurez un .env.testing avec une base dediee (ex: medisys_test) " .
                "ou positionnez ALLOW_NON_TEST_DB=true si vous assumez ce risque."
            );
        }
    }

    protected function setUpTraits()
    {
        $uses = array_flip(class_uses_recursive(static::class));

        if (isset($uses[RefreshDatabase::class])) {
            $this->refreshDatabaseDeterministic();
        }

        if (isset($uses[DatabaseMigrations::class])) {
            $this->runDatabaseMigrations();
        }

        if (isset($uses[DatabaseTruncation::class])) {
            $this->truncateDatabaseTables();
        }

        if (isset($uses[DatabaseTransactions::class])) {
            $this->beginDatabaseTransaction();
        }

        if (isset($uses[WithoutMiddleware::class])) {
            $this->disableMiddlewareForAllTests();
        }

        if (isset($uses[WithoutEvents::class])) {
            $this->disableEventsForAllTests();
        }

        if (isset($uses[WithFaker::class])) {
            $this->setUpFaker();
        }

        foreach ($uses as $trait) {
            if (method_exists($this, $method = 'setUp'.class_basename($trait))) {
                $this->{$method}();
            }

            if (method_exists($this, $method = 'tearDown'.class_basename($trait))) {
                $this->beforeApplicationDestroyed(fn () => $this->{$method}());
            }
        }

        return $uses;
    }

    protected function refreshDatabaseDeterministic(): void
    {
        $this->beforeRefreshingDatabase();

        $default = config('database.default');
        if (config("database.connections.{$default}.database") === ':memory:') {
            $this->artisan('migrate', $this->migrateUsing());
            $this->app[Kernel::class]->setArtisan(null);
        } else {
            $this->refreshMysqlTestDatabaseDeterministic();
        }

        $this->afterRefreshingDatabase();
    }

    protected function refreshMysqlTestDatabaseDeterministic(): void
    {
        if (! RefreshDatabaseState::$migrated) {
            $this->prepareDeterministicTestingDatabase();

            $this->artisan('migrate', $this->migrateUsing());
            $this->app[Kernel::class]->setArtisan(null);

            RefreshDatabaseState::$migrated = true;
        }

        $this->beginDatabaseTransaction();
    }

    protected function beginDatabaseTransaction()
    {
        $database = $this->app->make('db');

        $this->app->instance('db.transactions', $transactionsManager = new DatabaseTransactionsManager);

        foreach ($this->connectionsToTransact() as $name) {
            $connection = $database->connection($name);
            $connection->setTransactionManager($transactionsManager);
            $dispatcher = $connection->getEventDispatcher();

            $connection->unsetEventDispatcher();
            $connection->beginTransaction();
            $connection->setEventDispatcher($dispatcher);
        }

        $this->beforeApplicationDestroyed(function () use ($database) {
            foreach ($this->connectionsToTransact() as $name) {
                $connection = $database->connection($name);
                $dispatcher = $connection->getEventDispatcher();

                $connection->unsetEventDispatcher();
                $connection->rollBack();
                $connection->setEventDispatcher($dispatcher);
                $connection->disconnect();
            }
        });
    }

    protected function connectionsToTransact()
    {
        return property_exists($this, 'connectionsToTransact')
            ? $this->connectionsToTransact
            : [null];
    }

    protected function beforeRefreshingDatabase()
    {
        // hook
    }

    protected function afterRefreshingDatabase()
    {
        // hook
    }

    private function prepareDeterministicTestingDatabase(): void
    {
        if (self::$testingDatabasePrepared) {
            return;
        }

        $default = (string) config('database.default');
        $connection = (array) config("database.connections.{$default}");
        $driver = (string) ($connection['driver'] ?? '');

        if ($driver !== 'mysql') {
            self::$testingDatabasePrepared = true;

            return;
        }

        $database = (string) ($connection['database'] ?? '');
        if ($database === '') {
            throw new RuntimeException('Base de test MySQL non configuree.');
        }

        DB::purge($default);

        $dsn = !empty($connection['unix_socket'])
            ? 'mysql:unix_socket=' . $connection['unix_socket']
            : 'mysql:host=' . ($connection['host'] ?? '127.0.0.1') . ';port=' . ($connection['port'] ?? '3306');

        $pdo = new PDO(
            $dsn,
            (string) ($connection['username'] ?? ''),
            (string) ($connection['password'] ?? ''),
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]
        );

        $charset = (string) ($connection['charset'] ?? 'utf8mb4');
        $collation = (string) ($connection['collation'] ?? 'utf8mb4_unicode_ci');

        $pdo->exec('DROP DATABASE IF EXISTS `' . str_replace('`', '``', $database) . '`');
        $pdo->exec(
            'CREATE DATABASE `' . str_replace('`', '``', $database) . '` CHARACTER SET ' . $charset . ' COLLATE ' . $collation
        );

        DB::purge($default);
        self::$testingDatabasePrepared = true;
    }
}
