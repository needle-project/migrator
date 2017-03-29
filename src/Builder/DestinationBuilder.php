<?php
/**
 * This file is part of the NeedleProject\Migrator package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NeedleProject\Migrator\Builder;

use Psr\Log\LoggerInterface;
use NeedleProject\Migrator\Component\DestinationComponentInterface;
use NeedleProject\Migrator\Component\MySQL\MySQLDestinationComponent;

/**
 * Class DestinationBuilder
 *
 * @package NeedleProject\Migrator\Builder
 * @author Adrian Tilita <adrian@tilita.ro>
 * @copyright 2017 Adrian Tilita
 * @license https://opensource.org/licenses/MIT MIT Licence
 */
class DestinationBuilder
{
    /**
     * SourceBuilder constructor.
     *
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param array $sourceConfig
     * @param array $mapping
     * @return \NeedleProject\Migrator\Component\DestinationComponentInterface
     */
    public function createComponent(array $sourceConfig, array $mapping): DestinationComponentInterface
    {
        $destination = new MySQLDestinationComponent(
            $sourceConfig['connection'],
            $sourceConfig['connection']['table'],
            $mapping
        );
        if (isset($sourceConfig['parameters']['override_null_values'])) {
            $destination->setOverrideNullValues($sourceConfig['parameters']['override_null_values']);
        }
        if (isset($sourceConfig['parameters']['value_mapper'])) {
            $destination->setValueMapping($sourceConfig['parameters']['value_mapper']);
        }
        $destination->setLogger($this->logger);
        return $destination;
    }
}
