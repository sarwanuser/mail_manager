<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
    */

    'default' => env('DB_CONNECTION', 'routing'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the database connections setup for your application.
    | Of course, examples of configuring each database platform that is
    | supported by Laravel is shown below to make development simple.
    |
    |
    | All database work in Laravel is done through the PHP PDO facilities
    | so make sure you have the driver for your particular database of
    | choice installed on your machine before you begin development.
    |
    */

    'connections' => [

        'sqlite' => [
            'driver' => 'sqlite',
            'url' => env('DATABASE_URL'),
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix' => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
        ],
		'cart_management' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST1', '127.0.0.1'),
            'port' => env('DB_PORT1', 3306),
            'database' => env('DB_DATABASE1', 'forge'),
            'username' => env('DB_USERNAME1', 'forge'),
            'password' => env('DB_PASSWORD1', ''),
            'unix_socket' => env('DB_SOCKET1', ''),
            'charset' => env('DB_CHARSET1', 'utf8mb4'),
            'collation' => env('DB_COLLATION1', 'utf8mb4_unicode_ci'),
            'prefix' => env('DB_PREFIX1', ''),
            'strict' => env('DB_STRICT_MODE1', true),
            'engine' => env('DB_ENGINE1', null),
            'timezone' => env('DB_TIMEZONE1', '+00:00'),
        ],

        'routing' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST2', '127.0.0.1'),
            'port' => env('DB_PORT2', 3306),
            'database' => env('DB_DATABASE2', 'forge'),
            'username' => env('DB_USERNAME2', 'forge'),
            'password' => env('DB_PASSWORD2', ''),
            'unix_socket' => env('DB_SOCKET2', ''),
            'charset' => env('DB_CHARSET2', 'utf8mb4'),
            'collation' => env('DB_COLLATION2', 'utf8mb4_unicode_ci'),
            'prefix' => env('DB_PREFIX2', ''),
            'strict' => env('DB_STRICT_MODE2', true),
            'engine' => env('DB_ENGINE2', null),
            'timezone' => env('DB_TIMEZONE2', '+00:00'),
        ],

        'sp_notification' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST3', '127.0.0.1'),
            'port' => env('DB_PORT3', 3306),
            'database' => env('DB_DATABASE3', 'forge'),
            'username' => env('DB_USERNAME3', 'forge'),
            'password' => env('DB_PASSWORD3', ''),
            'unix_socket' => env('DB_SOCKET3', ''),
            'charset' => env('DB_CHARSET3', 'utf8mb4'),
            'collation' => env('DB_COLLATION3', 'utf8mb4_unicode_ci'),
            'prefix' => env('DB_PREFIX3', ''),
            'strict' => env('DB_STRICT_MODE3', true),
            'engine' => env('DB_ENGINE3', null),
            'timezone' => env('DB_TIMEZONE3', '+00:00'),
        ],

        'sp_management' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST4', '127.0.0.1'),
            'port' => env('DB_PORT4', 3306),
            'database' => env('DB_DATABASE4', 'forge'),
            'username' => env('DB_USERNAME4', 'forge'),
            'password' => env('DB_PASSWORD4', ''),
            'unix_socket' => env('DB_SOCKET4', ''),
            'charset' => env('DB_CHARSET4', 'utf8mb4'),
            'collation' => env('DB_COLLATION4', 'utf8mb4_unicode_ci'),
            'prefix' => env('DB_PREFIX4', ''),
            'strict' => env('DB_STRICT_MODE4', true),
            'engine' => env('DB_ENGINE4', null),
            'timezone' => env('DB_TIMEZONE4', '+00:00'),
        ],

        'catalog_management' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST5', '127.0.0.1'),
            'port' => env('DB_PORT5', 3306),
            'database' => env('DB_DATABASE5', 'forge'),
            'username' => env('DB_USERNAME5', 'forge'),
            'password' => env('DB_PASSWORD5', ''),
            'unix_socket' => env('DB_SOCKET5', ''),
            'charset' => env('DB_CHARSET5', 'utf8mb4'),
            'collation' => env('DB_COLLATION5', 'utf8mb4_unicode_ci'),
            'prefix' => env('DB_PREFIX5', ''),
            'strict' => env('DB_STRICT_MODE5', true),
            'engine' => env('DB_ENGINE5', null),
            'timezone' => env('DB_TIMEZONE5', '+00:00'),
        ],
        
        'clykk_um' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST6', '127.0.0.1'),
            'port' => env('DB_PORT6', 3306),
            'database' => env('DB_DATABASE6', 'forge'),
            'username' => env('DB_USERNAME6', 'forge'),
            'password' => env('DB_PASSWORD6', ''),
            'unix_socket' => env('DB_SOCKET6', ''),
            'charset' => env('DB_CHARSET6', 'utf8mb4'),
            'collation' => env('DB_COLLATION6', 'utf8mb4_unicode_ci'),
            'prefix' => env('DB_PREFIX6', ''),
            'strict' => env('DB_STRICT_MODE6', true),
            'engine' => env('DB_ENGINE6', null),
            'timezone' => env('DB_TIMEZONE6', '+00:00'),
        ],

        'location_service' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST7', '127.0.0.1'),
            'port' => env('DB_PORT7', 3306),
            'database' => env('DB_DATABASE7', 'forge'),
            'username' => env('DB_USERNAME7', 'forge'),
            'password' => env('DB_PASSWORD7', ''),
            'unix_socket' => env('DB_SOCKET7', ''),
            'charset' => env('DB_CHARSET7', 'utf8mb4'),
            'collation' => env('DB_COLLATION7', 'utf8mb4_unicode_ci'),
            'prefix' => env('DB_PREFIX7', ''),
            'strict' => env('DB_STRICT_MODE7', true),
            'engine' => env('DB_ENGINE7', null),
            'timezone' => env('DB_TIMEZONE7', '+00:00'),
        ],

        'likes_shares' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST8', '127.0.0.1'),
            'port' => env('DB_PORT8', 3306),
            'database' => env('DB_DATABASE8', 'forge'),
            'username' => env('DB_USERNAME8', 'forge'),
            'password' => env('DB_PASSWORD8', ''),
            'unix_socket' => env('DB_SOCKET8', ''),
            'charset' => env('DB_CHARSET8', 'utf8mb4'),
            'collation' => env('DB_COLLATION8', 'utf8mb4_unicode_ci'),
            'prefix' => env('DB_PREFIX8', ''),
            'strict' => env('DB_STRICT_MODE8', true),
            'engine' => env('DB_ENGINE8', null),
            'timezone' => env('DB_TIMEZONE8', '+00:00'),
        ],

        'mysql' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'pgsql' => [
            'driver' => 'pgsql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'schema' => 'public',
            'sslmode' => 'prefer',
        ],

        'sqlsrv' => [
            'driver' => 'sqlsrv',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '1433'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
    */

    'migrations' => 'migrations',

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer body of commands than a typical key-value system
    | such as APC or Memcached. Laravel makes it easy to dig right in.
    |
    */

    'redis' => [

        'client' => env('REDIS_CLIENT', 'phpredis'),

        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),
            'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_database_'),
        ],

        'default' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DB', '0'),
        ],

        'cache' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_CACHE_DB', '1'),
        ],

    ],

];
