<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace OmniIconDeps\Symfony\UX\Icons\Registry;

use OmniIconDeps\Symfony\Contracts\Cache\CacheInterface;
use OmniIconDeps\Symfony\UX\Icons\Exception\IconNotFoundException;
use OmniIconDeps\Symfony\UX\Icons\Icon;
use OmniIconDeps\Symfony\UX\Icons\IconRegistryInterface;
/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @internal
 */
final class CacheIconRegistry implements IconRegistryInterface
{
    public function __construct(private IconRegistryInterface $inner, private CacheInterface $cache)
    {
    }
    public function get(string $name, bool $refresh = \false): Icon
    {
        if (!Icon::isValidName($name)) {
            throw new IconNotFoundException(\sprintf('The icon name "%s" is not valid.', $name));
        }
        return $this->cache->get(Icon::nameToId($name), fn() => $this->inner->get($name), beta: $refresh ? \INF : null);
    }
}
