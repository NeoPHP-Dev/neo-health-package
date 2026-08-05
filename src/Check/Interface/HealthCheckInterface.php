<?php

declare(strict_types=1);

namespace Vendor\NeoPHP\HealthPackage\Check\Interface;

use Neo\Core\DI\Container;

interface HealthCheckInterface
{
    public function getName(): string;

    public function check(Container $container): array;
}