<?php
/**
 * This file is part of the NeedleProject\Migrator package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NeedleProject\Migrator\Component\MySQL;

use NeedleProject\Migrator\Component\DestinationComponentInterface;
use Psr\Log\LoggerAwareInterface;

/**
 * Class MySQLDestinationComponent
 *
 * @package NeedleProject\Migrator\Component\MySQL
 * @author Adrian Tilita <adrian@tilita.ro>
 * @copyright 2017 Adrian Tilita
 * @license https://opensource.org/licenses/MIT MIT Licence
 */
class MySQLDestinationComponent extends AbstractMySQLComponent implements
    DestinationComponentInterface,
    LoggerAwareInterface
{
    /**
     * Array of mapping the fields source_table_id => destination_table_id
     * @var array
     */
    private $fieldsMap;

    /**
     * For values that should not be null in destination, use a set of default values
     * example: ['field_name' => 0] - for field_name that should not contain null value
     * @var array
     */
    private $overrideNullValues;

    /**
     * @var array
     */
    private $valueMapping;

    /**
     * MySQLDestinationComponent constructor.
     *
     * @param array  $connectionDetails
     * @param string $tableName
     * @param array  $fieldsMap
     */
    public function __construct(array $connectionDetails, string $tableName, array $fieldsMap)
    {
        $this->build($connectionDetails, $tableName);
        $this->fieldsMap = $fieldsMap;
    }

    /**
     * @param array $fields
     * @return \NeedleProject\Migrator\Component\DestinationComponentInterface
     */
    public function setOverrideNullValues(array $fields): DestinationComponentInterface
    {
        $this->overrideNullValues = $fields;
        return $this;
    }

    /**
     * @param array $mappingValues
     */
    public function setValueMapping(array $mappingValues)
    {
        $this->valueMapping = $mappingValues;
    }

    /**
     * Insert an array of data extracted by "Source"
     * @param array $data
     * @return mixed
     */
    public function bulkInsert(array $data)
    {
        $baseQuery = "INSERT IGNORE INTO `{$this->getTableName()}` " .
            " (" . implode($this->fieldsMap, ', ') . ") ";

        $success = 0;
        foreach ($data as $importLine) {
            $bindingFields = [];
            $bindingValues = [];
            foreach ($this->fieldsMap as $sourceField => $destinationField) {
                $fieldAlias = ":{$destinationField}";
                $bindingFields[] = $fieldAlias;
                $bindingValues[$fieldAlias] = $importLine[$sourceField];

                // if we override null values for specific fields
                if (is_null($bindingValues[$fieldAlias]) &&
                    isset($this->overrideNullValues[$destinationField])) {
                    $bindingValues[$fieldAlias] = $this->overrideNullValues[$destinationField];
                }
                if (isset($this->valueMapping[$sourceField])) {
                    if (!isset($this->valueMapping[$sourceField][$importLine[$sourceField]])) {
                        $this->getLogger()->debug("Could not find a valid mapping for {$importLine[$sourceField]}");
                    } else {
                        $bindingValues[$fieldAlias] = $this->valueMapping[$sourceField][$importLine[$sourceField]];
                    }
                }
            }
            $finalQuery = sprintf("%s VALUES(%s)", $baseQuery, implode(',', $bindingFields));
            $statement = $this->getConnection()->prepare($finalQuery);
            $result = $statement->execute($bindingValues);
            if (true === $result && $statement->rowCount() <> 0) {
                $success++;
            }
        }
        $this->getLogger()->debug("Inserted " . $success . ' row(s) from ' . count($data) . " lines.");
    }

    /**
     * Get number of total items that would be migrated
     * @return int
     */
    public function getTotal(): int
    {
        return $this->fetchQueryResult(
            sprintf("SELECT count(*) as `ct` FROM `%s`", $this->getTableName())
        )[0]['ct'];
    }
}
