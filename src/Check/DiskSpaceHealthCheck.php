<?php

namespace Vendor\NeoPHP\HealthPackage\Check;

use Neo\Core\DI\Container;
use Neo\Core\Utils\Config\ConfigManager;
use Vendor\NeoPHP\HealthPackage\Check\Interface\HealthCheckInterface;

class DiskSpaceHealthCheck implements HealthCheckInterface
{

    public function getName(): string
    {
        return 'disk_space';
    }

    public function check(Container $container): array
    {
        $start = microtime(true);

        $config = $container->get(ConfigManager::class);
        $minFreePercent = (float)$config->from('health')->get('disk_min_free_percent', 10);

        $free = disk_free_space(\ROOT_DIR);
        $total = disk_total_space(\ROOT_DIR);

        if ($free === false || $total === false || $total === 0) {
            return [
                'status' => 'ERROR',
                'message' => 'Unable to read disk space',
                'duration_ms' => round((microtime(true) - $start) * 1000, 2),
            ];
        }

        $freePercent = ($free / $total) * 100;
        $ok = $freePercent >= $minFreePercent;

        return [
            'status' => $ok ? 'OK' : 'ERROR',
            'message' => $ok ? null : sprintf('Only %.1f%% disk free space.', $freePercent),
            'duration_ms' => round((microtime(true) - $start) * 1000, 2),
        ];
    }
}