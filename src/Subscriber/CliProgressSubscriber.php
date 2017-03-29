<?php
/**
 * This file is part of the NeedleProject\Migrator package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NeedleProject\Migrator\Subscriber;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CliProgressSubscriber
 *
 * @package NeedleProject\Migrator\Subscriber
 * @author Adrian Tilita <adrian@tilita.ro>
 * @copyright 2017 Adrian Tilita
 * @license https://opensource.org/licenses/MIT MIT Licence
 */
class CliProgressSubscriber implements ProgressSubscriberInterface
{
    /**
     * @var \Symfony\Component\Console\Helper\ProgressBar
     */
    private $progressBar;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * CliProgressSubscriber constructor.
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    public function __construct(OutputInterface $output)
    {
        $this->progressBar = new ProgressBar($output);
        $this->output = $output;
    }

    /**
     * {@inheritdoc}
     * @param int|null $units
     * @return \NeedleProject\Migrator\Subscriber\ProgressSubscriberInterface
     */
    public function start(int $units = null): ProgressSubscriberInterface
    {
        if ($this->output->isVerbose() === true) {
            return $this;
        }
        $this->progressBar->start($units);
        return $this;
    }

    /**
     * {@inheritdoc}
     * @param int|null $units
     * @return \NeedleProject\Migrator\Subscriber\ProgressSubscriberInterface
     */
    public function advance(int $units = null): ProgressSubscriberInterface
    {
        if ($this->output->isVerbose() === true) {
            return $this;
        }
        $this->progressBar->advance($units);
        return $this;
    }

    /**
     * {@inheritdoc}
     * @return \NeedleProject\Migrator\Subscriber\ProgressSubscriberInterface
     */
    public function complete(): ProgressSubscriberInterface
    {
        if ($this->output->isVerbose() === true) {
            return $this;
        }
        $this->progressBar->finish();
        // move following output after progress-bar line
        $this->output->writeln('');
        return $this;
    }
}
