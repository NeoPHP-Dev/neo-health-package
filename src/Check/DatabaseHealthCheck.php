<?php

namespace Vendor\NeoPHP\HealthPackage\Check;

use Neo\Core\Database\DatabaseManager;
use Neo\Core\DI\Container;
use Vendor\NeoPHP\HealthPackage\Check\Interface\HealthCheckInterface;

class DatabaseHealthCheck implements HealthCheckInterface
{

    public function getName(): string
    {
        return 'Database';
    }

    public function check(Container $container): array
    {
        $start = microtime(true);

        try {
            $db = $container->get(DatabaseManager::class);
            $db->fetch('SELECT 1');

            return [
                'status' => 'ok',
                'message' => null,
                'duration_ms' => round((microtime(true) - $start) * 1000, 2),
            ];
        } catch (\Throwable $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
                'duration_ms' => round((microtime(true) - $start) * 1000, 2),
            ];
        }
    }
}