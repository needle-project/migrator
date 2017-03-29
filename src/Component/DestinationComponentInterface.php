<?php
/**
 * This file is part of the NeedleProject\Migrator package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NeedleProject\Migrator\Component;

/**
 * Interface DestinationInterface
 *
 * @package NeedleProject\Migrator\Component
 * @author Adrian Tilita <adrian@tilita.ro>
 * @copyright 2017 Adrian Tilita
 * @license https://opensource.org/licenses/MIT MIT Licence
 */
interface DestinationComponentInterface
{
    /**
     * Insert an array of data extracted by "Source"
     * @param array $data
     * @return mixed
     */
    public function bulkInsert(array $data);

    /**
     * Get number of total items that would be migrated
     * @return int
     */
    public function getTotal(): int;
}
