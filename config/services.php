<?php

declare(strict_types=1);

use Illuminate\Database\Capsule\Manager as Capsule;
use Psr\Container\ContainerInterface;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

return function (ContainerInterface $container): void {
    // 数据库 Eloquent ORM
    $container->set('db', function () {
        $capsule = new Capsule();
        $capsule->addConnection([
            'driver'    => 'mysql',
            'host'      => $_ENV['DB_HOST'] ?? '127.0.0.1',
            'database'  => $_ENV['DB_DATABASE'] ?? 'sports_api',
            'username'  => $_ENV['DB_USERNAME'] ?? 'root',
            'password'  => $_ENV['DB_PASSWORD'] ?? '',
            'charset'   => $_ENV['DB_CHARSET'] ?? 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => '',
        ]);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
        return $capsule;
    });

    // 日志
    $container->set('logger', function () {
        $logger = new Logger('app');
        $logger->pushHandler(
            new StreamHandler(__DIR__ . '/../storage/logs/app.log', Logger::DEBUG)
        );
        return $logger;
    });

    // 配置
    $container->set('config', function () {
        return [
            'app' => [
                'env'   => $_ENV['APP_ENV'] ?? 'development',
                'debug' => filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN),
                'key'   => $_ENV['APP_KEY'] ?? '',
            ],
            'jwt' => [
                'secret' => $_ENV['JWT_SECRET'] ?? 'default-secret-change-in-production',
                'expire' => (int) ($_ENV['JWT_EXPIRE'] ?? 7200),
            ],
        ];
    });
};
