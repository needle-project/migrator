<?php
/**
 * This file is part of the NeedleProject\Migrator package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NeedleProject\Migrator\Component;

/**
 * Interface SourceComponentInterface
 *
 * @package NeedleProject\Migrator\Component
 * @author Adrian Tilita <adrian@tilita.ro>
 * @copyright 2017 Adrian Tilita
 * @license https://opensource.org/licenses/MIT MIT Licence
 */
interface SourceComponentInterface
{
    /**
     * Set the number of items that should be retrieved in a page
     * @param int $size
     * @return \NeedleProject\Migrator\Component\SourceComponentInterface
     */
    public function setChunkSize(int $size): SourceComponentInterface;

    /**
     * Return the size of the chunk
     * @return int
     */
    public function getChunkSize(): int;

    /**
     * Get number of total items that would be migrated
     * @return int
     */
    public function getTotal(): int;

    /**
     * Get the number of pages that correspond to a desired chunk
     * @return int
     */
    public function getPageCount(): int;

    /**
     * Fetch a specific page of data to be imported
     * @param int $pageNr
     * @return array
     */
    public function fetch(int $pageNr): array;
}
