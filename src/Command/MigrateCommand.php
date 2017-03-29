<?php
/**
 * This file is part of the NeedleProject\Migrator package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NeedleProject\Migrator\Command;

use NeedleProject\FileIo\File;
use NeedleProject\Migrator\Bridge\ConsoleLoggerBridge;
use NeedleProject\Migrator\Builder\MigrationServiceBuilder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class MigrateCommand
 *
 * @package NeedleProject\Migrator\Command
 * @author Adrian Tilita <adrian@tilita.ro>
 * @copyright 2017 Adrian Tilita
 * @license https://opensource.org/licenses/MIT MIT Licence
 */
class MigrateCommand extends Command
{
    /**
     * @const string
     */
    const OPTION_CONFIG_FILE = 'config_file';

    /**
     * {@parentDoc}
     */
    protected function configure()
    {
        $this->setName('migration:start')
            ->addOption(
                static::OPTION_CONFIG_FILE,
                "c",
                InputOption::VALUE_REQUIRED,
                "Path to the config file (yaml) - relative to execution path"
            )
            ->setDescription('Start db migration');
    }

    /**
     * Execute the battle of the year
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $configData = $this->readConfig(
            $input->getOption(static::OPTION_CONFIG_FILE)
        );
        $consoleFormatter = new FormatterHelper();

        $logger = new ConsoleLoggerBridge($output);
        $migrationServiceBuilder = new MigrationServiceBuilder($logger, $output);

        foreach ($configData as $migrationAliasName => $migrationDetails) {
            $titleBlock = $consoleFormatter->formatBlock(
                sprintf("Found %s migration config...", $migrationAliasName),
                'bg=green;options=bold',
                true
            );
            $output->writeln($titleBlock);

            $migrationService = $migrationServiceBuilder->createMigrationService($migrationDetails, $output);
            $output->writeln(sprintf("<info>Started migration for %s</info>", $migrationAliasName));
            $logger->info("Starting migration");
            $start = microtime(true);
            $migrationService->startMigration();
            $output->writeln(
                sprintf(
                    "<info>Completed migration migration for %s in %d sec</info>",
                    $migrationAliasName,
                    round(microtime(true) - $start, 4)
                )
            );
        }
        return 1;
    }

    /**
     * @param string $filename
     * @return array
     */
    private function readConfig(string $filename): array
    {
        $file = new File($filename);
        return $file->getContent()->getArray();
    }
}
