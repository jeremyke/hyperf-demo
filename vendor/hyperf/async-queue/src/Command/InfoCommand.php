<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf-cloud/hyperf/blob/master/LICENSE
 */

namespace Hyperf\AsyncQueue\Command;

use Hyperf\AsyncQueue\Driver\DriverFactory;
use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Input\InputArgument;

/**
 * @Command
 */
class InfoCommand extends HyperfCommand
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        parent::__construct('queue:info');
    }

    public function handle()
    {
        $name = $this->input->getArgument('name');
        $factory = $this->container->get(DriverFactory::class);
        $driver = $factory->get($name);

        $info = $driver->info();
        foreach ($info as $key => $count) {
            $this->output->writeln(sprintf('<fg=green>%s count is %d.</>', $key, $count));
        }
    }

    protected function configure()
    {
        $this->setDescription('Delete all message from failed queue.');
        $this->addArgument('name', InputArgument::OPTIONAL, 'The name of queue.', 'default');
    }
}
