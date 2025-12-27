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
use OmniIconDeps\Symfony\Component\Console\Input\InputOption;
use OmniIconDeps\Symfony\Component\Console\Output\OutputInterface;
use OmniIconDeps\Symfony\Component\Console\Style\SymfonyStyle;
use OmniIconDeps\Symfony\UX\Icons\Exception\IconNotFoundException;
use OmniIconDeps\Symfony\UX\Icons\Iconify;
use OmniIconDeps\Symfony\UX\Icons\Registry\LocalSvgIconRegistry;
use OmniIconDeps\Symfony\UX\Icons\Twig\IconFinder;
/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @internal
 */
#[AsCommand(name: 'ux:icons:lock', description: 'Scan project and import icon(s) from iconify.design')]
final class LockIconsCommand extends Command
{
    public function __construct(private Iconify $iconify, private LocalSvgIconRegistry $registry, private IconFinder $iconFinder, private readonly array $iconAliases = [], private readonly array $iconSetAliases = [])
    {
        parent::__construct();
    }
    protected function configure(): void
    {
        $this->addOption(name: 'force', mode: InputOption::VALUE_NONE, description: 'Force re-import of all found icons');
    }
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $force = $input->getOption('force');
        $count = 0;
        $io->comment('Scanning project for icons...');
        $finderIcons = $this->iconFinder->icons();
        if ($this->iconAliases) {
            $io->comment('Adding icons aliases...');
        }
        foreach ([...array_values($this->iconAliases), ...array_values($finderIcons)] as $icon) {
            if (2 !== \count($parts = explode(':', $icon))) {
                continue;
            }
            [$prefix, $name] = $parts;
            $prefix = $this->iconSetAliases[$prefix] ?? $prefix;
            if (!$force && $this->registry->has($prefix . ':' . $name)) {
                // icon already imported
                continue;
            }
            if (!$this->iconify->hasIconSet($prefix)) {
                // not an icon set? example: "og:twitter"
                if ($io->isVeryVerbose()) {
                    $io->writeln(\sprintf(' <fg=bright-yellow;options=bold>✗</> IconSet Not Found: <fg=bright-white;bg=black>%s:%s</>.', $prefix, $name));
                }
                continue;
            }
            try {
                $iconSvg = $this->iconify->fetchIcon($prefix, $name)->toHtml();
            } catch (IconNotFoundException) {
                // icon not found on iconify
                if ($io->isVerbose()) {
                    $io->writeln(\sprintf(' <fg=bright-red;options=bold>✗</> Icon Not Found: <fg=bright-white;bg=black>%s:%s</>.', $prefix, $name));
                }
                continue;
            }
            $this->registry->add(\sprintf('%s/%s', $prefix, $name), $iconSvg);
            $license = $this->iconify->metadataFor($prefix)['license'];
            ++$count;
            $io->writeln(\sprintf(" <fg=bright-green;options=bold>✓</> Imported <fg=bright-white;bg=black>%s:</><fg=bright-magenta;bg=black;options>%s</> (License: <href=%s>%s</>). Render with: <comment>{{ ux_icon('%s') }}</comment>", $prefix, $name, $license['url'] ?? '#', $license['title'], $icon));
        }
        $io->success(\sprintf('Imported %d icons.', $count));
        return Command::SUCCESS;
    }
}
