<?php
/**
 * This file is part of the NeedleProject\Migrator package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NeedleProject\Migrator\Component\MySQL;

use Psr\Log\LoggerInterface;
use Psr\Log\LoggerAwareInterface;

/**
 * Class AbstractPdoComponent
 *
 * @package NeedleProject\Migrator\Component
 * @author Adrian Tilita <adrian@tilita.ro>
 * @copyright 2017 Adrian Tilita
 * @license https://opensource.org/licenses/MIT MIT Licence
 */
abstract class AbstractMySQLComponent implements LoggerAwareInterface
{
    /**
     * Defined keys for $connectionParameters
     * @const string
     */
    const CONNECTION_HOST_PARAMETER = 'hostname';
    const CONNECTION_PORT_PARAMETER = 'port';
    const CONNECTION_DATABASE_PARAMETER = 'database';
    const CONNECTION_USERNAME_PARAMETER = 'username';
    const CONNECTION_PASSWORD_PARAMETER = 'password';

    /**
     * @var \PDO
     */
    private $connection;

    /**
     * @var string
     */
    private $tableName;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param array  $connectionParameters
     * @param string $tableName
     */
    protected function build(array $connectionParameters, string $tableName)
    {
        $this->tableName = $tableName;
        $this->connection = new \PDO(
            $this->composeDns($connectionParameters),
            $connectionParameters[static::CONNECTION_USERNAME_PARAMETER],
            $connectionParameters[static::CONNECTION_PASSWORD_PARAMETER]
        );
    }

    /**
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return \Psr\Log\LoggerInterface
     */
    protected function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    /**
     * Compose a mysql dns string
     * @todo    Add port mapping
     * @param array $parameters
     * @return string
     */
    private function composeDns(array $parameters): string
    {
        return sprintf(
            'mysql:dbname=%s;host=%s',
            $parameters[static::CONNECTION_DATABASE_PARAMETER],
            $parameters[static::CONNECTION_HOST_PARAMETER]
        );
    }

    /**
     * @return \PDO
     */
    protected function getConnection(): \PDO
    {
        return $this->connection;
    }

    /**
     * @return string
     */
    protected function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * Run a select query
     * @param $query
     * @return array
     */
    protected function fetchQueryResult(string $query): array
    {
        $startTime = microtime(true);
        $result = $this->connection
            ->query(
                $query
            )->fetchAll(\PDO::FETCH_ASSOC);
        $duration = round(microtime(true) - $startTime, 2);
        if ($duration > 1) {
            $this->logger->warning(
                sprintf("Query duration: %d sec - Query: %s", $duration, $query)
            );
        } else {
            $this->logger->notice(
                sprintf(
                    "Query duration: %d sec - Query: %s",
                    $duration,
                    substr($query, 0, 100)
                )
            );
        }
        return $result;
    }
}
