<?php

namespace cat;

use Illuminate\Database\Capsule\Manager as Capsule;

/**
 * Initializes a new database connection using the Capsule manager.
 *
 * Configures the connection with settings such as driver, host, database,
 * username, password, charset, collation, and prefix from environment variables.
 *
 * Sets the Capsule instance as globally accessible and boots the Eloquent ORM.
 */
class Database
{
    public function __construct()
    {
        $capsule = new Capsule;

        $capsule->addConnection([
            'driver'    => 'mysql',
            'host'      => $_ENV['DB_HOST'],
            'database'  => $_ENV['DB_DATABASE'],
            'username'  => $_ENV['DB_USERNAME'],
            'password'  => $_ENV['DB_PASSWORD'],
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ]);

        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    }
}