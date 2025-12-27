<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace OmniIconDeps\Symfony\UX\Icons\Command;

use OmniIconDeps\Symfony\Component\Console\Attribute\AsCommand;
use OmniIconDeps\Symfony\Component\Console\Command\Command;
use OmniIconDeps\Symfony\Component\Console\Input\InputInterface;
use OmniIconDeps\Symfony\Component\Console\Output\OutputInterface;
use OmniIconDeps\Symfony\Component\Console\Style\SymfonyStyle;
use OmniIconDeps\Symfony\UX\Icons\IconCacheWarmer;
/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @internal
 */
#[AsCommand(name: 'ux:icons:warm-cache', description: 'Warm the icon cache')]
final class WarmCacheCommand extends Command
{
    public function __construct(private IconCacheWarmer $warmer)
    {
        parent::__construct();
    }
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->comment('Warming the icon cache...');
        $this->warmer->warm(onSuccess: function (string $name) use ($io) {
            if ($io->isVerbose()) {
                $io->writeln(\sprintf(' Warmed icon <comment>%s</comment>.', $name));
            }
        }, onFailure: function (string $name, \Exception $e) use ($io) {
            if ($io->isVerbose()) {
                $io->writeln(\sprintf(' Failed to warm (potential) icon <error>%s</error>.', $name));
            }
        });
        $io->success('Icon cache warmed.');
        return Command::SUCCESS;
    }
}
