<?php
/**
 * This file is part of the NeedleProject\Migrator package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NeedleProject\Migrator\Subscriber;

/**
 * Interface ProgressSubscriberInterface
 *
 * @package NeedleProject\Migrator\Subscriber
 * @author Adrian Tilita <adrian@tilita.ro>
 * @copyright 2017 Adrian Tilita
 * @license https://opensource.org/licenses/MIT MIT Licence
 */
interface ProgressSubscriberInterface
{
    /**
     * Start progress
     * @param int $units
     * @return \NeedleProject\Migrator\Subscriber\ProgressSubscriberInterface
     */
    public function start(int $units = null): ProgressSubscriberInterface;

    /**
     * @param $units
     * @return \NeedleProject\Migrator\Subscriber\ProgressSubscriberInterface
     */
    public function advance(int $units = null): ProgressSubscriberInterface;

    /**
     * @return \NeedleProject\Migrator\Subscriber\ProgressSubscriberInterface
     */
    public function complete(): ProgressSubscriberInterface;
}
