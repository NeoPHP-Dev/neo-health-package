<?php

declare(strict_types=1);

namespace Vendor\NeoPHP\HealthPackage\Controllers;

use Neo\Core\Controller\AbstractController;
use Neo\Core\Http\Response\Types\JsonResponse;
use Neo\Core\Routing\Attribute\MainRoute;
use Neo\Core\Routing\Attribute\Route;
use Vendor\NeoPHP\HealthPackage\Check\DatabaseHealthCheck;
use Vendor\NeoPHP\HealthPackage\Check\DiskSpaceHealthCheck;

#[MainRoute(path: '/health', name: 'health')]
class HealthController extends AbstractController
{
    #[Route(path: '/', name: 'index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $checks = [
            new DatabaseHealthCheck(),
            new DiskSpaceHealthCheck(),
        ];

        $results = [];
        $overallOk = true;

        foreach ($checks as $check) {
            $result = $check->check($this->container);
            $results[$check->getName()] = $result;

            if ($result['status'] !== 'ok') {
                $overallOk = false;
            }
        }

        return $this->json([
            'status' => $overallOk ? 'ok' : 'degraded',
            'timestamp' => new \DateTime()->format(DATE_ATOM),
            'checks' => $results,
        ], $overallOk ? 200 : 503);
    }
}