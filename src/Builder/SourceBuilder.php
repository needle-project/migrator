<?php
/**
 * This file is part of the NeedleProject\Migrator package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NeedleProject\Migrator\Builder;

use NeedleProject\Migrator\Component\MySQL\MySQLSourceComponent;
use NeedleProject\Migrator\Component\SourceComponentInterface;
use Psr\Log\LoggerInterface;

/**
 * Class SourceBuilder
 *
 * @package NeedleProject\Migrator\Builder
 * @author Adrian Tilita <adrian@tilita.ro>
 * @copyright 2017 Adrian Tilita
 * @license https://opensource.org/licenses/MIT MIT Licence
 */
class SourceBuilder
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
     * @return \NeedleProject\Migrator\Component\SourceComponentInterface
     */
    public function createComponent(array $sourceConfig, array $mapping): SourceComponentInterface
    {
        list ($config, $tableName, $join, $filters, $fields) = $this->parseConfig($sourceConfig, $mapping);
        if (is_null($join)) {
            $join = '';
        }
        if (is_null($filters)) {
            $filters = '';
        }
        $source = new MySQLSourceComponent(
            $config,
            $tableName,
            $join,
            $filters,
            $fields,
            isset($sourceConfig['parameters']['order_query']) ?
                $sourceConfig['parameters']['order_query'] :
                ''
        );
        $source->setChunkSize($sourceConfig['chunk_size']);
        $source->setLogger($this->logger);
        return $source;
    }

    private function parseConfig($sourceConfig, $mapping)
    {
        $config = $sourceConfig['connection'];
        $tableName = $sourceConfig['connection']['table'];
        $join = isset($sourceConfig['parameters']['join']) ?
            $sourceConfig['parameters']['join'] :
            '';

        $fields = [];
        $joinFields = isset($sourceConfig['parameters']['join_fields']) ?
            $sourceConfig['parameters']['join_fields'] :
            [];
        if (!empty($joinFields)) {
            foreach ($joinFields as $tableField => $fieldAlias) {
                $fields[$fieldAlias] = $tableField;
            }
        }
        foreach ($mapping as $fromKey => $toKey) {
            if (isset($fields[$fromKey])) {
                continue;
            }
            $fields[$fromKey] = "`{$tableName}`.`{$fromKey}`";
        }
        $fields = implode(", ", $fields);

        $filters = '';
        if (isset($sourceConfig['parameters']['filters'])) {
            $tempFilter = $sourceConfig['parameters']['filters'];
            $filters = [];
            foreach ($tempFilter as $line) {
                foreach ($line as $field => $value) {
                    if (!is_array($value)) {
                        $filters[] = "{$field} = \"{$value}\"";
                    } else {
                        $filters[] = "{$field} IN(" . implode(',', $value) . ')';
                    }
                }
            }
            $filters = ' WHERE ' . implode(' AND ', $filters);
        }
        if (isset($sourceConfig['parameters']['query_filter']) && $filters === '') {
            $filters = " WHERE " . $sourceConfig['parameters']['query_filter'];
        }
        return [
            $config, $tableName, $join, $filters, $fields
        ];
    }
}
