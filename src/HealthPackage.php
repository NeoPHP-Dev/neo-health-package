<?php

declare(strict_types=1);

namespace Vendor\NeoPHP\HealthPackage;

use Neo\Core\Package\Abstract\AbstractPackage;

class HealthPackage extends AbstractPackage
{
    public function getName(): string
    {
        return 'Health';
    }

    public function getPath(): string
    {
        return dirname(__DIR__);
    }
}