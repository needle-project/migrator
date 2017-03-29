<?php
/**
 * This file is part of the NeedleProject\Migrator package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NeedleProject\Migrator\Service;

use NeedleProject\Migrator\Subscriber\ProgressSubscriberInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use NeedleProject\Migrator\Component\SourceComponentInterface;
use NeedleProject\Migrator\Component\DestinationComponentInterface;

/**
 * Class MigrationService
 *
 * @package NeedleProject\Migrator\Service
 * @author Adrian Tilita <adrian@tilita.ro>
 * @copyright 2017 Adrian Tilita
 * @license https://opensource.org/licenses/MIT MIT Licence
 */
class MigrationService implements LoggerAwareInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var SourceComponentInterface
     */
    private $source;

    /**
     * @var DestinationComponentInterface
     */
    private $destination;

    /**
     * @var ProgressSubscriberInterface
     */
    private $progressSubscriber;

    /**
     * @param \NeedleProject\Migrator\Subscriber\ProgressSubscriberInterface $subscriber
     * @return \NeedleProject\Migrator\Service\MigrationService
     */
    public function setProgressSubscriber(ProgressSubscriberInterface $subscriber): MigrationService
    {
        $this->progressSubscriber = $subscriber;
        return $this;
    }

    /**
     * @param \Psr\Log\LoggerInterface $logger
     * @return $this
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
        return $this;
    }

    /**
     * @param \NeedleProject\Migrator\Component\SourceComponentInterface $source
     * @return $this
     */
    public function setSource(SourceComponentInterface $source)
    {
        $this->source = $source;
        return $this;
    }

    /**
     * @param \NeedleProject\Migrator\Component\DestinationComponentInterface $destination
     * @return $this
     */
    public function setDestination(DestinationComponentInterface $destination)
    {
        $this->destination = $destination;
        return $this;
    }

    /**
     * Start the migration process
     */
    public function startMigration()
    {
        $pages = $this->source->getPageCount();
        $totalLines = $this->source->getTotal();

        $currentDestinationCount = $this->destination->getTotal();

        // log points
        $this->logger->info(
            sprintf("%d lines need to be migrated in %d chunks", $totalLines, $pages)
        );
        $this->logger->info(
            sprintf("%d current lines in destination", $currentDestinationCount)
        );
        $this->progressSubscriber->start($pages);

        for ($i = 0; $i < $pages; $i++) {
            $this->progressSubscriber->advance(1);

            $startTime = microtime(true);
            $data = $this->source->fetch($i);
            $this->logger->debug(
                sprintf(
                    "Fetching page %d took %s sec.",
                    $i,
                    round(microtime(true) - $startTime, 2)
                )
            );

            $startTime = microtime(true);
            $this->destination->bulkInsert($data);
            $this->logger->debug(
                sprintf(
                    "Inserting page %d took %s sec.",
                    $i,
                    round(microtime(true) - $startTime, 2)
                )
            );
        }
        $this->progressSubscriber->complete();
        $newDestinationCount = $this->destination->getTotal();
        $this->logger->info(
            sprintf(
                "%d current lines in destination, %d more before migration",
                $newDestinationCount,
                $newDestinationCount - $currentDestinationCount
            )
        );
    }
}
