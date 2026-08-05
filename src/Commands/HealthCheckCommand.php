<?php

declare(strict_types=1);

namespace Vendor\NeoPHP\HealthCheck\Commands;

use Neo\Core\Application\ApplicationPaths;
use Neo\Core\Console\Abstract\AbstractCommand;
use Neo\Core\Console\Attribute\Command;
use Neo\Core\Console\Enum\ExitCode;
use Neo\Core\Console\Input\Input;
use Neo\Core\Console\Input\InputOption;
use Neo\Core\Console\Output\Output;
use Neo\Core\DI\Container;
use Vendor\NeoPHP\HealthPackage\Check\DatabaseHealthCheck;
use Vendor\NeoPHP\HealthPackage\Check\DiskSpaceHealthCheck;

#[Command(
    name: 'health:check',
    description: 'Run all registered health checks for a project',
    category: 'Health',
)]
final class HealthCheckCommand extends AbstractCommand
{
    public function __construct(private readonly Container $container) {}

    public function configure(): void
    {
        $this->addOption(
            name: 'project',
            shortcut: null,
            mode: InputOption::REQUIRED,
            description: 'Target project',
        );
    }

    public function do(Input $input, Output $output): ExitCode
    {
        $project = $input->getOption('project');

        if (!is_dir($this->container->get('basePath') . "/src/$project")) {
            Output::error("Project '$project' not found.");
            return ExitCode::FAILURE;
        }

        new ApplicationPaths($this->container)->register($project);

        $checks = [
            new DatabaseHealthCheck(),
            new DiskSpaceHealthCheck(),
        ];

        $allOk = true;

        Output::title("Health check — $project");

        foreach ($checks as $check) {
            $result = $check->check($this->container);
            $icon = $result['status'] === 'ok' ? '✔' : '✘';
            $color = $result['status'] === 'ok' ? 'green' : 'red';

            echo Output::colorize("  $icon " . str_pad($check->getName(), 20), $color)
                . Output::colorize($result['duration_ms'] . 'ms', 'dim');

            if ($result['message'] !== null) {
                echo Output::colorize(' — ' . $result['message'], 'dim');
            }

            echo "\n";

            if ($result['status'] !== 'ok') {
                $allOk = false;
            }
        }

        Output::newLine();

        return $allOk ? ExitCode::SUCCESS : ExitCode::FAILURE;
    }
}