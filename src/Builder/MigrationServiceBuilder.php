<?php
/**
 * This file is part of the NeedleProject\Migrator package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NeedleProject\Migrator\Builder;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Output\OutputInterface;
use NeedleProject\Migrator\Bridge\ConsoleLoggerBridge;
use NeedleProject\Migrator\Service\MigrationService;
use NeedleProject\Migrator\Subscriber\CliProgressSubscriber;

/**
 * Class MigrationServiceBuilder
 *
 * @package NeedleProject\Migrator\Builder
 * @author Adrian Tilita <adrian@tilita.ro>
 * @copyright 2017 Adrian Tilita
 * @license https://opensource.org/licenses/MIT MIT Licence
 */
class MigrationServiceBuilder
{
    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * MigrationServiceBuilder constructor.
     *
     * @param \Psr\Log\LoggerInterface                          $logger
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    public function __construct(LoggerInterface $logger, OutputInterface $output)
    {
        $this->logger = $logger;
        $this->output = $output;
    }

    /**
     * @return \Psr\Log\LoggerInterface
     */
    protected function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    /**
     * @return \Symfony\Component\Console\Output\OutputInterface
     */
    protected function getOutput(): OutputInterface
    {
        return $this->output;
    }

    /**
     * @param array $migrationConfig
     * @return \NeedleProject\Migrator\Service\MigrationService
     */
    public function createMigrationService(array $migrationConfig): MigrationService
    {
        $sourceBuilder = new SourceBuilder($this->getLogger());
        $source = $sourceBuilder->createComponent($migrationConfig['source'], $migrationConfig['mapping']);

        $destinationBuilder = new DestinationBuilder($this->getLogger());
        $destination = $destinationBuilder->createComponent(
            $migrationConfig['destination'],
            $migrationConfig['mapping']
        );

        $migrationService = new MigrationService();
        return $migrationService->setProgressSubscriber(
            new CliProgressSubscriber($this->getOutput())
        )
            ->setLogger($this->getLogger())
            ->setSource($source)
            ->setDestination($destination);
    }
}
