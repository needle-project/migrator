<?php
/**
 * This file is part of the NeedleProject\Migrator package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NeedleProject\Migrator\Component\MySQL;

use NeedleProject\Migrator\Component\SourceComponentInterface;

/**
 * Class MySqlSourceComponent
 *
 * @package NeedleProject\Migrator\Component
 * @author Adrian Tilita <adrian@tilita.ro>
 * @copyright 2017 Adrian Tilita
 * @license https://opensource.org/licenses/MIT MIT Licence
 */
class MySQLSourceComponent extends AbstractMySQLComponent implements SourceComponentInterface
{
    /**
     * Join tables query append
     * @var string
     */
    private $join;

    /**
     * Filters for select (where [key0 => value0 and key1 => value1]
     * @var string
     */
    private $filter;

    /**
     * Order field - ex: (BY `id` DESC)
     * @var string
     */
    private $order;

    /**
     * The fields to extract from db. Avoid `*`
     * @var string
     */
    private $fields;

    /**
     * The number of results per page
     * @var int
     */
    private $chunkSize = 1000;

    /**
     * PdoQuery constructor.
     *
     * @param array  $connectionDetails
     * @param string $tableName
     * @param string $join
     * @param string $filter
     * @param string $fields
     * @param string $order
     */
    public function __construct(
        array $connectionDetails,
        string $tableName,
        string $join = '',
        string $filter = '',
        string $fields = '*',
        string $order = ''
    ) {
        $this->build($connectionDetails, $tableName);
        $this->join = $join;
        $this->filter = $filter;
        $this->order = $order;
        $this->fields = $fields;
    }

    /**
     * {@inheritdoc}
     * @param int $size
     * @return \NeedleProject\Migrator\Component\SourceComponentInterface
     */
    public function setChunkSize(int $size): SourceComponentInterface
    {
        $this->chunkSize = $size;
        return $this;
    }

    /**
     * @return int
     */
    public function getChunkSize(): int
    {
        return $this->chunkSize;
    }

    /**
     * Get number of total items that would be migrated
     * @return int
     */
    public function getTotal(): int
    {
        return $this->fetchQueryResult(
            sprintf("SELECT count(*) as `ct` FROM `%s` %s %s", $this->getTableName(), $this->join, $this->filter)
        )[0]['ct'];
    }

    /**
     * Get the number of pages that correspond to a desired chunk
     * @return int
     */
    public function getPageCount(): int
    {
        return ceil($this->getTotal() / $this->chunkSize);
    }

    /**
     * Fetch a specific page of data to be imported
     * @param int $pageNr
     * @return array
     */
    public function fetch(int $pageNr): array
    {
        return $this->fetchQueryResult(
            sprintf(
                "SELECT %s FROM `%s` %s %s %s %s",
                $this->fields,
                $this->getTableName(),
                $this->join,
                $this->filter,
                $this->order,
                $this->getLimitQuery($pageNr)
            )
        );
    }

    /**
     * @param int $pageNr
     * @return string
     */
    private function getLimitQuery(int $pageNr): string
    {
        $startOffset = 0;
        if ($pageNr > 1) {
            $startOffset = $pageNr * $this->chunkSize - $this->chunkSize;
        }
        return sprintf(" LIMIT %d, %d", $startOffset, $this->chunkSize);
    }
}
