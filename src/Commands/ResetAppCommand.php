<?php

namespace Mhbarry\Resourcefy\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ResetAppCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reseting app database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->warn("Application initialization");
        $driver = config('database.default');
        $database = config("database.connections.$driver.database");
        $username = config("database.connections.$driver.username");
        $password = config("database.connections.$driver.password");
        $host = config("database.connections.$driver.host");
        $port = config("database.connections.$driver.port");
        $connection = $dbh = new \PDO("mysql:host=$host:$port", $username, $password);
        $connection->exec("DROP DATABASE IF EXISTS ".$database);
        $connection->exec("CREATE DATABASE ".$database);
        Artisan::call('migrate:refresh');
        Artisan::call('passport:install');
        Artisan::call('db:seed');
        $this->info("Application initialized");
    }
}
