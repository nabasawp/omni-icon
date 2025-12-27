<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace OmniIconDeps\Symfony\UX\Icons\EventListener;

use OmniIconDeps\Symfony\Component\AssetMapper\Event\PreAssetsCompileEvent;
use OmniIconDeps\Symfony\UX\Icons\IconCacheWarmer;
/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @internal
 */
final class WarmIconCacheOnAssetCompileListener
{
    public function __construct(private IconCacheWarmer $warmer)
    {
    }
    public function __invoke(PreAssetsCompileEvent $event): void
    {
        $event->getOutput()->writeln('Warming the icon cache...');
        $this->warmer->warm();
        $event->getOutput()->writeln('Icon cache warmed.');
    }
}
